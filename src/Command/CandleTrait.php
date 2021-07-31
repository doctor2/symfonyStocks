<?php

namespace App\Command;

use Dzhdmitry\TinkoffInvestApi\Rest\Schema\Payload\Candle;

trait CandleTrait
{
    /**
     * @param Candle[] $candles
     */
    private function getCandlesMaximum(array $candles, ?float $initial = null): float
    {
        return (float) array_reduce($candles, function ($carry, Candle $candle) {
            if ($candle->getH() > $carry) {
                $carry = $candle->getH();
            }

            return $carry;
        }, $initial ?? PHP_FLOAT_MIN);
    }

    /**
     * @param Candle[] $candles
     */
    private function getCandlesMinimum(array $candles, ?float $initial = null): float
    {
        return (float) array_reduce($candles, function ($carry, Candle $candle) {
            if ($candle->getL() < $carry) {
                $carry = $candle->getL();
            }

            return $carry;
        }, $initial ?? PHP_FLOAT_MAX);
    }

    /**
     * @param Candle[] $candles
     */
    private function getCandlesCurrent(array $candles): ?float
    {
        $candle = end($candles);

        return $candle ? $candle->getC() : null;
    }

    /**
     * @param Candle[] $candles
     */
    private function getFirstCandlesOpen(array $candles): ?float
    {
        $candle = reset($candles);

        return $candle ? $candle->getO() : null;
    }

    /**
     * @param Candle[] $candles
     */
    private function getLastCandlesOpen(array $candles): ?float
    {
        $candle = end($candles);

        return $candle ? $candle->getO() : null;
    }
}
