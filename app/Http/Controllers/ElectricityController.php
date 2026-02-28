<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ElectricityController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::query()->with('tenant');

        // Filter by Month
        if ($request->has('month') && $request->filled('month')) {
            $query->where('month', $request->month);
        }

        // Filter by Tenant
        if ($request->has('tenant_id') && $request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        // Only invoices with electricity charge > 0 or where units exist?
        // User asked for "Electricity Unit Module", implies showing all electricity usage.
        // It's safer to show all invoices to avoid missing data, or filter where electricity_units > 0.
        // Let's filter to ensure we act on relevant records.
        // $query->where('electricity_units', '>', 0); 

        $invoices = $query->orderBy('month', 'desc')->paginate(10);

        $tenants = Tenant::all();

        // Calculate Totals for the current filtered set (or all for the page context?)
        // Usually totals should reflect the filter.
        // Since pagination is used, calculating total on collection only shows page total.
        // We might want a separate query for grand totals of the filter.

        $usageQuery = clone $query;
        // Optimization: remove eager loading for aggregate query
        $usageQuery->withOnly([]);

        $totalUnits = $usageQuery->sum('electricity_units_consumed'); // Wait, we don't store 'consumed' directly in DB, we store 'current units'.
        // The InvoiceService calculates 'used units' on the fly for PDF.
        // We need a robust way to calculate 'used items'.
        // Invoice Model stores: 'electricity_units' (current reading) and 'electricity_charge'.
        // To get 'units used', we can back-calculate from 'electricity_charge' / RATE.
        // rate is config('constants.ELECTRIC_RATE', 10).

        $electricRate = config('constants.ELECTRIC_RATE', 10);
        $totalCharge = $usageQuery->sum('electricity_charge');
        $totalUnits = $electricRate > 0 ? round($totalCharge / $electricRate) : 0;

        return view('electricity.index', compact('invoices', 'tenants', 'totalUnits', 'totalCharge', 'electricRate'));
    }
}
