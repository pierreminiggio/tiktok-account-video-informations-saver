<?php

namespace PierreMiniggio\TiKTokAccountVideoInformationsSaver\Command;

use PierreMiniggio\TiKTokAccountVideoInformationsSaver\Repository\AccountRepository;

class RetireAccountCommand
{
    public function __construct(private AccountRepository $accountRepository)
    {
    }

    /**
     * @param string[] $argv The CLI arguments, the first one being the script name
     *
     * @return int The exit code
     */
    public function run(array $argv): int
    {
        $accountName = $argv[1] ?? null;

        if ($accountName === null || $accountName === '') {
            echo 'Usage : php retire.php <tiktok_name>' . PHP_EOL;

            return 1;
        }

        $accounts = $this->accountRepository->findByName($accountName);

        if (! $accounts) {
            echo 'No account named ' . $accountName . PHP_EOL;

            return 1;
        }

        $activeAccounts = array_filter($accounts, fn (array $account): bool => $account['retired_at'] === null);

        if (! $activeAccounts) {
            echo 'Account ' . $accountName . ' is already retired' . PHP_EOL;

            return 0;
        }

        foreach ($activeAccounts as $account) {
            $this->accountRepository->retire((int) $account['id']);
        }

        echo 'Account ' . $accountName . ' retired' . PHP_EOL;

        return 0;
    }
}
