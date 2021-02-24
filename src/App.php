<?php

namespace PierreMiniggio\TiKTokAccountVideoInformationsSaver;

use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;
use PierreMiniggio\TiKTokAccountVideoInformationsSaver\Connection\DatabaseConnectionFactory;
use PierreMiniggio\TiKTokAccountVideoInformationsSaver\Repository\AccountRepository;

class App
{

    public function run(): int
    {

        $code = 0;

        $config = require(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.php');

        if (empty($config['db'])) {
            echo 'No DB config';

            return $code;
        }

        $databaseFetcher = new DatabaseFetcher((new DatabaseConnectionFactory())->makeFromConfig($config['db']));
        $channelRepository = new AccountRepository($databaseFetcher);

        $accounts = $channelRepository->findAll();

        if (! $accounts) {
            echo 'No accounts';

            return $code;
        }

        foreach ($accounts as $account) {
            echo PHP_EOL . PHP_EOL . 'Checking account ' . $account['tiktok_name'] . '...';

            // TODO call puppeteer script and get videos

            // TODO save videos if not present

            echo PHP_EOL . PHP_EOL . 'Done for account ' . $account['tiktok_name'] . ' !';
        }

        return $code;
    }
}
