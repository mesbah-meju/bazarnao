<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Warehouse;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wearhouses = Warehouse::get();
        $areas = Area::paginate(15);
        return view('backend.setup_configurations.area.index', compact('areas','wearhouses'));
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
        $area = new Area;

        $area->name = $request->name;
        $area->code = $request->code;
        $area->wearhouse_id = $request->wearhouse_id;

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
        $wearhouses = Warehouse::get();
         $area  = Area::findOrFail($id);
         return view('backend.setup_configurations.area.edit', compact('area','wearhouses'));
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
        $area->wearhouse_id = $request->wearhouse_id;

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
}
