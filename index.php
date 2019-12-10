<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors','On');

require_once('Socket.php');
require_once('Query.php');
require_once('VO/Server.php');

$type = filter_input(INPUT_GET, 'type');

$socket = Socket::open(Server::create('127.0.0.1', 7777));
$query = new Query($socket);

switch($type)
{
    case 'ping':
        $content = [ 'ping' => $query->ping() ];
        break;
    case 'info':
        $content = $query->getServerInfo();
        break;
    case 'rules':
        $content = $query->getRules();
        break;
    case 'basic_players':
        $content = $query->getBasicPlayers();
        break;
    case 'detailed_players':
        $content = $query->getDetailedPlayers();
        break;
    default:
        $content = ['error' => 'invalid type: ' . $type];
}

$response = json_encode($content);

header('Content-Type: application/json');
header('Content-Length: ' . mb_strlen($response));

echo $response;
die();
