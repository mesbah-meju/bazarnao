<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccCoa;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\GeneralLedgerExport;
use Maatwebsite\Excel\Facades\Excel;

class GeneralLedgerController extends Controller
{
    public function index(Request $request)
    {
        $general_ledger = AccCoa::where('is_bank_nature', 0)
            ->where('is_cash_nature', 0)
            ->where('head_level', 4)
            ->where('is_active', 1)
            ->orderBy('head_name', 'asc')
            ->get();

        return view('backend.accounts.reports.general_ledger.index', compact('general_ledger'));
    }

    public function report(Request $request)
    {
        $general_ledger = AccCoa::where('is_bank_nature', 0)
            ->where('is_cash_nature', 0)
            ->where('head_level', 4)
            ->where('is_active', 1)
            ->orderBy('head_name', 'asc')
            ->get();

        $cmbCode = $request->cmbCode;  
        $warehouse_id    = $request->warehouse_id;
        $party_name = $request->party_name;
        $dtpFromDate = date('Y-m-d', strtotime($request->dtpFromDate));
        $dtpToDate = date('Y-m-d', strtotime($request->dtpToDate));
        $type = $request->type;

        $HeadName = general_led_report_head_name($cmbCode);
        $pre_balance = get_opening_balance($cmbCode, $warehouse_id, $dtpFromDate, $dtpToDate);
        $HeadName2 = get_general_ledger_report($cmbCode, $warehouse_id, $dtpFromDate, $dtpToDate, 1);
        
        // Extract unique party names from transactions
        $partyNames = collect();
        foreach ($HeadName2 as $transaction) {
            $partyName = null;
            
            // Check supplier name
            if (($transaction->rev_coa->head_code == 5020201 || $transaction->rev_coa->head_code == 10204) && $transaction->reference_no) {
                $purchase = Purchase::find($transaction->reference_no);
                if ($purchase && $purchase->supplier_id) {
                    $supplier = Supplier::find($purchase->supplier_id);
                    if ($supplier) {
                        $partyName = $supplier->name;
                    }
                }
            }
            // Check customer name
            elseif (($transaction->rev_coa->head_code == 3010301 || $transaction->rev_coa->head_code == 1020801 || $transaction->rev_coa->head_code == 40101 || $transaction->rev_coa->head_code == 4010101 || $transaction->rev_coa->head_code == 1020401 || $transaction->rev_coa->head_code == 1020802) && $transaction->reference_no) {
                $order = Order::find($transaction->reference_no);
                if ($order && $order->user_id) {
                    $customer = User::find($order->user_id);
                    if ($customer) {
                        $partyName = $customer->name;
                    }
                }
            }
            // Check relvalue name
            elseif ($transaction->relvalue) {
                $partyName = $transaction->relvalue->name;
            }
            
            if ($partyName && !$partyNames->contains($partyName)) {
                $partyNames->push($partyName);
            }
        }
        $partyNames = $partyNames->sort()->values();
        
        // Filter by party name if provided
        if (!empty($party_name)) {
            $HeadName2 = $HeadName2->filter(function ($transaction) use ($party_name) {
                // Check supplier name
                if (($transaction->rev_coa->head_code == 5020201 || $transaction->rev_coa->head_code == 10204) && $transaction->reference_no) {
                    $purchase = Purchase::find($transaction->reference_no);
                    if ($purchase && $purchase->supplier_id) {
                        $supplier = Supplier::find($purchase->supplier_id);
                        if ($supplier && $supplier->name == $party_name) {
                            return true;
                        }
                    }
                }
                
                // Check customer name
                if (($transaction->rev_coa->head_code == 3010301 || $transaction->rev_coa->head_code == 1020801 || $transaction->rev_coa->head_code == 40101 || $transaction->rev_coa->head_code == 4010101) && $transaction->reference_no) {
                    $order = Order::find($transaction->reference_no);
                    if ($order && $order->user_id) {
                        $customer = User::find($order->user_id);
                        if ($customer && $customer->name == $party_name) {
                            return true;
                        }
                    }
                }
                
                // Check relvalue name
                if ($transaction->relvalue && $transaction->relvalue->name == $party_name) {
                    return true;
                }
                
                return false;
            });
        }

        // Get warehouse name
        $warehouseName = 'All Warehouses';
        if ($warehouse_id) {
            $warehouse = \App\Models\Warehouse::find($warehouse_id);
            $warehouseName = $warehouse ? $warehouse->name : 'All Warehouses';
        }

        $data['warehouse_id'] = $warehouse_id;
        $data['party_name'] = $party_name;
        $data['party_names'] = $partyNames;
        $data['dtpFromDate'] = $dtpFromDate;
        $data['dtpToDate'] = $dtpToDate;
        $data['cmbCode'] = $cmbCode;
        $data['HeadName'] = $HeadName;
        $data['ledger'] = $HeadName;
        $data['HeadName2'] = $HeadName2;
        $data['prebalance'] =  $pre_balance;
        $data['warehouseName'] = $warehouseName;
        $data['general_ledger']  = $general_ledger;

        // Handle Excel export
        if ($type == 'excel') {
            return Excel::download(new GeneralLedgerExport($data), 'GeneralLedger_' . date('Y-m-d') . '.xlsx');
        }

        return view('backend.accounts.reports.general_ledger.report', $data);
    }

