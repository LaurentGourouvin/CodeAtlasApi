<?php

namespace App\Infrastructure\Keycloak\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class KeycloakTokenService
{
    private string $keycloakUrl;
    private string $realm;
    private string $clientId;
    private string $username;
    private string $password;
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->keycloakUrl = $_ENV['KEYCLOAK_BASE_URL'];
        $this->realm = $_ENV['KEYCLOAK_REALM'];
        $this->clientId = $_ENV['KEYCLOAK_CLIENT_ID'];
        $this->username = $_ENV['KEYCLOAK_ADMIN_USER'];
        $this->password = $_ENV['KEYCLOAK_ADMIN_PASSWORD'];
    }

    public function getAdminAccessToken(): ?string
    {
        $url = sprintf('%s/realms/%s/protocol/openid-connect/token', $this->keycloakUrl, $this->realm);

        $response = $this->httpClient->request('POST', $url, [
            'body' => [
                'grant_type' => 'password',
                'client_id' => $this->clientId,
                'username' => $this->username,
                'password' => $this->password,
            ],
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $data = $response->toArray();

        return $data['access_token'] ?? null;
    }
}