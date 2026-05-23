<?php

declare(strict_types=1);

namespace App;

use App\Controller\AnnouncementController;
use App\Controller\AuthController;
use App\Controller\DashboardController;
use App\Controller\HomeController;
use App\Controller\PageController;
use App\Controller\ProfileController;
use App\Controller\SectionController;
use App\Controller\StudentController;
use App\Controller\UserController;
use App\Doctrine\EntityManagerFactory;
use App\Security\AuthService;
use App\Security\RoleMapper;
use App\Twig\AppExtension;
use Doctrine\ORM\EntityManager;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class App
{
    private string $currentRoute = '';
    private EntityManager $em;
    private Environment $twig;
    private AuthService $auth;

    public function __construct(
        private readonly string $projectDir,
    ) {
        $this->startSession();
        $this->em = EntityManagerFactory::create($projectDir);
        $this->auth = AuthService::getInstance();

        $loader = new FilesystemLoader($projectDir . '/templates');
        $this->twig = new Environment($loader, ['cache' => false, 'debug' => true]);
        $this->twig->addExtension(new AppExtension($this));
        $this->twig->addGlobal('app_user', $_SESSION['user'] ?? null);
        $this->twig->addGlobal('current_route', '');
    }

    public function handle(string $route): void
    {
        $this->currentRoute = $route;
        $this->twig->addGlobal('current_route', $route);

        $map = [
            'login' => [AuthController::class, 'login'],
            'logout' => [AuthController::class, 'logout'],
            'home' => [HomeController::class, 'index'],
            'dashboard' => [DashboardController::class, 'index'],
            'students_list' => [StudentController::class, 'list'],
            'student_show' => [StudentController::class, 'show'],
            'student_form' => [StudentController::class, 'form'],
            'student_delete' => [StudentController::class, 'delete'],
            'sections_list' => [SectionController::class, 'list'],
            'section_form' => [SectionController::class, 'form'],
            'section_delete' => [SectionController::class, 'delete'],
            'users_list' => [UserController::class, 'list'],
            'user_form' => [UserController::class, 'form'],
            'user_delete' => [UserController::class, 'delete'],
            'announcements_list' => [AnnouncementController::class, 'list'],
            'announcement_form' => [AnnouncementController::class, 'form'],
            'announcement_delete' => [AnnouncementController::class, 'delete'],
            'statistics' => [PageController::class, 'statistics'],
            'profile' => [ProfileController::class, 'index'],
            'help' => [PageController::class, 'help'],
            'about' => [PageController::class, 'about'],
        ];

        if (!isset($map[$route])) {
            http_response_code(404);
            $this->render('page/not_found.html.twig', ['route' => $route]);

            return;
        }

        [$class, $method] = $map[$route];
        (new $class($this))->$method();
    }

    public function em(): EntityManager
    {
        return $this->em;
    }

    public function projectDir(): string
    {
        return $this->projectDir;
    }

    public function auth(): AuthService
    {
        return $this->auth;
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user']);
    }

    public function currentDbRole(): string
    {
        return (string)($_SESSION['user']['role'] ?? 'user');
    }

    public function hasRole(string $requiredRole): bool
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        return $this->auth->hasRole($this->currentDbRole(), $requiredRole);
    }

    public function isTeacher(): bool
    {
        return $this->hasRole(RoleMapper::ROLE_TEACHER);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(RoleMapper::ROLE_SUPER_ADMIN);
    }

    public function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
        }
    }

    public function requireTeacher(): void
    {
        $this->requireAuth();
        if (!$this->isTeacher()) {
            http_response_code(403);
            $this->render('page/forbidden.html.twig', ['message' => 'Acces reserve aux enseignants et super administrateurs.']);
            exit;
        }
    }

    public function requireSuperAdmin(): void
    {
        $this->requireAuth();
        if (!$this->isSuperAdmin()) {
            http_response_code(403);
            $this->render('page/forbidden.html.twig', ['message' => 'Acces reserve au super administrateur.']);
            exit;
        }
    }

    public function hashPassword(string $plain): string
    {
        return $this->auth->hashPassword($plain);
    }

    public function verifyPassword(string $plain, string $hash): bool
    {
        return $this->auth->verifyPassword($plain, $hash);
    }

    /** @param array<string, mixed> $context */
    public function render(string $template, array $context = []): void
    {
        echo $this->twig->render($template, $context);
    }

    /** @param array<string, int|string> $params */
    public function redirect(string $route, array $params = []): never
    {
        header('Location: ' . $this->url($route, $params));
        exit;
    }

    /** @param array<string, int|string> $params */
    public function url(string $route, array $params = []): string
    {
        $query = http_build_query(array_merge(['r' => $route], $params));

        return 'index.php?' . $query;
    }

    public function isRouteActive(string $route): bool
    {
        return $this->currentRoute === $route
            || str_starts_with($this->currentRoute, rtrim($route, '_') . '_');
    }

    public function syncSessionUser(): void
    {
        if (!$this->isLoggedIn()) {
            return;
        }

        $user = $this->em->find(\App\Entity\User::class, (int)$_SESSION['user']['id']);
        if ($user === null) {
            unset($_SESSION['user']);

            return;
        }

        $_SESSION['user'] = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'role' => $user->getRole(),
        ];
        $this->twig->addGlobal('app_user', $_SESSION['user']);
    }

    private function startSession(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        $defaultSessionPath = (string)session_save_path();
        if ($defaultSessionPath === '' || !is_dir($defaultSessionPath) || !is_writable($defaultSessionPath)) {
            $localSessionPath = $this->projectDir . '/.sessions';
            if (!is_dir($localSessionPath)) {
                mkdir($localSessionPath, 0777, true);
            }
            session_save_path($localSessionPath);
        }

        session_start();
    }
}
