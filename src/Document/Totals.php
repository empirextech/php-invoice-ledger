<?php

declare(strict_types=1);

namespace EmpireX\InvoiceLedger\Document;

use EmpireX\InvoiceLedger\Money\Money;

final readonly class Totals
{
    public function __construct(
        public Money $net,
        public Money $vat,
        public Money $gross,
    ) {
    }
}
