<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$container = (require __DIR__ . '/../src/config/container.php')();
$app = (require __DIR__ . '/../src/config/app.php')($container);

$app->run();