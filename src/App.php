<?php

namespace App;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

class App {
    private static ?EntityManager $entityManager = null;

    public static function getEntityManager(): EntityManager {
        if (self::$entityManager === null) {
            $config = ORMSetup::createAttributeMetadataConfiguration(
                paths: [__DIR__ . '/Entity'],
                isDevMode: true,
            );

            $connection = DriverManager::getConnection([
                'driver' => 'pdo_sqlite',
                'path' => __DIR__ . '/../data/database.sqlite',
            ]);

            self::$entityManager = new EntityManager($connection, $config);
        }

        return self::$entityManager;
    }
}
