<?php

namespace App\Entity;

use App\Repository\EtfRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=EtfRepository::class)
 */
class Etf
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
    private $current;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $weekOpen;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $weekOpenPercent;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $monthOpen;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $monthOpenPercent;

    /**
     * @ORM\Column(type="text", options={"default" : ""})
     */
    private $usefulLinks = '';

    /**
     * @ORM\Column(type="text")
     */
    private $comment = '';

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

    public function getWeekOpen(): ?float
    {
        return $this->weekOpen;
    }

    public function setWeekOpen(?float $weekOpen): self
    {
        $this->weekOpen = $weekOpen;

        return $this;
    }

    public function getWeekOpenPercent(): ?float
    {
        return $this->weekOpenPercent;
    }

    public function calculateWeekOpenPercent(): self
    {
        if (empty($this->getWeekOpen()) || empty($this->getCurrent())) {
            return $this;
        }

        $this->weekOpenPercent = round(($this->getCurrent() - $this->getWeekOpen()) / $this->getWeekOpen(), 4);

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

    public function getMonthOpen(): ?float
    {
        return $this->monthOpen;
    }

    public function setMonthOpen(?float $monthOpen): self
    {
        $this->monthOpen = $monthOpen;

        return $this;
    }

    public function calculateMonthOpenPercent(): self
    {
        if (empty($this->getMonthOpen()) || empty($this->getCurrent())) {
            return $this;
        }

        $this->monthOpenPercent = round(($this->getCurrent() - $this->getMonthOpen()) / $this->getMonthOpen(), 4);

        return $this;
    }

    public function getMonthOpenPercent(): ?float
    {
        return $this->monthOpenPercent;
    }

    public function fillUsefulLinks(): void
    {
        $links = '<a href="https://ru.investing.com/search/?q=TICKER" target="_blank">investing</a>,
        <a href="https://www.tinkoff.ru/invest/etfs/TICKER/" target="_blank">tinkoff</a>,
        <a href="https://www.tradingview.com/symbols/MOEX-TICKER/" target="_blank">tradingview</a>';

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
}
