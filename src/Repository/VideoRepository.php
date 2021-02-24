<?php

namespace PierreMiniggio\TiKTokAccountVideoInformationsSaver\Repository;

use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;

class VideoRepository
{
    public function __construct(private DatabaseFetcher $fetcher)
    {
    }

    public function insertVideoIfNeeded(int $accountId, string $tikTokId, string $videoUrl): bool
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
                    'account_id, tiktok_id, tiktok_url',
                    ':account_id, :tiktok_id, :tiktok_url'
                )
            ,
            [
                'account_id' => $accountId,
                'tiktok_id' => $tikTokId,
                'tiktok_url' => $videoUrl
            ]
        );

        return true;
    }
}
