<?php

declare(strict_types=1);

use Leobro\SunnyCustomer\Infrastructure\Bootstrap\Bootstrap;
use Leobro\SunnyCustomer\Infrastructure\Http\Request;

require_once __DIR__ . '/../vendor/autoload.php';

$request = Request::fromGlobals();

$config = require __DIR__ . '/../config/database.php';
$bootstrap = new Bootstrap($config);
$router = $bootstrap->createRouter();

$response = $router->handle($request);
$response->send();
