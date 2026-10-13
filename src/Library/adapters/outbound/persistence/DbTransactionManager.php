<?php

namespace Library\Adapters\Outbound\Persistence;

use Illuminate\Support\Facades\DB;
use Library\Application\Ports\Outbound\TransactionManager;

class DbTransactionManager implements TransactionManager
{
    public function run(callable $callback): mixed
    {
        return DB::transaction($callback);
    }
}
