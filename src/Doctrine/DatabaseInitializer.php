<?php

declare(strict_types=1);

namespace App\Doctrine;

use App\Entity\Announcement;
use App\Entity\Etudiant;
use App\Entity\Section;
use App\Entity\User;
use App\Security\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

final class DatabaseInitializer
{
    public static function initialize(EntityManagerInterface $entityManager): void
    {
        if (!self::needsBootstrap($entityManager)) {
            return;
        }

        $schemaTool = new SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->updateSchema($metadata);

        self::seed($entityManager);
    }

    private static function needsBootstrap(EntityManagerInterface $entityManager): bool
    {
        try {
            $tableName = $entityManager->getConnection()
                ->executeQuery("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' LIMIT 1")
                ->fetchOne();

            return $tableName === false;
        } catch (\Throwable) {
            return true;
        }
    }

    private static function seed(EntityManagerInterface $entityManager): void
    {
        $auth = AuthService::getInstance();
        $userRepo = $entityManager->getRepository(User::class);

        $seedUsers = [
            ['username' => 'superadmin', 'password' => 'super123', 'role' => 'super_admin'],
            ['username' => 'teacher', 'password' => 'teacher123', 'role' => 'teacher'],
            ['username' => 'Abdellah Idriss', 'password' => 'user123', 'role' => 'user'],
        ];

        foreach ($seedUsers as $data) {
            $existing = $userRepo->findOneBy(['username' => $data['username']]);
            if ($existing !== null) {
                continue;
            }

            $user = new User();
            $user->setUsername($data['username']);
            $user->setPassword($auth->hashPassword($data['password']));
            $user->setRole($data['role']);
            $entityManager->persist($user);
        }

        $entityManager->flush();

        if ($entityManager->getRepository(Section::class)->count([]) === 0) {
            $sectionsData = [
                ['designation' => 'GL2', 'description' => 'Genie Logiciel Groupe 2'],
                ['designation' => 'RT2', 'description' => 'Reseaux Telecom Groupe 2'],
                ['designation' => 'MPI', 'description' => 'Maths Physique Informatique'],
            ];

            $sections = [];
            foreach ($sectionsData as $data) {
                $section = new Section();
                $section->setDesignation($data['designation']);
                $section->setDescription($data['description']);
                $entityManager->persist($section);
                $sections[] = $section;
            }

            $entityManager->flush();

            $studentsData = [
                ['nom' => 'Ahmed Mohsen', 'date' => '2004-05-12', 'section' => 0],
                ['nom' => 'Adelrazegh Mounib', 'date' => '2005-11-03', 'section' => 0],
                ['nom' => 'Abdellah Idriss', 'date' => '2006-08-21', 'section' => 1],
            ];

            foreach ($studentsData as $data) {
                $student = new Etudiant();
                $student->setNom($data['nom']);
                $student->setDateNaissance(new \DateTimeImmutable($data['date']));
                $student->setSection($sections[$data['section']]);
                $entityManager->persist($student);
            }

            $entityManager->flush();
        }

        if ($entityManager->getRepository(Announcement::class)->count([]) === 0) {
            $author = $userRepo->findOneBy(['username' => 'teacher']);
            $announcements = [
                ['title' => 'Bienvenue sur la plateforme', 'content' => 'Consultez le tableau de bord pour suivre les statistiques de votre etablissement.'],
                ['title' => 'Rappel examens', 'content' => 'Les listes des etudiants par section sont disponibles dans le menu Gestion.'],
            ];

            foreach ($announcements as $data) {
                $announcement = new Announcement();
                $announcement->setTitle($data['title']);
                $announcement->setContent($data['content']);
                $announcement->setAuthor($author);
                $entityManager->persist($announcement);
            }

            $entityManager->flush();
        }
    }
}