<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccCoa;
use Illuminate\Http\Request;
use App\Models\AccOpeningBalance;
use App\Models\AccSubcode;
use App\Models\AccTransaction;
use App\Models\AccVoucher;
use App\Models\FinancialYear;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ContraVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $head_code = null;
        $date_range = null;
        $warehouse_id = null;

        $coas = AccCoa::where('head_level', 4)
            ->where('is_active', 1)
            ->where(function ($query) {
                $query->where('is_bank_nature', 1)
                    ->orWhere('is_cash_nature', 1);
            })
            ->orderBy('head_name')
            ->get();

        if (Auth::user()->user_type == 'admin') {
            $contra_vouchers = AccVoucher::where('voucher_type', 'CT')
                ->with('warehouse')
                ->orderByRaw("LENGTH(voucher_no) DESC")
                ->orderByRaw("voucher_no DESC");
        } else {
            $warehousearray = getWearhouseBuUserId(auth()->user()->id);
            $contra_vouchers = AccVoucher::where('voucher_type', 'CT')
                ->whereIn('warehouse_id', $warehousearray)
                ->with('warehouse')
                ->orderByRaw("LENGTH(voucher_no) DESC")
                ->orderByRaw("voucher_no DESC");
        }

        if ($request->warehouse_id != null) {
            $warehouse_id = $request->warehouse_id;
            $contra_vouchers = $contra_vouchers->where('warehouse_id', $warehouse_id);
        }

        if ($request->date_range != null) {
            $date_range = $request->date_range;
            [$from_date, $to_date] = explode(' to ', $request->date_range);
            $from_date = date('Y-m-d', strtotime($from_date));
            $to_date = date('Y-m-d', strtotime($to_date));
            $contra_vouchers = $contra_vouchers->whereBetween('voucher_date', [$from_date, $to_date]);
        }

        if ($request->head_code != null) {
            $head_code = $request->head_code;
            $contra_vouchers = $contra_vouchers->where(function ($query) use ($head_code) {
                $query->where('coa_id', $head_code)
                    ->orWhere('rev_code', $head_code);
            });
        }

        $contra_vouchers = $contra_vouchers->paginate(20);
        return view('backend.accounts.vouchers.contra.index', compact('contra_vouchers', 'coas', 'date_range', 'head_code', 'warehouse_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $acc = AccCoa::where('head_level', 4)
            ->where('is_active', 1)
            ->where(function ($query) {
                $query->where('is_bank_nature', 1)
                    ->orWhere('is_cash_nature', 1);
            })
            ->orderBy('head_name')
            ->get();

        $voucher_no = AccTransaction::select("voucher_no")
            ->where('voucher_no', 'like', 'CV-%')
            ->orderBy('ID', 'desc')
            ->get();

        return view('backend.accounts.vouchers.contra.create', compact('acc', 'voucher_no'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $finyear = $request->finyear;
        if ($finyear <= 0) {
            flash(translate('Please Create Financial Year First'))->warning();
            return redirect()->route('contra-vouchers.index');
        } else {
            $validator = Validator::make($request->all(), [
                'cmbDebit' => 'required|max:100',
                'txtRemarks' => 'nullable|max:200',
                'dtpDate' => 'required|date',
            ]);

            if ($validator->fails()) {
                flash(translate('Please try again'))->error();
                return redirect()->route('contra-vouchers.index');
            } else {
                $financialyears = FinancialYear::where('status', 1)->first();
                $date = date('Y-m-d', strtotime($request->dtpDate));
                $startfdate = $financialyears->start_date;
                $crdate = date("Y-m-d");
                if ($startfdate > $date) {
                    flash(translate('Please try again'))->error();
                    return redirect()->route('contra-vouchers.index');
                } else if ($date > $crdate || $date > $financialyears->end_date) {
                    flash(translate('Please try again'))->error();
                    return redirect()->route('contra-vouchers.index');
                } else {
                    $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'CT', 'voucher_no');
                    $voucher_no = "CT-" . ($maxid + 1);

                    $fyear = get_financial_year();

                    $rev_coa_id = $request->cmbDebit;
                    $voucher_date = date('Y-m-d', strtotime($request->dtpDate));
                    $narration = addslashes(trim($request->txtRemarks));

                    $coa_ids = $request->cmbCode;
                    $comments = $request->txtComment;
                    $debits = $request->txtAmount;
                    $credits = $request->txtAmountcr;
                    $warehouse = $request->warehouse;

                    $created_by = Auth::user()->id;
                    $created_at = date('Y-m-d H:i:s');

                    $route_name = Route::currentRouteName();

                    for ($i = 0; $i < count($coa_ids); $i++) {
                        $coa_id = $coa_ids[$i];
                        $debit = $debits[$i];
                        $credit = $credits[$i];
                        $comment = $comments[$i];

                        $contra_voucher = new AccVoucher();
                        $contra_voucher->fyear = $fyear;
                        $contra_voucher->voucher_no = $voucher_no;
                        $contra_voucher->voucher_type = 'CT';
                        $contra_voucher->reference_no = null;
                        $contra_voucher->voucher_date = $voucher_date;
                        $contra_voucher->coa_id = $coa_id;
                        $contra_voucher->narration = $narration;
                        $contra_voucher->cheque_no = null;
                        $contra_voucher->cheque_date = null;
                        $contra_voucher->is_honour = 0;
                        $contra_voucher->ledger_comment = $comment;
                        $contra_voucher->debit = $debit;
                        $contra_voucher->credit = $credit;
                        $contra_voucher->rev_code = $rev_coa_id;
                        $contra_voucher->sub_type = 1;
                        $contra_voucher->sub_code = null;
                        $contra_voucher->warehouse_id = $warehouse;
                        $contra_voucher->is_approved = 0;
                        $contra_voucher->created_by = $created_by;
                        $contra_voucher->created_at = $created_at;
                        $contra_voucher->status = 0;
                        $contra_voucher->save();

                        add_activity_log("contra_voucher", "create", $contra_voucher->id, "acc_vouchers", $route_name, 1, $contra_voucher);
                    }

                    flash(translate('Contra voucher has saved Successfully'))->success();
                    return redirect()->route('contra-vouchers.index');
                }
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $contra = AccVoucher::findOrFail($id);

        return view('backend.accounts.vouchers.contra.show', compact('contra'));
    }

    /**
     * Display the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $acc = AccCoa::where('head_level', 4)
            ->where('is_active', 1)
            ->where(function ($query) {
                $query->where('is_bank_nature', 1)
                    ->orWhere('is_cash_nature', 1);
            })
            ->orderBy('head_name')
            ->get();

        $contra = AccVoucher::findOrFail($id);

        return view('backend.accounts.vouchers.contra.edit', compact('acc', 'contra'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $finyear = $request->finyear;
        if ($finyear <= 0) {
            flash(translate('Please Create Financial Year First'))->warning();
            return redirect()->route('contra-vouchers.index');
        } else {
            $validator = Validator::make($request->all(), [
                'cmbDebit' => 'required|max:100',
                'dtpDate' => 'required|date',
            ]);

            if ($validator->fails()) {
                flash(translate('Please try again'))->error();
                return redirect()->route('contra-vouchers.index');
            } else {
                $voucher_no = addslashes(trim($request->txtVNo));

                DB::beginTransaction();
                try {
                    AccVoucher::where('voucher_no', $voucher_no)->delete();

                    $fyear = get_financial_year();
                    $rev_coa_id = $request->cmbDebit;
                    $voucher_date = date('Y-m-d', strtotime($request->dtpDate));
                    $narration = addslashes(trim($request->txtRemarks));

                    $coa_ids = $request->cmbCode;
                    $comments = $request->txtComment;
                    $debits = $request->txtAmount;
                    $credits = $request->txtAmountcr;
                    $warehouse = $request->warehouse;

                    $created_by = $request->CreateBy;
                    $created_at = $request->CreateDate;
                    $updated_by = Auth::user()->id;
                    $updated_at = date('Y-m-d H:i:s');

                    $route_name = Route::currentRouteName();

                    for ($i = 0; $i < count($coa_ids); $i++) {
                        $coa_id = $coa_ids[$i];
                        $debit = $debits[$i];
                        $credit = $credits[$i];
                        $comment = $comments[$i];

                        $contra = new AccVoucher();
                        $contra->fyear = $fyear;
                        $contra->voucher_no = $voucher_no;
                        $contra->voucher_type = 'CT';
                        $contra->reference_no = null;
                        $contra->voucher_date = $voucher_date;
                        $contra->coa_id = $coa_id;
                        $contra->narration = $narration;
                        $contra->cheque_no = null;
                        $contra->cheque_date = null;
                        $contra->is_honour = 0;
                        $contra->ledger_comment = $comment;
                        $contra->debit = $debit;
                        $contra->credit = $credit;
                        $contra->rev_code = $rev_coa_id;
                        $contra->sub_type = 1;
                        $contra->sub_code = null;
                        $contra->warehouse_id = $warehouse;;
                        $contra->is_approved = 0;
                        $contra->created_by = $created_by;
                        $contra->created_at = $created_at;
                        $contra->updated_by = $updated_by;
                        $contra->updated_at = $updated_at;
                        $contra->status = 0;
                        $contra->save();

                        add_activity_log("contra_voucher", "update", $contra->id, "acc_vouchers", $route_name, 2, $contra);
                    }
                    DB::commit();

                    flash(translate('Contra voucher has updated successfully'))->success();
                    return redirect()->route('contra-vouchers.index');
                } catch (\Exception $e) {
                    DB::rollBack();

                    flash(translate('An error occurred: ' . $e->getMessage()))->error();
                    return redirect()->route('contra-vouchers.index');
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($voucher_no)
    {
        if (AccVoucher::where('voucher_no', $voucher_no)->delete()) {
            flash(translate('Contra Voucher has been deleted successfully'))->success();
            return redirect()->route('contra-vouchers.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function print($id)
    {
        $contra = AccVoucher::findOrFail($id);

        return view('backend.accounts.vouchers.contra.print', compact('contra'));
    }
}
