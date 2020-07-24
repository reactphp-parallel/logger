<?php

declare(strict_types=1);

namespace ReactParallel\Logger;

use parallel\Channel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use ReactParallel\Streams\Factory;

final class Item
{
    private string $level;
    private string $message;
    private string $context;

    public function __construct(string $level, string $message, array $context)
    {
        $this->level = $level;
        $this->message = $message;
        $this->context = \Opis\Closure\serialize($context);
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
        return \Opis\Closure\unserialize($this->context);
    }
}
