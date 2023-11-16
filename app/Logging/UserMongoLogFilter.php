<?php

namespace App\Logging;

use Monolog\Logger;

class UserMongoLogFilter
{
    /**
     * Customize the given Monolog instance.
     *
     * @param  \Monolog\Logger  $logger
     * @return void
     */
    public function __invoke(Logger $logger)
    {
        $logger->pushProcessor([$this, 'filterRecords']);
    }

    /**
     * Filtra os registros com base do id do user.
     *
     * @param  array  $record
     * @return array|null
     */
    public function filterRecords(array $record)
    {

        if (isset($record['context']['user_id'])) {
            return $record;
        }

        return null;
    }
}
