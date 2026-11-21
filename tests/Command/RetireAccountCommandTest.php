<?php

namespace PierreMiniggio\TiKTokAccountVideoInformationsSaver\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PierreMiniggio\TiKTokAccountVideoInformationsSaver\Command\RetireAccountCommand;
use PierreMiniggio\TiKTokAccountVideoInformationsSaver\Repository\AccountRepository;

class RetireAccountCommandTest extends TestCase
{
    /** @var AccountRepository&MockObject */
    private AccountRepository $repository;
    private RetireAccountCommand $command;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(AccountRepository::class);
        $this->command = new RetireAccountCommand($this->repository);
    }

    public function testItShowsTheUsageWhenNoAccountNameIsGiven(): void
    {
        $this->repository->expects(self::never())->method('findByName');
        $this->repository->expects(self::never())->method('retire');

        $this->expectOutputString('Usage : php retire.php <tiktok_name>' . PHP_EOL);

        self::assertSame(1, $this->command->run(['retire.php']));
    }

    public function testItFailsWhenTheAccountDoesNotExist(): void
    {
        $this->repository->method('findByName')->with('nobody')->willReturn([]);
        $this->repository->expects(self::never())->method('retire');

        $this->expectOutputString('No account named nobody' . PHP_EOL);

        self::assertSame(1, $this->command->run(['retire.php', 'nobody']));
    }

    public function testItDoesNothingWhenTheAccountIsAlreadyRetired(): void
    {
        $this->repository
            ->method('findByName')
            ->with('someone')
            ->willReturn([['id' => 3, 'retired_at' => '2026-11-01 10:00:00']])
        ;
        $this->repository->expects(self::never())->method('retire');

        $this->expectOutputString('Account someone is already retired' . PHP_EOL);

        self::assertSame(0, $this->command->run(['retire.php', 'someone']));
    }

    public function testItRetiresAnActiveAccount(): void
    {
        $this->repository
            ->method('findByName')
            ->with('someone')
            ->willReturn([['id' => 3, 'retired_at' => null]])
        ;
        $this->repository->expects(self::once())->method('retire')->with(3);

        $this->expectOutputString('Account someone retired' . PHP_EOL);

        self::assertSame(0, $this->command->run(['retire.php', 'someone']));
    }

    public function testItRetiresEveryActiveAccountSharingTheNameAndSkipsRetiredOnes(): void
    {
        $this->repository
            ->method('findByName')
            ->with('someone')
            ->willReturn([
                ['id' => 3, 'retired_at' => null],
                ['id' => 4, 'retired_at' => '2026-11-01 10:00:00'],
                ['id' => 5, 'retired_at' => null],
            ])
        ;
        $retiredIds = [];
        $this->repository
            ->expects(self::exactly(2))
            ->method('retire')
            ->willReturnCallback(function (int $id) use (&$retiredIds): void {
                $retiredIds[] = $id;
            })
        ;

        $this->expectOutputString('Account someone retired' . PHP_EOL);

        self::assertSame(0, $this->command->run(['retire.php', 'someone']));
        self::assertSame([3, 5], $retiredIds);
    }
}
