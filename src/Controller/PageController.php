<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Etudiant;
use App\Entity\Section;

final class PageController extends AbstractController
{
    public function statistics(): void
    {
        $this->app->requireAuth();

        $rows = $this->em()->createQueryBuilder()
            ->select('s.designation AS label', 'COUNT(e.id) AS total')
            ->from(Section::class, 's')
            ->leftJoin('s.etudiants', 'e')
            ->groupBy('s.id', 's.designation')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getArrayResult();

        $max = 1;
        foreach ($rows as $row) {
            $max = max($max, (int)$row['total']);
        }

        $students = $this->em()->getRepository(Etudiant::class)->findAll();
        $ages = array_map(static fn (Etudiant $e): int => $e->getAge(), $students);

        $sections = $this->em()->getRepository(Section::class)->findAll();
        $ageBySection = [];
        foreach ($sections as $section) {
            $sectionAges = [];
            foreach ($section->getEtudiants() as $etudiant) {
                $sectionAges[] = $etudiant->getAge();
            }
            $ageBySection[] = [
                'label' => $section->getDesignation(),
                'average' => $sectionAges === [] ? 0 : (int)round(array_sum($sectionAges) / count($sectionAges)),
                'count' => count($sectionAges),
            ];
        }

        $this->render('page/statistics.html.twig', [
            'rows' => $rows,
            'max' => $max,
            'averageAge' => $ages === [] ? 0 : (int)round(array_sum($ages) / count($ages)),
            'totalStudents' => count($ages),
            'ageBySection' => $ageBySection,
        ]);
    }

    public function help(): void
    {
        $this->app->requireAuth();
        $this->render('page/help.html.twig');
    }

    public function about(): void
    {
        $this->app->requireAuth();
        $this->render('page/about.html.twig');
    }
}
