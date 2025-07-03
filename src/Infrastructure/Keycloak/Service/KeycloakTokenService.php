<?php

namespace App\Infrastructure\Keycloak\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Jose\Component\Core\JWKSet;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Core\AlgorithmManager;
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
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
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

    public function validateTokenFromRequest(Request $request)
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new \RuntimeException('No Bearer token found in Authorization header.');
        }

        $token = substr($authHeader, 7);

        try {
            $jwksUri = sprintf('%s/realms/%s/protocol/openid-connect/certs', $this->keycloakUrl, $this->realm);
            $jwkData = $this->httpClient->request('GET', $jwksUri)->toArray();

            $serializer = new CompactSerializer();
            $jws = $serializer->unserialize($token);

            $header = $jws->getSignature(0)->getProtectedHeader();
            $tokenKid = $header['kid'] ?? null;

            if (!$tokenKid) {
                throw new \RuntimeException('No "kid" found in JWT header.');
            }

            $signatureKeys = array_filter(
                $jwkData['keys'],
                fn($key) =>
                ($key['use'] ?? null) === 'sig'
                && ($key['alg'] ?? null) === 'RS256'
                && ($key['kid'] ?? null) === $tokenKid
            );

            if (empty($signatureKeys)) {
                throw new \RuntimeException('No matching JWK key for "kid" ' . $tokenKid);
            }

            $jwkSet = JWKSet::createFromKeyData(['keys' => array_values($signatureKeys)]);

            $verifier = new JWSVerifier(new AlgorithmManager([new RS256()]));
            $jwk = current($jwkSet->all());


            if (!$verifier->verifyWithKey($jws, $jwk, 0)) {
                throw new \RuntimeException('Signature verification failed.');
            }

            $payload = json_decode($jws->getPayload(), true, 512, JSON_THROW_ON_ERROR);
            if (!isset($payload['exp']) || $payload['exp'] < time()) {
                throw new \RuntimeException('Token is expired.');
            }

            return $payload;
        } catch (\JsonException $e) {
            throw new \InvalidArgumentException('Invalid JWT payload JSON.', 0, $e);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Unable to validate token: ' . $e->getMessage(), 0, $e);
        }

    }
}