    public function print(Request $request)
    {
        $general_ledger = AccCoa::where('is_bank_nature', 0)
            ->where('is_cash_nature', 0)
            ->where('head_level', 4)
            ->where('is_active', 1)
            ->orderBy('head_name', 'asc')
            ->get();

        $cmbCode = $request->cmbCode;  
        $warehouse_id    = $request->warehouse_id;
        $party_name = $request->party_name;
        $dtpFromDate = date('Y-m-d', strtotime($request->dtpFromDate));
        $dtpToDate = date('Y-m-d', strtotime($request->dtpToDate));

        $HeadName = general_led_report_head_name($cmbCode);
        $pre_balance = get_opening_balance($cmbCode, $warehouse_id, $dtpFromDate, $dtpToDate);
        $HeadName2 = get_general_ledger_report($cmbCode, $warehouse_id, $dtpFromDate, $dtpToDate, 1);
        
        // Filter by party name if provided
        if (!empty($party_name)) {
            $HeadName2 = $HeadName2->filter(function ($transaction) use ($party_name) {
                // Check supplier name
                if (($transaction->rev_coa->head_code == 5020201 || $transaction->rev_coa->head_code == 10204) && $transaction->reference_no) {
                    $purchase = Purchase::find($transaction->reference_no);
                    if ($purchase && $purchase->supplier_id) {
                        $supplier = Supplier::find($purchase->supplier_id);
                        if ($supplier && $supplier->name == $party_name) {
                            return true;
                        }
                    }
                }
                
                // Check customer name
                if (($transaction->rev_coa->head_code == 3010301 || $transaction->rev_coa->head_code == 1020801 || $transaction->rev_coa->head_code == 40101 || $transaction->rev_coa->head_code == 4010101) && $transaction->reference_no) {
                    $order = Order::find($transaction->reference_no);
                    if ($order && $order->user_id) {
                        $customer = User::find($order->user_id);
                        if ($customer && $customer->name == $party_name) {
                            return true;
                        }
                    }
                }
                
                // Check relvalue name
                if ($transaction->relvalue && $transaction->relvalue->name == $party_name) {
                    return true;
                }
                
                return false;
            });
        }

        $data['warehouse_id'] = $warehouse_id;
        $data['party_name'] = $party_name;
        $data['dtpFromDate'] = $dtpFromDate;
        $data['dtpToDate'] = $dtpToDate;
        $data['cmbCode'] = $cmbCode;
        $data['HeadName'] = $HeadName;
        $data['ledger'] = $HeadName;
        $data['HeadName2'] = $HeadName2;
        $data['prebalance'] =  $pre_balance;

        $data['general_ledger']  = $general_ledger;

        return view('backend.accounts.reports.general_ledger.print', $data);
    }
}
