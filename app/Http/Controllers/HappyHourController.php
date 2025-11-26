<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HappyHour;
use App\Models\HappyHourProduct;
use App\Models\HappyHourTranslation;
use Illuminate\Support\Str;

class HappyHourController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $happy_hours = HappyHour::orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $happy_hours = $happy_hours->where('title', 'like', '%' . $sort_search . '%');
        }
        $happy_hours = $happy_hours->paginate(15);
        return view('backend.marketing.happy_hours.index', compact('happy_hours', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.marketing.happy_hours.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $happy_hour = new HappyHour;
        $happy_hour->title = $request->title;
        $happy_hour->text_color = $request->text_color;

        $date_var               = explode(" to ", $request->date_range);
        $happy_hour->start_date = strtotime($date_var[0]);
        $happy_hour->end_date   = strtotime($date_var[1]);

        $happy_hour->background_color = $request->background_color;
        $happy_hour->slug = strtolower(str_replace(' ', '-', $request->title) . '-' . Str::random(5));
        $happy_hour->banner = $request->banner;
        $happy_hour->status = 0;

        if ($happy_hour->save()) {
            foreach ($request->products as $key => $product) {
                $web_quantity = $request->input('web_quantity.' . $product);
                $app_quantity = $request->input('app_quantity.' . $product);

                $happy_hour_product = new HappyHourProduct;
                $happy_hour_product->happy_hour_id = $happy_hour->id;
                $happy_hour_product->web_quantity = $web_quantity;
                $happy_hour_product->app_quantity = $app_quantity;
                $happy_hour_product->product_id = $product;
                $happy_hour_product->discount = $request['discount_'.$product];
                $happy_hour_product->discount_type = $request['discount_type_'.$product];
                $happy_hour_product->save();
            }

            $happy_hour_translation = HappyHourTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'happy_hour_id' => $happy_hour->id]);
            $happy_hour_translation->title = $request->title;
            $happy_hour_translation->lang = env('DEFAULT_LANGUAGE');
            $happy_hour_translation->save();

            flash(translate('Happy Hour has been inserted successfully'))->success();
            return redirect()->route('happy_hours.index');
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
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

    public function edit(Request $request, $id)
    {
        $lang = $request->lang;
        $happy_hour = HappyHour::findOrFail($id);
        return view('backend.marketing.happy_hours.edit', compact('happy_hour', 'lang'));
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
        $happy_hour = HappyHour::findOrFail($id);

        $happy_hour->text_color = $request->text_color;

        $date_var = explode(" to ", $request->date_range);
        $happy_hour->start_date = strtotime($date_var[0]);
        $happy_hour->end_date = strtotime($date_var[1]);

        $happy_hour->background_color = $request->background_color;
        // $happy_hour->discount_percent = $request->discount_percent;

        if ($request->lang == env("DEFAULT_LANGUAGE")) {
            $happy_hour->title = $request->title;
            if (($happy_hour->slug == null) || ($happy_hour->title != $request->title)) {
                $happy_hour->slug = strtolower(str_replace(' ', '-', $request->title) . '-' . Str::random(5));
            }
        }

        $happy_hour->banner = $request->banner;

        // Delete the associated products before saving new ones
        foreach ($happy_hour->happy_hour_products as $key => $happy_hour_product) {
            $happy_hour_product->delete();
        }

        if ($happy_hour->save()) {
            // Add new products associated with the Happy Hour
            foreach ($request->products as $key => $product) {
                $web_quantity = $request->input('web_quantity.' . $product);
                $app_quantity = $request->input('app_quantity.' . $product);
                $happy_hour_product = new HappyHourProduct;
                $happy_hour_product->happy_hour_id = $happy_hour->id;
                $happy_hour_product->product_id = $product;
                $happy_hour_product->web_quantity = $web_quantity;
                $happy_hour_product->app_quantity = $app_quantity;
                $happy_hour_product->discount = $request['discount_'.$product];
                $happy_hour_product->discount_type = $request['discount_type_'.$product];
                $happy_hour_product->save();
            }

            // Handle translation for the Happy Hour
            $happy_hour_translation = HappyHourTranslation::firstOrNew(['lang' => $request->lang, 'happy_hour_id' => $happy_hour->id]);
            $happy_hour_translation->title = $request->title;
            $happy_hour_translation->lang = env('DEFAULT_LANGUAGE');
            $happy_hour_translation->save();

            flash(translate('Happy Hour has been updated successfully'))->success();
            return back();
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
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
        $happy_hour = HappyHour::findOrFail($id);

        foreach ($happy_hour->happy_hour_products as $key => $happy_hour_product) {
            $happy_hour_product->delete();
        }

        foreach ($happy_hour->happy_hour_translations as $key => $happy_hour_translation) {
            $happy_hour_translation->delete();
        }

        HappyHour::destroy($id);

        flash(translate('Happy Hour has been deleted successfully'))->success();
        return redirect()->route('happy_hours.index');
    }

    public function update_status(Request $request)
    {
        $happy_hour = HappyHour::findOrFail($request->id);
    
        if ($request->status == 1) {
            $happy_hour->status = $request->status;
            $happy_hour->save();
    
            foreach (HappyHour::where('id', '!=', $happy_hour->id)->get() as $other_happy_hour) {
                $other_happy_hour->status = 0;
                $other_happy_hour->featured = 0;
                $other_happy_hour->save();
            }
        } else {
            $happy_hour->status = $request->status;
            $happy_hour->featured = 0;

            $happy_hour->save();
        }
    
        if ($happy_hour->wasChanged()) {
            flash(translate('Happy Hour status updated successfully'))->success();
            return 1;
        }
    
        return 0;
    }
    

    public function update_featured(Request $request)
    {
        $happy_hour = HappyHour::findOrFail($request->id);
    
        if ($happy_hour->status != 1) {
            return 0;
        }
    
        foreach (HappyHour::where('id', '!=', $happy_hour->id)->get() as $other_happy_hour) {
            $other_happy_hour->featured = 0;
            $other_happy_hour->save();
        }
    
        $happy_hour->featured = $request->featured;
    
        if ($happy_hour->save()) {
            flash(translate('Happy Hour featured status updated successfully'))->success();
            return 1;
        }
    
        return 0;
    }
    

    public function product_discount(Request $request)
    {
        $product_ids = $request->product_ids;
        return view('backend.marketing.happy_hours.happy_hour_discount', compact('product_ids'));
    }

    public function product_discount_edit(Request $request)
    {
        $product_ids = $request->product_ids;
        $happy_hour_id = $request->happy_hour_id;
        return view('backend.marketing.happy_hours.happy_hour_discount_edit', compact('product_ids', 'happy_hour_id'));
    }
}
