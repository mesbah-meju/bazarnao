<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\CashTransfer;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\AccCoa;
use App\Models\AccTransaction;
use Illuminate\Support\Facades\Auth;
use Route;
use App\Exports\CashTransferExport;
use Maatwebsite\Excel\Facades\Excel;

class CashTransferController extends Controller
{
    public function index(Request $request, $type = null)
    {
        $sort_search = null;
        $from_warehouse_id = null;
        $to_warehouse_id = null;
        $from_date = null;
        $to_date = null;
        $export_type = $request->type;

        $query = CashTransfer::query();

        if (Auth::user()->user_type != 'admin') {
            $warehousearray = getWearhouseBuUserId(auth()->user()->id);
            $query->where(function($q) use ($warehousearray) {
                $q->whereIn('from_warehouse_id', $warehousearray)
                  ->orWhereIn('to_warehouse_id', $warehousearray);
            });
        }

        // Search filter
        if ($request->has('search')) {
            $sort_search = $request->search;
            $query->where(function($q) use ($sort_search) {
                $q->where('voucher_no', 'like', '%' . $sort_search . '%')
                  ->orWhere('remarks', 'like', '%' . $sort_search . '%');
            });
        }

        // From Warehouse filter
        if ($request->has('from_warehouse_id') && $request->from_warehouse_id != '') {
            $from_warehouse_id = $request->from_warehouse_id;
            $query->where('from_warehouse_id', $from_warehouse_id);
        }

        // To Warehouse filter
        if ($request->has('to_warehouse_id') && $request->to_warehouse_id != '') {
            $to_warehouse_id = $request->to_warehouse_id;
            $query->where('to_warehouse_id', $to_warehouse_id);
        }

        // Date filter
        if ($request->has('from_date') && $request->from_date != '') {
            $from_date = $request->from_date;
            $query->whereDate('voucher_date', '>=', $from_date);
        }

        if ($request->has('to_date') && $request->to_date != '') {
            $to_date = $request->to_date;
            $query->whereDate('voucher_date', '<=', $to_date);
        }

        // Get warehouse names for export
        $from_warehouse_name = null;
        $to_warehouse_name = null;
        if ($from_warehouse_id) {
            $warehouse = Warehouse::find($from_warehouse_id);
            $from_warehouse_name = $warehouse ? $warehouse->name : null;
        }
        if ($to_warehouse_id) {
            $warehouse = Warehouse::find($to_warehouse_id);
            $to_warehouse_name = $warehouse ? $warehouse->name : null;
        }

        // Handle Excel/PDF export
        if ($export_type == 'excel' || $export_type == 'pdf') {
            $transfers = $query->orderBy('voucher_date', 'desc')->get();
            
            $data = [
                'transfers' => $transfers,
                'from_warehouse_name' => $from_warehouse_name,
                'to_warehouse_name' => $to_warehouse_name,
                'from_date' => $from_date,
                'to_date' => $to_date,
            ];

            if ($export_type == 'excel') {
                return Excel::download(new CashTransferExport($data), 'CashTransfer_' . date('Y-m-d') . '.xlsx');
            } elseif ($export_type == 'pdf') {
                return view('backend.accounts.cash_transfer.pdf', $data);
            }
        }

        $transfers = $query->orderBy('voucher_date', 'desc')->paginate(15);
        $warehouses = Warehouse::get();
        
        return view('backend.accounts.cash_transfer.index', compact('transfers', 'warehouses', 'sort_search', 'from_warehouse_id', 'to_warehouse_id', 'from_date', 'to_date'));
    }

    public function create()
    {
        $crcc = AccCoa::where('head_level', 4)
            ->where('is_active', 1)
            ->where(function($query) {
                $query->where('is_cash_nature', 1);
            })
            ->orderBy('head_name')
            ->get();

        $crcc2 = AccCoa::where('head_level', 4)
            ->where('is_active', 1)
            ->where(function($query) {
                $query->where('is_bank_nature', 1);
            })
            ->orderBy('head_name')
            ->get();

        return view('backend.accounts.cash_transfer.create', compact('crcc', 'crcc2'));
    }

