<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccCoa;
use Illuminate\Http\Request;
use App\Exports\BankBookExport;
use Maatwebsite\Excel\Facades\Excel;

class BankBookController extends Controller
{
    public function index(Request $request)
    {
        $bankbook = AccCoa::where('is_bank_nature', 1)->orderBy('head_name', 'asc')->get();

        return view('backend.accounts.reports.bankbook.index', compact('bankbook'));
    }
    public function report(Request $request)
    {
        $bankbook = AccCoa::where('is_bank_nature', 1)->orderBy('head_name', 'asc')->get();

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
        $data['bankbook']  = $bankbook;

        // Handle Excel export
        if ($type == 'excel') {
            return Excel::download(new BankBookExport($data), 'BankBook_' . date('Y-m-d') . '.xlsx');
        }

        return view('backend.accounts.reports.bankbook.report', $data);
    }
}
