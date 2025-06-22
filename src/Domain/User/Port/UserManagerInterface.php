<?php

namespace App\Domain\User\Port;

interface UserManagerInterface {
    public function desactivate(string $userId): void;
}