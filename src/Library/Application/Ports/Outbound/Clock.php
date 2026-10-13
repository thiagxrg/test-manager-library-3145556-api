<?php

namespace Library\Application\Ports\Outbound;

use DateTimeImmutable;

interface Clock
{
    public function now(): DateTimeImmutable;
}
