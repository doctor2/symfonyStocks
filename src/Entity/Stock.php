<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=StockRepository::class)
 */
class Stock
{
    use TimestampableEntity;

    public const TRACKED_PERCENT = 50;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $figi;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ticker;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $isin = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $currency;

    /**
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $isTracked = false;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $sixMonthsMaximum;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $sixMonthsMinimum;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $sixMonthsMaximumPercent;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $current;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $currentWeekOpen;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $currentWeekOpenPercent;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $previousWeekOpen;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $previousWeekOpenPercent;

    /**
     * @ORM\Column(type="text", options={"default" : ""})
     */
    private $usefulLinks = '';

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $sixMonthsMinimumPercent;

    /**
     * @ORM\Column(type="text")
     */
    private $comment = '';

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $exchange;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sector;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $industry;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $percentInsiders;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $percentInstitutions;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $twoWeekOpenPercent;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isGrowingDynamics;

    public function __construct(string $figi, string $ticker, string $currency, string $name)
    {
        $this->figi = $figi;
        $this->ticker = $ticker;
        $this->currency = $currency;
        $this->name = $name;

        $this->fillUsefulLinks();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFigi(): string
    {
        return $this->figi;
    }

    public function setFigi(string $figi): self
    {
        $this->figi = $figi;

        return $this;
    }

    public function getTicker(): string
    {
        return $this->ticker;
    }

    public function setTicker(string $ticker): self
    {
        $this->ticker = $ticker;

        return $this;
    }

    public function getIsin(): ?string
    {
        return $this->isin;
    }

    public function setIsin(?string $isin): self
    {
        $this->isin = $isin;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getIsTracked(): ?bool
    {
        return $this->isTracked;
    }

    public function setIsTracked(bool $isTracked): self
    {
        $this->isTracked = $isTracked;

        return $this;
    }

    public function getSixMonthsMaximum(): ?float
    {
        return $this->sixMonthsMaximum;
    }

    public function setSixMonthsMaximum(?float $sixMonthsMaximum): self
    {
        $this->sixMonthsMaximum = $sixMonthsMaximum;

        return $this;
    }

    public function getSixMonthsMinimum(): ?float
    {
        return $this->sixMonthsMinimum;
    }

    public function setSixMonthsMinimum(?float $sixMonthsMinimum): self
    {
        $this->sixMonthsMinimum = $sixMonthsMinimum;

        return $this;
    }

    public function getSixMonthsMaximumPercent(): ?float
    {
        return $this->sixMonthsMaximumPercent;
    }

    public function calculateSixMonthsMaximumPercent(): self
    {
        if (empty($this->getSixMonthsMaximum()) || empty($this->getCurrent())) {
            return $this;
        }

        $this->sixMonthsMaximumPercent = round(($this->getSixMonthsMaximum() - $this->getCurrent()) / $this->getSixMonthsMaximum(), 4);

        return $this;
    }

    public function getSixMonthsMinimumPercent(): ?float
    {
        return $this->sixMonthsMinimumPercent;
    }

    public function calculateSixMonthsMinimumPercent(): self
    {
        if (empty($this->getSixMonthsMinimum()) || empty($this->getCurrent())) {
            return $this;
        }

        $this->sixMonthsMinimumPercent = round(($this->getCurrent() - $this->getSixMonthsMinimum()) / $this->getSixMonthsMinimum(), 4);

        return $this;
    }

    public function getCurrent(): ?float
    {
        return $this->current;
    }

    public function setCurrent(?float $current): self
    {
        $this->current = $current;

        return $this;
    }

    public function getCurrentWeekOpen(): ?float
    {
        return $this->currentWeekOpen;
    }

    public function setCurrentWeekOpen(?float $weekOpen): self
    {
        $this->currentWeekOpen = $weekOpen;

        return $this;
    }

    public function calculateCurrentWeekOpenPercent(): self
    {
        if (empty($this->getCurrentWeekOpen()) || empty($this->getCurrent())) {
            return $this;
        }

        $this->currentWeekOpenPercent = round(($this->getCurrent() - $this->getCurrentWeekOpen()) / $this->getCurrentWeekOpen(), 4);

        $this
            ->calculateTwoWeekOpenPercent()
            ->calculateIsGrowingDynamics()
        ;

        return $this;
    }

    public function getPreviousWeekOpen(): ?float
    {
        return $this->previousWeekOpen;
    }

    public function setPreviousWeekOpen(?float $previousWeekOpen): self
    {
        $this->previousWeekOpen = $previousWeekOpen;

        return $this;
    }

    public function getPreviousWeekOpenPercent(): ?float
    {
        return $this->previousWeekOpenPercent;
    }

    public function calculatePreviousWeekOpenPercent(): self
    {
        if (empty($this->getPreviousWeekOpen()) || empty($this->getCurrentWeekOpen())) {
            return $this;
        }

        $this->previousWeekOpenPercent = round(($this->getCurrentWeekOpen() - $this->getPreviousWeekOpen()) / $this->getPreviousWeekOpen(), 4);

        $this
            ->calculateTwoWeekOpenPercent()
            ->calculateIsGrowingDynamics()
        ;

        return $this;
    }

    public function getCurrentWeekOpenPercent(): ?float
    {
        return $this->currentWeekOpenPercent;
    }

    public function fillUsefulLinks(): void
    {
        $links = '<a href="https://ru.investing.com/search/?q=TICKER" target="_blank">investing</a>,
        <a href="https://www.tinkoff.ru/invest/catalog/?query=TICKER" target="_blank">tinkoff</a>,';

        if ($this->getExchange()) {
            $links .= sprintf('
            <a href="https://www.tradingview.com/symbols/%s-%s/" target="_blank">tradingview</a>', (string) $this->getExchange(), $this->getTicker());
        } else {
            $links .= '
            <a href="https://www.tradingview.com/chart/?symbol=TICKER" target="_blank">tradingview</a>';
        }

        $this->usefulLinks = str_replace('TICKER', $this->getTicker(), $links);
    }

    public function setUsefulLinks(string $usefulLinks): self
    {
        $this->usefulLinks = $usefulLinks;

        return $this;
    }

    public function getUsefulLinks(): ?string
    {
        return $this->usefulLinks;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getExchange(): ?string
    {
        return $this->exchange;
    }

    public function setExchange(?string $exchange): self
    {
        $this->exchange = $exchange;

        return $this;
    }

    public function getSector(): ?string
    {
        return $this->sector;
    }

    public function setSector(?string $sector): self
    {
        $this->sector = $sector;

        return $this;
    }

    public function getIndustry(): ?string
    {
        return $this->industry;
    }

    public function setIndustry(?string $industry): self
    {
        $this->industry = $industry;

        return $this;
    }

    public function getPercentInsiders(): ?float
    {
        return $this->percentInsiders;
    }

    public function setPercentInsiders(?float $percentInsiders): self
    {
        $this->percentInsiders = $percentInsiders;

        return $this;
    }

    public function getPercentInstitutions(): ?float
    {
        return $this->percentInstitutions;
    }

    public function setPercentInstitutions(?float $percentInstitutions): self
    {
        $this->percentInstitutions = $percentInstitutions;

        return $this;
    }

    public function getTwoWeekOpenPercent(): ?float
    {
        return $this->twoWeekOpenPercent;
    }

    public function calculateTwoWeekOpenPercent(): self
    {
        if (empty($this->getPreviousWeekOpen()) || empty($this->getCurrent())) {
            return $this;
        }

        $this->twoWeekOpenPercent = round(($this->getCurrent() - $this->getPreviousWeekOpen()) / $this->getPreviousWeekOpen(), 4);

        return $this;
    }

    public function getIsGrowingDynamics(): ?bool
    {
        return $this->isGrowingDynamics;
    }

    public function calculateIsGrowingDynamics(): self
    {
        if (empty($this->getPreviousWeekOpenPercent()) || empty($this->getCurrentWeekOpenPercent())) {
            return $this;
        }

        $this->isGrowingDynamics = $this->getCurrentWeekOpenPercent() > $this->getPreviousWeekOpenPercent();

        return $this;
    }
}
