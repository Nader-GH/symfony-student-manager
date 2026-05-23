<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Section;

final class SectionController extends AbstractController
{
    public function list(): void
    {
        $this->app->requireAuth();
        $q = trim($_GET['q'] ?? '');

        $qb = $this->em()->createQueryBuilder()
            ->select('s', 'e')
            ->from(Section::class, 's')
            ->leftJoin('s.etudiants', 'e')
            ->orderBy('s.id', 'DESC');

        if ($q !== '') {
            $qb->andWhere('s.designation LIKE :q OR s.description LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }

        $this->render('section/list.html.twig', [
            'sections' => $qb->getQuery()->getResult(),
            'q' => $q,
        ]);
    }

    public function form(): void
    {
        $this->app->requireTeacher();
        $id = (int)($_GET['id'] ?? 0);
        $isEdit = $id > 0;
        $error = '';

        $section = $isEdit ? $this->em()->find(Section::class, $id) : new Section();
        if ($isEdit && $section === null) {
            http_response_code(404);
            $this->render('page/not_found.html.twig', ['route' => 'section']);

            return;
        }

        if (!$section instanceof Section) {
            $section = new Section();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $designation = trim($_POST['designation'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if ($designation === '' || $description === '') {
                $error = 'Tous les champs sont obligatoires.';
            } else {
                $section->setDesignation($designation);
                $section->setDescription($description);
                if (!$isEdit) {
                    $this->em()->persist($section);
                }
                $this->em()->flush();
                $this->redirect('sections_list');
            }
        }

        $this->render('section/form.html.twig', [
            'section' => $section,
            'isEdit' => $isEdit,
            'error' => $error,
        ]);
    }

    public function delete(): void
    {
        $this->app->requireTeacher();
        $id = (int)($_GET['id'] ?? 0);
        $section = $this->em()->find(Section::class, $id);
        if ($section !== null) {
            $this->em()->remove($section);
            $this->em()->flush();
        }
        $this->redirect('sections_list');
    }
}
