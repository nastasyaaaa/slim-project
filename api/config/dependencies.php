<?php

use Laminas\ConfigAggregator\PhpFileProvider;
use Laminas\ConfigAggregator\ConfigAggregator;

$aggregator = new ConfigAggregator([
    new PhpFileProvider(__DIR__ . '/common/*.php'),
    new PhpFileProvider(__DIR__ . '/' . (getenv('APP_ENV') ?: 'prod') . '/*.php'),
]);

return $aggregator->getMergedConfig();