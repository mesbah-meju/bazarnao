<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccTransaction;
use App\Models\AccVoucher;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;


class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $warehouse = null;
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $voucher_no = null;

        if (Auth::user()->user_type == 'admin') {
            $vouchers = AccVoucher::select('*', DB::raw('SUM(debit) as debit_amnt'), DB::raw('SUM(credit) as credit_amnt'))
                ->where('is_approved', 0)
                ->whereIn('voucher_type', ['DV', 'CV', 'CT', 'JV'])
                ->orderByRaw("LENGTH(voucher_no) DESC")
                ->orderByRaw("voucher_no DESC")
                ->groupBy('voucher_no');
        } else {
            $vouchers = AccVoucher::select('*', DB::raw('SUM(debit) as debit_amnt'), DB::raw('SUM(credit) as credit_amnt'))
                ->where('is_approved', 0)
                ->whereIn('voucher_type', ['DV', 'CV', 'CT', 'JV'])
                ->whereIn('warehouse_id', $warehousearray)
                ->orderByRaw("LENGTH(voucher_no) DESC")
                ->orderByRaw("voucher_no DESC")
                ->groupBy('voucher_no');
        }

        if ($request->voucher_no != null) {
            $voucher_no = $request->voucher_no;
            $vouchers = $vouchers->where('voucher_no', 'like', '%' . $voucher_no . '%');
        }

        if ($request->warehouse != null) {
            $warehouse = $request->warehouse;
            $vouchers = $vouchers->where('warehouse_id', $warehouse);
        }

        if ($request->search != null) {
            $sort_search = $request->search;
            $vouchers = $vouchers->where('fyear', 'like', '%' . $request->search . '%');
        }

        $warehouses = Warehouse::whereIn('id', $warehousearray)->get();
        $vouchers = $vouchers->paginate(30);
        return view('backend.accounts.vouchers.index', compact('vouchers', 'sort_search', 'warehouse', 'warehouses', 'voucher_no'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $voucher = AccVoucher::findOrFail($id);

        return view('backend.accounts.vouchers.journal.show', compact('journal'));
    }

    /**
     * Approve Voucher the specified resource in storage.
     */
    public function approve($voucher_no, $action): JsonResponse
    {
        $vouchers = AccVoucher::where('voucher_no', $voucher_no)->get();
        $approved_by = Auth::user()->id;
        $approved_date = now();
        $route_name = Route::currentRouteName();

        if ($vouchers->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No vouchers found for approval']);
        }

        DB::beginTransaction();
        try {
            foreach ($vouchers as $voucher) {
                $transaction = new AccTransaction();
                $transaction->voucher_id = $voucher->id;
                $transaction->fyear = $voucher->fyear;
                $transaction->voucher_no = $voucher->voucher_no;
                $transaction->voucher_type = $voucher->voucher_type;
                $transaction->reference_no = $voucher->reference_no;
                $transaction->voucher_date = $voucher->voucher_date;
                $transaction->coa_id = $voucher->coa_id;
                $transaction->narration = $voucher->narration;
                $transaction->cheque_no = $voucher->cheque_no ?? '';
                $transaction->cheque_date = $voucher->cheque_date;
                $transaction->is_honour = $voucher->is_honour;
                $transaction->ledger_comment = $voucher->ledger_comment;
                $transaction->debit = $voucher->debit;
                $transaction->credit = $voucher->credit;
                $transaction->store_id = 0;
                $transaction->is_posted = 1;
                $transaction->rev_code = $voucher->rev_code;
                $transaction->sub_type = $voucher->sub_type;
                $transaction->sub_code = $voucher->sub_code;
                $transaction->relational_type = $voucher->relational_type;
                $transaction->relational_value = $voucher->relational_value;
                $transaction->is_approved = 1;
                $transaction->warehouse_id = $voucher->warehouse_id;
                $transaction->created_by = $approved_by;
                $transaction->created_at = $approved_date;

                if (!$transaction->save()) {
                    throw new \Exception("Transaction save failed.");
                }

                add_activity_log("approved_voucher_transaction", "create", $transaction->id, "acc_transaction", $route_name, 1, $transaction);

                $rev_transaction = new AccTransaction();
                $rev_transaction->voucher_id = $voucher->id;
                $rev_transaction->fyear = $voucher->fyear;
                $rev_transaction->voucher_no = $voucher->voucher_no;
                $rev_transaction->voucher_type = $voucher->voucher_type;
                $rev_transaction->reference_no = $voucher->reference_no;
                $rev_transaction->voucher_date = $voucher->voucher_date;
                $rev_transaction->coa_id = $voucher->rev_code;
                $rev_transaction->narration = $voucher->narration;
                $rev_transaction->cheque_no = $voucher->cheque_no ?? '';
                $rev_transaction->cheque_date = $voucher->cheque_date;
                $rev_transaction->is_honour = $voucher->is_honour;
                $rev_transaction->ledger_comment = $voucher->ledger_comment;
                $rev_transaction->debit = $voucher->credit;
                $rev_transaction->credit = $voucher->debit;
                $rev_transaction->store_id = 0;
                $rev_transaction->is_posted = 1;
                $rev_transaction->rev_code = $voucher->coa_id;
                $rev_transaction->sub_type = $voucher->sub_type;
                $rev_transaction->sub_code = $voucher->sub_code;
                $rev_transaction->relational_type = $voucher->relational_type;
                $rev_transaction->relational_value = $voucher->relational_value;
                $rev_transaction->is_approved = 1;
                $rev_transaction->warehouse_id = $voucher->warehouse_id;
                $rev_transaction->created_by = $approved_by;
                $rev_transaction->created_at = $approved_date;

                if (!$rev_transaction->save()) {
                    throw new \Exception("Reverse transaction save failed.");
                }

                add_activity_log("approved_voucher_reverse_transaction", "create", $rev_transaction->id, "acc_transaction", $route_name, 1, $rev_transaction);

                store_transaction_summary($voucher->coa_id, $voucher->warehouse_id, $voucher->voucher_date);
                store_transaction_summary($voucher->rev_code, $voucher->warehouse_id, $voucher->voucher_date);
            }

            $status_flag = ($action === 'active') ? 1 : 0;

            $voucher_update = AccVoucher::where('voucher_no', $voucher_no)
                ->update([
                    'is_approved' => $status_flag,
                    'approved_by' => $approved_by,
                    'approved_at' => $approved_date,
                    'status' => $status_flag,
                ]);

            if ($voucher_update === 0) {
                throw new \Exception("Voucher update failed.");
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Voucher approved successfully',
                'voucher_no' => $voucher_no,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Approval failed: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Reverse Voucher the specified resource in storage.
     */
    public function reverse($voucher_no)
    {
        $finyear = get_financial_year();
        if ($finyear != '') {
            $vouchers = AccVoucher::where('voucher_no', $voucher_no)->get();
            $deleted = AccTransaction::where('voucher_no', $voucher_no)->delete();

            $updated_by = Auth::user()->id;
            $updated_date = date('Y-m-d H:i:s');
            $route_name = Route::currentRouteName();

            $updatearray = array(
                'is_approved' => 0,
                'updated_by' => $updated_by,
                'updated_at' => $updated_date,
                'approved_by' => null,
                'approved_at' => null,
                'status' => 0,
            );

            if ($deleted) {
                $voucher_update = AccVoucher::where('voucher_no', $voucher_no)->update($updatearray);

                if ($voucher_update) {
                    if ($vouchers) {
                        foreach ($vouchers as $voucher) {
                            $store = store_transaction_summary($voucher->coa_id, $voucher->warehouse_id, $voucher->voucher_date);
                            $store2 = store_transaction_summary($voucher->rev_code, $voucher->warehouse_id, $voucher->voucher_date);

                            add_activity_log("reverse_vaucher", "reverse", $voucher->id, "acc_vouchers", $route_name, 6, $voucher_update);
                        }

                        if ($store && $store2) {
                            flash(translate('Voucher has been reversed successfully'))->success();
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
