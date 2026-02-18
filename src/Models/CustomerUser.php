<?php
declare(strict_types=1);

namespace App\Models;

use App\Traits\CanLogin;

final class CustomerUser extends UserBase
{
    use CanLogin;

    /** @var array<string, mixed> */
    private array $preferences = [];

    public function __construct(string $name, string $email)
    {
        parent::__construct($name, $email, self::ROLE_CUSTOMER);
    }

    /** @return array<string, mixed> */
    public function getPreferences(): array
    {
        return $this->preferences;
    }

    public function setPreference(string $key, mixed $value): void
    {
        $this->preferences[$key] = $value;
    }
}
