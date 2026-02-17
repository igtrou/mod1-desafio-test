<?php

namespace App\Providers;

use App\Application\Ports\Out\ApplicationEnvironmentPort;
use App\Application\Ports\Out\ApplicationLoggerPort;
use App\Application\Ports\Out\AuditLoggerPort;
use App\Application\Ports\Out\AuthLifecycleEventsPort;
use App\Application\Ports\Out\ConfigCachePort;
use App\Application\Ports\Out\EnvFileEditorPort;
use App\Application\Ports\Out\LoginRateLimiterPort;
use App\Application\Ports\Out\MarketDataProviderManagerPort;
use App\Application\Ports\Out\PasswordHasherPort;
use App\Application\Ports\Out\PasswordResetBrokerPort;
use App\Application\Ports\Out\QuotationCollectCommandRunnerPort;
use App\Application\Ports\Out\QuotationCollectExecutionLoggerPort;
use App\Application\Ports\Out\QuotationDeletionRepositoryPort;
use App\Application\Ports\Out\QuotationPersistencePort;
use App\Application\Ports\Out\QuotationQueryBuilderPort;
use App\Application\Ports\Out\QuotationReconciliationRepositoryPort;
use App\Application\Ports\Out\QuotationsConfigPort;
use App\Application\Ports\Out\QuoteCachePort;
use App\Application\Ports\Out\RememberTokenGeneratorPort;
use App\Application\Ports\Out\RuntimeStateStorePort;
use App\Application\Ports\Out\UserRepositoryPort;
use App\Application\Ports\Out\WebSessionAuthenticatorPort;
use App\Application\Ports\Out\WebSessionStatePort;
use App\Domain\MarketData\AssetTypeResolver;
use App\Domain\MarketData\SymbolNormalizer;
use App\Domain\Quotations\QuotationQualityService;
use App\Infrastructure\Audit\AuditLogger;
use App\Infrastructure\Auth\AuthLifecycleEvents;
use App\Infrastructure\Auth\LoginRateLimiter;
use App\Infrastructure\Auth\PasswordHasher;
use App\Infrastructure\Auth\PasswordResetBroker;
use App\Infrastructure\Auth\RememberTokenGenerator;
use App\Infrastructure\Auth\UserRepository;
use App\Infrastructure\Auth\WebSessionAuthenticator;
use App\Infrastructure\Auth\WebSessionState;
use App\Infrastructure\Cache\RuntimeStateStore;
use App\Infrastructure\Config\ApplicationEnvironment;
use App\Infrastructure\Config\ConfigCacheManager;
use App\Infrastructure\Config\EnvFileEditor;
use App\Infrastructure\Config\QuotationsConfig;
use App\Infrastructure\Console\QuotationCollectCommandRunner;
use App\Infrastructure\MarketData\MarketDataProviderManager;
use App\Infrastructure\MarketData\QuoteCache;
use App\Infrastructure\Observability\ApplicationLogger;
use App\Infrastructure\Observability\QuotationCollectExecutionLogger;
use App\Infrastructure\Quotations\QuotationDeletionRepository;
use App\Infrastructure\Quotations\QuotationPersistenceGateway;
use App\Infrastructure\Quotations\QuotationQueryBuilder;
use App\Infrastructure\Quotations\QuotationReconciliationRepository;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra configuracoes e dependencias necessarias.
     */
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerDomainServices();
        $this->registerPorts();
        $this->registerUseCases();
    }

    /**
     * Registra singletons de servicos de dominio puros e utilitarios core.
     */
    private function registerDomainServices(): void
    {
        $this->app->singleton(MarketDataProviderManager::class, function ($container) {
            return new MarketDataProviderManager(
                $container,
                static fn (): array => config('market-data', [])
            );
        });

        $this->app->singleton(AssetTypeResolver::class, function () {
            return new AssetTypeResolver(
                config('market-data.crypto_symbols', []),
                config('market-data.currency_codes', [])
            );
        });

        $this->app->singleton(SymbolNormalizer::class);

        $this->app->singleton(QuotationQualityService::class, function () {
            return new QuotationQualityService(
                outlierGuardEnabled: (bool) config('quotations.quality.outlier_guard.enabled', true),
                configuredMinReferencePoints: (int) config('quotations.quality.outlier_guard.min_reference_points', 4),
                configuredWindowSize: (int) config('quotations.quality.outlier_guard.window_size', 20),
                configuredMaxDeviationRatio: (float) config('quotations.quality.outlier_guard.max_deviation_ratio', 0.85),
            );
        });
    }

    /**
     * Registra adaptadores de infraestrutura para as portas de dominio.
     */
    private function registerPorts(): void
    {
        $bindings = [
            ApplicationEnvironmentPort::class => ApplicationEnvironment::class,
            ApplicationLoggerPort::class => ApplicationLogger::class,
            AuditLoggerPort::class => AuditLogger::class,
            AuthLifecycleEventsPort::class => AuthLifecycleEvents::class,
            ConfigCachePort::class => ConfigCacheManager::class,
            EnvFileEditorPort::class => EnvFileEditor::class,
            LoginRateLimiterPort::class => LoginRateLimiter::class,
            PasswordHasherPort::class => PasswordHasher::class,
            PasswordResetBrokerPort::class => PasswordResetBroker::class,
            QuotationCollectCommandRunnerPort::class => QuotationCollectCommandRunner::class,
            QuotationCollectExecutionLoggerPort::class => QuotationCollectExecutionLogger::class,
            QuotationDeletionRepositoryPort::class => QuotationDeletionRepository::class,
            QuotationPersistencePort::class => QuotationPersistenceGateway::class,
            QuotationQueryBuilderPort::class => QuotationQueryBuilder::class,
            QuotationReconciliationRepositoryPort::class => QuotationReconciliationRepository::class,
            QuotationsConfigPort::class => QuotationsConfig::class,
            QuoteCachePort::class => QuoteCache::class,
            RememberTokenGeneratorPort::class => RememberTokenGenerator::class,
            RuntimeStateStorePort::class => RuntimeStateStore::class,
            UserRepositoryPort::class => UserRepository::class,
            WebSessionAuthenticatorPort::class => WebSessionAuthenticator::class,
            WebSessionStatePort::class => WebSessionState::class,
        ];

        foreach ($bindings as $port => $adapter) {
            $this->app->bind($port, fn (Container $container): mixed => $container->make($adapter));
        }

        // Reutiliza o singleton do manager para todas as injeções via porta.
        $this->app->alias(MarketDataProviderManager::class, MarketDataProviderManagerPort::class);
    }

    /**
     * Registra bindings de portas de entrada (use cases) para suas implementacoes em Actions.
     */
    private function registerUseCases(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(app_path('Actions'))
        );

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo instanceof SplFileInfo || ! $fileInfo->isFile()) {
                continue;
            }

            if ($fileInfo->getExtension() !== 'php' || ! str_ends_with($fileInfo->getFilename(), 'Action.php')) {
                continue;
            }

            $actionFile = $fileInfo->getPathname();
            $relativePath = str_replace([app_path('Actions').DIRECTORY_SEPARATOR, '.php'], '', $actionFile);
            $relativeNamespace = str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);

            $actionClass = 'App\\Actions\\'.$relativeNamespace;
            $useCaseInterface = 'App\\Application\\Ports\\In\\'
                .preg_replace('/Action$/', 'UseCase', $relativeNamespace);

            if (! is_string($useCaseInterface) || ! interface_exists($useCaseInterface) || ! class_exists($actionClass)) {
                continue;
            }

            $this->app->bind(
                $useCaseInterface,
                fn (Container $container): mixed => $container->make($actionClass)
            );
        }
    }

    /**
     * Executa configuracoes na inicializacao da aplicacao.
     */
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
