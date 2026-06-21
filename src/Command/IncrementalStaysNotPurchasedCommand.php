<?php

namespace App\Command;

use App\Service\ExpiredReservationCleanupService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:unpaidorder', description: 'Cancel reservations not purchased within 15 minutes and release seats')]
final class IncrementalStaysNotPurchasedCommand extends Command
{
    public function __construct(private readonly ExpiredReservationCleanupService $cleanupService)
    {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $result = $this->cleanupService->cleanup();

        $io->success(sprintf(
            '%d seats in %d reservations not purchased removed',
            $result['seats_released'],
            $result['total_unpaid'],
        ));

        return Command::SUCCESS;
    }
}
