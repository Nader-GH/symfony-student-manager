<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

final class EntityManagerFactory
{
    public static function create(string $projectDir): EntityManager
    {
        $dbPath = self::resolveDatabasePath($projectDir);
        self::ensureDatabaseDirectoryExists($dbPath);

        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [$projectDir . '/src/Entity'],
            isDevMode: true,
        );

        $connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => $dbPath,
        ]);

        $entityManager = new EntityManager($connection, $config);
        DatabaseInitializer::initialize($entityManager);

        return $entityManager;
    }

    private static function resolveDatabasePath(string $projectDir): string
    {
        $configuredPath = getenv('DATABASE_PATH') ?: getenv('DB_PATH');
        if (is_string($configuredPath) && $configuredPath !== '') {
            return $configuredPath;
        }

        $localPath = $projectDir . '/database.sqlite';
        if (is_file($localPath) || is_writable($projectDir)) {
            return $localPath;
        }

        return rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'student-manager.sqlite';
    }

    private static function ensureDatabaseDirectoryExists(string $dbPath): void
    {
        $directory = dirname($dbPath);
        if (is_dir($directory)) {
            return;
        }

        mkdir($directory, 0777, true);
    }
}
