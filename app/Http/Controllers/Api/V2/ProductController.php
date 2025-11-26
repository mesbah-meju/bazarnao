<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\OfferCollection;
use App\Http\Resources\V2\ProductCollection;
use App\Http\Resources\V2\ProductMiniCollection;
use App\Http\Resources\V2\ProductDetailCollection;
use App\Http\Resources\V2\FlashDealCollection;
use App\Http\Resources\V2\FlashProductDetailCollection;
use App\Models\FlashDeal;
use App\Models\Product;
use App\Models\Group_product;
use App\Models\Shop;
use App\Models\Color;
use App\Models\Offer;
use Illuminate\Http\Request;
use App\Utility\CategoryUtility;

class ProductController extends Controller
{
    public function index()
    {
        return new ProductMiniCollection(Product::latest()->paginate(10));
    }

    public function flashProductDetails($id){
        $product = Product::find($id);
        return new FlashProductDetailCollection(Product::where('id', $id)->get());
    }

    public function show($id)
    {
        return new ProductDetailCollection(Product::where('id', $id)->get());
    }

    public function admin()
    {
        return new ProductCollection(Product::where('added_by', 'admin')->latest()->paginate(10));
    }

    public function seller($id, Request $request)
    {
        $shop = Shop::findOrFail($id);
        
        if ($request->name != "" || $request->name != null) {
			$products = Product::where('added_by', 'seller');
            $name = $request->name;
             $products = $products->leftJoin('product_translations','product_translations.product_id','=','products.id')->where('products.published', 1)->where('products.name', 'like', '%'.$name.'%')->orWhere('product_translations.name', 'like', '%'.$name.'%')->groupBy('products.id')->select('products.*')
        ->orderByRaw("CASE WHEN products.name LIKE '".$name."' OR product_translations.name LIKE '".$name."' THEN 1 WHEN products.name LIKE '".$name."%' OR product_translations.name LIKE '".$name."%' THEN 2 WHEN products.name LIKE '%".$name."' OR product_translations.name LIKE '%".$name."' THEN 4 ELSE 3 END");
        }else{
			$products = Product::where('added_by', 'seller')->where('user_id', $shop->user_id);
		}
        return new ProductMiniCollection($products->latest()->paginate(10));
    }

    public function category($id, Request $request)
    {
        $category_ids = CategoryUtility::children_ids($id);
        $category_ids[] = $id;

        

        if ($request->name != "" || $request->name != null) {
            $name = $request->name;
             $products = Product::leftJoin('product_translations','product_translations.product_id','=','products.id')->where('products.published', 1)->where('products.name', 'like', '%'.$name.'%')->orWhere('product_translations.name', 'like', '%'.$name.'%')->groupBy('products.id')->select('products.*')
        ->orderByRaw("CASE WHEN products.name LIKE '".$name."' OR product_translations.name LIKE '".$name."' THEN 1 WHEN products.name LIKE '".$name."%' OR product_translations.name LIKE '".$name."%' THEN 2 WHEN products.name LIKE '%".$name."' OR product_translations.name LIKE '%".$name."' THEN 4 ELSE 3 END");
        }else{
			$products = Product::whereIn('category_id', $category_ids);
		}
        return new ProductMiniCollection(filter_products($products)->latest()->paginate(10));
    }

    public function subCategory($id)
    {
        $category_ids = CategoryUtility::children_ids($id);
        $category_ids[] = $id;

        return new ProductMiniCollection(Product::whereIn('category_id', $category_ids)->latest()->paginate(10));
    }

    public function subSubCategory($id)
    {
        $category_ids = CategoryUtility::children_ids($id);
        $category_ids[] = $id;

        return new ProductMiniCollection(Product::whereIn('category_id', $category_ids)->latest()->paginate(10));
    }

    public function brand($id, Request $request)
    {
       
        if ($request->name != "" || $request->name != null) {
			$name = $request->name;
             $products = Product::leftJoin('product_translations','product_translations.product_id','=','products.id')->where('products.published', 1)->where('products.name', 'like', '%'.$name.'%')->orWhere('product_translations.name', 'like', '%'.$name.'%')->groupBy('products.id')->select('products.*')
        ->orderByRaw("CASE WHEN products.name LIKE '".$name."' OR product_translations.name LIKE '".$name."' THEN 1 WHEN products.name LIKE '".$name."%' OR product_translations.name LIKE '".$name."%' THEN 2 WHEN products.name LIKE '%".$name."' OR product_translations.name LIKE '%".$name."' THEN 4 ELSE 3 END");
        }else {
			 $products = Product::where('brand_id', $id);
		}
        return new ProductMiniCollection($products->latest()->paginate(10));
    }

