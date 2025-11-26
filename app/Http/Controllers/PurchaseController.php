<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use App\Models\AccCoa;
use App\Models\AccSubcode;
use App\Models\AccSubtype;
use App\Models\Supplier;
use App\Models\Role;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $areas = Area::paginate(15);
        return view('backend.setup_configurations.area.index', compact('areas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $area = new Area;

        $area->name = $request->name;
        $area->code = $request->code;

        $area->save();

        flash(translate('Area has been inserted successfully'))->success();

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $area  = Area::findOrFail($id);
        return view('backend.setup_configurations.area.edit', compact('area'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $area = Area::findOrFail($id);
        $area->name = $request->name;


        $area->code = $request->code;

        $area->save();


        flash(translate('Area has been updated successfully'))->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $area = Area::findOrFail($id);

        Area::destroy($id);

        flash(translate('Area has been deleted successfully'))->success();
        return redirect()->route('area.index');
    }

    public function payment($id)
    {
        $purchase = Purchase::where('id', $id)
            ->join('suppliers', 'suppliers.supplier_id', '=', 'purchases.supplier_id')
            ->select('purchases.*', 'suppliers.name')
            ->first();

        $data_item_rows = PurchaseDetail::where('purchase_id', $id)
            ->join('products', 'products.id', '=', 'purchase_details.product_id')
            ->get();

        $subtype = 4;
        $advance_payment = advance_payment($purchase->warehouse_id, $subtype, $purchase->supplier_id);

        if (Auth::user()->user_type == 'admin') {
            return view('backend.purchase.payment', compact('purchase', 'data_item_rows', 'advance_payment'));
        } else {
            return view('backend.purchase.payment', compact('purchase', 'data_item_rows', 'advance_payment'));
        }
    }

    public function purchase_orders_view($id)
    {
        $purchase = Purchase::where('id', $id)
            ->join('suppliers', 'suppliers.supplier_id', '=', 'purchases.supplier_id')
            ->select('purchases.*', 'suppliers.name')
            ->get();
        $data_item_rows = PurchaseDetail::where('purchase_id', $id)
            ->join('products', 'products.id', '=', 'purchase_details.product_id')
            ->get();

        if (Auth::user()->user_type == 'admin') {
            return view('backend.purchase_order.view', compact('purchase', 'data_item_rows'));
        } else {
            return view('backend.staff_panel.purchase_executive.purchase_view', compact('purchase', 'data_item_rows'));
        }
    }

    public function paymentmodal()
    {

        $data['payment_types'] = payment_methods();

        return view('backend.purchase.newpayment', $data);
    }

    public function warehouse()
    {
        $warehouses = Warehouse::get();
        return view('backend.warehouse.index', compact('warehouses'));
    }

    public function createWarehouse()
    {
        return view('backend.warehouse.create');
    }

    public function storeWarehouse(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $lastWarehouse = Warehouse::orderBy('id', 'desc')->first();
        $nextCode = $lastWarehouse ? (intval($lastWarehouse->code) + 1) : 101;

        $warehouse = new Warehouse();
        $warehouse->name   = $request->name;
        $warehouse->code   = $nextCode;
        $warehouse->status = $request->status;

        if ($warehouse->save()) {
            $subcode = [
                'sub_type_id'   => 6,
                'name'          => $warehouse->name,
                'reference_no'  => $warehouse->id,
                'status'        => 1,
                'created_at'    => now(),
            ];
            AccSubcode::create($subcode);

            flash(translate('Warehouse has been added successfully'))->success();
            return redirect()->route('warehouse.index');
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function editWarehouse($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        return view('backend.warehouse.edit', compact('warehouse'));
    }

    public function updateWarehouse(Request $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);

        $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $warehouse->update([
            'name'   => $request->name,
            'status' => $request->status,
        ]);

        $subcode = AccSubcode::where('reference_no', $warehouse->id)
            ->where('sub_type_id', 6)
            ->first();


        if ($subcode) {
            $subcode->update([
                'name'       => $warehouse->name,
                'status'     => $warehouse->status,
                'updated_at' => now(),
            ]);
        } else {
            $subcode = AccSubcode::create([
                'sub_type_id'  => 6,
                'name'         => $warehouse->name,
                'reference_no' => $warehouse->id,
                'status'       => $warehouse->status,
                'created_at'   => now(),
            ]);
        }
        flash(translate('Warehouse has been updated successfully'))->success();
        return redirect()->route('warehouse.index');
    }
}
