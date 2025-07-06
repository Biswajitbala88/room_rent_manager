<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;

class DashboardController extends Controller
{
    public function index2()
    {
        return view('dashboard');
    }
    public function index(Request $request)
    {
        $roomNo = $request->room_no;

        $invoices = Invoice::whereHas('tenant', function ($q) use ($roomNo) {
            $q->where('room_no', $roomNo);
        })->where('status', 'unpaid')->get();

        return view('dashboard', compact('invoices'));
    }

    public function savePayments(Request $request)
    {
        $receivedAmounts = $request->input('received_amounts', []);

        foreach ($receivedAmounts as $invoiceId => $received) {
            $invoice = Invoice::find($invoiceId);
            if ($invoice) {
                $invoice->received_amount = $received;
                $invoice->save();

                PaymentHistory::create([
                    'invoice_id' => $invoice->id,
                    'received_amount' => $received,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Payments saved successfully.');
    }

}
