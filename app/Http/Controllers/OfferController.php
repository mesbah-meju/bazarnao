<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;
use Schema;


class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $offers = Offer::orderBy('id','desc')->get();
        return view('backend.marketing.offer.index', compact('offers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.marketing.offer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $offer = new Offer;
          if ($request->offer_type == "product_base") {
              $offer->type = $request->offer_type;
              $offer->title = $request->title;
              $offer->banner           = $request->banner;
            //   $offer->discount = $request->discount;
            //   $offer->discount_type = $request->discount_type;
              $data['min_buy']          = $request->min_buy;
              $data['max_discount']     = $request->max_discount;
              $date_var                 = explode(" - ", $request->date_range);
              $offer->start_date       = strtotime($date_var[0]);
              $offer->end_date         = strtotime( $date_var[1]);
              $cupon_details = array();
              foreach($request->product_ids as $product_id) {
                  $data['product_id'] = $product_id;
                  array_push($cupon_details, $data);
              }
              $offer->details = json_encode($cupon_details);
			  $offer->full_discount = $request->full_discount;
			  $offer->max_qty = $request->max_qty;
			  $offer->disc_per_qty = $request->disc_per_qty;
              if ($offer->save()) {
                  flash(translate('Offer has been saved successfully'))->success();
                  return redirect()->route('offer.index');
              }
              else{
                  flash(translate('Something went wrong'))->danger();
                  return back();
              }
          }
          elseif ($request->offer_type == "cart_base") {
            $offer->type             = $request->offer_type;
            $offer->title             = $request->title;
            $offer->banner           = $request->banner;
            $offer->discount         = $request->discount;
            $offer->discount_type    = $request->discount_type;
            $date_var                 = explode(" - ", $request->date_range);
            $offer->start_date       = strtotime($date_var[0]);
            $offer->end_date         = strtotime( $date_var[1]);
            
            $data['min_buy']          = $request->min_buy;
            $data['max_discount']     = $request->max_discount;

            //$details = array();
            foreach($request->product_ids as $product_id) {
                $data['product_id'][] = $product_id;
            }
           // array_push($details, $data);
            $offer->details = json_encode($data);

            if ($offer->save()) {
                flash(translate('Offer has been saved successfully'))->success();
                return redirect()->route('offer.index');
            }
            else{
                flash(translate('Something went wrong'))->danger();
                return back();
            }
        }
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
      $offer = Offer::findOrFail(decrypt($id));
      return view('backend.marketing.offer.edit', compact('offer'));
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
      
      $offer = Offer::findOrFail($id);
        if ($request->offer_type == "product_base") {
            $offer->type = $request->offer_type;
            $offer->title = $request->title;
            $offer->banner           = $request->banner;
            // $offer->discount = $request->discount;
            // $offer->discount_type  = $request->discount_type;
            $data['min_buy']          = $request->min_buy;
            $data['max_discount']     = $request->max_discount;
            $date_var                 = explode(" - ", $request->date_range);
            $offer->start_date       = strtotime($date_var[0]);
            $offer->end_date         = strtotime( $date_var[1]);
            $cupon_details = array();
            foreach($request->product_ids as $product_id) {
                $data['product_id'] = $product_id;
                array_push($cupon_details, $data);
            }
			  $offer->full_discount = $request->full_discount;
			  $offer->max_qty = $request->max_qty;
			  $offer->disc_per_qty = $request->disc_per_qty;
            $offer->details = json_encode($cupon_details);
            if ($offer->save()) {
                flash(translate('Offer has been saved successfully'))->success();
                return redirect()->route('offer.index');
            }
            else{
                flash(translate('Something went wrong'))->danger();
                return back();
            }
        }
        elseif ($request->offer_type == "cart_base") {
            $offer->type           = $request->offer_type;
            $offer->title           = $request->title;
            $offer->banner           = $request->banner;
            $offer->discount       = $request->discount;
            $offer->discount_type  = $request->discount_type;
            $date_var               = explode(" - ", $request->date_range);
            $offer->start_date     = strtotime($date_var[0]);
            $offer->end_date       = strtotime( $date_var[1]);
            $data                   = array();
            $data['min_buy']        = $request->min_buy;
            $data['max_discount']   = $request->max_discount;
            foreach($request->product_ids as $product_id) {
                $data['product_id'][] = $product_id;
            }
            $offer->details = json_encode($data);
            if ($offer->save()) {
                flash(translate('Offer has been saved successfully'))->success();
                return redirect()->route('offer.index');
            }
            else{
                flash(translate('Something went wrong'))->danger();
                return back();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $offer = Offer::findOrFail($id);
        if(Offer::destroy($id)){
            flash(translate('Offer has been deleted successfully'))->success();
            return redirect()->route('offer.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    public function get_offer_form(Request $request)
    {
        if($request->offer_type == "product_base") {
            return view('backend.marketing.offer.product_base_offer');
        }
        elseif($request->offer_type == "cart_base"){
            return view('backend.marketing.offer.cart_base_offer');
        }
    }

    public function get_offer_form_edit(Request $request)
    {
        if($request->offer_type == "product_base") {
            $offer = Offer::findOrFail($request->id);
            return view('backend.marketing.offer.product_base_offer_edit',compact('offer'));
        }
        elseif($request->offer_type == "cart_base"){
            $offer = Offer::findOrFail($request->id);
            return view('backend.marketing.offer.cart_base_offer_edit',compact('offer'));
        }
    }

}
