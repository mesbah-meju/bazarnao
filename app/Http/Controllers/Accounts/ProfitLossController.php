<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccPredefineAccount;
use App\Models\FinancialYear;
use Illuminate\Http\Request;
use App\Exports\ProfitLossExport;
use Maatwebsite\Excel\Facades\Excel;

class ProfitLossController extends Controller
{
    public function index()
    {
        return view('backend.accounts.reports.profit_loss.index');
    }

    public function report(Request $request)
    {
        $dtpFromDate = date('Y-m-d',strtotime($request->dtpFromDate));
        $dtpToDate   = date('Y-m-d',strtotime($request->dtpToDate));
        $warehouse_id= (!empty($request->warehouse_id) ? $request->warehouse_id : '');
        $type = $request->type;

        // Get warehouse name
        $warehouseName = 'All Warehouses';
        if ($warehouse_id) {
            $warehouse = \App\Models\Warehouse::find($warehouse_id);
            $warehouseName = $warehouse ? $warehouse->name : 'All Warehouses';
        }

        $data['incomes'] = get_head_summary('I', 'Income', $warehouse_id, $dtpFromDate, $dtpToDate, 0);
        $data['expenses'] = get_head_summary('E', 'Expenses', $warehouse_id, $dtpFromDate, $dtpToDate, 0);

        $data['dtpFromDate']  = $dtpFromDate;
        $data['dtpToDate']    = $dtpToDate;
        $data['warehouse_id'] = $warehouse_id;
        $data['warehouseName'] = $warehouseName;

        // Handle Excel export
        if ($type == 'excel') {
            return Excel::download(new ProfitLossExport($data), 'ProfitLoss_' . date('Y-m-d') . '.xlsx');
        }

        return view('backend.accounts.reports.profit_loss.report', $data);
    }
}
