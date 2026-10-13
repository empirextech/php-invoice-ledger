<?php

declare(strict_types=1);

namespace EmpireX\InvoiceLedger\Tests;

use EmpireX\InvoiceLedger\Document\Invoice;
use EmpireX\InvoiceLedger\Document\InvoiceNumber;
use EmpireX\InvoiceLedger\Document\Line;
use EmpireX\InvoiceLedger\Einvoice\XmlDocument;
use EmpireX\InvoiceLedger\Money\Money;
use EmpireX\InvoiceLedger\Plan\Plan;
use EmpireX\InvoiceLedger\Plan\PlanLimiter;
use PHPUnit\Framework\TestCase;

final class InvoiceLedgerTest extends TestCase
{
    public function testVatTotalsMatchTheClassicTwoLineCase(): void
    {
        $invoice = $this->invoice([
            new Line('Item 1', 10, Money::fromDecimal('100.00', 'RON'), 1900),
            new Line('Item 2', 5, Money::fromDecimal('200.00', 'RON'), 1900),
        ]);
        $totals = $invoice->totals();

        self::assertSame('2000.00', $totals->net->toDecimal());
        self::assertSame('380.00', $totals->vat->toDecimal());
        self::assertSame('2380.00', $totals->gross->toDecimal());
        self::assertSame('FAC-2026-000041', $invoice->number->format());
    }

    public function testStarterPlanBlocksASecondCompany(): void
    {
        $limiter = new PlanLimiter();
        $ok = $limiter->canAddCompany(Plan::starter(), 0);
        $blocked = $limiter->canAddCompany(Plan::starter(), 1);

        self::assertTrue($ok->allowed);
        self::assertFalse($blocked->allowed);
        self::assertSame(1, $blocked->limit);
    }

    public function testEinvoiceXmlContainsTaxIdsAndGross(): void
    {
        $xml = (new XmlDocument())->render($this->invoice([
            new Line('Consulting', 1, Money::fromDecimal('1000.00', 'RON'), 1900),
        ]));

        self::assertStringContainsString('<seller>RO12345678</seller>', $xml);
        self::assertStringContainsString('<gross>1190.00</gross>', $xml);
    }

    /**
     * @param list<Line> $lines
     */
    private function invoice(array $lines): Invoice
    {
        return new Invoice(
            number: new InvoiceNumber('FAC', 41, 2026),
            sellerTaxId: 'RO12345678',
            buyerTaxId: 'RO87654321',
            issuedAt: new \DateTimeImmutable('2026-10-13'),
            dueAt: new \DateTimeImmutable('2026-11-12'),
            lines: $lines,
            currency: 'RON',
        );
    }
}
