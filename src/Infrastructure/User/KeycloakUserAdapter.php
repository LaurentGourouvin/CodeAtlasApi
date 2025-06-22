<?php

namespace App\Infrastructure\User;

use App\Domain\User\Port\UserManagerInterface;
use App\Infrastructure\Keycloak\Service\KeycloakTokenService;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class KeycloakUserAdapter implements UserManagerInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly KeycloakTokenService $tokenService,
        private readonly string $keycloakBaseUrl,
        private readonly string $realm,
    ) {
    }

    public function desactivate(string $userId): void
    {
        $adminToken = $this->tokenService->getAdminAccessToken();

        if (!$adminToken) {
            throw new \RuntimeException('Unable to retrieve Keycloak admin token.');
        }

        $url = sprintf('%s/admin/realms/%s/users/%s', $this->keycloakBaseUrl, $this->realm, $userId);

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
    }
}