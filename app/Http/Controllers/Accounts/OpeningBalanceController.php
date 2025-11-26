<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccCoa;
use Illuminate\Http\Request;
use App\Models\AccOpeningBalance;
use App\Models\AccSubcode;
use App\Models\AccSubtype;
use App\Models\FinancialYear;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class OpeningBalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort_search = null;

        if(Auth::user()->user_type == 'admin') {
            $opening_balances = AccOpeningBalance::orderBy('id', 'desc');
        } else {
            $warehousearray = getWearhouseBuUserId(auth()->user()->id);
            $opening_balances = AccOpeningBalance::orderBy('id', 'desc')->whereIn('warehouse_id', $warehousearray);
        }
        

        if ($request->search != null){
            $sort_search = $request->search;
            $opening_balances = $opening_balances->where('fyear', 'like', '%'.$request->search.'%');
        }

        $opening_balances = $opening_balances->paginate(30);
        return view('backend.accounts.opening_balances.index', compact('opening_balances', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $acc = AccCoa::where('head_level', 4)  
            ->where('is_active', 1)
            ->whereIn('head_type', array('A','L'))
            ->orderBy('head_type')
            ->get();

        $oldyears = FinancialYear::where('status', 0)
            ->orderBy('end_date','DESC')
            ->get();

        $crcc = AccCoa::where('head_code', 'like', '1020102%')
            ->where('is_transaction', 1)
            ->orderBy('head_name')
            ->get();

        return view('backend.accounts.opening_balances.create', compact('acc','oldyears','crcc'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fyear = $request->fyear;
        $is_subtypes = $request->isSubtype;
        $subtypes = $request->subtype;
        $coa_ids = $request->cmbCode;
        $debits = $request->txtDebit;
        $credits = $request->txtCredit;
        $voucher_date = date('Y-m-d', strtotime($request->dtpDate));
        $warehouse_id = $request->warehouse_id;

        $created_by = Auth::user()->id;
        $created_at = date('Y-m-d H:i:s');

        $route_name = Route::currentRouteName();

        for ($i = 0; $i < count($coa_ids); $i++) {
            $coa_id = $coa_ids[$i];
            $debit = $debits[$i];
            $credit = $credits[$i];
            $is_subtype = $is_subtypes[$i];
            
            if ($is_subtype != 1) {
                $subcode = $subtypes[$i];
            } else {
                $subcode = null;
            }

            $opening_balance = new AccOpeningBalance();
            $opening_balance->fyear = $fyear;
            $opening_balance->coa_id = $coa_id;
            $opening_balance->sub_type = $is_subtype;
            $opening_balance->sub_code = $subcode;
            $opening_balance->open_date = $voucher_date;
            $opening_balance->warehouse_id = $warehouse_id;
            $opening_balance->debit = $debit;
            $opening_balance->credit = $credit;
            $opening_balance->created_by = $created_by;
            $opening_balance->created_at = $created_at;
            $opening_balance->save();

            add_activity_log("opening_balance", "create", $opening_balance->id, "acc_opening_balances", $route_name, 1, $opening_balance);
        }

        flash(translate('Opening balance has added Successfully'))->success();
        return redirect()->route('opening-balances.index');
    }

    /**
     * Display the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $acc = AccCoa::where('head_level', 4)  
            ->where('is_active', 1)
            ->whereIn('head_type', array('A','L'))
            ->orderBy('head_type')
            ->get();

        $oldyears = FinancialYear::where('status', 0)
            ->orderBy('end_date','DESC')
            ->get();

        $opening_balance = AccOpeningBalance::findOrFail($id);

        if ($opening_balance->sub_code != null) {
            $sub_codes = AccSubcode::where('sub_type_id', $opening_balance->sub_type)->orderBy('id', 'ASC')->get();
        } else {
            $sub_codes = array();
        }

        return view('backend.accounts.opening_balances.edit', compact('acc','oldyears','opening_balance','sub_codes'));
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $fyear = $request->fyear;
        $is_subtypes = $request->isSubtype;
        $subtypes = $request->subtype;
        $coa_ids = $request->cmbCode;
        $debits = $request->txtDebit;
        $credits = $request->txtCredit;
        $voucher_date = date('Y-m-d', strtotime($request->dtpDate));
        $warehouse_id = $request->warehouse_id;

        $created_by = Auth::user()->id;
        $created_at = date('Y-m-d H:i:s');

        $route_name = Route::currentRouteName();

        for ($i = 0; $i < count($coa_ids); $i++) {
            $coa_id = $coa_ids[$i];
            $debit = $debits[$i];
            $credit = $credits[$i];
            $is_subtype = $is_subtypes[$i];
            
            if ($is_subtype != 1) {
                $subcode = $subtypes[$i];
            } else {
                $subcode = null;
            }

            $opening_balance = AccOpeningBalance::findOrFail($id);
            $opening_balance->fyear = $fyear;
            $opening_balance->coa_id = $coa_id;
            $opening_balance->sub_type = $is_subtype;
            $opening_balance->sub_code = $subcode;
            $opening_balance->open_date = $voucher_date;
            $opening_balance->warehouse_id = $warehouse_id;
            $opening_balance->debit = $debit;
            $opening_balance->credit = $credit;
            $opening_balance->created_by = $created_by;
            $opening_balance->created_at = $created_at;
            $opening_balance->save();

            add_activity_log("opening_balance", "update", $opening_balance->id, "acc_opening_balances", $route_name, 2, $opening_balance);
        }

        flash(translate('Opening balance has been updated Successfully'))->success();
        return redirect()->route('opening-balances.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if(AccOpeningBalance::destroy($id)) {
            flash(translate('Opening balance has been deleted successfully'))->success();
            return redirect()->route('opening-balances.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    // Opening balance get subtype
    public function subtypecode($id)
    {
        $htm = '';
        $debitvcode = AccCoa::where('head_code', $id)->first();

        if ($debitvcode->sub_type != 1) {
            $subcodes = AccSubcode::where('sub_type_id', $debitvcode->sub_type)->get();

            foreach ($subcodes as $subcode) {
                $htm .= '<option value="' . $subcode->id . '" >' . $subcode->name . '</option>';
            }
        }

        echo json_encode($htm);
    }
    
    // Opening balance get subtype code
    public function subtypebyid($id)
    {
        $debitvcode = AccCoa::where('head_code', $id)->first();

        $data = array('sub_type' => $debitvcode->sub_type);
        echo json_encode($data);
    }

    // Opening balance get relation value
    public function relvaluebyid($id)
    {
        $html = '';
        if ($id != null) {
            $subcodes = AccSubcode::where('sub_type_id', $id)->get();

            foreach ($subcodes as $subcode) {
                $html .= '<option value="' . $subcode->id . '" >' . $subcode->name . '</option>';
            }
        }
        echo json_encode($html);
    }
}
