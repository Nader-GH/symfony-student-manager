<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;

final class AuthController extends AbstractController
{
    public function login(): void
    {
        if ($this->app->isLoggedIn()) {
            $this->redirect('dashboard');
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = $this->em()->getRepository(User::class)->findOneBy(['username' => $username]);
            if ($user !== null && $this->app->verifyPassword($password, $user->getPassword())) {
                $_SESSION['user'] = [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'role' => $user->getRole(),
                ];
                $this->redirect('dashboard');
            }

            $error = 'Username ou mot de passe incorrect.';
        }

        $this->render('auth/login.html.twig', ['error' => $error]);
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool)$params['secure'], (bool)$params['httponly']);
        }
        session_destroy();
        $this->redirect('login');
    }
}
