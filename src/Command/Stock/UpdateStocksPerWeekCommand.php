<?php

namespace App\Command\Stock;

use App\Command\CandleTrait;
use App\Entity\Stock;
use App\Repository\StockRepository;
use DateTime;
use Dzhdmitry\TinkoffInvestApi\Rest\Api\Market;
use Dzhdmitry\TinkoffInvestApi\Rest\ClientFactory;
use Dzhdmitry\TinkoffInvestApi\Rest\Schema\Payload\Candle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateStocksPerWeekCommand extends Command
{
    use CandleTrait;

    private const INTERVAL = 'week';

    private $tinkoffToken;
    private $stockRepository;

    protected static $defaultName = 'app:stocks:update-per-week';
    protected static $defaultDescription = 'Update stocks per week by tinkoff api';

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
        /** @var Market $market */
        $market = $client->market();

        $stocks = $this->stockRepository->findAll();

        /** @var Stock $stock */
        foreach ($stocks as $stock) {
            if ($stock->getId() < 1) {
                continue;
            }

            $candles = $market->getCandles($stock->getFigi(), new DateTime('-2 weeks'), new DateTime(), self::INTERVAL)
                ->getPayload()->getCandles();

            if (empty($candles)) {
                $io->error(sprintf('id %d, name %s', $stock->getId(), $stock->getName()));

                continue;
            }

            $this->updateStock($stock, $candles);

            $io->note(sprintf('id %d, name %s', $stock->getId(), $stock->getName()));

            sleep(rand(2, 4));
        }

        $io->success('Stocks updated');

        return Command::SUCCESS;
    }

    /**
     * @param Candle[] $candles
     */
    private function updateStock(Stock $stock, array $candles): void
    {
        $stock
            ->setCurrent($this->getCandlesCurrent($candles))
            ->setCurrentWeekOpen($this->getLastCandlesOpen($candles))
            ->setPreviousWeekOpen($this->getFirstCandlesOpen($candles))
            ->setSixMonthsMaximum($this->getCandlesMaximum($candles, $stock->getSixMonthsMaximum()))
            ->setSixMonthsMinimum($this->getCandlesMinimum($candles, $stock->getSixMonthsMinimum()))
            ->calculateCurrentWeekOpenPercent()
            ->calculatePreviousWeekOpenPercent()
            ->calculateSixMonthsMinimumPercent()
            ->calculateSixMonthsMaximumPercent()
        ;

        $this->stockRepository->save($stock);
    }
}
