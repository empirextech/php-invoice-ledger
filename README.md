# PHP Invoice Ledger

Invoice arithmetic for Romanian companies that cannot afford float drift.

SC EMPIRE GLOBAL SOLUTIONS SRL builds invoicing platforms where a 1-ban error becomes an ANAF problem. This library keeps amounts in integer minor units, applies 19% VAT as basis points with half-up rounding, issues `FAC-2026-000041` numbers, and renders a company XML document that a later ANAF/UBL adapter can wrap.

This is original EMPIREX Tech engineering, extracted from the domain of our invoicing product — not a dump of that product, and not a client company file.

## What it solves

| Concern | Implementation |
| --- | --- |
| Money | Integer minor units (`100.00 RON` → `10000`) |
| VAT | Basis points (`1900` = 19%), half-up |
| Numbering | Series + year + padded sequence |
| SaaS limits | Companies / documents-per-month |
| E-invoice | XML port, not a live SPV connection |

## Usage

```php
use EmpireX\InvoiceLedger\Document\Invoice;
use EmpireX\InvoiceLedger\Document\InvoiceNumber;
use EmpireX\InvoiceLedger\Document\Line;
use EmpireX\InvoiceLedger\Money\Money;

$invoice = new Invoice(
    number: new InvoiceNumber('FAC', 41, 2026),
    sellerTaxId: 'RO12345678',
    buyerTaxId: 'RO87654321',
    issuedAt: new DateTimeImmutable('2026-10-13'),
    dueAt: new DateTimeImmutable('2026-11-12'),
    lines: [
        new Line('Item 1', 10, Money::fromDecimal('100.00', 'RON'), 1900),
        new Line('Item 2', 5, Money::fromDecimal('200.00', 'RON'), 1900),
    ],
    currency: 'RON',
);

$invoice->totals()->gross->toDecimal(); // 2380.00
```

## Tests

```bash
composer install
composer test
```

## Security

Tax identifiers are data, not secrets. Do not commit live CUI lists, Stripe keys, or SPV certificates into a public fork.

## License

MIT — EMPIREX Tech / SC EMPIRE GLOBAL SOLUTIONS SRL
