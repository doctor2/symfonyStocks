<?php

namespace App\Entity\Listener;

use App\Entity\Stock;
use Doctrine\Persistence\Event\PreUpdateEventArgs;

class StockListener
{
    public function preUpdate(Stock $stock, PreUpdateEventArgs $event): void
    {
        if ($event->hasChangedField('current')) {
            $this->calculateSixMonthsPercent($stock);
            $this->calculateWeekOpenPercent($stock);

            return;
        } 

    }

    private function calculateSixMonthsPercent(Stock $stock): void
    {
        $stock
            ->calculateSixMonthsMinimumPercent()
            ->calculateSixMonthsMaximumPercent()
        ;
    }

    private function calculateWeekOpenPercent(Stock $stock): void
    {
        $stock
            ->calculateCurrentWeekOpenPercent()
            ->calculatePreviousWeekOpenPercent()
        ;
    }
}
