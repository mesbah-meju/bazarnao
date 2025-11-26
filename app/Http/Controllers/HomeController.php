<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomersCommentsComplainExport;
use Illuminate\Http\Request;
use Hash;
use App\Models\Category;
use App\Models\FlashDeal;
use App\Models\Brand;
use App\Models\HappyHour;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Customer;
use App\Models\CustomerPackage;
use App\Models\Offer;
use App\Models\User;
use App\Models\Seller;
use App\Models\Shop;
use App\Models\Order;
use App\Models\Warehouse;
use App\Models\CouponUsage;
use App\Models\BusinessSetting;
use App\Models\CustomerServiceOrder;
use App\Models\DeliveryExecutiveLedger;
use App\Models\Damage;
use App\Models\ProductStock;
use App\Http\Controllers\SearchController;
use Cookie;
use Illuminate\Support\Str;
use App\Mail\SecondEmailVerifyMailManager;
use Mail;
use App\Utility\CategoryUtility;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Controllers\OTPVerificationController;
use App\Models\Dailyactivity;
use App\Models\Supplier_ledger;
use App\Models\PurchaseDetail;
use App\Models\RefundRequest;
use App\Models\Supplier;
use App\Models\Target;
use App\Models\DueCollection;
use App\Models\FireService;
use App\Models\PoliceStation;
use Artisan;
use App\Models\Staff;

