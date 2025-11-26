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
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class DebitVoucherController extends Controller
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

        // $debit_vouchers = AccVoucher::where('voucher_type', 'DV')->with('warehouse')
        //     ->orderByRaw("LENGTH(voucher_no) DESC")
        //     ->orderByRaw("voucher_no DESC");

        if(Auth::user()->user_type == 'admin') {
            $debit_vouchers = AccVoucher::where('voucher_type', 'DV')
                ->with('warehouse')
                ->orderByRaw("LENGTH(voucher_no) DESC")
                ->orderByRaw("voucher_no DESC");
        } else {
            $warehousearray = getWearhouseBuUserId(auth()->user()->id);
            $debit_vouchers = AccVoucher::where('voucher_type', 'DV')
                ->whereIn('warehouse_id', $warehousearray)
                ->with('warehouse')
                ->orderByRaw("LENGTH(voucher_no) DESC")
                ->orderByRaw("voucher_no DESC");
        }
        
        if ($request->warehouse_id != null) {
            $warehouse_id = $request->warehouse_id;
            $debit_vouchers = $debit_vouchers->where('warehouse_id', $warehouse_id);
        }

        if ($request->date_range != null) {
            $date_range = $request->date_range;
            [$from_date, $to_date] = explode(' to ', $request->date_range);
            $from_date = date('Y-m-d', strtotime($from_date));
            $to_date = date('Y-m-d', strtotime($to_date));
            $debit_vouchers = $debit_vouchers->whereBetween('voucher_date', [$from_date, $to_date]);
        }

        if($request->party_id != null) {
            $party_id = $request->party_id;
            $debit_vouchers = $debit_vouchers->where('relational_value', $request->party_id);
        }

        if($request->head_code != null) {
            $head_code = $request->head_code;
            $debit_vouchers = $debit_vouchers->where(function ($query) use ($head_code) {
                $query->where('coa_id', $head_code)
                      ->orWhere('rev_code', $head_code);
            });
        }

        $debit_vouchers = $debit_vouchers->paginate(20);
        return view('backend.accounts.vouchers.debit.index', compact('debit_vouchers', 'parties', 'coas', 'date_range', 'party_id', 'head_code', 'warehouse_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $acc = AccCoa::where('is_bank_nature', 0)
            ->where('is_cash_nature', 0)
            ->where('head_level', 4)
            ->where('is_active', 1)
            ->orderBy('head_name')
            ->get();

        $voucher_no = AccTransaction::select("voucher_no")
            ->where('voucher_no', 'like', 'DV-%')
            ->orderBy('id', 'desc')
            ->get();

        $crcc = AccCoa::where('head_level', 4)
            ->where('is_active', 1)
            ->where(function($query) {
                $query->where('is_bank_nature', 1)
                    ->orWhere('is_cash_nature', 1);
            })
            ->orderBy('head_name')
            ->get();

        $rel_types = AccSubtype::where('status', 1)->where('id', '!=', 1)->get();

        return view('backend.accounts.vouchers.debit.create', compact('acc','voucher_no','crcc','rel_types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $finyear = $request->finyear;
        if ($finyear <= 0) {
            flash(translate('Please Create Financial Year First'))->warning();
            return redirect()->route('debit-vouchers.index');
        } else {
            $validator = Validator::make($request->all(), [
                'cmbCredit' => 'required|max:100',
                'txtRemarks' => 'nullable|max:200',
                'dtpDate' => 'required|date',
            ]);

            if ($validator->fails()) {
                flash(translate('Please try again'))->error();
                return redirect()->route('debit-vouchers.index');
            } else {
                $financialyears = FinancialYear::where('status', 1)->first();
                $date = date('Y-m-d', strtotime($request->dtpDate));
                $startfdate = $financialyears->start_date;
                $crdate = date("Y-m-d");
                if ($startfdate > $date) {
                    flash(translate('Please try again'))->error();
                    return redirect()->route('debit-vouchers.index');
                } else if ($date > $crdate || $date > $financialyears->end_date) {
                    flash(translate('Please try again'))->error();
                    return redirect()->route('debit-vouchers.index');
                } else {
                    $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'DV', 'voucher_no');
                    $voucher_no = "DV-" . ($maxid + 1);

                    $fyear = get_financial_year();

                    $rev_coa_id = $request->cmbCredit;
                    $voucher_date = date('Y-m-d', strtotime($request->dtpDate));
                    $cheque_no = $request->chequeNo;
                    $cheque_date = $request->chequeDate ? date('Y-m-d', strtotime($request->chequeDate)) : null;
                    $is_honours = $request->ishonours;
                    $narration = addslashes(trim($request->txtRemarks));

                    $is_subtypes = $request->isSubtype;
                    $subtypes = $request->subtype;
                    $coa_ids = $request->cmbCode;
                    $comments = $request->txtComment;
                    $debits = $request->txtAmount;
                    $warehouse = $request->warehouse;

                    $reltypes = $request->reltype;
                    $relvalues = $request->relvalue;

                    $created_by = Auth::user()->id;
                    $created_at = date('Y-m-d H:i:s');

                    $route_name = Route::currentRouteName();

                    for ($i = 0; $i < count($coa_ids); $i++) {
                        $coa_id = $coa_ids[$i];
                        
                        $debit_amnt = $debits[$i];
                        $is_subtype = $is_subtypes[$i];
                        $comment = $comments[$i];

                        $relational_type = $reltypes[$i];
                        if($relational_type == null) {
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

                        $debit = new AccVoucher();
                        $debit->fyear = $fyear;
                        $debit->voucher_no = $voucher_no;
                        $debit->voucher_type = 'DV';
                        $debit->reference_no = $refno;
                        $debit->voucher_date = $voucher_date;
                        $debit->coa_id = $coa_id;
                        $debit->narration = $narration;
                        $debit->cheque_no = $cheque_no;
                        $debit->cheque_date = $cheque_date;
                        $debit->is_honour = $is_honour;
                        $debit->ledger_comment = $comment;
                        $debit->debit = $debit_amnt;
                        $debit->credit = 0.00;
                        $debit->rev_code = $rev_coa_id;
                        $debit->sub_type = $is_subtype;
                        $debit->sub_code = $subcode;
                        $debit->relational_type = $relational_type;
                        $debit->relational_value = $relational_value;
                        $debit->sub_code = $subcode;
                        $debit->warehouse_id = $warehouse;
                        $debit->is_approved = 0;
                        $debit->created_by = $created_by;
                        $debit->created_at = $created_at;
                        $debit->status = 0;
                        $debit->save();

                        add_activity_log("debit_voucher", "create", $debit->id, "acc_vouchers", $route_name, 1, $debit);
                    }

                    flash(translate('Debit voucher has saved Successfully'))->success();
                    return redirect()->route('debit-vouchers.index');
                }
            }
        } 
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $debit = AccVoucher::findOrFail($id);

        return view('backend.accounts.vouchers.debit.show', compact('debit'));
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
            ->where(function($query) {
                $query->where('is_bank_nature', 1)
                    ->orWhere('is_cash_nature', 1);
            })
            ->orderBy('head_name')
            ->get();

        $debit = AccVoucher::findOrFail($id);

        $rel_types = AccSubtype::where('status', 1)->where('id', '!=', 1)->get();

        return view('backend.accounts.vouchers.debit.edit', compact('acc','crcc','debit','rel_types'));
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $finyear = $request->finyear;
        if ($finyear <= 0) {
            flash(translate('Please Create Financial Year First'))->warning();
            return redirect()->route('debit-vouchers.index');
        } else {
            $validator = Validator::make($request->all(), [
                'cmbCredit' => 'required|max:100',
                'dtpDate' => 'required|date',
            ]);

            if ($validator->fails()) {
                flash(translate('Please try again'))->error();
                return redirect()->route('debit-vouchers.index');
            } else {
                $voucher_no = addslashes(trim($request->txtVNo));

                DB::beginTransaction();
                try {
                    AccVoucher::where('voucher_no', $voucher_no)->delete();

                    $fyear = get_financial_year();

                    $rev_coa_id = $request->cmbCredit;
                    $voucher_date = date('Y-m-d', strtotime($request->dtpDate));
                    $cheque_no = $request->chequeNo;
                    $cheque_date = $request->chequeDate ? date('Y-m-d', strtotime($request->chequeDate)) : null;
                    $is_honours = $request->ishonours;
                    $narration = addslashes(trim($request->txtRemarks));

                    $is_subtypes = $request->isSubtype;
                    $subtypes = $request->subtype;
                    $coa_ids = $request->cmbCode;
                    $comments = $request->txtComment;
                    $debits = $request->txtAmount;
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
                        
                        $debit_amnt = $debits[$i];
                        $is_subtype = $is_subtypes[$i];
                        $comment = $comments[$i];

                        $relational_type = $reltypes[$i];
                        if($relational_type == null) {
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

                        $debit = new AccVoucher();
                        $debit->fyear = $fyear;
                        $debit->voucher_no = $voucher_no;
                        $debit->voucher_type = 'DV';
                        $debit->reference_no = $refno;
                        $debit->voucher_date = $voucher_date;
                        $debit->coa_id = $coa_id;
                        $debit->narration = $narration;
                        $debit->cheque_no = $cheque_no;
                        $debit->cheque_date = $cheque_date;
                        $debit->is_honour = $is_honour;
                        $debit->ledger_comment = $comment;
                        $debit->debit = $debit_amnt;
                        $debit->credit = 0.00;
                        $debit->rev_code = $rev_coa_id;
                        $debit->sub_type = $is_subtype;
                        $debit->sub_code = $subcode;
                        $debit->relational_type = $relational_type;
                        $debit->relational_value = $relational_value;
                        $debit->warehouse_id = $warehouse;
                        $debit->is_approved = 0;
                        $debit->created_by = $created_by;
                        $debit->created_at = $created_at;
                        $debit->updated_by = $updated_by;
                        $debit->updated_at = $updated_at;
                        $debit->status = 0;
                        $debit->save();

                        add_activity_log("debit_voucher", "update", $debit->id, "acc_vouchers", $route_name, 2, $debit);
                    }
                    DB::commit();

                    flash(translate('Debit voucher has updated successfully'))->success();
                    return redirect()->route('debit-vouchers.index');
                } catch (\Exception $e) {
                    DB::rollBack();

                    flash(translate('An error occurred: '. $e->getMessage()))->error();
                    return redirect()->route('debit-vouchers.index');
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($voucher_no)
    {
        if(AccVoucher::where('voucher_no', $voucher_no)->delete()) {
            flash(translate('Debit voucher has been deleted successfully'))->success();
            return redirect()->route('debit-vouchers.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function print($id)
    {
        $debit = AccVoucher::findOrFail($id);

        return view('backend.accounts.vouchers.debit.print', compact('debit'));
    }
}
