<?php

use Gateway\Gateway;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

require_once __DIR__ . "/src/Gateway.php";

$gateway = new Gateway();

$gateway->start();
