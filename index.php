<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

if (isset($_GET['r'])) {
    app()->handle((string)$_GET['r']);
    exit;
}

if (app()->isLoggedIn()) {
    app()->redirect('dashboard');
}

app()->redirect('login');
