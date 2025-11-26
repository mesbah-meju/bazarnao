<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Group_product;
use App\Models\ProductTranslation;
use App\Models\ProductStock;
use App\Models\Category;
use App\Models\Warehouse;
use App\Models\OpeningStock;
use Auth;

use Illuminate\Support\Str;
use Artisan;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_products(Request $request)
    {
        $type = 'In House';
        $col_name = null;
        $query = null;
        $sort_search = null;
        $pro_sort_by = null;
        $seller_id = null;
        $products = Product::where('added_by', 'admin');
        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }
        if ($request->search != null) {
            $products = $products
                ->where('name', 'like', '%' . $request->search . '%');
            $sort_search = $request->search;
        }
        $products = $products->where('digital', 0)->orderBy('created_at', 'desc')->paginate(15);
        return view('backend.product.products.index', compact('products', 'type', 'col_name', 'query', 'sort_search', 'pro_sort_by', 'seller_id'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function seller_products(Request $request)
    {
        $col_name = null;
        $query = null;
        $seller_id = null;
        $sort_search = null;
        $products = Product::where('added_by', 'seller');
        if ($request->has('user_id') && $request->user_id != null) {
            $products = $products->where('user_id', $request->user_id);
            $seller_id = $request->user_id;
        }
        if ($request->search != null) {
            $products = $products
                ->where('name', 'like', '%' . $request->search . '%');
            $sort_search = $request->search;
        }
        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }

        $products = $products->orderBy('created_at', 'desc')->paginate(15);
        $type = 'Seller';

        return view('backend.product.products.index', compact('products', 'type', 'col_name', 'query', 'seller_id', 'sort_search'));
    }

    public function all_products(Request $request)
    {
        
        $warehouse_id=2;            
        $startDate = '2024-10-01'; 
        $nextMFdate = date('Y-m-01 00:00:00', strtotime($startDate . ' + 1 month'));
        $nextMLdate = date('Y-m-t 23:59:59', strtotime($startDate . ' + 1 month'));

        $opening_stocks = OpeningStock::where('wearhouse_id', $warehouse_id)->whereBetween('created_at', array($nextMFdate, $nextMLdate))->get();
        foreach($opening_stocks as $opening_stock){
            $product_stock =  ProductStock::where('product_id',$opening_stock->product_id)->where('wearhouse_id',$warehouse_id)->first();
            $product_stock->qty = $opening_stock->qty;
            $product_stock->opening_stock = $opening_stock->qty;
            $product_stock->save();
        }














        $col_name = null;
        $query = null;
        $seller_id = null;
        $sort_search = null;
        $pro_sort_by = null;
        $products = Product::orderBy('created_at', 'desc');
        if ($request->has('user_id') && $request->user_id != null) {
            $products = $products->where('user_id', $request->user_id);
            $seller_id = $request->user_id;
        }
        if ($request->search != null) {
            $products = $products
                ->where('name', 'like', '%' . $request->search . '%');
            $sort_search = $request->search;
        }

        /*if ($request->type != null){
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }
        */

        if ($request->type == "name,asc") {


            $products = Product::orderBy('name', 'asc');
        } elseif ($request->type == "name,desc") {
            $products = Product::orderBy('name', 'desc');
        } elseif ($request->type == "last,added") {
            $products = Product::orderBy('created_at', 'desc');
        }
        if (!empty($request->product_id)) {
            $pro_sort_by = $request->product_id;
            // dd($pro_sort_by);
            $products = $products->whereIn('products.id', $pro_sort_by);
        }

        $products = $products->paginate(15);
        $type = 'All';

        return view('backend.product.products.index', compact('products', 'type', 'col_name', 'query', 'seller_id', 'sort_search','pro_sort_by'));
    }

    
    public function staff_product_list(Request $request)
    {

        $col_name = null;
        $query = null;
        $seller_id = null;
        $sort_search = null;
        $products = Product::orderBy('created_at', 'desc');
        if ($request->has('user_id') && $request->user_id != null) {
            $products = $products->where('user_id', $request->user_id);
            $seller_id = $request->user_id;
        }
        if ($request->search != null) {
            $products = $products
                ->where('name', 'like', '%' . $request->search . '%');
            $sort_search = $request->search;
        }

        /*if ($request->type != null){
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy($col_name, $query);
            $sort_type = $request->type;
        }
        */

        if ($request->type == "name,asc") {


            $products = Product::orderBy('name', 'asc');
        } elseif ($request->type == "name,desc") {
            $products = Product::orderBy('name', 'desc');
        } elseif ($request->type == "last,added") {
            $products = Product::orderBy('created_at', 'desc');
        }

        $products = $products->paginate(15);
        $type = 'All';

        return view('backend.staff_panel.customer_service.products.index', compact('products', 'type', 'col_name', 'query', 'seller_id', 'sort_search'));
    }
    public function import_translation()
    {
        $filename = public_path('pp.csv');
        if (!file_exists($filename) || !is_readable($filename))
            return false;
        if (($handle = fopen($filename, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $product_name = trim($data[1]);
                $pp = trim($data[2]);
                $sp = trim($data[3]);
                Product::where('name', $product_name)->update(['unit_price' => $sp, 'purchase_price' => $pp]);
            }
            fclose($handle);
        }
    }

    public function group_product_create()
    {
        $categories = Category::where('parent_id', 0)
            ->with('childrenCategories')
            ->get();
        $wearhouses = Warehouse::get();
        return view('backend.product.products.group_product_create', compact('categories','wearhouses'));
    }

    public function group_products_list(Request $request){
        $product_ids = $request->product_ids;
        return view('backend.product.products.group_product_info', compact('product_ids'));
    }

    public function group_products_store(Request $request)
    {
        $refund_request_addon = \App\Models\Addon::where('unique_identifier', 'refund_request')->first();
        $category = Category::where('name', 'Group Product')->value('id');
        $price = 0;
        $purchase_price = 0;
        foreach ($request->products as $key => $id) 
        {
            // $all[] = $id;
            // $unit[]= Product::where('id',$id)->value('unit_price');
            $unit_price = Product::where('id',$id)->value('unit_price');
            $price += $unit_price;
            $purchasePrice = Product::where('id',$id)->value('purchase_price');
            $purchase_price += $purchasePrice;
        }

        $product = new Product;
        $product->name = $request->name;
        $product->user_id = Auth::user()->id;
        $product->category_id = $category;
        $product->unit_price = $price;
        $product->purchase_price = $purchase_price;
        $product->description = $request->description;
        $product->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)) . '-' . Str::random(5);
        $product->thumbnail_img = $request->thumbnail_img;
        $product->photos = $request->photos;
        if ($refund_request_addon != null && $refund_request_addon->activated == 1) {
            if ($request->refundable != null) {
                $product->refundable = 1;
            } else {
                $product->refundable = 0;
            }
        }
        $product->min_qty = $request->min_qty;
        $product->max_qty = $request->max_qty;
        $product->app_max_qty = $request->app_max_qty;
        $product->barcode = $request->barcode;
        $product->choice_options = "[]";
        $product->colors = "[]";
        $product->is_group_product = 1;
        // $product->cash_on_delivery = 1;
        $product->choice_options = json_encode([]);
        $product->colors = json_encode([]);
        $product->notice = $request->notice;

        $product->save();

        // total_stock
        foreach ($request->products as $key => $id) 
        {
            $group_product = new Group_product;
            $group_product->group_product_id = $product->id;
            $group_product->product_id = $id;

            $group_product->qty = $request->web_quantity[$id]; 
            $group_product->discount_type = $request->web_discount_type[$id]; 
            $group_product->discount_amount = $request->web_discount_price[$id]; 
            $group_product->price = $request->web_price[$id];
            
            $group_product->app_qty = $request->app_quantity[$id]; 
            $group_product->app_discount_type = $request->app_discount_type[$id]; 
            $group_product->app_discount_amount = $request->app_discount_price[$id]; 
            $group_product->app_price = $request->app_price[$id];

            $group_product->min_qty = $request->min_qty;
            $group_product->max_qty = $request->max_qty;
            $group_product->app_max_qty = $request->app_max_qty;
            if ($refund_request_addon != null && $refund_request_addon->activated == 1) {
                if ($request->refundable != null) {
                    $group_product->refundable = 1;
                } else {
                    $group_product->refundable = 0;
                }
            }
            $group_product->photos = $request->photos;
            $group_product->thumbnail_img = $request->thumbnail_img;
            $group_product->save();
        }
    
        flash(translate('Product has been inserted successfully'))->success();
    
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        if (Auth::user()->user_type != 'admin' && Auth::user()->user_type == 'staff') {
            if (auth()->user()->staff->role->name == 'Customer Service Executive') {
                return redirect()->route('staff_product');
            }
        }
    
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return redirect()->route('products.all');
        } else {
            if (\App\Models\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Models\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
                $seller = Auth::user()->seller;
                $seller->remaining_uploads -= 1;
                $seller->save();
            }
            return redirect()->route('seller.products');
        }
    }

    public function group_products_destroy($id)
    {
        $product = Product::findOrFail($id);

        foreach ($product->product_translations as $product_translation) {
            $product_translation->delete();
        }

        $group_products = Group_product::where('group_product_id', $id)->get();
        foreach ($group_products as $group_product) {
            $group_product->delete();
        }

        $product_stock = ProductStock::where('product_id',$id)->first();
        if ($product_stock) {
            $product_stock->delete();
        }

        if (Product::destroy($id)) {
            flash(translate('Product has been deleted successfully'))->success();
            Artisan::call('view:clear');
            Artisan::call('cache:clear');

            if (Auth::user()->user_type == 'admin') {
                return redirect()->route('products.admin');
            } else {
                return redirect()->route('seller.products');
            }
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }
    
    public function admin_group_products_edit(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $lang = $request->lang;
        $group_product= Group_product::where('group_product_id', $id)->get();
        return view('backend.product.products.group_product_edit', compact('product', 'group_product', 'lang'));
    }

    public function group_product_edit(Request $request){
        $product_ids = $request->product_ids;
        $group_product_id = $request->group_product_id;

        return view('backend.product.products.group_product_edit_info', compact('product_ids', 'group_product_id'));
    }

    public function group_products_update(Request $request){
        $refund_request_addon = \App\Models\Addon::where('unique_identifier', 'refund_request')->first();
        $category = Category::where('name', 'Group Product')->value('id');
        $price = 0;
        $web_price = 0;
        $app_price = 0;
        $purchase_price = 0;
        foreach ($request->products as $key => $id) 
        {
            $unit_price = Product::where('id',$id)->value('unit_price');
            $price += $unit_price;
            $web_price += $request->web_price[$id];
            $app_price += $request->app_price[$id];
            $purchasePrice = Product::where('id',$id)->value('purchase_price');
            $purchase_price += $purchasePrice;
        }

        $product = Product::where('id',$request->id)->first();
        $product->name = $request->name;
        $product->user_id = Auth::user()->id;
        $product->category_id = $category;
        $product->unit_price = $price;
        $product->purchase_price = $purchase_price;
        $product->description = $request->description;
        $product->notice = $request->notice;

        $product->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)) . '-' . Str::random(5);
        $product->thumbnail_img = $request->thumbnail_img;
        $product->photos = $request->photos;

        if ($refund_request_addon != null && $refund_request_addon->activated == 1) {
            if ($request->refundable != null) {
                $product->refundable = 1;
            } else {
                $product->refundable = 0;
            }
        }
        $product->min_qty = $request->min_qty;
        $product->max_qty = $request->max_qty;
        $product->app_max_qty = $request->app_max_qty;
        $product->barcode = $request->barcode;
        $product->is_group_product = 1;
        $product->save();  


        foreach ($request->products as $key => $id) 
        {
            $group_product = Group_product::where('product_id',$id)->where('group_product_id',$product->id)->first();
            if($group_product){
                $group_product->group_product_id = $product->id;
                $group_product->product_id = $id;

                $group_product->qty = $request->web_quantity[$id]; 
                $group_product->discount_type = $request->web_discount_type[$id]; 
                $group_product->discount_amount = $request->web_discount_price[$id]; 
                $group_product->price = $request->web_price[$id];
                
                $group_product->app_qty = $request->app_quantity[$id]; 
                $group_product->app_discount_type = $request->app_discount_type[$id]; 
                $group_product->app_discount_amount = $request->app_discount_price[$id]; 
                $group_product->app_price = $request->app_price[$id];

                $group_product->min_qty = $request->min_qty;
                $group_product->max_qty = $request->max_qty;
                $group_product->app_max_qty = $request->app_max_qty;

                if ($refund_request_addon != null && $refund_request_addon->activated == 1) {
                    if ($request->refundable != null) {
                        $group_product->refundable = 1;
                    } else {
                        $group_product->refundable = 0;
                    }
                }
                $group_product->photos = $request->photos;
                $group_product->thumbnail_img = $request->thumbnail_img;
                $group_product->save();
            }else{
                $group_product = new Group_product;
                $group_product->group_product_id = $product->id;
                $group_product->product_id = $id;

                $group_product->qty = $request->web_quantity[$id]; 
                $group_product->discount_type = $request->web_discount_type[$id]; 
                $group_product->discount_amount = $request->web_discount_price[$id]; 
                $group_product->price = $request->web_price[$id];
                
                $group_product->app_qty = $request->app_quantity[$id]; 
                $group_product->app_discount_type = $request->app_discount_type[$id]; 
                $group_product->app_discount_amount = $request->app_discount_price[$id]; 
                $group_product->app_price = $request->app_price[$id];

                $group_product->min_qty = $request->min_qty;
                $group_product->max_qty = $request->max_qty;
                if ($refund_request_addon != null && $refund_request_addon->activated == 1) {
                    if ($request->refundable != null) {
                        $group_product->refundable = 1;
                    } else {
                        $group_product->refundable = 0;
                    }
                }
                $group_product->photos = $request->photos;
                $group_product->thumbnail_img = $request->thumbnail_img;
                $group_product->save();
            }
            
        }
    
        flash(translate('Product has been updated successfully'))->success();
    
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        if (Auth::user()->user_type != 'admin' && Auth::user()->user_type == 'staff') {
            if (auth()->user()->staff->role->name == 'Customer Service Executive') {
                return redirect()->route('staff_product');
            }
        }
    
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return redirect()->route('products.all');
        } else {
            if (\App\Models\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Models\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
                $seller = Auth::user()->seller;
                $seller->remaining_uploads -= 1;
                $seller->save();
            }
            return redirect()->route('seller.products');
        }
    }

    public function create()
    {
        $categories = Category::where('parent_id', 0)
            ->with('childrenCategories')
            ->get();
        $wearhouses = Warehouse::get();
        return view('backend.product.products.create', compact('categories','wearhouses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:products',
        ]);
        $refund_request_addon = \App\Models\Addon::where('unique_identifier', 'refund_request')->first();
        $request->lang = ($request->lang == '') ? 'en' : $request->lang;
        $product = new Product;
        $product->name = $request->name;
        $product->added_by = Auth::user()->user_type;
        if (Auth::user()->user_type == 'seller') {
            $product->user_id = Auth::user()->id;
        } else {
            $product->user_id = Auth::user()->id;
        }
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->parent_id = $request->parent_id;
        $product->deduct_qty = $request->deduct_qty;
        $product->current_stock = $request->current_stock;
        $product->barcode = $request->barcode;

        if ($refund_request_addon != null && $refund_request_addon->activated == 1) {
            if ($request->refundable != null) {
                $product->refundable = 1;
            } else {
                $product->refundable = 0;
            }
        }
        if ($request->outofstock != null) {
            $product->outofstock = 1;
        } else {
            $product->outofstock = 0;
        }

        $product->photos = $request->photos;
        $product->thumbnail_img = $request->thumbnail_img;
        $product->unit = $request->unit;
        $product->min_qty = $request->min_qty;
        $product->max_qty = $request->max_qty;
        $product->app_max_qty = $request->app_max_qty;
        $product->self_name = $request->self_name;
        $product->self_no = $request->self_no;

        $tags = array();
        if ($request->tags[0] != null) {
            foreach (json_decode($request->tags[0]) as $key => $tag) {
                array_push($tags, $tag->value);
            }
        }
        $product->tags = implode(',', $tags);

        $product->description = $request->description;
        $product->video_provider = $request->video_provider;
        $product->video_link = $request->video_link;
        if($request->has('unit_price')) {
            $product->unit_price = $request->unit_price;    
        }

        $product->purchase_price = $request->purchase_price;
        $product->tax = $request->tax;
        $product->tax_type = $request->tax_type;

        $product->discount = $request->discount;
        $product->discount_type = $request->discount_type;

        $product->app_discount = $request->app_discount;
        $product->app_discount_type = $request->app_discount_type;

        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'pos_discount_') === 0) {
                $wearhouseId = substr($key, strlen('pos_discount_'));
                $discountTypeKey = 'discount_type_' . $wearhouseId;
                if ($request->has($discountTypeKey)) {
                    $product->{'warehouse' . $wearhouseId . '_discount'} = $value;
                    $product->{'warehouse' . $wearhouseId . '_discount_type'} = $request->input($discountTypeKey);
                }
            }
        }

        // if (strpos($key, 'pos_discount_') === 0) {
        //     $wearhouseId = substr($key, strlen('pos_discount_'));
        //     $warehouse = Warehouse::find($wearhouseId);
        //     if ($warehouse) {
        //         $discountTypeKey = 'discount_type_' . $wearhouseId;

        //         $product->{'warehouse' . $wearhouseId . '_discount'} = $value;
        //         $product->{'warehouse' . $wearhouseId . '_discount_type'} = $request->$discountTypeKey;
        //     }
        // }

        $product->shipping_type = $request->shipping_type;

        if ($request->has('shipping_type')) {
            if ($request->shipping_type == 'free') {
                $product->shipping_cost = 0;
            } elseif ($request->shipping_type == 'flat_rate') {
                $product->shipping_cost = $request->flat_shipping_cost;
            }
        }
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;

        if ($request->has('meta_img')) {
            $product->meta_img = $request->meta_img;
        } else {
            $product->meta_img = $product->thumbnail_img;
        }

        if ($product->meta_title == null) {
            $product->meta_title = $product->name;
        }

        if ($product->meta_description == null) {
            $product->meta_description = $product->description;
        }

        if ($request->hasFile('pdf')) {
            $product->pdf = $request->pdf->store('uploads/products/pdf');
        }

        $product->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)) . '-' . Str::random(5);

        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $product->colors = json_encode($request->colors);
        } else {
            $colors = array();
            $product->colors = json_encode($colors);
        }

        $choice_options = array();

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;

                $item['attribute_id'] = $no;

                $data = array();
                foreach (json_decode($request[$str][0]) as $key => $eachValue) {
                    array_push($data, $eachValue->value);
                }

                $item['values'] = $data;
                array_push($choice_options, $item);
            }
        }

        if (!empty($request->choice_no)) {
            $product->attributes = json_encode($request->choice_no);
        } else {
            $product->attributes = json_encode(array());
        }

        $product->choice_options = json_encode($choice_options, JSON_UNESCAPED_UNICODE);
        $product->notice = $request->notice;

        //$variations = array();

        $product->save();

        //combinations start
        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        }

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $data = array();
                foreach (json_decode($request[$name][0]) as $key => $item) {
                    array_push($data, $item->value);
                }
                array_push($options, $data);
            }
        }

        if ($request->has('unit_price')) {
            $default_price = $request->input('unit_price');
            $wearhouses = Warehouse::all();
        
            foreach ($wearhouses as $wearhouse) {
                $product_stock = new ProductStock;
                $product_stock->product_id = $product->id;
                $product_stock->wearhouse_id = $wearhouse->id;  
                $product_stock->price = $default_price;
                $product_stock->qty = $request->input('current_stock');
                $product_stock->save();
            }
        }
    
        $product->save();

        $product_translation = ProductTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE', 'en'), 'product_id' => $product->id]);
        $product_translation->name = $request->name;
        $product_translation->unit = $request->unit;
        $product_translation->description = $request->description;
        $product_translation->save();

        flash(translate('Product has been inserted successfully'))->success();

        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        if (Auth::user()->user_type != 'admin' && Auth::user()->user_type == 'staff') {
            if (auth()->user()->staff->role->name == 'Customer Service Executive') {
                return redirect()->route('staff_product');
            }
        }

        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return redirect()->route('products.all');
        } else {
            if (\App\Models\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Models\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
                $seller = Auth::user()->seller;
                $seller->remaining_uploads -= 1;
                $seller->save();
            }
            return redirect()->route('seller.products');
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
    public function admin_product_edit(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $lang = $request->lang;
        $tags = json_decode($product->tags);
        $categories = Category::where('parent_id', 0)
            ->with('childrenCategories')
            ->get();
        $product_stocks = ProductStock::join('warehouses','warehouses.id','product_stocks.wearhouse_id')
        ->where('product_id',$id)
        ->select('product_stocks.*','warehouses.name')
        ->get();
        $wearhouses = Warehouse::get();
        // dd($product_stocks);
        return view('backend.product.products.edit', compact('product', 'categories', 'tags', 'lang','wearhouses','product_stocks'));
    }

    public function staff_product_edit(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $lang = $request->lang;
        $tags = json_decode($product->tags);
        $categories = Category::where('parent_id', 0)
            ->with('childrenCategories')
            ->get();
        return view('backend.staff_panel.customer_service.products.edit', compact('product', 'categories', 'tags', 'lang'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function seller_product_edit(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $lang = $request->lang;
        $tags = json_decode($product->tags);
        $categories = Category::all();
        return view('backend.product.products.edit', compact('product', 'categories', 'tags', 'lang'));
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

        $product                    = Product::findOrFail($id);
        $request->lang = ($request->lang == '') ? 'en' : $request->lang;
        $refund_request_addon       = \App\Models\Addon::where('unique_identifier', 'refund_request')->first();
        $product->category_id       = $request->category_id;
        $product->brand_id          = $request->brand_id;
        $product->parent_id = $request->parent_id;
        $product->deduct_qty = $request->deduct_qty;
        $product->current_stock     = $request->current_stock;
        $product->barcode           = $request->barcode;
        $product->notice            = $request->notice;


        if ($refund_request_addon != null && $refund_request_addon->activated == 1) {
            if ($request->refundable != null) {
                $product->refundable = 1;
            } else {
                $product->refundable = 0;
            }
        }
        if ($request->outofstock != null) {
            $product->outofstock = 1;
        } else {
            $product->outofstock = 0;
        }

        if ($request->lang == env("DEFAULT_LANGUAGE")) {
            $product->name          = $request->name;
            $product->unit          = $request->unit;
            $product->description   = $request->description;
            $product->slug          = strtolower($request->slug);
        }

        $product->photos         = $request->photos;
        $product->thumbnail_img  = $request->thumbnail_img;
        $product->min_qty        = $request->min_qty;
        $product->max_qty = $request->max_qty;
        $product->app_max_qty = $request->app_max_qty;
        $product->self_name = $request->self_name;
        $product->self_no = $request->self_no;
        $tags = array();
        if ($request->tags[0] != null) {
            foreach (json_decode($request->tags[0]) as $key => $tag) {
                array_push($tags, $tag->value);
            }
        }
        $product->tags           = implode(',', $tags);

        $product->video_provider = $request->video_provider;
        $product->video_link     = $request->video_link;
        $product->unit_price     = $request->unit_price;
        $product->purchase_price = $request->purchase_price;
        $product->tax            = $request->tax;
        $product->tax_type       = $request->tax_type;
        $product->discount       = $request->discount;
        $product->discount_type     = $request->discount_type;
        $product->app_discount = $request->app_discount;
        $product->app_discount_type = $request->app_discount_type;

        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'pos_discount_') === 0) {
                $wearhouseId = substr($key, strlen('pos_discount_'));
                $discountTypeKey = 'discount_type_' . $wearhouseId;
                if ($request->has($discountTypeKey)) {
                    $product->{'warehouse' . $wearhouseId . '_discount'} = $value;
                    $product->{'warehouse' . $wearhouseId . '_discount_type'} = $request->input($discountTypeKey);
                }
            }
        }

        $product->shipping_type  = $request->shipping_type;
        if ($request->has('shipping_type')) {
            if ($request->shipping_type == 'free') {
                $product->shipping_cost = 0;
            } elseif ($request->shipping_type == 'flat_rate') {
                $product->shipping_cost = $request->flat_shipping_cost;
            }
        }
        $product->weekend_offer     = $request->weekend_offer;
        $product->weekend_offer_type     = $request->weekend_offer_type;
        $product->meta_title        = $request->meta_title;
        $product->meta_description  = $request->meta_description;
        $product->meta_img          = $request->meta_img;

        if ($product->meta_title == null) {
            $product->meta_title = $product->name;
        }

        if ($product->meta_description == null) {
            $product->meta_description = $product->description;
        }
        $product->pdf = $request->pdf;

        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $product->colors = json_encode($request->colors);
        } else {
            $colors = array();
            $product->colors = json_encode($colors);
        }

        $choice_options = array();

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;

                $item['attribute_id'] = $no;

                $data = array();
                foreach (json_decode($request[$str][0]) as $key => $eachValue) {
                    array_push($data, $eachValue->value);
                }

                $item['values'] = $data;
                array_push($choice_options, $item);
            }
        }



        if (!empty($request->choice_no)) {
            $product->attributes = json_encode($request->choice_no);
        } else {
            $product->attributes = json_encode(array());
        }

        $product->choice_options = json_encode($choice_options, JSON_UNESCAPED_UNICODE);


        //combinations start
        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        }

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $data = array();
                foreach (json_decode($request[$name][0]) as $key => $item) {
                    array_push($data, $item->value);
                }
                array_push($options, $data);
            }
        }

        if ($request->has('unit_price')) {
            $default_price = $request->input('unit_price');
            $wearhouses = Warehouse::all();
        
            foreach ($wearhouses as $wearhouse) {
                $product_stock = ProductStock::where('product_id', $product->id)->where('wearhouse_id',$wearhouse->id )->first();
                if($product_stock){
                    $product_stock->price = $default_price;      
                    $product_stock->save();
                }else{
                    $product_stock = new ProductStock;
                    $product_stock->product_id = $product->id;
                    $product_stock->wearhouse_id = $wearhouse->id;  
                    $product_stock->price = $default_price;
                    $product_stock->save();
                } 
            }
        }

        // $product_stock = ProductStock::where('product_id', $product->id)->where('wearhouse_id', '1')->first();
        //     if (Auth::user()->user_type == 'admin') {
        //         $product_stock->price = $request->unit_price;
        //         $product_stock->save();
        //     }
        

        $product->save();

        // Product Translations
        $product_translation                = ProductTranslation::firstOrNew(['lang' => $request->lang, 'product_id' => $product->id]);
        $product_translation->name          = $request->name;
        $product_translation->unit          = $request->unit;
        $product_translation->description   = $request->description;
        $product_translation->save();

        flash(translate('Product has been updated successfully'))->success();

        Artisan::call('view:clear');
        Artisan::call('cache:clear');

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
        $product = Product::findOrFail($id);
        foreach ($product->product_translations as $key => $product_translations) {
            $product_translations->delete();
        }
        if (Product::destroy($id)) {

            flash(translate('Product has been deleted successfully'))->success();

            Artisan::call('view:clear');
            Artisan::call('cache:clear');

            if (Auth::user()->user_type == 'admin') {
                return redirect()->route('products.admin');
            } else {
                return redirect()->route('seller.products');
            }
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }


    /**
     * Duplicates the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Request $request, $id)
    {
        $product = Product::find($id);
        $product_new = $product->replicate();
        $product_new->slug = substr($product_new->slug, 0, -5) . Str::random(5);

        if ($product_new->save()) {
            flash(translate('Product has been duplicated successfully'))->success();
            if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
                if ($request->type == 'In House')
                    return redirect()->route('products.admin');
                elseif ($request->type == 'Seller')
                    return redirect()->route('products.seller');
                elseif ($request->type == 'All')
                    return redirect()->route('products.all');
            } else {
                return redirect()->route('seller.products');
            }
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function get_products_by_brand(Request $request)
    {
        $products = Product::where('brand_id', $request->brand_id)->get();
        return view('partials.product_select', compact('products'));
    }

    public function updateTodaysDeal(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->todays_deal = $request->status;
        if ($product->save()) {
            return 1;
        }
        return 0;
    }

    public function updatePublished(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->published = $request->status;

        if ($product->added_by == 'seller' && \App\Models\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Models\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
            $seller = $product->user->seller;
            if ($seller->invalid_at != null && Carbon::now()->diffInDays(Carbon::parse($seller->invalid_at), false) <= 0) {
                return 0;
            }
        }

        $product->save();
        return 1;
    }

    public function updateFeatured(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->featured = $request->status;
        if ($product->save()) {
            return 1;
        }
        return 0;
    }

    public function updateRefundable(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->refundable = $request->status;
        if ($product->save()) {
            return 1;
        }
        return 0;
    }

    public function updateOutOfStock(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->outofstock = $request->status;
        if ($product->save()) {
            return 1;
        }
        return 0;
    }

    public function updateSellerFeatured(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->seller_featured = $request->status;
        if ($product->save()) {
            return 1;
        }
        return 0;
    }

    public function sku_combination(Request $request)
    {
        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $unit_price = $request->unit_price;
        $product_name = $request->name;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $data = array();
                foreach (json_decode($request[$name][0]) as $key => $item) {
                    array_push($data, $item->value);
                }
                array_push($options, $data);
            }
        }

        $combinations = null;

        return view('backend.product.products.sku_combinations', compact('combinations', 'unit_price', 'colors_active', 'product_name'));
    }

    public function sku_combination_edit(Request $request)
    {
        $product = Product::findOrFail($request->id);

        $options = array();
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $product_name = $request->name;
        $unit_price = $request->unit_price;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $data = array();
                foreach (json_decode($request[$name][0]) as $key => $item) {
                    array_push($data, $item->value);
                }
                array_push($options, $data);
            }
        }

        $combinations = null;

        return view('backend.product.products.sku_combinations_edit', compact('combinations', 'unit_price', 'colors_active', 'product_name', 'product'));
    }
}
