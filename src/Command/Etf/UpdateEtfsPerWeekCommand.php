<?php

namespace App\Command\Etf;

use App\Command\CandleTrait;
use App\Entity\Etf;
use App\Repository\EtfRepository;
use DateTime;
use Dzhdmitry\TinkoffInvestApi\Rest\Api\Market;
use Dzhdmitry\TinkoffInvestApi\Rest\ClientFactory;
use Dzhdmitry\TinkoffInvestApi\Rest\Schema\Payload\Candle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateEtfsPerWeekCommand extends Command
{
    use CandleTrait;

    private const INTERVAL = 'week';

    private $tinkoffToken;
    private $etfRepository;

    protected static $defaultName = 'app:etfs:update-per-week';
    protected static $defaultDescription = 'Update etfs per week by tinkoff api';

    public function __construct(string $tinkoffToken, EtfRepository $etfRepository)
    {
        $this->tinkoffToken = $tinkoffToken;
        $this->etfRepository = $etfRepository;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $client = (new ClientFactory())->create($this->tinkoffToken);
        /** @var Market $market */
        $market = $client->market();

        $etfs = $this->etfRepository->findAll();

        /** @var Etf $etf */
        foreach ($etfs as $etf) {
            if ($etf->getId() < 1) {
                continue;
            }

            $candles = $market->getCandles($etf->getFigi(), new DateTime('-4 weeks'), new DateTime(), self::INTERVAL)
                ->getPayload()->getCandles();

            if (empty($candles)) {
                $io->error(sprintf('id %d, name %s', $etf->getId(), $etf->getName()));

                continue;
            }

            $this->updateEtf($etf, $candles);

            $io->note(sprintf('id %d, name %s', $etf->getId(), $etf->getName()));

            sleep(rand(2, 4));
        }

        $io->success('Etfs updated');

        return Command::SUCCESS;
    }

    /**
     * @param Candle[] $candles
     */
    private function updateEtf(Etf $etf, array $candles): void
    {
        $etf
            ->setCurrent($this->getCandlesCurrent($candles))
            ->setWeekOpen($this->getLastCandlesOpen($candles))
            ->setMonthOpen($this->getFirstCandlesOpen($candles))
            ->calculateWeekOpenPercent()
            ->calculateMonthOpenPercent()
        ;

        $this->etfRepository->save($etf);
    }
}
