<?php

declare(strict_types=1);

require_once('VO/InfoType.php');

final class Packet
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var InfoType
     */
    private $infoType;

    public function __construct(Server $server, InfoType $infoType) {
        $this->server = $server;
        $this->infoType = $infoType;
    }

    public function __toString(): string {
        return $this->getHeader() . $this->infoType->getPayload();
    }

    private function getHeader(): string {
        $header = 'SAMP';
        $octets = explode('.', $this->server->getHost());

        foreach($octets as $octet) {
            $header .= chr($octet);
        }

        $header .= chr($this->server->getPort() & 0xFF);
        $header .= chr($this->server->getPort() >> 8 & 0xFF);

        return $header;
    }
}
