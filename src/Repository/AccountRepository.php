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

    /**
     * @return array<int, array{id: int|string, retired_at: ?string}>
     */
    public function findByName(string $tikTokName): array
    {
        return $this->fetcher->query(
            $this->fetcher
                ->createQuery('tiktok_account')
                ->select('id', 'retired_at')
                ->where('tiktok_name = :tiktok_name')
            ,
            ['tiktok_name' => $tikTokName]
        );
    }

    public function retire(int $accountId): void
    {
        $this->fetcher->exec(
            $this->fetcher
                ->createQuery('tiktok_account')
                ->update('retired_at = NOW()')
                ->where('id = :id')
                ->andWhere('retired_at IS NULL')
            ,
            ['id' => $accountId]
        );
    }
}
