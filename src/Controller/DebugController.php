<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Attributes as OA;


class DebugController extends AbstractController
{
    #[Route(path: "/api/debug", name: "debug", methods: ["GET"])]
    #[OA\Get(
        path: '/api/debug',
        summary: 'Debug test',
        tags: ['Debug'],
        responses: [
            new OA\Response(response: 200, description: 'OK')
        ]
    )] public function debug(): JsonResponse
    {
        return new JsonResponse(['debug' => 'ok']);
    }
}