    public function store(Request $request)
    {
        $fyear = get_financial_year();
        $narration = "Cash Transfer Voucher";
        
        // Determine voucher type first
        if($request->txtVNo == 'Cash to Cash'){
            $voucher_type = 'WCTC';
        } else {
            $voucher_type = 'WCTB';
        }
        
        // Get maxid for specific voucher type (WCTC or WCTB)
        $maxid = get_max_field_number('id', 'cash_transfers', 'voucher_type', $voucher_type, 'voucher_no');
        $voucher_no = $voucher_type . "-" . ($maxid + 1);

        $transfer = new CashTransfer;

        $transfer->fyear = $fyear;
        $transfer->from_warehouse_id = $request->from_warehouse;
        $transfer->to_warehouse_id = $request->to_warehouse;
        $transfer->voucher_type = $voucher_type;
        $transfer->voucher_no = $voucher_no;

        $transfer->narration = $narration;
        $transfer->ledger_comment = $request->txtRemarks;
        $transfer->voucher_date = date('Y-m-d', strtotime($request->dtpDate));
        $transfer->from_coa_id = $request->cmbCredit;
        $transfer->to_coa_id = $request->cmbDebit;
        $transfer->amount = $request->txtAmount;
        $transfer->created_by = Auth::user()->id;
        $transfer->remarks = $request->txtRemarks;

        $transfer->save();

        flash(translate('Cash Transfer has been saved successfully!'))->success();
        return redirect()->route('cash-transfers.index');
    }

    public function store_old(Request $request)
    {
        $maxid = get_max_field_number('id', 'cash_transfers', 'voucher_type', 'WCT', 'voucher_no');
        $voucher_no = "WCT-" . ($maxid + 1);

        $fyear      = get_financial_year();
        $narration  = "Cash Transfer Voucher";
        $comment    = "Cash Transfer for Warehouse";

        $transfer = new CashTransfer;

        $transfer->fyear = $fyear;
        $transfer->from_warehouse_id = $request->from_warehouse;
        $transfer->from_warehouse_id = $request->from_warehouse;
        $transfer->to_warehouse_id = $request->to_warehouse;
        $transfer->voucher_no = $voucher_no;
        $transfer->voucher_type = 'WCT';
        $transfer->narration = $narration;
        $transfer->ledger_comment = $comment;
        $transfer->voucher_date = date('Y-m-d', strtotime($request->dtpDate));
        $transfer->coa_id = $request->cmbCredit;
        $transfer->amount = $request->txtAmount;
        $transfer->created_by = Auth::user()->id;

        $transfer->save();

        flash(translate('Cash Transfer has been saved successfully!'))->success();
        return redirect()->route('cash-transfers.index');
    }

    public function show($id)
    {
        $wearhouses = Warehouse::get();
        $transfer  = CashTransfer::findOrFail($id);
        return view('backend.accounts.cash_transfer.show', compact('transfer', 'wearhouses'));
    }

    public function edit(Request $request, $id)
    {
        $transfer  = CashTransfer::findOrFail($id);

        // Cash accounts
        $crcc = AccCoa::where('head_level', 4)
            ->where('is_active', 1)
            ->where(function($query) {
                $query->where('is_cash_nature', 1);
            })
            ->orderBy('head_name')
            ->get();

        // Bank accounts
        $crcc2 = AccCoa::where('head_level', 4)
            ->where('is_active', 1)
            ->where(function($query) {
                $query->where('is_bank_nature', 1);
            })
            ->orderBy('head_name')
            ->get();

        return view('backend.accounts.cash_transfer.edit', compact('crcc', 'crcc2', 'transfer'));
    }

