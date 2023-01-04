<?php

namespace App\Command\Stock;

use App\Entity\Stock;
use App\Repository\StockRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ChangeStocksCommand extends Command
{
    private $tinkoffToken;
    private $stockRepository;

    protected static $defaultName = 'app:stocks:change';
    protected static $defaultDescription = 'Change stocks from tinkoff api';

    public function __construct(string $tinkoffToken, StockRepository $stockRepository)
    {
        $this->tinkoffToken = $tinkoffToken;
        $this->stockRepository = $stockRepository;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $stocks = $this->stockRepository->findAll();

        /** @var Stock $stock */
        foreach ($stocks as $stock) {
            if ($stock->getId() < 1) {
                continue;
            }

            $this->updateStock($stock);

            $io->note(sprintf('id %d, name %s', $stock->getId(), $stock->getName()));
        }

        $io->success('Stocks updated');

        return Command::SUCCESS;
    }

    private function updateStock(Stock $stock): void
    {
        $stock
            ->calculateTwoWeekOpenPercent()
        ;


        $this->stockRepository->save($stock);
    }

}
