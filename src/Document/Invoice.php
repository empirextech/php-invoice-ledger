<?php

declare(strict_types=1);

namespace EmpireX\InvoiceLedger\Document;

final readonly class Invoice
{
    /**
     * @param list<Line> $lines
     */
    public function __construct(
        public InvoiceNumber $number,
        public string $sellerTaxId,
        public string $buyerTaxId,
        public \DateTimeImmutable $issuedAt,
        public \DateTimeImmutable $dueAt,
        public array $lines,
        public string $currency,
        public string $status = 'draft',
    ) {
        if ($this->lines === []) {
            throw new \InvalidArgumentException('Invoice needs at least one line.');
        }
        if ($this->dueAt < $this->issuedAt) {
            throw new \InvalidArgumentException('Due date cannot precede issue date.');
        }
    }

    public function totals(): Totals
    {
        $net = $this->lines[0]->net();
        $vat = $this->lines[0]->vat();
        foreach (array_slice($this->lines, 1) as $line) {
            $net = $net->add($line->net());
            $vat = $vat->add($line->vat());
        }

        return new Totals($net, $vat, $net->add($vat));
    }
}
