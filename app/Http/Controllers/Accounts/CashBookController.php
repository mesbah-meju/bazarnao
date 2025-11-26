<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccCoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\CashBookExport;
use Maatwebsite\Excel\Facades\Excel;


class CashBookController extends Controller
{
    public function index(Request $request)
    {
        $cashbook = AccCoa::where('is_cash_nature', 1)->orderBy('head_name', 'asc')->get();

        return view('backend.accounts.reports.cashbook.index', compact('cashbook'));
    }

    public function report(Request $request)
    {
        $cashbook = AccCoa::where('is_cash_nature', 1)->orderBy('head_name', 'asc')->get();

        $cmbCode = $request->cmbCode;  
        $warehouse_id = $request->warehouse_id;
        $dtpFromDate = date('Y-m-d', strtotime($request->dtpFromDate));
        $dtpToDate = date('Y-m-d', strtotime($request->dtpToDate));
        $type = $request->type;

        $HeadName = general_led_report_head_name($cmbCode);       
        $pre_balance = get_opening_balance($cmbCode, $warehouse_id, $dtpFromDate, $dtpToDate);
        $HeadName2 = get_general_ledger_report($cmbCode, $warehouse_id, $dtpFromDate, $dtpToDate, 1, 0);

        // Get warehouse name
        $warehouseName = 'All Warehouses';
        if ($warehouse_id) {
            $warehouse = \App\Models\Warehouse::find($warehouse_id);
            $warehouseName = $warehouse ? $warehouse->name : 'All Warehouses';
        }

        $data['dtpFromDate'] = $dtpFromDate;
        $data['dtpToDate'] = $dtpToDate;
        $data['cmbCode'] = $cmbCode;
        $data['warehouse_id'] = $warehouse_id;
        $data['warehouseName'] = $warehouseName;
        $data['HeadName'] = $HeadName;
        $data['ledger'] = $HeadName;
        $data['HeadName2'] = $HeadName2;
        $data['prebalance'] =  $pre_balance;
        $data['cashbook']  = $cashbook;

        // Handle Excel export
        if ($type == 'excel') {
            return Excel::download(new CashBookExport($data), 'CashBook_' . date('Y-m-d') . '.xlsx');
        }

        return view('backend.accounts.reports.cashbook.report', $data);
    }
}
