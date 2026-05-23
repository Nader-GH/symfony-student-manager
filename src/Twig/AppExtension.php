<?php

declare(strict_types=1);

namespace App\Twig;

use App\App;
use App\Security\RoleMapper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AppExtension extends AbstractExtension
{
    public function __construct(
        private readonly App $app,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('url', [$this->app, 'url']),
            new TwigFunction('is_teacher', fn (): bool => $this->app->isTeacher()),
            new TwigFunction('is_super_admin', fn (): bool => $this->app->isSuperAdmin()),
            new TwigFunction('is_logged_in', fn (): bool => $this->app->isLoggedIn()),
            new TwigFunction('role_label', fn (string $role): string => RoleMapper::roleLabel($role)),
            new TwigFunction('route_active', [$this->app, 'isRouteActive']),
        ];
    }
}
