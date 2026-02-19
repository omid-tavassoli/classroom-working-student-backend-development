<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Interfaces\Resettable;
use App\Models\AdminUser;
use App\Models\CustomerUser;
use App\Models\UserBase;

function formatUser(UserBase $user): string
{
    return (string)$user;
}

function yesNo(bool $value): string
{
    return $value ? 'YES' : 'NO';
}

echo "=== PHP Basics Demo ===\n\n";
ini_set('assert.exception', '1');

echo "1) Create users + exception handling\n";

try {
    $admin = new AdminUser('Omid Admin', 'omid.admin@example.com');
    $customer = new CustomerUser('Omid Customer', 'omid.customer@example.com');
} catch (InvalidArgumentException $e) {
    echo "Caught exception: " . $e->getMessage() . "\n";
    exit(1);
}

echo "- " . formatUser($admin) . "\n";
echo "- " . formatUser($customer) . "\n\n";

echo "2) Trait usage (CanLogin)\n";
$loginChecks = [
    'correct' => $admin->login('omid.admin@example.com', 'secret12'),
    'wrong email' => $admin->login('wrong@example.com', 'secret12'),
    'short password' => $admin->login('omid.admin@example.com', '123'),
];
foreach ($loginChecks as $case => $ok) {
    echo "- {$case}: " . yesNo($ok) . "\n";
}
echo "\n";

echo "3) Interface usage (Resettable)\n";
/** @var Resettable $resettable */
$resettable = $admin;
$resettable->resetPassword('superSecurePassword');
echo "- password hash set: " . yesNo($admin->getPasswordHash() !== '') . "\n\n";

echo "4) Arrays (numeric vs associative)\n";
$users = [$admin, $customer];
$usersByEmail = [
    $admin->getEmail() => $admin,
    $customer->getEmail() => $customer,
];
echo "- Numeric array count: " . count($users) . "\n";
echo "- Lookup customer: " . $usersByEmail['omid.customer@example.com'] . "\n\n";

echo "5) array_map / array_filter\n";
$mapped = array_map(fn(UserBase $u): string => (string)$u, $users);
$adminsOnly = array_filter($users, fn(UserBase $u): bool => $u->getRole() === UserBase::ROLE_ADMIN);
echo "- mapped:\n";
foreach ($mapped as $item) {
    echo "  {$item}\n";
}
echo "- admins only:\n";
foreach ($adminsOnly as $item) {
    echo "  {$item}\n";
}
echo "\n";

echo "6) Control structures: if / switch / foreach / while\n";
foreach ($users as $u) {
    echo $u->getName() . ($u->getRole() === UserBase::ROLE_ADMIN ? ' is an ADMIN' : ' is NOT an admin') . "\n";

    switch ($u->getRole()) {
        case UserBase::ROLE_ADMIN:
            echo "  switch: has elevated permissions\n";
            break;
        case UserBase::ROLE_CUSTOMER:
            echo "  switch: regular customer permissions\n";
            break;
        default:
            echo "  switch: unknown role\n";
            break;
    }
}

echo "\nWhile loop over numeric array:\n";
$i = 0;
while (isset($users[$i])) {
    echo "- index {$i}: " . $users[$i]->getEmail() . "\n";
    $i++;
}
echo "\n";

echo "7) Customer preferences (associative array inside object)\n";
$customer->setPreference('language', 'de');
$customer->setPreference('newsletter', true);
echo "- preferences:\n";
foreach ($customer->getPreferences() as $key => $value) {
    $v = is_bool($value) ? ($value ? 'true' : 'false') : (string)$value;
    echo "  {$key}: {$v}\n";
}
echo "\n";

echo "8) Static counter\n";
echo "- instances: " . UserBase::getInstanceCount() . "\n\n";

echo "9) Magic methods __get/__set demo (safe whitelist)\n";
$meta = $admin->meta;
echo "- meta keys: " . implode(', ', array_keys($meta)) . "\n";
$admin->meta = array_merge($admin->meta, ['demoKey' => 'demoValue']);
echo "- meta demoKey: " . ($admin->meta['demoKey'] ?? 'not set') . "\n\n";

echo "10) assert() mini tests\n";
assert($admin->getRole() === UserBase::ROLE_ADMIN);
assert($customer->getRole() === UserBase::ROLE_CUSTOMER);
assert(UserBase::getInstanceCount() >= 2);
assert($admin->login('omid.admin@example.com', 'secret12') === true);
assert($admin->login('omid.admin@example.com', 'no') === false);
echo "All asserts passed ✅\n\n";

echo "=== Demo finished ===\n";
