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

    /**
     * @var string
     */
    private $payload;

    public function __construct(Server $server, InfoType $infoType, ?string $payload = null) {
        $this->server = $server;
        $this->infoType = $infoType;
        $this->payload = $payload;
    }

    public function __toString(): string {
        return $this->getHeader($this->infoType) . $this->getPayload();
    }

    public function getHeader(InfoType $infoType): string {
        $header = 'SAMP';
        $octets = explode('.', $this->server->getHost());

        foreach($octets as $octet) {
            $header .= chr($octet);
        }

        $header .= chr($this->server->getPort() & 0xFF);
        $header .= chr($this->server->getPort() >> 8 & 0xFF);
        $header .= $infoType->getType();

        return $header;
    }

    public function getPayload(): string {
        return $this->payload ?? '';
    }
}
