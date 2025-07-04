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
        $invoices = Invoice::with('tenant')->orderBy('created_at', 'desc')->get();
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
            'electricity_units' => 'nullable|numeric',
            'electricity_charge' => 'nullable|numeric',
            'water_charge' => 'nullable|numeric',
            'total_amount' => 'required|numeric',
            'status' => 'required|in:paid,unpaid',
        ]);

        Invoice::create($validated);

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
            'electricity_units' => 'nullable|numeric',
            'electricity_charge' => 'nullable|numeric',
            'water_charge' => 'nullable|numeric',
            'total_amount' => 'required|numeric',
            'status' => 'required|in:paid,unpaid',
        ]);

        $invoice->update($validated);

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
