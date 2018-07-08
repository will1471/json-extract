<?php
declare(strict_types=1);

namespace Will1471\JsonExtract;

final class Token
{
    private $type;
    private $match;

    public function __construct(int $type, string $match)
    {
        $this->type = $type;
        $this->match = $match;
    }

    public function type(): int
    {
        return $this->type;
    }

    public function __toString(): string
    {
        return $this->match;
    }
}
