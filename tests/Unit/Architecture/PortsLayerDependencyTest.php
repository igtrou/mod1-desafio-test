<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Tests\TestCase;

class PortsLayerDependencyTest extends TestCase
{
    #[Test]
    /**
     * Executa a rotina principal do metodo input_ports_do_not_depend_on_framework_or_adapters.
     */
    public function input_ports_do_not_depend_on_framework_or_adapters(): void
    {
        $forbidden = [
            'use Illuminate\\',
            'use Laravel\\',
            'use App\\Infrastructure\\',
            'use App\\Http\\',
            'use App\\Actions\\',
            'use App\\Services\\',
            'use App\\Models\\',
        ];

        $this->assertPortsWithoutForbiddenDependencies(
            basePath: app_path('Application/Ports/In'),
            forbiddenDependencies: $forbidden
        );
    }

    #[Test]
    /**
     * Executa a rotina principal do metodo output_ports_do_not_depend_on_framework_or_adapters.
     */
    public function output_ports_do_not_depend_on_framework_or_adapters(): void
    {
        $forbidden = [
            'use Illuminate\\',
            'use Laravel\\',
            'use App\\Infrastructure\\',
            'use App\\Http\\',
            'use App\\Actions\\',
            'use App\\Services\\',
            'use App\\Models\\',
            'use App\\Data\\',
        ];

        $this->assertPortsWithoutForbiddenDependencies(
            basePath: app_path('Application/Ports/Out'),
            forbiddenDependencies: $forbidden
        );
    }

    /**
     * @param  array<int, string>  $forbiddenDependencies
     */
    private function assertPortsWithoutForbiddenDependencies(string $basePath, array $forbiddenDependencies): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath)
        );

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($fileInfo->getPathname());

            $this->assertIsString($contents);
            $this->assertStringContainsString(
                'interface ',
                $contents,
                sprintf('Port file [%s] must declare an interface.', $fileInfo->getPathname())
            );

            foreach ($forbiddenDependencies as $forbiddenDependency) {
                $this->assertStringNotContainsString(
                    $forbiddenDependency,
                    $contents,
                    sprintf(
                        'Port file [%s] must not depend on [%s].',
                        $fileInfo->getPathname(),
                        $forbiddenDependency
                    )
                );
            }
        }
    }
}
