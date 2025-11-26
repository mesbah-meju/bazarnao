<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccCoa;
use App\Models\AccVoucher;
use Illuminate\Http\Request;
use DB;
use App\Exports\ExpenditureStatementExport;
use Maatwebsite\Excel\Facades\Excel;

class ExpenditureStatementController extends Controller
{
    public function index()
    {      
        return view('backend.accounts.reports.expenditure_statement.index');
    }

    public function report(Request $request)
    {
        $warehouse_id   = $request->warehouse_id;
        $dtpFromDate = date('Y-m-d', strtotime($request->dtpFromDate));
        $dtpToDate = date('Y-m-d', strtotime($request->dtpToDate));
        $type = $request->type;
        
        // Get warehouse name
        $warehouseName = 'All Warehouses';
        if ($warehouse_id) {
            $warehouse = \App\Models\Warehouse::find($warehouse_id);
            $warehouseName = $warehouse ? $warehouse->name : 'All Warehouses';
        }

        $data['warehouse_id'] = $warehouse_id;
        $data['dtpFromDate'] = $dtpFromDate;
        $data['dtpToDate'] = $dtpToDate;
        $data['currency'] = '';
        $data['warehouseName'] = $warehouseName;

        $data['expenses'] = get_head_summary('E', 'Expenses', $warehouse_id, $dtpFromDate, $dtpToDate, 0);

        // Handle Excel export
        if ($type == 'excel') {
            return Excel::download(new ExpenditureStatementExport($data), 'ExpenditureStatement_' . date('Y-m-d') . '.xlsx');
        }

        return view('backend.accounts.reports.expenditure_statement.report', $data);
    }
}
