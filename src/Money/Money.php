<?php

declare(strict_types=1);

namespace EmpireX\InvoiceLedger\Money;

final readonly class Money
{
    public function __construct(
        public int $amount,
        public string $currency,
    ) {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Money cannot be negative.');
        }
        if (strlen($this->currency) !== 3) {
            throw new \InvalidArgumentException('Currency must be ISO 4217.');
        }
    }

    public static function fromDecimal(string $decimal, string $currency): self
    {
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $decimal)) {
            throw new \InvalidArgumentException('Invalid decimal.');
        }
        $parts = explode('.', $decimal, 2);
        $minor = isset($parts[1]) ? str_pad($parts[1], 2, '0') : '00';

        return new self(((int) $parts[0] * 100) + (int) $minor, strtoupper($currency));
    }

    public function add(self $other): self
    {
        $this->assertSame($other);

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function multiply(int $quantity): self
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity cannot be negative.');
        }

        return new self($this->amount * $quantity, $this->currency);
    }

    /**
     * VAT in basis points (1900 = 19%). Half-up to the nearest minor unit.
     */
    public function vat(int $basisPoints): self
    {
        if ($basisPoints < 0) {
            throw new \InvalidArgumentException('VAT cannot be negative.');
        }

        $raw = $this->amount * $basisPoints;
        $vat = intdiv($raw, 10_000);
        if (($raw % 10_000) >= 5_000) {
            $vat++;
        }

        return new self($vat, $this->currency);
    }

    public function toDecimal(): string
    {
        return sprintf('%d.%02d', intdiv($this->amount, 100), $this->amount % 100);
    }

    private function assertSame(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException('Currency mismatch.');
        }
    }
}
