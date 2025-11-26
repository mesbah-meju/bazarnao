<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FinancialYear;
use App\Models\AccPredefineAccount;
use App\Exports\FixedAssetExport;
use Maatwebsite\Excel\Facades\Excel;

class FixedAssetController extends Controller
{
    public function index(Request $request)
    {
        $fyears = get_financial_years();

        return view('backend.accounts.reports.fixed_asset.index', compact('fyears'));
    }

    public function report(Request $request)
    {
        $fyear          = $request->fyear;
        $warehouse_id   = $request->warehouse_id;
        $type           = $request->type;
        $fixedAsset     =  get_predefined_head('fixed_asset');

        // Get warehouse name
        $warehouseName = 'All Warehouses';
        if ($warehouse_id) {
            $warehouse = \App\Models\Warehouse::find($warehouse_id);
            $warehouseName = $warehouse ? $warehouse->name : 'All Warehouses';
        }

        $data['fixedAssets'] = get_fixed_asset_report('A', $fixedAsset, $warehouse_id, $fyear);
        $data['fyears'] = get_financial_years();
        $data['currentYear'] = get_financial_years($fyear);
        $data['warehouse_id'] = $warehouse_id;
        $data['fyear'] = $fyear;
        $data['warehouseName'] = $warehouseName;

        // Handle Excel export
        if ($type == 'excel') {
            return Excel::download(new FixedAssetExport($data), 'FixedAsset_' . date('Y-m-d') . '.xlsx');
        }
        
        return view('backend.accounts.reports.fixed_asset.report', $data);
    }
}
