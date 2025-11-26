<?php

namespace App\Http\Controllers;

use App\Models\AccCoa;
use App\Models\AccSubcode;
use App\Models\AccSubtype;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Role;
use Hash;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suppliers = Supplier::orderBy('supplier_id', 'desc')->paginate(10);
        return view('backend.supplier.index', compact('suppliers'));
    }

    public function suppliers_coa(Request $request)
    {
        $suppliers = Supplier::all();
        foreach ($suppliers as $supplier) {

            $check_subcode = AccSubcode::where('sub_type_id', 4)->where('reference_no', $supplier->supplier_id)->first();

            if (!$check_subcode) {
                $sub_acc = [
                    'sub_type_id'   => 4,
                    'name'          => $supplier->name,
                    'reference_no'  => $supplier->supplier_id,
                    'status'        => 1,
                    'created_at'    => date('Y-m-d H:i:s'),
                ];

                AccSubcode::create($sub_acc, $sub_acc);
            }
        }
        return redirect()->route('sub-accounts.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        if (Auth::user()->user_type == 'admin') {
            return view('backend.supplier.create', compact('roles'));
        } else {
            return view('backend.staff_panel.purchase_executive.vendor_create', compact('roles'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $supplier = new Supplier;
        $supplier->staff_id = Auth::user()->id;
        $supplier->name = $request->name;
        $supplier->email = $request->email;
        $supplier->phone = $request->mobile;
        $supplier->address = $request->address;
        $supplier->contact_person = $request->contact_person;
        $supplier->comment = $request->comment;
        $supplier->status = 1;
        $supplier->supplier_group_id = 0;
        $supplier->branch_id = 1;

        if ($supplier->save()) {
            $subcode = [
                'sub_type_id'   => 4,
                'name'          => $request->name,
                'reference_no'  => $supplier->supplier_id,
                'status'        => 1,
                'created_at'    => now(),
            ];
            AccSubcode::create($subcode);

            flash(translate('Supplier has been inserted successfully'))->success();
            if (Auth::user()->user_type == 'admin') {
                return redirect()->route('supplier.index');
            } else {
                return back();
            }
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $supplier = Supplier::where('supplier_id', decrypt($id))->get();

        return view('backend.supplier.edit', compact('supplier'));
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
        $supplier = Supplier::where('supplier_id', $id)->first();
        $supplier->name = $request->name;
        $supplier->email = $request->email;
        $supplier->phone = $request->mobile;
        $supplier->address = $request->address;
        $supplier->contact_person = $request->contact_person;

        if ($supplier->save()) {


            flash(translate('Supplier has been updated successfully'))->success();
            return redirect()->route('supplier.index');
        }

        flash(translate('Something went wrong'))->error();
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

        if (Supplier::where('supplier_id', $id)->delete()) {
            flash(translate('Supplier has been deleted successfully'))->success();
            return redirect()->route('supplier.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }
}
