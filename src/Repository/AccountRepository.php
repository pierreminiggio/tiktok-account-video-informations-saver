<?php

namespace PierreMiniggio\TiKTokAccountVideoInformationsSaver\Repository;

use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;

class AccountRepository
{
    public function __construct(private DatabaseFetcher $fetcher)
    {
    }

    public function findActive(): array
    {
        return $this->fetcher->query(
            $this->fetcher
                ->createQuery('tiktok_account')
                ->select('id', 'tiktok_name', 'api_url', 'api_token')
                ->where('retired_at IS NULL')
        );
    }
}
