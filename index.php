<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors','On');

require_once('Socket.php');
require_once('Query.php');
require_once('VO/Server.php');

$socket = Socket::open(Server::create('127.0.0.1', 7777));
$query = new Query($socket);

$serverInfo = $query->getServerInfo();
$response = json_encode($serverInfo);

header('Content-Type: application/json');
header('Content-Length: ' . mb_strlen($response));

echo $response;
die();
