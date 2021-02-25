<?php

namespace PierreMiniggio\TiKTokAccountVideoInformationsSaver\Repository;

use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;

class VideoRepository
{
    public function __construct(private DatabaseFetcher $fetcher)
    {
    }

    public function insertVideoIfNeeded(int $accountId, string $tikTokId, string $videoUrl, string $legend): bool
    {
        $res = $this->fetcher->query(
            $this->fetcher
                ->createQuery('tiktok_video')
                ->select('id')
                ->where('tiktok_id = :tiktok_id')
            ,
            ['tiktok_id' => $tikTokId]
        );

        if ($res) {
            return false;
        }

        $this->fetcher->exec(
            $this->fetcher
                ->createQuery('tiktok_video')
                ->insertInto(
                    'account_id, tiktok_id, tiktok_url, legend',
                    ':account_id, :tiktok_id, :tiktok_url, :legend'
                )
            ,
            [
                'account_id' => $accountId,
                'tiktok_id' => $tikTokId,
                'tiktok_url' => $videoUrl,
                'legend' => $legend
            ]
        );

        return true;
    }
}
