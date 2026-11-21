<?php

namespace PierreMiniggio\TiKTokAccountVideoInformationsSaver\Tests\Repository;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PierreMiniggio\DatabaseConnection\DatabaseConnection;
use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;
use PierreMiniggio\TiKTokAccountVideoInformationsSaver\Repository\AccountRepository;

class AccountRepositoryTest extends TestCase
{
    /** @var DatabaseConnection&MockObject */
    private DatabaseConnection $connection;
    private AccountRepository $repository;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(DatabaseConnection::class);
        $this->repository = new AccountRepository(new DatabaseFetcher($this->connection));
    }

    public function testFindActiveReturnsTheAccountsFromTheDatabase(): void
    {
        $accounts = [
            ['id' => 1, 'tiktok_name' => 'first', 'api_url' => 'https://api/1', 'api_token' => 'token1'],
            ['id' => 2, 'tiktok_name' => 'second', 'api_url' => 'https://api/2', 'api_token' => 'token2'],
        ];
        $this->connection->method('query')->willReturn($accounts);

        self::assertSame($accounts, $this->repository->findActive());
    }

    public function testFindActiveOnlyAsksForAccountsThatAreNotRetired(): void
    {
        $this->connection
            ->expects(self::once())
            ->method('query')
            ->with(
                'SELECT id,tiktok_name,api_url,api_token FROM tiktok_account WHERE retired_at IS NULL',
                []
            )
            ->willReturn([])
        ;

        $this->repository->findActive();
    }

    public function testFindByNameReturnsTheAccountsWithThisName(): void
    {
        $rows = [['id' => 3, 'retired_at' => null]];

        $this->connection
            ->expects(self::once())
            ->method('query')
            ->with(
                'SELECT id,retired_at FROM tiktok_account WHERE tiktok_name = :tiktok_name',
                ['tiktok_name' => 'someone']
            )
            ->willReturn($rows)
        ;

        self::assertSame($rows, $this->repository->findByName('someone'));
    }

    public function testRetireSetsTheRetirementDateOnlyIfTheAccountIsNotAlreadyRetired(): void
    {
        $this->connection
            ->expects(self::once())
            ->method('exec')
            ->with(
                'UPDATE tiktok_account SET retired_at = NOW() WHERE id = :id AND retired_at IS NULL',
                ['id' => 3]
            )
        ;

        $this->repository->retire(3);
    }
}
