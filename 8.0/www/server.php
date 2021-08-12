<?php

require 'vendor/autoload.php';

use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\Server\HTTP\Middleware\Server\AccessLogGenerator;
use Rubix\Server\HTTP\Middleware\Server\BasicAuthenticator;
use Rubix\Server\HTTP\Middleware\Server\SharedTokenAuthenticator;
use Rubix\Server\HTTP\Middleware\Server\TrustedClients;
use Rubix\Server\HTTPServer;
use Rubix\Server\Loggers\File;

$estimator = PersistentModel::load(new Filesystem(getenv('MODEL_FILEPATH')));

$middlewares = [];

if ($accessLogFilepath = getenv('ACCESS_LOG_FILEPATH')) {
    $middlewares[] = new AccessLogGenerator(new File($accessLogFilepath));
}

if (getenv('BASIC_AUTHENTICATOR_USERNAME') && getenv('BASIC_AUTHENTICATOR_PASSWORD')) {
    $middlewares[] = new BasicAuthenticator([
        getenv('BASIC_AUTHENTICATOR_USERNAME') => getenv('BASIC_AUTHENTICATOR_PASSWORD'),
    ], getenv('BASIC_AUTHENTICATOR_REALM'));
}

if ($tokens = getenv('SHARED_TOKEN_AUTHENTICATOR_TOKENS')) {
    $middlewares[] = new SharedTokenAuthenticator(
        explode(',', $tokens),
        getenv('SHARED_TOKEN_AUTHENTICATOR_REALM')
    );
}

if ($ips = getenv('TRUSTED_CLIENTS_IPS')) {
    $middlewares[] = new TrustedClients(explode(',', $ips));
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
