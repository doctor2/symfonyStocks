<?php

namespace App\Command\Etf;

use App\Entity\Etf;
use App\Repository\EtfRepository;
use Dzhdmitry\TinkoffInvestApi\Rest\Api\Market;
use Dzhdmitry\TinkoffInvestApi\Rest\ClientFactory;
use Dzhdmitry\TinkoffInvestApi\Rest\Schema\Payload\MarketInstrument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateEtfsCommand extends Command
{
    private $tinkoffToken;
    private $etfRepository;

    protected static $defaultName = 'app:etfs:create';
    protected static $defaultDescription = 'Create etfs from tinkoff api';

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
        $etfResponse = $market->getEtfs();

        $etfFigis = $this->etfRepository->findAllFigis();

        foreach ($etfResponse->getPayload()->getInstruments() as $instrument) {
            if (in_array($instrument->getFigi(), $etfFigis, true)) {
                continue;
            }

            $this->createEtf($instrument);

            $io->note($instrument->getName());
        }

        $io->success('Etfs created');

        return Command::SUCCESS;
    }

    public function createEtf(MarketInstrument $instrument): void
    {
        $etf = new Etf(
            $instrument->getFigi(),
            $instrument->getTicker(),
            $instrument->getCurrency(),
            $instrument->getName(),
        );

        $etf->setIsin($instrument->getIsin());

        $this->etfRepository->save($etf);
    }
}
