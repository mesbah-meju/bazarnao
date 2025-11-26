<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccCoa;
use App\Models\AccSubcode;
use App\Models\AccSubtype;
use Illuminate\Http\Request;
use App\Exports\SubLedgerExport;
use Maatwebsite\Excel\Facades\Excel;

class SubLedgerController extends Controller
{
    public function index(Request $request)
    {
        $subtypes = AccSubtype::distinct()
            ->select('acc_subtypes.id', 'acc_subtypes.name')
            ->join('acc_subcodes', 'acc_subcodes.sub_type_id', '=', 'acc_subtypes.id')
            ->orderBy('acc_subtypes.id', 'asc')
            ->get();

        return view('backend.accounts.reports.sub_ledger.index', compact('subtypes'));
    }

    public function report(Request $request)
    {
        $subtypes = AccSubtype::distinct()
            ->select('acc_subtypes.id', 'acc_subtypes.name')
            ->join('acc_subcodes', 'acc_subcodes.sub_type_id', '=', 'acc_subtypes.id')
            ->orderBy('acc_subtypes.id', 'asc')
            ->get();

        $subtype = $request->subtype;
        $subcode = $request->subcode;
        $accounthead = $request->accounthead; 
        $warehouse_id    = $request->warehouse_id;
        $dtpFromDate = date('Y-m-d', strtotime($request->dtpFromDate));
        $dtpToDate = date('Y-m-d', strtotime($request->dtpToDate));
        $type = $request->type;

        // Normalize account heads to array to support multi-select
        $accountHeadCodes = [];
        if (is_array($accounthead)) {
            $accountHeadCodes = $accounthead;
        } elseif (is_string($accounthead) && strpos($accounthead, ',') !== false) {
            $accountHeadCodes = array_filter(array_map('trim', explode(',', $accounthead)));
        } elseif (!empty($accounthead)) {
            $accountHeadCodes = [$accounthead];
        }

        $subLedger = get_subcode_by_id($subcode);

        // Compute opening balance and transactions aggregating multiple heads if provided
        $prebalance = 0;
        $HeadName2 = collect();
        if (!empty($accountHeadCodes)) {
            foreach ($accountHeadCodes as $headCode) {
                $prebalance += (float) get_opening_balance_subtype($headCode, $warehouse_id, $dtpFromDate, $dtpToDate, $subtype, $subcode);
                $headTxns = collect(get_general_ledger_report($headCode, $warehouse_id, $dtpFromDate, $dtpToDate, 1, 0, $subtype, $subcode));
                $HeadName2 = $HeadName2->merge($headTxns);
            }
            // Sort merged transactions by voucher_date then voucher_no for stability
            $HeadName2 = $HeadName2->sortBy(function ($item) {
                return sprintf('%s-%s', $item->voucher_date ?? '', $item->voucher_no ?? '');
            })->values();
            // For header display, set a placeholder ledger when multiple heads selected
            $HeadName = (object) [
                'head_name' => count($accountHeadCodes) > 1 ? 'Multiple Account Heads' : (optional(general_led_report_head_name($accountHeadCodes[0]))->head_name ?? ''),
                'head_type' => count($accountHeadCodes) > 1 ? 'A' : (optional(general_led_report_head_name($accountHeadCodes[0]))->head_type ?? 'A'),
            ];
        } else {
            // Fallback to previous single-head behavior
            $HeadName = general_led_report_head_name($accounthead);
            $prebalance = get_opening_balance_subtype($accounthead, $warehouse_id, $dtpFromDate, $dtpToDate, $subtype, $subcode);
            $HeadName2 = collect(get_general_ledger_report($accounthead, $warehouse_id, $dtpFromDate, $dtpToDate, 1, 0, $subtype, $subcode));
        }

        // Get warehouse name
        $warehouseName = 'All Warehouses';
        if ($warehouse_id) {
            $warehouse = \App\Models\Warehouse::find($warehouse_id);
            $warehouseName = $warehouse ? $warehouse->name : 'All Warehouses';
        }

        $data['warehouse_id'] = $warehouse_id;
        $data['dtpFromDate'] = $dtpFromDate;
        $data['dtpToDate'] = $dtpToDate;
        $data['subtype'] = $subtype;
        $data['subcode'] = $subcode;
        $data['accounthead'] = $accounthead;
        $data['warehouseName'] = $warehouseName;

        $data['ledger'] = $HeadName;
        $data['subLedger'] = $subLedger;
        $data['HeadName2'] = $HeadName2;
        $data['prebalance'] =  $prebalance;

        $data['subtypes']  = $subtypes;
        $data['subcodes']  = get_sub_type_items($subtype);
        $data['acchead']  = get_account_head_by_subtype($subtype);

        // Handle Excel export
        if ($type == 'excel') {
            return Excel::download(new SubLedgerExport($data), 'SubLedger_' . date('Y-m-d') . '.xlsx');
        }

        return view('backend.accounts.reports.sub_ledger.report', $data);
    }

    public function get_subcode($id)
    {
        $htm = '';
        $subcodes = AccSubcode::where('sub_type_id', $id)->get();

        if ($subcodes) {
            foreach ($subcodes as $subcode) {
                $htm .= '<option value="' . $subcode->id . '" >' . $subcode->name . '</option>';
            }
        }
        echo json_encode($htm);
    }

    public function get_account_head($id)
    {
        $htm = '';
        $coas =  AccCoa::where('sub_type', $id)
            ->get();

        if ($coas) {
            foreach ($coas as $coa) {
                $htm .= '<option value="' . $coa->head_code . '" >' . $coa->head_name . '</option>';
            }
        }
        echo json_encode($htm);
    }

    public function get_subcode_by_accounthead($codes)
    {
        $htm = '';
        $headCodes = array_filter(array_map('trim', explode(',', $codes)));
        if (empty($headCodes)) {
            echo json_encode($htm);
            return;
        }

        $subTypeIds = AccCoa::whereIn('head_code', $headCodes)->pluck('sub_type')->unique()->filter()->values();
        if ($subTypeIds->isEmpty()) {
            echo json_encode($htm);
            return;
        }

        $subcodes = AccSubcode::whereIn('sub_type_id', $subTypeIds)->orderBy('id', 'asc')->get();
        foreach ($subcodes as $subcode) {
            $htm .= '<option value="' . $subcode->id . '">' . $subcode->name . '</option>';
        }
        echo json_encode($htm);
    }
}
