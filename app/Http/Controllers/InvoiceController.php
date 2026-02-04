<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Tenant;
use App\Services\InvoiceService;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Show list of all invoices.
     */
    public function index()
    {
        $user_id = auth()->user()->id;
        $invoices = Invoice::with(['tenant.parentUser'])->with('tenant')
            ->whereHas('tenant', function ($query) {
                $query->ofUser();
            })
            ->orderByDesc('month')
            ->get();

        // Transformation logic could be moved to resource/service if needed,
        // but kept here for now as it's view-specific data prep.
        $invoices->map(function ($invoice, $index) use ($invoices) {
            $previous = $invoices
                ->where('tenant_id', $invoice->tenant_id)
                ->where('month', '<', $invoice->month)
                ->where('electricity_units', '>', 0)
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
    public function create(Request $request)
    {
        $tenants = Tenant::where('status', 'active')->ofUser()->orderBy('room_no', 'asc')->get();
        return view('invoices.upsert', compact('tenants'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $this->invoiceService->createInvoice($request->validated());

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    // AJAX endpoint to get last unit
    public function getLastUnits($tenant_id, $month)
    {
        $lastUnits = $this->invoiceService->getLastUnits($tenant_id, $month);

        return response()->json([
            'last_units' => $lastUnits
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
        $tenants = Tenant::ofUser()->get();
        return view('invoices.upsert', compact('invoice', 'tenants'));
    }

    /**
     * Update invoice.
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $this->invoiceService->updateInvoice($invoice, $request->validated());

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
        $invoice = $this->invoiceService->preparePdfData($invoice);
        $filename = $this->invoiceService->getPdfFilename($invoice);

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
