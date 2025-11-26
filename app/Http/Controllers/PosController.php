<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Group_product;
use App\Models\RefundRequest;
use App\Models\Customer_ledger;
use Session;
use Auth;
use App\Http\Resources\V2\PosProductCollection;
use App\Models\OrderStatusLog;
use App\Models\Pos_ledger;
use App\Models\ProductStock;
use App\Models\Staff;
use App\Utility\CategoryUtility;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PosController extends Controller
{
    public function index()
    {
        // $customers = User::where('user_type', 'customer')->where('email_verified_at', '!=', null)->orderBy('created_at', 'desc')->get();
        $customers = User::where('user_type', 'customer')->orderBy('created_at', 'desc')->get();
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            $warehousearray = getWearhouseBuUserId(auth()->user()->id);
            $wearhouses = Warehouse::WhereIn('id', $warehousearray)->get();

            if ($wearhouses->isEmpty()) {
                flash("Warehouse Not Assained For Admin")->warning();
                return back();
            } else {
                return view('pos.index', compact('customers', 'wearhouses'));
            }
        }
    }

    public function search(Request $request)
    {

        $products = Product::select('products.id', 'products.name', 'products.unit_price', 'products.barcode')
            ->where('is_group_product', 0);

        if ($request->category != null) {
            $arr = explode('-', $request->category);
            if ($arr[0] == 'category') {
                $category_ids = CategoryUtility::children_ids($arr[1]);
                $category_ids[] = $arr[1];
                $products = $products->whereIn('products.category_id', $category_ids);
            }
        }

        // if ($request->warehouse_id != null) {
        //     $warehouse_id = $request->warehouse_id;
        //     $products = $products->leftJoin('product_stocks', 'products.id', '=', 'product_stocks.product_id')
        //                          ->where('product_stocks.warehouse_id', $warehouse_id)
        //                          ->addSelect('product_stocks.price as pos_price');
        // }

        if ($request->brand != null) {
            $products = $products->where('products.brand_id', $request->brand);
        }

        if ($request->keyword != null) {
            $products = $products->where('products.name', 'like', '%' . $request->keyword . '%');
        }

        $stocks = new PosProductCollection($products->paginate(40));
        $stocks->appends(['keyword' => $request->keyword, 'category' => $request->category, 'brand' => $request->brand, 'warehouse_id' => $request->warehouse_id]);

        return $stocks;
    }



    public function addToCart(Request $request)
    {
        if ($request->product_id) {
            $product = Product::find($request->product_id);
        } else {
            $product = Product::where('barcode', $request->barcode)->first();
        }
        if (!empty($product) && $product->barcode !== null) {
            $data = array();
            $data['id'] = $product->id;
            $data['barcode'] = $request->barcode;
            $data['quantity'] = $product->min_qty;

            $warehouseIds = getWearhouseBuUserId(auth()->user()->id);
            $wearhouses = Warehouse::WhereIn('id', $warehouseIds)->first();

            $pos_price = ProductStock::where('wearhouse_id', $wearhouses->id)
                ->where('product_id', $product->id)
                ->value('price');

            $discount_type_column = 'warehouse' . $wearhouses->id . '_discount_type';
            $discount_column = 'warehouse' . $wearhouses->id . '_discount';

            $discount_type = $product->$discount_type_column;
            $discount = $product->$discount_column;

            $tax = 0;
            $price = $pos_price;

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
                if ($discount_type == 'percent') {
                    $price -= ($price * $discount) / 100;
                } elseif ($discount_type == 'amount') {
                    $price -= $discount;
                }
            }

            $data['price'] = $price;
            $data['tax'] = $tax;

            if ($request->session()->has('pos.cart')) {
                $foundInCart = false;
                $cart = collect();
                foreach ($request->session()->get('pos.cart') as $key => $cartItem) {

                    if ($cartItem['id'] == $product->id) {
                        $foundInCart = true;
                        $cartItem['quantity'] += 1;
                    }
                    $cart->push($cartItem);
                }

                if (!$foundInCart) {
                    $cart->push($data);
                }
                $request->session()->put('pos.cart', $cart);
            } else {
                $cart = collect([$data]);
                $request->session()->put('pos.cart', $cart);
            }

            $request->session()->put('pos.cart', $cart);

            return array('success' => 1, 'message' => '', 'view' => view('pos.cart')->render());
        } else {
            return array('success' => 0, 'message' => 'No Product Found With This Barcode');
        }
    }


    public function online_orders_show_new(Request $request)
    {
        $orderdetails = OrderDetail::where('order_id', $request->order_id)
            ->select('id', 'order_id', 'product_id', 'quantity', 'price', 'shipping_cost', 'discount')
            ->get();

        $storedData = [];

        foreach ($orderdetails as $key => $orderdetail) {
            $product = $orderdetail->product;

            // Determine if the product is part of a group
            $isGroupProduct = $product->is_group_product; // Modify this line according to your logic

            if ($isGroupProduct) {
                // Fetch group products (assuming you have a method to get these)
                $groupProducts = $this->getGroupProducts($product->id); // You need to implement this method

                // Collect product IDs for the group
                $productIds = $groupProducts->pluck('product_id')->toArray();
                $barcodes = Product::whereIn('id', $productIds)->pluck('barcode');
                $number = Group_product::where('group_product_id', $product->id)->count();
                // dd($number);
                // dd($barcodes,$productIds);
                $data = [
                    'id' => $orderdetail->id,
                    'order_id' => $orderdetail->order_id,
                    'product_id' => $product->id, // Store all group product IDs as an array
                    'groups_product_ids' => $productIds,
                    'barcode' => $barcodes,
                    'total_item' => $number,
                    'quantity' => $orderdetail->quantity,
                    'price' => $orderdetail->price + $orderdetail->discount,
                    'shipping_cost' => $orderdetail->shipping_cost,
                    'discount' => $orderdetail->discount,
                ];
            } else {
                $data = [
                    'id' => $orderdetail->id,
                    'order_id' => $orderdetail->order_id,
                    'product_id' => $orderdetail->product_id,
                    'barcode' => $product->barcode,
                    'quantity' => $orderdetail->quantity,
                    'price' => $orderdetail->price + $orderdetail->discount,
                    'shipping_cost' => $orderdetail->shipping_cost,
                    'discount' => $orderdetail->discount,
                ];
            }

            $details = collect($data);
            $storedData[] = $details;
        }

        session()->put('online.orderDetails', $storedData);

        return response()->json([
            'success' => 1,
            'message' => '',
            'view' => view('pos.OnlineOrderDetails')->render()
        ]);
    }

    public function online_orders_show(Request $request)
    {
        $orderdetails = OrderDetail::where('order_id', $request->order_id)
            ->select('id', 'order_id', 'product_id', 'quantity', 'price', 'shipping_cost', 'discount')->get();
        foreach ($orderdetails  as $key => $orderdetail) {
            $data = array();
            $data['id'] = $orderdetail->id;
            $data['order_id'] = $orderdetail->order_id;
            $data['product_id'] = $orderdetail->product_id;
            $data['barcode'] = $orderdetail->product->barcode;
            $data['quantity'] = $orderdetail->quantity;
            $data['price'] = $orderdetail->price + $orderdetail->discount;
            $data['shipping_cost'] = $orderdetail->shipping_cost;
            $data['discount'] = $orderdetail->discount;
            $details = collect($data);
            $storedData[] = $details;
        }
        session()->put('online.orderDetails', $storedData);
        return array('success' => 1, 'message' => '', 'view' => view('pos.OnlineOrderDetails')->render());
    }

    // Example method to get group products (you need to implement this based on your logic)
    private function getGroupProducts($productId)
    {
        // Example logic: Fetch all products that are part of the same group as the given product
        return Group_product::where('group_product_id', function ($query) use ($productId) {
            $query->select('id')
                ->from('products')
                ->where('id', $productId);
        })->get();
    }

    public function scan_online_order()
    {
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $delivery = \App\Models\Staff::where('role_id', 10)->get()->filter(function ($staff) use ($warehousearray) {
            $warehouses = @unserialize($staff->warehouse_id);
            if ($warehouses === false && $staff->warehouse_id !== 'b:0;') {

                return false;
            }
            return is_array($warehouses) && !empty(array_intersect($warehouses, $warehousearray));
        });

        $orders = Order::whereIn('order_from', ['Web', 'App'])
            ->where('online_order_delivery_status', 'confirmed')->get();
        return view('pos.onlineorder', compact('warehousearray', 'orders', 'delivery'));
    }

    public function RemoveOnlineOrderDetails(Request $request)
    {
        $barcode = $request->barcode;
        $removekey = 'barcode';
        $onlineorderDetails = Session::get('online.orderDetails');

        $product = collect($onlineorderDetails)->first(function ($collection) use ($barcode) {
            return $collection['barcode'] === $barcode;
        });

        $price = $product['price'] / $product['quantity'];
        $data = array();
        $data['id'] = $product['id'];
        $data['order_id'] = $product['order_id'];
        $data['product_id'] = $product['product_id'];
        $data['quantity'] = $product['quantity'];
        $data['price'] = $price;
        $data['shipping_cost'] = $product['shipping_cost'];
        $data['discount'] = $product['discount'];;
        $data['tax'] = 0;

        if (session()->has('online.orderConfirm')) {
            $foundInCart = false;
            $cart = collect();
            foreach (session()->get('online.orderConfirm') as $key => $cartItem) {
                if ($cartItem['id'] == $product['id']) {
                    $foundInCart = true;
                    $cartItem['quantity'] += 1;
                }
                $cart->push($cartItem);
            }
            if (!$foundInCart) {
                $cart->push($data);
            }
            session()->put('online.orderConfirm', $cart);
        } else {
            $cart = collect([$data]);
            session()->put('online.orderConfirm', $cart);
        }
        session()->put('online.orderConfirm', $cart);

        $details = array_filter($onlineorderDetails, function ($item) use ($removekey, $barcode) {
            return $item[$removekey] !== $barcode;
        });
        $request->session()->put('online.orderDetails', $details);
        return view('pos.OnlineOrderDetails');
        //return array('success' => 1, 'message' => '', 'view' => view('pos.onlineOrderConfirm')->render());
    }

    public function RemoveOnlineOrderDetails_new(Request $request)
    {

        $barcode = $request->barcode;
        $removekey = 'barcode';
        $onlineorderDetails = Session::get('online.orderDetails');
        // dd($onlineorderDetails);

        $product = Product::where('barcode', $barcode)->first();



        $product = collect($onlineorderDetails)->first(function ($collection) use ($barcode) {
            return $collection['barcode'] === $barcode;
        });

        if (empty($product)) {
            foreach ($onlineorderDetails as $check) {
                $item = Group_product::where('group_products.group_product_id', $check['product_id'])
                    ->join('products', 'products.id', '=', 'group_products.product_id') // Join on the correct column
                    ->where('products.barcode', $barcode)
                    ->select('products.*')
                    ->get();

                if ($item) {
                    $check['total_item'] = $check['total_item'] - 1;

                    if ($check->total_item == 0) {
                        $product = collect($onlineorderDetails)->first(function ($collection) use ($check) {
                            return $collection['product_id'] === $check->product_id;
                        });

                        $price = $product['price'] / $product['quantity'];
                        $data = array();
                        $data['id'] = $product['id'];
                        $data['order_id'] = $product['order_id'];
                        $data['product_id'] = $product['product_id'];
                        $data['quantity'] = $product['quantity'];
                        $data['price'] = $price;
                        $data['shipping_cost'] = $product['shipping_cost'];
                        $data['discount'] = $product['discount'];;
                        $data['tax'] = 0;

                        if (session()->has('online.orderConfirm')) {
                            $foundInCart = false;
                            $cart = collect();
                            foreach (session()->get('online.orderConfirm') as $key => $cartItem) {
                                if ($cartItem['id'] == $product['id']) {
                                    $foundInCart = true;
                                    $cartItem['quantity'] += 1;
                                }
                                $cart->push($cartItem);
                            }
                            if (!$foundInCart) {
                                $cart->push($data);
                            }
                            session()->put('online.orderConfirm', $cart);
                        } else {
                            $cart = collect([$data]);
                            session()->put('online.orderConfirm', $cart);
                        }
                        session()->put('online.orderConfirm', $cart);

                        $details = array_filter($onlineorderDetails, function ($item) use ($removekey, $barcode) {
                            return $item[$removekey] !== $barcode;
                        });
                        $request->session()->put('online.orderDetails', $details);
                        return view('pos.OnlineOrderDetails');
                    }
                }
                $orderdetail[] = $check;
            }
            dd($orderdetail);
            Session::put('online.orderDetails', $orderdetail);
            // $request->session()->put('online.orderDetails', $orderdetail);

        } else {
            $price = $product['price'] / $product['quantity'];
            $data = array();
            $data['id'] = $product['id'];
            $data['order_id'] = $product['order_id'];
            $data['product_id'] = $product['product_id'];
            $data['quantity'] = $product['quantity'];
            $data['price'] = $price;
            $data['shipping_cost'] = $product['shipping_cost'];
            $data['discount'] = $product['discount'];;
            $data['tax'] = 0;

            if (session()->has('online.orderConfirm')) {
                $foundInCart = false;
                $cart = collect();
                foreach (session()->get('online.orderConfirm') as $key => $cartItem) {
                    if ($cartItem['id'] == $product['id']) {
                        $foundInCart = true;
                        $cartItem['quantity'] += 1;
                    }
                    $cart->push($cartItem);
                }
                if (!$foundInCart) {
                    $cart->push($data);
                }
                session()->put('online.orderConfirm', $cart);
            } else {
                $cart = collect([$data]);
                session()->put('online.orderConfirm', $cart);
            }
            session()->put('online.orderConfirm', $cart);

            $details = array_filter($onlineorderDetails, function ($item) use ($removekey, $barcode) {
                return $item[$removekey] !== $barcode;
            });
            $request->session()->put('online.orderDetails', $details);
            return view('pos.OnlineOrderDetails');
            //return array('success' => 1, 'message' => '', 'view' => view('pos.onlineOrderConfirm')->render());
        }
    }

    public function update_online_order_delivery_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order_status_log = new OrderStatusLog();
        $order_status_log->order_id = $request->order_id;
        $order_status_log->user_id = Auth::user()->id;
        $order_status_log->order_status = $request->status;
        $order_status_log->remarks = $request->reason_of_cancel;
        $order_status_log->order_code = $order->code;
        $order_status_log->save();

        $order->delivery_viewed = '0';
        $order->on_delivery_date = date('Y-m-d H:i:s');
        $order->on_delivery_by  = Auth::user()->id;
        $order->online_order_delivery_status  = $request->status;
        $order->save();

        foreach ($order->orderDetails as $key => $orderDetail) {
            $orderDetail->delivery_status = $request->status;
            $orderDetail->delivery_status_changer_id = Auth::user()->id;
            $orderDetail->save();
        }

        if (in_array($request->status, array('on_delivery'))) {
            $order->delivery_boy = $request->delivery_boy;
            $order->is_deduct = 1;
            $order->save();
        }

        Session::forget('online.orderConfirm');
        $check = collect(Session::get('online.orderDetails'));

        if (count($check) == 0) {
            if (request()->ajax()) {
                $newRoute = url('/pos/gen-invoice/' . $order->id);
                return response()->json([
                    'redirect' => $newRoute,
                    'success' => 1,
                ]);
            }
        }
    }

    public function updateQuantity(Request $request)
    {
        $cart = $request->session()->get('pos.cart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if ($key == $request->key) {
                $product = Product::find($object['id']);
                $object['quantity'] = $request->quantity;
            }
            return $object;
        });
        $request->session()->put('pos.cart', $cart);

        return array('success' => 1, 'message' => '', 'view' => view('pos.cart')->render());
    }

    public function updatePrice(Request $request)
    {
        $cart = $request->session()->get('pos.cart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if ($key == $request->key) {
                $product = Product::find($object['id']);
                $object['price'] = $request->price;
            }
            return $object;
        });
        $request->session()->put('pos.cart', $cart);
        return array('success' => 1, 'message' => '', 'view' => view('pos.cart')->render());
    }

    //removes from Cart
    public function removeFromCart(Request $request)
    {
        if (Session::has('pos.cart')) {
            $cart = Session::get('pos.cart', collect([]));
            $cart->forget($request->key);
            Session::put('pos.cart', $cart);

            $request->session()->put('pos.cart', $cart);
        }

        return view('pos.cart');
    }

    public function ClearOnlineOrderConfirm()
    {
        Session::forget('online.orderConfirm');
        Session::forget('online.orderDetails');
        return 1;
    }

    //Shipping Address for admin
    public function getShippingAddress(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        if (($user)) {
            $data['name'] = $user->name;
            $data['address'] = $user->address;
            $data['phone'] = $user->phone;
            $shipping_info = $data;
            $request->session()->put('pos.shipping_info', $shipping_info);
            return 1;
        }
    }

    public function set_shipping_address(Request $request)
    {
        $user = User::where('phone', $request->phone)->first();
        if (!empty($user)) {
            return 0;
        } else {
            if ($request->address_id != null) {
                $address = Address::findOrFail($request->address_id);
                $data['name'] = $address->user->name;
                $data['address'] = $address->address;
                $data['phone'] = $address->phone;
            } else {
                $data['name'] = $request->name;
                $data['address'] = $request->address;
                $data['phone'] = $request->phone;
            }
            $shipping_info = $data;
            $request->session()->put('pos.shipping_info', $shipping_info);
            return 1;
        }
    }

    //set Discount
    public function setDiscount(Request $request)
    {
        if ($request->discount >= 0) {
            Session::put('pos.discount', $request->discount);
        }
        return view('pos.cart');
    }

    //set Shipping Cost
    public function setShipping(Request $request)
    {
        if ($request->shipping != null) {
            Session::put('pos.shipping', $request->shipping);
        }
        return view('pos.cart');
    }

    //order summary
    public function get_order_summary(Request $request)
    {
        return view('pos.order_summary');
    }

    //order place
    public function order_store_old(Request $request)
    {
        $customer = null;
        $staff =  Staff::where('user_id', Auth::user()->id)->first();
        if (empty($staff)) {
            return array('success' => 0, 'message' => translate('You are not permited to create POS Order.'));
        }

        if ($staff) {
            $length = unserialize($staff->warehouse_id);
            $count = count($length);
            if ($count > 1) {
                return array('success' => 0, 'message' => translate('Multy Warehouse Permited   User Not Allow.'));
            }
        }

        $shipping_info = Session::get('pos.shipping_info');

        if ($request->user_id == null && (!empty($shipping_info))) {
            $user = User::create([
                'name' => $shipping_info['name'],
                'address' => $shipping_info['address'],
                'phone' => $shipping_info['phone'],
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('Bz123456'),
            ]);

            if ($length[0] == 2) {
                //area code for malibug and mirpur branch 
                $areacode = '43';
            } elseif ($length[0] == 3) {
                $areacode = '01';
            }

            $exists = Customer::where('area_code', $areacode)
                ->orderBy('created_at', 'desc')->first();

            if ($exists) {
                $customer_id = (int)$exists->customer_id + 1;
                if (strlen((string)$customer_id) < 8) {
                    $customer_id = '0' . $customer_id;
                }
            }

            $customer = new Customer;
            $customer->user_id = $user->id;
            $customer->area_code = $areacode;
            $customer->customer_id = $customer_id;
            $customer->customer_type = 'POS';
            $customer->save();
        }

        if (!empty($customer)) {
            $customerID = $customer->user_id;
        } else {
            $customer = Customer::where('user_id', $request->user_id)->first();
            $customerID = $customer->user_id;
        }

        if ($request->user_id) {
            $user_id = $request->user_id;
        } elseif (!empty($user->id)) {
            $user_id = $user->id;
        }



        if (Session::has('pos.cart') && count(Session::get('pos.cart')) > 0) {

            $order = new Order;
            $shipping_info = Session::get('pos.shipping_info');

            if ($request->user_id == null && (empty($user->id))) {
                $order->guest_id    = mt_rand(100000, 999999);
            } else {
                $order->user_id = $user_id;
            }

            $exists = Order::whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get()->take(1);
            if (count($exists) > 0) {
                $code = date('dmy') . substr($exists[0]->code, -4);
                $code = ((int)$code) + 1;
            } else {
                $code = date('dmy') . '0001';
            }

            if (!empty($shipping_info)) {
                $data['name']           = $shipping_info['name'];
                $data['address']        = $shipping_info['address'];
                $data['phone']          = $shipping_info['phone'];
            }

            if (!empty($shipping_info)) {
                $order->shipping_address = json_encode($data);
            }

            if ($request->receive_amount >= $request->total_amount) {
                $cash_collection = $request->total_amount;
                $status = 'paid';
                $due = 0;
            } elseif ($request->receive_amount < $request->total_amount && $request->receive_amount > 0) {
                $cash_collection = $request->receive_amount;
                $status = 'partial';
                $due = $request->total_amount - $request->receive_amount;
            } else {
                $cash_collection = 0;
                $status = 'unpaid';
                $due = $request->total_amount;
            }

            $payment_details = (object)[
                'amount' => $cash_collection,
                'status' => 'VALID',
                'error' => null
            ];


            $order->payment_type = $request->payment_type;
            $order->delivery_viewed = '0';
            $order->order_from = 'POS';
            $order->received_amount = $request->receive_amount;
            $order->change_amount = $request->change_amount;
            $order->warehouse = $length[0];
            $order->payment_status_viewed = '0';
            $order->code = $code;
            $order->date = strtotime('now');
            $order->payment_status = $status;
            $order->payment_details = json_encode($payment_details);
            $order->grand_total = $request->total_amount;
            if (Session::has('pos.discount')) {
                $order->coupon_discount = Session::get('pos.discount');
            }

            if ($order->save()) {
                $subtotal = 0;
                $tax = 0;
                foreach (Session::get('pos.cart') as $key => $cartItem) {
                    $product = Product::find($cartItem['id']);
                    $subtotal += $cartItem['price'] * $cartItem['quantity'];
                    $tax += $cartItem['tax'] * $cartItem['quantity'];

                    $order_detail = new OrderDetail;
                    $order_detail->order_id  = $order->id;
                    $order_detail->seller_id = $product->user_id;
                    $order_detail->product_id = $product->id;
                    $order_detail->payment_status = $status;
                    $order_detail->delivery_status = 'delivered';
                    $order_detail->price = $cartItem['price'] * $cartItem['quantity'];
                    $order_detail->tax = $cartItem['tax'] * $cartItem['quantity'];
                    $order_detail->quantity = $cartItem['quantity'];
                    $order_detail->shipping_type = null;

                    if (Session::get('pos.shipping', 0) >= 0) {
                        $order_detail->shipping_cost = Session::get('pos.shipping', 0) / count(Session::get('pos.cart'));
                    } else {
                        $order_detail->shipping_cost = 0;
                    }

                    $order_detail->save();

                    $product->num_of_sale++;
                    $product->save();
                }

                $order_status_log = new OrderStatusLog;
                $order_status_log->order_id = $order->id;
                $order_status_log->user_id = Auth::user()->id;
                $order_status_log->order_status = 'on_delivery';
                $order_status_log->order_code = $order->code;
                $order_status_log->save();

                $customer_debit = new Customer_ledger();
                $customer_debit->customer_id = $customerID;
                $customer_debit->order_id = $order->id;
                $customer_debit->descriptions = "Order by cash_on_delivery";
                $customer_debit->type = 'Order';
                $customer_debit->debit = $request->total_amount;
                $customer_debit->credit = 0;
                $customer_debit->date = date('Y-m-d', strtotime($order->created_at));
                $customer_debit->save();

                DB::table('customer_ledger')->insert([
                    'customer_id' => $customerID,
                    'order_id' => $order->id,
                    'descriptions' => "Cash Payment",
                    'type' => 'Payment',
                    'debit' => 0,
                    'credit' => $cash_collection,
                    'date' => date('Y-m-d', strtotime($order->created_at)),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                DB::table('pos_ledger')->insert([
                    'order_id' => $order->id,
                    'warehouse_id' => $length[0],
                    'type' => 'Order',
                    'order_amount' => $request->total_amount,
                    'due' => $due,
                    'debit' => $cash_collection,
                    'date' => date('Y-m-d', strtotime($order->created_at)),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                Session::forget('pos.shipping_info');
                Session::forget('pos.shipping');
                Session::forget('pos.discount');
                Session::forget('pos.cart');

                if ($order->id) {
                    $newRoute = url('/pos/gen-invoice/' . $order->id);
                    return response()->json([
                        'success' => 1,
                        'redirect' => $newRoute,
                    ]);
                }
            }
        }
        return array('success' => 0, 'message' => translate("Please select a product."));
    }

    public function order_store(Request $request)
    {
        $customer = null;
        $staff = Staff::where('user_id', Auth::user()->id)->first();

        if (empty($staff)) {
            return array('success' => 0, 'message' => translate('You are not permitted to create POS Order.'));
        }

        if ($staff) {
            $length = unserialize($staff->warehouse_id);
            $count = count($length);
            if ($count > 1) {
                return array('success' => 0, 'message' => translate('Multi Warehouse Permitted User Not Allowed.'));
            }
        }

        $shipping_info = Session::get('pos.shipping_info');

        if ($request->user_id == null && !empty($shipping_info)) {
            $user = User::create([
                'name' => $shipping_info['name'],
                'address' => $shipping_info['address'],
                'phone' => $shipping_info['phone'],
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('Bz123456'),
            ]);

            if ($length[0] == 2) {
                $areacode = '43';
            } elseif ($length[0] == 3) {
                $areacode = '01';
            }

            $exists = Customer::where('area_code', $areacode)
                ->orderBy('created_at', 'desc')->first();

            if ($exists) {
                $customer_id = (int)$exists->customer_id + 1;
                if (strlen((string)$customer_id) < 8) {
                    $customer_id = '0' . $customer_id;
                }
            }

            $customer = new Customer;
            $customer->user_id = $user->id;
            $customer->area_code = $areacode;
            $customer->customer_id = $customer_id;
            $customer->customer_type = 'POS';
            $customer->save();
        }

        if (!empty($customer)) {
            $customerID = $customer->user_id;
        } else {
            $customer = Customer::where('user_id', $request->user_id)->first();
            $customerID = $customer->user_id;
        }

        if ($request->user_id) {
            $user_id = $request->user_id;
        } elseif (!empty($user->id)) {
            $user_id = $user->id;
        }

        if (Session::has('pos.cart') && count(Session::get('pos.cart')) > 0) {
            DB::beginTransaction();
            try {
                $order = new Order;
                $shipping_info = Session::get('pos.shipping_info');

                if ($request->user_id == null && empty($user->id)) {
                    $order->guest_id = mt_rand(100000, 999999);
                } else {
                    $order->user_id = $user_id;
                }

                $exists = Order::whereMonth('created_at', date('m'))
                    ->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->first();

                if ($exists) {
                    $code = date('dmy') . substr($exists->code, -4);
                    $code = ((int)$code) + 1;
                } else {
                    $code = date('dmy') . '0001';
                }

                if (!empty($shipping_info)) {
                    $data['name'] = $shipping_info['name'];
                    $data['address'] = $shipping_info['address'];
                    $data['phone'] = $shipping_info['phone'];
                }

                if (!empty($shipping_info)) {
                    $order->shipping_address = json_encode($data);
                }

                if ($request->receive_amount >= $request->total_amount) {
                    $cash_collection = $request->total_amount;
                    $status = 'paid';
                    $due = 0;
                } elseif ($request->receive_amount < $request->total_amount && $request->receive_amount > 0) {
                    $cash_collection = $request->receive_amount;
                    $status = 'partial';
                    $due = $request->total_amount - $request->receive_amount;
                } else {
                    $cash_collection = 0;
                    $status = 'unpaid';
                    $due = $request->total_amount;
                }

                $payment_details = (object)[
                    'amount' => $cash_collection,
                    'status' => 'VALID',
                    'error' => null
                ];

                $order->payment_type = $request->payment_type;
                $order->delivery_viewed = '0';
                $order->order_from = 'POS';
                $order->received_amount = $request->receive_amount;
                $order->change_amount = $request->change_amount;
                $order->cash_collection = $request->receive_amount - $request->change_amount;
                $order->warehouse = $length[0];
                $order->payment_status_viewed = '0';
                $order->code = $code;
                $order->online_order_delivery_status = "on_delivery";
                $order->date = strtotime('now');
                // $order->delivered_date = now();
                $order->payment_status = $status;
                $order->payment_details = json_encode($payment_details);
                $order->grand_total = $request->total_amount;

                if (Session::has('pos.discount')) {
                    $order->coupon_discount = Session::get('pos.discount');
                }

                if ($order->save()) {
                    $subtotal = 0;
                    $tax = 0;

                    foreach (Session::get('pos.cart') as $key => $cartItem) {
                        $product = Product::find($cartItem['id']);
                        $subtotal += $cartItem['price'] * $cartItem['quantity'];
                        $tax += $cartItem['tax'] * $cartItem['quantity'];

                        $order_detail = new OrderDetail;
                        $order_detail->order_id = $order->id;
                        $order_detail->seller_id = $product->user_id;
                        $order_detail->product_id = $product->id;
                        $order_detail->payment_status = $status;
                        $order_detail->delivery_status = 'on_delivery';
                        $order_detail->price = $cartItem['price'] * $cartItem['quantity'];
                        $order_detail->tax = $cartItem['tax'] * $cartItem['quantity'];
                        $order_detail->quantity = $cartItem['quantity'];
                        $order_detail->shipping_type = null;

                        if (Session::get('pos.shipping', 0) >= 0) {
                            $order_detail->shipping_cost = Session::get('pos.shipping', 0) / count(Session::get('pos.cart'));
                        } else {
                            $order_detail->shipping_cost = 0;
                        }

                        $order_detail->save();

                        $product->num_of_sale++;
                        $product->save();
                    }

                    $order_status_log = new OrderStatusLog;
                    $order_status_log->order_id = $order->id;
                    $order_status_log->user_id = Auth::user()->id;
                    $order_status_log->order_status = 'on_delivery';
                    $order_status_log->order_code = $order->code;
                    $order_status_log->save();

                    $customer_debit = new Customer_ledger();
                    $customer_debit->customer_id = $customerID;
                    $customer_debit->order_id = $order->id;
                    $customer_debit->descriptions = "Order by cash_on_delivery";
                    $customer_debit->type = 'Order';
                    $customer_debit->debit = $request->total_amount;
                    $customer_debit->credit = 0;
                    $customer_debit->date = date('Y-m-d', strtotime($order->created_at));
                    $customer_debit->save();

                    DB::table('customer_ledger')->insert([
                        'customer_id' => $customerID,
                        'order_id' => $order->id,
                        'descriptions' => "Cash Payment",
                        'type' => 'Payment',
                        'debit' => 0,
                        'credit' => $cash_collection,
                        'date' => date('Y-m-d', strtotime($order->created_at)),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);

                    DB::table('pos_ledger')->insert([
                        'order_id' => $order->id,
                        'warehouse_id' => $length[0],
                        'type' => 'Order',
                        'order_amount' => $request->total_amount,
                        'due' => $due,
                        'debit' => $cash_collection,
                        'date' => date('Y-m-d', strtotime($order->created_at)),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);

                    Session::forget('pos.shipping_info');
                    Session::forget('pos.shipping');
                    Session::forget('pos.discount');
                    Session::forget('pos.cart');

                    DB::commit();

                    if ($order->id) {
                        $newRoute = url('/pos/gen-invoice/' . $order->id);
                        return response()->json([
                            'success' => 1,
                            'redirect' => $newRoute,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                DB::rollback();
                return array('success' => 0, 'message' => $e->getMessage());
            }
        }

        return array('success' => 0, 'message' => translate("Please select a product."));
    }

    public function gen_invoice($id)
    {
        $order = Order::findOrfail($id);
        $customer_information = json_decode($order->shipping_address);
        $user_all_orders = Order::where('id', '!=', $id)
            ->where('user_id', $order->user_id)
            ->where('online_order_delivery_status', 'delivered')
            ->whereNotNull('payment_details')
            ->selectRaw('grand_total, payment_details, id, code, grand_total - CAST(JSON_UNQUOTE(JSON_EXTRACT(payment_details, "$.amount")) AS DECIMAL(10,2)) as due_amount')
            ->get();

        $due_count = 0;
        $total_due_counts = 0;
        foreach ($user_all_orders as $user_order) {
            if ($user_order->due_amount > 0) {
                $due_count += $user_order->due_amount;
            }
        }

        $user_all_orders_with_recent = Order::where('user_id', $order->user_id)
            ->where('online_order_delivery_status', 'delivered')
            // ->whereNotNull('payment_details')
            ->selectRaw('grand_total, payment_details, id, code, due_amount')
            ->get();

        $refund_amount = 0;
        $order_ids = $user_all_orders_with_recent->pluck('id');
        $total_refunds = RefundRequest::whereIn('order_id', $order_ids)
            ->where('refund_status', 5)
        ->pluck('refund_amount', 'order_id');

        foreach ($user_all_orders_with_recent as $user_order) {
                $refund_amount = $total_refunds[$user_order->id] ?? 0;

            if ($user_order->due_amount - $refund_amount > 0) {
                $total_due_counts += $user_order->due_amount - $refund_amount;
            }
        }

        $paid = 0;
        if (!empty($order->payment_details)) {
            $payment = json_decode($order->payment_details);
            if (!empty($payment)) {
                $paid = $payment->amount;
            }
        }

 
        $total_due_count = $total_due_counts;
        return view('pos.invoice', compact('order', 'customer_information', 'due_count','total_due_count'));
    }

    public function pos_amount_transfer(Request $request)
    {
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $pendingceck = Pos_ledger::where('accounts_executive_status', 'Pending')->where('warehouse_id', $warehousearray)->first();
        if (!empty($pendingceck)) {
            return array(
                'success' => 0,
            );
        }
        $amount = $request->paymentAmountbyid;
        DB::table('pos_ledger')->insert([
            'order_id' => '',
            'warehouse_id' => $warehousearray[0],
            'type' => 'Payment',
            'order_amount' => 0,
            'debit' => 0,
            'credit' => $amount,
            'accounts_executive_status' => 'Pending',
            'date' => date('Y-m-d', strtotime(Carbon::now())),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return array(
            'success' => 1,
        );
    }

    public function pos_amount_transfer_accept($id)
    {
        $pos_ledger = Pos_ledger::find($id);
        $pos_ledger->accounts_executive_status = 'Accepted';
        $pos_ledger->save();
        return back();
    }

    public function staff_pos_ledger(Request $request)
    {
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $pos_type = null;
        $status = $request->status;
        if (!empty($request->start_date))
            $start_date = $request->start_date;
        if (!empty($request->end_date))
            $end_date = $request->end_date;

        $cust = Pos_ledger::where('warehouse_id', $warehousearray)->first();
        if ($cust) {
            $sql = "SELECT
            w.id,w.name,pol.id as poslid,pol.warehouse_id,pol.order_id,pol.order_amount,pol.due,pol.date,pol.type,pol.accounts_executive_status,
            
            pol.debit as debit,pol.credit as credit,pol.balance as balance
        FROM
            warehouses w
            LEFT JOIN pos_ledger pol ON w.id = pol.warehouse_id";

            $sql .= "	where w.id = $cust->warehouse_id and pol.date between '" . $start_date . "' and '" . $end_date . "'";

            if (!empty($request->pos_type)) {
                $pos_type = $request->pos_type;
                $sql .= " AND pol.type = '$pos_type'";
            }
            if (!empty($status)) {
                $sql .= " AND pol.accounts_executive_status = '$status'";
            }
            $sql .= " order by pol.date,pol.id asc";

            $statements = DB::select($sql);


            $sql = "SELECT sum(pol.debit-pol.credit) as opening_balance
            FROM
                warehouses w
                LEFT JOIN pos_ledger pol ON w.id = pol.warehouse_id";
            $sql .= "	where w.id = $cust->warehouse_id and pol.date < '" . $start_date . "'";

            $opening = DB::select($sql);
            return view('backend.reports.staff_pos_ledger', compact('statements', 'cust', 'start_date', 'end_date', 'opening', 'pos_type', 'status'));
        }
        flash("No Tranjection Found")->success();
        return back();
    }

    public function purchase_withbarcode(Request $request)
    {
        $products = Product::where('barcode', $request->barcode)->where('parent_id', null)->get();

        if ($products->isNotEmpty()) {
            $product = $products->first();
            $stock = ProductStock::where('product_id', $product->id)->where('wearhouse_id', $request->wearhouse_id)->first();

            if ($stock) {
                return response()->json([
                    'success' => 1,
                    'product' => $product,
                    'product_id' => $product->id,
                    'barcode' => $product->barcode,
                    'stock' => $stock->qty,
                    'qty' => 1,
                    'unit_price' => $product->purchase_price,
                ]);
            } else {
                return response()->json([
                    'success' => 0,
                    'message' => 'Product Stock Not Found for the specified warehouse',
                ]);
            }
        } else {
            return response()->json([
                'success' => 0,
                'message' => 'Product Not Found or This is a Child Product',
            ]);
        }
    }
}
