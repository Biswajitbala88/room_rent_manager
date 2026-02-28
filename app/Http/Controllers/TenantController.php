<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Services\TenantService;
use Exception;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function index()
    {
        $tenants = Tenant::ofUser()
            ->with('parentUser')
            ->orderBy('id', 'desc')
            ->paginate(10);
        return view('tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('tenants.upsert');
    }

    public function store(StoreTenantRequest $request)
    {
        try {
            $this->tenantService->createTenant(
                $request->validated(),
                $request->file('aadhaar_image')
            );
            return redirect()->route('tenants.index')->with('success', 'Tenant created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Tenant $tenant)
    {
        return view('tenants.show', compact('tenant'));
    }

    public function edit($id)
    {
        $tenant = Tenant::findOrFail($id);
        return view('tenants.upsert', compact('tenant'));
    }

    public function update(UpdateTenantRequest $request, $id)
    {
        $tenant = Tenant::findOrFail($id);

        try {
            $this->tenantService->updateTenant(
                $tenant,
                $request->validated(),
                $request->file('aadhaar_image')
            );
            return redirect()->back()->with('success', 'Tenant updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->route('tenants.index')->with('success', 'Tenant deleted successfully.');
    }

    public function transactions($id)
    {
        $transactions = \App\Models\Transaction::where('tenant_id', $id)
            ->with('invoice')
            ->orderBy('payment_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'payment_mode' => $transaction->payment_mode ?? 'Unknown',
                    'payment_date' => \Carbon\Carbon::parse($transaction->payment_date)->format('M d, Y'),
                    'created_at' => \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y H:i'),
                    'invoice_month' => $transaction->invoice ? \Carbon\Carbon::parse($transaction->invoice->month)->format('M Y') : 'N/A'
                ];
            });

        return response()->json($transactions);
    }
}

