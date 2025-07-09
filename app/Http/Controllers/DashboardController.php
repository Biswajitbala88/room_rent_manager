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
        $currentMonth = Carbon::now()->format('Y-m'); // "2025-07"

        // Monthly summary
        $totalPendingInvoices = Invoice::ofUser()
            ->whereColumn('received_amount', '<', 'total_amount')
            ->get();

        $totalDueAmount = Invoice::ofUser()
            ->sum(DB::raw('total_amount - received_amount'));

        $totalReceivedAmount = Invoice::ofUser()
            ->sum('received_amount');

        // Tenants for the dropdown
        $tenants = Tenant::ofUser()->get(); // ✅ Fix here

        foreach ($tenants as $tenant) {
            $tenant->due_invoice_count = Invoice::where('tenant_id', $tenant->id)
                ->whereColumn('received_amount', '<', 'total_amount')
                ->count();
        }

        return view('dashboard', compact(
            'tenants',
            'totalPendingInvoices',
            'totalDueAmount',
            'totalReceivedAmount'
        ));
    }


}
