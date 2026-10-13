<?php

declare(strict_types=1);

namespace EmpireX\InvoiceLedger\Plan;

final readonly class Plan
{
    public function __construct(
        public string $name,
        public ?int $maxCompanies,
        public ?int $maxDocumentsPerMonth,
    ) {
    }

    public static function starter(): self
    {
        return new self('Starter', 1, 20);
    }

    public static function studio(): self
    {
        return new self('Studio', 3, 200);
    }

    public static function firm(): self
    {
        return new self('Firm', null, null);
    }
}
