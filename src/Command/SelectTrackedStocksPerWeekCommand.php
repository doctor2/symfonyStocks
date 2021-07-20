<?php

namespace App\Command;

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

class SelectTrackedStocksPerWeekCommand extends Command
{
    use CandleTrait;

    private const INTERVAL = 'week';

    private $tinkoffToken;
    private $stockRepository;
    protected static $defaultName = 'app:stocks:select-tracked-per-week';
    protected static $defaultDescription = 'Select tracked stocks per week from tinkoff api';

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

        $stockFigis = $this->stockRepository->findAllFigis();

        foreach ($stockFigis as $figi) {
            /** @var Stock $stock */
            $stock = $this->stockRepository->findOneByFigi($figi);

            if ($stock->getId() < 1) {
                continue;
            }

            $candles = $market->getCandles($figi, new DateTime('-1 week'), new DateTime(), self::INTERVAL)
                ->getPayload()->getCandles();

            if (empty($candles)) {
                $io->error(sprintf('id %d, name %s', $stock->getId(), $stock->getName()));

                continue;
            }

            $this->updateStock($stock, $candles);

            $io->note(sprintf('id %d, name %s', $stock->getId(), $stock->getName()));

            sleep(rand(3, 7));
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
            ->setWeekOpen($this->getCandlesOpen($candles));

        $maximum = $this->getCandlesMaximum($candles);

        if ($maximum > $stock->getSixMonthsMaximum()) {
            $stock->setSixMonthsMaximum($maximum);
        }

        $minimum = $this->getCandlesMinimum($candles);

        if ($minimum < $stock->getSixMonthsMaximum()) {
            $stock->setSixMonthsMinimum($minimum);
        }

        $stock
            ->calculateWeekOpenPercent()
            ->calculateSixMonthsMaximumPercent();

        $this->stockRepository->save($stock);
    }
}
