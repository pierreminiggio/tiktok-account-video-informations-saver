<?php

namespace PierreMiniggio\TiKTokAccountVideoInformationsSaver;

use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;
use PierreMiniggio\TiKTokAccountVideoInformationsSaver\Connection\DatabaseConnectionFactory;
use PierreMiniggio\TiKTokAccountVideoInformationsSaver\Repository\AccountRepository;
use PierreMiniggio\TiKTokAccountVideoInformationsSaver\Repository\VideoRepository;

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
        $videoRepository = new VideoRepository($databaseFetcher);

        $accounts = $channelRepository->findAll();

        if (! $accounts) {
            echo 'No accounts';

            return $code;
        }

        foreach ($accounts as $account) {
            $accountName = $account['tiktok_name'];
            echo PHP_EOL . PHP_EOL . 'Checking account ' . $accountName . '...';
            $scrapingResult = shell_exec('node scrape.js ' . escapeshellarg($accountName));
            
            if (empty($scrapingResult)) {
                echo PHP_EOL . PHP_EOL . 'No video or error for ' . $accountName . ' !';

                continue;
            }

            $jsonResult = json_decode($scrapingResult, true);

            if (empty($jsonResult)) {
                echo PHP_EOL . PHP_EOL . 'No video or JSON error for ' . $accountName . ' !';

                continue;
            }

            $videoLinks = array_reverse($jsonResult);

            echo PHP_EOL . PHP_EOL . 'Inserting the ' . count($videoLinks) . ' videos if needed !' . PHP_EOL;
            
            foreach ($videoLinks as $videoLink) {
                $videoId = substr($videoLink, strlen('https://www.tiktok.com/@' . $accountName . '/video/'));
                $inserted = $videoRepository->insertVideoIfNeeded(
                    (int) $account['id'],
                    $videoId,
                    $videoLink
                );
                echo PHP_EOL . ($inserted
                    ? ('Video ' . $videoId . ' inserted !')
                    : ('Video ' . $videoId . ' already saved.')
                );
            }

            echo PHP_EOL . PHP_EOL . 'Done for account ' . $accountName . ' !';
        }

        return $code;
    }
}
