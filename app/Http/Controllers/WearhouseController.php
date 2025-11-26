<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;

class WearhouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wearhouses = Warehouse::paginate(15);
        return view('backend.setup_configurations.wearhouse.index', compact('wearhouses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $warehouse = new Warehouse;
        $warehouse->name = $request->name;
        $warehouse->code = $request->code;
        $warehouse->save();

        flash(translate('Wearhouse has been inserted successfully'))->success();

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
         $wearhouse  = Warehouse::findOrFail($id);
         return view('backend.setup_configurations.wearhouse.edit', compact('wearhouse'));
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
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->name = $request->name;
        $warehouse->code = $request->code;
        $warehouse->save();

        flash(translate('Wearhouse has been updated successfully'))->success();
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
        $area = Warehouse::findOrFail($id);

        Warehouse::destroy($id);

        flash(translate('Wearhouse has been deleted successfully'))->success();
        return redirect()->route('wearhouse.index');
    }
}
