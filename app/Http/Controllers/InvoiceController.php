<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Show list of all invoices.
     */
    public function index()
    {
        $invoices = Invoice::with('tenant')
            ->orderByDesc('month')
            ->get();

        $invoices->map(function ($invoice, $index) use ($invoices) {
            // Find previous invoice for the same tenant
            $previous = $invoices
                ->where('tenant_id', $invoice->tenant_id)
                ->where('month', '<', $invoice->month)
                ->sortByDesc('month')
                ->first();

            $prev_units = $previous?->electricity_units ?? 0;

            $invoice->sum_electricity_units = max($invoice->electricity_units - $prev_units, 0);
            return $invoice;
        });

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show form to create a new invoice.
     */
    public function create()
    {
        $tenants = Tenant::where('status', 'active')->get();
        return view('invoices.create', compact('tenants'));
    }

    /**
     * Store a new invoice.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'month' => 'required|date_format:Y-m',
            'electricity_units' => 'required|numeric',
            'water_charge' => 'required|numeric',
            'status' => 'required|in:paid,unpaid',
        ]);

        // Get tenant with rent
        $tenant = Tenant::findOrFail($validated['tenant_id']);

        // Get last month's invoice for this tenant
        $lastInvoice = Invoice::where('tenant_id', $tenant->id)
            ->where('month', '<', $validated['month'])
            ->orderBy('month', 'desc')
            ->first();

        $last_units = $lastInvoice?->electricity_units ?? 0;

        $unit_diff = $validated['electricity_units'] - $last_units;
        $unit_diff = max($unit_diff, 0); // prevent negative value

        $electricity_charge = $unit_diff * 10; // fixed unit price

        $total_amount = $electricity_charge + $validated['water_charge'] + $tenant->rent_amount;

        Invoice::create([
            'tenant_id' => $validated['tenant_id'],
            'month' => $validated['month'],
            'electricity_units' => $validated['electricity_units'],
            'electricity_charge' => $electricity_charge,
            'water_charge' => $validated['water_charge'],
            'total_amount' => $total_amount,
            'status' => $validated['status'],
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }


    /**
     * Show a single invoice as PDF.
     */
    public function show(Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Edit invoice (optional).
     */
    public function edit(Invoice $invoice)
    {
        $tenants = Tenant::all();
        return view('invoices.edit', compact('invoice', 'tenants'));
    }

    /**
     * Update invoice.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'month' => 'required|date_format:Y-m',
            'electricity_units' => 'required|numeric',
            'water_charge' => 'required|numeric',
            'status' => 'required|in:paid,unpaid',
        ]);

        // Get tenant with rent
        $tenant = Tenant::findOrFail($validated['tenant_id']);

        // Get last month's invoice (excluding the one being updated)
        $lastInvoice = Invoice::where('tenant_id', $tenant->id)
            ->where('month', '<', $validated['month'])
            ->where('id', '!=', $invoice->id) // Exclude current
            ->orderBy('month', 'desc')
            ->first();

        $last_units = $lastInvoice?->electricity_units ?? 0;

        $unit_diff = $validated['electricity_units'] - $last_units;
        $unit_diff = max($unit_diff, 0); // prevent negative

        $electricity_charge = $unit_diff * 10; // ₹10/unit fixed
        $total_amount = $electricity_charge + $validated['water_charge'] + $tenant->rent_amount;

        $invoice->update([
            'tenant_id' => $validated['tenant_id'],
            'month' => $validated['month'],
            'electricity_units' => $validated['electricity_units'],
            'electricity_charge' => $electricity_charge,
            'water_charge' => $validated['water_charge'],
            'total_amount' => $total_amount,
            'status' => $validated['status'],
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }



    /**
     * Delete invoice.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }

    /**
     * Download PDF version of invoice.
     */
    public function download(Invoice $invoice)
    {
        $pdf = Pdf::loadView('invoices.invoice', compact('invoice'));
        return $pdf->download("Invoice_{$invoice->id}.pdf");
    }
}
