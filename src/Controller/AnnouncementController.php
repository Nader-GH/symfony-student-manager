<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Announcement;
use App\Entity\User;

final class AnnouncementController extends AbstractController
{
    public function list(): void
    {
        $this->app->requireAuth();
        $announcements = $this->em()->createQueryBuilder()
            ->select('a', 'u')
            ->from(Announcement::class, 'a')
            ->leftJoin('a.author', 'u')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $this->render('announcement/list.html.twig', ['announcements' => $announcements]);
    }

    public function form(): void
    {
        $this->app->requireTeacher();
        $id = (int)($_GET['id'] ?? 0);
        $isEdit = $id > 0;
        $error = '';

        $announcement = $isEdit ? $this->em()->find(Announcement::class, $id) : new Announcement();
        if ($isEdit && $announcement === null) {
            http_response_code(404);
            $this->render('page/not_found.html.twig', ['route' => 'announcement']);

            return;
        }

        if (!$announcement instanceof Announcement) {
            $announcement = new Announcement();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');

            if ($title === '' || $content === '') {
                $error = 'Titre et contenu sont obligatoires.';
            } else {
                $announcement->setTitle($title);
                $announcement->setContent($content);

                if (!$isEdit) {
                    $author = $this->em()->find(User::class, (int)$_SESSION['user']['id']);
                    $announcement->setAuthor($author);
                    $this->em()->persist($announcement);
                }

                $this->em()->flush();
                $this->redirect('announcements_list');
            }
        }

        $this->render('announcement/form.html.twig', [
            'announcement' => $announcement,
            'isEdit' => $isEdit,
            'error' => $error,
        ]);
    }

    public function delete(): void
    {
        $this->app->requireTeacher();
        $id = (int)($_GET['id'] ?? 0);
        $announcement = $this->em()->find(Announcement::class, $id);
        if ($announcement !== null) {
            $this->em()->remove($announcement);
            $this->em()->flush();
        }
        $this->redirect('announcements_list');
    }
}
