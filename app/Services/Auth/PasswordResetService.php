<?php

namespace App\Services\Auth;

use App\Application\Ports\Out\AuthLifecycleEventsPort;
use App\Application\Ports\Out\PasswordHasherPort;
use App\Application\Ports\Out\PasswordResetBrokerPort;
use App\Application\Ports\Out\RememberTokenGeneratorPort;
use App\Domain\Exceptions\DomainValidationException;

/**
 * Implementa o fluxo de recuperacao e redefinicao de senha.
 */
class PasswordResetService
{
    /**
     * Injeta componentes de broker, hashing, token e eventos.
     */
    /**
     * Executa a rotina principal do metodo __construct.
     */
    public function __construct(
        private readonly PasswordResetBrokerPort $passwordResetBroker,
        private readonly PasswordHasherPort $passwordHasher,
        private readonly RememberTokenGeneratorPort $rememberTokenGenerator,
        private readonly AuthLifecycleEventsPort $authLifecycleEvents,
    ) {}

    /**
     * Solicita envio do link de redefinicao para o e-mail informado.
     *
     * @param  string  $email  E-mail alvo do fluxo de reset.
     *
     * @throws DomainValidationException
     */
    /**
     * Executa a rotina principal do metodo sendResetLink.
     */
    public function sendResetLink(string $email): string
    {
        $status = $this->passwordResetBroker->sendResetLink($email);

        if ($status !== $this->passwordResetBroker->resetLinkSentStatus()) {
            throw DomainValidationException::withErrors([
                'email' => [$status],
            ]);
        }

        return $status;
    }

    /**
     * Valida token de reset e aplica nova senha para o usuario correspondente.
     *
     * @param  array{
     *     token: string,
     *     email: string,
     *     password: string
     * }  $validatedPayload
     *
     * @throws DomainValidationException
     */
    /**
     * Executa a rotina principal do metodo reset.
     */
    public function reset(array $validatedPayload): string
    {
        $resetResult = $this->passwordResetBroker->reset(
            [
                'token' => $validatedPayload['token'],
                'email' => $validatedPayload['email'],
                'password' => $validatedPayload['password'],
                'password_confirmation' => $validatedPayload['password'],
            ],
            $this->passwordHasher->make($validatedPayload['password']),
            $this->rememberTokenGenerator->generate(),
        );
        $status = $resetResult->status;

        if ($resetResult->userIdentity !== null) {
            $this->authLifecycleEvents->dispatchPasswordReset($resetResult->userIdentity);
        }

        if ($status !== $this->passwordResetBroker->passwordResetStatus()) {
            throw DomainValidationException::withErrors([
                'email' => [$status],
            ]);
        }

        return $status;
    }
}
