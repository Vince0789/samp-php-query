<?php

declare(strict_types=1);

require_once('VO/Server.php');

final class Socket
{
    private $server;

    private $resource;

    private $connectionTimeout;

    /**
     * @throws RuntimeException
     */
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

    public static function open(Server $server, int $connectionTimeout = 5): self {
        return new self($server, $connectionTimeout);
    }

    public function getServer(): Server {
        return $this->server;
    }

    public function getConnectionTimeout(): int {
        return $this->connectionTimeout;
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
            throw new RuntimeException(sprintf('Failed to open socket (error %d: %s).', $errno, $errstr));
        }

        stream_set_timeout($resource, $this->connectionTimeout);

        return $resource;
    }

    private function closeSocket(): bool {
        if(is_resource($this->resource)) {
            return fclose($this->resource);
        }

        return false;
    }

    /**
     * @throws RuntimeException
     */
    public function send(Packet $packet): int {
        $bytes = fwrite($this->resource, (string) $packet);
        if($bytes === false) {
            throw new RuntimeException('Failed to send packet.');
        }

        return $bytes;
    }

    public function readBool(int $bytes = 1): bool {
        return (bool) $this->readInt($bytes);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function readInt(int $bytes): int {
        if($bytes < 1 || $bytes > 4) {
            throw new InvalidArgumentException(sprintf('cannot read integer of %d bytes', $bytes));
        }

        $buffer = $this->readRaw($bytes);
        $int = 0;

        for($i = 0; $i < $bytes; $i++) {
            $int |= ord($buffer{$i}) << ($i * 8);
        }

        return $int;
    }

    public function readInt8(): int {
        return $this->readInt(1);
    }

    public function readInt16(): int {
        return $this->readInt(2);
    }

    public function readInt32(): int {
        return $this->readInt(4);
    }

    public function readString(int $lengthBytes = 4): string {
        $strlen = $this->readInt($lengthBytes);
        return ($strlen > 0) ? $this->readRaw($strlen) : '';
    }

    /**
     * @throws RuntimeException
     */
    public function readRaw(int $bytes): string {
        $response = fread($this->resource, $bytes);
        if($response === false) {
            throw new RuntimeException(sprintf('Failed to read response (bytes: %d).', $bytes));
        }

        return $response;
    }
}
