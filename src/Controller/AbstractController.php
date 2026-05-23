<?php

declare(strict_types=1);

namespace App\Controller;

use App\App;
use Doctrine\ORM\EntityManager;

abstract class AbstractController
{
    public function __construct(
        protected readonly App $app,
    ) {
    }

    protected function em(): EntityManager
    {
        return $this->app->em();
    }

    /** @param array<string, mixed> $context */
    protected function render(string $template, array $context = []): void
    {
        $this->app->render($template, $context);
    }

    /** @param array<string, int|string> $params */
    protected function redirect(string $route, array $params = []): never
    {
        $this->app->redirect($route, $params);
    }
}
