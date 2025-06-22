<?php

namespace App\Application\User;

use App\Domain\User\Port\UserManagerInterface;

class DesactivateUserHandler
{
    public function __construct(private readonly UserManagerInterface $userManager)
    {
    }

    public function handle(string $userId): void
    {
        $this->userManager->desactivate(userId: $userId);
    }
}