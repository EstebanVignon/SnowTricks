<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\TokenHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PurgeTokenHistoryCommand extends Command
{
    /**
     * @var TokenHistoryRepository
     */
    private TokenHistoryRepository $tokenHistoryRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    protected static $defaultName = 'tokens:purge';

    public function __construct(
        TokenHistoryRepository $tokenHistoryRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->tokenHistoryRepository = $tokenHistoryRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tokens = $this->tokenHistoryRepository->findAllTokenOlderThanDaysNumber(7);
        foreach ($tokens as $token) {
            $this->entityManager->remove($token);
        }
        $this->entityManager->flush();
        return Command::SUCCESS;
    }
}
