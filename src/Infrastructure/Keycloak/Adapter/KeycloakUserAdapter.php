<?php

namespace App\Infrastructure\Keycloak\Adapter;

use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\Port\UserManagerInterface;
use App\Infrastructure\Keycloak\Service\KeycloakTokenService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;


class KeycloakUserAdapter implements UserManagerInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly KeycloakTokenService $tokenService,
        private readonly string $keycloakBaseUrl,
        private readonly string $realm,
        private readonly LoggerInterface $logger
    ) {
    }

    public function desactivate(string $userId, Request $request): void
    {
        $adminToken = $this->tokenService->getAdminAccessToken();

        if (!$adminToken) {
            throw new \RuntimeException('Unable to retrieve Keycloak admin token.');
        }

        $validateKeycloakToken = $this->tokenService->validateTokenFromRequest($request);

        $url = sprintf('%s/admin/realms/%s/users/%s', $this->keycloakBaseUrl, $this->realm, $userId);

        try {
            $this->httpClient->request(
                'PUT',
                $url,
                [
                    'headers' => [
                        'Authorization' => "Bearer $adminToken",
                        'Content-Type' => 'application/json'
                    ],
                    'json' => ['enabled' => false]
                ]
            );
        } catch (ClientExceptionInterface $e) {
            if ($e->getCode() === 404) {
                throw new UserNotFoundException("User not found.");
            }
            throw new \RuntimeException("An unexpected error occurred while processing the request");
        }

    }

    public function synchronize(Request $request): void
    {
    }
}