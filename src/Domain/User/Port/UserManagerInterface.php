<?php

namespace App\Domain\User\Port;

use Symfony\Component\HttpFoundation\Request;
interface UserManagerInterface
{
    public function desactivate(string $userId, Request $request): void;
}