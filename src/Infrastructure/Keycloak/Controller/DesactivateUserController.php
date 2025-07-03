<?php

namespace App\Infrastructure\Keycloak\Controller;

use App\Application\User\DesactivateUserHandler;
use App\Domain\User\Exception\UserNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use OpenApi\Attributes as OA;

class DesactivateUserController extends AbstractController
{
    public function __construct(private readonly DesactivateUserHandler $handler)
    {
    }

    #[Route('keycloak/users/{id}/desactivate', name: 'user_desasctivate', methods: ['POST'])]
    #[OA\Post(
        path: '/api/keycloak/users/{id}/desactivate',
        tags: ['Keycloak'],
        summary: 'Désactive un utilisateur dans Keycloak',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                description: 'ID utilisateur Keycloak'
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Utilisateur désactivé'
            ),
            new OA\Response(
                response: 404,
                description: 'Utilisateur introuvable'
            )
        ]
    )]
    public function __invoke(string $id, NormalizerInterface $normalizer, Request $request): JsonResponse
    {
        try {
            $this->handler->handle($id, $request);
            return new JsonResponse(['message' => 'User desactivated']);
        } catch (UserNotFoundException $e) {
            return new JsonResponse(
                $normalizer->normalize($e),
                Response::HTTP_NOT_FOUND
            );
        }
    }
}