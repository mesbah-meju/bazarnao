<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccCoa;
use Illuminate\Http\Request;
use App\Models\AccOpeningBalance;
use App\Models\AccSubcode;
use App\Models\AccSubtype;
use App\Models\AccTransaction;
use App\Models\AccVoucher;
use App\Models\FinancialYear;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class CreditVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $party_id = null;
        $head_code = null;
        $date_range = null;
        $warehouse_id = null;

        $parties = AccSubcode::where('status', 1)->get();

        $coas = AccCoa::where('head_level', 4)
            ->where('is_active', 1)
            ->orderBy('head_name')
            ->get();

        if (Auth::user()->user_type == 'admin') {
            $credit_vouchers = AccVoucher::where('voucher_type', 'CV')
                ->with('warehouse')
                ->orderByRaw("LENGTH(voucher_no) DESC")
                ->orderByRaw("voucher_no DESC");
        } else {
            $warehousearray = getWearhouseBuUserId(auth()->user()->id);
            $credit_vouchers = AccVoucher::where('voucher_type', 'CV')
                ->whereIn('warehouse_id', $warehousearray)
                ->with('warehouse')
                ->orderByRaw("LENGTH(voucher_no) DESC")
                ->orderByRaw("voucher_no DESC");
        }

        if ($request->warehouse_id != null) {
            $warehouse_id = $request->warehouse_id;
            $credit_vouchers = $credit_vouchers->where('warehouse_id', $warehouse_id);
        }

        if ($request->date_range != null) {
            $date_range = $request->date_range;
            [$from_date, $to_date] = explode(' to ', $request->date_range);
            $from_date = date('Y-m-d', strtotime($from_date));
            $to_date = date('Y-m-d', strtotime($to_date));
            $credit_vouchers = $credit_vouchers->whereBetween('voucher_date', [$from_date, $to_date]);
        }

        if ($request->party_id != null) {
            $party_id = $request->party_id;
            $credit_vouchers = $credit_vouchers->where('relational_value', $request->party_id);
        }

        if ($request->head_code != null) {
            $head_code = $request->head_code;
            $credit_vouchers = $credit_vouchers->where(function ($query) use ($head_code) {
                $query->where('coa_id', $head_code)
                    ->orWhere('rev_code', $head_code);
            });
        }

        $credit_vouchers = $credit_vouchers->paginate(30);
        return view('backend.accounts.vouchers.credit.index', compact('credit_vouchers', 'parties', 'coas', 'date_range', 'party_id', 'head_code', 'warehouse_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $acc = AccCoa::where('is_bank_nature', 0)
            ->where('is_cash_nature', 0)
            ->where('head_level', 4)
            ->where('is_active', 1)
            ->orderBy('head_name')
            ->get();

        $crcc = AccCoa::where('head_level', 4)
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

        $rel_types = AccSubtype::where('status', 1)->where('id', '!=', 1)->get();

        return view('backend.accounts.vouchers.credit.create', compact('acc', 'crcc', 'voucher_no', 'rel_types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $finyear = $request->finyear;
        if ($finyear <= 0) {
            flash(translate('Please Create Financial Year First'))->warning();
            return redirect()->route('credit-vouchers.index');
        } else {
            $validator = Validator::make($request->all(), [
                'cmbDebit' => 'required|max:100',
                'txtRemarks' => 'nullable|max:200',
                'dtpDate' => 'required|date',
            ]);

            if ($validator->fails()) {
                flash(translate('Please try again'))->error();
                return redirect()->route('credit-vouchers.index');
                // return redirect('credit_voucher')->withErrors($validator)->withInput();
            } else {
                $financialyears = FinancialYear::where('status', 1)->first();
                $date = date('Y-m-d', strtotime($request->dtpDate));
                $startfdate = $financialyears->start_date;
                $crdate = date("Y-m-d");
                if ($startfdate > $date) {
                    flash(translate('Please try again'))->error();
                    return redirect()->route('credit-vouchers.index');
                } else if ($date > $crdate || $date > $financialyears->end_date) {
                    flash(translate('Please try again'))->error();
                    return redirect()->route('credit-vouchers.index');
                } else {
                    $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'CV', 'voucher_no');
                    $voucher_no = "CV-" . ($maxid + 1);

                    $fyear = get_financial_year();

                    $rev_coa_id = $request->cmbDebit;
                    $voucher_date = date('Y-m-d', strtotime($request->dtpDate));
                    $cheque_no = $request->checkno;
                    $cheque_date = date('Y-m-d', strtotime($request->chequeDate));
                    $is_honours = $request->ishonours;
                    $narration = addslashes(trim($request->txtRemarks));

                    $is_subtypes = $request->isSubtype;
                    $subtypes = $request->subtype;
                    $coa_ids = $request->cmbCode;
                    $comments = $request->txtComment;
                    $credits = $request->txtAmount;
                    $warehouse = $request->warehouse;

                    $reltypes = $request->reltype;
                    $relvalues = $request->relvalue;

                    $created_by = Auth::user()->id;
                    $created_at = date('Y-m-d H:i:s');

                    $route_name = Route::currentRouteName();

                    for ($i = 0; $i < count($coa_ids); $i++) {
                        $coa_id = $coa_ids[$i];

                        $credit_amnt = $credits[$i];
                        $is_subtype = $is_subtypes[$i];
                        $comment = $comments[$i];

                        $relational_type = $reltypes[$i];
                        if ($relational_type == null) {
                            $relational_value = null;
                        } else {
                            $relational_value = $relvalues[$i];
                        }

                        if ($is_subtype != 1) {
                            $subcode = $subtypes[$i];
                            $refno = get_referance_no($subcode);
                        } else {
                            $subcode = null;
                            $refno = null;
                        }

                        if (isset($is_honours)) {
                            $is_honour = 1;
                        } else {
                            $is_honour = 0;
                        }

                        $credit = new AccVoucher();
                        $credit->fyear = $fyear;
                        $credit->voucher_no = $voucher_no;
                        $credit->voucher_type = 'CV';
                        $credit->reference_no = $refno;
                        $credit->voucher_date = $voucher_date;
                        $credit->coa_id = $coa_id;
                        $credit->narration = $narration;
                        $credit->cheque_no = $cheque_no;
                        $credit->cheque_date = $cheque_date;
                        $credit->is_honour = $is_honour;
                        $credit->ledger_comment = $comment;
                        $credit->debit = 0.00;
                        $credit->credit = $credit_amnt;
                        $credit->rev_code = $rev_coa_id;
                        $credit->sub_type = $is_subtype;
                        $credit->sub_code = $subcode;
                        $credit->relational_type = $relational_type;
                        $credit->relational_value = $relational_value;
                        $credit->warehouse_id = $warehouse;
                        $credit->is_approved = 0;
                        $credit->created_by = $created_by;
                        $credit->created_at = $created_at;
                        $credit->status = 0;
                        $credit->save();

                        add_activity_log("credit_voucher", "create", $credit->id, "acc_vouchers", $route_name, 1, $credit);
                    }

                    flash(translate('Credit voucher has saved Successfully'))->success();
                    return redirect()->route('credit-vouchers.index');
                }
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $credit = AccVoucher::findOrFail($id);

        return view('backend.accounts.vouchers.credit.show', compact('credit'));
    }

    /**
     * Display the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $acc = AccCoa::where('is_bank_nature', 0)
            ->where('is_cash_nature', 0)
            ->where('head_level', 4)
            ->where('is_active', 1)
            ->orderBy('head_name')
            ->get();

        $crcc = AccCoa::where('head_level', 4)
            ->where('is_active', 1)
            ->where(function ($query) {
                $query->where('is_bank_nature', 1)
                    ->orWhere('is_cash_nature', 1);
            })
            ->orderBy('head_name')
            ->get();

        $credit = AccVoucher::findOrFail($id);

        $rel_types = AccSubtype::where('status', 1)->where('id', '!=', 1)->get();

        return view('backend.accounts.vouchers.credit.edit', compact('acc', 'crcc', 'credit', 'rel_types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $finyear = $request->finyear;
        if ($finyear <= 0) {
            flash(translate('Please Create Financial Year First'))->warning();
            return redirect()->route('credit-vouchers.index');
        } else {
            $validator = Validator::make($request->all(), [
                'cmbDebit' => 'required|max:100',
                'dtpDate' => 'required|date',
            ]);

            if ($validator->fails()) {
                flash(translate('Please try again'))->error();
                return redirect()->route('credit-vouchers.index');
            } else {
                $voucher_no = addslashes(trim($request->txtVNo));

                DB::beginTransaction();
                try {
                    AccVoucher::where('voucher_no', $voucher_no)->delete();

                    $fyear = get_financial_year();
                    $rev_coa_id = $request->cmbDebit;
                    $voucher_date = date('Y-m-d', strtotime($request->dtpDate));
                    $cheque_no = $request->checkno;
                    $cheque_date = date('Y-m-d', strtotime($request->chequeDate));
                    $is_honours = $request->ishonours;
                    $narration = addslashes(trim($request->txtRemarks));

                    $is_subtypes = $request->isSubtype;
                    $subtypes = $request->subtype;
                    $coa_ids = $request->cmbCode;
                    $comments = $request->txtComment;
                    $credits = $request->txtAmount;
                    $warehouse = $request->warehouse;

                    $reltypes = $request->reltype;
                    $relvalues = $request->relvalue;

                    $created_by = $request->CreateBy;
                    $created_at = $request->CreateDate;
                    $updated_by = Auth::user()->id;
                    $updated_at = date('Y-m-d H:i:s');

                    $route_name = Route::currentRouteName();

                    for ($i = 0; $i < count($coa_ids); $i++) {
                        $coa_id = $coa_ids[$i];

                        $credit_amnt = $credits[$i];
                        $is_subtype = $is_subtypes[$i];
                        $comment = $comments[$i];

                        $relational_type = $reltypes[$i];
                        if ($relational_type == null) {
                            $relational_value = null;
                        } else {
                            $relational_value = $relvalues[$i];
                        }

                        if ($is_subtype != 1) {
                            $subcode = $subtypes[$i];
                            $refno = get_referance_no($subcode);
                        } else {
                            $subcode = null;
                            $refno = null;
                        }

                        if (isset($is_honours)) {
                            $is_honour = 1;
                        } else {
                            $is_honour = 0;
                        }

                        $credit = new AccVoucher();
                        $credit->fyear = $fyear;
                        $credit->voucher_no = $voucher_no;
                        $credit->voucher_type = 'CV';
                        $credit->reference_no = $refno;
                        $credit->voucher_date = $voucher_date;
                        $credit->coa_id = $coa_id;
                        $credit->narration = $narration;
                        $credit->cheque_no = $cheque_no;
                        $credit->cheque_date = $cheque_date;
                        $credit->is_honour = $is_honour;
                        $credit->ledger_comment = $comment;
                        $credit->debit = 0.00;
                        $credit->credit = $credit_amnt;
                        $credit->rev_code = $rev_coa_id;
                        $credit->sub_type = $is_subtype;
                        $credit->sub_code = $subcode;
                        $credit->relational_type = $relational_type;
                        $credit->relational_value = $relational_value;
                        $credit->warehouse_id = $warehouse;
                        $credit->is_approved = 0;
                        $credit->created_by = $created_by;
                        $credit->created_at = $created_at;
                        $credit->updated_by = $updated_by;
                        $credit->updated_at = $updated_at;
                        $credit->status = 0;
                        $credit->save();

                        add_activity_log("credit_voucher", "update", $credit->id, "acc_vouchers", $route_name, 2, $credit);
                    }
                    DB::commit();

                    flash(translate('Contra voucher has updated successfully'))->success();
                    return redirect()->route('credit-vouchers.index');
                } catch (\Exception $e) {
                    DB::rollBack();

                    flash(translate('An error occurred: ' . $e->getMessage()))->error();
                    return redirect()->route('credit-vouchers.index');
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
            flash(translate('Credit Voucher has been deleted successfully'))->success();
            return redirect()->route('credit-vouchers.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function print($id)
    {
        $credit = AccVoucher::findOrFail($id);

        return view('backend.accounts.vouchers.credit.print', compact('credit'));
    }
}
