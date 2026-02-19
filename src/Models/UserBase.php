<?php declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;

abstract class UserBase
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_CUSTOMER = 'customer';

    private string $name ;
    protected string $email ;
    public string $role ;

    protected static int $instanceCount = 0;

    /** @var array<string, mixed> */
    private array $meta = [];

    public function __construct(
        string $name, string $email, string $role)
    {
        self::$instanceCount++;

        $this->setName($name);
        $this->setEmail($email);
        $this->role = $role;
    }

    public static function getInstanceCount(): int
    {
        return self::$instanceCount;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $name = trim($name);
        if ($name === '') {
            throw new InvalidArgumentException('Name must not be empty.');
        }
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $email = trim($email);

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('Invalid email format: ' . $email);
        }

        $this->email = $email;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    protected function setMeta(string $key, mixed $value): void
    {
        $this->meta[$key] = $value;
    }

    public function __toString(): string
    {
        return sprintf('%s(%s) [%s]', static::class, $this->email, $this->role);
    }

    /**
     * allow whitelisted virtual properties.
     */
    public function __get(string $name): mixed
    {
        $allowed = ['meta'];

        if (in_array($name, $allowed, true)) {
            return $this->$name;
        }

        throw new InvalidArgumentException("Property '{$name}' is not accessible via __get().");
    }

    /**
     * allow whitelisted virtual properties.
     */
    public function __set(string $name, mixed $value): void
    {
        $allowed = ['meta'];

        if (in_array($name, $allowed, true)) {
            $this->$name = $value;
            return;
        }

        throw new InvalidArgumentException("Property '{$name}' is not accessible via __set().");
    }

}