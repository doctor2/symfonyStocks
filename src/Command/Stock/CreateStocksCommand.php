<?php

namespace App\Command\Stock;

use App\Entity\Stock;
use App\Repository\StockRepository;
use Dzhdmitry\TinkoffInvestApi\Rest\ClientFactory;
use Dzhdmitry\TinkoffInvestApi\Rest\Schema\Payload\MarketInstrument;
use Exception;
use jamesRUS52\TinkoffInvest\TIClient;
use jamesRUS52\TinkoffInvest\TISiteEnum;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateStocksCommand extends Command
{
    private $tinkoffToken;
    private $stockRepository;

    protected static $defaultName = 'app:stocks:create';
    protected static $defaultDescription = 'Create stocks from tinkoff api';

    public function __construct(string $tinkoffToken, StockRepository $stockRepository)
    {
        $this->tinkoffToken = $tinkoffToken;
        $this->stockRepository = $stockRepository;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $client = (new ClientFactory())->create($this->tinkoffToken);
        try{
            $client = new TIClient($this->tinkoffToken,TISiteEnum::SANDBOX);
            dd($client->getStocks());
            // $response = $client->market()->getStocks();

        } catch(Exception $e) {
        dd($e->getMessage());
            
        }


        $stockFigis = $this->stockRepository->findAllFigis();

        /** @var MarketInstrument $instrument */
        foreach ($response->getPayload()->getInstruments() as $instrument) {
            if (in_array($instrument->getFigi(), $stockFigis, true)) {
                continue;
            }

            $this->createStock($instrument);

            $io->note($instrument->getName());
        }

        $io->success('Stocks created');

        return Command::SUCCESS;
    }

    public function createStock(MarketInstrument $instrument): void
    {
        $stock = new Stock(
            $instrument->getFigi(),
            $instrument->getTicker(),
            $instrument->getCurrency(),
            $instrument->getName(),
        );

        $stock->setIsin($instrument->getIsin());

        $this->stockRepository->save($stock);
    }
}
