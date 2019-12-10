<?php

declare(strict_types=1);

require_once('Packet.php');
require_once('Socket.php');
require_once('VO/InfoType.php');

final class Query
{
    private $socket;

    public function __construct(Socket $socket) {
        $this->socket = $socket;
    }

    /**
     * @throws RuntimeException
     */
    public function ping(): int {
        $packet = new Packet(
            $this->socket->getServer(), 
            InfoType::fromName('ping'),
            $payload = (string) mt_rand(1000, 9999)
        );

        $start = microtime(true);
        $this->socket->send($packet);
        $this->socket->readRaw(11);

        if($this->socket->readRaw(4) !== $payload) {
            throw new RuntimeException('Malformed response.');
        }

        $ping = (microtime(true) - $start) * 1000;

        return (int) ceil($ping);
    }

    public function getServerInfo(): array {
        $packet = new Packet(
            $this->socket->getServer(),
            InfoType::fromName('info')
        );
        
        $this->socket->send($packet);
        $this->socket->readRaw(11); // strip header

        return [
            'password'      => $this->socket->readBool(),
            'players'       => $this->socket->readInt16(),
            'max_players'   => $this->socket->readInt16(),
            'hostname'      => $this->socket->readString(),
            'gamemode'      => $this->socket->readString(),
            'language'      => $this->socket->readString(),
        ];
    }

    public function getRules(): array {
        $packet = new Packet(
            $this->socket->getServer(), 
            InfoType::fromName('rules')
        );

        $this->socket->send($packet);
        $this->socket->readRaw(11);

        $ruleCount = $this->socket->readInt16();
        $rules = [];

        for($i = 0; $i < $ruleCount; $i++) {
            $rule = $this->socket->readString(1);
            $value = $this->socket->readString(1);

            $rules[$rule] = $value;
        }

        return $rules;
    }

    public function getBasicPlayers(): array {
        $packet = new Packet(
            $this->socket->getServer(),
            InfoType::fromName('basic_players')
        );

        $this->socket->send($packet);
        $this->socket->readRaw(11);

        $playerCount = $this->socket->readInt16();
        $players = [];

        for($i = 0; $i < $playerCount; $i++) {
            $name = $this->socket->readString(1);
            $score = $this->socket->readInt32();

            $players[$name] = $score;
        }

        return $players;
    }

    public function getDetailedPlayers(): array {
        $packet = new Packet(
            $this->socket->getServer(),
            InfoType::fromName('detailed_players')
        );

        $this->socket->send($packet);
        $this->socket->readRaw(11);

        $playerCount = $this->socket->readInt16();
        $players = [];

        for($i = 0; $i < $playerCount; $i++) {
            $playerId = $this->socket->readInt8(); // only 0 - 255 possible
            
            $players[$playerId] = [
                'name' => $this->socket->readString(1),
                'score' => $this->socket->readInt32(),
                'ping' => $this->socket->readInt32(),
            ];
        }

        return $players;
    }
}
