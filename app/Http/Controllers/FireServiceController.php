<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FireService;


class FireServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $banks = Bank::get();
        // dd($banks);
        // $fireService = FireService::where('status', 1)->paginate(20);
        $fireServices = FireService::get();
        return view('backend.emergency_contact.fire_service.index', compact('fireServices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.emergency_contact.fire_service.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fireService = new FireService;
    
        $fireService->service_name	 = $request->service_name	; 
        $fireService->area_code      = $request->area_code;
        $fireService->description    = $request->description;
        $fireService->duration_hours = $request->duration_hours;
        $fireService->provider_name  = $request->provider_name;
        $fireService->contact_phone  = $request->contact_phone;
        $fireService->contact_email  = $request->contact_email;
    
        $fireService->save();
    
        flash(translate('Fire Service contact has been inserted successfully'))->success();
    
        return redirect()->route('fire_service.index');
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $fireServices = FireService::findOrFail($id);
        return view('backend.emergency_contact.fire_service.edit', compact('fireServices'));
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
        $fireService = FireService::findOrFail($id);

        $fireService->service_name	 = $request->service_name	; 
        $fireService->area_code      = $request->area_code;
        $fireService->description    = $request->description;
        $fireService->duration_hours = $request->duration_hours;
        $fireService->provider_name  = $request->provider_name;
        $fireService->contact_phone  = $request->contact_phone;
        $fireService->contact_email  = $request->contact_email;
    
        $fireService->save();


        flash(translate('Fire Service Contact has been updated successfully'))->success();
        return redirect()->route('fire_service.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $fireService = FireService::findOrFail($id);
        $fireService->delete();

        flash(translate('Fire Service Contact has been deleted successfully'))->success();
        return redirect()->route('fire_service.index');
    }

}
