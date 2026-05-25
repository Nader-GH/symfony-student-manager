<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

App\Doctrine\DatabaseInitializer::initialize(app()->em());

echo "Database initialized (Doctrine schema + seeds).\n";
echo "Super Admin: superadmin / super123\n";
echo "Teacher: teacher / teacher123\n";
echo "User: Abdellah Idriss / user123\n";
echo "Open: http://localhost:8000/index.php?r=login\n";
