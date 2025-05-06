<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Outlet;
use Carbon\Carbon;

class InvoiceService
{
    /**
     * Generate a unique invoice number
     * Format: INV-YYYYMMDD-OUTLETID-SEQUENCE (e.g., INV-20240505-001-0001)
     *
     * @param int $outletId
     * @return string
     */
    public function generate(int $outletId): string
    {
        $date = Carbon::now()->format('Ymd');
        $outletCode = str_pad($outletId, 3, '0', STR_PAD_LEFT);

        // Get the latest invoice for this outlet today
        $latestInvoice = Transaction::where('invoice_number', 'like', "INV-{$date}-{$outletCode}-%")
            ->latest()
            ->first();

        // Determine the sequence number
        $sequence = $latestInvoice
            ? (int) substr($latestInvoice->invoice_number, -4) + 1
            : 1;

        // Pad the sequence with leading zeros
        $paddedSequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        return "INV-{$date}-{$outletCode}-{$paddedSequence}";
    }

    /**
     * Validate if an invoice number follows the correct format
     *
     * @param string $invoiceNumber
     * @return bool
     */
    public function validateFormat(string $invoiceNumber): bool
    {
        return preg_match('/^INV-\d{8}-\d{3}-\d{4}$/', $invoiceNumber) === 1;
    }
}