    public function todaysDeal()
    {
        return new ProductMiniCollection(Product::where('todays_deal', 1)->limit(20)->latest()->get());
    }

    public function flashDeal()
    {
        $flash_deals = FlashDeal::where('status', 1)->where('featured', 1)->where('start_date', '<=', strtotime(date('d-m-Y')))->where('end_date', '>=', strtotime(date('d-m-Y')))->get();
        return new FlashDealCollection($flash_deals);
    }

    public function featured()
    {
        return new ProductMiniCollection(Product::where('featured', 1)->limit(20)->latest()->get());
    }

    public function bestSeller()
    {
        return new ProductMiniCollection(Product::orderBy('num_of_sale', 'desc')->limit(20)->get());
    }

    public function related($id)
    {
        $product = Product::find($id);
        return new ProductMiniCollection(Product::where('category_id', $product->category_id)->where('id', '!=', $id)->limit(10)->get());
    }

    public function topFromSeller($id)
    {
        $product = Product::find($id);
        return new ProductMiniCollection(Product::where('user_id', $product->user_id)->orderBy('num_of_sale', 'desc')->limit(10)->get());
    }

    public function wishListProduct($id){
        $product_ids = Wishlist::where('user_id', $id)->pluck('product_id')->toArray();

        return response()->json([
            'result' => true,
            'data' => $product_ids
        ]);
    }


