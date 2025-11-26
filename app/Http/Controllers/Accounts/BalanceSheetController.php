<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Exports\BalanceSheetExport;
use Maatwebsite\Excel\Facades\Excel;

class BalanceSheetController extends Controller
{
    public function index(Request $request)
    {
        $dtpFromDate            = (!empty($request->dtpFromDate) ? date('Y-m-d',strtotime($request->dtpFromDate)) : Session::get('fyearStartDate'));
        $dtpToDate              = (!empty($request->dtpToDate) ? date('Y-m-d',strtotime($request->dtpToDate)) : date('Y-m-d'));
        $warehouse_id           = (!empty($request->warehouse_id) ? $request->warehouse_id : '');
        $type                   = $request->type;

        // Get warehouse name
        $warehouseName = 'All Warehouses';
        if ($warehouse_id) {
            $warehouse = \App\Models\Warehouse::find($warehouse_id);
            $warehouseName = $warehouse ? $warehouse->name : 'All Warehouses';
        }

        $data['warehouse_id']   = $warehouse_id;
        $data['dtpFromDate']    = $dtpFromDate;
        $data['dtpToDate']      = $dtpToDate;
        $data['warehouseName']  = $warehouseName;

        $data['financialyears'] = get_previous_financial_year(2);

        $data['assets']         = get_balance_sheet_summary('A', 'Assets', $warehouse_id, $dtpFromDate, $dtpToDate);
        $data['liabilities']    = get_balance_sheet_summary('L', 'Liabilities', $warehouse_id, $dtpFromDate, $dtpToDate);
        $data['equitys']        = get_balance_sheet_summary('L', 'Shareholder\'s Equity', $warehouse_id, $dtpFromDate, $dtpToDate);

        $data['incomes'] = get_head_summary('I', 'Income', $warehouse_id, $dtpFromDate, $dtpToDate, 0);
        $data['expenses'] = get_head_summary('E', 'Expenses', $warehouse_id, $dtpFromDate, $dtpToDate, 0);

        $data['currency'] = '';

        // Handle Excel export
        if ($type == 'excel') {
            return Excel::download(new BalanceSheetExport($data), 'BalanceSheet_' . date('Y-m-d') . '.xlsx');
        }

        return view('backend.accounts.reports.balance_sheet.index', $data);
    }
}
