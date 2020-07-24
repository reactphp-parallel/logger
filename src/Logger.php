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

final class Logger extends AbstractLogger
{
    private string $channelName;

    public function __construct(LoggerInterface $logger, Factory $streamFactory)
    {
        $channel = new Channel(Channel::Infinite);
        $this->channelName = (string)$channel;
        $streamFactory->stream($channel)->map(static fn (string $item): Item => unserialize($item))->subscribe(
            static function (Item $item) use ($logger) {
                $logger->log($item->level(), $item->message(), $item->context());
            }
        );
    }

    public function log($level, $message, array $context = array())
    {
        Channel::open($this->channelName)->send(serialize(new Item((string)$level, $message, $context)));
    }
}
