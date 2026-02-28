<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService
{
    /**
     * Create a new invoice.
     *
     * @param array $data
     * @return Invoice
     */
    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $tenant = Tenant::findOrFail($data['tenant_id']);

            // default: rent included
            $rent = $tenant->rent_amount;

            // only when closing, check advance
            if (($data['closer'] ?? 0) == 1 && $tenant->is_advanced == 1) {
                $rent = 0;
            }

            $total_amount =
                $data['electricity_charge']
                + ($data['water_charge'] ?? 0)
                + $rent;

            $invoice = Invoice::create([
                'tenant_id' => $tenant->id,
                'month' => $data['month'],
                'electricity_units' => $data['electricity_units'],
                'electricity_charge' => $data['electricity_charge'],
                'water_charge' => $data['water_charge'] ?? 0,
                'total_amount' => $total_amount,
                'received_amount' => $data['received_amount'] ?? 0,
                'is_excluded' => $data['is_excluded'] ?? false,
            ]);

            if (($data['received_amount'] ?? 0) > 0) {
                \App\Models\Transaction::create([
                    'tenant_id' => $invoice->tenant_id,
                    'invoice_id' => $invoice->id,
                    'amount' => $data['received_amount'],
                    'payment_mode' => $data['payment_mode'] ?? 'Cash',
                    'payment_date' => $data['payment_date'] ?? now()->format('Y-m-d'),
                ]);
            }

            if (($data['closer'] ?? 0) == 1) {
                $tenant->update(['status' => 'close']);
            }

            return $invoice;
        });
    }

    /**
     * Update an existing invoice.
     *
     * @param Invoice $invoice
     * @param array $data
     * @return bool
     */
    public function updateInvoice(Invoice $invoice, array $data): bool
    {
        $oldReceived = $invoice->received_amount;
        $updated = $invoice->update([
            'tenant_id' => $data['tenant_id'],
            'month' => $data['month'],
            'electricity_units' => $data['electricity_units'],
            'electricity_charge' => $data['electricity_charge'],
            'water_charge' => $data['water_charge'] ?? 0,
            'total_amount' => $data['total_amount'],
            'received_amount' => $data['received_amount'],
            'is_excluded' => $data['is_excluded'] ?? false,
        ]);

        $amountAdded = $data['received_amount'] - $oldReceived;
        if ($amountAdded > 0) {
            \App\Models\Transaction::create([
                'tenant_id' => $invoice->tenant_id,
                'invoice_id' => $invoice->id,
                'amount' => $amountAdded,
                'payment_mode' => $data['payment_mode'] ?? 'Cash',
                'payment_date' => $data['payment_date'] ?? now()->format('Y-m-d'),
            ]);
        }

        return $updated;
    }

    /**
     * Get last units for a tenant before a given month.
     *
     * @param int $tenantId
     * @param string $month Y-m
     * @return float
     */
    public function getLastUnits(int $tenantId, string $month): float
    {
        $lastInvoice = Invoice::where('tenant_id', $tenantId)
            ->where('month', '<', $month)
            ->orderBy('month', 'desc')
            ->first();

        return $lastInvoice?->electricity_units ?? 0;
    }

    /**
     * Prepare invoice object with additional data for PDF.
     *
     * @param Invoice $invoice
     * @return Invoice
     */
    public function preparePdfData(Invoice $invoice): Invoice
    {
        // Current reading from the invoice
        $currentUnit = (int) $invoice->electricity_units;
        $invoice->currentUnit = $currentUnit;

        // Calculate usage based on stored charge
        $electricityRate = config('constants.ELECTRIC_RATE', 10); // ₹10 default
        $electricityCharge = $invoice->electricity_charge;

        // Back-calculate unit difference from the charge
        // Avoid division by zero
        $unitDiff = $electricityRate > 0 ? round($electricityCharge / $electricityRate) : 0;

        $previousUnit = $currentUnit - $unitDiff;

        // Append custom display-only fields
        $invoice->electricity_display = "{$currentUnit} - {$previousUnit} = {$unitDiff}";
        $invoice->electricity_used_units = $unitDiff;
        $invoice->electricity_charge = $electricityCharge;
        $invoice->electricity_rate = $electricityRate;

        return $invoice;
    }

    /**
     * Generate PDF filename.
     */
    public function getPdfFilename(Invoice $invoice): string
    {
        $nameSlug = strtolower(str_replace(' ', '_', $invoice->tenant->name));
        return "Invoice_{$nameSlug}_{$invoice->month}.pdf";
    }
}
