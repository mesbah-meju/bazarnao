<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccCoa;
use App\Models\AccSubcode;
use App\Models\AccSubtype;
use Illuminate\Http\Request;
use App\Models\AccTransaction;
use App\Models\AccVoucher;
use App\Models\FinancialYear;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class JournalVoucherController extends Controller
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

        $coas = AccCoa::where('is_bank_nature', 0)
            ->where('is_cash_nature', 0)
            ->where('head_level', 4)
            ->where('is_active', 1)
            ->orderBy('head_name')
            ->get();

        if (Auth::user()->user_type == 'admin') {
            $journal_vouchers = AccVoucher::where('voucher_type', 'JV')
                ->with('warehouse')
                ->orderByRaw("LENGTH(voucher_no) DESC")
                ->orderByRaw("voucher_no DESC");
        } else {
            $warehousearray = getWearhouseBuUserId(auth()->user()->id);
            $journal_vouchers = AccVoucher::where('voucher_type', 'JV')
                ->whereIn('warehouse_id', $warehousearray)
                ->with('warehouse')
                ->orderByRaw("LENGTH(voucher_no) DESC")
                ->orderByRaw("voucher_no DESC");
        }

        if ($request->warehouse_id != null) {
            $warehouse_id = $request->warehouse_id;
            $journal_vouchers = $journal_vouchers->where('warehouse_id', $warehouse_id);
        }

        if ($request->date_range != null) {
            $date_range = $request->date_range;
            [$from_date, $to_date] = explode(' to ', $request->date_range);
            $from_date = date('Y-m-d', strtotime($from_date));
            $to_date = date('Y-m-d', strtotime($to_date));
            $journal_vouchers = $journal_vouchers->whereBetween('voucher_date', [$from_date, $to_date]);
        }

        if ($request->party_id != null) {
            $party_id = $request->party_id;
            $journal_vouchers = $journal_vouchers->where('relational_value', $request->party_id);
        }

        if ($request->head_code != null) {
            $head_code = $request->head_code;
            $journal_vouchers = $journal_vouchers->where(function ($query) use ($head_code) {
                $query->where('coa_id', $head_code)
                    ->orWhere('rev_code', $head_code);
            });
        }

        $journal_vouchers = $journal_vouchers->paginate(20);
        return view('backend.accounts.vouchers.journal.index', compact('journal_vouchers', 'parties', 'coas', 'date_range', 'party_id', 'head_code', 'warehouse_id'));
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

        $voucher_no = AccTransaction::select("voucher_no")
            ->where('voucher_no', 'like', 'JV-%')
            ->orderBy('ID', 'desc')
            ->get();

        $rel_types = AccSubtype::where('status', 1)->where('id', '!=', 1)->get();

        return view('backend.accounts.vouchers.journal.create', compact('acc', 'voucher_no', 'rel_types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $finyear = $request->finyear;
        if ($finyear <= 0) {
            flash(translate('Please Create Financial Year First'))->warning();
            return redirect()->route('journal-vouchers.index');
        } else {
            $validator = Validator::make($request->all(), [
                'cmbDebit' => 'required|max:100',
                'txtRemarks' => 'nullable|max:200',
            ]);

            if ($validator->fails()) {
                flash(translate('Please try again'))->error();
                return redirect()->route('journal-vouchers.index');
            } else {
                $financialyears = FinancialYear::where('status', 1)->first();
                $date = date('Y-m-d', strtotime($request->dtpDate));
                $startfdate = $financialyears->start_date;
                $crdate = date("Y-m-d");
                if ($startfdate > $date) {
                    flash(translate('Please try again'))->error();
                    return redirect()->route('journal-vouchers.index');
                } else if ($date > $crdate || $date > $financialyears->end_date) {
                    flash(translate('Please try again'))->error();
                    return redirect()->route('journal-vouchers.index');
                } else {
                    $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'JV', 'voucher_no');
                    $voucher_no = "JV-" . ($maxid + 1);

                    $fyear = get_financial_year();

                    $voucher_date = date('Y-m-d', strtotime($request->dtpDate));
                    $narration = addslashes(trim($request->txtRemarks));

                    $is_subtypes = $request->isSubtype;
                    $subtypes = $request->subtype;
                    $coa_ids = $request->cmbCode;
                    $rev_coa_ids = $request->cmbDebit;
                    $comments = $request->txtComment;
                    $debits = $request->txtAmount;
                    $credits = $request->txtAmountcr;
                    $warehouse = $request->warehouse;

                    $reltypes = $request->reltype;
                    $relvalues = $request->relvalue;

                    $created_by = Auth::user()->id;
                    $created_at = date('Y-m-d H:i:s');

                    $route_name = Route::currentRouteName();

                    for ($i = 0; $i < count($coa_ids); $i++) {
                        $coa_id = $coa_ids[$i];
                        $rev_coa_id = $rev_coa_ids[$i];
                        $debit = $debits[$i];
                        $credit = $credits[$i];
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

                        $journal_voucher = new AccVoucher();
                        $journal_voucher->fyear = $fyear;
                        $journal_voucher->voucher_no = $voucher_no;
                        $journal_voucher->voucher_type = 'JV';
                        $journal_voucher->reference_no = $refno;
                        $journal_voucher->voucher_date = $voucher_date;
                        $journal_voucher->coa_id = $coa_id;
                        $journal_voucher->narration = $narration;
                        $journal_voucher->ledger_comment = $comment;
                        $journal_voucher->debit = $debit;
                        $journal_voucher->credit = $credit;
                        $journal_voucher->rev_code = $rev_coa_id;
                        $journal_voucher->sub_type = $is_subtype;
                        $journal_voucher->sub_code = $subcode;
                        $journal_voucher->relational_type = $relational_type;
                        $journal_voucher->relational_value = $relational_value;
                        $journal_voucher->warehouse_id = $warehouse;
                        $journal_voucher->is_approved = 0;
                        $journal_voucher->created_by = $created_by;
                        $journal_voucher->created_at = $created_at;
                        $journal_voucher->status = 0;
                        $journal_voucher->save();

                        add_activity_log("journal_voucher", "create", $journal_voucher->id, "acc_vouchers", $route_name, 1, $journal_voucher);
                    }

                    flash(translate('Journal voucher has saved successfully'))->success();
                    return redirect()->route('journal-vouchers.index');
                }
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $journal = AccVoucher::findOrFail($id);

        return view('backend.accounts.vouchers.journal.show', compact('journal'));
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

        $journal = AccVoucher::findOrFail($id);

        $rel_types = AccSubtype::where('status', 1)->where('id', '!=', 1)->get();

        return view('backend.accounts.vouchers.journal.edit', compact('acc', 'journal', 'rel_types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $finyear = $request->finyear;
        if ($finyear <= 0) {
            flash(translate('Please Create Financial Year First'))->warning();
            return redirect()->route('journal-vouchers.index');
        } else {
            $validator = Validator::make($request->all(), [
                'cmbDebit' => 'required|max:100',
            ]);

            if ($validator->fails()) {
                flash(translate('Please try again'))->error();
                return redirect()->route('journal-vouchers.index');
            } else {
                $voucher_no = addslashes(trim($request->txtVNo));

                DB::beginTransaction();
                try {
                    AccVoucher::where('voucher_no', $voucher_no)->delete();

                    $fyear = get_financial_year();
                    $voucher_date = date('Y-m-d', strtotime($request->dtpDate));
                    $narration = addslashes(trim($request->txtRemarks));

                    $is_subtypes = $request->isSubtype;
                    $subtypes = $request->subtype;
                    $coa_ids = $request->cmbCode;
                    $rev_coa_ids = $request->cmbDebit;
                    $comments = $request->txtComment;
                    $debits = $request->txtAmount;
                    $credits = $request->txtAmountcr;
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
                        $rev_coa_id = $rev_coa_ids[$i];
                        $debit = $debits[$i];
                        $credit = $credits[$i];
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

                        $journal_voucher = new AccVoucher();
                        $journal_voucher->fyear = $fyear;
                        $journal_voucher->voucher_no = $voucher_no;
                        $journal_voucher->voucher_type = 'JV';
                        $journal_voucher->reference_no = $refno;
                        $journal_voucher->voucher_date = $voucher_date;
                        $journal_voucher->coa_id = $coa_id;
                        $journal_voucher->narration = $narration;
                        $journal_voucher->ledger_comment = $comment;
                        $journal_voucher->debit = $debit;
                        $journal_voucher->credit = $credit;
                        $journal_voucher->rev_code = $rev_coa_id;
                        $journal_voucher->sub_type = $is_subtype;
                        $journal_voucher->sub_code = $subcode;
                        $journal_voucher->relational_type = $relational_type;
                        $journal_voucher->relational_value = $relational_value;
                        $journal_voucher->warehouse_id = $warehouse;
                        $journal_voucher->is_approved = 0;
                        $journal_voucher->created_by = $created_by;
                        $journal_voucher->created_at = $created_at;
                        $journal_voucher->updated_by = $updated_by;
                        $journal_voucher->updated_at = $updated_at;
                        $journal_voucher->status = 0;
                        $journal_voucher->save();

                        add_activity_log("journal_voucher", "update", $journal_voucher->id, "acc_vouchers", $route_name, 2, $journal_voucher);
                    }

                    DB::commit();

                    flash(translate('Journal voucher has updated successfully'))->success();
                    return redirect()->route('journal-vouchers.index');
                } catch (\Exception $e) {
                    DB::rollBack();

                    flash(translate('An error occurred: ' . $e->getMessage()))->error();
                    return redirect()->route('journal-vouchers.index');
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
            flash(translate('Journal Voucher has been deleted successfully'))->success();
            return redirect()->route('journal-vouchers.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function print($id)
    {
        $journal = AccVoucher::findOrFail($id);

        return view('backend.accounts.vouchers.journal.print', compact('journal'));
    }
}
