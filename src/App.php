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
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $account['api_token']
                ]
            ]);
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

            $videos = array_reverse($curlJsonResponse);

            echo PHP_EOL . PHP_EOL . 'Inserting the ' . count($videos) . ' videos if needed !' . PHP_EOL;
            
            foreach ($videos as $videoIndex => $video) {

                if (! isset($video['id']) || ! isset($video['caption']) || ! isset($video['url'])) {
                    echo PHP_EOL . 'Missing data for video ' . $videoIndex;
                    continue;
                }

                $videoId = $video['id'];
                $inserted = $videoRepository->insertVideoIfNeeded(
                    (int) $account['id'],
                    $videoId,
                    $video['url'],
                    $video['caption']
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
