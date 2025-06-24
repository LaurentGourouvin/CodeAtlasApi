<?php

namespace App\Infrastructure\Keycloak\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Jose\Component\Core\JWKSet;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSVerifier;
use Symfony\Component\HttpFoundation\Request;


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

    public function validateTokenFromRequest(Request $request, string $keycloakJwt)
    {
        $jwksUri = sprintf('%s/realms/%s/protocol/openid-connect/certs', $this->keycloakUrl, $this->realm);

        // TODO Mettre en place l'extraction du Bearer pour le vérifier à l'aide du code suivant

        try {
            $jwkRequest = $this->httpClient->request('GET', $jwksUri);
            $jwkData = $jwkRequest->toArray();
            $jwkSet = JWKSet::createFromKeyData($jwkData['keys']);

            $serializer = new CompactSerializer();
            $jws = $serializer->unserialize($keycloakJwt);

            $verifier = new JWSVerifier([new RS256()]);

            foreach ($jwkSet->all() as $jwk) {
                if ($verifier->verifyWithKey($jws, $jwk, 0)) {
                    $payload = json_decode($jws->getPayload(), true);
                    if ($payload['exp'] < time()) {
                        return null; // Expiré
                    }
                    return $payload; // ✅ Token valide
                }
            }

            return null;
        } catch (\Throwable) {

        }

    }
}