<?php

declare(strict_types=1);

namespace App\Security;

final class RoleMapper
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_TEACHER = 'ROLE_TEACHER';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /** @var array<string, string> */
    private const DB_TO_SYMFONY = [
        'user' => self::ROLE_USER,
        'teacher' => self::ROLE_TEACHER,
        'super_admin' => self::ROLE_SUPER_ADMIN,
        'admin' => self::ROLE_TEACHER,
    ];

    public static function toSymfonyRole(string $dbRole): string
    {
        return self::DB_TO_SYMFONY[$dbRole] ?? self::ROLE_USER;
    }

    public static function roleLabel(string $dbRole): string
    {
        return match ($dbRole) {
            'super_admin' => 'Super Admin',
            'teacher', 'admin' => 'Teacher',
            default => 'User',
        };
    }
}
