<?php

declare(strict_types=1);

namespace EmpireX\InvoiceLedger\Document;

final readonly class InvoiceNumber
{
    public function __construct(
        public string $series,
        public int $sequence,
        public int $year,
        public int $width = 6,
    ) {
        if (!preg_match('/^[A-Z]{2,6}$/', $this->series)) {
            throw new \InvalidArgumentException('Series must be 2–6 uppercase letters.');
        }
        if ($this->sequence < 1) {
            throw new \InvalidArgumentException('Sequence must start at 1.');
        }
    }

    public function format(): string
    {
        return sprintf('%s-%d-%0' . $this->width . 'd', $this->series, $this->year, $this->sequence);
    }

    public function next(): self
    {
        return new self($this->series, $this->sequence + 1, $this->year, $this->width);
    }
}
