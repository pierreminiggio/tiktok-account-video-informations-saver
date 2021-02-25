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

            $curl = curl_init($account['api_url']);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $curlResponse = curl_exec($curl);
            
            if (empty($curlResponse)) {
                echo PHP_EOL . PHP_EOL . 'No video or error for ' . $accountName . ' !';

                continue;
            }

            $curlJsonResponse = json_decode($curlResponse, true);

            if (empty($curlJsonResponse)) {
                echo PHP_EOL . PHP_EOL . 'No video or JSON error for ' . $accountName . ' !';

                continue;
            }

            if (! isset($curlJsonResponse['itemList'])) {
                echo PHP_EOL . PHP_EOL . 'No bad item list for ' . $accountName . ' !';

                continue;
            }

            $videos = array_reverse($curlJsonResponse['itemList']);

            echo PHP_EOL . PHP_EOL . 'Inserting the ' . count($videos) . ' videos if needed !' . PHP_EOL;
            
            foreach ($videos as $video) {
                $videoId = $video['id'];
                $inserted = $videoRepository->insertVideoIfNeeded(
                    (int) $account['id'],
                    $videoId,
                    'https://www.tiktok.com/@pierreminiggio/video/' . $videoId,
                    $video['desc']
                );
                echo PHP_EOL . ($inserted
                    ? ('Video ' . $videoId . ' inserted !')
                    : ('Video ' . $videoId . ' already saved.')
                );
            }

            echo PHP_EOL . PHP_EOL . 'Done for account ' . $accountName . ' !';

            curl_close($curl);
        }

        return $code;
    }
}
