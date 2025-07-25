<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Show list of all invoices.
     */
    public function index()
    {
        $invoices = Invoice::with('tenant')
            // ->where('tenant_id', 5)
            ->orderByDesc('month')
            ->get();
// echo '<pre>'; print_r($invoices->toArray()); exit;

        $invoices->map(function ($invoice, $index) use ($invoices) {
            // Find previous invoice for the same tenant
// echo '<pre>'; print_r($invoice->tenant_id); exit;

            $previous = $invoices
                ->where('tenant_id', $invoice->tenant_id)
                ->where('month', '<', $invoice->month)
                ->where('electricity_units', '>', 0)
                ->sortByDesc('month')
                ->first();
            // echo '<pre>'; print_r($previous->toArray()); exit;

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
        $tenants = Tenant::all();
        return view('invoices.create', compact('tenants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'month' => 'required|date_format:Y-m',
            'electricity_units' => 'required|numeric',
            'water_charge' => 'required|numeric',
            // 'status' => 'required|in:paid,unpaid',
        ]);

        $tenant = Tenant::findOrFail($validated['tenant_id']);
        $electricRate = Config::get('constants.electric_rate');

        $lastInvoice = Invoice::where('tenant_id', $tenant->id)
            ->where('month', '<', $validated['month'])
            ->orderBy('month', 'desc')
            ->first();

        $last_units = $lastInvoice?->electricity_units ?? 0;
        $unit_diff = max($validated['electricity_units'] - $last_units, 0);
        $electricity_charge = $unit_diff * $electricRate;
        $total_amount = $electricity_charge + $validated['water_charge'] + $tenant->rent_amount;

        Invoice::create([
            'tenant_id' => $validated['tenant_id'],
            'month' => $validated['month'],
            'electricity_units' => $validated['electricity_units'],
            'electricity_charge' => $electricity_charge,
            'water_charge' => $validated['water_charge'],
            'total_amount' => $total_amount,
            // 'status' => $validated['status'],
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    // AJAX endpoint to get last unit
    public function getLastUnits($tenant_id, $month)
    {
        $lastInvoice = Invoice::where('tenant_id', $tenant_id)
            ->where('month', '<', $month)
            ->where('electricity_units', '>', 0)
            ->orderBy('month', 'desc') 
            ->first();

        return response()->json([
            'last_units' => $lastInvoice?->electricity_units ?? 0,
        ]);
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
            'received_amount' => 'required|numeric',
        ]);

        // Get tenant with rent
        $tenant = Tenant::findOrFail($validated['tenant_id']);
        $electricRate = Config::get('constants.electric_rate');

        // Get last month's invoice (excluding the one being updated)
        $lastInvoice = Invoice::where('tenant_id', $tenant->id)
            ->where('month', '<', $validated['month'])
            ->where('id', '!=', $invoice->id) // Exclude current
            ->where('electricity_units', '>', 0)
            ->orderBy('month', 'desc')
            ->first();

        $last_units = $lastInvoice?->electricity_units ?? 0;

        $unit_diff = $validated['electricity_units'] - $last_units;
        $unit_diff = max($unit_diff, 0); // prevent negative

        $electricity_charge = $unit_diff * $electricRate;
        $total_amount = $electricity_charge + $validated['water_charge'] + $tenant->rent_amount;

        $invoice->update([
            'tenant_id' => $validated['tenant_id'],
            'month' => $validated['month'],
            'electricity_units' => $validated['electricity_units'],
            'electricity_charge' => $electricity_charge,
            'water_charge' => $validated['water_charge'],
            'total_amount' => $total_amount,
            'received_amount' => $validated['received_amount'],
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }
    

    // public function getLastUnits(Request $request)
    // {
    //     $tenantId = $request->query('tenant_id');
    //     $month = $request->query('month');

    //     $last = DB::table('invoices')
    //         ->where('tenant_id', $tenantId)
    //         ->where('month', '<', $month)
    //         ->orderBy('month', 'desc')
    //         ->first();

    //     return response()->json([
    //         'last_units' => $last->electricity_units ?? 0,
    //     ]);
    // }



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
        // Current reading from the invoice
        $currentUnit = (int) $invoice->electricity_units;
        $invoice->currentUnit = $currentUnit;
        // Fetch the previous invoice for the same tenant
        $lastInvoice = Invoice::where('tenant_id', $invoice->tenant_id)
            ->where('month', '<', $invoice->month)
            ->where('electricity_units', '>', 0)
            ->orderBy('month', 'desc')
            ->first();

        // Previous reading
        $previousUnit = $lastInvoice ? (int) $lastInvoice->electricity_units : 0;

        // Calculate usage
        $unitDiff = $currentUnit - $previousUnit;
        $electricityRate = config('constants.ELECTRIC_RATE', 10); // ₹10 default if not found
        $electricityCharge = $unitDiff * $electricityRate;

        // Append custom display-only fields
        $invoice->electricity_display = "{$currentUnit} - {$previousUnit} = {$unitDiff}";
        $invoice->electricity_used_units = $unitDiff;
        $invoice->electricity_charge = $electricityCharge;
        $invoice->electricity_rate = $electricityRate;

        // Remove debug
        $nameSlug = strtolower(str_replace(' ', '_', $invoice->tenant->name));
        $filenameSlug = "{$nameSlug}_{$invoice->month}.pdf";
        
        
        $filename = "Invoice_{$filenameSlug}";

        // Load and download PDF
        // return view('invoices.invoice', compact('invoice', 'invoice'));exit;
        $pdf = Pdf::loadView('invoices.invoice', compact('invoice'));
        return $pdf->download($filename);
    }

    public function getDueInvoices($id)
    {
        $invoices = Invoice::where('tenant_id', $id)
        ->whereColumn('received_amount', '<', 'total_amount')
        ->get(['id', 'month', 'total_amount', 'received_amount'])
        ->map(function ($invoice) {
            $invoice->month = \Carbon\Carbon::parse($invoice->month)->format('Y-F');
            return $invoice;
        });
        return response()->json($invoices);
    }
    public function addPayment(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $amountToAdd = $request->input('amount', 0);

        $invoice->received_amount += $amountToAdd;
        $invoice->save();

        return response()->json([
            'success' => true,
            'new_received' => $invoice->received_amount,
            'new_due' => $invoice->total_amount - $invoice->received_amount,
        ]);
    }


}
