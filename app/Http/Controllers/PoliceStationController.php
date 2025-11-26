<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PoliceStation;


class PoliceStationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $policeStations = PoliceStation::get();
        return view('backend.emergency_contact.police_station.index', compact('policeStations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.emergency_contact.police_station.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $policeStation = new PoliceStation;
    
        $policeStation->name      = $request->name	; 
        $policeStation->code      = $request->code;
        $policeStation->district  = $request->district;
        $policeStation->branch    = $request->branch;
        $policeStation->thana     = $request->thana;
        $policeStation->area      = $request->area;
        $policeStation->phone     = $request->phone;
        $policeStation->alt_phone = $request->alt_phone;
        $policeStation->email     = $request->email;
    
        $policeStation->save();
    
        flash(translate('Police Station contact has been inserted successfully'))->success();
    
        return redirect()->route('police_station.index');
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $policeStations = PoliceStation::findOrFail($id);
        return view('backend.emergency_contact.police_station.edit', compact('policeStations'));
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
        $policeStation = PoliceStation::findOrFail($id);

        $policeStation->name      = $request->name	; 
        $policeStation->code      = $request->code;
        $policeStation->district  = $request->district;
        $policeStation->branch    = $request->branch;
        $policeStation->thana     = $request->thana;
        $policeStation->area      = $request->area;
        $policeStation->phone     = $request->phone;
        $policeStation->alt_phone = $request->alt_phone;
        $policeStation->email     = $request->email;
    
        $policeStation->save();


        flash(translate('police Station Contact has been updated successfully'))->success();
        return redirect()->route('police_station.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $policeStation = PoliceStation::findOrFail($id);
        $policeStation->delete();

        flash(translate('police Station Contact has been deleted successfully'))->success();
        return redirect()->route('police_station.index');
    }

}
