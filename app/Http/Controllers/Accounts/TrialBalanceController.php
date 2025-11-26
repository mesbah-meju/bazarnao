<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\TrialBalanceExport;
use Maatwebsite\Excel\Facades\Excel;

class TrialBalanceController extends Controller
{
    public function index(Request $request)
    {
        return view('backend.accounts.reports.trialbalance.index');
    }

    public function report(Request $request)
    {
        $data['software_info'] = '';
        $dtpFromDate     = date('Y-m-d', strtotime($request->input('dtpFromDate')));
        $dtpToDate       = date('Y-m-d', strtotime($request->input('dtpToDate')));
        $warehouse_id    = $request->warehouse_id;
        $type            = $request->type;

        $chkWithOpening  = $request->input('chkWithOpening', true);
        $accounts = get_transational_accounts();

        $transationList = array();
        $openingList = array();
        foreach ($accounts as $account) {
            $opening =   get_opening_balance($account->head_code, $warehouse_id, $dtpFromDate, $dtpToDate);
            $transsummery  = get_general_ledger_report($account->head_code, $warehouse_id, $dtpFromDate, $dtpToDate, 0, 0);
            
            $transsummery['head_name'] = $account->head_name;
            $transsummery['head_code'] = $account->head_code;
            $transsummery['head_type'] = $account->head_type;
            $transsummery['sub_type'] = $account->sub_type;
            $transsummery['pre_head_name'] = $account->pre_head_name;
            $transsummery['pre_head_code'] = $account->pre_head_code;
            $transationList[$account->head_code] = $transsummery;
            $openingList[$account->head_code] = $opening;
        }

        // Get warehouse name
        $warehouseName = 'All Warehouses';
        if ($warehouse_id) {
            $warehouse = \App\Models\Warehouse::find($warehouse_id);
            $warehouseName = $warehouse ? $warehouse->name : 'All Warehouses';
        }

        $data['openings']  = $openingList;
        $data['results']  = $transationList;

        $data['warehouse_id'] = $warehouse_id;
        $data['dtpFromDate']  = $dtpFromDate;
        $data['dtpToDate']    = $dtpToDate;
        $data['currency'] = defult_currency_symbol();
        $data['warehouseName'] = $warehouseName;

        // Handle Excel export
        if ($type == 'excel') {
            return Excel::download(new TrialBalanceExport($data), 'TrialBalance_' . date('Y-m-d') . '.xlsx');
        }

        return view('backend.accounts.reports.trialbalance.report', $data);
    }

    public function detail(Request $request)
    {
        $cmbCode = $request->input('coaid');
        $warehouse_id = $request->input('warehouse_id');
        $dtpFromDate = $request->input('sdate');
        $dtpToDate = $request->input('edate');

        $HeadName = general_led_report_head_name($cmbCode);
        $pre_balance = get_opening_balance($cmbCode, $warehouse_id, $dtpFromDate, $dtpToDate);
        $HeadName2 = get_general_ledger_report($cmbCode, $warehouse_id, $dtpFromDate, $dtpToDate, 1, 0);

        $data['warehouse_id'] = $warehouse_id;
        $data['dtpFromDate'] = $dtpFromDate;
        $data['dtpToDate'] = $dtpToDate;
        $data['cmbCode'] = $cmbCode;
        $data['HeadName'] = $HeadName;
        $data['ledger'] = $HeadName;
        $data['HeadName2'] = $HeadName2;
        $data['prebalance'] =  $pre_balance;

        $data['coaid'] = $cmbCode;
        $data['edate'] = $dtpToDate;
        $data['sdate'] = $dtpFromDate;
        $data['achead'] = get_general_ledger_head_name($cmbCode);
        $data['currency_symbol'] = defult_currency_symbol();

        return view('backend.accounts.reports.trialbalance.detail', $data);
    }
}
