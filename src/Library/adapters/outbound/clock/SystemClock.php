<?php

namespace Library\Adapters\Outbound\Clock;

use DateTimeImmutable;
use DateTimeZone;
use Library\Application\Ports\Outbound\Clock;

class SystemClock implements Clock
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}
