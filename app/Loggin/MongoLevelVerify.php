<?php

namespace App\Logging;

use Illuminate\Log\Logger;
use Monolog\Level;

class MongoLevelVerify
{
    public function __invoke(Logger $logger)
    {
        dd('chegou aqui');
    }
}
