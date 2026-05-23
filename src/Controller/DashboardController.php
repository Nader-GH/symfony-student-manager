<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Announcement;
use App\Entity\Etudiant;
use App\Entity\Section;
use App\Entity\User;

final class DashboardController extends AbstractController
{
    public function index(): void
    {
        $this->app->requireAuth();
        $em = $this->em();

        $stats = [
            'students' => (int)$em->createQuery('SELECT COUNT(e.id) FROM App\Entity\Etudiant e')->getSingleScalarResult(),
            'sections' => (int)$em->createQuery('SELECT COUNT(s.id) FROM App\Entity\Section s')->getSingleScalarResult(),
            'users' => (int)$em->createQuery('SELECT COUNT(u.id) FROM App\Entity\User u')->getSingleScalarResult(),
            'announcements' => (int)$em->createQuery('SELECT COUNT(a.id) FROM App\Entity\Announcement a')->getSingleScalarResult(),
        ];

        $recentAnnouncements = $em->createQueryBuilder()
            ->select('a')
            ->from(Announcement::class, 'a')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
            'recentAnnouncements' => $recentAnnouncements,
        ]);
    }
}
