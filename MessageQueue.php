<?php
declare(strict_types=1);

namespace MessageQueue;

use RuntimeException;
use SysvMessageQueue;

use function msg_get_queue;
use function msg_receive;
use function msg_send;
use function msg_stat_queue;

final class MessageQueue
{
    private readonly SysvMessageQueue $queue;

    public function __construct(
        int $queueId,
        private readonly int $maxSize = 1024
    ) {
        $queue = msg_get_queue($queueId);
        if ($queue === false) {
            throw new RuntimeException('Failed to get message queue');
        }

        $this->queue = $queue;
    }

    public function send(string $message, int $msgType = 1): bool
    {
        return msg_send($this->queue, $msgType, $message);
    }

    public function receive(int $desiredMsgType, int &$msgType, string &$message): bool
    {
        return msg_receive($this->queue, $desiredMsgType, $msgType, $this->maxSize, $message);
    }

    public function stat(): array
    {
        return msg_stat_queue($this->queue);
    }
}