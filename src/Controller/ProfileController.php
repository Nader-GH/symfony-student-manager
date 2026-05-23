<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;

final class ProfileController extends AbstractController
{
    public function index(): void
    {
        $this->app->requireAuth();
        $error = '';
        $success = '';

        $user = $this->em()->find(User::class, (int)$_SESSION['user']['id']);
        if ($user === null) {
            $this->redirect('logout');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (!$this->app->verifyPassword($currentPassword, $user->getPassword())) {
                $error = 'Mot de passe actuel incorrect.';
            } elseif (strlen($newPassword) < 6) {
                $error = 'Le nouveau mot de passe doit contenir au moins 6 caracteres.';
            } elseif ($newPassword !== $confirmPassword) {
                $error = 'Les mots de passe ne correspondent pas.';
            } else {
                $user->setPassword($this->app->hashPassword($newPassword));
                $this->em()->flush();
                $success = 'Mot de passe mis a jour avec succes.';
            }
        }

        $this->render('profile/index.html.twig', [
            'user' => $user,
            'error' => $error,
            'success' => $success,
        ]);
    }
}
