<?php
declare(strict_types=1);

namespace App\Models;

use App\Interfaces\Resettable;
use App\Traits\CanLogin;
use InvalidArgumentException;

final class AdminUser extends UserBase implements Resettable
{
    use CanLogin;

    private string $passwordHash = '';

    public function __construct(string $name, string $email)
    {
        parent::__construct($name, $email, self::ROLE_ADMIN);
    }

    public function resetPassword(string $newPassword): void
    {
        $newPassword = trim($newPassword);

        if (strlen($newPassword) < 8) {
            throw new InvalidArgumentException('Admin password must be at least 8 characters.');
        }

        $this->passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->setMeta('lastPasswordReset', date('c'));
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }
}
