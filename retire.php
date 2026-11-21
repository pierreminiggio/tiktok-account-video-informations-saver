<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;
use PierreMiniggio\TiKTokAccountVideoInformationsSaver\Command\RetireAccountCommand;
use PierreMiniggio\TiKTokAccountVideoInformationsSaver\Connection\DatabaseConnectionFactory;
use PierreMiniggio\TiKTokAccountVideoInformationsSaver\Repository\AccountRepository;

try {
    $config = require __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

    if (empty($config['db'])) {
        echo 'No DB config' . PHP_EOL;
        exit(1);
    }

    $databaseFetcher = new DatabaseFetcher((new DatabaseConnectionFactory())->makeFromConfig($config['db']));

    exit((new RetireAccountCommand(new AccountRepository($databaseFetcher)))->run($argv));
} catch (Throwable $e) {
    echo get_class($e) . ' : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
