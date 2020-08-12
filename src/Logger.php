<?php

declare(strict_types=1);

namespace ReactParallel\Logger;

use parallel\Channel;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use ReactParallel\Streams\Factory;

use function serialize;
use function unserialize;

final class Logger extends AbstractLogger
{
    private string $channelName;

    public function __construct(LoggerInterface $logger, Factory $streamFactory)
    {
        $channel           = new Channel(Channel::Infinite);
        $this->channelName = (string) $channel;
        $streamFactory->channel($channel)->map(static fn (string $item): Item => unserialize($item))->subscribe(
            static function (Item $item) use ($logger): void {
                $logger->log($item->level(), $item->message(), $item->context());
            }
        );
    }

    public function log($level, $message, array $context = []): void
    {
        Channel::open($this->channelName)->send(serialize(new Item((string) $level, $message, $context)));
    }
}
