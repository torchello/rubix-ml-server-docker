<?php

require 'vendor/autoload.php';

use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\Server\HTTP\Middleware\Server\AccessLogGenerator;
use Rubix\Server\HTTPServer;
use Rubix\Server\Loggers\File;

$estimator = PersistentModel::load(new Filesystem(getenv('MODEL_FILEPATH')));

$middlewares = [];

if ($accessLogFilepath = getenv('ACCESS_LOG_FILEPATH')) {
    $middlewares[] = new AccessLogGenerator(new File($accessLogFilepath));
}

$server = new HTTPServer(
    getenv('HOST'),
    getenv('PORT'),
    getenv('CERT_FILEPATH') ?: null,
    $middlewares,
    getenv('MAX_CONCURRENT_REQUESTS'),
    null,
    getenv('SSE_RECONNECT_BUFFER')
);

if ($verboseLogFilepath = getenv('VERBOSE_LOG_FILEPATH')) {
    $server->setLogger(new File($verboseLogFilepath));
}

$server->serve($estimator);
