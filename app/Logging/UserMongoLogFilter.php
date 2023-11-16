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
     * Filtra os registros com base em algum critério.
     *
     * @param  array  $record
     * @return array|null
     */
    public function filterRecords(array $record)
    {
        // Personalize esta lógica para filtrar os registros com base no tipo de erro ou outras condições
        // Por exemplo, aceitar apenas registros com um contexto 'user_id'
        if (isset($record['context']['user_id'])) {
            return $record; // Aceita registros com 'user_id'
        }

        // Ignora outros tipos de registros
        return null;
    }
}
