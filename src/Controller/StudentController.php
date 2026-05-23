<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Etudiant;
use App\Entity\Section;

final class StudentController extends AbstractController
{
    public function list(): void
    {
        $this->app->requireAuth();
        $sectionId = isset($_GET['section_id']) ? (int)$_GET['section_id'] : 0;

        $qb = $this->em()->createQueryBuilder()
            ->select('e', 's')
            ->from(Etudiant::class, 'e')
            ->join('e.section', 's')
            ->orderBy('e.id', 'DESC');

        if ($sectionId > 0) {
            $qb->andWhere('s.id = :sectionId')->setParameter('sectionId', $sectionId);
        }

        $sections = $this->em()->getRepository(Section::class)->findBy([], ['designation' => 'ASC']);

        $this->render('student/list.html.twig', [
            'students' => $qb->getQuery()->getResult(),
            'sections' => $sections,
            'sectionId' => $sectionId,
        ]);
    }

    public function show(): void
    {
        $this->app->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $student = $this->em()->find(Etudiant::class, $id);
        if ($student === null) {
            http_response_code(404);
            $this->render('page/not_found.html.twig', ['route' => 'student']);

            return;
        }

        $this->render('student/show.html.twig', ['student' => $student]);
    }

    public function form(): void
    {
        $this->app->requireTeacher();
        $id = (int)($_GET['id'] ?? 0);
        $isEdit = $id > 0;
        $error = '';

        $student = $isEdit ? $this->em()->find(Etudiant::class, $id) : new Etudiant();
        if ($isEdit && $student === null) {
            http_response_code(404);
            $this->render('page/not_found.html.twig', ['route' => 'student']);

            return;
        }

        if (!$student instanceof Etudiant) {
            $student = new Etudiant();
        }

        $sections = $this->em()->getRepository(Section::class)->findBy([], ['designation' => 'ASC']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $dateNaissance = $_POST['date_naissance'] ?? '';
            $sectionId = (int)($_POST['section_id'] ?? 0);
            $section = $this->em()->find(Section::class, $sectionId);
            $imagePath = $student->getImage();

            if ($nom === '' || $dateNaissance === '' || $section === null) {
                $error = 'Tous les champs sauf image sont obligatoires.';
            } else {
                if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploaded = $this->handleUpload();
                    if ($uploaded['error'] !== '') {
                        $error = $uploaded['error'];
                    } else {
                        $imagePath = $uploaded['path'];
                    }
                }

                if ($error === '') {
                    $student->setNom($nom);
                    $student->setDateNaissance(new \DateTimeImmutable($dateNaissance));
                    $student->setSection($section);
                    $student->setImage($imagePath);

                    if (!$isEdit) {
                        $this->em()->persist($student);
                    }
                    $this->em()->flush();
                    $this->redirect('students_list');
                }
            }
        }

        $this->render('student/form.html.twig', [
            'student' => $student,
            'sections' => $sections,
            'isEdit' => $isEdit,
            'error' => $error,
        ]);
    }

    public function delete(): void
    {
        $this->app->requireTeacher();
        $id = (int)($_GET['id'] ?? 0);
        $student = $this->em()->find(Etudiant::class, $id);
        if ($student !== null) {
            $this->em()->remove($student);
            $this->em()->flush();
        }
        $this->redirect('students_list');
    }

    /** @return array{path: ?string, error: string} */
    private function handleUpload(): array
    {
        $ext = strtolower(pathinfo((string)$_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            return ['path' => null, 'error' => 'Format image non supporte.'];
        }

        $targetDir = $this->app->projectDir() . '/assets/uploads/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = uniqid('student_', true) . '.' . $ext;
        $targetPath = $targetDir . $fileName;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            return ['path' => null, 'error' => "Echec de l'upload image."];
        }

        return ['path' => 'assets/uploads/' . $fileName, 'error' => ''];
    }
}
