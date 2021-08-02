<?php

namespace App\Module\Alphavantage\Response;

use JMS\Serializer\Annotation as Serializer;

class StockOverview
{
    /**
     * @Serializer\SerializedName("Exchange")
     * @Serializer\Type("string")
     */
    private $exchange;

    /**
     * @Serializer\SerializedName("Country")
     * @Serializer\Type("string")
     */
    private $country;

    /**
     * @Serializer\SerializedName("Sector")
     * @Serializer\Type("string")
     */
    private $sector;

    /**
     * @Serializer\SerializedName("Industry")
     * @Serializer\Type("string")
     */
    private $industry;

    /**
     * @Serializer\SerializedName("PercentInsiders")
     * @Serializer\Type("float")
     */
    private $percentInsiders;

    /**
     * @Serializer\SerializedName("PercentInstitutions")
     * @Serializer\Type("float")
     */
    private $percentInstitutions;

    public function getExchange(): string
    {
        return $this->exchange;
    }

    public function setExchange(string $exchange): self
    {
        $this->exchange = $exchange;

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getSector(): string
    {
        return $this->sector;
    }

    public function setSector(string $sector): self
    {
        $this->sector = $sector;

        return $this;
    }

    public function getIndustry(): string
    {
        return $this->industry;
    }

    public function setIndustry(string $industry): self
    {
        $this->industry = $industry;

        return $this;
    }

    public function getPercentInsiders(): float
    {
        return $this->percentInsiders;
    }

    public function setPercentInsiders(float $percentInsiders): self
    {
        $this->percentInsiders = $percentInsiders;

        return $this;
    }

    public function getPercentInstitutions(): float
    {
        return $this->percentInstitutions;
    }

    public function setPercentInstitutions(float $percentInstitutions): self
    {
        $this->percentInstitutions = $percentInstitutions;

        return $this;
    }

    public function isEmpty(): bool
    {
        return empty($this->exchange);
    }
}
