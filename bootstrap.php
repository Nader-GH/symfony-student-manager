<?php

declare(strict_types=1);

use App\App;

require_once __DIR__ . '/vendor/autoload.php';

function app(): App
{
    static $instance = null;
    if ($instance === null) {
        $instance = new App(__DIR__);
    }

    return $instance;
}
