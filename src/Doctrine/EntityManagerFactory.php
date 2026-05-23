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
        $dbPath = $projectDir . '/database.sqlite';
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [$projectDir . '/src/Entity'],
            isDevMode: true,
        );

        $connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => $dbPath,
        ]);

        return new EntityManager($connection, $config);
    }
}
