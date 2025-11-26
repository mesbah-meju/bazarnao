<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccCoa;
use App\Models\AccSubcode;
use App\Models\AccVoucher;
use Illuminate\Http\Request;
use DB;
use App\Exports\DayBookExport;
use Maatwebsite\Excel\Facades\Excel;


class DayBookController extends Controller
{
    public function index(Request $request)
    {
        $parties = AccSubcode::where('status', 1)->get();

        $coas = AccCoa::where('head_level', 4)
            ->where('is_active', 1)
            ->orderBy('head_name')
            ->get();

        return view('backend.accounts.reports.daybook.index', compact('parties', 'coas'));
    }

    public function report(Request $request)
    {
        $parties = AccSubcode::where('status', 1)->get();

        $coas = AccCoa::where('head_level', 4)
            ->where('is_active', 1)
            ->orderBy('head_name')
            ->get();

        $daybook = AccCoa::where('is_cash_nature', 1)->orderBy('head_name', 'asc')->get();

        $cmbCode        = $request->cmbCode;
        $warehouse_id   = $request->warehouse_id;
        $party_id       = $request->party_id;
        $head_code      = $request->head_code;
        $dtpFromDate    = date('Y-m-d', strtotime($request->dtpFromDate));
        $dtpToDate      = date('Y-m-d', strtotime($request->dtpToDate));
        $type           = $request->type;

        $voucherInfo = get_voucher_by_date($warehouse_id, $party_id, $head_code, $dtpFromDate, $dtpToDate, 1);

        // Get warehouse name
        $warehouseName = 'All Warehouses';
        if ($warehouse_id) {
            $warehouse = \App\Models\Warehouse::find($warehouse_id);
            $warehouseName = $warehouse ? $warehouse->name : 'All Warehouses';
        }

        $data['warehouse_id'] = $warehouse_id;
        $data['parties'] = $parties;
        $data['party_id'] = $party_id;
        $data['coas'] = $coas;
        $data['head_code'] = $head_code;
        $data['dtpFromDate'] = $dtpFromDate;
        $data['dtpToDate'] = $dtpToDate;
        $data['cmbCode'] = $cmbCode;
        $data['warehouseName'] = $warehouseName;
        
        $data['voucherInfo'] =  $voucherInfo; 
        $data['daybook']  = $daybook;

        // Handle Excel export
        if ($type == 'excel') {
            return Excel::download(new DayBookExport($data), 'DayBook_' . date('Y-m-d') . '.xlsx');
        }

        return view('backend.accounts.reports.daybook.report', $data);
    }

}
