<?php

declare(strict_types=1);

final class InfoType 
{
    const ALLOWED_TYPES = [
        'info' => 'i',
        'rules' => 'r',
        'basic_players' => 'c',
        'detailed_players' => 'd',
        'rcon' => 'x',
        'ping' => 'p',
    ];

    private $infoType;

    private function __construct(string $infoType) {
        if(!in_array($infoType, array_keys(self::ALLOWED_TYPES), true)) {
            throw new InvalidArgumentException(sprintf('invalid info type detected: %s', $infoType));
        }

        $this->infoType = $infoType;
    }

    public static function fromName(string $infoType): self {
        return new self($infoType);
    }

    public static function fromPayload(string $payload): self {
        if(!in_array($payload, self::ALLOWED_TYPES, true)) {
            throw new InvalidArgumentException(sprintf('invalid info type payload detected: %s', $payload));
        }

        $infoType = array_search($payload);
        return new self($infoType);
    }

    public function __toString(): string {
        return (string) $this->infoType;
    }

    public function getPayload(): string {
        return (string) self::ALLOWED_TYPES[$this->infoType];
    }
}
