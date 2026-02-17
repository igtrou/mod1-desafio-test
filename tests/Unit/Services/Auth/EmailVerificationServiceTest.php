<?php

namespace Tests\Unit\Services\Auth;

use App\Domain\Auth\UserIdentity;
use App\Application\Ports\Out\AuthLifecycleEventsPort;
use App\Application\Ports\Out\EmailVerificationPort;
use App\Services\Auth\EmailVerificationService;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class EmailVerificationServiceTest extends TestCase
{
    /**
     * Limpa o cenario apos a execucao do teste.
     */
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * Valida o comportamento esperado deste cenario de teste.
     */
    public function test_verify_returns_false_when_mark_email_as_verified_fails(): void
    {
        $emailVerification = Mockery::mock(EmailVerificationPort::class, function (MockInterface $mock): void {
            $mock->shouldReceive('isVerified')
                ->once()
                ->with(1)
                ->andReturn(false);
            $mock->shouldReceive('markAsVerified')
                ->once()
                ->with(1)
                ->andReturn(false);
        });
        $authLifecycleEvents = Mockery::mock(AuthLifecycleEventsPort::class, function (MockInterface $mock): void {
            $mock->shouldNotReceive('dispatchVerified');
        });
        $service = new EmailVerificationService($emailVerification, $authLifecycleEvents);

        $result = $service->verify(1);

        $this->assertFalse($result);
    }

    /**
     * Valida o comportamento esperado deste cenario de teste.
     */
    public function test_verify_returns_true_and_dispatches_event_when_mark_email_succeeds(): void
    {
        $emailVerification = Mockery::mock(EmailVerificationPort::class, function (MockInterface $mock): void {
            $mock->shouldReceive('isVerified')
                ->once()
                ->with(1)
                ->andReturn(false);
            $mock->shouldReceive('markAsVerified')
                ->once()
                ->with(1)
                ->andReturn(true);
        });
        $authLifecycleEvents = Mockery::mock(AuthLifecycleEventsPort::class, function (MockInterface $mock): void {
            $mock->shouldReceive('dispatchVerified')
                ->once()
                ->withArgs(static fn (UserIdentity $user): bool => $user->id === 1);
        });
        $service = new EmailVerificationService($emailVerification, $authLifecycleEvents);

        $result = $service->verify(1);

        $this->assertTrue($result);
    }
}
