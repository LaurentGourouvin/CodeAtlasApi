<?php

namespace App\Infrastructure\User\Controller;

use App\Application\User\DesactivateUserHandler;
use App\Domain\User\Exception\UserNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Infrastructure\Keycloak\Service\KeycloakTokenService;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DesactivateUserController extends AbstractController
{
    public function __construct(private readonly DesactivateUserHandler $handler)
    {
    }

    #[Route('keycloak/users/{id}/desactivate', name: 'user_desasctivate', methods: ['POST'])]
    public function __invoke(string $id, NormalizerInterface $normalizer): JsonResponse
    {
        try {
            $this->handler->handle($id);
            return new JsonResponse(['message' => 'User desactivated']);
        } catch (UserNotFoundException $e) {
            return new JsonResponse(
                $normalizer->normalize($e),
                Response::HTTP_NOT_FOUND
            );
        }
    }
}