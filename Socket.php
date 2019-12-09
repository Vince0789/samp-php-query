<?php

declare(strict_types=1);

require_once('VO/Server.php');

final class Socket
{
    private $server;

    private $resource;

    private $connectionTimeout;

    private function __construct(Server $server, int $connectionTimeout = 5) {
        $this->server = $server;
        $this->connectionTimeout = $connectionTimeout;

        if(!function_exists('fsockopen')) {
            throw new RuntimeException('fsockopen is not available');
        }

        $this->resource = $this->getSocket();
    }

    public function __destruct() {
        $this->closeSocket();
    }

    public static function open(Server $server): self {
        return new self($server);
    }

    public function getServer(): Server {
        return $this->server;
    }

    /**
     * @throws RuntimeException
     */
    private function getSocket() {
        $errno = 0;
        $errstr = '';

        $resource = fsockopen(
            'udp://' . $this->server->getHost(),
            $this->server->getPort(),
            $errno,
            $errstr,
            $this->connectionTimeout
        );

        if($errno !== 0 || !is_resource($resource)) {
            throw new RuntimeException('failed to open socket');
        }

        return $resource;
    }

    private function closeSocket(): bool {
        if(is_resource($this->resource)) {
            return fclose($this->resource);
        }

        return false;
    }

    public function send(Packet $packet): int {
        $bytes = fwrite($this->resource, (string) $packet);
        if($bytes === false) {
            throw new RuntimeException('failed to send packet');
        }

        return $bytes;
    }

    public function readInt(int $bytes = 2): int {
        return ord($this->readRaw($bytes));
    }

    public function readBool(int $bytes = 1): bool {
        return (bool) ord($this->readRaw($bytes));
    }

    public function readString(int $lengthBytes = 4): string {
        $strlen = $this->readInt($lengthBytes);
        return ($strlen > 0) ? $this->readRaw($strlen) : '';
    }

    public function readRaw(int $bytes = 2): string {
        $response = fread($this->resource, $bytes);
        if($response === false) {
            throw new RuntimeException('failed to read response');
        }

        return $response;
    }
}
