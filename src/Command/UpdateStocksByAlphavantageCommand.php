<?php

namespace App\Command;

use App\Entity\Stock;
use App\Repository\StockRepository;
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
    private $client;

    protected static $defaultName = 'app:stocks:update-by-alphavantage';
    protected static $defaultDescription = 'Update stocks by alphavantage api';

    public function __construct(string $alphavantageToken, StockRepository $stockRepository, HttpClientInterface $client)
    {
        $this->alphavantageToken = $alphavantageToken;
        $this->stockRepository = $stockRepository;
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
        $additionalStockData = $this->requestAdditionalStockDataByTicket($stock->getTicker());

        if (empty($additionalStockData) || !isset($additionalStockData['Symbol'])) {
            sleep(15);

            return;
        } else {
            sleep(rand(1, 5));
        }

        $stock
            ->setExchange($additionalStockData['Exchange'])
            ->setCountry($additionalStockData['Country'])
            ->setSector($additionalStockData['Sector'])
            ->setIndustry($additionalStockData['Industry'])
            ->setPercentInsiders((float) $additionalStockData['PercentInsiders'])
            ->setPercentInstitutions((float) $additionalStockData['PercentInstitutions'])
            ->fillUsefulLinks()
        ;

        $this->stockRepository->save($stock);
    }

    /**
     * @return string[]
     */
    public function requestAdditionalStockDataByTicket(string $ticket): array
    {
        return $this->client->request(Request::METHOD_GET, sprintf(self::ALPHAVANTAGE_OVERVIEW_URL, $ticket, $this->alphavantageToken))->toArray();
    }
}
