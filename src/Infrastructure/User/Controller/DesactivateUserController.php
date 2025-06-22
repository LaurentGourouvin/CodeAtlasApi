<?php

namespace App\Infrastructure\User\Controller;

use App\Application\User\DesactivateUserHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Infrastructure\Keycloak\Service\KeycloakTokenService;

class DesactivateUserController extends AbstractController
{
    public function __construct(private readonly DesactivateUserHandler $handler)
    {
    }

    #[Route('/api/users/{id}/desactivate', name: 'user_desasctivate', methods: ['POST'])]
    public function __invoke(string $id): JsonResponse
    {
        $this->handler->handle($id);
        return new JsonResponse(['message' => 'User desactivated']);
    }

    #[Route('/keycloak/test-token', name: 'keycloak_test_token')]
    public function testToken(KeycloakTokenService $tokenService): JsonResponse
    {
        $token = $tokenService->getAdminAccessToken();

        if (!$token) {
            return new JsonResponse(['error' => 'Unable to retrieve token'], 500);
        }

        return new JsonResponse(['access_token' => $token]);
    }
}