class HomeController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('frontend.user_login');
    }

    public function registration(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        if ($request->has('referral_code')) {
            Cookie::queue('referral_code', $request->referral_code, 43200);
        }
        return view('frontend.user_registration');
    }

    public function cart_login(Request $request)
    {
        $user = User::whereIn('user_type', ['customer', 'seller'])->where('email', $request->email)->orWhere('phone', $request->email)->orWhere('phone', str_replace("+88", "", $request->email))->first();
        if ($user != null) {
            if (Hash::check($request->password, $user->password)) {
                if ($request->has('remember')) {
                    auth()->login($user, true);
                } else {
                    auth()->login($user, false);
                }
            } else {
                flash(translate('Invalid email or password!'))->warning();
            }
        }
        return back();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_dashboard()
    {
        if (auth()->user()->user_type == 'staff') {
            if (auth()->user()->staff->role->role_type == 1)
                return view('backend.dashboard');
            else
                return redirect()->route('staff.dashboard');
        } else
            return view('backend.dashboard');
    }

    /**
     * Show the customer/seller dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    // In your controller
    public function dashboard()
    {
        $user = Auth::user();

        if ($user->user_type === 'seller') {
            return view('frontend.user.seller.dashboard');
        } elseif ($user->user_type === 'customer') {
            // Optimized data fetching
            $cart_count = session()->has('cart') ? count(session()->get('cart')) : 0;
            $wishlist_count = $user->wishlists()->count();

            $orders = \App\Models\Order::withCount('orderDetails')
                ->where('user_id', $user->id)
                ->get();
            $ordered_products_count = $orders->sum('order_details_count');

            $default_address = $user->addresses()->where('set_default', 1)->first();

            $classified_enabled = \App\Models\BusinessSetting::where('type', 'classified_product')->value('value');
            $customer_package = null;
            if ($classified_enabled && $user->customer_package_id) {
                $customer_package = \App\Models\CustomerPackage::find($user->customer_package_id);
            }

            return view('frontend.user.customer.dashboard', compact(
                'cart_count',
                'wishlist_count',
                'ordered_products_count',
                'default_address',
                'classified_enabled',
                'customer_package'
            ));
        } else {
            abort(404);
        }
    }


    public function profile(Request $request)
    {
        if (Auth::user()->user_type == 'customer') {
            return view('frontend.user.customer.profile');
        } elseif (Auth::user()->user_type == 'seller') {
            return view('frontend.user.seller.profile');
        }
    }

    public function customer_update_profile(Request $request)
    {
        if (env('DEMO_MODE') == 'On') {
            flash(translate('Sorry! the action is not permitted in demo '))->error();
            return back();
        }
        $staff = Customer::where('user_id', Auth::user()->id)->first();

        if ($request->credit_form == 1) {
            $staff->ref1_name = $request->ref1_name;
            $staff->ref1_phone = $request->ref1_phone;
            $staff->ref1_relation = $request->ref1_relation;
            $staff->ref2_name = $request->ref2_name;
            $staff->ref2_phone = $request->ref2_phone;
            $staff->ref2_relation = $request->ref2_relation;

            $staff->office = $request->office;
            $staff->office_phone = $request->office_phone;
            $staff->designation = $request->designation;
            $staff->salary = $request->salary;

            $staff->document_type = $request->document_type;
            if ($request->document_type == 'Nid') {
                $staff->nid = $request->nid;
                $staff->nid_photo = $request->nid_photo;
            } elseif ($request->document_type == 'Birth Certificate') {
                $staff->nid = $request->b_cert;
                $staff->nid_photo = $request->nid_photo1;
            } else {
                $staff->nid = $request->passport;
                $staff->nid_photo = $request->nid_photo2;
            }

            $staff->utility = $request->utility;
            $staff->office_id = $request->office_id;

            if ($staff->save()) {
                flash(translate('Your Profile has been updated successfully!'))->success();
                return back();
            }
        } else {
            //$staff->nid = $request->nid;
            $staff->dob = $request->dob;
            $staff->save();
            $user = Auth::user();
            $postpon = $request->phone;
            $userpho = $user->phone;
            $user->name = $request->name;
            $user->address = $request->address;
            $user->country = $request->country;
            $user->city = $request->city;
            $user->postal_code = $request->postal_code;
            $user->phone = $request->phone;
            if ($postpon != $userpho) {
                if (User::where('phone', $request->phone)->first() != null) {
                    flash(translate('Phone already exists.'));
                    return back();
                }
            }

            if ($request->new_password != null && ($request->new_password == $request->confirm_password)) {
                $user->password = Hash::make($request->new_password);
            }
            $user->avatar_original = $request->photo;

            if ($user->save()) {
                flash(translate('Your Profile has been updated successfully!'))->success();
                return back();
            }
        }
        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }

    /**
     * Show the application frontend home.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('frontend.index');
    }

    public function flash_deal_details($slug)
    {
        $flash_deal = FlashDeal::where('slug', $slug)->first();
        if ($flash_deal != null)
            return view('frontend.flash_deal_details', compact('flash_deal'));
        else {
            abort(404);
        }
    }

    public function happy_hour_details($slug)
    {
        $happy_hour = HappyHour::where('slug', $slug)->first();
        if ($happy_hour != null) {
            return view('frontend.happy_hour_details', compact('happy_hour'));
        } else {
            abort(404);
        }
    }


    public function referr_link($user_id)
    {

        $user = User::where('id', $user_id)->first();
        if ($user != null) {

            $ref = new \App\Models\Referr_code();
            $ref->code = str_pad(rand(0, pow(10, 5) - 1), 5, '0', STR_PAD_LEFT);
            $ref->user_id = $user_id;
            $ref->save();

            $code = $ref->code;
            return view('frontend.reffer_link', compact('user', 'code'));
        } else {
            abort(404);
        }
    }
    public function load_featured_section()
    {
        return view('frontend.partials.featured_products_section');
    }

    public function load_best_selling_section()
    {
        return view('frontend.partials.best_selling_section');
    }

    public function group_product_section()
    {
        $groupProducts = filter_products(
            Product::where('published', 1)->where('featured', 1)->where('is_group_product', 1)
        )->get();
        return view('frontend.partials.group_product_section', compact('groupProducts'));
    }

    public function offer_group_product_section()
    {
        $groupProducts = filter_products(
            Product::where('published', 1)->where('is_group_product', 1)
        )->get();

        return view('frontend.partials.offer_group_product_section', compact('groupProducts'));
    }


    public function load_home_categories_section()
    {
        return view('frontend.partials.home_categories_section');
    }

    public function trackOrder(Request $request)
    {
        if ($request->has('order_code')) {
            $order = Order::where('code', $request->order_code)->first();
            if ($order != null) {
                return view('frontend.track_order', compact('order'));
            }
        }
        return view('frontend.track_order');
    }

    public function product(Request $request, $slug)
    {
        $detailedProduct  = Product::where('slug', $slug)->first();

        if ($detailedProduct != null && $detailedProduct->published) {
            //updateCartSetup();
            if ($request->has('product_referral_code')) {
                Cookie::queue('product_referral_code', $request->product_referral_code, 43200);
                Cookie::queue('referred_product_id', $detailedProduct->id, 43200);
            }

            return view('frontend.product_details', compact('detailedProduct'));
        }
        abort(404);
    }

    public function shop($slug)
    {
        $shop  = Shop::where('slug', $slug)->first();
        if ($shop != null) {
            $seller = Seller::where('user_id', $shop->user_id)->first();
            if ($seller->verification_status != 0) {
                return view('frontend.seller_shop', compact('shop'));
            } else {
                return view('frontend.seller_shop_without_verification', compact('shop', 'seller'));
            }
        }
        abort(404);
    }

    public function filter_shop($slug, $type)
    {
        $shop  = Shop::where('slug', $slug)->first();
        if ($shop != null && $type != null) {
            return view('frontend.seller_shop', compact('shop', 'type'));
        }
        abort(404);
    }

    public function all_categories(Request $request)
    {
        $categories = Category::where('level', 0)->orderBy('name', 'asc')->get();
        return view('frontend.all_category', compact('categories'));
    }
    public function coupon_usage()
    {
        $coupons = CouponUsage::join('coupons', 'coupons.id', '=', 'coupon_usages.coupon_id')->join('orders', 'orders.id', '=', 'coupon_usages.order_id')->where('coupon_usages.user_id', Auth::user()->id)->select('coupons.code as coupon_code', 'coupon_usages.coupon_id', 'orders.*')->orderBy('orders.date', 'desc')->get();
        return view('frontend.user.coupon_usage', compact('coupons'));
    }
    public function referral_link()
    {
        return view('frontend.user.referral_link');
    }
    public function all_brands(Request $request)
    {
        $categories = Category::all();
        return view('frontend.all_brand', compact('categories'));
    }

    public function show_product_upload_form(Request $request)
    {
        if (\App\Models\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Models\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
            if (Auth::user()->seller->remaining_uploads > 0) {
                $categories = Category::where('parent_id', 0)
                    ->with('childrenCategories')
                    ->get();
                return view('frontend.user.seller.product_upload', compact('categories'));
            } else {
                flash(translate('Upload limit has been reached. Please upgrade your package.'))->warning();
                return back();
            }
        }
        $categories = Category::where('parent_id', 0)
            ->with('childrenCategories')
            ->get();
        return view('frontend.user.seller.product_upload', compact('categories'));
    }

    public function profile_edit(Request $request)
    {
        $user = User::where('user_type', 'admin')->first();
        auth()->login($user);
        return redirect()->route('admin.dashboard');
    }

    public function show_product_edit_form(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $lang = $request->lang;
        $tags = json_decode($product->tags);
        $categories = Category::where('parent_id', 0)
            ->with('childrenCategories')
            ->get();
        return view('frontend.user.seller.product_edit', compact('product', 'categories', 'tags', 'lang'));
    }

    public function seller_product_list(Request $request)
    {
        $search = null;
        $products = Product::where('user_id', Auth::user()->id)->where('digital', 0)->orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $search = $request->search;
            $products = $products->where('name', 'like', '%' . $search . '%');
        }
        $products = $products->paginate(10);
        return view('frontend.user.seller.products', compact('products', 'search'));
    }

    public function ajax_search(Request $request)
    {
        $keywords = array();
        // $products = Product::where('published', 1)->where('tags', 'like', '%'.$request->search.'%')->get();
        // foreach ($products as $key => $product) {
        //     foreach (explode(',',$product->tags) as $key => $tag) {
        //         if(stripos($tag, $request->search) !== false){
        //             if(sizeof($keywords) > 5){
        //                 break;
        //             }
        //             else{
        //                 if(!in_array(strtolower($tag), $keywords)){
        //                     array_push($keywords, strtolower($tag));
        //                 }
        //             }
        //         }
        //     }
        // }

        $products = filter_products(Product::leftJoin('product_translations', 'product_translations.product_id', '=', 'products.id')->where('products.published', 1)->where('products.name', 'like', '%' . $request->search . '%')->orWhere('product_translations.name', 'like', '%' . $request->search . '%'))->groupBy('products.id')->select('products.*')
            ->orderByRaw("CASE WHEN products.name LIKE '" . $request->search . "' OR product_translations.name LIKE '" . $request->search . "' THEN 1 WHEN products.name LIKE '" . $request->search . "%' OR product_translations.name LIKE '" . $request->search . "%' THEN 2 WHEN products.name LIKE '%" . $request->search . "' OR product_translations.name LIKE '%" . $request->search . "' THEN 4 ELSE 3 END")->get()->take(12);

        $categories = []; // Category::where('name', 'like', '%'.$request->search.'%')->get()->take(3);

        $shops = []; // Shop::whereIn('user_id', verified_sellers_id())->where('name', 'like', '%'.$request->search.'%')->get()->take(3);

        if (sizeof($keywords) > 0 || sizeof($categories) > 0 || sizeof($products) > 0 || sizeof($shops) > 0) {
            return view('frontend.partials.search_content', compact('products', 'categories', 'keywords', 'shops'));
        }
        return '0';
    }

    public function listing(Request $request)
    {
        return $this->search($request);
    }

    public function listingByCategory(Request $request, $category_slug)
    {
        $category = Category::where('slug', $category_slug)->first();
        if ($category != null) {
            return $this->search($request, $category->id);
        }
        abort(404);
    }

    public function listingByBrand(Request $request, $brand_slug)
    {
        $brand = Brand::where('slug', $brand_slug)->first();
        if ($brand != null) {
            return $this->search($request, null, $brand->id);
        }
        abort(404);
    }

    public function search(Request $request, $category_id = null, $brand_id = null)
    {
        $query = $request->q;
        $sort_by = $request->sort_by;
        $min_price = $request->min_price;
        $max_price = $request->max_price;
        $seller_id = $request->seller_id;

        $conditions = ['published' => 1];

        if ($brand_id != null) {
            $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
        } elseif ($request->brand != null) {
            $brand_id = (Brand::where('slug', $request->brand)->first() != null) ? Brand::where('slug', $request->brand)->first()->id : null;
            $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
        }

        if ($seller_id != null) {
            $conditions = array_merge($conditions, ['user_id' => Seller::findOrFail($seller_id)->user->id]);
        }

        $products = Product::where($conditions)->orderBy('discount', 'Desc');

        if ($category_id != null) {
            $category_ids = CategoryUtility::children_ids($category_id);
            $category_ids[] = $category_id;

            $products = $products->whereIn('category_id', $category_ids);
        }

        if ($min_price != null && $max_price != null) {
            $products = $products->where('unit_price', '>=', $min_price)->where('unit_price', '<=', $max_price);
        }

        if ($query != null) {
            $searchController = new SearchController;
            $searchController->store($request);
            $products = $products->where('name', 'like', '%' . $query . '%')->orWhere('tags', 'like', '%' . $query . '%');
        }

        if ($sort_by != null) {
            switch ($sort_by) {
                case 'newest':
                    $products->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $products->orderBy('created_at', 'asc');
                    break;
                case 'price-asc':
                    $products->orderBy('unit_price', 'asc');
                    break;
                case 'price-desc':
                    $products->orderBy('unit_price', 'desc');
                    break;
                default:
                    // code...
                    break;
            }
        }


        $non_paginate_products = filter_products($products)->get();

        //Attribute Filter

        $attributes = array();
        foreach ($non_paginate_products as $key => $product) {
            if ($product->attributes != null && is_array(json_decode($product->attributes))) {
                foreach (json_decode($product->attributes) as $key => $value) {
                    $flag = false;
                    $pos = 0;
                    foreach ($attributes as $key => $attribute) {
                        if ($attribute['id'] == $value) {
                            $flag = true;
                            $pos = $key;
                            break;
                        }
                    }
                    if (!$flag) {
                        $item['id'] = $value;
                        $item['values'] = array();
                        foreach (json_decode($product->choice_options) as $key => $choice_option) {
                            if ($choice_option->attribute_id == $value) {
                                $item['values'] = $choice_option->values;
                                break;
                            }
                        }
                        array_push($attributes, $item);
                    } else {
                        foreach (json_decode($product->choice_options) as $key => $choice_option) {
                            if ($choice_option->attribute_id == $value) {
                                foreach ($choice_option->values as $key => $value) {
                                    if (!in_array($value, $attributes[$pos]['values'])) {
                                        array_push($attributes[$pos]['values'], $value);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $selected_attributes = array();

        foreach ($attributes as $key => $attribute) {
            if ($request->has('attribute_' . $attribute['id'])) {
                foreach ($request['attribute_' . $attribute['id']] as $key => $value) {
                    $str = '"' . $value . '"';
                    $products = $products->where('choice_options', 'like', '%' . $str . '%');
                }

                $item['id'] = $attribute['id'];
                $item['values'] = $request['attribute_' . $attribute['id']];
                array_push($selected_attributes, $item);
            }
        }


        //Color Filter
        $all_colors = array();

        foreach ($non_paginate_products as $key => $product) {
            if ($product->colors != null) {
                foreach (json_decode($product->colors) as $key => $color) {
                    if (!in_array($color, $all_colors)) {
                        array_push($all_colors, $color);
                    }
                }
            }
        }

        $selected_color = null;

        if ($request->has('color')) {
            $str = '"' . $request->color . '"';
            $products = $products->where('colors', 'like', '%' . $str . '%');
            $selected_color = $request->color;
        }


        $products = filter_products($products)->paginate(16)->appends(request()->query());

        if ($request->ajax()) {
            $view = view('frontend.load_product_listing', compact('products', 'query', 'category_id', 'brand_id', 'sort_by', 'seller_id', 'min_price', 'max_price', 'attributes', 'selected_attributes', 'all_colors', 'selected_color'))->render();
            return response()->json(['html' => $view]);
        }

        return view('frontend.product_listing', compact('products', 'query', 'category_id', 'brand_id', 'sort_by', 'seller_id', 'min_price', 'max_price', 'attributes', 'selected_attributes', 'all_colors', 'selected_color'));
    }

    public function home_settings(Request $request)
    {
        return view('home_settings.index');
    }

    public function top_10_settings(Request $request)
    {
        foreach (Category::all() as $key => $category) {
            if (is_array($request->top_categories) && in_array($category->id, $request->top_categories)) {
                $category->top = 1;
                $category->save();
            } else {
                $category->top = 0;
                $category->save();
            }
        }

        foreach (Brand::all() as $key => $brand) {
            if (is_array($request->top_brands) && in_array($brand->id, $request->top_brands)) {
                $brand->top = 1;
                $brand->save();
            } else {
                $brand->top = 0;
                $brand->save();
            }
        }

        flash(translate('Top 10 categories and brands have been updated successfully'))->success();
        return redirect()->route('home_settings.index');
    }

    public function variant_price(Request $request)
    {
        $product = Product::find($request->id);
        $str = '';
        $quantity = 0;

        if ($request->has('color')) {
            $str = $request['color'];
        }

        if (json_decode(Product::find($request->id)->choice_options) != null) {
            foreach (json_decode(Product::find($request->id)->choice_options) as $key => $choice) {
                if ($str != null) {
                    $str .= '-' . str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                } else {
                    $str .= str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                }
            }
        }
        if (($product->max_qty) < $request->quantity) {
            $msg = 'You Can not add more than ' . ($product->max_qty) . ' Quantity for this product';
            return array('status' => false, 'msg' => $msg, 'quantity' => $product->max_qty);
        }
        if ($str != null && $product->variant_product) {
            $product_stock = $product->stocks->where('variant', $str)->first();
            $price = $product_stock->price;
            $quantity = $product_stock->qty;
        } else {
            $price = $product->unit_price;
            $quantity = $product->current_stock;
        }

        //discount calculation
        $flash_deals = \App\Models\FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $key => $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && \App\Models\FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first() != null) {
                $flash_deal_product = \App\Models\FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first();
                if ($flash_deal_product->discount_type == 'percent') {
                    $price -= ($price * $flash_deal_product->discount) / 100;
                } elseif ($flash_deal_product->discount_type == 'amount') {
                    $price -= $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }
        if (!$inFlashDeal) {
            $group_product_price = 0;
            if ($product->is_group_product == 1) {
                $group_product = \App\Models\Group_product::where('group_product_id', $product->id)->get();
                foreach ($group_product as $item) {
                    $group_product_price += $item->price;
                }
                $price = $group_product_price;
            } else {
                if ($product->discount_type == 'percent') {
                    $price -= ($price * $product->discount) / 100;
                } elseif ($product->discount_type == 'amount') {
                    $price -= $product->discount;
                }
            }
        }

        if ($product->tax_type == 'percent') {
            $price += ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $price += $product->tax;
        }
        return array('price' => single_price($price * $request->quantity), 'quantity' => $quantity, 'variation' => $str, 'status' => true);
    }

    public function sellerpolicy()
    {
        //$otpController = new OTPVerificationController;
        // $otpController->testSMS('01841552567');
        return view("frontend.policies.sellerpolicy");
    }

    public function returnpolicy()
    {
        return view("frontend.policies.returnpolicy");
    }

    public function supportpolicy()
    {
        return view("frontend.policies.supportpolicy");
    }

    public function terms()
    {
        return view("frontend.policies.terms");
    }

    public function privacypolicy()
    {
        return view("frontend.policies.privacypolicy");
    }

    public function offers(Request $request, $category_id = null, $brand_id = null)
    {
        $currentdate = strtotime(date('Y-m-d'));
        $query = $request->q;
        $sort_by = $request->sort_by;
        $min_price = $request->min_price;
        $max_price = $request->max_price;
        $seller_id = $request->seller_id;

        // Fetch offers based on date
        $offers = Offer::whereRaw('start_date <= ' . $currentdate . ' AND end_date >= ' . $currentdate)->get();

        // Filter products with conditions
        $conditions = ['published' => 1];
        $products = Product::where($conditions)->whereRaw('discount > 0');

        // Non-paginated products
        $non_paginate_products = filter_products($products)->get();

        // Attribute Filter
        $products = filter_products($products)->paginate(50)->appends(request()->query());

        return view("frontend.offer.offers", compact('offers', 'products', 'query', 'category_id', 'brand_id', 'sort_by', 'seller_id', 'min_price', 'max_price'));
    }

    public function weekendoffers(Request $request, $category_id = null, $brand_id = null)
    {
        $query = $request->q;
        $sort_by = $request->sort_by;
        $min_price = $request->min_price;
        $max_price = $request->max_price;
        $seller_id = $request->seller_id;

        $conditions = ['published' => 1];
        $products = Product::where($conditions);
        $products->whereRaw('weekend_offer > 0');

        $non_paginate_products = filter_products($products)->get();

        //Attribute Filter

        $attributes = array();
        foreach ($non_paginate_products as $key => $product) {
            if ($product->attributes != null && is_array(json_decode($product->attributes))) {
                foreach (json_decode($product->attributes) as $key => $value) {
                    $flag = false;
                    $pos = 0;
                    foreach ($attributes as $key => $attribute) {
                        if ($attribute['id'] == $value) {
                            $flag = true;
                            $pos = $key;
                            break;
                        }
                    }
                    if (!$flag) {
                        $item['id'] = $value;
                        $item['values'] = array();
                        foreach (json_decode($product->choice_options) as $key => $choice_option) {
                            if ($choice_option->attribute_id == $value) {
                                $item['values'] = $choice_option->values;
                                break;
                            }
                        }
                        array_push($attributes, $item);
                    } else {
                        foreach (json_decode($product->choice_options) as $key => $choice_option) {
                            if ($choice_option->attribute_id == $value) {
                                foreach ($choice_option->values as $key => $value) {
                                    if (!in_array($value, $attributes[$pos]['values'])) {
                                        array_push($attributes[$pos]['values'], $value);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $selected_attributes = array();

        foreach ($attributes as $key => $attribute) {
            if ($request->has('attribute_' . $attribute['id'])) {
                foreach ($request['attribute_' . $attribute['id']] as $key => $value) {
                    $str = '"' . $value . '"';
                    $products = $products->where('choice_options', 'like', '%' . $str . '%');
                }

                $item['id'] = $attribute['id'];
                $item['values'] = $request['attribute_' . $attribute['id']];
                array_push($selected_attributes, $item);
            }
        }


        //Color Filter
        $all_colors = array();

        foreach ($non_paginate_products as $key => $product) {
            if ($product->colors != null) {
                foreach (json_decode($product->colors) as $key => $color) {
                    if (!in_array($color, $all_colors)) {
                        array_push($all_colors, $color);
                    }
                }
            }
        }

        $selected_color = null;

        if ($request->has('color')) {
            $str = '"' . $request->color . '"';
            $products = $products->where('colors', 'like', '%' . $str . '%');
            $selected_color = $request->color;
        }


        $products = filter_products($products)->paginate(50)->appends(request()->query());

        return view('frontend.offer.weekend_offers', compact('products', 'query', 'category_id', 'brand_id', 'sort_by', 'seller_id', 'min_price', 'max_price', 'attributes', 'selected_attributes', 'all_colors', 'selected_color'));
    }


    public function get_category_items(Request $request)
    {
        $category = Category::findOrFail($request->id);
        return view('frontend.partials.category_elements', compact('category'));
    }

    public function premium_package_index()
    {
        $customer_packages = CustomerPackage::all();
        return view('frontend.user.customer_packages_lists', compact('customer_packages'));
    }


    // Ajax call
    public function new_verify(Request $request)
    {
        $email = $request->email;
        if (isUnique($email) == '0') {
            $response['status'] = 2;
            $response['message'] = 'Email already exists!';
            return json_encode($response);
        }

        $response = $this->send_email_change_verification_mail($request, $email);
        return json_encode($response);
    }


    // Form request
    public function update_email(Request $request)
    {
        $email = $request->email;
        if (isUnique($email)) {
            $this->send_email_change_verification_mail($request, $email);
            flash(translate('A verification mail has been sent to the mail you provided us with.'))->success();
            return back();
        }

        flash(translate('Email already exists!'))->warning();
        return back();
    }

    public function send_email_change_verification_mail($request, $email)
    {
        $response['status'] = 0;
        $response['message'] = 'Unknown';

        $verification_code = Str::random(32);

        $array['subject'] = 'Email Verification';
        $array['from'] = 'info@bazarnao.com';
        $array['content'] = 'Verify your account';
        $array['link'] = route('email_change.callback') . '?new_email_verificiation_code=' . $verification_code . '&email=' . $email;
        $array['sender'] = Auth::user()->name;
        $array['details'] = "Email Second";

        $user = Auth::user();
        $user->new_email_verificiation_code = $verification_code;
        $user->save();

        try {
            Mail::to($email)->queue(new SecondEmailVerifyMailManager($array));

            $response['status'] = 1;
            $response['message'] = translate("Your verification mail has been Sent to your email.");
        } catch (\Exception $e) {
            // return $e->getMessage();
            $response['status'] = 0;
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    public function email_change_callback(Request $request)
    {
        if ($request->has('new_email_verificiation_code') && $request->has('email')) {
            $verification_code_of_url_param =  $request->input('new_email_verificiation_code');
            $user = User::where('new_email_verificiation_code', $verification_code_of_url_param)->first();

            if ($user != null) {

                $user->email = $request->input('email');
                $user->new_email_verificiation_code = null;
                $user->save();

                auth()->login($user, true);

                flash(translate('Email Changed successfully'))->success();
                return redirect()->route('dashboard');
            }
        }

        flash(translate('Email was not verified. Please resend your mail!'))->error();
        return redirect()->route('dashboard');
    }

    public function reset_password_with_code(Request $request)
    {
        if (($user = User::where('email', $request->email)->where('verification_code', $request->code)->first()) != null) {
            if ($request->password == $request->password_confirmation) {
                $user->password = Hash::make($request->password);
                $user->email_verified_at = date('Y-m-d h:m:s');
                $user->save();
                event(new PasswordReset($user));
                auth()->login($user, true);

                flash(translate('Password updated successfully'))->success();

                if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff') {
                    return redirect()->route('admin.dashboard');
                }
                return redirect()->route('home');
            } else {
                flash("Password and confirm password didn't match")->warning();
                return back();
            }
        } else {
            flash("Verification code mismatch")->error();
            return back();
        }
    }


    public function all_flash_deals()
    {
        $today = strtotime(date('Y-m-d'));
        $todaytime = strtotime(date('H:i:s'));

        $data = FlashDeal::where('status', '=', 1)
            ->where('start_date', "<=", $today)
            ->where('end_date', ">=", $today)
            ->orderBy('created_at', 'desc')
            ->get();

        if (count($data) > 0) {
            $flashstart = strtotime(date('H:i:s', $data[0]->start_date));
            $flashend = strtotime(date('H:i:s', $data[0]->end_date));
            if ($flashstart <= $todaytime && $flashend >= $todaytime) {
                $pdata = FlashDeal::leftjoin('flash_deal_products', 'flash_deals.id', '=', 'flash_deal_products.flash_deal_id')
                    ->where('start_date', "<=", $today)
                    ->where('end_date', ">", $today)
                    ->orderBy('flash_deals.created_at', 'desc')
                    ->get();

                foreach ($pdata as $row) {
                    $pro[] = $row->product_id;
                }
                $conditions = ['published' => 1];
                $products = Product::where($conditions);
                $products->whereNotIn('id', $pro);
                //dd($products); 

                $non_paginate_products = filter_products($products)->get();
                //Attribute Filter
                $products = filter_products($products)->paginate(50)->appends(request()->query());
                return view("frontend.flash_deal.all_flash_deal_list", compact('data', 'products'));
            } else {
                flash("No Flash deals avaiable in this time")->success();
                return back();
            }
        } else {
            flash("No Flash deals avaiable in this time")->success();
            return back();
        }
    }

    // public function staff_dashboard_old(Request $request)
    // {
    //     $user_id = Auth::user()->id;
    //     $from_date = date('Y-m-01');
    //     $to_date = date('Y-m-t');
    //     $today = date('Y-m-d');
    //     $warehousearray = getWearhouseBuUserId(auth()->user()->id);

    //     if (auth()->user()->staff->role->name == 'Sales Executive') {
    //         $data['total_order_qty'] = Customer::where('staff_id', Auth::user()->id)
    //             ->join('orders', 'customers.user_id', 'orders.user_id')
    //             ->join('order_details', 'orders.id', 'order_details.order_id')
    //             ->where('delivery_status', '=', 'delivered')
    //             ->whereBetween('orders.created_at', [$from_date, $to_date])
    //             ->count(DB::raw('DISTINCT orders.id'));

    //         $data['target'] = Target::where('user_id', Auth::user()->id)->where('year', date('Y'))->where('month', date('m'))->sum('target');

    //         $order_ids = Customer::where('staff_id', Auth::user()->id)
    //             ->join('orders', 'customers.user_id', 'orders.user_id')
    //             ->join('order_details', 'orders.id', 'order_details.order_id')
    //             ->where('delivery_status', '=', 'delivered')
    //             ->whereBetween('orders.created_at', [$from_date, $to_date])
    //             ->select(DB::raw('group_concat(DISTINCT orders.id) as ids'))->get();

    //         if (count($order_ids) > 0)
    //             $data['total_sales_amount'] = Order::whereIn('id', explode(',', $order_ids[0]['ids']))->sum('grand_total');
    //         else
    //             $data['total_sales_amount'] = 0;


    //         $data['total_POS_sales_amount'] = Order::where('orders.order_from', '=', 'POS')
    //             ->where('orders.created_at', '>=', $today)
    //             ->where('orders.warehouse', '=', $warehousearray)
    //             ->sum('grand_total');


    //         $data['terget_customer'] = Target::where('user_id', Auth::user()->id)->where('year', date('Y'))->where('month', date('m'))->sum('terget_customer');

    //         if (count($order_ids) > 0) {
    //             $getpaidamounts = Order::whereIn('id', explode(',', $order_ids[0]['ids']))
    //                 ->select('code', 'payment_details')->whereNotNull('payment_details')
    //                 ->get();
    //             $paid = 0;
    //             foreach ($getpaidamounts as $getpaidamount) {
    //                 if (!empty($getpaidamount->payment_details)) {
    //                     $payment = json_decode($getpaidamount->payment_details);
    //                     if (!empty($payment)) {
    //                         $paid += $payment->amount;
    //                     }
    //                 }
    //             }
    //         } else {
    //             $paid = 0;
    //         }

    //         $order_ids_for_total_due = Customer::where('staff_id', Auth::user()->id)
    //             ->join('orders', 'customers.user_id', 'orders.user_id')
    //             ->join('order_details', 'orders.id', 'order_details.order_id')
    //             ->where('delivery_status', '=', 'delivered')
    //             ->select(DB::raw('group_concat(DISTINCT orders.id) as ids'))->get();

    //         if (count($order_ids_for_total_due) > 0)
    //             $data['total_sales_amount_for_total_due'] = Order::whereIn('id', explode(',', $order_ids_for_total_due[0]['ids']))->sum('grand_total');
    //         else
    //             $data['total_sales_amount_for_total_due'] = 0;

    //         if (count($order_ids_for_total_due) > 0) {
    //             $getpaidamounts = Order::whereIn('id', explode(',', $order_ids_for_total_due[0]['ids']))
    //                 ->select('code', 'payment_details')->whereNotNull('payment_details')
    //                 ->get();
    //             $totalpaid = 0;
    //             foreach ($getpaidamounts as $getpaidamount) {
    //                 if (!empty($getpaidamount->payment_details)) {
    //                     $payment = json_decode($getpaidamount->payment_details);
    //                     if (!empty($payment)) {
    //                         $totalpaid += $payment->amount;
    //                     }
    //                 }
    //             }
    //         } else {
    //             $totalpaid = 0;
    //         }



    //         $sql = "SELECT
    //             u.name,c.user_id,c.customer_id as customer_no,sum(cl.debit) as debit,sum(cl.credit) as credit,sum(cl.balance) as balance,
    //             (select sum(cll.debit-cll.credit) from customer_ledger as cll where c.user_id=cll.customer_id and cll.date < '" . $from_date . "') as opening_balance
    //                 FROM
    //                 customers c
    //                 LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id
    //                 LEFT JOIN users u ON c.user_id = u.id";
    //         $sql .= " where 1=1 ";
    //         $sql .= " AND c.staff_id = $user_id";
    //         $sql .= "	and (cl.date between '" . $from_date . "' and '" . $to_date . "' or cl.date is null) ";
    //         $sql .= "	and (debit>0 or credit>0) 
    //                     GROUP BY c.customer_id
    //                     order by u.name asc";

    //         $customers = DB::select($sql);
    //         $balance = 0;
    //         $opening_balance = 0;
    //         foreach ($customers as $key => $customer) {
    //             $balance += $customer->opening_balance + $customer->debit - $customer->credit;
    //             $opening_balance += $customer->opening_balance;
    //         }



    //         $data['total_due'] = round($balance, 2);

    //         $sql2 = "SELECT
    //                 SUM(cl.debit) AS debit,
    //                 SUM(cl.credit) AS credit,
    //                 SUM(cl.balance) AS balance,
    //                 (
    //                     SELECT SUM(cll.debit - cll.credit)
    //                     FROM customer_ledger AS cll
    //                     WHERE c.user_id = cll.customer_id AND cll.date < '" . $to_date . "'
    //                 ) AS opening_balance
    //             FROM customers c
    //             LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id
    //             WHERE (cl.debit > 0 OR cl.credit > 0)";
    //         $sql2 .= " AND c.staff_id = $user_id";

    //         $customers2 = DB::select($sql2);
    //         $due = 0;
    //         $opening_balance = 0;
    //         foreach ($customers2 as $key => $customer) {
    //             $due += $customer->opening_balance + $customer->debit - $customer->credit;
    //         }

    //         $data['all_time_due'] = $due;


    //         $data['sales_target_recovery'] = round(($data['total_sales_amount'] * 100) / ($data['target'] ?: 1), 2);
    //         $data['new_customer'] = Customer::where('staff_id', Auth::user()->id)->whereBetween('updated_at', [$from_date, $to_date])->get()->count();
    //         $data['customer_achivement'] = round(($data['new_customer'] * 100) / ($data['terget_customer'] ?: 1));
    //         $data['performance'] = 0;
    //         $data['total_customer'] = Customer::where('staff_id', Auth::user()->id)->count();
    //         $data['daily_activities'] = Dailyactivity::where('user_id', Auth::user()->id)->where('date', date('Y-m-d'))->get();
    //         $warehouseIds = getWearhouseBuUserId(auth()->user()->id);
    //         $warehouseNames = Warehouse::whereIn('id', $warehouseIds)->pluck('name');
    //         $data['warehousearray'] = $warehouseNames;
    //         return view('backend.sales_executive_dashboard', compact('data'));
    //     } else if (auth()->user()->staff->role->name == 'Delivery Executive') {
    //         $data['total_order_qty'] = Order::where('delivery_boy', Auth::user()->id)
    //             ->whereBetween('created_at', [$from_date, $to_date])
    //             ->whereNull('cancel_date')->count();

    //         $data['delivered_qty'] = Order::where('delivery_boy', Auth::user()->id)
    //             ->whereBetween('created_at', [$from_date, $to_date])
    //             ->whereNotNull('delivered_date')->count();

    //         $data['pending_qty'] = ($data['total_order_qty']) - ($data['delivered_qty']);
    //         $data['target'] = Target::where('user_id', Auth::user()->id)->where('year', date('Y'))->where('month', date('m'))->sum('target');

    //         // $data['cash_balance'] = Order::where('delivery_boy',Auth::user()->id)
    //         // ->whereBetween('orders.created_at',[$from_date,$to_date])
    //         // ->sum('cash_collection');

    //         $data['cash_balance'] = DeliveryExecutiveLedger::where('user_id', Auth::user()->id)
    //             ->where('type', 'Order')->whereBetween('created_at', [$from_date, $to_date])->sum('debit');

    //         $data['daily_activities'] = Dailyactivity::where('user_id', Auth::user()->id)->where('date', date('Y-m-d'))->get();

    //         $data['order_daily_activities'] = Order::where('delivery_boy', Auth::user()->id)
    //             ->whereIn('order_details.delivery_status', ['on_delivery'])
    //             ->leftjoin('customers', 'customers.user_id', 'orders.user_id')
    //             ->leftjoin('areas', 'areas.code', '=', 'customers.area_code')
    //             ->leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')->groupBy('orders.id')
    //             ->select(
    //                 'orders.code',
    //                 'orders.delivery_boy',
    //                 'orders.warehouse',
    //                 'orders.grand_total',
    //                 'orders.id',
    //                 'orders.user_id',
    //                 'orders.guest_id',
    //                 'orders.shipping_address',
    //                 'orders.cash_collection',
    //                 'customers.customer_id',
    //                 'areas.name as areaname',
    //                 'order_details.delivery_status'
    //             )->get();

    //         $data2['delivery_executive_ledger'] = DeliveryExecutiveLedger::where('user_id', Auth::user()->id)
    //             ->where('type', 'Order')->where('status', 'Pending')->get();

    //         $accounts_exe = Staff::where('role_id', '15')->WhereIn('warehouse_id', $warehousearray)->get();
    //         $data['achivement'] = round($data['delivered_qty'] * 100 / ($data['total_order_qty'] ?: 1));
    //         $data['performance'] = 0;
    //         $warehouseIds = getWearhouseBuUserId(auth()->user()->id);
    //         $warehouseNames = Warehouse::whereIn('id', $warehouseIds)->pluck('name');
    //         $data['warehousearray'] = $warehouseNames;
    //         return view('backend.delivery_executive_dashboard', compact('data', 'data2', 'warehousearray'));
    //     } else if (auth()->user()->staff->role->name == 'Operation Manager') {
    //         $data['total_order_qty'] = Order::WhereNotNull('confirm_date')
    //             ->WhereNull('cancel_date')
    //             ->WhereIn('warehouse', $warehousearray)
    //             ->whereBetween('created_at', [$from_date, $to_date])->count();

    //         $data['total_delivered_qty'] = Order::WhereIn('warehouse', $warehousearray)
    //             ->WhereNotNull('delivered_date')->whereBetween('created_at', [$from_date, $to_date])
    //             ->count();

    //         $data['pending_qty'] = $data['total_order_qty']  - $data['total_delivered_qty'];

    //         $data['pending_qty_old'] = Order::WhereIn('warehouse', $warehousearray)
    //             ->WhereNull('delivered_date')->WhereNotNull('confirm_date')
    //             ->WhereNotNull('on_delivery_date')->whereBetween('created_at', [$from_date, $to_date])
    //             ->count();

    //         $data['replacement_product'] = RefundRequest::leftjoin('order_details', 'refund_requests.order_detail_id', 'order_details.id')
    //             ->leftjoin('orders', 'order_details.order_id', 'orders.id')
    //             ->select('order_details.quantity', 'orders.warehouse')->whereBetween('refund_requests.created_at', [$from_date, $to_date])
    //             ->WhereIn('warehouse', $warehousearray)
    //             ->whereIn('refund_requests.refund_status', [2, 3, 4])
    //             ->sum('quantity');

    //         $data['damage_qty'] = Damage::WhereIn('wearhouse_id', $warehousearray)
    //             ->whereBetween('created_at', [$from_date, $to_date])
    //             ->sum('qty');

    //         $data['achivement'] = round(($data['total_delivered_qty'] * 100) / ($data['total_order_qty'] ?: 1));
    //         $data['target'] = Target::where('user_id', Auth::user()->id)->where('year', date('Y'))->where('month', date('m'))->sum('target');

    //         $data['daily_activities'] = Dailyactivity::where('user_id', Auth::user()->id)->where('date', date('Y-m-d'))->get();
    //         $data['order_daily_activities'] = Order::whereIn('order_details.delivery_status', ['confirmed'])->WhereIn('warehouse', $warehousearray)
    //             ->leftjoin('customers', 'customers.user_id', 'orders.user_id')
    //             ->leftjoin('areas', 'areas.code', '=', 'customers.area_code')
    //             ->groupBy('orders.id')
    //             ->leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')
    //             ->select(
    //                 'orders.code',
    //                 'orders.delivery_boy',
    //                 'orders.warehouse',
    //                 'orders.grand_total',
    //                 'orders.id',
    //                 'orders.user_id',
    //                 'orders.guest_id',
    //                 'orders.shipping_address',
    //                 'customers.customer_id',
    //                 'areas.name as areaname',
    //                 'order_details.delivery_status'
    //             )->get();
    //         $data['performance'] = 0;
    //         $warehouseIds = getWearhouseBuUserId(auth()->user()->id);
    //         $warehouseNames = Warehouse::whereIn('id', $warehouseIds)->pluck('name');
    //         $data['warehousearray'] = $warehouseNames;
    //         return view('backend.operation_manager_dashboard', compact('data'));
    //     } else if (auth()->user()->staff->role->name == 'Purchase Executive') {

    //         $data['total_purchase_qty'] = Purchase::leftjoin('purchase_details', 'purchases.id', 'purchase_details.id')
    //             ->whereBetween('purchases.created_at', [$from_date, $to_date])
    //             ->where('created_by', Auth::user()->id)
    //             ->where('status', '2')->select('purchase_details.qty')->sum('qty');

    //         $data['total_purchase_amount'] = Purchase::leftjoin('purchase_details', 'purchases.id', 'purchase_details.id')
    //             ->whereBetween('purchases.created_at', [$from_date, $to_date])
    //             ->where('created_by', Auth::user()->id)
    //             ->where('status', '2')->select('amount')->sum('amount');

    //         $data['damage_product_qty'] = Damage::whereBetween('created_at', [$from_date, $to_date])
    //             ->where('status', 'Approved')
    //             ->select('amount')->sum('qty');

    //         $data['damage_product_amount'] = Damage::whereBetween('created_at', [$from_date, $to_date])
    //             ->where('status', 'Approved')
    //             ->select('total_amount')->sum('total_amount');

    //         $data['achivement'] = 100 - round(($data['damage_product_qty']) / ($data['total_purchase_qty'] ?: 1) * 100, 2);

    //         $data['new_supplier'] = Supplier::where('staff_id', Auth::user()->id)
    //             ->whereBetween('created_at', [$from_date, $to_date])->where('status', 1)->count();
    //         $data['performance'] = 0;

    //         $data['total_supplier'] = Supplier::where('staff_id', Auth::user()->id)
    //             ->where('status', 1)->count();

    //         $data['daily_activities'] = Dailyactivity::where('user_id', Auth::user()->id)->where('date', date('Y-m-d'))->get();
    //         $data['purchasedetails'] = Purchase::where('purchases.status', 2)->where('purchases.created_by', Auth::user()->id)->where('date', date('Y-m-d'))
    //             ->rightjoin('suppliers', 'suppliers.supplier_id', 'purchases.supplier_id')
    //             ->select('suppliers.supplier_id', 'suppliers.name', 'suppliers.address', 'suppliers.phone', 'purchases.payment_amount', 'purchases.total_value', 'purchases.purchase_no', 'purchases.id')->get();
    //         $warehouseIds = getWearhouseBuUserId(auth()->user()->id);
    //         $warehouseNames = Warehouse::whereIn('id', $warehouseIds)->pluck('name');
    //         $data['warehousearray'] = $warehouseNames;
    //         return view('backend.purchase_executive_dashboard', compact('data'));
    //     } else if (auth()->user()->staff->role->name == 'Purchase Manager') {

    //         $data['total_purchase_qty'] = Purchase::leftjoin('purchase_details', 'purchases.id', 'purchase_details.id')
    //             ->where('purchases.created_by', Auth::user()->id)
    //             ->whereBetween('purchases.created_at', [$from_date, $to_date])
    //             ->where('purchases.status', '2')
    //             ->select('purchase_details.qty')
    //             ->sum('qty');

    //         $data['total_purchase_amount'] = Purchase::leftjoin('purchase_details', 'purchases.id', 'purchase_details.id')
    //             ->where('purchases.created_by', Auth::user()->id)
    //             ->whereBetween('purchases.created_at', [$from_date, $to_date])
    //             ->where('status', '2')->select('amount')->sum('amount');

    //         $data['damage_product_qty'] = Damage::whereBetween('created_at', [$from_date, $to_date])
    //             ->where('status', 'Approved')
    //             ->select('amount')->sum('qty');

    //         $data['damage_product_amount'] = Damage::whereBetween('created_at', [$from_date, $to_date])
    //             ->where('status', 'Approved')
    //             ->select('total_amount')->sum('total_amount');

    //         $data['total_supplier'] = Supplier::where('staff_id', Auth::user()->id)
    //             ->where('status', 1)->count();

    //         $data['new_supplier'] = Supplier::where('staff_id', Auth::user()->id)
    //             ->whereBetween('created_at', [$from_date, $to_date])->count();
    //         $data['total_supplier_creadit'] = 0;

    //         $user_id = Auth::user()->id;

    //         $data['total_supplier_cr'] = " SELECT 
    //                     SUM(supplier_ledger.credit) AS cred,
    //                     SUM(supplier_ledger.debit) AS deb,
    //                     SUM(supplier_ledger.debit) - SUM(supplier_ledger.credit) AS total
    //                 FROM 
    //                     supplier_ledger
    //                 JOIN 
    //                     suppliers ON suppliers.supplier_id = supplier_ledger.supplier_id
    //                 WHERE 
    //                     suppliers.staff_id = $user_id";



    //         $data['total_creadit'] = DB::select($data['total_supplier_cr']);
    //         $data['final_total_creadit'] = $data['total_creadit'][0]->total;


    //         $data['achivement'] = 0;
    //         $data['performance'] = 0;

    //         $data['daily_activities'] = Dailyactivity::where('user_id', Auth::user()->id)->where('date', date('Y-m-d'))->get();
    //         $data['purchasedetails'] = Purchase::where('purchases.status', 2)
    //             ->where('purchases.created_by', Auth::user()->id)
    //             ->where('date', date('Y-m-d'))->wherein('wearhouse_id', $warehousearray)
    //             ->rightjoin('suppliers', 'suppliers.supplier_id', 'purchases.supplier_id')
    //             ->select('suppliers.supplier_id', 'suppliers.name', 'suppliers.address', 'suppliers.phone', 'purchases.payment_amount', 'purchases.total_value', 'purchases.purchase_no', 'purchases.id')->get();
    //         $warehouseIds = getWearhouseBuUserId(auth()->user()->id);
    //         $warehouseNames = Warehouse::whereIn('id', $warehouseIds)->pluck('name');
    //         $data['warehousearray'] = $warehouseNames;
    //         return view('backend.purchase_manager_dashboard', compact('data'));
    //     } else if (auth()->user()->staff->role->name == 'Account Executive' || auth()->user()->staff->role->name == 'Account Manager') {
    //         $data['total_order_qty'] = Order::where('delivery_boy', Auth::user()->id)->whereNull('delivered_date')->count();
    //         $data['delivered_qty'] = Order::where('delivery_boy', Auth::user()->id)->whereNotNull('delivered_date')->count();
    //         $data['target'] = Target::where('user_id', Auth::user()->id)
    //             ->where('year', date('Y'))
    //             ->where('month', date('m'))
    //             ->sum('target');
    //         $data['achivement'] = 0;
    //         $data['pending_qty'] = 0;
    //         $data['performance'] = 0;
    //         $data['cash_balance'] = 0;
    //         $data['daily_activities'] = Dailyactivity::where('user_id', Auth::user()->id)
    //             ->where('date', date('Y-m-d'))
    //             ->get();

    //         // Filtering order daily activities
    //         $orderQuery = Order::where('delivery_boy', Auth::user()->id)
    //             ->where('orders.created_at', '>=', date('Y-m-d') . ' 00:00:00')
    //             ->where('orders.created_at', '<=', date('Y-m-d') . ' 23:59:59')
    //             ->whereIn('order_details.delivery_status', ['on_delivery', 'delivered'])
    //             ->leftJoin('customers', 'customers.user_id', 'orders.user_id')
    //             ->leftJoin('areas', 'areas.code', '=', 'customers.area_code')
    //             ->leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')
    //             ->groupBy('orders.id')
    //             ->select('orders.code', 'orders.delivery_boy', 'orders.warehouse', 'orders.grand_total', 'orders.id', 'orders.user_id', 'orders.guest_id', 'orders.shipping_address', 'orders.cash_collection', 'customers.customer_id', 'areas.name as areaname', 'order_details.delivery_status');

    //         if ($request->has('user_id') && !empty($request->user_id)) {
    //             $orderQuery->where('orders.delivery_boy', $request->user_id);
    //         }

    //         if ($request->has('status') && !empty($request->status)) {
    //             $orderQuery->where('order_details.delivery_status', $request->status);
    //         }

    //         $data['order_daily_activities'] = $orderQuery->get();

    //         // Filtering delivery executive ledger
    //         $ledgerQuery = DeliveryExecutiveLedger::where('note', Auth::user()->id)
    //             ->leftjoin('orders','orders.code','delivery_executive_ledger.order_no')
    //             ->select('delivery_executive_ledger.*', 'orders.user_id as customer_id', 'orders.id as order_id', 'orders.paid_amount as order_paid_amount')
    //             ->where('status', 'Pending')
    //             ->where('type', 'Payment')
    //             ->orderBy('created_at', 'desc');

    //         if ($request->has('user_id') && !empty($request->user_id)) {
    //             $ledgerQuery->where('user_id', $request->user_id);
    //         }

    //         if ($request->has('status') && !empty($request->status)) {
    //             $ledgerQuery->where('status', $request->status);
    //         }

    //         $data2['delivery_executive_ledger'] = $ledgerQuery->get();


    //         $warehouseIds = getWearhouseBuUserId(auth()->user()->id);
    //         $warehouseNames = Warehouse::whereIn('id', $warehouseIds)->pluck('name');
    //         $data['warehousearray'] = $warehouseNames;

    //         return view('backend.account_executive_dashboard', compact('data', 'data2'));
    //     }else if (auth()->user()->staff->role->name == 'Customer Service Executive') {

    //         $data1['customer_service_orders'] = CustomerServiceOrder::where('user_id', Auth::user()->id)->where('date', date('Y-m-d'))->get();

    //         $data1['customerServiceOrder'] = Order::where('order_details.delivery_status', 'pending')
    //             ->leftjoin('customers', 'customers.user_id', 'orders.user_id')
    //             ->leftjoin('areas', 'areas.code', '=', 'customers.area_code')
    //             ->leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')
    //             ->groupBy('orders.id')
    //             ->select('orders.code', 'orders.warehouse', 'orders.grand_total', 'orders.id', 'orders.user_id', 'orders.guest_id', 'orders.shipping_address', 'customers.customer_id', 'customers.staff_id', 'areas.name as areaname', 'order_details.delivery_status')->get();

    //         $data['total_order_qty'] = Customer::where('staff_id', Auth::user()->id)
    //             ->join('orders', 'customers.user_id', 'orders.user_id')
    //             ->join('order_details', 'orders.id', 'order_details.order_id')
    //             ->where('delivery_status', '=', 'delivered')
    //             ->whereBetween('orders.created_at', [$from_date, $to_date])
    //             ->count(DB::raw('DISTINCT orders.id'));


    //         $data['target'] = Target::where('user_id', Auth::user()->id)->where('year', date('Y'))->where('month', date('m'))->sum('target');
    //         $data['recovery_target'] = Target::where('user_id', Auth::user()->id)->where('year', date('Y'))->where('month', date('m'))->sum('recovery_target');

    //         $order_ids = Customer::where('staff_id', Auth::user()->id)
    //             ->join('orders', 'customers.user_id', 'orders.user_id')
    //             ->join('order_details', 'orders.id', 'order_details.order_id')
    //             ->where('delivery_status', '=', 'delivered')
    //             ->whereBetween('orders.created_at', [$from_date, $to_date])
    //             ->select(DB::raw('group_concat(DISTINCT orders.id) as ids'))->get();

    //         if (count($order_ids) > 0)
    //             $data['total_sales_amount'] = Order::whereIn('id', explode(',', $order_ids[0]['ids']))->sum('grand_total');
    //         else
    //             $data['total_sales_amount'] = 0;

    //         $order_ids_POS = Customer::where('staff_id', Auth::user()->id)
    //             ->join('orders', 'customers.user_id', 'orders.user_id')
    //             ->join('order_details', 'orders.id', 'order_details.order_id')
    //             ->where('order_from', '=', 'POS')
    //             ->where('orders.payment_status', '=', 'paid')
    //             ->whereBetween('orders.created_at', [$from_date, $to_date])
    //             ->select(DB::raw('group_concat(DISTINCT orders.id) as ids'))->get();

    //         if (count($order_ids_POS) > 0) {
    //             $data['total_POS_sales_amount'] = Order::whereIn('id', explode(',', $order_ids_POS[0]['ids']))
    //                 ->sum('grand_total');
    //         } else {
    //             $data['total_POS_sales_amount'] = 0;
    //         }

    //         $data['terget_customer'] = Target::where('user_id', Auth::user()->id)->where('year', date('Y'))->where('month', date('m'))->sum('terget_customer');

    //         if (count($order_ids) > 0) {
    //             $getpaidamounts = Order::whereIn('id', explode(',', $order_ids[0]['ids']))
    //                 ->select('code', 'payment_details')->whereNotNull('payment_details')
    //                 ->get();
    //             $paid = 0;
    //             foreach ($getpaidamounts as $getpaidamount) {
    //                 if (!empty($getpaidamount->payment_details)) {
    //                     $payment = json_decode($getpaidamount->payment_details);
    //                     if (!empty($payment)) {
    //                         $paid += $payment->amount;
    //                     }
    //                 }
    //             }
    //         } else {
    //             $paid = 0;
    //         }


    //         $order_ids_for_total_due = Customer::where('staff_id', Auth::user()->id)
    //             ->join('orders', 'customers.user_id', 'orders.user_id')
    //             ->join('order_details', 'orders.id', 'order_details.order_id')
    //             ->where('delivery_status', '=', 'delivered')
    //             ->select(DB::raw('group_concat(DISTINCT orders.id) as ids'))->get();

    //         if (count($order_ids_for_total_due) > 0)
    //             $data['total_sales_amount_for_total_due'] = Order::whereIn('id', explode(',', $order_ids_for_total_due[0]['ids']))->sum('grand_total');
    //         else
    //             $data['total_sales_amount_for_total_due'] = 0;

    //         if (count($order_ids_for_total_due) > 0) {
    //             $getpaidamounts = Order::whereIn('id', explode(',', $order_ids_for_total_due[0]['ids']))
    //                 ->select('code', 'payment_details')->whereNotNull('payment_details')
    //                 ->get();
    //             $totalpaid = 0;
    //             foreach ($getpaidamounts as $getpaidamount) {
    //                 if (!empty($getpaidamount->payment_details)) {
    //                     $payment = json_decode($getpaidamount->payment_details);
    //                     if (!empty($payment)) {
    //                         $totalpaid += $payment->amount;
    //                     }
    //                 }
    //             }
    //         } else {
    //             $totalpaid = 0;
    //         }

    //         $sql = "SELECT
    //         u.name,c.user_id,c.customer_id as customer_no,sum(cl.debit) as debit,sum(cl.credit) as credit,sum(cl.balance) as balance,
    //         (select sum(cll.debit-cll.credit) from customer_ledger as cll where c.user_id=cll.customer_id and cll.date < '" . $from_date . "') as opening_balance
    //             FROM
    //             customers c
    //             LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id
    //             LEFT JOIN users u ON c.user_id = u.id";
    //         $sql .= " where 1=1 ";
    //         $sql .= " AND c.staff_id = $user_id";
    //         $sql .= "	and (cl.date between '" . $from_date . "' and '" . $to_date . "' or cl.date is null) ";
    //         $sql .= "	and (debit>0 or credit>0) 
    //                 GROUP BY c.customer_id
    //                 order by u.name asc";

    //         $customers = DB::select($sql);
    //         $balance = 0;
    //         $opening_balance = 0;
    //         foreach ($customers as $key => $customer) {
    //             $balance += $customer->opening_balance + $customer->debit - $customer->credit;
    //             $opening_balance += $customer->opening_balance;
    //         }

    //         $data['total_due'] = round($balance, 2);

    //         $sql2 = "SELECT
    //             SUM(cl.debit) AS debit,
    //             SUM(cl.credit) AS credit,
    //             SUM(cl.balance) AS balance,
    //             (
    //                 SELECT SUM(cll.debit - cll.credit)
    //                 FROM customer_ledger AS cll
    //                 WHERE c.user_id = cll.customer_id AND cll.date < '" . $to_date . "'
    //             ) AS opening_balance
    //         FROM customers c
    //         LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id
    //         WHERE (cl.debit > 0 OR cl.credit > 0)";
    //         $sql2 .= " AND c.staff_id = $user_id";

    //         $customers2 = DB::select($sql2);
    //         $due = 0;
    //         $opening_balance = 0;
    //         foreach ($customers2 as $key => $customer) {
    //             $due += $customer->opening_balance + $customer->debit - $customer->credit;
    //         }

    //         $data['all_time_due'] = $due;
    //         $data['target_credit_recovery'] = $data['total_sales_amount'] - $data['total_due'];
    //         $data['terget_customer'] = Target::where('user_id', Auth::user()->id)->where('year', date('Y'))->where('month', date('m'))->sum('terget_customer');
    //         if ($data['target'] != 0) {
    //             $target_value = $data['target'];
    //         } else {
    //             $target_value = 0;
    //         }
    //         $data['achivement'] = round(($data['total_sales_amount'] * 100) / ($data['target'] ?: 1));
    //         $data['new_customer'] = Customer::where('staff_id', Auth::user()->id)->whereBetween('updated_at', [$from_date, $to_date])->get()->count();
    //         $data['customer_achivement'] = round(($data['new_customer'] * 100) / ($data['terget_customer'] ?: 1));
    //         $data['performance'] = 0;
    //         $data['total_customer'] = Customer::where('staff_id', Auth::user()->id)->count();

    //         $data['daily_activities'] = Dailyactivity::where('user_id', Auth::user()->id)->where('date', date('Y-m-d'))->get();

    //         $warehouseIds = getWearhouseBuUserId(auth()->user()->id);
    //         $warehouseNames = Warehouse::whereIn('id', $warehouseIds)->pluck('name');
    //         $data['warehousearray'] = $warehouseNames;


    //         return view('backend.customer_service_dashboard', compact('data', 'data1'));
    //     } else if (auth()->user()->staff->role->name == 'Sales Manager') {
    //         $data['total_order_qty'] = Customer::where('staff_id', Auth::user()->id)
    //             ->join('orders', 'customers.user_id', 'orders.user_id')
    //             ->join('order_details', 'orders.id', 'order_details.order_id')
    //             ->where('delivery_status', '=', 'delivered')
    //             ->whereBetween('orders.created_at', [$from_date, $to_date])
    //             ->count(DB::raw('DISTINCT orders.id'));

    //         $data['target'] = Target::where('user_id', Auth::user()->id)->where('year', date('Y'))->where('month', date('m'))->sum('target');

    //         $order_ids = Customer::where('staff_id', Auth::user()->id)
    //             ->join('orders', 'customers.user_id', 'orders.user_id')
    //             ->join('order_details', 'orders.id', 'order_details.order_id')
    //             ->where('delivery_status', '=', 'delivered')
    //             ->whereBetween('orders.created_at', [$from_date, $to_date])
    //             ->select(DB::raw('group_concat(DISTINCT orders.id) as ids'))->get();

    //         if (count($order_ids) > 0)
    //             $data['total_sales_amount'] = Order::whereIn('id', explode(',', $order_ids[0]['ids']))->sum('grand_total');
    //         else
    //             $data['total_sales_amount'] = 0;

    //         $order_ids_POS = Customer::where('staff_id', Auth::user()->id)
    //             ->join('orders', 'customers.user_id', 'orders.user_id')
    //             ->join('order_details', 'orders.id', 'order_details.order_id')
    //             ->where('order_from', '=', 'POS')
    //             ->where('orders.payment_status', '=', 'paid')
    //             ->whereBetween('orders.created_at', [$from_date, $to_date])
    //             ->select(DB::raw('group_concat(DISTINCT orders.id) as ids'))->get();

    //         if (count($order_ids_POS) > 0) {
    //             $data['total_POS_sales_amount'] = Order::whereIn('id', explode(',', $order_ids_POS[0]['ids']))
    //                 ->sum('grand_total');
    //         } else {
    //             $data['total_POS_sales_amount'] = 0;
    //         }

    //         $data['terget_customer'] = Target::where('user_id', Auth::user()->id)->where('year', date('Y'))->where('month', date('m'))->sum('terget_customer');

    //         if (count($order_ids) > 0) {
    //             $getpaidamounts = Order::whereIn('id', explode(',', $order_ids[0]['ids']))
    //                 ->select('code', 'payment_details')->whereNotNull('payment_details')
    //                 ->get();
    //             $paid = 0;
    //             foreach ($getpaidamounts as $getpaidamount) {
    //                 if (!empty($getpaidamount->payment_details)) {
    //                     $payment = json_decode($getpaidamount->payment_details);
    //                     if (!empty($payment)) {
    //                         $paid += $payment->amount;
    //                     }
    //                 }
    //             }
    //         } else {
    //             $paid = 0;
    //         }

    //         $order_ids_for_total_due = Customer::where('staff_id', Auth::user()->id)
    //             ->join('orders', 'customers.user_id', 'orders.user_id')
    //             ->join('order_details', 'orders.id', 'order_details.order_id')
    //             ->where('delivery_status', '=', 'delivered')
    //             ->select(DB::raw('group_concat(DISTINCT orders.id) as ids'))->get();

    //         if (count($order_ids_for_total_due) > 0)
    //             $data['total_sales_amount_for_total_due'] = Order::whereIn('id', explode(',', $order_ids_for_total_due[0]['ids']))->sum('grand_total');
    //         else
    //             $data['total_sales_amount_for_total_due'] = 0;

    //         if (count($order_ids_for_total_due) > 0) {
    //             $getpaidamounts = Order::whereIn('id', explode(',', $order_ids_for_total_due[0]['ids']))
    //                 ->select('code', 'payment_details')->whereNotNull('payment_details')
    //                 ->get();
    //             $totalpaid = 0;
    //             foreach ($getpaidamounts as $getpaidamount) {
    //                 if (!empty($getpaidamount->payment_details)) {
    //                     $payment = json_decode($getpaidamount->payment_details);
    //                     if (!empty($payment)) {
    //                         $totalpaid += $payment->amount;
    //                     }
    //                 }
    //             }
    //         } else {
    //             $totalpaid = 0;
    //         }



    //         $sql = "SELECT
    //             u.name,c.user_id,c.customer_id as customer_no,sum(cl.debit) as debit,sum(cl.credit) as credit,sum(cl.balance) as balance,
    //             (select sum(cll.debit-cll.credit) from customer_ledger as cll where c.user_id=cll.customer_id and cll.date < '" . $from_date . "') as opening_balance
    //                 FROM
    //                 customers c
    //                 LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id
    //                 LEFT JOIN users u ON c.user_id = u.id";
    //         $sql .= " where 1=1 ";
    //         $sql .= " AND c.staff_id = $user_id";
    //         $sql .= "	and (cl.date between '" . $from_date . "' and '" . $to_date . "' or cl.date is null) ";
    //         $sql .= "	and (debit>0 or credit>0) 
    //                     GROUP BY c.customer_id
    //                     order by u.name asc";

    //         $customers = DB::select($sql);
    //         $balance = 0;
    //         $opening_balance = 0;
    //         foreach ($customers as $key => $customer) {
    //             $balance += $customer->opening_balance + $customer->debit - $customer->credit;
    //             $opening_balance += $customer->opening_balance;
    //         }



    //         $data['total_due'] = round($balance, 2);

    //         $sql2 = "SELECT
    //                 SUM(cl.debit) AS debit,
    //                 SUM(cl.credit) AS credit,
    //                 SUM(cl.balance) AS balance,
    //                 (
    //                     SELECT SUM(cll.debit - cll.credit)
    //                     FROM customer_ledger AS cll
    //                     WHERE c.user_id = cll.customer_id AND cll.date < '" . $to_date . "'
    //                 ) AS opening_balance
    //             FROM customers c
    //             LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id
    //             WHERE (cl.debit > 0 OR cl.credit > 0)";
    //         $sql2 .= " AND c.staff_id = $user_id";

    //         $customers2 = DB::select($sql2);
    //         $due = 0;
    //         $opening_balance = 0;
    //         foreach ($customers2 as $key => $customer) {
    //             $due += $customer->opening_balance + $customer->debit - $customer->credit;
    //         }

    //         $data['all_time_due'] = $due;


    //         $data['sales_target_recovery'] = round(($data['total_sales_amount'] * 100) / ($data['target'] ?: 1), 2);
    //         $data['new_customer'] = Customer::where('staff_id', Auth::user()->id)->whereBetween('updated_at', [$from_date, $to_date])->get()->count();
    //         $data['customer_achivement'] = round(($data['new_customer'] * 100) / ($data['terget_customer'] ?: 1));
    //         $data['performance'] = 0;
    //         $data['total_customer'] = Customer::where('staff_id', Auth::user()->id)->count();
    //         $data['daily_activities'] = Dailyactivity::where('user_id', Auth::user()->id)->where('date', date('Y-m-d'))->get();
    //         $warehouseIds = getWearhouseBuUserId(auth()->user()->id);
    //         $warehouseNames = Warehouse::whereIn('id', $warehouseIds)->pluck('name');
    //         $data['warehousearray'] = $warehouseNames;
    //         return view('backend.sales_executive_dashboard', compact('data'));
    //     } else {
    //         flash("You are not Authorized")->error();
    //         return back();
    //     }
    // }

    public function staff_dashboard(Request $request)
    {
        $user_id = Auth::user()->id;
        $from_date = date('Y-m-01');
        $to_date = date('Y-m-t');
        $today = date('Y-m-d');
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);

        if (auth()->user()->staff->role->name == 'Sales Executive') {
            $data['total_order_qty'] = Customer::where('staff_id', Auth::user()->id)
                ->join('orders', 'customers.user_id', 'orders.user_id')
                ->join('order_details', 'orders.id', 'order_details.order_id')
                ->where('delivery_status', '=', 'delivered')
                ->whereBetween('orders.created_at', [$from_date, $to_date])
                ->count(DB::raw('DISTINCT orders.id'));

            $data['target'] = Target::where('user_id', Auth::user()->id)->where('year', date('Y'))->where('month', date('m'))->sum('target');

            $order_ids = Customer::where('staff_id', Auth::user()->id)
                ->join('orders', 'customers.user_id', 'orders.user_id')
                ->join('order_details', 'orders.id', 'order_details.order_id')
                ->where('delivery_status', '=', 'delivered')
                ->whereBetween('orders.created_at', [$from_date, $to_date])
                ->select(DB::raw('group_concat(DISTINCT orders.id) as ids'))->get();

            if (count($order_ids) > 0)
                $data['total_sales_amount'] = Order::whereIn('id', explode(',', $order_ids[0]['ids']))->sum('grand_total');
            else
                $data['total_sales_amount'] = 0;


            $data['total_POS_sales_amount'] = Order::where('orders.order_from', '=', 'POS')
                ->where('orders.created_at', '>=', $today)
                ->where('orders.warehouse', '=', $warehousearray)
                ->sum('grand_total');


            $data['terget_customer'] = Target::where('user_id', Auth::user()->id)->where('year', date('Y'))->where('month', date('m'))->sum('terget_customer');

            if (count($order_ids) > 0) {
                $getpaidamounts = Order::whereIn('id', explode(',', $order_ids[0]['ids']))
                    ->select('code', 'payment_details')->whereNotNull('payment_details')
                    ->get();
                $paid = 0;
                foreach ($getpaidamounts as $getpaidamount) {
                    if (!empty($getpaidamount->payment_details)) {
                        $payment = json_decode($getpaidamount->payment_details);
                        if (!empty($payment)) {
                            $paid += $payment->amount;
                        }
                    }
                }
            } else {
                $paid = 0;
            }

            $order_ids_for_total_due = Customer::where('staff_id', Auth::user()->id)
                ->join('orders', 'customers.user_id', 'orders.user_id')
                ->join('order_details', 'orders.id', 'order_details.order_id')
                ->where('delivery_status', '=', 'delivered')
                ->select(DB::raw('group_concat(DISTINCT orders.id) as ids'))->get();

            if (count($order_ids_for_total_due) > 0)
                $data['total_sales_amount_for_total_due'] = Order::whereIn('id', explode(',', $order_ids_for_total_due[0]['ids']))->sum('grand_total');
            else
                $data['total_sales_amount_for_total_due'] = 0;

            if (count($order_ids_for_total_due) > 0) {
                $getpaidamounts = Order::whereIn('id', explode(',', $order_ids_for_total_due[0]['ids']))
                    ->select('code', 'payment_details')->whereNotNull('payment_details')
                    ->get();
                $totalpaid = 0;
                foreach ($getpaidamounts as $getpaidamount) {
                    if (!empty($getpaidamount->payment_details)) {
                        $payment = json_decode($getpaidamount->payment_details);
                        if (!empty($payment)) {
                            $totalpaid += $payment->amount;
                        }
                    }
                }
            } else {
                $totalpaid = 0;
            }



            $sql = "SELECT
                u.name,c.user_id,c.customer_id as customer_no,sum(cl.debit) as debit,sum(cl.credit) as credit,sum(cl.balance) as balance,
                (select sum(cll.debit-cll.credit) from customer_ledger as cll where c.user_id=cll.customer_id and cll.date < '" . $from_date . "') as opening_balance
                    FROM
                    customers c
                    LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id
                    LEFT JOIN users u ON c.user_id = u.id";
            $sql .= " where 1=1 ";
            $sql .= " AND c.staff_id = $user_id";
            $sql .= "	and (cl.date between '" . $from_date . "' and '" . $to_date . "' or cl.date is null) ";
            $sql .= "	and (debit>0 or credit>0) 
                        GROUP BY c.customer_id
                        order by u.name asc";

            $customers = DB::select($sql);
            $balance = 0;
            $opening_balance = 0;
            foreach ($customers as $key => $customer) {
                $balance += $customer->opening_balance + $customer->debit - $customer->credit;
                $opening_balance += $customer->opening_balance;
            }



            $data['total_due'] = round($balance, 2);

            $sql2 = "SELECT
                    SUM(cl.debit) AS debit,
                    SUM(cl.credit) AS credit,
                    SUM(cl.balance) AS balance,
                    (
                        SELECT SUM(cll.debit - cll.credit)
                        FROM customer_ledger AS cll
                        WHERE c.user_id = cll.customer_id AND cll.date < '" . $to_date . "'
                    ) AS opening_balance
                FROM customers c
                LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id
                WHERE (cl.debit > 0 OR cl.credit > 0)";
            $sql2 .= " AND c.staff_id = $user_id";

            $customers2 = DB::select($sql2);
            $due = 0;
            $opening_balance = 0;
            foreach ($customers2 as $key => $customer) {
                $due += $customer->opening_balance + $customer->debit - $customer->credit;
            }

            $data['all_time_due'] = $due;


            $data['sales_target_recovery'] = round(($data['total_sales_amount'] * 100) / ($data['target'] ?: 1), 2);
            $data['new_customer'] = Customer::where('staff_id', Auth::user()->id)->whereBetween('updated_at', [$from_date, $to_date])->get()->count();
            $data['customer_achivement'] = round(($data['new_customer'] * 100) / ($data['terget_customer'] ?: 1));
            $data['performance'] = 0;
            $data['total_customer'] = Customer::where('staff_id', Auth::user()->id)->count();
            $data['daily_activities'] = Dailyactivity::where('user_id', Auth::user()->id)->where('date', date('Y-m-d'))->get();
            $warehouseIds = getWearhouseBuUserId(auth()->user()->id);
            $warehouseNames = Warehouse::whereIn('id', $warehouseIds)->pluck('name');
            $data['warehousearray'] = $warehouseNames;
            return view('backend.sales_executive_dashboard', compact('data'));
        } else if (auth()->user()->staff->role->name == 'Delivery Executive') {
            $data['total_order_qty'] = Order::where('delivery_boy', Auth::user()->id)
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereNull('cancel_date')->count();

            $data['delivered_qty'] = Order::where('delivery_boy', Auth::user()->id)
                ->whereBetween('created_at', [$from_date, $to_date])
                ->whereNotNull('delivered_date')->count();

            $data['pending_qty'] = ($data['total_order_qty']) - ($data['delivered_qty']);
            $data['target'] = Target::where('user_id', Auth::user()->id)->where('year', date('Y'))->where('month', date('m'))->sum('target');

            // $data['cash_balance'] = Order::where('delivery_boy',Auth::user()->id)
            // ->whereBetween('orders.created_at',[$from_date,$to_date])
            // ->sum('cash_collection');

            $data['cash_balance'] = DeliveryExecutiveLedger::where('user_id', Auth::user()->id)
                ->where('type', 'Order')->whereBetween('created_at', [$from_date, $to_date])->sum('debit');

            $data['daily_activities'] = Dailyactivity::where('user_id', Auth::user()->id)->where('date', date('Y-m-d'))->get();

            $data['order_daily_activities'] = Order::where('delivery_boy', Auth::user()->id)
                ->whereIn('order_details.delivery_status', ['on_delivery'])
                ->leftjoin('customers', 'customers.user_id', 'orders.user_id')
                ->leftjoin('areas', 'areas.code', '=', 'customers.area_code')
                ->leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')->groupBy('orders.id')
                ->select(
                    'orders.code',
                    'orders.delivery_boy',
                    'orders.warehouse',
                    'orders.grand_total',
                    'orders.id',
                    'orders.user_id',
                    'orders.guest_id',
                    'orders.shipping_address',
                    'orders.cash_collection',
                    'customers.customer_id',
                    'areas.name as areaname',
                    'order_details.delivery_status'
                )->get();

            $data2['delivery_executive_ledger'] = DeliveryExecutiveLedger::where('user_id', Auth::user()->id)
                ->where('type', 'Order')->where('status', 'Pending')->get();

            $accounts_exe = Staff::where('role_id', '15')->WhereIn('warehouse_id', $warehousearray)->get();
            $data['achivement'] = round($data['delivered_qty'] * 100 / ($data['total_order_qty'] ?: 1));
            $data['performance'] = 0;
            $warehouseIds = getWearhouseBuUserId(auth()->user()->id);
            $warehouseNames = Warehouse::whereIn('id', $warehouseIds)->pluck('name');
            $data['warehousearray'] = $warehouseNames;
            return view('backend.delivery_executive_dashboard', compact('data', 'data2', 'warehousearray'));
        } else if (auth()->user()->staff->role->name == 'Operation Manager') {
            $data['total_order_qty'] = Order::WhereNotNull('confirm_date')
                ->WhereNull('cancel_date')
                ->WhereIn('warehouse', $warehousearray)
                ->whereBetween('created_at', [$from_date, $to_date])->count();

            $data['total_delivered_qty'] = Order::WhereIn('warehouse', $warehousearray)
                ->WhereNotNull('delivered_date')->whereBetween('created_at', [$from_date, $to_date])
                ->count();

            $data['pending_qty'] = $data['total_order_qty']  - $data['total_delivered_qty'];

            $data['pending_qty_old'] = Order::WhereIn('warehouse', $warehousearray)
                ->WhereNull('delivered_date')->WhereNotNull('confirm_date')
                ->WhereNotNull('on_delivery_date')->whereBetween('created_at', [$from_date, $to_date])
                ->count();

            $data['replacement_product'] = RefundRequest::leftjoin('order_details', 'refund_requests.order_detail_id', 'order_details.id')
                ->leftjoin('orders', 'order_details.order_id', 'orders.id')
                ->select('order_details.quantity', 'orders.warehouse')->whereBetween('refund_requests.created_at', [$from_date, $to_date])
                ->WhereIn('warehouse', $warehousearray)
                ->whereIn('refund_requests.refund_status', [2, 3, 4])
                ->sum('quantity');

            $data['damage_qty'] = Damage::WhereIn('wearhouse_id', $warehousearray)
                ->whereBetween('created_at', [$from_date, $to_date])
                ->sum('qty');

            $data['achivement'] = round(($data['total_delivered_qty'] * 100) / ($data['total_order_qty'] ?: 1));
            $data['target'] = Target::where('user_id', Auth::user()->id)->where('year', date('Y'))->where('month', date('m'))->sum('target');

            $data['daily_activities'] = Dailyactivity::where('user_id', Auth::user()->id)->where('date', date('Y-m-d'))->get();
            $data['order_daily_activities'] = Order::whereIn('order_details.delivery_status', ['confirmed'])->WhereIn('warehouse', $warehousearray)
                ->leftjoin('customers', 'customers.user_id', 'orders.user_id')
                ->leftjoin('areas', 'areas.code', '=', 'customers.area_code')
                ->groupBy('orders.id')
                ->leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')
                ->select(
                    'orders.code',
                    'orders.delivery_boy',
                    'orders.warehouse',
                    'orders.grand_total',
                    'orders.id',
                    'orders.user_id',
                    'orders.guest_id',
                    'orders.shipping_address',
                    'customers.customer_id',
                    'areas.name as areaname',
                    'order_details.delivery_status'
                )->get();
            $data['performance'] = 0;
            $warehouseIds = getWearhouseBuUserId(auth()->user()->id);
            $warehouseNames = Warehouse::whereIn('id', $warehouseIds)->pluck('name');
            $data['warehousearray'] = $warehouseNames;
            return view('backend.operation_manager_dashboard', compact('data'));
        } else if (auth()->user()->staff->role->name == 'Purchase Executive') {

            $data['total_purchase_qty'] = Purchase::leftjoin('purchase_details', 'purchases.id', 'purchase_details.id')
                ->whereBetween('purchases.created_at', [$from_date, $to_date])
                ->where('created_by', Auth::user()->id)
                ->where('status', '2')->select('purchase_details.qty')->sum('qty');

            $data['total_purchase_amount'] = Purchase::leftjoin('purchase_details', 'purchases.id', 'purchase_details.id')
                ->whereBetween('purchases.created_at', [$from_date, $to_date])
                ->where('created_by', Auth::user()->id)
                ->where('status', '2')->select('amount')->sum('amount');

            $data['damage_product_qty'] = Damage::whereBetween('created_at', [$from_date, $to_date])
                ->where('status', 'Approved')
                ->select('amount')->sum('qty');

            $data['damage_product_amount'] = Damage::whereBetween('created_at', [$from_date, $to_date])
                ->where('status', 'Approved')
                ->select('total_amount')->sum('total_amount');

            $data['achivement'] = 100 - round(($data['damage_product_qty']) / ($data['total_purchase_qty'] ?: 1) * 100, 2);

            $data['new_supplier'] = Supplier::where('staff_id', Auth::user()->id)
                ->whereBetween('created_at', [$from_date, $to_date])->where('status', 1)->count();
            $data['performance'] = 0;

            $data['total_supplier'] = Supplier::where('staff_id', Auth::user()->id)
                ->where('status', 1)->count();

            $data['daily_activities'] = Dailyactivity::where('user_id', Auth::user()->id)->where('date', date('Y-m-d'))->get();
            $data['purchasedetails'] = Purchase::where('purchases.status', 2)->where('purchases.created_by', Auth::user()->id)->where('date', date('Y-m-d'))
                ->rightjoin('suppliers', 'suppliers.supplier_id', 'purchases.supplier_id')
                ->select('suppliers.supplier_id', 'suppliers.name', 'suppliers.address', 'suppliers.phone', 'purchases.payment_amount', 'purchases.total_value', 'purchases.purchase_no', 'purchases.id')->get();
            $warehouseIds = getWearhouseBuUserId(auth()->user()->id);
            $warehouseNames = Warehouse::whereIn('id', $warehouseIds)->pluck('name');
            $data['warehousearray'] = $warehouseNames;
            return view('backend.purchase_executive_dashboard', compact('data'));
        } else if (auth()->user()->staff->role->name == 'Purchase Manager') {

            $data['total_purchase_qty'] = Purchase::leftjoin('purchase_details', 'purchases.id', 'purchase_details.id')
                ->where('purchases.created_by', Auth::user()->id)
                ->whereBetween('purchases.created_at', [$from_date, $to_date])
                ->where('purchases.status', '2')
                ->select('purchase_details.qty')
                ->sum('qty');

            $data['total_purchase_amount'] = Purchase::leftjoin('purchase_details', 'purchases.id', 'purchase_details.id')
                ->where('purchases.created_by', Auth::user()->id)
                ->whereBetween('purchases.created_at', [$from_date, $to_date])
                ->where('status', '2')->select('amount')->sum('amount');

            $data['damage_product_qty'] = Damage::whereBetween('created_at', [$from_date, $to_date])
                ->where('status', 'Approved')
                ->select('amount')->sum('qty');

            $data['damage_product_amount'] = Damage::whereBetween('created_at', [$from_date, $to_date])
                ->where('status', 'Approved')
                ->select('total_amount')->sum('total_amount');

            $data['total_supplier'] = Supplier::where('staff_id', Auth::user()->id)
                ->where('status', 1)->count();

            $data['new_supplier'] = Supplier::where('staff_id', Auth::user()->id)
                ->whereBetween('created_at', [$from_date, $to_date])->count();
            $data['total_supplier_creadit'] = 0;

            $user_id = Auth::user()->id;

            $data['total_supplier_cr'] = " SELECT 
                        SUM(supplier_ledger.credit) AS cred,
                        SUM(supplier_ledger.debit) AS deb,
                        SUM(supplier_ledger.debit) - SUM(supplier_ledger.credit) AS total
                    FROM 
                        supplier_ledger
                    JOIN 
                        suppliers ON suppliers.supplier_id = supplier_ledger.supplier_id
                    WHERE 
                        suppliers.staff_id = $user_id";



            $data['total_creadit'] = DB::select($data['total_supplier_cr']);
            $data['final_total_creadit'] = $data['total_creadit'][0]->total;


            $data['achivement'] = 0;
            $data['performance'] = 0;

            $data['daily_activities'] = Dailyactivity::where('user_id', Auth::user()->id)->where('date', date('Y-m-d'))->get();
            $data['purchasedetails'] = Purchase::where('purchases.status', 2)
                ->where('purchases.created_by', Auth::user()->id)
                ->where('date', date('Y-m-d'))->wherein('wearhouse_id', $warehousearray)
                ->rightjoin('suppliers', 'suppliers.supplier_id', 'purchases.supplier_id')
                ->select('suppliers.supplier_id', 'suppliers.name', 'suppliers.address', 'suppliers.phone', 'purchases.payment_amount', 'purchases.total_value', 'purchases.purchase_no', 'purchases.id')->get();
            $warehouseIds = getWearhouseBuUserId(auth()->user()->id);
            $warehouseNames = Warehouse::whereIn('id', $warehouseIds)->pluck('name');
            $data['warehousearray'] = $warehouseNames;
            return view('backend.purchase_manager_dashboard', compact('data'));
        } else if (auth()->user()->staff->role->name == 'Account Executive' || auth()->user()->staff->role->name == 'Account Manager') {
            $data['total_order_qty'] = Order::where('delivery_boy', Auth::user()->id)->whereNull('delivered_date')->count();
            $data['delivered_qty'] = Order::where('delivery_boy', Auth::user()->id)->whereNotNull('delivered_date')->count();
            $data['target'] = Target::where('user_id', Auth::user()->id)
                ->where('year', date('Y'))
                ->where('month', date('m'))
                ->sum('target');
            $data['achivement'] = 0;
            $data['pending_qty'] = 0;
            $data['performance'] = 0;
            $data['cash_balance'] = 0;
            $data['daily_activities'] = Dailyactivity::where('user_id', Auth::user()->id)
                ->where('date', date('Y-m-d'))
                ->get();

            // Filtering order daily activities
            $orderQuery = Order::where('delivery_boy', Auth::user()->id)
                ->where('orders.created_at', '>=', date('Y-m-d') . ' 00:00:00')
                ->where('orders.created_at', '<=', date('Y-m-d') . ' 23:59:59')
                ->whereIn('order_details.delivery_status', ['on_delivery', 'delivered'])
                ->leftJoin('customers', 'customers.user_id', 'orders.user_id')
                ->leftJoin('areas', 'areas.code', '=', 'customers.area_code')
                ->leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')
                ->groupBy('orders.id')
                ->select('orders.code', 'orders.delivery_boy', 'orders.warehouse', 'orders.grand_total', 'orders.id', 'orders.user_id', 'orders.guest_id', 'orders.shipping_address', 'orders.cash_collection', 'customers.customer_id', 'areas.name as areaname', 'order_details.delivery_status');

            if ($request->has('user_id') && !empty($request->user_id)) {
                $orderQuery->where('orders.delivery_boy', $request->user_id);
            }

            if ($request->has('status') && !empty($request->status)) {
                $orderQuery->where('order_details.delivery_status', $request->status);
            }

            $data['order_daily_activities'] = $orderQuery->get();

            // Filtering delivery executive ledger
            $ledgerQuery = DeliveryExecutiveLedger::where('note', Auth::user()->id)
                ->leftjoin('orders', 'orders.code', 'delivery_executive_ledger.order_no')
                ->select('delivery_executive_ledger.*', 'orders.user_id as customer_id', 'orders.id as order_id', 'orders.paid_amount as order_paid_amount')
                ->where('status', 'Pending')
                ->where('type', 'Payment')
                ->orderBy('created_at', 'desc');

            if ($request->has('user_id') && !empty($request->user_id)) {
                $ledgerQuery->where('user_id', $request->user_id);
            }

            if ($request->has('status') && !empty($request->status)) {
                $ledgerQuery->where('status', $request->status);
            }

            $data2['delivery_executive_ledger'] = $ledgerQuery->get();


            $warehouseIds = getWearhouseBuUserId(auth()->user()->id);
            $warehouseNames = Warehouse::whereIn('id', $warehouseIds)->pluck('name');
            $data['warehousearray'] = $warehouseNames;

            return view('backend.account_executive_dashboard', compact('data', 'data2'));
        } else if (auth()->user()->staff->role->name == 'Customer Service Executive') {

            $userId = Auth::user()->id;
            $today = date('Y-m-d');

            $data1['customer_service_orders'] = CustomerServiceOrder::where('user_id', $userId)
                ->where('date', $today)
                ->get();

            $data1['customerServiceOrder'] = Order::leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')
                ->leftJoin('customers', 'customers.user_id', 'orders.user_id')
                ->leftJoin('areas', 'areas.code', '=', 'customers.area_code')
                ->where('order_details.delivery_status', 'pending')
                ->groupBy('orders.id')
                ->select('orders.code', 'orders.warehouse', 'orders.grand_total', 'orders.id', 'orders.user_id', 'orders.guest_id', 'orders.shipping_address', 'customers.customer_id', 'customers.staff_id', 'areas.name as areaname', 'order_details.delivery_status')
                ->get();

            $customerIds = Customer::where('staff_id', $userId)->pluck('user_id');

            $deliveredOrderIds = Order::whereIn('user_id', $customerIds)
                ->whereHas('orderDetails', function ($q) {
                    $q->where('delivery_status', 'delivered');
                })
                ->whereBetween('created_at', [$from_date, $to_date])
                ->pluck('id');

            $data['total_order_qty'] = $deliveredOrderIds->count();

            $data['target'] = Target::where('user_id', $userId)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('target');
            $data['recovery_target'] = Target::where('user_id', $userId)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('recovery_target');

            $data['total_sales_amount'] = Order::whereIn('id', $deliveredOrderIds)->sum('grand_total');

            $posOrderIds = Order::whereIn('user_id', $customerIds)
                ->where('order_from', 'POS')
                ->where('payment_status', 'paid')
                ->whereBetween('created_at', [$from_date, $to_date])
                ->pluck('id');

            $data['total_POS_sales_amount'] = Order::whereIn('id', $posOrderIds)->sum('grand_total');

            $data['terget_customer'] = Target::where('user_id', $userId)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('terget_customer');

            // Paid amount from delivered order_ids
            $paid = Order::whereIn('id', $deliveredOrderIds)
                ->whereNotNull('payment_details')
                ->get()
                ->sum(function ($order) {
                    $payment = json_decode($order->payment_details);
                    return $payment->amount ?? 0;
                });

            $data['total_sales_amount_for_total_due'] = $data['total_sales_amount'];

            $totalpaid = $paid;

            // Optimized customer ledger raw SQL query
            $sql = "
                SELECT
                    u.name,
                    c.user_id,
                    c.customer_id as customer_no,
                    SUM(cl.debit) as debit,
                    SUM(cl.credit) as credit,
                    SUM(cl.balance) as balance,
                    (
                        SELECT SUM(cll.debit - cll.credit)
                        FROM customer_ledger AS cll
                        WHERE c.user_id = cll.customer_id
                        AND cll.date < ?
                    ) as opening_balance
                FROM customers c
                LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.staff_id = ?
                    AND (cl.date BETWEEN ? AND ? OR cl.date IS NULL)
                    AND (debit > 0 OR credit > 0)
                GROUP BY c.customer_id
                ORDER BY u.name ASC
            ";

            $customers = DB::select($sql, [$from_date, $userId, $from_date, $to_date]);

            $balance = 0;
            $opening_balance = 0;
            foreach ($customers as $customer) {
                $balance += ($customer->opening_balance ?? 0) + ($customer->debit ?? 0) - ($customer->credit ?? 0);
                $opening_balance += $customer->opening_balance ?? 0;
            }

            $data['total_due'] = round($balance, 2);

            // All-time due optimized
            $sql2 = "
                SELECT
                    SUM(cl.debit) AS debit,
                    SUM(cl.credit) AS credit,
                    SUM(cl.balance) AS balance,
                    (
                        SELECT SUM(cll.debit - cll.credit)
                        FROM customer_ledger AS cll
                        WHERE c.user_id = cll.customer_id
                        AND cll.date < ?
                    ) AS opening_balance
                FROM customers c
                LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id
                WHERE (cl.debit > 0 OR cl.credit > 0)
                AND c.staff_id = ?
            ";

            $customers2 = DB::select($sql2, [$to_date, $userId]);

            $due = 0;
            foreach ($customers2 as $customer) {
                $due += ($customer->opening_balance ?? 0) + ($customer->debit ?? 0) - ($customer->credit ?? 0);
            }

            $data['all_time_due'] = $due;

            $data['target_credit_recovery'] = $data['total_sales_amount'] - $data['total_due'];
            $data['achivement'] = round(($data['total_sales_amount'] * 100) / max($data['target'], 1));

            $data['new_customer'] = Customer::where('staff_id', $userId)->whereBetween('updated_at', [$from_date, $to_date])->count();

            $data['customer_achivement'] = round(($data['new_customer'] * 100) / max($data['terget_customer'], 1));

            $data['performance'] = 0;
            $data['total_customer'] = Customer::where('staff_id', $userId)->count();

            $data['daily_activities'] = Dailyactivity::where('user_id', $userId)->where('date', $today)->get();

            $warehouseIds = getWearhouseBuUserId($userId);
            $data['warehousearray'] = Warehouse::whereIn('id', $warehouseIds)->pluck('name');

            return view('backend.customer_service_dashboard', compact('data', 'data1'));
        } else if (auth()->user()->staff->role->name == 'Sales Manager') {
            $data['total_order_qty'] = Customer::where('staff_id', Auth::user()->id)
                ->join('orders', 'customers.user_id', 'orders.user_id')
                ->join('order_details', 'orders.id', 'order_details.order_id')
                ->where('delivery_status', '=', 'delivered')
                ->whereBetween('orders.created_at', [$from_date, $to_date])
                ->count(DB::raw('DISTINCT orders.id'));

            $data['target'] = Target::where('user_id', Auth::user()->id)->where('year', date('Y'))->where('month', date('m'))->sum('target');

            $order_ids = Customer::where('staff_id', Auth::user()->id)
                ->join('orders', 'customers.user_id', 'orders.user_id')
                ->join('order_details', 'orders.id', 'order_details.order_id')
                ->where('delivery_status', '=', 'delivered')
                ->whereBetween('orders.created_at', [$from_date, $to_date])
                ->select(DB::raw('group_concat(DISTINCT orders.id) as ids'))->get();

            if (count($order_ids) > 0)
                $data['total_sales_amount'] = Order::whereIn('id', explode(',', $order_ids[0]['ids']))->sum('grand_total');
            else
                $data['total_sales_amount'] = 0;

            $order_ids_POS = Customer::where('staff_id', Auth::user()->id)
                ->join('orders', 'customers.user_id', 'orders.user_id')
                ->join('order_details', 'orders.id', 'order_details.order_id')
                ->where('order_from', '=', 'POS')
                ->where('orders.payment_status', '=', 'paid')
                ->whereBetween('orders.created_at', [$from_date, $to_date])
                ->select(DB::raw('group_concat(DISTINCT orders.id) as ids'))->get();

            if (count($order_ids_POS) > 0) {
                $data['total_POS_sales_amount'] = Order::whereIn('id', explode(',', $order_ids_POS[0]['ids']))
                    ->sum('grand_total');
            } else {
                $data['total_POS_sales_amount'] = 0;
            }

            $data['terget_customer'] = Target::where('user_id', Auth::user()->id)->where('year', date('Y'))->where('month', date('m'))->sum('terget_customer');

            if (count($order_ids) > 0) {
                $getpaidamounts = Order::whereIn('id', explode(',', $order_ids[0]['ids']))
                    ->select('code', 'payment_details')->whereNotNull('payment_details')
                    ->get();
                $paid = 0;
                foreach ($getpaidamounts as $getpaidamount) {
                    if (!empty($getpaidamount->payment_details)) {
                        $payment = json_decode($getpaidamount->payment_details);
                        if (!empty($payment)) {
                            $paid += $payment->amount;
                        }
                    }
                }
            } else {
                $paid = 0;
            }

            $order_ids_for_total_due = Customer::where('staff_id', Auth::user()->id)
                ->join('orders', 'customers.user_id', 'orders.user_id')
                ->join('order_details', 'orders.id', 'order_details.order_id')
                ->where('delivery_status', '=', 'delivered')
                ->select(DB::raw('group_concat(DISTINCT orders.id) as ids'))->get();

            if (count($order_ids_for_total_due) > 0)
                $data['total_sales_amount_for_total_due'] = Order::whereIn('id', explode(',', $order_ids_for_total_due[0]['ids']))->sum('grand_total');
            else
                $data['total_sales_amount_for_total_due'] = 0;

            if (count($order_ids_for_total_due) > 0) {
                $getpaidamounts = Order::whereIn('id', explode(',', $order_ids_for_total_due[0]['ids']))
                    ->select('code', 'payment_details')->whereNotNull('payment_details')
                    ->get();
                $totalpaid = 0;
                foreach ($getpaidamounts as $getpaidamount) {
                    if (!empty($getpaidamount->payment_details)) {
                        $payment = json_decode($getpaidamount->payment_details);
                        if (!empty($payment)) {
                            $totalpaid += $payment->amount;
                        }
                    }
                }
            } else {
                $totalpaid = 0;
            }



            $sql = "SELECT
                u.name,c.user_id,c.customer_id as customer_no,sum(cl.debit) as debit,sum(cl.credit) as credit,sum(cl.balance) as balance,
                (select sum(cll.debit-cll.credit) from customer_ledger as cll where c.user_id=cll.customer_id and cll.date < '" . $from_date . "') as opening_balance
                    FROM
                    customers c
                    LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id
                    LEFT JOIN users u ON c.user_id = u.id";
            $sql .= " where 1=1 ";
            $sql .= " AND c.staff_id = $user_id";
            $sql .= "	and (cl.date between '" . $from_date . "' and '" . $to_date . "' or cl.date is null) ";
            $sql .= "	and (debit>0 or credit>0) 
                        GROUP BY c.customer_id
                        order by u.name asc";

            $customers = DB::select($sql);
            $balance = 0;
            $opening_balance = 0;
            foreach ($customers as $key => $customer) {
                $balance += $customer->opening_balance + $customer->debit - $customer->credit;
                $opening_balance += $customer->opening_balance;
            }



            $data['total_due'] = round($balance, 2);

            $sql2 = "SELECT
                    SUM(cl.debit) AS debit,
                    SUM(cl.credit) AS credit,
                    SUM(cl.balance) AS balance,
                    (
                        SELECT SUM(cll.debit - cll.credit)
                        FROM customer_ledger AS cll
                        WHERE c.user_id = cll.customer_id AND cll.date < '" . $to_date . "'
                    ) AS opening_balance
                FROM customers c
                LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id
                WHERE (cl.debit > 0 OR cl.credit > 0)";
            $sql2 .= " AND c.staff_id = $user_id";

            $customers2 = DB::select($sql2);
            $due = 0;
            $opening_balance = 0;
            foreach ($customers2 as $key => $customer) {
                $due += $customer->opening_balance + $customer->debit - $customer->credit;
            }

            $data['all_time_due'] = $due;


            $data['sales_target_recovery'] = round(($data['total_sales_amount'] * 100) / ($data['target'] ?: 1), 2);
            $data['new_customer'] = Customer::where('staff_id', Auth::user()->id)->whereBetween('updated_at', [$from_date, $to_date])->get()->count();
            $data['customer_achivement'] = round(($data['new_customer'] * 100) / ($data['terget_customer'] ?: 1));
            $data['performance'] = 0;
            $data['total_customer'] = Customer::where('staff_id', Auth::user()->id)->count();
            $data['daily_activities'] = Dailyactivity::where('user_id', Auth::user()->id)->where('date', date('Y-m-d'))->get();
            $warehouseIds = getWearhouseBuUserId(auth()->user()->id);
            $warehouseNames = Warehouse::whereIn('id', $warehouseIds)->pluck('name');
            $data['warehousearray'] = $warehouseNames;
            return view('backend.sales_executive_dashboard', compact('data'));
        } else {
            flash("You are not Authorized")->error();
            return back();
        }
    }

    function activity_save(Request $request)
    {
        $dailyactivity = array();
        Dailyactivity::where('user_id', Auth::user()->id)->where('date', date('Y-m-d'))->delete();
        foreach ($request->phone as $key => $each) {

            $dailyactivity[] = array(
                'user_id' => Auth::user()->id,
                'date' => date('Y-m-d'),
                'phone' => !empty($request->phone[$key]) ? $request->phone[$key] : '',
                'name' => !empty($request->name[$key]) ? $request->name[$key] : '',
                'customer_id' => !empty($request->id[$key]) ? $request->id[$key] : '',
                'area' => !empty($request->area[$key]) ? $request->area[$key] : '',
                'address' => !empty($request->address[$key]) ? $request->address[$key] : '',
                'type' => !empty($request->type[$key]) ? $request->type[$key] : '',
                'comment' => !empty($request->comment[$key]) ? $request->comment[$key] : '',
                'complain' => !empty($request->complain[$key]) ? $request->complain[$key] : '',
                'order_confirm' => !empty($request->order_confirm[$key]) ? $request->order_confirm[$key] : '',
                'order_id' => !empty($request->order_id[$key]) ? $request->order_id[$key] : '',
                'amount' => !empty($request->amount[$key]) ? $request->amount[$key] : '',
                'cash_collection' => !empty($request->cash_collection[$key]) ? $request->cash_collection[$key] : '',
                'status' => !empty($request->status[$key]) ? $request->status[$key] : '',
                'delivery_man' => !empty($request->delivery_man[$key]) ? $request->delivery_man[$key] : '',
                'paid_amount' => !empty($request->paid_amount[$key]) ? $request->paid_amount[$key] : '',
                'purchase_amount' => !empty($request->purchase_amount[$key]) ? $request->purchase_amount[$key] : '',
                'due_balance' => !empty($request->due_balance[$key]) ? $request->due_balance[$key] : '',
                'werehouse' => !empty($request->werehouse[$key]) ? $request->werehouse[$key] : '',
                'order_no' => !empty($request->order_no[$key]) ? $request->order_no[$key] : '',
                'created_at' => date('Y-m-d H:i:s')
            );

            if (auth()->user()->staff->role->name == 'Operation Manager') {
                $order = Order::findOrFail($request->order_id[$key]);
                $order->delivery_boy = $request->delivery_man[$key];
                $order->save();
            }

            if (auth()->user()->staff->role->name == 'Delivery Executive') {

                DeliveryExecutiveLedger::where('order_no', $request->order_no[$key])
                    ->where('type', 'Order')->where('date', date('Y-m-d'))->where('due_status', 0)->delete();
                $delivery_executive_ledger = new DeliveryExecutiveLedger;
                $delivery_executive_ledger->user_id = Auth::user()->id;
                $delivery_executive_ledger->name = $request->name[$key];
                $delivery_executive_ledger->order_no = $request->order_no[$key];
                $delivery_executive_ledger->date = date('Y-m-d');
                $delivery_executive_ledger->debit = $request->cash_collection[$key];
                $delivery_executive_ledger->type = "Order";
                $delivery_executive_ledger->due_status = 0;
                $delivery_executive_ledger->save();
                $order = Order::findOrFail($request->order_id[$key]);
                $order->cash_collection = $request->cash_collection[$key];
                $order->save();
            }
            if (auth()->user()->staff->role->name == 'Purchase Executive' || auth()->user()->staff->role->name == 'Purchase Manager') {

                if (!empty($request->paid_amount[$key])) {
                    if ($request->due_balance[$key] > 0) {
                        $status = 2;
                    } else {
                        $status = 3;
                    }
                    $purchase = Purchase::findOrFail($request->purchase_id[$key]);
                    $purchase->payment_amount = $request->paid_amount[$key];
                    $purchase->payment_status = $status;
                    $purchase->save();
                }
            }
        }

        Dailyactivity::insert($dailyactivity);
        flash("Daily Activity Saved Successfully")->success();
        return back();
    }

    function customer_service_activity_save(Request $request)
    {

        $customer_service_order = array();
        CustomerServiceOrder::where('user_id', Auth::user()->id)->where('date', date('Y-m-d'))->delete();
        foreach ($request->phone as $key => $each) {
            $customer_service_order[] = array(
                'user_id' => Auth::user()->id,
                'date' => date('Y-m-d'),
                'order_no' => !empty($request->order_no[$key]) ? $request->order_no[$key] : '',
                'name' => !empty($request->name[$key]) ? $request->name[$key] : '',
                'customer_id' => !empty($request->customer_id[$key]) ? $request->customer_id[$key] : '',
                'phone' => !empty($request->phone[$key]) ? $request->phone[$key] : '',
                'address' => !empty($request->address[$key]) ? $request->address[$key] : '',
                'amount' => !empty($request->amount[$key]) ? $request->amount[$key] : '',
                'warehouse' => !empty($request->warehouse[$key]) ? $request->warehouse[$key] : '',
                'status' => !empty($request->status[$key]) ? $request->status[$key] : '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );

            $order = Order::where('id', $request->order_id[$key])->first();
            $order->warehouse = $request->warehouse[$key];
            $order->save();
            // foreach ($order->orderDetails as $key1 => $orderDetail) {
            //     $orderDetail->delivery_status = $request->status[$key];
            //     $orderDetail->save();
            // }

        }

        CustomerServiceOrder::insert($customer_service_order);
        flash("Customer Activity Saved Successfully")->success();
        return back();
    }

    function fordelivery_executive_ledger(Request $request)
    {

        $fordelivery_executive_ledger = array();
        DeliveryExecutiveLedger::where('user_id', Auth::user()->id)
            ->where('type', 'Payment')->delete();
        foreach ($request->order_no as $key => $each) {
            $fordelivery_executive_ledger[] = array(
                'user_id' => Auth::user()->id,
                'name' => !empty($request->name[$key]) ? $request->name[$key] : '',
                'order_no' => !empty($request->order_no[$key]) ? $request->order_no[$key] : '',
                'type' => "Payment",
                'note' => !empty($request->note[$key]) ? $request->note[$key] : '',
                'credit' => !empty($request->credit[$key]) ? $request->credit[$key] : '',
                'date' => !empty($request->date[$key]) ? $request->date[$key] : '',
                'status' => !empty($request->status[$key]) ? $request->status[$key] : '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );
        }

        DeliveryExecutiveLedger::insert($fordelivery_executive_ledger);
        flash("Amount Saved Successfully")->success();
        return back();
    }


    public function staff_customers(Request $request)
    {

        $sort_search = null;
        $customers = Customer::orderBy('created_at', 'desc');
        $customers = $customers->join('areas', 'areas.code', '=', 'customers.area_code');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $user_ids = User::where('user_type', 'customer')->join('customers', 'users.id', '=', 'customers.user_id')->where(function ($user) use ($sort_search) {
                $user->where('name', 'like', '%' . $sort_search . '%')->orWhere('email', 'like', '%' . $sort_search . '%')->orWhere('customer_id', 'like', '%' . $sort_search . '%')->orWhere('phone', 'like', '%' . $sort_search . '%')->orWhere('customer_type', 'like', '%' . $sort_search . '%');
            })->pluck('users.id')->toArray();
            $customers = $customers->where(function ($customer) use ($user_ids) {
                $customer->whereIn('user_id', $user_ids);
            });
        }

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
            $customers->whereBetween('customers.created_at', [$start_date, $end_date]);
        }

        $start_date = !empty($start_date) ? date('Y-m-d', strtotime($start_date)) : '';
        $end_date = !empty($end_date) ? date('Y-m-d', strtotime($end_date)) : '';
        $customers->select('customers.*', 'areas.name as areacode');
        $customers->where('staff_id', Auth::user()->id);
        $customers = $customers->paginate(15);
        return view('backend.staff_panel.sales_executives_customers', compact('customers', 'sort_search', 'start_date', 'end_date',));
    }

    public function staff_customer_ledger(Request $request)
    {
        $wearhouse = null;
        $sort_by = null;
        $status = null;
        $sort_search = '';
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        if (empty($request->start_date))
            $request->start_date = $start_date;
        if (empty($request->end_date))
            $request->end_date = $end_date;
        $cust = array();
        $orders = array();

        $sql = "SELECT
        u.name,c.user_id,c.customer_id as customer_no,sum(cl.debit) as debit,sum(cl.credit) as credit,sum(cl.balance) as balance,
   (select sum(cll.debit-cll.credit) from customer_ledger as cll where c.user_id=cll.customer_id and cll.date < '" . $request->start_date . "') as opening_balance, cl.order_id
        FROM
        customers c
        LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id
        LEFT JOIN users u ON c.user_id = u.id
        LEFT JOIN orders ors ON cl.order_id = ors.id";
        $sql .= " where 1=1 and staff_id=" . Auth::user()->id;
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d', strtotime($request->end_date));
            $sql .= "	and (cl.date between '" . $start_date . "' and '" . $end_date . "' or cl.date is null) ";
        }
        if ($request->has('search')) {
            $sort_search = $s = $request->search;
            $sql .= " and (u.email like '%" . $s . "%' or u.name like '%" . $s . "%' or u.phone like '%" . $s . "%' or c.customer_id like '%" . $s . "%') ";
        }
        if (!empty($request->warehouse)) {
            $warehouse = $request->warehouse;
            $sql .= " AND ors.warehouse = '" . $warehouse . "'";
        }

        $sql .= "	and (debit>0 or credit>0) 
        GROUP BY c.customer_id
        order by u.name asc";
        $customers = DB::select($sql);
        return view('backend.staff_panel.staff_customer_ledger_main', compact('customers', 'sort_search', 'start_date', 'end_date', 'wearhouse'));
    }

    public function staff_customer_ledger_details(Request $request)
    {
        $cust_id = $request->cust_id;
        if (empty($cust_id))
            $cust_id = $request->customer_id;
        if (empty($cust_id))
            return Redirect::back();

        $start_date = !empty($request->start_date) ? $request->start_date : date('Y-m-01');
        $end_date = !empty($request->end_date) ? $request->end_date : date('Y-m-t');

        $cust = User::where('id', $cust_id)->first();
        $sql = "SELECT
        u.name,o.code,cl.order_id,cl.date,cl.type,cl.descriptions,c.user_id,c.customer_id as customer_no,cl.debit as debit,cl.credit as credit,cl.balance as balance
    FROM
        customers c
        LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id
        LEFT JOIN orders o ON cl.order_id = o.id
        LEFT JOIN users u ON c.user_id = u.id";
        $sql .= "	where c.user_id=$cust_id and cl.date between '" . $start_date . "' and '" . $end_date . "'";

        $sql .= " order by cl.date asc,cl.order_id DESC,
		 CASE 
			WHEN cl.type='Order' THEN 1 
			WHEN cl.type='Discount' THEN 2
			WHEN cl.type='Payment' THEN 3	
		 END ASC 
		";

        $customers = DB::select($sql);

        $sql = "SELECT sum(cl.debit-cl.credit) as opening_balance
    FROM
        customers c
	LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id";
        $sql .= "	where c.user_id=$cust_id and cl.date < '" . $start_date . "'";

        $opening = DB::select($sql);

        return view('backend.staff_panel.customer_ledger', compact('customers', 'cust', 'start_date', 'end_date', 'opening'));
    }

    public function staff_sales_report(Request $request)
    {
        $wearhouse = null;
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        // $start_date = date('Y-m-d');
        // $end_date = date('Y-m-d');
        $date = $request->date;
        $sort_search = null;
        $warehousearray = getWearhouseBuUserId(Auth::user()->id);

        $orders = Order::select('orders.*', 'customers.staff_id')
            ->Join('customers', 'customers.user_id', '=', 'orders.user_id')->where('customers.staff_id', Auth::user()->id)
            ->where('orders.delivered_by', '>', '0')
            ->whereNull('orders.canceled_by')
            ->orderBy('orders.created_at', 'ASC');

        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('orders.code', 'like', '%' . $sort_search . '%');
        }
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d 00:00:00', strtotime($request->start_date));
            $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
        }
        if (!empty($request->warehouse)) {
            $wearhouse = $request->warehouse;
            $orders = $orders->where('orders.warehouse', $wearhouse);
        }
        $orders = $orders->whereBetween('orders.delivered_date', [$start_date, $end_date]);


        $orders = $orders->get();
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));

        return view('backend.staff_panel.staff_sales_report', compact('orders', 'sort_search', 'date', 'start_date', 'end_date', 'wearhouse', 'warehousearray'));
    }

    public function staff_product_sales_report(Request $request)
    {
        $sort_by = null;
        $pro_sort_by = null;
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
            ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('customers', 'orders.user_id', '=', 'customers.user_id')
            ->where('num_of_sale', '>', '0')
            ->where('customers.staff_id', Auth::user()->id)
            ->select('products.name as product_name', 'categories.name as category_name', DB::raw('sum(order_details.price) AS price'), DB::raw('sum(quantity) AS quantity'), DB::raw('count(product_id) AS num_of_sale'))->groupBy('products.id')->orderBy('num_of_sale', 'desc');
        if ($request->has('category_id') && !empty($request->category_id)) {
            $sort_by = $request->category_id;
            $products = $products->where('category_id', $sort_by);
        }
        if (!empty($request->product_id)) {
            $pro_sort_by = $request->product_id;
            $products = $products->where('products.id', $pro_sort_by);
        }
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = $request->start_date;
            $end_date = date('Y-m-d', strtotime($request->end_date . ' +1 day'));
            $products = $products->whereBetween('orders.date', [strtotime($start_date), strtotime($end_date)])->select('products.name as product_name', 'categories.name as category_name', DB::raw('sum(order_details.price) AS price'), DB::raw('sum(quantity) AS quantity'), DB::raw('count(product_id) AS num_of_sale'))->groupBy('products.id')->orderBy('num_of_sale', 'desc');
        }
        $products->whereNotIn('order_details.delivery_status', ['cancel']);
        $products = $products->get();
        if (!empty($request->end_date))
            $end_date = date('Y-m-d', strtotime($end_date . ' -1 day'));

        return view('backend.staff_panel.staff_product_sales_report', compact('products', 'sort_by', 'pro_sort_by', 'start_date', 'end_date'));
    }

    public function staff_refund(Request $request)
    {
        $warehouseid = $request->warehouse;
        $start_date = date('Y-m-01 00:00:00');
        $end_date = date('Y-m-t 23:59:59');

        $refunds = RefundRequest::latest()
            ->leftJoin('orders', 'orders.id', '=', 'refund_requests.order_id')
            ->select('refund_requests.*', 'orders.shipping_address');

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d 00:00:00', strtotime($request->start_date));
            $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
        }
        if (auth()->user()->staff->role->name == 'Delivery Executive') {
            $refunds = $refunds->where('refund_requests.delivery_boy', Auth::user()->id);
        }

        if (auth()->user()->staff->role->name == 'Operation Manager') {
            $refunds = $refunds->whereIn('refund_requests.refund_status', [1, 3, 4, 5]);
        }

        if (!empty($warehouseid)) {
            $refunds =  $refunds->whereBetween('refund_requests.created_at', [$start_date, $end_date])
                ->Where('orders.warehouse', $warehouseid);
        } else {
            $refunds =  $refunds->whereBetween('refund_requests.created_at', [$start_date, $end_date]);
        }
        //dd($start_date);
        $refunds = $refunds->paginate(15);
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));
        return view('backend.staff_panel.refund_request', compact('refunds', 'start_date', 'end_date'));
    }

    public function staff_product(Request $request)
    {
        $sort_by = null;
        $pro_sort_by = null;
        $products = Product::join('categories', 'products.category_id', '=', 'categories.id')->orderBy('products.current_stock', 'desc')->limit(10);
        if ($request->has('category_id') && !empty($request->category_id)) {
            $sort_by = $request->category_id;
            $products = $products->where('category_id', $sort_by);
        }
        if (!empty($request->product_id)) {
            $pro_sort_by = $request->product_id;
            $products = $products->where('products.id', $pro_sort_by);
        }
        $products = $products->select('products.*', 'categories.name as category_name')->get();
        return view('backend.staff_panel.stock_report', compact('products', 'sort_by', 'pro_sort_by'));
    }

    public function staff_delivery_report(Request $request)
    {

        $start_date = date('Y-m-01 00:00:00');
        $end_date = date('Y-m-t 23:59:59');
        $date = $request->date;
        $sort_search = null;

        $orders = Order::where('delivery_boy', Auth::user()->id)->orderBy('orders.created_at', 'ASC');


        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('orders.code', 'like', '%' . $sort_search . '%');
        }
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d 00:00:00', strtotime($request->start_date));
            $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
        }
        $orders = $orders->whereBetween('orders.created_at', [$start_date, $end_date]);


        $orders = $orders->get();
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));
        //dd($orders);exit;
        return view('backend.staff_panel.delivery_report', compact('orders', 'sort_search', 'date', 'start_date', 'end_date'));
    }

    public function delivery_executive_ledger(Request $request)
    {

        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $date = $request->date;
        $sort_search = null;

        $delivery_activitys  = Dailyactivity::where('user_id', "162008")->orderBy('created_at', 'ASC')
            ->select('dailyactivities.customer_id', 'dailyactivities.order_no', 'dailyactivities.name', 'dailyactivities.phone', 'dailyactivities.address', 'dailyactivities.amount', 'dailyactivities.cash_collection');

        if ($request->has('search')) {
            $sort_search = $request->search;
            $delivery_activitys  = $delivery_activitys->where('order_no', 'like', '%' . $sort_search . '%');
        }

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d 00:00:00', strtotime($request->start_date));
            $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
        }

        $delivery_activitys  =   $delivery_activitys->whereBetween('created_at', [$start_date, $end_date]);

        $delivery_activitys  =   $delivery_activitys->get();
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));

        return view('backend.staff_panel.delivery_executive.delivery_executive_ledger', compact('delivery_activitys', 'sort_search', 'date', 'start_date', 'end_date'));
    }


    public function delivery_executive_collection_payment(Request $request)
    {
        $delivery_executive = Auth::user()->id;
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');

        if (empty($request->start_date))
            $request->start_date = $start_date;
        if (empty($request->end_date))
            $request->end_date = $end_date;

        $sql = "SELECT
        
    deel.created_at,deel.order_no,deel.note,sum(deel.debit) as debit,sum(deel.credit) as credit,
   (select sum(deel.debit-deel.credit)where deel.date between '" . $start_date . "' and '" . $end_date . "' or deel.date is null) as due
    FROM
    delivery_executive_ledger deel ";

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d', strtotime($request->end_date));

            $sql .= "where deel.user_id = $delivery_executive and deel.date between '" . $start_date . "' and '" . $end_date . "' or deel.date is null ";
        } else {
            $sql .= "where deel.user_id = $delivery_executive and deel.date between '" . $start_date . "' and '" . $end_date . "' or deel.date is null";
        }

        $sql .= "and deel.debit != '0' and deel.credit != '0'
        GROUP BY deel.order_no
        order by deel.id";
        $deelivery_ledgers = DB::select($sql);

        //dd($deelivery_ledgers);

        return view('backend.staff_panel.delivery_executive.deliveryextv_ledger', compact('deelivery_ledgers', 'start_date', 'end_date'));
    }



    public function get_customer_service_order(Request $request)
    {
        $customerServiceOrder = Order::where('orders.code', $request->ordernumber)->where('delivery_boy', Auth::user()->id)->whereIn('payment_status', ['unpaid', 'partial'])
            ->leftjoin('customers', 'customers.user_id', 'orders.user_id')
            ->leftjoin('areas', 'areas.code', '=', 'customers.area_code')
            ->select('orders.grand_total', 'orders.id', 'orders.cash_collection', 'orders.shipping_address', 'customers.customer_id', 'areas.name as areaname')->first();

        return $customerServiceOrder;
    }
    public function get_delivery_ledger_by_order(Request $request)
    {
        // dd($request->ordernumber);
        $customerServiceOrder = Order::where('orders.code', $request->ordernumber)
            ->join('customers', 'customers.user_id', 'orders.user_id')
            ->join('delivery_executive_ledger', 'delivery_executive_ledger.order_no', '=', 'orders.code')
            ->select('orders.grand_total', 'orders.id', 'orders.shipping_address', 'orders.cash_collection', 'customers.customer_id', 'delivery_executive_ledger.order_no', 'delivery_executive_ledger.date')->first();
        return $customerServiceOrder;
    }
    public function get_purchase_details(Request $request)
    {
        $purchasedetails = Purchase::where('purchase_no', $request->purchaseno)
            ->rightjoin('suppliers', 'suppliers.supplier_id', 'purchases.supplier_id')
            ->select('suppliers.supplier_id', 'suppliers.name', 'suppliers.address', 'suppliers.phone', 'purchases.payment_amount', 'purchases.total_value')->first();
        return $purchasedetails;
    }

    public function get_customer_by_phone(Request $request)
    {
        $customerdetails = customer::join('users', 'users.id', 'customers.user_id')
            ->join('areas', 'areas.code', 'customers.area_code')
            ->select('users.name', 'users.address', 'customers.customer_id', 'areas.name as areaname')
            ->where('phone', $request->phonenumber)->first();
        return $customerdetails;
    }


    public function purchase_list_new(Request $request)
    {

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $expiry_date_start = $request->expiry_date_start;
        $expiry_date_end = $request->expiry_date_start;

        if (!empty($start_date) && !empty($end_date) && empty($expiry_date_start) && empty($expiry_date_end)) {

            $sort_by = null;
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
            $warehousearray = getWearhouseBuUserId(auth()->user()->id);
            if (!$warehousearray) {
                $warehousearray = array();
            }
            $data = Purchase::leftjoin('suppliers', 'suppliers.supplier_id', '=', 'purchases.supplier_id')
                ->orderBy('purchases.date', 'asc')
                ->whereIn('wearhouse_id', $warehousearray)
                ->select('purchases.*', 'suppliers.name');

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start_date = date('Y-m-d', strtotime($request->start_date));
                $end_date = date('Y-m-d', strtotime($request->end_date));
                $data = $data->whereBetween('date', [$start_date, $end_date]);
            } else {
                $data = $data->whereBetween('date', [$start_date, $end_date]);
            }
            $data =   $data->get();
            return view('backend.staff_panel.purchase_manager.purchase_list', compact('data', 'sort_by', 'start_date', 'end_date', 'expiry_date_start', 'expiry_date_end'));
        }

        if (empty($start_date) && empty($end_date) && !empty($expiry_date_start) && !empty($expiry_date_end)) {
            $sort_by = null;
            $expiry_date_start = $request->expiry_date_start;
            $expiry_date_end = $request->expiry_date_start;

            $warehousearray = getWearhouseBuUserId(auth()->user()->id);
            if (!$warehousearray) {
                $warehousearray = array();
            }

            $data = Purchase::leftjoin('suppliers', 'suppliers.supplier_id', '=', 'purchases.supplier_id')
                ->join('purchase_details', 'purchases.id', '=', 'purchase_details.id')
                ->orderBy('purchases.date', 'asc')
                ->whereIn('purchases.wearhouse_id', $warehousearray)
                ->select('purchases.*', 'suppliers.name', 'purchase_details.expiry_date');


            if (!empty($request->expiry_date_start) && !empty($request->expiry_date_end)) {
                $expiry_date_start = date('Y-m-d', strtotime($request->expiry_date_start));
                $expiry_date_end = date('Y-m-d', strtotime($request->expiry_date_end));
                $data = $data->whereBetween('purchase_details.expiry_date', [$expiry_date_start, $expiry_date_end]);
            } else {
                $data = $data->whereBetween('purchase_details.expiry_date', [$expiry_date_start, $expiry_date_end]);
            }

            $data = $data->get();

            return view('backend.staff_panel.purchase_manager.purchase_list', compact('data', 'sort_by', 'start_date', 'end_date', 'expiry_date_start', 'expiry_date_end'));
        }

        if (!empty($start_date) && !empty($end_date) && !empty($expiry_date_start) && !empty($expiry_date_end)) {
            flash(__('Filter By Either Purcahase Date or Expired Date'))->error();
            return back();
        } else {
            return view('backend.staff_panel.purchase_manager.purchase_list_nothing');
        }
    }

    public function purchase_list(Request $request)
    {

        $sort_by = null;
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $warehouse = $request->warehouse;

        if (!$warehousearray) {
            $warehousearray = array();
        }
        $data = Purchase::leftjoin('suppliers', 'suppliers.supplier_id', '=', 'purchases.supplier_id')
            ->orderBy('purchases.date', 'asc')
            ->whereIn('wearhouse_id', $warehousearray)
            ->select('purchases.*', 'suppliers.name');

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d', strtotime($request->end_date));
            $data = $data->whereBetween('date', [$start_date, $end_date]);
        } else {
            $data = $data->whereBetween('date', [$start_date, $end_date]);
        }

        if (Auth::user()->staff->role->name == 'Manager') {
            if ($warehouse) {
                $data->where('wearhouse_id', $warehouse);
            }
        }

        $data =   $data->get();

        return view('backend.staff_panel.purchase_manager.purchase_list', compact('data', 'sort_by', 'start_date', 'end_date', 'warehouse'));
    }



    public function purchase_list_for_purchase_executive(Request $request)
    {
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');

        $all_purchase = Purchase::leftjoin('suppliers', 'suppliers.supplier_id', '=', 'purchases.supplier_id')
            ->orderBy('purchases.date', 'asc')
            ->where('created_by', Auth::user()->id)
            ->select(
                'purchases.id',
                'purchases.date',
                'purchases.total_value',
                'purchases.status as purstatus',
                'purchases.purchase_no',
                'suppliers.name'
            );

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d', strtotime($request->end_date));
            $all_purchase = $all_purchase->whereBetween('date', [$start_date, $end_date]);
        } else {
            $all_purchase = $all_purchase->whereBetween('date', [$start_date, $end_date]);
        }
        $all_purchase = $all_purchase->get();
        // dd($start_date, $end_date, $all_purchase, Auth::user()->id);
        return view('backend.staff_panel.purchase_executive.purchase_list', compact('all_purchase', 'start_date', 'end_date'));
    }

    public function purchase_approve($id)
    {
        $purchase_order_item = PurchaseDetail::where('purchase_id', $id)->get();

        $purchase = Purchase::findOrFail($id);
        $purchase->status = 2;
        $purchase->approved_date = date('Y-m-d');

        // Start Accounting Sales Journal Hit
        $insert_purchase_journal = insert_purchase_journal($purchase->id);
        if ($insert_purchase_journal) {
            autoapprove($purchase->id);
        }
        // End Accounting Sales Journal Hit

        if ($purchase->save()) {
            $supplier_ledger = new Supplier_ledger();
            $supplier_ledger->supplier_id = $purchase->supplier_id;
            $supplier_ledger->purchase_id = $purchase->id;
            $supplier_ledger->descriptions = 'Purchase Order';
            $supplier_ledger->type = 'Purchase';
            $supplier_ledger->debit = $purchase->total_value;
            $supplier_ledger->credit = 0;
            $supplier_ledger->date = $purchase->date;
            $supplier_ledger->save();
        }

        if (!empty($purchase_order_item)) {
            foreach ($purchase_order_item as $key => $prod) {
                $ps = ProductStock::where(['product_id' => $prod->product_id, 'wearhouse_id' => $purchase->wearhouse_id])->first();
                if (!empty($ps)) {
                    $ps->increment('qty', $prod->qty);
                    $ps->save();
                } else {
                    ProductStock::insert(['product_id' => $prod->product_id, 'wearhouse_id' => $purchase->wearhouse_id, 'qty' => $prod->qty]);
                }
            }
        }
        flash(translate('Purchase status has been updated successfully!'))->success();
        return back();
    }

    public function numberGenerator()
    {
        $invoiceNo = Purchase::max('purchase_id');

        if ($invoiceNo) {
            $invoiceNo += 1;
        } else {
            $invoiceNo = 1;
        }

        return $invoiceNo;
    }


    public function damage_approve(Request $request, $id)
    {
        $purchase = Damage::findOrFail($id);
        $purchase->status = 'Approved';
        $purchase->save();

        $ops = ProductStock::where(['product_id' => $purchase->product_id, 'wearhouse_id' => $purchase->wearhouse_id])->first();
        $ops->decrement('qty', $purchase->qty);
        $ops->save();

        flash(translate('Purchase status has been updated successfully!'))->success();
        return back();
    }

    public function damage_list(Request $request)
    {
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);

        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $damages = Damage::orderBy('date', 'asc')
            ->whereIn('wearhouse_id', $warehousearray);

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d', strtotime($request->end_date));
            $damages = $damages->whereBetween('date', [$start_date, $end_date]);
        } else {
            $damages = $damages->whereBetween('date', [$start_date, $end_date]);
        }

        $damages = $damages->get();

        return view('backend.staff_panel.purchase_manager.damage_list', compact('damages', 'start_date', 'end_date'));
    }

    public function vendor_list()
    {
        $suppliers_list = Supplier::all();
        return view('backend.staff_panel.purchase_manager.supplier_list', compact('suppliers_list'));
    }

    public function purchase_reject($id)
    {
        $order = Purchase::findOrFail($id);
        $order->status = 3;
        $order->save();
        flash(translate('Purchase Order has been deleted successfully'))->success();
        return back();
    }

    public function delivery_payment_paid($id)
    {
        $deorder = DeliveryExecutiveLedger::findOrFail($id);
        //dd($deorder->credit);
        $deorder->status = 'Paid';
        $deorder->paid_date = date('Y-m-d');
        //$deorder->save();
        if ($deorder->save()) {
            DeliveryExecutiveLedger::where('order_no', $deorder->order_no)->where('debit', $deorder->credit)
                ->update(['status' => "Paid"]);
            $order = Order::where('code', $deorder->order_no)->first();
            //$order = Order::findOrFail($request->order_id);
            $order->payment_status_viewed = '0';
            $order->save();

            if (!empty($order->payment_details)) {
                $orderpayment = json_decode($order->payment_details);
                if (!empty($orderpayment)) {
                    $total = $orderpayment->amount + $deorder->credit;
                    if ($total <= $order->grand_total) {
                        $paid = $orderpayment->amount + $$deorder->credit;
                    } else {

                        flash(translate('Not Possible'))->error();
                        return back();
                    }
                }
            } else {
                $paid = $deorder->credit;
            }
            if ($paid >= $order->grand_total) {
                $status = 'paid';
            } else {
                $status = 'partial';
            }

            if (Auth::user()->user_type == 'seller') {
                foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
                    $orderDetail->payment_status = $status;
                    $orderDetail->save();
                }
            } else {
                foreach ($order->orderDetails as $key => $orderDetail) {
                    $orderDetail->payment_status = $status;
                    $orderDetail->save();
                }
            }

            $status = $status;
            foreach ($order->orderDetails as $key => $orderDetail) {
                if ($orderDetail->payment_status == 'unpaid') {
                    $status = 'unpaid';
                }
            }
            $order->payment_status = $status;
            //if($request->status !='paid'){

            $oVal = (object)[
                'amount' => $paid,
                'status' => 'VALID',
                'error' => null
            ];
            $order->payment_details = json_encode($oVal);
            //}
            $order->save();
            if ($order->payment_status == 'paid' || $order->payment_status == 'partial') {
                $array['view'] = 'emails.payment';
                $array['subject'] = translate('Your order payment has been paid') . ' - ' . $order->code;
                $array['from'] = 'sales@bazarnao.com';
                $array['order'] = $order;
                $shipping_address = json_decode($order->shipping_address);
                if (!empty($shipping_address->email)) {
                    //Mail::to($shipping_address->email)->queue(new InvoiceEmailManager($array));
                }

                if (!empty($order->user_id)) {
                    $customer_id = $order->user_id;
                } else {
                    $customer_id = $order->guest_id;
                }
                $cust_ledger = array();
                $cust_ledger['customer_id'] = $customer_id;
                $cust_ledger['order_id'] = $order->id;
                $cust_ledger['descriptions'] = 'Cash Payment';
                $cust_ledger['type'] = 'Payment';
                $cust_ledger['debit'] = 0;
                $cust_ledger['credit'] = $deorder->credit;
                $cust_ledger['date'] = date('Y-m-d');
                save_customer_ledger($cust_ledger);
            }

            if ($order->payment_status == 'paid' && $order->commission_calculated == 0) {
                if (\App\Models\Addon::where('unique_identifier', 'seller_subscription')->first() == null || !\App\Models\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
                    if ($order->payment_type == 'cash_on_delivery') {
                        if (BusinessSetting::where('type', 'category_wise_commission')->first()->value != 1) {
                            $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
                            foreach ($order->orderDetails as $key => $orderDetail) {
                                $orderDetail->payment_status = 'paid';
                                $orderDetail->save();
                                if ($orderDetail->product->user->user_type == 'seller') {
                                    $seller = $orderDetail->product->user->seller;
                                    $seller->admin_to_pay = $seller->admin_to_pay - ($orderDetail->price * $commission_percentage) / 100;
                                    $seller->save();
                                }
                            }
                        } else {
                            foreach ($order->orderDetails as $key => $orderDetail) {
                                $orderDetail->payment_status = 'paid';
                                $orderDetail->save();
                                if ($orderDetail->product->user->user_type == 'seller') {
                                    $commission_percentage = $orderDetail->product->category->commision_rate;
                                    $seller = $orderDetail->product->user->seller;
                                    $seller->admin_to_pay = $seller->admin_to_pay - ($orderDetail->price * $commission_percentage) / 100;
                                    $seller->save();
                                }
                            }
                        }
                    } elseif ($order->manual_payment) {
                        if (BusinessSetting::where('type', 'category_wise_commission')->first()->value != 1) {
                            $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
                            foreach ($order->orderDetails as $key => $orderDetail) {
                                $orderDetail->payment_status = 'paid';
                                $orderDetail->save();
                                if ($orderDetail->product->user->user_type == 'seller') {
                                    $seller = $orderDetail->product->user->seller;
                                    $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
                                    $seller->save();
                                }
                            }
                        } else {
                            foreach ($order->orderDetails as $key => $orderDetail) {
                                $orderDetail->payment_status = 'paid';
                                $orderDetail->save();
                                if ($orderDetail->product->user->user_type == 'seller') {
                                    $commission_percentage = $orderDetail->product->category->commision_rate;
                                    $seller = $orderDetail->product->user->seller;
                                    $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
                                    $seller->save();
                                }
                            }
                        }
                    }
                }

                if (\App\Models\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Models\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {
                    $affiliateController = new AffiliateController;
                    $affiliateController->processAffiliatePoints($order);
                }

                if (\App\Models\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Models\Addon::where('unique_identifier', 'club_point')->first()->activated) {
                    if ($order->user != null) {
                        $clubpointController = new ClubPointController;
                        $clubpointController->processClubPoints($order);
                    }
                }

                $order->commission_calculated = 1;
                $order->save();
            }

            if (\App\Models\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Models\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\Models\OtpConfiguration::where('type', 'otp_for_paid_status')->first()->value) {
                try {
                    $otpController = new OTPVerificationController;
                    $otpController->send_payment_status($order, $deorder->credit);
                } catch (\Exception $e) {
                }
            }
        }
        flash(translate('Paid successfully'))->success();
        return back();
    }


    public function cutomerservice_add_product()
    {
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();

        return view('backend.staff_panel.customer_service.create', compact('categories'));
    }

    public function account_activity_report(Request $request)
    {
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $delivery_executive_ledger = DeliveryExecutiveLedger::join('orders', 'delivery_executive_ledger.order_no', 'orders.code')->where('note', Auth::user()->id)->where('type', 'Payment')->where('status', 'Paid')
            ->select('delivery_executive_ledger.*', 'orders.id', 'orders.payment_status');
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d', strtotime($request->end_date));
            $delivery_executive_ledger = $delivery_executive_ledger->whereBetween('paid_date', [$start_date, $end_date]);
        } else {
            $delivery_executive_ledger = $delivery_executive_ledger->whereBetween('paid_date', [$start_date, $end_date]);
        }

        $delivery_executive_ledger = $delivery_executive_ledger->get();
        return view('backend.staff_panel.account_executive.activity_report', compact('delivery_executive_ledger', 'start_date', 'end_date'));
    }
    public function delivery_executive_due_collection(Request $request)
    {
        $start_date = date('Y-m-d 00:00:00');
        $end_date = date('Y-m-d 23:59:59');
        $order_daily_activities = DueCollection::where('user_id', Auth::user()->id)->whereBetween('created_at', [$start_date, $end_date])->get();
        $delivery_executive_ledger = DeliveryExecutiveLedger::where('user_id', Auth::user()->id)->where('type', 'Payment')->where('date', date('Y-m-d'))->get();

        return view('backend.staff_panel.delivery_executive.delivery_executive_due_collection', compact('order_daily_activities', 'delivery_executive_ledger'));
    }
    public function due_collection(Request $request)
    {
        $start_date = date('Y-m-d 00:00:00');
        $end_date = date('Y-m-d 23:59:59');
        DueCollection::where('user_id', Auth::user()->id)
            ->whereBetween('created_at', [$start_date, $end_date])->delete();
        DeliveryExecutiveLedger::where('user_id', Auth::user()->id)
            ->where('date', date('Y-m-d'))->where('due_status', 1)->delete();
        if ($request->order_no) {
            foreach ($request->order_no as $key => $due) {
                // if($request->amount[$key] == $request->total_collection[$key]){
                //     flash("already collected full amount")->error();
                //     return back();
                // }

                $DueCollection = new DueCollection;
                $DueCollection->user_id = Auth::user()->id;
                $DueCollection->order_no = $request->order_no[$key];
                $DueCollection->name = $request->name[$key];
                $DueCollection->customer_id = $request->id[$key];
                $DueCollection->address = $request->address[$key];
                $DueCollection->mobile = $request->phone[$key];
                $DueCollection->area = $request->area[$key];
                $DueCollection->amount = $request->amount[$key];
                $DueCollection->total_collection = $request->total_collection[$key];
                $DueCollection->cash_collection = $request->cash_collection[$key];

                if ($DueCollection->save()) {

                    $delivery_executive_ledger = new DeliveryExecutiveLedger;
                    $delivery_executive_ledger->user_id = Auth::user()->id;
                    $delivery_executive_ledger->order_no = $request->order_no[$key];
                    $delivery_executive_ledger->name = $request->name[$key];
                    $delivery_executive_ledger->date = date('Y-m-d');
                    $delivery_executive_ledger->debit = $request->cash_collection[$key];
                    $delivery_executive_ledger->status = "Pending";
                    $delivery_executive_ledger->type = "Order";
                    $delivery_executive_ledger->due_status = 1;
                    $delivery_executive_ledger->save();

                    $order = Order::where('code', $request->order_no[$key])->first();
                    $order->cash_collection = $request->total_collection[$key];
                    $order->save();
                }
            }

            flash("Due collection Saved Successfully")->success();
            return back();
        }
    }


    // public function customers_comments_complain(Request $request)
    // {
    //     $start_date = date('Y-m-01');
    //     $end_date = date('Y-m-t');

    //     $comment_complain = Dailyactivity::Where('user_id', Auth()->user()->id)->orderBy('created_at', 'ASC');
    //     if (!empty($request->start_date) && !empty($request->end_date)) {
    //         $start_date = date('Y-m-d 00:00:00', strtotime($request->start_date));
    //         $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
    //         $comment_complain = $comment_complain->whereBetween('created_at', [$start_date, $end_date]);
    //     }
    //     $comment_complain = $comment_complain->get();

    //     if (Auth::user()->user_type == 'admin') {
    //         $comment_complain = Dailyactivity::orderBy('created_at', 'ASC')
    //             // ->Where('dailyactivities.user_id', Auth()->user()->id)
    //             ->leftjoin('customers', 'dailyactivities.customer_id', 'customers.customer_id')
    //             ->select('dailyactivities.*', 'customers.user_id as cuser_id');
    //         if (!empty($request->start_date) && !empty($request->end_date)) {
    //             $start_date = date('Y-m-d 00:00:00', strtotime($request->start_date));
    //             $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
    //             $comment_complain = $comment_complain->whereBetween('dailyactivities.created_at', [$start_date, $end_date]);
    //         }
    //         $comment_complain = $comment_complain->get();
    //         return view('backend.reports.comment_complain', compact('comment_complain', 'start_date', 'end_date'));
    //     } else {
    //         $comment_complain = Dailyactivity::orderBy('created_at', 'ASC')
    //             // ->Where('dailyactivities.user_id', Auth()->user()->id)
    //             ->leftjoin('customers', 'dailyactivities.customer_id', 'customers.customer_id')
    //             ->select('dailyactivities.*', 'customers.user_id as cuser_id');
    //         if (!empty($request->start_date) && !empty($request->end_date)) {
    //             $start_date = date('Y-m-d 00:00:00', strtotime($request->start_date));
    //             $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
    //             $comment_complain = $comment_complain->whereBetween('dailyactivities.created_at', [$start_date, $end_date]);
    //         }
    //         $comment_complain = $comment_complain->get();
    //         return view('backend.staff_panel.customer_service.comment_complain', compact('comment_complain', 'start_date', 'end_date'));
    //     }
    // }

    public function customers_comments_complain(Request $request, $type = null)
    {
        $start_date = date('Y-m-01 00:00:00', strtotime('-1 month'));
        $end_date = date('Y-m-t 23:59:59');

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d 00:00:00', strtotime($request->start_date));
            $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
        }

        $comment_complain = Dailyactivity::orderBy('dailyactivities.created_at', 'DESC')
            ->whereBetween('dailyactivities.created_at', [$start_date, $end_date]);

        if (!(Auth::user()->user_type == 'admin' || auth()->user()->staff->role->name == 'Manager')) {
            $comment_complain = $comment_complain->where('dailyactivities.user_id', Auth()->user()->id);
        }

        $comment_complain = $comment_complain
            ->leftJoin('customers', 'dailyactivities.customer_id', '=', 'customers.customer_id')
            ->select('dailyactivities.*', 'customers.user_id as cuser_id')
            ->get();

        if ($type == 'excel') {
            return Excel::download(new CustomersCommentsComplainExport(['comment_complain' => $comment_complain]), 'commentComplainList.xlsx');
        }

        if (Auth::user()->user_type == 'admin') {
            return view('backend.reports.comment_complain', compact('comment_complain', 'start_date', 'end_date'));
        } else {
            return view('backend.staff_panel.customer_service.comment_complain', compact('comment_complain', 'start_date', 'end_date'));
        }
    }



    function send_birth_day_wish_sms()
    {

        $customer_info = Customer::join('orders', 'customers.user_id', 'orders.user_id')
            ->select('customers.dob', 'orders.shipping_address')
            ->where('customers.dob', '>', '01-01-1970')->get();

        foreach ($customer_info as $customer) {
            if (!empty(($customer->dob) && ($customer->shipping_address))) {
                $get_phone = json_decode($customer->shipping_address)->phone;
                $date = date('m-d', strtotime($customer->dob));
                $today = date('m-d');
                if ($date === $today) {
                    try {
                        $otpController = new OTPVerificationController;
                        $otpController->send_birth_day_wish_sms($get_phone, $customer);
                    } catch (\Exception $e) {
                    }
                }
            }
        }
    }

    function clearCache(Request $request)
    {
        Artisan::call('optimize:clear');
        flash(translate('Cache cleared successfully'))->success();
        return back();
    }


    public function emergency_contact()
    {

        return view('frontend.emergency_contact');
    }

    public function fire_services()
    {
        $fireServices = FireService::orderBy('service_name', 'ASC')->get();
        return view('frontend.fire_services', compact('fireServices'));
    }

    public function police_stations()
    {
        $policeStations = PoliceStation::orderBy('name', 'ASC')->get();
        return view('frontend.police_stations', compact('policeStations'));
    }
}
