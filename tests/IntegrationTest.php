<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Models\AdminUser;
use App\Models\CustomerUser;
use App\Models\UserBase;
use App\Interfaces\Resettable;

final class IntegrationTest
{
    public static function run(): void
    {
        ini_set('assert.exception', '1');

        self::testEndToEndUserFlow();

        echo "Integration test passed ✅\n";
    }

    private static function expect(bool $condition, string $message): void
    {
        if (!$condition) {
            throw new \RuntimeException('Integration test failed: ' . $message);
        }
    }

    private static function testEndToEndUserFlow(): void
    {
        // 1) Create objects (Models + constructors + validation already covered indirectly)
        $admin = new AdminUser('Admin', 'admin.integration@example.com');
        $customer = new CustomerUser('Customer', 'customer.integration@example.com');

        // 2) Interface behavior: Resettable + exception-safe reset
        /** @var Resettable $resettable */
        $resettable = $admin;
        $resettable->resetPassword('strongPassword123');
        self::expect($admin->getPasswordHash() !== '', 'Password hash should be set after reset.');

        // 3) Trait behavior: login logic uses if + logical operators
        self::expect($admin->login('admin.integration@example.com', 'secret12') === true, 'Login with valid credentials should pass.');
        self::expect($admin->login('admin.integration@example.com', '123') === false, 'Login with short password should fail.');

        // 4) Arrays: numeric list + associative lookup
        $users = [$admin, $customer]; // numeric array (ordered list)
        $usersByEmail = [
            $admin->getEmail() => $admin,
            $customer->getEmail() => $customer,
        ]; // associative array (fast lookup)

        self::expect(count($users) === 2, 'Users array should contain exactly 2 entries.');
        self::expect($usersByEmail['customer.integration@example.com'] instanceof CustomerUser, 'Associative lookup should return a CustomerUser.');

        // 5) array_map: turn objects into strings (uses __toString)
        $strings = array_map(
            fn(UserBase $u): string => (string)$u,
            $users
        );
        self::expect(count($strings) === 2, 'Mapped strings should contain exactly 2 entries.');
        self::expect(str_contains($strings[0], '['), 'String format should include role section.');

        // 6) array_filter + closure: keep only admins
        $isAdmin = fn(UserBase $u): bool => $u->getRole() === UserBase::ROLE_ADMIN;
        $adminsOnly = array_values(array_filter($users, $isAdmin)); // reindex

        self::expect(count($adminsOnly) === 1, 'Filtered admin list should contain exactly 1 user.');
        self::expect($adminsOnly[0] instanceof AdminUser, 'Filtered user should be an AdminUser.');

        // 7) Control structures: switch + while (just to “merge” all requirements)
        $i = 0;
        while ($i < count($users)) {
            $role = $users[$i]->getRole();

            switch ($role) {
                case UserBase::ROLE_ADMIN:
                case UserBase::ROLE_CUSTOMER:
                    self::expect(true, 'Role is valid.');
                    break;
                default:
                    self::expect(false, 'Unexpected role found in switch statement.');
            }

            $i++;
        }

        // 8) Customer preferences (associative array inside object)
        $customer->setPreference('newsletter', true);
        $prefs = $customer->getPreferences();
        self::expect(isset($prefs['newsletter']) && $prefs['newsletter'] === true, 'Customer preferences should store newsletter=true.');

        // 9) Magic methods whitelist: meta is allowed
        $meta = $admin->meta;
        self::expect(is_array($meta), 'Admin meta should be an array.');
        self::expect(isset($meta['lastPasswordReset']), 'Admin meta should include lastPasswordReset.');
    }
}

IntegrationTest::run();
