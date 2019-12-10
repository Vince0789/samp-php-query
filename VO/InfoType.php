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
        $this->infoType = $infoType;
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function fromName(string $name): self {
        if(!in_array($name, array_keys(self::ALLOWED_TYPES), true)) {
            throw new InvalidArgumentException(sprintf('invalid info type name detected: %s', $name));
        }
        return new self($name);
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function fromType(string $type): self {
        $name = array_search($type, self::ALLOWED_TYPES);
        if($name === false) {
            throw new InvalidArgumentException(sprintf('invalid info type detected: %s', $type));
        }

        return new self($name);
    }

    public function __toString(): string {
        return (string) $this->infoType;
    }

    public function getType(): string {
        return (string) self::ALLOWED_TYPES[$this->infoType];
    }
}