    public function search(Request $request)
    {
 
        $category_ids = [];
        $brand_ids = [];

        if ($request->categories != null && $request->categories != "") {
            $category_ids = explode(',', $request->categories);
        }

        if ($request->brands != null && $request->brands != "") {
            $brand_ids = explode(',', $request->brands);
        }

        $sort_by = $request->sort_key;
        $name = $request->name;
        $min = $request->min;
        $max = $request->max;


        $products = Product::query();

        if (!empty($brand_ids)) {
            $products = $products->whereIn('brand_id', $brand_ids);
        }

        if (!empty($category_ids)) {
            $n_cid = [];
            foreach ($category_ids as $cid) {
                $n_cid = array_merge($n_cid, CategoryUtility::children_ids($cid));
            }

            if (!empty($n_cid)) {
                $category_ids = array_merge($category_ids, $n_cid);
            }

            $products = $products->whereIn('category_id', $category_ids);
        }

        if ($name != null && $name != "") {
            $products = $products->leftJoin('product_translations','product_translations.product_id','=','products.id')
            ->where('products.published', 1)->where('products.name', 'like', '%'.$name.'%')
            ->orWhere('product_translations.name', 'like', '%'.$name.'%')->groupBy('products.id')
            ->select('products.*')->orderByRaw("CASE WHEN products.name LIKE '".$name."'
             OR product_translations.name LIKE '".$name."' THEN 1 WHEN products.name LIKE '".$name."%' 
             OR product_translations.name LIKE '".$name."%' THEN 2 WHEN products.name LIKE '%".$name."' 
             OR product_translations.name LIKE '%".$name."' THEN 4 ELSE 3 END");
        }

        if ($min != null && $min != "" && is_numeric($min)) {
            $products = $products->where('unit_price', '>=', $min);
        }

        if ($max != null && $max != "" && is_numeric($max)) {
            $products = $products->where('unit_price', '<=', $max);
        }

        switch ($sort_by) {
            case 'price_low_to_high':
                $products = $products->orderBy('unit_price', 'asc');
                break;

            case 'price_high_to_low':
                $products = $products->orderBy('unit_price', 'desc');
                break;

            case 'new_arrival':
                $products = $products->orderBy('created_at', 'desc');
                break;

            case 'popularity':
                $products = $products->orderBy('num_of_sale', 'desc');
                break;

            case 'top_rated':
                $products = $products->orderBy('rating', 'desc');
                break;

            default:
                $products = $products->orderBy('created_at', 'desc');
                break;
        }

        return new ProductMiniCollection($products->paginate(10));
    }

    public function variantPrice(Request $request)
    {
    
        $product = Product::findOrFail($request->id);
        $str = '';
        $tax = 0;
        $quantity = $request->quantity;
        
        if($product->is_group_product){
            $stock_txt = translate('In Stock');
            $str = '';
            $price = Group_product::where('group_product_id',$product->id)->sum('app_price');
            return response()->json(
    
                [
                    'result' => true,
                    'data' => [
                        'price' => single_price($price * $quantity),
                        'stock' => 100,
                        'stock_txt' => $stock_txt,
                        'variant' => $str,
                        'variation' => $str,
                        'max_limit' =>$product->app_max_qty,
                        'in_stock' => 100,
                        'image' => ""
                    ]
                ]
            );

        }else{
            if ($request->has('color') && $request->color != null) {
                $str = Color::where('code', '#' . $request->color)->first()->name;
            }
    
            $var_str = str_replace(',', '-', $request->variants);
            $var_str = str_replace(' ', '', $var_str);
    
            if ($var_str != "") {
                $temp_str = $str == "" ? $var_str : '-' . $var_str;
                $str .= $temp_str;
            }
    
            $product_stock = $product->stocks->where('variant', $str)->first();
            $price = $product->unit_price;
    
            $stock_qty = $product_stock->qty;
            $stock_txt = $product_stock->qty;
            $max_limit = $product_stock->qty;
    
            if ($stock_qty >= 1 && $product->min_qty <= $stock_qty) {
                $in_stock = 1;
            } else {
                $in_stock = 0;
            }
    
            //Product Stock Visibility
            if ($product->stock_visibility_state == 'text') {
                if ($stock_qty >= 1 && $product->min_qty < $stock_qty) {
                    $stock_txt = translate('In Stock');
                } else {
                    $stock_txt = translate('Out Of Stock');
                }
            }
    
            //discount calculation
            $discount_applicable = false;
    
            if ($product->discount_start_date == null) {
                $discount_applicable = true;
            } elseif (
                strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
                strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
            ) {
                $discount_applicable = true;
            }
    
            if ($discount_applicable) {
                if ($product->app_discount_type == 'percent') {
                    $price -= ($price * $product->app_discount) / 100;
                } elseif ($product->app_discount_type == 'amount') {
                    $price -= $product->app_discount;
                }
            }
    
            if ($product->tax_type == 'percent') {
                $price += ($price*$product->tax) / 100;
            }
            elseif ($product->tax_type == 'amount') {
                $price += $product->tax;
            }
    
            if($request->campaign == true){
                $numprice = homeDiscountedBasePrice($request->id);
                $price = (double) preg_replace("/[^0-9.]/", "", $numprice);
            }
    
            return response()->json(
    
                [
                    'result' => true,
                    'data' => [
                        'price' => single_price($price * $quantity),
                        'stock' => 100,
                        'stock_txt' => $stock_txt,
                        'variant' => $str,
                        'variation' => $str,
                        'max_limit' =>$product->app_max_qty,
                        'in_stock' => 100,
                        'image' => ""
                    ]
                ]
            );
        }

        
    }

    public function home()
    {
        return new ProductCollection(Product::inRandomOrder()->take(50)->get());
    }

     public function offerList(){
          $currentdate = strtotime(date('Y-m-d'));
          //$offers = Offer::whereRaw('start_date <= '.$currentdate.' and end_date >= '.$currentdate)->get();
        return new OfferCollection(Offer::whereRaw('start_date <= '.$currentdate.' and end_date >= '.$currentdate)->get());
     
     }

    public function discountProduct(Request $request){  
        if ($request->name != "" || $request->name != null) {
            $name = $request->name;
            // dd($name);

            return new ProductMiniCollection(Product::join('product_translations','product_translations.product_id','products.id')
            ->whereRaw('published = 1 and app_discount > 0 and outofstock = 0')->where('products.name', 'like', '%'.$name.'%')->orWhere('product_translations.name', 'like', '%'.$name.'%')->groupBy('products.id')->select('products.*')
            ->paginate(15));
        }else{
            return new ProductMiniCollection(Product::whereRaw('published = 1 and app_discount > 0 and outofstock = 0')->paginate(15));
        }
    }
}
