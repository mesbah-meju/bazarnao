<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccCoa;
use App\Models\AccTransaction;
use App\Models\AccVoucher;
use Illuminate\Http\Request;
use App\Exports\BankReconciliationExport;
use Maatwebsite\Excel\Facades\Excel;

class BankReconciliationController extends Controller
{
    public function index(Request $request)
    {
        $banks = AccCoa::where('is_bank_nature', 1)->where('is_active', 1)->orderBy('id', 'asc')->get();

        $warehouse_id   = $request->warehouse_id ? $request->warehouse_id : null;
        $dtpFromDate    = $request->dtpFromDate ? date('Y-m-d', strtotime($request->dtpFromDate)) : date('Y-m-d', strtotime('first day of this month'));
        $dtpToDate      = $request->dtpToDate ? date('Y-m-d', strtotime($request->dtpToDate)) : date('Y-m-d', strtotime('last day of this month'));
        $bankCode       = $request->bankCode ? $request->bankCode : null;
        
        if ($bankCode == '' || $bankCode == null) {
            $bankCode = null;
        } else {
            $bankCode = $bankCode;
        }
        
        $data['banks']       = $banks;
        $data['warehouse_id']= $warehouse_id;
        $data['dtpFromDate'] = $dtpFromDate;
        $data['dtpToDate']   = $dtpToDate;
        $data['bankCode']    = $bankCode;
        $data['vouchers']    = reconciliation_voucher($dtpFromDate, $dtpToDate, $bankCode, $warehouse_id, 0);

        return view('backend.accounts.reports.bank_reconciliation.index', $data);
    }

    public function report(Request $request)
    {
        $banks = AccCoa::where('is_bank_nature', 1)->where('is_active', 1)->orderBy('id', 'asc')->get();

        $warehouse_id        = $request->warehouse_id ? $request->warehouse_id : null;
        $dtpFromDate         = $request->dtpFromDate ? date('Y-m-d', strtotime($request->dtpFromDate)) : date('Y-m-d', strtotime('first day of this month'));
        $dtpToDate           = $request->dtpToDate ? date('Y-m-d', strtotime($request->dtpToDate)) : date('Y-m-d', strtotime('last day of this month'));
        $bankCode            = $request->bankCode ? $request->bankCode : null;
        $type                = $request->type;
        
        if ($bankCode == '' || $bankCode == null) {
            $bankCode = null;
        } else {
            $bankCode = $bankCode;
        }

        // Get warehouse name
        $warehouseName = 'All Warehouses';
        if ($warehouse_id) {
            $warehouse = \App\Models\Warehouse::find($warehouse_id);
            $warehouseName = $warehouse ? $warehouse->name : 'All Warehouses';
        }

        // Get bank name
        $bankName = 'All Banks';
        if ($bankCode) {
            $bank = AccCoa::where('head_code', $bankCode)->first();
            $bankName = $bank ? $bank->head_name : 'All Banks';
        }
        
        $data['banks']       = $banks;
        $data['warehouse_id']= $warehouse_id;
        $data['dtpFromDate'] = $dtpFromDate;
        $data['dtpToDate']   = $dtpToDate;
        $data['bankCode']    = $bankCode;
        $data['vouchers']    = reconciliation_voucher($dtpFromDate, $dtpToDate, $bankCode, $warehouse_id, 0);
        $data['currency'] = '';
        $data['warehouseName'] = $warehouseName;
        $data['bankName'] = $bankName;

        // Handle Excel export
        if ($type == 'excel') {
            return Excel::download(new BankReconciliationExport($data), 'BankReconciliation_' . date('Y-m-d') . '.xlsx');
        }

        return view('backend.accounts.reports.bank_reconciliation.report', $data);
    }

    public function approve($voucher_no)
    {
        $udata = array(
            'is_honour'  => 1
        );

        $upvoucher = AccVoucher::where('voucher_no', $voucher_no)->update($udata);
        $uptransation = AccTransaction::where('voucher_no', $voucher_no)->update($udata);
        
        if ($upvoucher && $uptransation) {
            flash(translate('Reconcilation has been successfully'))->success();
            return back();
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function disapprove($voucher_no)
    {
        $udata = array(
            'is_honour'  => 0
        );

        $upvoucher = AccVoucher::where('voucher_no', $voucher_no)->update($udata);
        $uptransation = AccTransaction::where('voucher_no', $voucher_no)->update($udata);
        
        if ($upvoucher && $uptransation) {
            flash(translate('Reconcilation has been successfully'))->success();
            return back();
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }
}
