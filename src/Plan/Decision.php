<?php

declare(strict_types=1);

namespace EmpireX\InvoiceLedger\Plan;

final readonly class Decision
{
    public function __construct(
        public bool $allowed,
        public ?string $reason = null,
        public int $used = 0,
        public ?int $limit = null,
    ) {
    }
}
