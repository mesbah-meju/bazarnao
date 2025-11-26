<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccPredefineAccount;
use App\Models\FinancialYear;
use Illuminate\Http\Request;
use Session;
use App\Exports\IncomeStatementExport;
use App\Exports\IncomeStatementYearlyExport;
use Maatwebsite\Excel\Facades\Excel;

class IncomeStatementController extends Controller
{
    public function index()
    {
        $fyears = FinancialYear::orderBy('end_date', 'desc')->get();
        $fyear  = Session::get('fyear');

        return view('backend.accounts.reports.income_statement.index', compact('fyears', 'fyear'));
    }

    public function report(Request $request)
    {
        $warehouse_id   = $request->warehouse_id ? $request->warehouse_id : null;
        $fyear          = $request->input('fyear') ? $request->input('fyear') : Session::get('fyear');
        $type           = $request->type;
        
        $predefined = AccPredefineAccount::select('costs_of_good_solds')->first();

        $data['incomes'] = get_monthly_income('I', 'Income', $warehouse_id, $fyear);
        $data['costofgoodsolds'] = get_from_second_level_expenses('E', $predefined->costs_of_good_solds, $warehouse_id, $fyear);
        $data['expenses'] = get_monthly_income('E', 'Expenses', $warehouse_id, $fyear);

        // Get warehouse name
        $warehouseName = 'All Warehouses';
        if ($warehouse_id) {
            $warehouse = \App\Models\Warehouse::find($warehouse_id);
            $warehouseName = $warehouse ? $warehouse->name : 'All Warehouses';
        }

        $data['warehouse_id'] = $warehouse_id;
        $data['fyears'] = get_financial_years();
        $data['curentYear'] = get_financial_years($fyear);
        $data['warehouseName'] = $warehouseName;
        $data['startmonth'] = date('n', strtotime($data['curentYear']->start_date));

        // Handle Excel export
        if ($type == 'excel') {
            return Excel::download(new IncomeStatementExport($data), 'IncomeStatement_' . date('Y-m-d') . '.xlsx');
        }

        return view('backend.accounts.reports.income_statement.report', $data);
    }

    // public function yearly_report(Request $request)
    // {
    //     $warehouse_id   = $request->warehouse_id ? $request->warehouse_id : null;
    //     $fyear          = $request->input('fyear') ? $request->input('fyear') : Session::get('fyear');

    //     $financial_years = FinancialYear::where('id', '<=', $fyear)->orderBy('id', 'desc')->limit(3)->get();

    //     // dd($financial_years);

    //     $predefined = AccPredefineAccount::select('costs_of_good_solds')->first();

    //     $data['incomes'] = get_monthly_income('I', 'Income', $warehouse_id, $fyear);
    //     $data['costofgoodsolds'] = get_from_second_level_expenses('E', $predefined->costs_of_good_solds, $warehouse_id, $fyear);
    //     $data['expenses'] = get_monthly_income('E', 'Expenses', $warehouse_id, $fyear);

    //     $data['warehouse_id'] = $warehouse_id;
    //     $data['fyears'] = get_financial_years();
    //     $data['curentYear'] = get_financial_years($fyear);
    //     $data['financial_years'] = $financial_years;

    //     return view('backend.accounts.reports.income_statement.yearly_report', $data);
    // }

    

    public function yearly_report(Request $request)
    {
        $warehouse_id   = $request->warehouse_id ? $request->warehouse_id : null;
        $fyear          = $request->input('fyear') ? $request->input('fyear') : Session::get('fyear');
        $type           = $request->type;

        $financial_years = FinancialYear::where('id', '<=', $fyear)->orderBy('id', 'desc')->limit(3)->get();
        
        $predefined = AccPredefineAccount::select('costs_of_good_solds')->first();

        $data['incomes'] = get_monthly_income('I', 'Income', $warehouse_id, $fyear);
        $data['costofgoodsolds'] = get_from_second_level_expenses('E', $predefined->costs_of_good_solds, $warehouse_id, $fyear);
        $data['expenses'] = get_monthly_income('E', 'Expenses', $warehouse_id, $fyear);

        // Get warehouse name
        $warehouseName = 'All Warehouses';
        if ($warehouse_id) {
            $warehouse = \App\Models\Warehouse::find($warehouse_id);
            $warehouseName = $warehouse ? $warehouse->name : 'All Warehouses';
        }

        $data['warehouse_id'] = $warehouse_id;
        $data['fyears'] = get_financial_years();
        $data['curentYear'] = get_financial_years($fyear);
        $data['financial_years'] = $financial_years;
        $data['warehouseName'] = $warehouseName;

        // Handle Excel export
        if ($type == 'excel') {
            return Excel::download(new IncomeStatementYearlyExport($data), 'IncomeStatement_Yearly_' . date('Y-m-d') . '.xlsx');
        }

        return view('backend.accounts.reports.income_statement.yearly_report', $data);
    }
}
