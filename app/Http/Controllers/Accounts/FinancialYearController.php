<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccOpeningBalance;
use App\Models\AccVoucher;
use App\Models\FinancialYear;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class FinancialYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort_search = null;

        $last_close_year = last_closed_financial_year();
        $financial_years = FinancialYear::orderBy('start_date', 'desc');

        if ($request->search != null) {
            $sort_search = $request->search;
            $financial_years = $financial_years->where('year_name', 'like', '%' . $request->search . '%');
        }

        $financial_years = $financial_years->paginate(10);
        return view('backend.accounts.financial_years.index', compact('sort_search', 'last_close_year', 'financial_years'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.accounts.financial_years.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $financial_year = new FinancialYear;
        $financial_year->year_name = $request->year_name;
        $financial_year->start_date = date('Y-m-d', strtotime($request->start_date));
        $financial_year->end_date = date('Y-m-d', strtotime($request->end_date));
        $financial_year->status = $request->status;
        $financial_year->save();

        flash(translate('Financial year has been added successfully'))->success();
        return redirect()->route('financial-years.index');
    }

    public function show()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $financial_year = FinancialYear::findOrFail($id);
        return view('backend.accounts.financial_years.edit', compact('financial_year'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $financial_year = FinancialYear::findOrFail($id);
        $financial_year->year_name = $request->year_name;
        $financial_year->start_date = date('Y-m-d', strtotime($request->start_date));
        $financial_year->end_date = date('Y-m-d', strtotime($request->end_date));
        $financial_year->status = $request->status;
        $financial_year->save();

        flash(translate('Financial year has been updated successfully'))->success();
        return redirect()->route('financial-years.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (FinancialYear::destroy($id)) {
            flash(translate('Financial year has been deleted successfully'))->success();
            return redirect()->route('financial-years.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Chnage status the specified resource from storage.
     */
    public function change_status(Request $request)
    {
        $financial_year = FinancialYear::find($request->id);
        $financial_year->status = $request->status;

        $financial_year->save();
        return 1;
    }

    public function closing($id)
    {
        $fyear = get_current_financial_year();

        $dtpFromDate   = $fyear->start_date;
        $dtpToDate     = $fyear->end_date;
        $oldyearid     = $fyear->id;
        $oldyearname   = $fyear->year_name;

        if (strpos($oldyearname, '-')) {
            list($preV, $postV) = explode('-', $oldyearname);
            $preV++;
            $postV++;
            $newyear = $preV . '-' . $postV;
        } else {
            $newyear = $oldyearname + 1;
        }

        $created_by = Auth::user()->id;
        $created_at = date('Y-m-d H:i:s');
        $open_date = date('Y-m-d', strtotime($dtpToDate . ' +1 day'));
        $end_date = date('Y-m-d', strtotime($dtpToDate . ' +1 year'));

        $warehouses = Warehouse::where('status', 1)->get();
        if($warehouses->isNotEmpty()) {
            $assets = null;
            $liabilities = null;
            $equitys = null;

            foreach($warehouses as $warehouse) {
                $assets     = year_closing_summary('A', 'Assets', $warehouse->id, $dtpFromDate, $dtpToDate, $oldyearid);
                $liabilities = year_closing_summary('L', 'Liabilities', $warehouse->id, $dtpFromDate, $dtpToDate, $oldyearid);
                $equitys    = year_closing_summary('L', 'Shareholder\'s Equity', $warehouse->id, $dtpFromDate, $dtpToDate, $oldyearid);
            }
        } else {
            flash(translate('Warehouse not found!'))->warning();
            return redirect()->back();
        }

        if ($assets && $liabilities && $equitys) {
            $check = check_financial_year($open_date, $end_date, $newyear);

            if ($check) {
                $chkbtn = FinancialYear::where('start_date', '>=', $fyear->end_date)->count();
                if ($chkbtn == 0) {
                    $financial_year = new FinancialYear();
                    $financial_year->year_name = $newyear;
                    $financial_year->start_date = $open_date;
                    $financial_year->end_date = $end_date;
                    $financial_year->is_close = 0;
                    $financial_year->status = 1;
                    $financial_year->created_by = $created_by;
                    $financial_year->created_at = $created_at;
                    $financial_year->save();
                }
            } else {
                $financial_year = FinancialYear::where('start_date', $open_date)->where('end_date', $end_date)->where('year_name', $newyear)->first();
                $financial_year->year_name = $newyear;
                $financial_year->start_date = $open_date;
                $financial_year->end_date = $end_date;
                $financial_year->is_close = 0;
                $financial_year->status = 1;
                $financial_year->created_by = $created_by;
                $financial_year->updated_at = $created_at;
                $financial_year->save();
            }

            $financial_year_closing = FinancialYear::findOrFail($oldyearid);
            $financial_year_closing->is_close = 1;
            $financial_year_closing->status = 0;
            $financial_year_closing->created_by = $created_by;
            $financial_year_closing->created_at = $created_at;

            if ($financial_year_closing->save()) {
                Session::put('fyear', $financial_year->id);
                Session::put('fyearName', $newyear);
                Session::put('fyearStartDate', $open_date);
                Session::put('fyearEndDate', $end_date);

                flash(translate('You have successfully clossing the financial year ' . $oldyearname . ' and next financial is now activated'))->success();

                $voucherarray = array('is_year_closed' => 1);
                $upvoucher = AccVoucher::where('fyear', $oldyearid)->update($voucherarray);
                if ($upvoucher) {
                    return redirect()->route('financial-years.index');
                }

                return redirect()->route('financial-years.index');
            }
        }
    }

    public function reverse($id)
    {
        $created_by = Auth::user()->id;
        $created_at = date('Y-m-d H:i:s');

        AccOpeningBalance::where('fyear', $id)->delete();

        $fyear =   get_current_financial_year();
        $currentfyear = array(
            'is_close'      => 0,
            'status'        => 0,
            'updated_by'    => $created_by,
            'updated_at'    => $created_at
        );
        FinancialYear::where('id', $fyear->id)->update($currentfyear);

        $updatefyear = array(
            'is_close'      => 0,
            'status'        => 1,
            'updated_by'    => $created_by,
            'updated_at'    => $created_at
        );
        FinancialYear::where('id', $id)->update($updatefyear);

        $financial_year = FinancialYear::findOrFail($id);
        if ($financial_year) {
            Session::put('fyear', $financial_year->id);
            Session::put('fyearName', $financial_year->year_name);
            Session::put('fyearStartDate', $financial_year->start_date);
            Session::put('fyearEndDate', $financial_year->end_date);
        }

        $voucherarray = array(
            'is_year_closed' => 0
        );
        AccVoucher::where('fyear', $id)->update($voucherarray);

        flash(translate('You have successfully reverse financial year clossing'))->success();
        return redirect()->route('financial-years.index');
    }
}
