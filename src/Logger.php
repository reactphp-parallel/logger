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
    private Channel $channel;

    public function __construct(LoggerInterface $logger, Factory $streamFactory)
    {
        $this-> channel = new Channel(Channel::Infinite);
        $streamFactory->stream($this->channel)->subscribe(
            static function (Item $item) use ($logger) {
                $logger->log($item->level(), $item->message(), $item->context());
            }
        );
    }

    public function __destruct()
    {
        $this->channel->close();
    }

    public function log($level, $message, array $context = array())
    {
        $this->channel->send(new Item((string)$level, $message, $context));
    }
}
