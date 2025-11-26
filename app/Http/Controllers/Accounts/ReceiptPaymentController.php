<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccCoa;
use App\Models\Bank;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\ReceiptPaymentExport;
use Maatwebsite\Excel\Facades\Excel;

class ReceiptPaymentController extends Controller
{
    public function index()
    {
        return view('backend.accounts.reports.receipt_payment.index');
    }

    public function receipt_payment_report(Request $request)
    {
        $reporttype  = $request->reportType;
        $warehouse_id= $request->warehouse_id;
        $dtpFromDate = date('Y-m-d',strtotime($request->dtpFromDate));
        $dtpToDate   = date('Y-m-d',strtotime($request->dtpToDate));
        $type        = $request->type;

        $cashCode =  get_predefined_head('cash_code');
        $bankCode =  get_predefined_head('bank_code');
        $advancedCode =  get_predefined_head('advance');

        // Get warehouse name
        $warehouseName = 'All Warehouses';
        if ($warehouse_id) {
            $warehouse = \App\Models\Warehouse::find($warehouse_id);
            $warehouseName = $warehouse ? $warehouse->name : 'All Warehouses';
        }

        $data['cashOpening']   =  get_opening_summary($cashCode, $warehouse_id, $dtpFromDate, $dtpToDate);
        $data['bankOpening']   =  get_opening_summary($bankCode, $warehouse_id, $dtpFromDate, $dtpToDate);
        $data['advOpening']    =  get_opening_summary($advancedCode, $warehouse_id, $dtpFromDate, $dtpToDate);
        $data['cashClosing']   =  get_closing_summary($cashCode, $warehouse_id, $dtpToDate);
        $data['bankClosing']   =  get_closing_summary($bankCode, $warehouse_id, $dtpToDate);
        $data['advClosing']    =  get_closing_summary($advancedCode, $warehouse_id, $dtpToDate);
        $data['receiptitems']  =  get_item_ledger_receipt_payment($reporttype, $warehouse_id, $dtpFromDate, $dtpToDate, 'CV');
        $data['paymentitems']  =  get_item_ledger_receipt_payment($reporttype, $warehouse_id, $dtpFromDate, $dtpToDate, 'DV');

        $data['dtpFromDate'] = $dtpFromDate;
        $data['dtpToDate']   = $dtpToDate;
        $data['reportType']  = $reporttype;
        $data['warehouse_id']= $warehouse_id;
        $data['warehouseName'] = $warehouseName;
        
        $data['currency'] = '';

        // Handle Excel export
        if ($type == 'excel') {
            return Excel::download(new ReceiptPaymentExport($data), 'ReceiptPayment_' . date('Y-m-d') . '.xlsx');
        }

        return view('backend.accounts.reports.receipt_payment.report', $data);
    }
}
