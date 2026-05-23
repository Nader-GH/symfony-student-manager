<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;

final class UserController extends AbstractController
{
    public function list(): void
    {
        $this->app->requireSuperAdmin();
        $users = $this->em()->getRepository(User::class)->findBy([], ['id' => 'ASC']);
        $this->render('user/list.html.twig', ['users' => $users]);
    }

    public function form(): void
    {
        $this->app->requireSuperAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $isEdit = $id > 0;
        $error = '';

        $account = $isEdit ? $this->em()->find(User::class, $id) : new User();
        if ($isEdit && $account === null) {
            http_response_code(404);
            $this->render('page/not_found.html.twig', ['route' => 'user']);

            return;
        }

        if (!$account instanceof User) {
            $account = new User();
            $account->setRole('teacher');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $role = $_POST['role'] ?? 'user';
            $password = $_POST['password'] ?? '';
            $allowedRoles = ['user', 'teacher', 'super_admin'];

            if (!in_array($role, $allowedRoles, true)) {
                $error = 'Role invalide.';
            } elseif (strlen($username) < 3) {
                $error = 'Le username doit contenir au moins 3 caracteres.';
            } elseif ($username === '') {
                $error = 'Le username est obligatoire.';
            } elseif (!$isEdit && $password === '') {
                $error = 'Le mot de passe est obligatoire pour un nouveau compte.';
            } else {
                $duplicate = $this->em()->createQueryBuilder()
                    ->select('COUNT(u.id)')
                    ->from(User::class, 'u')
                    ->where('u.username = :username')
                    ->andWhere('u.id != :id')
                    ->setParameter('username', $username)
                    ->setParameter('id', $id)
                    ->getQuery()
                    ->getSingleScalarResult();

                if ((int)$duplicate > 0) {
                    $error = 'Ce username existe deja.';
                } else {
                    $account->setUsername($username);
                    $account->setRole($role);
                    if ($password !== '') {
                        $account->setPassword($this->app->hashPassword($password));
                    } elseif (!$isEdit) {
                        $error = 'Mot de passe requis.';
                    }

                    if ($error === '') {
                        if (!$isEdit) {
                            $this->em()->persist($account);
                        }
                        $this->em()->flush();

                        if ($isEdit && $id === (int)$_SESSION['user']['id']) {
                            $this->app->syncSessionUser();
                        }

                        $this->redirect('users_list');
                    }
                }
            }
        }

        $this->render('user/form.html.twig', [
            'account' => $account,
            'isEdit' => $isEdit,
            'error' => $error,
        ]);
    }

    public function delete(): void
    {
        $this->app->requireSuperAdmin();
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->redirect('users_list');
        }

        if ($id === (int)$_SESSION['user']['id']) {
            http_response_code(403);
            $this->render('page/forbidden.html.twig', ['message' => 'Vous ne pouvez pas supprimer votre propre compte.']);

            return;
        }

        $user = $this->em()->find(User::class, $id);
        if ($user !== null) {
            $this->em()->remove($user);
            $this->em()->flush();
        }

        $this->redirect('users_list');
    }
}
