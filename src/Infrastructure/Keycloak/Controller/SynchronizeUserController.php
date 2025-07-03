<?php

namespace App\Infrastructure\Keycloak\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use App\Application\User\SynchronizeUserHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class SynchronizeUserController extends AbstractController
{
    public function __construct(private readonly SynchronizeUserHandler $handler)
    {
    }

    #[OA\Post(
        tags: ['Keycloak'],
        summary: 'Synchronise un utilisateur Keycloak',
        responses: [
            new OA\Response(response: 200, description: 'OK')
        ]
    )]
    #[Route('keycloak/users/synchronize', name: 'synchronize_keycloak_user', methods: ['POST'])]

    public function __invoke(Request $request): JsonResponse
    {
        $this->handler->handle($request);
        return new JsonResponse(['message' => 'Route synchronize'], 200);
    }
}