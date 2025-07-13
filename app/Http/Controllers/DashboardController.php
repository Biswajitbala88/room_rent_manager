<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    
    public function index()
    {
        // Current month
        $currentMonth = Carbon::now()->format('Y-m');

        // Set default values to prevent null errors
        $totalPendingInvoices = collect(); // empty collection
        $totalDueAmount = 0;
        $totalReceivedAmount = 0;

        // Tenants list for dropdown
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $tenant->due_invoice_count = Invoice::where('tenant_id', $tenant->id)
                ->whereColumn('received_amount', '<', 'total_amount')
                ->count();
        }

        return view('dashboard', compact(
            'currentMonth',
            'totalPendingInvoices',
            'totalDueAmount',
            'totalReceivedAmount',
            'tenants'
        ));
    }


    public function getDashboardSummary(Request $request)
    {
        $month = $request->input('month'); // format: 2025-07
        $query = Invoice::query();

        if ($month) {
            $query->where('month', $month);
        }

        $totalPendingInvoices = $query->whereColumn('received_amount', '<', 'total_amount')->get();
        $totalDueAmount = $query->sum(DB::raw('total_amount - received_amount'));
        $totalReceivedAmount = $query->sum('received_amount');

        return response()->json([
            'totalPendingCount' => count($totalPendingInvoices),
            'totalDueAmount' => number_format($totalDueAmount, 2),
            'totalReceivedAmount' => number_format($totalReceivedAmount, 2)
        ]);
    }

}
