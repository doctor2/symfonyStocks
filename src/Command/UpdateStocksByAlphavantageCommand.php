<?php

namespace App\Command;

use App\Entity\Stock;
use App\Module\Alphavantage\Response\StockOverview;
use App\Repository\StockRepository;
use JMS\Serializer\ArrayTransformerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UpdateStocksByAlphavantageCommand extends Command
{
    private const ALPHAVANTAGE_OVERVIEW_URL = 'https://www.alphavantage.co/query?function=OVERVIEW&symbol=%s&apikey=%s';

    private $alphavantageToken;
    private $stockRepository;
    private $arrayTransformer;
    private $client;

    protected static $defaultName = 'app:stocks:update-by-alphavantage';
    protected static $defaultDescription = 'Update stocks by alphavantage api';

    public function __construct(string $alphavantageToken, StockRepository $stockRepository, ArrayTransformerInterface $arrayTransformer, HttpClientInterface $client)
    {
        $this->alphavantageToken = $alphavantageToken;
        $this->stockRepository = $stockRepository;
        $this->arrayTransformer = $arrayTransformer;
        $this->client = $client;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $stocks = $this->stockRepository->findAll();

        /** @var Stock $stock */
        foreach ($stocks as $stock) {
            if ($stock->getId() < 1 || $stock->getExchange() || preg_match('/[а-я]/i', $stock->getName())) {
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
        $stockOverview = $this->requestStockOverviewByTicket($stock->getTicker());

        if ($stockOverview->isEmpty()) {
            sleep(15);

            return;
        } else {
            sleep(30);
        }

        $stock
            ->setExchange($stockOverview->getExchange())
            ->setCountry($stockOverview->getCountry())
            ->setSector($stockOverview->getSector())
            ->setIndustry($stockOverview->getIndustry())
            ->setPercentInsiders($stockOverview->getPercentInsiders())
            ->setPercentInstitutions($stockOverview->getPercentInstitutions())
            ->fillUsefulLinks()
        ;

        $this->stockRepository->save($stock);
    }

    public function requestStockOverviewByTicket(string $ticket): StockOverview
    {
        return $this->arrayTransformer->fromArray(
            $this->client->request(Request::METHOD_GET, sprintf(self::ALPHAVANTAGE_OVERVIEW_URL, $ticket, $this->alphavantageToken))->toArray(),
            StockOverview::class
        );
    }
}
