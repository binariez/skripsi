<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once 'SessionHandlerInterface.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

$uri = 'mongodb+srv://' . $_ENV['MDB_USER'] . ':' . $_ENV['MDB_PASS'] . '@' . $_ENV['ATLAS_CLUSTER_SRV'] . '/?retryWrites=true&w=majority&appName=' . $_ENV['APP_NAME'];

$client = new MongoDB\Client($uri);

$db = $client->crm_nafisah;
