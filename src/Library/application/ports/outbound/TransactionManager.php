<?php

namespace Library\Application\Ports\Outbound;

interface TransactionManager
{
    /** Runs the callback atomically and returns its result; rolls back on any exception. */
    public function run(callable $callback): mixed;
}
