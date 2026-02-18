<?php
declare(strict_types=1);

namespace App\Traits;

trait CanLogin
{
    public function login(string $email, string $password): bool
    {
        //  email must match the user's email
        //  password must not be empty
        //  password length must be at least 8 chars
        if ($email !== $this->getEmail() || $password === '' || strlen($password) < 8) {
            return false;
        }

        return true;
    }
}
