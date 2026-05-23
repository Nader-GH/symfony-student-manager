<?php

declare(strict_types=1);

use App\Entity\Announcement;
use App\Entity\Etudiant;
use App\Entity\Section;
use App\Entity\User;
use Doctrine\ORM\Tools\SchemaTool;

require __DIR__ . '/bootstrap.php';

$app = app();
$em = $app->em();

$tool = new SchemaTool($em);
$metadata = $em->getMetadataFactory()->getAllMetadata();
$tool->updateSchema($metadata);

$userRepo = $em->getRepository(User::class);

$seedUsers = [
    ['username' => 'superadmin', 'password' => 'super123', 'role' => 'super_admin'],
    ['username' => 'teacher', 'password' => 'teacher123', 'role' => 'teacher'],
    ['username' => 'user1', 'password' => 'user123', 'role' => 'user'],
];

foreach ($seedUsers as $data) {
    $existing = $userRepo->findOneBy(['username' => $data['username']]);
    if ($existing === null) {
        $user = new User();
        $user->setUsername($data['username']);
        $user->setPassword($app->hashPassword($data['password']));
        $user->setRole($data['role']);
        $em->persist($user);
    }
}

$em->flush();

if ($em->getRepository(Section::class)->count([]) === 0) {
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
        $em->persist($section);
        $sections[] = $section;
    }

    $em->flush();

    $studentsData = [
        ['nom' => 'Ahmed Ben Ali', 'date' => '2004-05-12', 'section' => 0],
        ['nom' => 'Yasmine Trabelsi', 'date' => '2005-11-03', 'section' => 0],
        ['nom' => 'Sami Gharbi', 'date' => '2006-08-21', 'section' => 1],
    ];

    foreach ($studentsData as $data) {
        $student = new Etudiant();
        $student->setNom($data['nom']);
        $student->setDateNaissance(new \DateTimeImmutable($data['date']));
        $student->setSection($sections[$data['section']]);
        $em->persist($student);
    }

    $em->flush();
}

if ($em->getRepository(Announcement::class)->count([]) === 0) {
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
        $em->persist($announcement);
    }

    $em->flush();
}

echo "Database initialized (Doctrine schema + seeds).\n";
echo "Super Admin: superadmin / super123\n";
echo "Teacher: teacher / teacher123\n";
echo "User: user1 / user123\n";
echo "Open: http://localhost:8000/index.php?r=login\n";