    public function update(Request $request, $id)
    {
        $narration = "Cash Transfer Voucher";
        
        // Determine voucher type
        if($request->txtVNo == 'Cash to Cash'){
            $voucher_type = 'WCTC';
        } else {
            $voucher_type = 'WCTB';
        }

        $transfer = CashTransfer::findOrFail($id);
        
        // Check if voucher type has changed
        if($transfer->voucher_type != $voucher_type) {
            // Generate new voucher number for the new type
            $maxid = get_max_field_number('id', 'cash_transfers', 'voucher_type', $voucher_type, 'voucher_no');
            $voucher_no = $voucher_type . "-" . ($maxid + 1);
            $transfer->voucher_no = $voucher_no;
        }
        
        $transfer->from_warehouse_id = $request->from_warehouse;
        $transfer->to_warehouse_id = $request->to_warehouse;
        $transfer->voucher_type = $voucher_type;
        $transfer->narration = $narration;
        $transfer->ledger_comment = $request->txtRemarks;
        $transfer->voucher_date = date('Y-m-d', strtotime($request->dtpDate));
        $transfer->from_coa_id = $request->cmbCredit;
        $transfer->to_coa_id = $request->cmbDebit;
        $transfer->amount = $request->txtAmount;
        $transfer->updated_by = Auth::user()->id;
        $transfer->remarks = $request->txtRemarks;
        $transfer->save();

        flash(translate('Cash Transfer has been updated successfully!'))->success();
        return redirect()->route('cash-transfers.index');
    }

    public function destroy($id)
    {
        if(CashTransfer::destroy($id)) {
            flash(translate('Transfer has been deleted successfully'))->success();
            return redirect()->route('cash-transfers.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Approve the specified resource in storage.
     */
    public function approve($voucher_no, $action)
    {
        $transfer = CashTransfer::where('voucher_no', $voucher_no)->first();

        $approved_by = Auth::user()->id;
        $approved_at = date('Y-m-d H:i:s');

        $action = ($action == 'active' ? 1 : 0);

        $transfer_update = CashTransfer::where('voucher_no', $voucher_no)
            ->update([
                'voucher_no' => $voucher_no,
                'is_approved' => $action,
                'approved_by' => $approved_by,
                'approved_at' => $approved_at,
                'status' => $action,
            ]);

        $insert_cash_transfer_journal = insert_cash_transfer_journal($transfer->id);

        if ($insert_cash_transfer_journal) {
            autoapprove($transfer->id);
        }

        if ($transfer_update > 0) {
            flash(translate('Cash Transfer has been approved successfully'))->success();
            return back();
        } else {
            flash(translate('Please try again'))->error();
            return back();
        }
    }

    /**
     * Reverse Voucher the specified resource in storage.
     */
    public function reverse($voucher_no)
    {
        $finyear = get_financial_year();
        if ($finyear != '') {
            $transfers = CashTransfer::where('voucher_no', $voucher_no)->get();
            $deleted = AccTransaction::where('voucher_no', $voucher_no)->delete();

            $updated_by = Auth::user()->id;
            $updated_date = date('Y-m-d H:i:s');
            $route_name = Route::currentRouteName();

            $updatearray = array (
                'is_approved' => 0,
                'updated_by' => $updated_by,
                'updated_at' => $updated_date,
                'approved_by' => null,
                'approved_at' => null,
                'status' => 0,
            );

            if ($deleted) {
                $transfer_update = CashTransfer::where('voucher_no', $voucher_no)->update($updatearray);

                if ($transfer_update) {
                    if ($transfers) {
                        foreach ($transfers as $transfer) {
                            $store = store_transaction_summary($transfer->coa_id, $transfer->from_warehouse_id, $transfer->voucher_date);
                            $store2 = store_transaction_summary($transfer->coa_id, $transfer->to_warehouse_id, $transfer->voucher_date);

                            add_activity_log("reverse_cash_transfer", "reverse", $transfer->id, "acc_vouchers", $route_name, 6, $transfer_update);
                        }

                        if ($store && $store2) {
                            flash(translate('Cash Transfer has been reversed successfully'))->success();
                            return back();
                        } else {
                            flash(translate('Please try again'))->error();
                            return back();
                        }
                    }
                }
            }
            flash(translate('Please try again'))->error();
            return back();
        } else {
            flash(translate('Please Create Financial Year First'))->success();
            return back();
        }
    }
}
