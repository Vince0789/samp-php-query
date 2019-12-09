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

    public function getServerInfo(): array {
        $packet = new Packet(
            $this->socket->getServer(),
            InfoType::fromName('info')
        );
        
        $this->socket->send($packet);
        $this->socket->readRaw(11); // strip header

        return [
            'password'      => $this->socket->readBool(),
            'players'       => $this->socket->readInt(),
            'max_players'   => $this->socket->readInt(),
            'hostname'      => $this->socket->readString(),
            'gamemode'      => $this->socket->readString(),
            'language'      => $this->socket->readString(),
        ];
    }
}
