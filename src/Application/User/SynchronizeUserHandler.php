<?php

namespace App\Application\User;
use App\Domain\User\Port\UserManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class SynchronizeUserHandler
{
    public function __construct(private readonly UserManagerInterface $userManager)
    {
    }

    public function handle(Request $request)
    {
        $this->userManager->synchronize($request);
    }
}