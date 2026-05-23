<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final class AuthService
{
    private static ?self $instance = null;

    private RoleHierarchy $roleHierarchy;
    private UserPasswordHasher $passwordHasher;

    private function __construct()
    {
        $this->roleHierarchy = new RoleHierarchy([
            RoleMapper::ROLE_SUPER_ADMIN => [RoleMapper::ROLE_TEACHER, RoleMapper::ROLE_USER],
            RoleMapper::ROLE_TEACHER => [RoleMapper::ROLE_USER],
        ]);

        $factory = new PasswordHasherFactory([
            PasswordAuthenticatedUserInterface::class => ['algorithm' => 'auto'],
        ]);
        $this->passwordHasher = new UserPasswordHasher($factory);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function hashPassword(string $plainPassword): string
    {
        $user = new InMemoryUser('seed', $plainPassword);

        return $this->passwordHasher->hashPassword($user, $plainPassword);
    }

    public function verifyPassword(string $plainPassword, string $hashedPassword): bool
    {
        $user = new InMemoryUser('login', $hashedPassword);

        return $this->passwordHasher->isPasswordValid($user, $plainPassword);
    }

    public function hasRole(string $dbRole, string $requiredRole): bool
    {
        $symfonyRole = RoleMapper::toSymfonyRole($dbRole);
        $reachableRoles = $this->roleHierarchy->getReachableRoleNames([$symfonyRole]);

        return in_array($requiredRole, $reachableRoles, true);
    }

    public function roleHierarchy(): RoleHierarchy
    {
        return $this->roleHierarchy;
    }
}
