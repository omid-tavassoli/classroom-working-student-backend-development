<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Models\AdminUser;
use App\Models\CustomerUser;
use App\Models\UserBase;

final class UnitTest
{
    /**
     * Runs all unit tests in sequence.
     */
    public static function run(): void
    {
        ini_set('assert.exception', '1');

        self::testUserCreationAndRoles();
        self::testEmailValidationThrows();
        self::testLoginTrait();
        self::testResettableAdmin();
        self::testStaticInstanceCounter();

        echo "All unit tests passed ✅\n";
    }

    /**
     * Fails the test run when a condition is false.
     */
    private static function expect(bool $condition, string $message): void
    {
        if (!$condition) {
            throw new RuntimeException('Test failed: ' . $message);
        }
    }

    /**
     * Tests basic user creation, roles, and string conversion.
     */
    private static function testUserCreationAndRoles(): void
    {
        $admin = new AdminUser('Admin', 'admin@example.com');
        $customer = new CustomerUser('Customer', 'customer@example.com');

        self::expect($admin->getRole() === UserBase::ROLE_ADMIN, 'Admin role should be ROLE_ADMIN.');
        self::expect($customer->getRole() === UserBase::ROLE_CUSTOMER, 'Customer role should be ROLE_CUSTOMER.');

        // __toString should return something non-empty
        self::expect((string)$admin !== '', 'Admin __toString() should not be empty.');
        self::expect((string)$customer !== '', 'Customer __toString() should not be empty.');
    }

    /**
     * Verifies invalid email input throws an exception.
     */
    private static function testEmailValidationThrows(): void
    {
        $thrown = false;

        try {
            new CustomerUser('Bad', 'not-an-email');
        } catch (InvalidArgumentException $e) {
            $thrown = true;
        }

        self::expect($thrown === true, 'Invalid email should throw InvalidArgumentException.');
    }

    /**
     * Tests login behavior from the shared CanLogin trait.
     */
    private static function testLoginTrait(): void
    {
        $admin = new AdminUser('Admin', 'admin2@example.com');

        self::expect($admin->login('admin2@example.com', 'secret12') === true, 'Login with correct email/password should pass.');
        self::expect($admin->login('wrong@example.com', 'secret12') === false, 'Login with wrong email should fail.');
        self::expect($admin->login('admin2@example.com', '') === false, 'Login with empty password should fail.');
        self::expect($admin->login('admin2@example.com', '123') === false, 'Login with short password should fail.');
    }

    /**
     * Tests password reset and meta updates on admin users.
     */
    private static function testResettableAdmin(): void
    {
        $admin = new AdminUser('Admin', 'admin3@example.com');

        $admin->resetPassword('veryStrongPassword');
        self::expect($admin->getPasswordHash() !== '', 'Password hash should be set after resetPassword().');

        // meta is whitelisted for magic __get/__set in UserBase
        $meta = $admin->meta;
        self::expect(is_array($meta), 'meta should be an array.');
        self::expect(isset($meta['lastPasswordReset']), 'meta should contain lastPasswordReset.');
    }

    /**
     * Ensures static user instance counter increases correctly.
     */
    private static function testStaticInstanceCounter(): void
    {
        $before = UserBase::getInstanceCount();

        new AdminUser('A', 'a' . uniqid('', true) . '@example.com');
        new CustomerUser('B', 'b' . uniqid('', true) . '@example.com');

        $after = UserBase::getInstanceCount();
        self::expect($after === $before + 2, 'Instance counter should increase by 2.');
    }
}

UnitTest::run();
