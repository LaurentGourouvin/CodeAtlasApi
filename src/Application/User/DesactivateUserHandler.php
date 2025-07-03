<?php

namespace App\Application\User;

use App\Domain\User\Port\UserManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class DesactivateUserHandler
{
    public function __construct(private readonly UserManagerInterface $userManager)
    {
    }

    public function handle(string $userId, Request $request): void
    {
        $this->userManager->desactivate(userId: $userId, request: $request);
    }
}