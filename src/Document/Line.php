<?php

declare(strict_types=1);

namespace EmpireX\InvoiceLedger\Document;

use EmpireX\InvoiceLedger\Money\Money;

final readonly class Line
{
    public function __construct(
        public string $description,
        public int $quantity,
        public Money $unitPrice,
        public int $vatBasisPoints,
        public string $unit = 'buc',
    ) {
        if ($this->description === '' || $this->quantity < 1) {
            throw new \InvalidArgumentException('Line requires a description and quantity >= 1.');
        }
        if ($this->vatBasisPoints < 0 || $this->vatBasisPoints > 10_000) {
            throw new \InvalidArgumentException('VAT basis points out of range.');
        }
    }

    public function net(): Money
    {
        return $this->unitPrice->multiply($this->quantity);
    }

    public function vat(): Money
    {
        return $this->net()->vat($this->vatBasisPoints);
    }

    public function gross(): Money
    {
        return $this->net()->add($this->vat());
    }
}
