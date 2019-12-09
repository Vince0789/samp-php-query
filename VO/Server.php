<?php

declare(strict_types=1);

final class Server
{
    private $host;

    private $port;

    /**
     * @throws InvalidArgumentException
     */
    private function __construct(string $host, int $port) {
        $validHost = filter_var($host, FILTER_VALIDATE_IP, array('flags' => FILTER_FLAG_IPV4));
        
        if($validHost === false) {
            throw new InvalidArgumentException('Not a valid IP address. Hostnames are not supported at this point.');
        }

        $this->host = $host;
        $this->port = $port;
    }

    public static function create(string $host, int $port): self {
        return new self($host, $port);
    }

    public function getHost(): string {
        return $this->host;
    }

    public function getPort(): int {
        return $this->port;
    }
}
