<?php

declare(strict_types=1);

namespace ReactParallel\Logger;

use function Opis\Closure\serialize;
use function Opis\Closure\unserialize;

final class Item
{
    private string $level;
    private string $message;
    private string $context;

    public function __construct(string $level, string $message, array $context)
    {
        $this->level   = $level;
        $this->message = $message;
        $this->context = serialize($context);
    }

    public function level(): string
    {
        return $this->level;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function context(): array
    {
        return unserialize($this->context);
    }
}
