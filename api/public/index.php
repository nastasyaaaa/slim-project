<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

Sentry\init(['dsn' => 'https://f03f134abcf44defafdac6b38b238475@o434717.ingest.sentry.io/5392124' ]);

$container = (require __DIR__ . '/../config/container.php')();
$app = (require __DIR__ . '/../config/app.php')($container);

$app->run();