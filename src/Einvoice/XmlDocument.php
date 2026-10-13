<?php

declare(strict_types=1);

namespace EmpireX\InvoiceLedger\Einvoice;

use EmpireX\InvoiceLedger\Document\Invoice;

/**
 * Company-owned e-invoice XML. Production ANAF/UBL adapters implement EinvoicePort.
 */
final class XmlDocument
{
    public function render(Invoice $invoice): string
    {
        $totals = $invoice->totals();
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('invoice');
        $xml->writeAttribute('xmlns', 'https://empirex.tech/einvoice');
        $xml->writeElement('number', $invoice->number->format());
        $xml->writeElement('issued', $invoice->issuedAt->format('Y-m-d'));
        $xml->writeElement('due', $invoice->dueAt->format('Y-m-d'));
        $xml->writeElement('currency', $invoice->currency);
        $xml->writeElement('seller', $invoice->sellerTaxId);
        $xml->writeElement('buyer', $invoice->buyerTaxId);
        $xml->startElement('lines');
        foreach ($invoice->lines as $line) {
            $xml->startElement('line');
            $xml->writeElement('description', $line->description);
            $xml->writeElement('qty', (string) $line->quantity);
            $xml->writeElement('unit', $line->unit);
            $xml->writeElement('unit_price', $line->unitPrice->toDecimal());
            $xml->writeElement('vat_bps', (string) $line->vatBasisPoints);
            $xml->writeElement('net', $line->net()->toDecimal());
            $xml->writeElement('vat', $line->vat()->toDecimal());
            $xml->endElement();
        }
        $xml->endElement();
        $xml->writeElement('net', $totals->net->toDecimal());
        $xml->writeElement('vat', $totals->vat->toDecimal());
        $xml->writeElement('gross', $totals->gross->toDecimal());
        $xml->endElement();
        $xml->endDocument();

        return $xml->outputMemory();
    }
}
