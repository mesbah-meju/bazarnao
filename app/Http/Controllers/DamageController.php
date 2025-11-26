<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Damage;
use App\Models\Product;
use App\Models\ProductStock;

class DamageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t 23:59:59');
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $damages = Damage::orderby('date','asc');
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d',strtotime($request->start_date));
            $end_date = date('Y-m-d',strtotime($request->end_date));
            $damages = $damages->whereBetween('date', [$start_date, $end_date]);
            
        }else{
            $damages = $damages->whereBetween('date', [$start_date, $end_date]);
        }
        $damages =   $damages->WhereIn('wearhouse_id', $warehousearray)
        ->paginate(50);
        return view('backend.staff_panel.operation_manager.damage', compact('damages','start_date','end_date'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        if(!$warehousearray){
            $warehousearray=array(); 
        }
        
        $products = Product::get();
        
        $wearhouses = Warehouse::WhereIn('id',$warehousearray)->get();
        return view('backend.staff_panel.operation_manager.damage_add', compact('wearhouses','products'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $transfer = new Damage;

        $transfer->wearhouse_id = $request->wearhouse_id;
        $transfer->qty = $request->qty;
        $transfer->total_amount = $request->total_amount;
        $transfer->date = $request->date;
        $transfer->product_id = $request->product_id;
        $transfer->remarks = $request->remarks;
        

        $transfer->save();

        flash(translate('Damage has been inserted successfully'))->success();

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
        $products = Product::get();
        $wearhouses = Warehouse::get();
         $transfer  = Damage::findOrFail($id);
         return view('backend.staff_panel.operation_manager.damage_edit', compact('transfer','wearhouses','products'));
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
        $transfer = Damage::findOrFail($id);
        $transfer->wearhouse_id = $request->wearhouse_id;
        $transfer->qty = $request->qty;
        $transfer->date = $request->date;
        $transfer->product_id = $request->product_id;
        $transfer->remarks = $request->remarks;

        $transfer->save();


        flash(translate('Damage has been updated successfully'))->success();
        return redirect()->route('damage.index');
    }

    public function approve(Request $request, $id){
        $transfer = Transfer::findOrFail($id);
        $transfer->status = 'Approved';
        $transfer->save();
        
        $ps = ProductStock::where(['product_id'=>$transfer->product_id,'wearhouse_id'=>$transfer->to_wearhouse_id])->first();
         if($ps){
         $ps->increment('qty', $transfer->qty);
         $ps->save();
         }else{
         ProductStock::insert(['product_id'=>$transfer->product_id,'wearhouse_id'=>$transfer->to_wearhouse_id,'qty'=>$transfer->qty]);
         }

        $dps = ProductStock::where(['product_id'=>$transfer->product_id,'wearhouse_id'=>$transfer->from_wearhouse_id])->first();
         if($dps){
            $dps->decrement('qty', $transfer->qty);
            $dps->save();
         }


        flash(translate('Transfer has been updated successfully'))->success();
        return redirect()->route('transfer.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $transfer = Damage::findOrFail($id);
        $transfer->status = 'Canceled';
        $transfer->save();

        flash(translate('Damage has been deleted successfully'))->success();
        return redirect()->route('damage.index');
    }
}
