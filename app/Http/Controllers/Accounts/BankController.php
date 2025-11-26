<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccCoa;
use App\Models\AccPredefineAccount;
use Illuminate\Http\Request;
use App\Models\Bank;
use Auth;
use Illuminate\Support\Facades\Route;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $banks = Bank::where('status', 1);

        if ($request->search != null) {
            $sort_search = $request->search;
            $banks = $banks->where('bank_name', 'like', '%'.$request->search.'%');
        }

        $banks = $banks->paginate(15);

        return view('backend.accounts.banks.index', compact('banks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.accounts.banks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $bank = new Bank();
        $bank->bank_name = $request->bank_name;
        $bank->ac_name = $request->ac_name;
        $bank->ac_number = $request->ac_number;
        $bank->branch = $request->branch;

        $route_name = Route::currentRouteName();

        if($bank->save()) {
            add_activity_log("bank", "create", $bank->id, "banks", $route_name, 1, $bank);

            $bank_code = AccPredefineAccount::first()->bank_code;
            $newdata = AccCoa::where('head_code', $bank_code)->first();
            $maxHeadCode = AccCoa::where('pre_head_code', $newdata->head_code)->max('head_code');

            $nid = $maxHeadCode;
            if ($nid > 0) {
                $HeadCode = $nid + 1;
            } else {
                $n = $nid + 1;
                if ($n / 10 < 1) {
                    $HeadCode = $bank_code . "0" . $n;
                } else {
                    $HeadCode = $bank_code . $n;
                }
            }

            $coa = new AccCoa();
            $coa->head_code = $HeadCode;
            $coa->pre_head_code = $newdata->head_code;
            $coa->head_name = $request->bank_name;
            $coa->pre_head_name = $newdata->head_name;
            $coa->head_level = $newdata->head_level + 1;
            $coa->is_active = 1;
            $coa->is_stock = 0;
            $coa->is_sub_type = 0;
            $coa->depreciation_rate = 0;
            $coa->head_type = 'A';
            $coa->is_budget = 0;
            $coa->is_cash_nature = 0;
            $coa->is_bank_nature = 1;
            $coa->is_fixed_asset_sch = 0;
            $coa->asset_code = null;
            $coa->dep_code = null;
            $coa->sub_type = 1;
            $coa->note_no = null;
            $coa->created_by = Auth::user()->id; 
            $coa->created_at = date('Y-m-d H:i:s');
            $coa->is_transaction = 0;
            $coa->is_gl = 0;
            $coa->bank_id = $bank->id;

            if ($coa->save()) {
                add_activity_log("coa_account", "create", $coa->id, "acc_coas", $route_name, 1, $coa);

                flash(translate('Bank has been save successfully'))->success();
                return redirect()->route('banks.index');
            }

            flash(translate('Bank has been save successfully'))->success();
            return redirect()->route('banks.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $bank = Bank::findOrFail($id);
        return view('backend.accounts.banks.edit', compact('bank'));
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $bank = Bank::findOrFail($id);
        $bank->bank_name = $request->bank_name;
        $bank->ac_name = $request->ac_name;
        $bank->ac_number = $request->ac_number;
        $bank->branch = $request->branch;
        
        $route_name = Route::currentRouteName();

        if($bank->save()) {
            add_activity_log("bank", "update", $bank->id, "banks", $route_name, 2, $bank);

            $bank_code = AccPredefineAccount::first()->bank_code;
            $newdata = AccCoa::where('bank_id', $id)->first();

            $coa = AccCoa::findOrFail($newdata->id);
            $coa->head_name = $request->bank_name;
            $coa->is_active = 1;
            $coa->is_stock = 0;
            $coa->is_sub_type = 0;
            $coa->depreciation_rate = 0;
            $coa->head_type = 'A';
            $coa->is_budget = 0;
            $coa->is_cash_nature = 0;
            $coa->is_bank_nature = 1;
            $coa->is_fixed_asset_sch = 0;
            $coa->asset_code = null;
            $coa->dep_code = null;
            $coa->sub_type = 1;
            $coa->note_no = null;
            $coa->updated_by = Auth::user()->id; 
            $coa->updated_at = date('Y-m-d H:i:s');
            $coa->is_transaction = 0;
            $coa->is_gl = 0;

            if ($coa->save()) {
                add_activity_log("coa_account", "update", $coa->id, "acc_coas", $route_name, 2, $coa);

                flash(translate('Bank has been updated successfully'))->success();
                return redirect()->route('banks.index');
            }

            flash(translate('Bank has been updated successfully'))->success();
            return redirect()->route('banks.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $bank = Bank::findOrFail($id);
        $bank->delete();

        flash(translate('Bank has been deleted successfully'))->success();
        return redirect()->route('banks.index');
    }
}
