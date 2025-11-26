<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\OTPVerificationController;
use App\Http\Controllers\ClubPointController;
use App\Http\Controllers\AffiliateController;
use App\Models\Order;
use App\Models\Damage;
use App\Models\RefundRequest;
use App\Models\Transfer;
use App\Models\Pos_ledger;
use Illuminate\Support\Carbon;
use App\Models\Customer_ledger;
use App\Models\Supplier_ledger;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\OrderDetail;
use App\Models\CouponUsage;
use App\Models\Coupon;
use App\Models\User;
use App\Models\OpeningStock;
use App\Models\BusinessSetting;
use App\Models\Warehouse;
use App\Models\OrderStatusLog;
use App\Models\Group_product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\DeliveryExecutiveLedger;
use App\Models\Supplier;
use App\Models\Offer;
use App\Models\Wallet;
use PHPUnit\Framework\Constraint\Count;
use Session;

use function PHPUnit\Framework\isEmpty;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource to seller.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $orders = DB::table('orders')
            ->orderBy('code', 'desc')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('order_details.seller_id', Auth::user()->id)
            ->select('orders.id')
            ->distinct();

        if ($request->payment_status != null) {
            $orders = $orders->where('order_details.payment_status', $request->payment_status);
            $payment_status = $request->payment_status;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('order_details.delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }

        $orders = $orders->paginate(15);

        foreach ($orders as $key => $value) {
            $order = \App\Models\Order::find($value->id);
            $order->viewed = 1;
            $order->save();
        }

        return view('frontend.user.seller.orders', compact('orders', 'payment_status', 'delivery_status', 'sort_search'));
    }

    // All Orders
    public function purchase_orders(Request $request)
    {
        $date = $request->date;
        $sort_search = '';
        DB::enableQueryLog();
        $data = Purchase::select('purchases.*', 'suppliers.supplier_id', 'suppliers.name')->leftjoin('suppliers', 'suppliers.supplier_id', '=', 'purchases.supplier_id')->orderBy('purchases.date', 'desc');

        if ($date != null) {
            $data = $data->where('purchases.date', '>=', date('Y-m-d 00:00:00', strtotime(explode(" to ", $date)[0])))->where('purchases.date', '<=', date('Y-m-d 23:59:59', strtotime(explode(" to ", $date)[1])));
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $data = $data->where('purchase_no', 'like', '%' . $sort_search . '%');
        }
        //  dd(DB::getQueryLog());
        $data = $data->paginate(15);
        $title =  'Purchase Order';
        return view('backend.purchase_order.index', compact('data', 'title', 'date', 'sort_search'));
    }

    function updatePurchasePrice()
    {
        $data = DB::select("SELECT p.product_id,(select p1.price from purchase_details as p1 where p.product_id=p1.product_id order by p1.created_at desc limit 1) as price FROM `purchase_details` as p group by product_id");

        foreach ($data as $pp) {
            try {
                $p = Product::find($pp->product_id);
                if ($p != null) {
                    $p->purchase_price = $pp->price;
                    $p->save();
                }
            } catch (\Exception $e) {
                dd($e);
            }
        }
    }
    public function add_purchase(Request $request)
    {
        $products = Product::where('parent_id', '=', null)->get();
        $supplier = Supplier::all();
        $title =  'Purchase Add';
        $wearhouses = Warehouse::get();
        // foreach($products as $row){
        //     ProductStock::insert(['product_id'=>$row->id,'wearhouse_id'=>1,'price'=>$row->unit_price,'qty'=>$row->current_stock]);
        // }
        return view('backend.purchase_order.add', compact('products', 'title', 'supplier', 'wearhouses'));
    }
    public function store_purchase(Request $request)
    {
        if (!empty($request)) {
            $purchase = new Purchase();

            $exists = Purchase::whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get()->take(1);
            if (count($exists) > 0) {
                $code = date('dmy') . substr($exists[0]->purchase_no, -4);
                $code = ((int)$code) + 1;
            } else {
                $code = date('dmy') . '0001';
            }

            $purchase->purchase_no = $code;
            $purchase->supplier_id = $request->supplier_id;
            $purchase->date =  $request->purchase_date;
            $purchase->total_value =  $request->total;
            $purchase->created_by = Auth::user()->id;
            $purchase->remarks = $request->remarks;
            $purchase->wearhouse_id = $request->wearhouse_id;
            $purchase->chalan_no = $request->chalan_no;
            $purchase->voucher_img = $request->voucher_img;
            $purchase->save();
            if (!empty($purchase->id)) {

                foreach ($request->product as $key => $prod) {
                    $item = new PurchaseDetail();
                    $item->purchase_id = $purchase->id;
                    $item->product_id = $prod;
                    $item->qty = $request->qty[$key];
                    $item->wearhouse_id = $request->wearhouse_id;
                    $item->expiry_date = $request->exp[$key];
                    $item->desc = $request->desc[$key];
                    $item->price = $request->price[$key];
                    $item->amount = $request->price[$key] * $request->qty[$key];

                    $item->stock_qty = $request->qty[$key];
                    $item->stock_amount = $request->price[$key] * $request->qty[$key];

                    $item->save();

                    if (!empty($item->id)) {
                        $p = Product::find($prod);
                        $p->increment('current_stock', $request->qty[$key]);
                        $p->expiry_date = $request->exp[$key];
                        $p->purchase_price = $request->price[$key];
                        $p->save();
                    }
                }



                if (Auth::user()->user_type == 'admin') {
                    return redirect()->route('purchase_orders.index');
                } else {
                    return redirect()->route('purchase_list_for_purchase_executive.index');
                }
            } else {
                flash(translate('Something went wrong'))->error();
                return back();
            }
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function puracher_edit($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase_item = PurchaseDetail::where('purchase_id', $purchase->id)->get();
        //dd($purchase_item);
        $products = Product::all();
        $supplier = Supplier::all();
        $title =  'Purchase Edit';
        $wearhouses = Warehouse::all();
        $data_item_rows = PurchaseDetail::where('purchase_id', $id)
            ->join('products', 'products.id', '=', 'purchase_details.product_id')
            ->get();
        if (Auth::user()->user_type == 'admin') {
            return view('backend.purchase_order.edit', compact('products', 'title', 'supplier', 'purchase', 'purchase_item', 'wearhouses', 'data_item_rows'));
        } else {
            return view('backend.staff_panel.purchase_executive.purchase_edit', compact('products', 'title', 'supplier', 'purchase', 'purchase_item', 'wearhouses', 'data_item_rows'));
        }
    }

    public function purchase_delete($id)
    {

        $purchase = Purchase::where('id', $id)->delete();
        if ($purchase == true) {

            PurchaseDetail::where('id', $id)->delete();
            flash('Purchase Order Deleted SusscessFully')->success();
            return back();
        }
    }

    public function puracher_edit_store(Request $request)
    {
        if (!empty($request->purchase_no)) {
            //dd($request->wearhouse_id); 
            $purchase = Purchase::findOrFail($request->id);

            $purchase->purchase_no = $request->purchase_no;
            $purchase->supplier_id = $request->supplier_id;
            $purchase->date =  $request->purchase_date;
            $purchase->total_value =  $request->total;
            $purchase->created_by = Auth::user()->id;
            $purchase->remarks = $request->remarks;
            $purchase->chalan_no = $request->chalan_no;
            $purchase->voucher_img = $request->voucher_img;
            //$purchase->save();
            if ($purchase->save()) {
                //$pur_item = PurchaseDetail::where('id', $request->id)->get();
                //dd($pur_item);
                // foreach($pur_item as $row){
                //     $ops = ProductStock::where(['product_id'=>$row->product_id,'wearhouse_id'=>$request->wearhouse_id])->first();
                //     $ops->decrement('qty', $row->qty);
                //     $ops->save();
                // }
                PurchaseDetail::where('purchase_id', $request->id)->delete();


                foreach ($request->product as $key => $prod) {
                    $item = new PurchaseDetail();
                    $item->purchase_id = $purchase->id;
                    $item->product_id = $prod;
                    $item->qty = $request->qty[$key];
                    $item->wearhouse_id = $request->wearhouse_id;
                    $item->desc = $request->desc[$key];
                    $item->price = $request->price[$key];
                    $item->amount = $request->price[$key] * $request->qty[$key];

                    $item->stock_qty = $request->qty[$key];
                    $item->stock_amount = $request->price[$key] * $request->qty[$key];

                    $item->save();

                    $p = Product::find($prod);
                    $p->purchase_price = $request->price[$key];
                    $p->save();
                    // $ps = ProductStock::where(['product_id'=>$prod,'wearhouse_id'=>$request->wearhouse_id])->first();

                    // if($ps){
                    //     $ps->increment('qty', $request->qty[$key]);
                    //     $ps->save();
                    // }else{

                    //     ProductStock::insert(['product_id'=>$prod,'wearhouse_id'=>$request->wearhouse_id,'qty'=>$request->qty[$key]]);
                    // }
                }

                Supplier_ledger::where(array('supplier_id' => $request->supplier_id, 'purchase_id' => $purchase->id, 'type' => 'Purchase', 'descriptions' => 'Purchase Order'))->update(array('debit' => $request->total));
                flash(translate('Purchase updated successfully'))->success();

                if (Auth::user()->user_type == 'admin') {
                    return redirect()->route('purchase_orders.index');
                } else {
                    return redirect('/purchase_list_for_purchase_executive');
                }
            } else {
                flash(translate('Something went wrong'))->error();
                return back();
            }
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function purchase_orders_view($id)
    {
        $purchase = Purchase::where('id', $id)
            ->join('suppliers', 'suppliers.supplier_id', '=', 'purchases.supplier_id')
            ->select('purchases.*', 'suppliers.name')
            ->get();

        $data_item_rows = PurchaseDetail::where('purchase_id', $id)
            ->join('products', 'products.id', '=', 'purchase_details.product_id')
            ->get();

        if (Auth::user()->user_type == 'admin') {
            return view('backend.purchase_order.view', compact('purchase', 'data_item_rows'));
        } else {
            return view('backend.staff_panel.purchase_executive.purchase_view', compact('purchase', 'data_item_rows'));
        }
    }

    public function get_purchase_details($id)
    {
        $purchase = Purchase::where('purchases.id', $id)
            ->join('suppliers', 'suppliers.supplier_id', '=', 'purchases.supplier_id')
            ->select('purchases.*', 'suppliers.name as supplier_name')
            ->first();

        if (!$purchase) {
            return response()->json(['error' => 'Purchase not found'], 404);
        }

        $details = PurchaseDetail::where('purchase_id', $id)
            ->join('products', 'products.id', '=', 'purchase_details.product_id')
            ->select('purchase_details.*', 'products.name as product_name')
            ->get();

        $detailsFormatted = $details->map(function($item) {
            return [
                'product_name' => $item->product_name,
                'qty' => $item->qty,
                'price' => $item->price,
                'price_formatted' => single_price($item->price),
                'amount' => $item->amount,
                'amount_formatted' => single_price($item->amount),
            ];
        });

        return response()->json([
            'purchase_no' => $purchase->purchase_no,
            'date' => $purchase->date,
            'supplier_name' => $purchase->supplier_name,
            'chalan_no' => $purchase->chalan_no,
            'remarks' => $purchase->remarks,
            'total_value' => $purchase->total_value,
            'total_value_formatted' => single_price($purchase->total_value),
            'voucher_img' => $purchase->voucher_img,
            'voucher_url' => $purchase->voucher_img ? uploaded_asset($purchase->voucher_img) : null,
            'details' => $detailsFormatted,
        ]);
    }

    public function purchase_update_payment_status(Request $request)
    {
        $purchase_id = $request->purchase_id;
        $purchase = Purchase::findOrFail($purchase_id);
        $total_pay = ($purchase->payment_amount + $request->payment_amount);
        if ($total_pay >= $purchase->total_value) {
            $pstatus = 3;
        } else {
            $pstatus = $request->status;
        }
        $purchase->payment_amount = ($purchase->payment_amount + $request->payment_amount);
        $purchase->payment_status = $pstatus;
        if ($purchase->save()) {
            $supplier_ledger = new Supplier_ledger();
            $supplier_ledger->supplier_id = $purchase->supplier_id;
            $supplier_ledger->purchase_id = $purchase->id;
            $supplier_ledger->descriptions = 'Purchase Order';
            $supplier_ledger->type = 'Payment';
            $supplier_ledger->debit = 0;
            $supplier_ledger->credit = $request->payment_amount;
            $supplier_ledger->date = $request->payment_date;
            $supplier_ledger->save();
        }
        return 1;
    }

    // All Orders
    public function all_orders(Request $request)
    {
        $user = auth()->user();

        $sort_search = $request->input('search');
        $delivery_status = $request->input('delivery_status');
        $payment_status = $request->input('payment_status');
        $order_from = $request->input('order_from');
        $customer_type = $request->input('customer_type');
        $warehouse = $request->input('warehouse');
        $date = $request->input('date');

        $orders = Order::query();

        // Search by order code
        if ($sort_search) {
            $orders->where('code', 'like', '%' . $sort_search . '%');
        }

        // Filter by delivery status
        if ($delivery_status) {
            if (!is_array($delivery_status)) {
                $delivery_status = [$delivery_status];
            }
            $orders->whereIn('delivery_status', $delivery_status);
        }

        // Filter by payment status
        if ($payment_status) {
            $orders->where('orders.payment_status', $payment_status);
        }

        // Filter by order from
        if ($order_from) {
            if (!is_array($order_from)) {
                $order_from = [$order_from];
            }
            $orders->whereIn('order_from', $order_from);
        }

        // Filter by customer type
        if ($customer_type) {
            $orders->join('customers', 'orders.user_id', '=', 'customers.user_id')
                ->where('customers.customer_type', $customer_type);
        }

        $user_name_check = auth()->user()->name;

        $canSeeAllWarehouses =
            $user->user_type == 'admin' ||
            ($user->user_type == 'staff' && $user_name_check == 'Account Department');

        if (!$canSeeAllWarehouses) {
            $warehousearray = getWearhouseBuUserId($user->id);
            $orders->whereIn('orders.warehouse', $warehousearray);
        }

        // Filter by warehouse
        if ($warehouse) {
            $orders->where('orders.warehouse', $warehouse);
        }

        // Filter by date range
        if ($date) {
            [$startDate, $endDate] = explode(' to ', $date);
            $orders->whereBetween('orders.created_at', [
                date('Y-m-d 00:00:00', strtotime($startDate)),
                date('Y-m-d 23:59:59', strtotime($endDate))
            ]);
        }

        // Join with order_details and order by created_at
        $orders->leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')
            ->select('orders.*', 'order_details.delivery_status')
            ->orderBy('orders.created_at', 'desc');

        // Filter based on user role
        switch ($user->name) {
            case 'Delivery Department':
                $orders->whereIn('order_details.delivery_status', ['on_delivery', 'delivered']);
                break;
            case 'Operational Department':
                $orders->whereIn('order_details.delivery_status', ['confirmed', 'on_delivery', 'delivered']);
                break;
            case 'Sales Department':
                $orders->whereIn('order_details.delivery_status', ['pending', 'cancel', 'confirmed', 'on_delivery', 'delivered']);
                break;
        }

        // Group and paginate orders
        $orders = $orders->groupBy('orders.id')->paginate(15);
        return view('backend.sales.all_orders.index', compact('orders', 'sort_search', 'date', 'delivery_status', 'payment_status', 'order_from', 'customer_type', 'warehouse'));
    }

    public function all_orders___(Request $request)
    {
        $user = Auth::user();

        $sort_search = $request->input('search');
        $delivery_status = $request->input('delivery_status');
        $payment_status = $request->input('payment_status');
        $order_from = $request->input('order_from');
        $customer_type = $request->input('customer_type');
        $warehouse = $request->input('warehouse');
        $date = $request->input('date');

        $orders = Order::query();

        // Apply filters
        if ($sort_search = $request->input('search')) {
            $orders->where('code', 'like', "%{$sort_search}%");
        }

        if ($delivery_status = $request->input('delivery_status')) {
            $orders->whereIn('delivery_status', (array) $delivery_status);
        }

        if ($payment_status = $request->input('payment_status')) {
            $orders->where('payment_status', $payment_status);
        }

        if ($order_from = $request->input('order_from')) {
            $orders->whereIn('order_from', (array) $order_from);
        }

        if ($customer_type = $request->input('customer_type')) {
            $orders->whereHas('customer', function ($query) use ($customer_type) {
                $query->where('customer_type', $customer_type);
            });
        }

        if ($warehouse = $request->input('warehouse')) {
            $orders->where('warehouse', $warehouse);
        }

        if ($date = $request->input('date')) {
            [$startDate, $endDate] = explode(' to ', $date);
            $orders->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Filter based on user role
        $roleFilters = [
            'Delivery Department' => ['on_delivery', 'delivered'],
            'Operational Department' => ['confirmed', 'on_delivery', 'delivered'],
            'Sales Department' => ['pending', 'cancel', 'confirmed', 'on_delivery', 'delivered'],
        ];

        if (isset($roleFilters[$user->name])) {
            $orders->whereHas('orderDetails', function ($query) use ($roleFilters, $user) {
                $query->whereIn('delivery_status', $roleFilters[$user->name]);
            });
        }

        $orders = $orders->with('customer', 'orderDetails')->paginate(15);

        return view('backend.sales.all_orders.index', compact('orders', 'sort_search', 'date', 'delivery_status', 'payment_status', 'order_from', 'customer_type', 'warehouse'));
    }

    public function pending_orders()
    {
        $pending_order = Order::where('order_details.delivery_status', 'pending')
            ->leftjoin('customers', 'customers.user_id', 'orders.user_id')
            ->leftjoin('areas', 'areas.code', '=', 'customers.area_code')
            ->leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')
            ->groupBy('orders.id')
            ->select(
                'orders.code',
                'orders.warehouse',
                'orders.grand_total',
                'orders.id',
                'orders.user_id',
                'orders.guest_id',
                'orders.shipping_address',
                'customers.customer_id',
                'customers.staff_id',
                'areas.name as areaname',
                'order_details.delivery_status'
            )->get();
        return view('backend.sales.all_orders.pending_orders', compact('pending_order'));
    }

    public function all_orders_show($id)
    {
        $order = Order::findOrFail(decrypt($id));

        $user_all_orders_without_recent = Order::where('id', '!=', decrypt($id))
            ->where('user_id', $order->user_id)
            ->where('online_order_delivery_status', 'delivered')
            // ->whereNotNull('payment_details')
            ->selectRaw('grand_total, payment_details, id, code, due_amount')
            ->get();

        $user_all_orders_with_recent = Order::where('user_id', $order->user_id)
            ->where('online_order_delivery_status', 'delivered')
            // ->whereNotNull('payment_details')
            ->selectRaw('grand_total, payment_details, id, code, due_amount')
            ->get();


        return view('backend.sales.all_orders.show', compact('order', 'user_all_orders_without_recent', 'user_all_orders_with_recent'));
    }

    public function all_order_details($id)
    {
        $order = Order::findOrFail(decrypt($id));
        return view('backend.staff_panel.order_show', compact('order'));
    }
    public function staff_order_show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        return view('backend.staff_panel.customer_service.all_orders.show', compact('order'));
    }

    // Inhouse Orders
    public function admin_orders(Request $request)
    {

        $date = $request->date;
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $admin_user_id = User::where('user_type', 'admin')->first()->id;
        $orders = DB::table('orders')
            ->orderBy('code', 'desc')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('order_details.seller_id', $admin_user_id)
            ->select('orders.id')
            ->distinct();

        if ($request->payment_type != null) {
            $orders = $orders->where('order_details.payment_status', $request->payment_type);
            $payment_status = $request->payment_type;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('order_details.delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        if ($date != null) {
            $orders = $orders->where('orders.created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->where('orders.created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }

        $orders = $orders->paginate(15);
        return view('backend.sales.inhouse_orders.index', compact('orders', 'payment_status', 'delivery_status', 'sort_search', 'admin_user_id', 'date'));
    }

    public function show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $order->viewed = 1;
        $order->save();
        return view('backend.sales.inhouse_orders.show', compact('order'));
    }

    // Seller Orders
    public function seller_orders(Request $request)
    {

        $date = $request->date;
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $admin_user_id = User::where('user_type', 'admin')->first()->id;
        $orders = DB::table('orders')
            ->orderBy('code', 'desc')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('order_details.seller_id', '!=', $admin_user_id)
            ->select('orders.id')
            ->distinct();

        if ($request->payment_type != null) {
            $orders = $orders->where('order_details.payment_status', $request->payment_type);
            $payment_status = $request->payment_type;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('order_details.delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        if ($date != null) {
            $orders = $orders->where('orders.created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->where('orders.created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }

        $orders = $orders->paginate(15);
        return view('backend.sales.seller_orders.index', compact('orders', 'payment_status', 'delivery_status', 'sort_search', 'admin_user_id', 'date'));
    }

    public function seller_orders_show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $order->viewed = 1;
        $order->save();
        return view('backend.sales.seller_orders.show', compact('order'));
    }


    public function pickup_point_order_sales_show($id)
    {
        if (Auth::user()->user_type == 'staff') {
            $order = Order::findOrFail(decrypt($id));
            return view('backend.sales.pickup_point_orders.show', compact('order'));
        } else {
            $order = Order::findOrFail(decrypt($id));
            return view('backend.sales.pickup_point_orders.show', compact('order'));
        }
    }

    /**
     * Display a single sale to admin.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->customer_ledger_resolve();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store_old(Request $request)
    // {
    //     $order = new Order;
    //     if (Auth::check()) {
    //         $order->user_id = Auth::user()->id;
    //         $customer_id = Auth::user()->id;
    //     } else {
    //         $exists = Order::where('guest_id', "!=", null)->orderBy('guest_id', 'desc')->get()->take(1);

    //         if (count($exists) > 0) {
    //             $guest_id = $exists[0]->guest_id;
    //             $guest_id++;
    //         } else {
    //             $guest_id = '10000001';
    //         }
    //         $order->guest_id = $guest_id;
    //         $customer_id = $guest_id;
    //     }

    //     $order->shipping_address = json_encode($request->session()->get('shipping_info'));

    //     $order->payment_type = $request->payment_option;
    //     $order->delivery_viewed = '0';
    //     $order->payment_status_viewed = '0';
    //     $exists = Order::whereMonth('created_at', date('m'))
    //         ->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get()->take(1);
    //     if (count($exists) > 0) {
    //         $code = date('dmy') . substr($exists[0]->code, -4);
    //         $code = ((int)$code) + 1;
    //         // echo $code;
    //         // dd($exists);
    //     } else {
    //         $code = date('dmy') . '0001';
    //     }
    //     $order->code = $code;
    //     $order->order_from = 'Web';
    //     $order->date = strtotime('now');
    //     $order->user_ip = $request->ip();

    //     if ($order->save()) {

    //         $itemdiscount = 0;
    //         $actsubtotal = 0;
    //         $subtotal = 0;
    //         $tax = 0;
    //         $shipping = 0;

    //         //calculate shipping is to get shipping costs of different types
    //         $admin_products = array();
    //         $seller_products = array();

    //         //Order Details Storing


    //         $temp = Session::get('cart');
    //         $totalQuantity = collect($temp)->sum(function ($item) {
    //             return (int) $item['quantity'];
    //         });

    //         $PerProductDiscount = 0;

    //         if (Session::has('coupon_discount')) {
    //             $PerProductDiscount = Session::get('coupon_discount') / $totalQuantity;
    //         }



    //         foreach (Session::get('cart') as $key => $cartItem) {

    //             if (!isset($cartItem['shipping_type'])) {
    //                 $cartItem['shipping_type'] = 'home_delivery';
    //             }

    //             $product = Product::find($cartItem['id']);

    //             if ($product->added_by == 'admin') {
    //                 array_push($admin_products, $cartItem['id']);
    //             } else {
    //                 $product_ids = array();
    //                 if (array_key_exists($product->user_id, $seller_products)) {
    //                     $product_ids = $seller_products[$product->user_id];
    //                 }
    //                 array_push($product_ids, $cartItem['id']);
    //                 $seller_products[$product->user_id] = $product_ids;
    //             }

    //             if ($product->is_group_product) {
    //                 $itemdiscount += Group_product::where('group_product_id', $product->id)->sum('discount_amount');
    //             } else {
    //                 $itemdiscount += $product->discount * $cartItem['quantity'];
    //             }
    //             $actsubtotal += $product->unit_price * $cartItem['quantity'];
    //             $subtotal += $cartItem['price'] * $cartItem['quantity'];
    //             $tax += $cartItem['tax'] * $cartItem['quantity'];

    //             $product_variation = $cartItem['variant'];

    //             if ($product_variation != null) {
    //                 $product_stock = $product->stocks->where('variant', $product_variation)->first();
    //                 if ($product->digital != 1 &&  $product->outofstock == 1) {
    //                     flash(translate('The requested quantity is not available for ') . $product->getTranslation('name'))->warning();
    //                     $order->delete();
    //                     return redirect()->route('cart')->send();
    //                 } else {
    //                     $product_stock->qty -= $cartItem['quantity'];
    //                     $product_stock->save();
    //                 }
    //             } else {

    //                 if ($product->digital != 1 && $product->outofstock == 1) {
    //                     flash(translate('The requested quantity is not available for ') . $product->getTranslation('name'))->warning();
    //                     $order->delete();
    //                     return redirect()->route('cart')->send();
    //                 } else {
    //                     $product->current_stock -= $cartItem['quantity'];
    //                     $product->save();
    //                 }
    //             }

    //             $order_detail = new OrderDetail;
    //             $order_detail->order_id  = $order->id;
    //             $order_detail->seller_id = $product->user_id;
    //             $order_detail->product_id = $product->id;
    //             $order_detail->variation = $product_variation;
    //             $order_detail->price = $cartItem['price'] * $cartItem['quantity'];
    //             $order_detail->tax = $cartItem['tax'] * $cartItem['quantity'];
    //             $order_detail->shipping_type = !empty($cartItem['shipping_type']) ? $cartItem['shipping_type'] : 'home_delivery';
    //             if ($product->is_group_product) {
    //                 $group_discount = Group_product::where('group_product_id', $product->id)->sum('discount_amount');
    //                 $order_detail->discount = !empty($group_discount) ? $group_discount : '0';
    //             } else {
    //                 $order_detail->discount = !empty($product->discount * $cartItem['quantity']) ? $product->discount * $cartItem['quantity'] : '0';
    //             }
    //             if (Session::has('coupon_discount')) {
    //                 $order_detail->discount = $PerProductDiscount * $cartItem['quantity'];
    //             }
    //             if (!empty($cartItem['discount'])) {
    //                 $order_detail->special_discount = $cartItem['discount'];
    //             }
    //             $order_detail->product_referral_code = !empty($cartItem['product_referral_code']) ? $cartItem['product_referral_code'] : '';

    //             //Dividing Shipping Costs
    //             if ($cartItem['shipping_type'] == 'home_delivery') {
    //                 $order_detail->shipping_cost = getShippingCost($key);
    //             } else {
    //                 $order_detail->shipping_cost = 0;
    //             }

    //             $shipping += $order_detail->shipping_cost;

    //             if ($cartItem['shipping_type'] == 'pickup_point') {
    //                 $order_detail->pickup_point_id = $cartItem['pickup_point'];
    //             }
    //             //End of storing shipping cost
    //             $profit = (($cartItem['price'] * $cartItem['quantity']) - ($product->purchase_price * $cartItem['quantity']));
    //             $order_detail->profit = $profit;

    //             $order_detail->quantity = $cartItem['quantity'];
    //             $order_detail->save();

    //             $product->num_of_sale++;
    //             $product->save();
    //         }

    //         $shipping_skip_total = \App\Models\BusinessSetting::where('type', 'flat_rate_shipping_cost_total')->first()->value;
    //         if ($shipping_skip_total > $subtotal) {
    //             $shipping = \App\Models\BusinessSetting::where('type', 'flat_rate_shipping_cost')->first()->value;
    //         } else {
    //             $shipping = 0;
    //         }

    //         $order->grand_total = $subtotal + $tax + $shipping;

    //         if ($request->payment_option == 'bkash') {
    //             $bkashOffer = Offer::where('title', 'Bkash Offer')->where('status', 1)->get();
    //             if (count($bkashOffer) > 0) {
    //                 $dis = $bkashOffer[0]->discount;
    //                 $type = $bkashOffer[0]->discount_type;
    //                 if ($type == 'percent') {
    //                     $dis = ($order->grand_total * $dis) / 100;
    //                 }
    //                 $dd = Session::get('offer_discount');
    //                 Session::put('offer_discount', $dis + $dd);
    //             }
    //         }

    //         if (Session::has('coupon_discount')) {
    //             $order->grand_total -= Session::get('coupon_discount');
    //             $order->coupon_discount = Session::get('coupon_discount');
    //         }

    //         if (Session::has('coupon_id')) {
    //             $order->coupon_id = Session::get('coupon_id');
    //         }

    //         if (Session::has('offer_discount')) {
    //             $order->grand_total -= Session::get('offer_discount');
    //             $order->special_discount = Session::get('offer_discount');
    //         }
    //         $order->save();


    //         if (Session::has('offer_discount')) {
    //             $cust_ledger_dis = array();
    //             $cust_ledger_dis['customer_id'] = $customer_id;
    //             $cust_ledger_dis['order_id'] = $order->id;
    //             $cust_ledger_dis['descriptions'] = 'Offer Discount';
    //             $cust_ledger_dis['type'] = 'Discount';
    //             $cust_ledger_dis['debit'] = 0;
    //             $cust_ledger_dis['credit'] = Session::get('offer_discount');
    //             $cust_ledger_dis['date'] = date('Y-m-d');
    //             // save_customer_ledger($cust_ledger_dis); 
    //         }

    //         //Customer ledger start
    //         if ($request->payment_option == 'cash_on_delivery') {
    //             $desc = 'Order by cash on delivery';
    //         } elseif ($request->payment_option == 'sslcommerz') {
    //             $desc = 'Order by sslcommerz';
    //         } elseif ($request->payment_option == 'wallet') {
    //             $desc = 'Order by wallet';
    //         } elseif ($request->payment_option == 'bkash') {

    //             $desc = 'Order by bkash';
    //         } else {
    //             $desc = 'Order by ' . $request->payment_option;
    //         }
    //         $cltotl = $actsubtotal + $tax + $shipping;

    //         $cust_ledger = array();
    //         $cust_ledger['customer_id'] = $customer_id;
    //         $cust_ledger['order_id'] = $order->id;
    //         $cust_ledger['descriptions'] = $desc;
    //         $cust_ledger['type'] = 'Order';
    //         $cust_ledger['debit'] = $cltotl;
    //         $cust_ledger['credit'] = 0;
    //         $cust_ledger['date'] = date('Y-m-d');
    //         // save_customer_ledger($cust_ledger);
    //         if ($itemdiscount > 0) {
    //             $cust_ledger_dis = array();
    //             $cust_ledger_dis['customer_id'] = $customer_id;
    //             $cust_ledger_dis['order_id'] = $order->id;
    //             $cust_ledger_dis['descriptions'] = 'Discount';
    //             $cust_ledger_dis['type'] = 'Discount';
    //             $cust_ledger_dis['debit'] = 0;
    //             $cust_ledger_dis['credit'] = $itemdiscount;
    //             $cust_ledger_dis['date'] = date('Y-m-d');
    //             // save_customer_ledger($cust_ledger_dis); 
    //         }
    //         //Customer ledger End


    //         if (Session::has('coupon_discount')) {

    //             $coupon_usage = new CouponUsage;
    //             $coupon_usage->user_id = Auth::user()->id;
    //             $coupon_usage->coupon_id = Session::get('coupon_id');
    //             $coupon_usage->order_id = $order->id;
    //             $coupon_usage->save();
    //         }

    //         $array['view'] = 'emails.invoice';
    //         $array['subject'] = translate('Your order has been placed') . ' - ' . $order->code;
    //         $array['from'] = 'sales@bazarnao.com';
    //         $array['order'] = $order;

    //         // foreach ($seller_products as $key => $seller_product) {
    //         //     try {
    //         //         Mail::to(\App\Models\User::find($key)->email)->queue(new InvoiceEmailManager($array));
    //         //     } catch (\Exception $e) {
    //         //     }
    //         // }

    //         if (\App\Models\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Models\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\Models\OtpConfiguration::where('type', 'otp_for_order')->first()->value) {
    //             try {
    //                 $otpController = new OTPVerificationController;
    //                 $otpController->send_order_code($order);
    //             } catch (\Exception $e) {
    //             }
    //         }
    //         //  if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
    //         //  $request->device_token = $order->user->device_token;
    //         //  $request->title = "Order placed !";
    //         //   $request->text = " An order {$order->code} has been placed";

    //         //    $request->type = "order";
    //         //     $request->id = $order->id;
    //         //     $request->user_id = $order->user->id;

    //         //      send_firebase_notification($request);
    //         //  }
    //         //echo env('MAIL_USERNAME');exit;
    //         //sends email to customer with the invoice pdf attached
    //         //     if (env('MAIL_USERNAME') != null) {

    //         //  try {
    //         //  echo $request->session()->get('shipping_info')['email'];exit;
    //         //   if(!empty($request->session()->get('shipping_info')['email']))
    //         // Mail::to($request->session()->get('shipping_info')['email'])->send(new InvoiceEmailManager($array));
    //         //Mail::to(User::where('user_type', 'admin')->first()->email)->queue(new InvoiceEmailManager($array));
    //         //Mail::to('sales@bazarnao.com')->send(new InvoiceEmailManager($array));
    //         //Mail::to('sales@bazarnao.com')->queue(new InvoiceEmailManager($array));
    //         //  } catch (\Exception $e) {
    //         //   dd($e);
    //         //  }
    //         // }

    //         $request->session()->put('order_id', $order->id);
    //     }
    // }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $order = new Order;
        if (Auth::check()) {
            $order->user_id = Auth::user()->id;
            $customer_id = Auth::user()->id;
        } else {
            $exists = Order::where('guest_id', "!=", null)->orderBy('guest_id', 'desc')->get()->take(1);
            $guest_id = (count($exists) > 0) ? $exists[0]->guest_id + 1 : 10000001;
            $order->guest_id = $guest_id;
            $customer_id = $guest_id;
        }

        $order->shipping_address = json_encode($request->session()->get('shipping_info'));
        $order->payment_type = $request->payment_option;
        $order->delivery_viewed = '0';
        $order->payment_status_viewed = '0';

        $exists = Order::whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get()->take(1);
        $code = (count($exists) > 0) ? date('dmy') . substr($exists[0]->code, -4) + 1 : date('dmy') . '0001';
        $order->code = $code;
        $order->order_from = 'Web';
        $order->date = strtotime('now');
        $order->user_ip = $request->ip();

        if ($order->save()) {

            $itemdiscount = 0;
            $actsubtotal = 0;
            $subtotal = 0;
            $tax = 0;
            $shipping = 0;

            $temp = Session::get('cart');
            $totalQuantity = collect($temp)->sum(fn($item) => (int) $item['quantity']);
            $PerProductDiscount = Session::has('coupon_discount') ? Session::get('coupon_discount') / $totalQuantity : 0;

            foreach (Session::get('cart') as $key => $cartItem) {

                if (!isset($cartItem['shipping_type'])) {
                    $cartItem['shipping_type'] = 'home_delivery';
                }

                $product = Product::find($cartItem['id']);
                $discount_price = 0;

                // Happy Hour Discount
                $happy_hour = \App\Models\HappyHour::with('happy_hour_products')
                    ->where('status', 1)
                    ->where('end_date', '>=', now())
                    ->first();

                $happy_hour_product = $happy_hour ? $happy_hour->happy_hour_products->where('product_id', $product->id)->first() : null;
                if ($happy_hour_product) {
                    if ($happy_hour_product->discount_type == "percent") {
                        $discount_price = ($product->unit_price * $happy_hour_product->discount) / 100;
                    } else {
                        $discount_price = $happy_hour_product->discount;
                    }
                }

                // Flash Deal Discount
                $flash_deal = \App\Models\FlashDeal::where('status', 1)->first();
                $flash_deal_product = $flash_deal
                    ? \App\Models\FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first()
                    : null;

                if ($flash_deal_product) {
                    $flash_discount = ($product->unit_price * $flash_deal_product->discount_percent) / 100;
                    $discount_price = max($discount_price, $flash_discount); // Use the higher discount
                }

                // Regular Product Discount
                if (!$happy_hour_product && !$flash_deal_product) {
                    if ($product->discount_type == 'percent') {
                        $discount_price = ($product->unit_price * $product->discount) / 100;
                    } elseif ($product->discount_type == 'amount') {
                        $discount_price = $product->discount;
                    }
                }

                // Calculate Group Product Discounts
                if ($product->is_group_product) {
                    $group_products = Group_product::where('group_product_id', $product->id)->get();
                    $total_new_price = 0;
                    $total_main_price = 0;

                    foreach ($group_products as $item) {
                        $main_price = Product::where('id', $item->product_id)->value('unit_price');
                        $total_main_price += $main_price * $item->qty;
                        $total_new_price += $item->price;
                    }

                    $productwisediscount = $total_main_price - $total_new_price;
                    $itemdiscount += $productwisediscount * $cartItem['quantity'];
                }

                $actsubtotal += $product->unit_price * $cartItem['quantity'];
                $subtotal += $cartItem['price'] * $cartItem['quantity'];
                $tax += $cartItem['tax'] * $cartItem['quantity'];

                $product_variation = $cartItem['variant'];
                if ($product_variation != null) {
                    $product_stock = $product->stocks->where('variant', $product_variation)->first();
                    if ($product->digital != 1 && $product->outofstock == 1) {
                        flash(translate('The requested quantity is not available for ') . $product->getTranslation('name'))->warning();
                        $order->delete();
                        return redirect()->route('cart')->send();
                    } else {
                        $product_stock->qty -= $cartItem['quantity'];
                        $product_stock->save();
                    }
                } else {
                    if ($product->digital != 1 && $product->outofstock == 1) {
                        flash(translate('The requested quantity is not available for ') . $product->getTranslation('name'))->warning();
                        $order->delete();
                        return redirect()->route('cart')->send();
                    } else {
                        $product->current_stock -= $cartItem['quantity'];
                        $product->save();
                    }
                }

                // Store Order Details
                $order_detail = new OrderDetail;
                $order_detail->order_id = $order->id;
                $order_detail->seller_id = $product->user_id;
                $order_detail->product_id = $product->id;
                $order_detail->variation = $product_variation;
                $order_detail->price = ($cartItem['price']) * $cartItem['quantity'];
                $order_detail->tax = $cartItem['tax'] * $cartItem['quantity'];
                $order_detail->shipping_type = $cartItem['shipping_type'];
                $order_detail->discount = $discount_price * $cartItem['quantity'];
                if (Session::has('coupon_discount')) {
                    $order_detail->discount = $PerProductDiscount * $cartItem['quantity'];
                }
                $order_detail->special_discount = !empty($cartItem['discount']) ? $cartItem['discount'] : 0;

                // Dividing Shipping Costs
                if ($cartItem['shipping_type'] == 'home_delivery') {
                    $order_detail->shipping_cost = getShippingCost($key);
                } else {
                    $order_detail->shipping_cost = 0;
                }

                $shipping += $order_detail->shipping_cost;

                if ($cartItem['shipping_type'] == 'pickup_point') {
                    $order_detail->pickup_point_id = $cartItem['pickup_point'];
                }

                $profit = (($cartItem['price'] * $cartItem['quantity']) - ($product->purchase_price * $cartItem['quantity']));
                $order_detail->profit = $profit;

                $order_detail->quantity = $cartItem['quantity'];
                $order_detail->save();

                $product->num_of_sale++;
                $product->save();
            }

            // Shipping Fee Logic
            $shipping_skip_total = \App\Models\BusinessSetting::where('type', 'flat_rate_shipping_cost_total')->first()->value;
            if ($shipping_skip_total > $subtotal) {
                $shipping = \App\Models\BusinessSetting::where('type', 'flat_rate_shipping_cost')->first()->value;
            } else {
                $shipping = 0;
            }

            $order->grand_total = $subtotal + $tax + $shipping;

            if (Session::has('coupon_discount')) {
                $order->grand_total -= Session::get('coupon_discount');
                $order->coupon_discount = Session::get('coupon_discount');
            }

            if (Session::has('offer_discount')) {
                $order->grand_total -= Session::get('offer_discount');
                $order->special_discount = Session::get('offer_discount');
            }

            $order->save();

            // Coupon Usage
            if (Session::has('coupon_discount')) {
                $coupon_usage = new CouponUsage;
                $coupon_usage->user_id = Auth::user()->id;
                $coupon_usage->coupon_id = Session::get('coupon_id');
                $coupon_usage->order_id = $order->id;
                $coupon_usage->save();
            }

            $request->session()->put('order_id', $order->id);
        }
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order = Order::findOrFail($id);

        $order->viewed = 1;
        $order->save();
        $products = Product::where('outofstock', '0')->get();
        return view('backend.sales.all_orders.edit', compact('order', 'products'));
    }
    public function staff_order_edit($id)
    {
        $order = Order::findOrFail($id);
        //dd($order->orderDetails);
        $order->viewed = 1;
        $order->save();
        $products = Product::where('outofstock', '0')->get();
        return view('backend.staff_panel.customer_service.all_orders.edit', compact('order', 'products'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_old(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $coupon_discount = $this->apply_coupon_code($id, $order, $request->sub_total, $request);
        $order->grand_total = $request->sub_total;

        if ($order->save()) {
            OrderDetail::where('order_id', $id)->delete();
            Customer_ledger::where('order_id', $id)->delete();
            $itemdiscount = 0;
            $actsubtotal = 0;
            $subtotal = 0;
            $tax = 0;
            $shipping = 0;

            foreach ($request->product as $key => $cartItem) {
                if (($request->rate[$key] * $request->qty[$key]) == 0) {
                    continue;
                }
                $itemdiscount += $request->dis_amount[$key] * $request->qty[$key];
                $product = Product::find($cartItem);
                //$product_stock = ProductStock::where('product_id', $cartItem)->where('wearhouse_id', $wearhouse_id)->first();
                if (!empty($request->oldqty[$key])) {

                    // if ($product_stock != null) {
                    //     $product_stock->increment('qty', $request->oldqty[$key]);
                    //     $product_stock->decrement('qty', $request->qty[$key]);
                    //     $product_stock->save();
                    // }
                    $product->current_stock = ($product->current_stock + $request->oldqty[$key]) -  $request->qty[$key];
                } else {
                    // if ($product_stock != null) {
                    //     $product_stock->decrement('qty', $request->qty[$key]);
                    //     $product_stock->save();
                    // }else{
                    //     $product_stock = new ProductStock;
                    //     $product_stock->product_id = $cartItem;
                    //     $product_stock->wearhouse_id = $wearhouse_id;
                    //     $product_stock->decrement('qty', $request->qty[$key]);
                    //     $product_stock->save();

                    // }
                    $product->current_stock = ($product->current_stock) -  $request->qty[$key];
                }


                $product->save();
                $order_detail = new OrderDetail;
                $order_detail->order_id  = $id;
                $order_detail->seller_id = $product->user_id;
                $order_detail->product_id = $product->id;
                $order_detail->variation = '';
                $order_detail->price = $request->rate[$key] * $request->qty[$key];
                $order_detail->tax = 0;
                $order_detail->shipping_type = 'home_deliver';
                $order_detail->discount = $product->discount * $request->qty[$key];
                $order_detail->special_discount = !empty($request->special_discount[$key]) ? $request->special_discount[$key] : 0;
                $order_detail->product_referral_code = '';
                $order_detail->shipping_cost = $request->shippingcost;


                //End of storing shipping cost

                $order_detail->quantity = $request->qty[$key];
                $order_detail->save();
            }



            $cust_ledger = array();
            $cust_ledger['customer_id'] = $request->user_id;
            $cust_ledger['order_id'] = $id;
            $cust_ledger['descriptions'] = 'Cash on delivery ';
            $cust_ledger['type'] = 'Order';
            $cust_ledger['debit'] = $request->grand_total;
            $cust_ledger['credit'] = 0;
            $cust_ledger['date'] = date('Y-m-d');
            //save_customer_ledger($cust_ledger);

            if ($itemdiscount > 0) {
                $cust_ledger_dis = array();
                $cust_ledger_dis['customer_id'] = $request->user_id;
                $cust_ledger_dis['order_id'] = $id;
                $cust_ledger_dis['descriptions'] = 'Discount';
                $cust_ledger_dis['type'] = 'Discount';
                $cust_ledger_dis['debit'] = 0;
                $cust_ledger_dis['credit'] = $itemdiscount;
                $cust_ledger_dis['date'] = date('Y-m-d');
                // save_customer_ledger($cust_ledger_dis); 
            }
            //Customer ledger End

            $total = $this->calculate_discount_edit($order);
            $order = Order::findOrFail($id);
            $order->grand_total = $total - $coupon_discount;
            $order->coupon_discount = $coupon_discount;
            $order->save();
            if (auth()->user()->user_type != 'admin' && auth()->user()->staff->role->name == 'Customer Service Executive') {
                return redirect()->route('cutomerservice_all_orders.index')->send();
            } else {
                return redirect()->route('all_orders.index')->send();
            }
        }
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $coupon_discount = $this->apply_coupon_code($id, $order, $request->sub_total, $request);

        $coupon_discount = Order::where('id', $id)->value('coupon_discount');
        OrderDetail::where('order_id', $id)->delete();
        Customer_ledger::where('order_id', $id)->delete();
        $itemdiscount = 0;
        $actsubtotal = 0;
        $subtotal = 0;
        $tax = 0;
        $shipping = 0;

        foreach ($request->product as $key => $cartItem) {
            if (($request->rate[$key] * $request->qty[$key]) == 0) {
                continue;
            }
            $itemdiscount += $request->dis_amount[$key] * $request->qty[$key];
            $product = Product::find($cartItem);
            //$product_stock = ProductStock::where('product_id', $cartItem)->where('wearhouse_id', $wearhouse_id)->first();
            if (!empty($request->oldqty[$key])) {

                // if ($product_stock != null) {
                //     $product_stock->increment('qty', $request->oldqty[$key]);
                //     $product_stock->decrement('qty', $request->qty[$key]);
                //     $product_stock->save();
                // }
                $product->current_stock = ($product->current_stock + $request->oldqty[$key]) -  $request->qty[$key];
            } else {
                // if ($product_stock != null) {
                //     $product_stock->decrement('qty', $request->qty[$key]);
                //     $product_stock->save();
                // }else{
                //     $product_stock = new ProductStock;
                //     $product_stock->product_id = $cartItem;
                //     $product_stock->wearhouse_id = $wearhouse_id;
                //     $product_stock->decrement('qty', $request->qty[$key]);
                //     $product_stock->save();

                // }
                $product->current_stock = ($product->current_stock) -  $request->qty[$key];
            }


            $product->save();
            $order_detail = new OrderDetail;
            $order_detail->order_id  = $id;
            $order_detail->seller_id = $product->user_id;
            $order_detail->product_id = $product->id;
            $order_detail->variation = '';
            $order_detail->price = $request->rate[$key] * $request->qty[$key];
            $order_detail->tax = 0;
            $order_detail->shipping_type = 'home_deliver';
            $order_detail->discount = $product->discount * $request->qty[$key];
            $order_detail->special_discount = !empty($request->special_discount[$key]) ? $request->special_discount[$key] : 0;
            $order_detail->product_referral_code = '';
            $order_detail->shipping_cost = $request->shippingcost;
            if ($order->order_from == 'POS') {
                $order_detail->delivery_status = 'on_delivery';
            }
            //End of storing shipping cost

            $order_detail->quantity = $request->qty[$key];
            $order_detail->save();
        }

        $cust_ledger = array();
        if ($request->user_id) {
            $cust_ledger['customer_id'] = $request->user_id;
        } else {
            $cust_ledger['customer_id'] = $order->guest_id;
        }

        $cust_ledger['order_id'] = $id;
        $cust_ledger['descriptions'] = 'Cash on delivery ';
        $cust_ledger['type'] = 'Order';
        $cust_ledger['debit'] = $order->orderDetails->sum('price');
        $cust_ledger['credit'] = 0;
        $cust_ledger['date'] = date('Y-m-d');
        // save_customer_ledger($cust_ledger);

        // if ($itemdiscount > 0) {
        //     $cust_ledger_dis = array();
        //     $cust_ledger_dis['customer_id'] = $request->user_id;
        //     $cust_ledger_dis['order_id'] = $id;
        //     $cust_ledger_dis['descriptions'] = 'Discount';
        //     $cust_ledger_dis['type'] = 'Discount';
        //     $cust_ledger_dis['debit'] = 0;
        //     $cust_ledger_dis['credit'] = $itemdiscount;
        //     $cust_ledger_dis['date'] = date('Y-m-d');
        //     save_customer_ledger($cust_ledger_dis); 
        // }
        //Customer ledger End

        $order->grand_total = $order->orderDetails->sum('price');
        $order->save();
        $total = $this->calculate_discount_edit($order);
        $order = Order::findOrFail($id);
        $order->grand_total = $total - $coupon_discount;
        $order->coupon_discount = $coupon_discount;

        if ($order->order_from == 'POS') {
            $order->online_order_delivery_status = 'on_delivery';
        }
        $order->save();


        if (auth()->user()->user_type != 'admin' && auth()->user()->staff->role->name == 'Customer Service Executive') {
            return redirect()->route('cutomerservice_all_orders.index')->send();
        } else {
            return redirect()->back();
        }
    }

    function calculate_discount_edit($order)
    {

        $total = $order->grand_total;
        $offer_arr = array();
        $offers = Offer::get();
        $dis = 0;
        $diss = 0;
        $products = array();
        foreach ($offers as $offer) {

            if (time() >= $offer->start_date || time() <= $offer->end_date) {
                $d = json_decode($offer->details);
                if (strpos($offer->title, 'Bkash') !== false) {
                    continue;
                }
                if (strpos($offer->title, '2nd') !== false) {
                    if (!empty($order->user_id)) {
                        $uid = $order->user_id;
                        $orderCount = Order::leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')->where('orders.user_id', $uid)->whereNotIn('order_details.delivery_status', ['cancel'])->whereBetween('orders.created_at', [date('Y-m-01 00:00:00'), date('Y-m-t 23:59:59')])->groupBy('orders.id')->count();
                        if ($orderCount == 1) {
                            if (($total >= $d[0]->min_buy && $total <= $d[0]->max_discount)) {
                                foreach ($d as $row) {
                                    $this->addToDiscountProductEdit($row->product_id, $order->id);
                                }
                            }
                        }
                    }
                } else {
                    if ($offer->type == 'cart_base') {
                        if (!empty($d->product_id)) {
                            foreach ($d->product_id as $pd_id) {
                                foreach ($order->orderDetails as $key => $cartItem) {
                                    if ($pd_id == $cartItem->product_id) {
                                        $total -= ($cartItem->price * $cartItem->quantity);
                                    }
                                }
                            }
                        }

                        if (($total >= $d->min_buy && $total <= $d->max_discount)) {
                            $dis = $offer->discount;
                            $type = $offer->discount_type;
                            if ($type == 'percent') {
                                $dis = ($total * $dis) / 100;
                            }
                            $cust_ledger_dis = array();
                            $cust_ledger_dis['customer_id'] = $order->user_id;
                            $cust_ledger_dis['order_id'] = $order->id;
                            $cust_ledger_dis['descriptions'] = 'Offer Discount';
                            $cust_ledger_dis['type'] = 'Discount';
                            $cust_ledger_dis['debit'] = 0;
                            $cust_ledger_dis['credit'] = $dis;
                            $cust_ledger_dis['date'] = date('Y-m-d');
                            //save_customer_ledger($cust_ledger_dis); 
                        }
                    } else {
                        if (($total >= $d[0]->min_buy && $total <= $d[0]->max_discount)) {
                            $dis = $offer->discount;

                            foreach ($d as $row) {

                                if ($offer->full_discount == 0) {
                                    $cart = collect();
                                    foreach ($order->orderDetails as $key => $cartItem) {
                                        if ($row->product_id == $cartItem->product_id) {

                                            $max_qty = $offer->max_qty;
                                            $itm_disc = $offer->disc_per_qty;
                                            if ($cartItem->quantity > $max_qty) {
                                                $qqty = $max_qty;
                                            } else {
                                                $qqty = $cartItem->quantity;
                                            }
                                            $cartItem->discount = $itm_disc * $qqty;
                                            $diss += $itm_disc * $qqty;
                                        }
                                    }
                                } else {
                                    $this->addToDiscountProductEdit($row->product_id, $order->id);
                                }
                            }
                        }
                    }
                }
            }
        }
        $order->special_discount = $dis + $diss;
        $order->save();
        return $order->grand_total - ($dis + $diss);
    }
    function addToDiscountProductEdit($p_id, $id)
    {
        $product = Product::find($p_id);
        $order_detail = new OrderDetail;
        $order_detail->order_id  = $id;
        $order_detail->seller_id = $product->user_id;
        $order_detail->product_id = $product->id;
        $order_detail->variation = '';
        $order_detail->price = 0;
        $order_detail->tax = 0;
        $order_detail->shipping_type = 'home_deliver';
        $order_detail->discount = 0;
        $order_detail->product_referral_code = '';
        $order_detail->shipping_cost = 0;


        //End of storing shipping cost
        $order_detail->quantity = 1;
        $order_detail->save();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy_po($id)
    {
        $order = Purchase::findOrFail($id);

        if ($order != null) {

            $orderDetails = PurchaseDetail::where('id', $id)->get();

            //dd($orderDetails);
            // foreach ($orderDetails as $key => $prod) {
            //             $product = Product::find($prod->product_id);
            //             $product->current_stock -= $prod->qty;
            //             //dd($product);
            //             $product->save();

            // }
            PurchaseDetail::where('id', $id)->delete();
            $order->delete();
            flash(translate('Purchase Order has been deleted successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }
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
        $order = Order::findOrFail($id);
        if (!empty($order) && $order->delivered_by == null) {
            foreach ($order->orderDetails as $orderDetail) {
                $orderDetail->delete();
            }

            $order->delete();
            flash(translate('Order has been deleted successfully'))->success();
        } else {
            flash(translate('Delivered Order Can Not Delete'))->error();
        }
        return back();
    }

    public function order_details(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->save();
        return view('frontend.user.seller.order_details_seller', compact('order'));
    }

    public function update_delivery_boy(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->delivery_boy = $request->delivery_boy_id;
        if ($order->save()) {
            return 1;
        } else {
            return 0;
        }
    }

    public function update_warehouse(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->warehouse = $request->warehouse_id;
        if ($order->save()) {
            return 1;
        } else {
            return 0;
        }
    }

    public function product_stock_qty_check(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        if (!$order->warehouse) {
            $message = [
                'message' => 'warehouse',
            ];
            return response()->json($message);
        }

        $insufficientStockProducts = [];

        foreach ($order->orderDetails as $orderDetail) {
            $group_product_check = Product::where('id', $orderDetail->product_id)->value('is_group_product');

            if ($group_product_check == 1) {
                $group_product_items = Group_product::where('group_product_id', $orderDetail->product_id)->get();
                foreach ($group_product_items as $item) {
                    $product = Product::where('id', $item->product_id)->first();
                    if (!empty($product->parent_id)) {
                        $stock_qty_check = ProductStock::where('product_id', $product->parent_id)
                            ->where('wearhouse_id', $order->warehouse)->first();
                        $reduceable_qty = ($product->deduct_qty * $orderDetail->quantity * $item->qty);
                    } else {
                        $stock_qty_check = ProductStock::where('product_id', $product->id)
                            ->where('wearhouse_id', $order->warehouse)->first();
                        $reduceable_qty = $orderDetail->quantity * $item->qty;
                    }

                    if ($stock_qty_check->qty < $reduceable_qty) {
                        $insufficientStockProducts[] = $stock_qty_check->product->name;
                    }
                }
            } else {
                $product = Product::where('id', $orderDetail->product_id)->first();
                if (!empty($product->parent_id)) {
                    $stock_qty_check = ProductStock::where('product_id', $product->parent_id)
                        ->where('wearhouse_id', $order->warehouse)->first();
                    $reduceable_qty = ($product->deduct_qty * $orderDetail->quantity);
                } else {
                    $stock_qty_check = ProductStock::where('product_id', $product->id)
                        ->where('wearhouse_id', $order->warehouse)->first();
                    $reduceable_qty = $orderDetail->quantity;
                }

                if ($stock_qty_check->qty < $reduceable_qty) {
                    $insufficientStockProducts[] = $stock_qty_check->product->name;
                }
            }
        }


        if (!empty($insufficientStockProducts)) {
            $message = [
                'message' => 'false',
                'product' => $insufficientStockProducts,
            ];
        } else {
            $message = [
                'message' => 'true',
                'product' => "[]",
            ];
        }

        return response()->json($message);
    }

    // public function product_stock_qty_check(Request $request)
    // {
    //     $order = Order::findOrFail($request->order_id);
    //     if (!$order->warehouse) {
    //         return response()->json(['message' => 'warehouse']);
    //     }

    //     $warehouse_id = $order->warehouse;
    //     $insufficientStockProducts = [];

    //     foreach ($order->orderDetails as $orderDetail) {
    //         $group_product_check = Product::where('id', $orderDetail->product_id)->value('is_group_product');

    //         // Collect all products that will be reduced
    //         $productsToCheck = [];

    //         if ($group_product_check == 1) {
    //             $group_product_items = Group_product::where('group_product_id', $orderDetail->product_id)->get();
    //             foreach ($group_product_items as $item) {
    //                 $product = Product::find($item->product_id);
    //                 $reduceable_qty = !empty($product->parent_id)
    //                     ? ($product->deduct_qty * $orderDetail->quantity * $item->qty)
    //                     : ($orderDetail->quantity * $item->qty);

    //                 $productsToCheck[] = [
    //                     'product' => $product->parent_id ?? $product->id,
    //                     'reduceable_qty' => $reduceable_qty
    //                 ];
    //             }
    //         } else {
    //             $product = Product::find($orderDetail->product_id);
    //             $reduceable_qty = !empty($product->parent_id)
    //                 ? ($product->deduct_qty * $orderDetail->quantity)
    //                 : $orderDetail->quantity;

    //             $productsToCheck[] = [
    //                 'product' => $product->parent_id ?? $product->id,
    //                 'reduceable_qty' => $reduceable_qty
    //             ];
    //         }

    //         // Now calculate stock availability like closing stock
    //         foreach ($productsToCheck as $check) {
    //             $product_id = $check['product'];
    //             $reduceable_qty = $check['reduceable_qty'];

    //             // Collect inflows
    //             $opening_stocks = OpeningStock::where('wearhouse_id', $warehouse_id)
    //                 ->where('product_id', $product_id)->get();

    //             $purchases = PurchaseDetail::select('purchase_details.*', 'purchases.date')
    //                 ->leftJoin('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')
    //                 ->where('purchases.status', 2)
    //                 ->where('purchase_details.product_id', $product_id)
    //                 ->where('purchase_details.wearhouse_id', $warehouse_id)
    //                 ->get();

    //             $receives = Transfer::where('product_id', $product_id)
    //                 ->where('status', 'Approved')
    //                 ->where('to_wearhouse_id', $warehouse_id)
    //                 ->get();

    //             $refunds = RefundRequest::select(
    //                 'refund_requests.return_qty as qty',
    //                 'refund_requests.purchase_price as price'
    //             )
    //                 ->leftJoin('order_details', 'order_details.id', '=', 'refund_requests.order_detail_id')
    //                 ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
    //                 ->where('refund_requests.refund_status', 5)
    //                 ->where('orders.warehouse', $warehouse_id)
    //                 ->where('order_details.product_id', $product_id)
    //                 ->get();

    //             // Collect outflows
    //             $sales = OrderDetail::select('order_details.*', 'orders.delivered_date')
    //                 ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
    //                 ->where('order_details.delivery_status', 'delivered')
    //                 ->where('orders.warehouse', $warehouse_id)
    //                 ->where('order_details.product_id', $product_id)
    //                 ->get();

    //             $damages = Damage::where('product_id', $product_id)
    //                 ->where('status', 'Approved')
    //                 ->where('wearhouse_id', $warehouse_id)
    //                 ->get();

    //             $transfers = Transfer::where('product_id', $product_id)
    //                 ->where('status', 'Approved')
    //                 ->where('from_wearhouse_id', $warehouse_id)
    //                 ->get();

    //             // Merge inflows
    //             $inflows = collect();
    //             $inflows = $inflows->merge($opening_stocks)->merge($purchases)->merge($receives)->merge($refunds);
    //             $inflows = $inflows->sortBy('date');

    //             // Outflows total qty
    //             $outflow_qty = $sales->sum('quantity') + $damages->sum('qty') + $transfers->sum('qty');

    //             // FIFO calc
    //             $closing_qty = 0;
    //             $remaining_outflow = $outflow_qty;

    //             foreach ($inflows as $in) {
    //                 $available = $in->qty;
    //                 if ($remaining_outflow > 0) {
    //                     if ($available >= $remaining_outflow) {
    //                         $in->qty -= $remaining_outflow;
    //                         $remaining_outflow = 0;
    //                     } else {
    //                         $remaining_outflow -= $available;
    //                         $in->qty = 0;
    //                     }
    //                 }
    //                 if ($in->qty > 0) {
    //                     $closing_qty += $in->qty;
    //                 }
    //             }

    //             // Now check order qty against closing qty
    //             if ($closing_qty < $reduceable_qty) {
    //                 $insufficientStockProducts[] = Product::find($product_id)->name;
    //             }
    //         }
    //     }

    //     if (!empty($insufficientStockProducts)) {
    //         return response()->json([
    //             'message' => 'false',
    //             'product' => $insufficientStockProducts,
    //         ]);
    //     }

    //     return response()->json([
    //         'message' => 'true',
    //         'product' => [],
    //     ]);
    // }


    public function update_delivery_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order_status_log = new OrderStatusLog;
        $order_status_log->order_id = $request->order_id;
        $order_status_log->user_id = Auth::user()->id;
        $order_status_log->order_status = $request->status;
        $order_status_log->remarks = $request->reason_of_cancel;
        $order_status_log->order_code = $order->code;
        $order_status_log->save();

        if ($request->cash_collection > 0) {
            $order->cash_collection = $request->cash_collection;
            $order->save();

            $delivery_executive_ledger = new DeliveryExecutiveLedger();
            $delivery_executive_ledger->user_id = Auth::user()->id;

            if (!empty($order->user->name)) {
                $delivery_executive_ledger->name = $order->user->name;
            } else {
                $shipping_address = json_decode($order->shipping_address);
                $delivery_executive_ledger->name = $shipping_address->name;
            }

            $delivery_executive_ledger->status = 'Pending';
            $delivery_executive_ledger->order_no = $order->code;
            $delivery_executive_ledger->date = date('Y-m-d');
            $delivery_executive_ledger->debit = $request->cash_collection;
            $delivery_executive_ledger->type = "Order";
            $delivery_executive_ledger->due_status = 0;
            $delivery_executive_ledger->save();
        }

        $order->delivery_viewed = '0';
        if ($request->status == 'confirmed') {
            $order->confirm_date = date('Y-m-d H:i:s');
            $order->confirmed_by  = Auth::user()->id;
        } else if ($request->status == 'cancel') {
            $order->cancel_date = date('Y-m-d H:i:s');
            $order->canceled_by  = Auth::user()->id;
        } else if ($request->status == 'on_delivery') {
            $order->on_delivery_date = date('Y-m-d H:i:s');
            $order->on_delivery_by  = Auth::user()->id;
        } else if ($request->status == 'delivered') {
            $order->delivered_date = date('Y-m-d H:i:s');
            $order->delivered_by  = Auth::user()->id;
        }

        // $order->online_order_delivery_status = $request->status;
        // $order->save();

        // foreach ($order->orderDetails as $key => $orderDetail) {
        //     $orderDetail->delivery_status = $request->status;
        //     $orderDetail->delivery_status_changer_id = Auth::user()->id;
        //     $orderDetail->save();
        // }

        if ($request->status == 'confirmed') {
            $order->online_order_delivery_status = $request->status;
            $order->save();


            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->delivery_status = $request->status;
                $orderDetail->delivery_status_changer_id = Auth::user()->id;
                $orderDetail->save();
            }

            $message = [
                'message' => 'true',
                'product' => '',
            ];
            return response()->json($message);
        }

        if ($request->status == 'on_delivery') {
            $order->delivery_boy = $request->delivery_boy;
            $order->is_deduct = 1;
            $order->online_order_delivery_status = $request->status;
            $order->save();

            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->delivery_status = $request->status;
                $orderDetail->delivery_status_changer_id = Auth::user()->id;
                $orderDetail->save();
            }

            $message = [
                'message' => 'true',
                'product' => '',
            ];
            return response()->json($message);
        }

        if ($request->status == 'delivered') {
            $total_purchase_price = 0;

            foreach ($order->orderDetails as $key => $orderDetail) {
                $wearhouse_id = getWearhouseId($request->order_id);
                $remaining_qty = $orderDetail->quantity;
                $purchase_prices = [];
                $from_date = date('Y-m-01 00:00:00');
                $to_date = date('Y-m-t 23:59:59');
                $startDate = date('Y-m-01');
                $endDate = date('Y-m-t');

                $parent_id = Product::where('id', $orderDetail->product_id)->value('parent_id');
                $product_id = null;

                if ($parent_id) {
                    $product_id = $parent_id;
                } else {
                    $product_id = $orderDetail->product_id;
                }

                $opening_stocks = OpeningStock::where('wearhouse_id', $wearhouse_id)
                    ->where('product_id', $product_id)
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->get()
                    ->groupBy('product_id');

                $purchases = PurchaseDetail::select('purchase_details.id', 'purchase_details.product_id', 'purchase_details.wearhouse_id', 'purchase_details.qty', 'purchase_details.price', 'purchases.date')
                    ->leftJoin('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')
                    ->where('purchases.status', 2)
                    ->where('purchase_details.qty', '>', 0)
                    ->where('purchase_details.product_id', $orderDetail->product_id)
                    ->where('purchase_details.wearhouse_id', $wearhouse_id)
                    ->whereBetween('purchases.date', [$startDate, $endDate])
                    ->get()
                    ->groupBy('product_id');


                $receives = Transfer::select('transfers.id', 'transfers.product_id', 'transfers.to_wearhouse_id', 'transfers.qty', 'transfers.unit_price as price', 'transfers.amount', 'transfers.status', 'transfers.date')
                    ->where('product_id', $product_id)
                    ->where('status', 'Approved')
                    ->where('to_wearhouse_id', $wearhouse_id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get()
                    ->groupBy('product_id');

                $refunds = RefundRequest::select('refund_requests.id', 'order_details.product_id', 'refund_requests.return_qty as qty', 'refund_requests.return_amount as amount', 'refund_requests.created_at as date')
                    ->leftJoin('order_details', 'order_details.id', '=', 'refund_requests.order_detail_id')
                    ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
                    ->where('order_details.product_id', $product_id)
                    ->where('refund_requests.refund_status', 5)
                    ->where('orders.warehouse', $wearhouse_id)
                    ->whereBetween('refund_requests.created_at', [$from_date, $to_date])
                    ->get()
                    ->groupBy('product_id');

                $main_orders = OrderDetail::select('order_details.id', 'order_details.order_id', 'order_details.product_id', 'order_details.price', 'order_details.quantity as qty', 'order_details.delivery_status', 'orders.warehouse', 'orders.delivered_date', 'order_details.created_at', 'order_details.updated_at')
                    ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
                    ->where('order_details.delivery_status', 'delivered')
                    ->where('order_details.product_id', $product_id)
                    ->where('orders.warehouse', $wearhouse_id)
                    ->whereBetween('orders.delivered_date', [$from_date, $to_date])
                    ->get();

                $child_orders = OrderDetail::select('order_details.id', 'order_details.order_id', 'products.parent_id as product_id', 'order_details.price', DB::raw('products.deduct_qty * order_details.quantity as qty'), 'order_details.delivery_status', 'orders.warehouse', 'orders.delivered_date', 'order_details.created_at', 'order_details.updated_at')
                    ->leftJoin('products', 'products.id', '=', 'order_details.product_id')
                    ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
                    ->where('order_details.delivery_status', 'delivered')
                    ->where('products.parent_id', $product_id) // Ensure you're getting child products
                    ->where('orders.warehouse', $wearhouse_id)
                    ->whereBetween('orders.delivered_date', [$from_date, $to_date])
                    ->get();

                $sales = $main_orders->merge($child_orders)->groupBy('product_id');

                $damages = Damage::select('damages.id', 'damages.product_id', 'damages.wearhouse_id', 'damages.qty', 'damages.total_amount as amount', 'damages.status', 'damages.date')
                    ->where('product_id', $product_id)
                    ->where('status', 'Approved')
                    ->where('wearhouse_id', $wearhouse_id)
                    ->whereBetween('date', array($startDate, $endDate))
                    ->get()
                    ->groupBy('product_id');

                $transfers = Transfer::select('transfers.id', 'transfers.product_id', 'transfers.from_wearhouse_id', 'transfers.qty', 'transfers.unit_price as price', 'transfers.amount', 'transfers.status', 'transfers.date')
                    ->where('product_id', $product_id)
                    ->where('status', 'Approved')
                    ->where('from_wearhouse_id', $wearhouse_id)
                    ->whereBetween('date', array($startDate, $endDate))
                    ->get()
                    ->groupBy('product_id');

                $transactions = array();
                if (isset($opening_stocks[$product_id])) {
                    foreach ($opening_stocks[$product_id] as $stock) {
                        $transactions[] = [
                            'id'            => $stock->id,
                            'particulars'   => 'Opening Stock',
                            'date'          => date('Y-m-d H:i:s', strtotime($stock->created_at)),
                            'qty'           => $stock->qty,
                            'per_unit'      => $stock->price,
                            'total'         => $stock->qty * $stock->price,
                            'type'   => 'Receipt',
                        ];
                    }
                }

                if (isset($purchases[$product_id])) {

                    foreach ($purchases[$product_id] as $purchase) {
                        $transactions[] = [
                            'id'            => $purchase->id,
                            'particulars'   => 'Purchase',
                            'date'          => date('Y-m-d H:i:s', strtotime($purchase->date)),
                            'qty'           => $purchase->qty,
                            'per_unit'      => $purchase->price,
                            'total'         => $purchase->qty * $purchase->price,
                            'type'   => 'Receipt',
                        ];
                    }
                }

                if (isset($receives[$product_id])) {
                    foreach ($receives[$product_id] as $receive) {
                        $transactions[] = [
                            'id'            => $receive->id,
                            'particulars'   => 'Receive',
                            'date'          => date('Y-m-d H:i:s', strtotime($receive->date)),
                            'qty'           => $receive->qty,
                            'per_unit'      => $receive->price,
                            'total'         => $receive->qty * $receive->price,
                            'type'   => 'Receipt',
                        ];
                    }
                }

                if (isset($refunds[$product_id])) {
                    foreach ($refunds[$product_id] as $refund) {
                        $transactions[] = [
                            'id'            => $refund->id,
                            'particulars'   => 'Refund',
                            'date'          => date('Y-m-d H:i:s', strtotime($refund->created_at)),
                            'qty'           => $refund->qty,
                            'per_unit'      => $refund->price,
                            'total'         => $refund->qty * $refund->price,
                            'type'   => 'Receipt',
                        ];
                    }
                }

                if (isset($sales[$product_id])) {
                    foreach ($sales[$product_id] as $sale) {
                        $transactions[] = [
                            'id'            => $sale->id,
                            'particulars'   => 'Sale',
                            'date'          => date('Y-m-d H:i:s', strtotime($sale->delivered_date)),
                            'qty'           => $sale->qty,
                            'per_unit'      => $sale->price,
                            'total'         => $sale->qty * $sale->price,
                            'type'   => 'Issue',
                        ];
                    }
                }


                if (isset($damages[$product_id])) {
                    foreach ($damages[$product_id] as $damage) {
                        $transactions[] = [
                            'id'            => $damage->id,
                            'particulars'   => 'Damage',
                            'date'          => date('Y-m-d H:i:s', strtotime($damage->date)),
                            'qty'           => $damage->qty,
                            'per_unit'      => $damage->amount / $damage->qty,
                            'total'         => $damage->amount,
                            'type'   => 'Issue',
                        ];
                    }
                }

                if (isset($transfers[$product_id])) {
                    foreach ($transfers[$product_id] as $transfer) {
                        $transactions[] = [
                            'id'            => $transfer->id,
                            'particulars'   => 'Transfer',
                            'date'          => date('Y-m-d H:i:s', strtotime($transfer->date)),
                            'qty'           => $transfer->qty,
                            'per_unit'      => $transfer->price,
                            'total'         => $transfer->qty * $transfer->price,
                            'type'   => 'Issue',
                        ];
                    }
                }
                usort($transactions, function ($a, $b) {
                    return strtotime($a['date']) <=> strtotime($b['date']);
                });


                $balance = [];
                $totalIssueQty = 0;
                $costOfGoodsSold = 0;
                $totalBalanceQty = 0;
                $totalBalanceValue = 0;
                $inventoryData = [];
                // if($product_id==3896){
                //     dd($transactions);

                // }
                if (!$transactions) {
                    $product_name = Product::where('id', $product_id)->value('name');
                    $message = [
                        'message' => 'false',
                        'product' => $product_name,
                    ];
                    return response()->json($message);
                }

                foreach ($transactions as $transaction) {
                    $date = $transaction['date'];
                    $particulars = $transaction['particulars'];
                    $qty = $transaction['qty'];
                    $perUnit = $transaction['per_unit'];
                    $total = $transaction['total'];
                    $type = $transaction['type'];

                    $receiptQty = $issueQty = $receiptPerUnit = $issuePerUnit = $receiptTotal = $issueTotal = null;
                    $issueDetails = [];

                    if ($type === 'Receipt') {
                        $balance[] = ['qty' => $qty, 'per_unit' => $perUnit];
                        $receiptQty = $qty;
                        $receiptPerUnit = $perUnit;
                        $receiptTotal = $total;
                    } elseif ($type === 'Issue') {
                        $issueQty = $qty;
                        $remainingQty = $qty;
                        $issueTotal = 0;

                        while ($remainingQty > 0 && !empty($balance)) {
                            $batch = array_shift($balance);
                            if ($batch['qty'] <= $remainingQty) {
                                $issueDetails[] = [
                                    'qty' => $batch['qty'],
                                    'per_unit' => $batch['per_unit'],
                                    'total' => $batch['qty'] * $batch['per_unit']
                                ];
                                $issueTotal += $batch['qty'] * $batch['per_unit'];
                                $remainingQty -= $batch['qty'];
                            } else {
                                $issueDetails[] = [
                                    'qty' => $remainingQty,
                                    'per_unit' => $batch['per_unit'],
                                    'total' => $remainingQty * $batch['per_unit']
                                ];
                                $issueTotal += $remainingQty * $batch['per_unit'];
                                $batch['qty'] -= $remainingQty;
                                array_unshift($balance, $batch);
                                $remainingQty = 0;
                            }
                        }

                        $totalIssueQty += $issueQty;
                        $costOfGoodsSold += $issueTotal;
                    }

                    $balanceDetails = [];
                    foreach ($balance as $batch) {
                        if ($batch['qty'] > 0) {
                            $balanceDetails[] = [
                                'qty' => $batch['qty'],
                                'per_unit' => $batch['per_unit'],
                                'total' => $batch['qty'] * $batch['per_unit']
                            ];
                        }
                    }

                    $inventoryData[] = [
                        'date' => $date,
                        'particulars' => $particulars,
                        'receiptQty' => $receiptQty,
                        'receiptPerUnit' => $receiptPerUnit,
                        'receiptTotal' => $receiptTotal,
                        'issueQty' => $issueQty,
                        'issueDetails' => $issueDetails,
                        'issueTotal' => $issueTotal,
                        'balanceDetails' => $balanceDetails,
                    ];
                }

                $lastInventoryRow = end($inventoryData);
                $remaining_qty = $orderDetail->quantity;

                if (!empty($lastInventoryRow) && isset($lastInventoryRow['balanceDetails'])) {
                    $purchasePrice = 0;
                    $purchaseDetails = [];
                    $total_price = 0;

                    foreach ($lastInventoryRow['balanceDetails'] as $batch) { // Extract balanceDetails
                        if ($remaining_qty > 0) {
                            $qty = $batch['qty'];
                            $perUnit = $batch['per_unit'];

                            if ($remaining_qty >= $qty) {
                                $purchasePrice += $qty * $perUnit;
                                $total_price += ($qty * $perUnit);
                                $purchaseDetails[] = ['qty' => $qty, 'per_unit' => $perUnit];
                                $remaining_qty -= $qty;
                            } else {
                                $purchasePrice += $remaining_qty * $perUnit;
                                $total_price += ($remaining_qty * $perUnit);

                                $purchaseDetails[] = ['qty' => $remaining_qty, 'per_unit' => $perUnit];
                                $remaining_qty = 0;
                            }
                        }
                    }

                    $total_purchase_price += $purchasePrice;
                    $orderDetail->purchase_price = json_encode($purchaseDetails);
                    $orderDetail->save();
                }




                $group_product_check = Product::where('id', $orderDetail->product_id)->value('is_group_product');

                if ($group_product_check == 1) {
                    $group_product_items = Group_product::where('group_product_id', $orderDetail->product_id)->get();

                    foreach ($group_product_items as $item) {
                        $product_stock = ProductStock::where('product_id', $item->product_id)
                            ->where('wearhouse_id', $wearhouse_id)
                            ->first();

                        $reduceable_qty = $orderDetail->quantity * $item->qty;

                        if ($product_stock) {
                            $product_stock->decrement('qty', $reduceable_qty);
                        } else {
                            ProductStock::create([
                                'product_id' => $item->product_id,
                                'wearhouse_id' => $wearhouse_id,
                                'qty' => -$reduceable_qty
                            ]);
                        }
                    }
                } else {
                    $product_stock = ProductStock::where('product_id', $orderDetail->product_id)
                        ->where('wearhouse_id', $wearhouse_id)
                        ->first();

                    if ($product_stock) {
                        $product_stock->decrement('qty', $orderDetail->quantity);
                    } else {
                        ProductStock::create([
                            'product_id' => $orderDetail->product_id,
                            'wearhouse_id' => $wearhouse_id,
                            'qty' => -$orderDetail->quantity
                        ]);
                    }
                }
            }


            $order->purchase_price = $total_purchase_price;
            $order->online_order_delivery_status = $request->status;
            $order->save();


            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->delivery_status = $request->status;
                $orderDetail->delivery_status_changer_id = Auth::user()->id;
                $orderDetail->save();
            }


            if ($order->order_from != 'POS') {
                $cust_ledger = array();
                $customer_id = null;
                if ($order->guest_id != null) {
                    $cust_ledger['customer_id'] = $order->guest_id;
                    $customer_id = $order->guest_id;
                } else {
                    $cust_ledger['customer_id'] = $order->user_id;
                    $customer_id = $order->user_id;
                }

                $cust_ledger['order_id'] = $request->order_id;
                $cust_ledger['descriptions'] = 'Cash on delivery';
                $cust_ledger['type'] = 'Order';
                $cust_ledger['debit'] = $order->orderDetails->sum('price');
                $cust_ledger['credit'] = 0;
                $cust_ledger['date'] = date('Y-m-d');
                save_customer_ledger($cust_ledger);


                if ($order->coupon_discount > 0) {
                    $cust_ledger = array();
                    $cust_ledger['customer_id'] = $customer_id;
                    $cust_ledger['order_id'] = $request->order_id;
                    $cust_ledger['descriptions'] = 'Coupon Discount';
                    $cust_ledger['type'] = 'Coupon Discount';
                    $cust_ledger['debit'] = 0;
                    $cust_ledger['credit'] = $order->orderDetails->sum('coupon_discount');
                    $cust_ledger['date'] = date('Y-m-d');
                    save_customer_ledger($cust_ledger);
                }
                if ($order->special_discount > 0) {
                    $cust_ledger = array();
                    $cust_ledger['customer_id'] = $customer_id;
                    $cust_ledger['order_id'] = $request->order_id;
                    $cust_ledger['descriptions'] = 'Special Discount';
                    $cust_ledger['type'] = 'Special Discount';
                    $cust_ledger['debit'] = 0;
                    $cust_ledger['credit'] = $order->orderDetails->sum('discount');
                    $cust_ledger['date'] = date('Y-m-d');
                    save_customer_ledger($cust_ledger);
                }
            }

            $purchase_amount = $order->purchase_price;
            $insert_sale_journal = insert_sale_journal($order->id, $purchase_amount);

            if ($insert_sale_journal) {
                autoapprove($order->id);
            }

            $message = [
                'message' => 'true',
                'product' => '',
            ];
            return response()->json($message);
        }


        if ($request->status == 'cancel') {
            $order = Order::findOrFail($request->order_id);
            if ($order != null) {
                if ($request->reason_of_cancel) {
                    $order->reason_of_cancel = $request->reason_of_cancel;
                }
                $order->cancel_date = date('Y-m-d H:i:s');
                $order->canceled_by  = Auth::user()->id;
                $order->cancel_user_id = Auth::user()->id;
                $order->save();
            }

            DB::table('coupon_usages')->where('order_id', $request->order_id)->delete();

            $array['view'] = 'emails.invoice';
            $array['subject'] = translate('Your order has been cancel') . ' - ' . $order->code;
            $array['from'] = 'sales@bazarnao.com';
            $array['order'] = $order;
            $shipping_address = json_decode($order->shipping_address);
            // Mail::to($shipping_address->email)->queue(new InvoiceEmailManager($array));
            // Mail::to('sales@bazarnao.com')->queue(new InvoiceEmailManager($array));
            // Mail::to('shaheen@4axiz.com')->queue(new InvoiceEmailManager($array));

            if ($order->payment_type == 'wallet' && !empty($order->user_id)) {
                $amt = $order->grand_total;
                $order->payment_status = 'unpaid';
                $order->payment_details = '';
                $order->save();
                $chk = Wallet::where('order_id', $request->order_id)->where('payment_method', 'Wallet amount refund for order cancel')->count();
                if ($chk == 0) {
                    $amt = Wallet::where('order_id', $request->order_id)->first()->amount;
                    User::where('id', $order->user_id)->increment('balance', abs($amt));
                    $wallet = new Wallet();
                    $wallet->user_id = $order->user_id;
                    $wallet->payment_method = 'Wallet amount refund for order cancel';
                    $wallet->amount = abs($amt);
                    $wallet->order_id = $request->order_id;
                    $wallet->payment_details = json_encode(array('payment_method' => 'Wallet Refund', 'order_id' => $request->order_id));
                    $wallet->save();
                }
            }
            DB::table('customer_ledger')->where('order_id', $request->order_id)->delete();
            $order->online_order_delivery_status = $request->status;
            $order->save();

            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->delivery_status = $request->status;
                $orderDetail->delivery_status_changer_id = Auth::user()->id;
                $orderDetail->save();
            }

            $message = [
                'message' => 'true',
                'product' => '',
            ];
            return response()->json($message);
        }

        // if(in_array($request->status, array('confirmed')) && $order->grand_total>=1000)
        // checkForFirstOrder($order->user_id);

        try {
            if (in_array($request->status, array('cancel', 'confirmed', 'delivered'))) {
                $otpController = new OTPVerificationController;
                $otpController->send_delivery_status($order);
            }
        } catch (\Exception $e) {
        }
        return 1;
    }

    public function update_payment_status_main(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->payment_status_viewed = '0';
        $order->save();

        if (!empty($order->payment_details)) {
            $orderpayment = json_decode($order->payment_details);
            if (!empty($orderpayment)) {
                $paid = $orderpayment->amount + $request->payment_amount;
            }
        } else {
            $paid = $request->payment_amount;
        }
        if ($paid >= $order->grand_total) {
            $status = 'paid';
        } else {
            $status = $request->status;
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
            $cust_ledger['order_id'] = $request->order_id;
            $cust_ledger['descriptions'] = 'Cash Payment';
            $cust_ledger['type'] = 'Payment';
            $cust_ledger['debit'] = 0;
            $cust_ledger['credit'] = $request->payment_amount;
            $cust_ledger['date'] = date('Y-m-d', strtotime($request->payment_date));
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
                $otpController->send_payment_status($order, $request->payment_amount);
            } catch (\Exception $e) {
            }
        }
        return 1;
    }

    public function update_payment_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->payment_status_viewed = '0';
        $order->save();

        if (!empty($order->payment_details)) {
            $orderpayment = json_decode($order->payment_details);
            if (!empty($orderpayment)) {
                $total = $orderpayment->amount + $request->payment_amount;
                if ($total <= $order->grand_total && $request->payment_amount != 0) {
                    $paid = $orderpayment->amount + $request->payment_amount;
                } else {
                    flash(translate('Already Paid Full Amount'))->error();
                    return 0;
                }
            }
        } else {
            $paid = $request->payment_amount;
        }
        if ($paid >= $order->grand_total) {
            $status = 'paid';
        } else {
            $status = $request->status;
        }

        if ($order->order_from == 'POS') {
            $posledgerorder = Pos_ledger::where('order_id', $request->order_id)->first();
            $posledgerorder->decrement('due', $request->payment_amount);
            $posledgerorder->save();
            $warehousearray = getWearhouseBuUserId(auth()->user()->id);
            DB::table('pos_ledger')->insert([
                'order_id' => $order->id,
                'warehouse_id' => $warehousearray[0],
                'type' => 'Order',
                'order_amount' => 0,
                'due' => 0,
                'debit' => $request->payment_amount,
                'date' => date('Y-m-d', strtotime($order->created_at)),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

        foreach ($order->orderDetails as $key => $orderDetail) {
            $orderDetail->payment_status = $status;
            $orderDetail->save();
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

            // Start Accounting Sales Journal Hit
            // $invoiceBalance = invoice_method_wise_balance($order->id);
            // $voucherCreated = insert_sale_journal($order->id, $request->payment_amount, $order->payment_status);

            // if ($voucherCreated) {
            //     autoapprove($order->id);
            // }
            // End Accounting Sales Journal Hit

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
            $cust_ledger['order_id'] = $request->order_id;
            $cust_ledger['descriptions'] = 'Cash Payment';
            $cust_ledger['type'] = 'Payment';
            $cust_ledger['debit'] = 0;
            $cust_ledger['credit'] = $request->payment_amount;
            $cust_ledger['date'] = date('Y-m-d', strtotime($request->payment_date));
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
                $otpController->send_payment_status($order, $request->payment_amount);
            } catch (\Exception $e) {
            }
        }
        return 1;
    }

    function update_paid_to(Request $request)
    {

        $del_exledger_order = DeliveryExecutiveLedger::where('id', $request->id)->first();
        $del_exledger_order->note = $request->paid_to;
        $del_exledger_order->save();

        $del_exledger_payment = new DeliveryExecutiveLedger();
        $del_exledger_payment->user_id = Auth::user()->id;
        $del_exledger_payment->name = $del_exledger_order->name;
        $del_exledger_payment->order_no = $del_exledger_order->order_no;
        $del_exledger_payment->type = 'Payment';
        $del_exledger_payment->note = $del_exledger_order->note;
        $del_exledger_payment->credit = $del_exledger_order->debit;
        $del_exledger_payment->date = $del_exledger_order->date;
        $del_exledger_payment->status = $del_exledger_order->status;
        $del_exledger_payment->due_status = $del_exledger_order->due_status;
        $del_exledger_payment->save();
    }

    public function customer_ledger_hit($order_id)
    {
        Customer_ledger::where('order_id', $order_id)->delete();
        $orders = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->select('orders.*', DB::raw('SUM(order_details.discount) AS total_discount'))
            ->whereNotNull('orders.user_id')
            ->where('orders.id', $order_id);
        $orders = $orders->whereIn('order_details.delivery_status', ['confirmed', 'on_delivery', 'delivered']);

        $orders = $orders->groupBy('orders.id')->get();
        foreach ($orders as $order) {
            $product_discount = $order->total_discount;
            $offer_discount = $order->special_discount;
            $total = $order->grand_total + $product_discount + $offer_discount;
            $paid = 0;
            if (!empty($order->payment_details)) {
                $payment = json_decode($order->payment_details);
                if (!empty($payment)) {
                    $paid = $payment->amount;
                }
            }
            $desc = 'Order by ' . $order->payment_type;
            $cust_ledger = array();
            $cust_ledger['customer_id'] = $order->user_id;
            $cust_ledger['order_id'] = $order->id;
            $cust_ledger['descriptions'] = $desc;
            $cust_ledger['type'] = 'Order';
            $cust_ledger['debit'] = $total;
            $cust_ledger['credit'] = 0;
            $cust_ledger['date'] = date('Y-m-d', strtotime($order->created_at));
            save_customer_ledger($cust_ledger);
            if (!empty($offer_discount)) {
                $cust_ledger_dis = array();
                $cust_ledger_dis['customer_id'] = $order->user_id;
                $cust_ledger_dis['order_id'] = $order->id;
                $cust_ledger_dis['descriptions'] = 'Offer Discount';
                $cust_ledger_dis['type'] = 'Discount';
                $cust_ledger_dis['debit'] = 0;
                $cust_ledger_dis['credit'] = $offer_discount;
                $cust_ledger_dis['date'] = date('Y-m-d', strtotime($order->created_at));
                save_customer_ledger($cust_ledger_dis);
            }
            if (!empty($product_discount)) {
                $cust_ledger_dis = array();
                $cust_ledger_dis['customer_id'] = $order->user_id;
                $cust_ledger_dis['order_id'] = $order->id;
                $cust_ledger_dis['descriptions'] = 'Discount';
                $cust_ledger_dis['type'] = 'Discount';
                $cust_ledger_dis['debit'] = 0;
                $cust_ledger_dis['credit'] = $product_discount;
                $cust_ledger_dis['date'] = date('Y-m-d', strtotime($order->created_at));
                save_customer_ledger($cust_ledger_dis);
            }
            if (!empty($paid)) {
                $cust_ledger = array();
                $cust_ledger['customer_id'] = $order->user_id;
                $cust_ledger['order_id'] = $order->id;
                if ($order->payment_type == 'cash_on_delivery')
                    $cust_ledger['descriptions'] = 'Paid by Cash';
                else
                    $cust_ledger['descriptions'] = 'Paid by ' . $order->payment_type;
                $cust_ledger['type'] = 'Payment';
                $cust_ledger['debit'] = 0;
                $cust_ledger['credit'] = $paid;
                $cust_ledger['date'] = date('Y-m-d', strtotime($order->created_at));
                save_customer_ledger($cust_ledger);
            }
        }
    }
    public function customer_ledger_resolve()
    {


        $orders = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->select('orders.*', DB::raw('SUM(order_details.discount) AS total_discount'))
            ->whereNotNull('orders.user_id')
            ->where('orders.user_id', 1223);
        $orders = $orders->whereIn('order_details.delivery_status', ['confirmed', 'on_delivery', 'delivered']);

        $orders = $orders->groupBy('orders.id')->get();

        foreach ($orders as $order) {
            Customer_ledger::where('order_id', $order->id)->delete();
            $product_discount = $order->total_discount;
            $offer_discount = $order->special_discount;
            $total = $order->grand_total + $product_discount + $offer_discount;
            $paid = 0;
            if (!empty($order->payment_details)) {
                $payment = json_decode($order->payment_details);
                if (!empty($payment)) {
                    $paid = $payment->amount;
                }
            }
            $desc = 'Order by ' . $order->payment_type;
            $cust_ledger = array();
            $cust_ledger['customer_id'] = $order->user_id;
            $cust_ledger['order_id'] = $order->id;
            $cust_ledger['descriptions'] = $desc;
            $cust_ledger['type'] = 'Order';
            $cust_ledger['debit'] = $total;
            $cust_ledger['credit'] = 0;
            $cust_ledger['date'] = date('Y-m-d', strtotime($order->created_at));
            save_customer_ledger($cust_ledger);
            if (!empty($offer_discount)) {
                $cust_ledger_dis = array();
                $cust_ledger_dis['customer_id'] = $order->user_id;
                $cust_ledger_dis['order_id'] = $order->id;
                $cust_ledger_dis['descriptions'] = 'Offer Discount';
                $cust_ledger_dis['type'] = 'Discount';
                $cust_ledger_dis['debit'] = 0;
                $cust_ledger_dis['credit'] = $offer_discount;
                $cust_ledger_dis['date'] = date('Y-m-d', strtotime($order->created_at));
                save_customer_ledger($cust_ledger_dis);
            }
            if (!empty($product_discount)) {
                $cust_ledger_dis = array();
                $cust_ledger_dis['customer_id'] = $order->user_id;
                $cust_ledger_dis['order_id'] = $order->id;
                $cust_ledger_dis['descriptions'] = 'Discount';
                $cust_ledger_dis['type'] = 'Discount';
                $cust_ledger_dis['debit'] = 0;
                $cust_ledger_dis['credit'] = $product_discount;
                $cust_ledger_dis['date'] = date('Y-m-d', strtotime($order->created_at));
                save_customer_ledger($cust_ledger_dis);
            }
            if (!empty($paid)) {
                $cust_ledger = array();
                $cust_ledger['customer_id'] = $order->user_id;
                $cust_ledger['order_id'] = $order->id;
                if ($order->payment_type == 'cash_on_delivery')
                    $cust_ledger['descriptions'] = 'Paid by Cash';
                else
                    $cust_ledger['descriptions'] = 'Paid by ' . $order->payment_type;
                $cust_ledger['type'] = 'Payment';
                $cust_ledger['debit'] = 0;
                $cust_ledger['credit'] = $paid;
                $cust_ledger['date'] = date('Y-m-d', strtotime($order->created_at));
                save_customer_ledger($cust_ledger);
            }
        }
    }
    public function apply_coupon_code($order_id, $order, $total, $request)
    {
        $usages_coupon = CouponUsage::where('order_id', $order_id)->get();
        if (count($usages_coupon) == 0)
            return 0;
        $coupon = Coupon::where('id', $usages_coupon[0]->coupon_id)->first();

        if ($coupon != null) {
            $coupon_details = json_decode($coupon->details);

            if ($coupon->type == 'cart_base') {
                $subtotal = 0;
                $tax = 0;
                $shipping = 0;

                $sum = $total;
                if ($sum >= $coupon_details->min_buy) {
                    if ($coupon->discount_type == 'percent') {
                        $coupon_discount = ($sum * $coupon->discount) / 100;
                        if ($coupon_discount > $coupon_details->max_discount) {
                            $coupon_discount = $coupon_details->max_discount;
                        }
                    } elseif ($coupon->discount_type == 'amount') {
                        $coupon_discount = $coupon->discount;
                    }
                    return $coupon_discount;
                }
            } elseif ($coupon->type == 'product_base') {
                $coupon_discount = 0;
                foreach ($request->product as $key1 => $cartItem) {
                    foreach ($coupon_details as $key => $coupon_detail) {
                        if ($coupon_detail->product_id == $cartItem) {
                            if ($coupon->discount_type == 'percent') {
                                $coupon_discount += $request->rate[$key1] * $coupon->discount / 100;
                            } elseif ($coupon->discount_type == 'amount') {
                                $coupon_discount += $coupon->discount;
                            }
                        }
                    }
                }
                return $coupon_discount;
            }
        } else {
            return 0;
        }
    }

    public function get_puracher_product(Request $request)
    {
        $product_id = $request->product_id;
        $wearhouse_id = $request->wearhouse_id;
        $product = ProductStock::where(['product_id' => $product_id, 'wearhouse_id' => $wearhouse_id])->first();
        return $product;
    }

    // Cuatomer service All Orders
    public function cutomerservice_all_orders(Request $request)
    {

        $id = Auth::user()->name;

        $date = $request->date;
        $sort_search = null;
        $delivery_status = null;
        $payment_status = null;
        $payment_status = null;
        $warehouse_id = null;
        $warehouses = Warehouse::get();
        $orders = Order::orderBy('date', 'desc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->payment_status != null) {
            $orders = $orders->where('orders.payment_status', $request->payment_status);
            $payment_status = $request->payment_status;
        }
        if ($request->warehouse_id != null) {
            $orders = $orders->where('orders.warehouse', $request->warehouse_id);
            $warehouse_id = $request->warehouse_id;
        }
        if ($date != null) {
            $orders = $orders->where('orders.created_at', '>=', date('Y-m-d 00:00:00', strtotime(explode(" to ", $date)[0])))->where('orders.created_at', '<=', date('Y-m-d 23:59:59', strtotime(explode(" to ", $date)[1])));
        } else {
            $firstdate = date('Y-m-01 00:00:00');
            $lastdate = date('Y-m-t 12:59:59');
            $orders = $orders->where('orders.created_at', '>=', date('Y-m-d 00:00:00', strtotime($firstdate)))->where('orders.created_at', '<=', date('Y-m-d 23:59:59', strtotime($lastdate)));
        }
        $orders = $orders->leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')
            ->leftjoin('customers', 'customers.user_id', 'orders.user_id')
            ->select('orders.*', 'order_details.delivery_status', 'customers.staff_id');


        if (auth()->user()->name == 'Delivery Department') {
            $orders = $orders->whereIn('order_details.delivery_status', ['on_delivery', 'delivered']);
        } else if (auth()->user()->name == 'Operational Department') {
            $orders = $orders->whereIn('order_details.delivery_status', ['confirmed', 'on_delivery', 'delivered']);
        } else if (auth()->user()->name == 'Sales Department') {
            $orders = $orders->whereIn('order_details.delivery_status', ['pending', 'cancel', 'confirmed', 'on_delivery', 'delivered']);
        }

        $orders = $orders->groupBy('orders.id');
        $orders = $orders->paginate(15);

        return view('backend.staff_panel.customer_service.all_orders.index', compact('orders', 'sort_search', 'date', 'delivery_status', 'payment_status', 'warehouses', 'warehouse_id'));
    }


    // Operation Manager All Orders
    public function operation_manager_order(Request $request)
    {

        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        if (!$warehousearray) {
            $warehousearray = array();
        }
        $id = auth()->user()->name;

        $date = $request->date;
        $sort_search = null;
        $delivery_status = null;
        $payment_status = null;
        $payment_status = null;
        $warehouse_id = null;
        $warehouses = Warehouse::get();
        $orders = Order::orderBy('date', 'desc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->payment_status != null) {
            $orders = $orders->where('orders.payment_status', $request->payment_status);
            $payment_status = $request->payment_status;
        }
        if ($request->warehouse_id != null) {
            $orders = $orders->where('orders.warehouse', $request->warehouse_id);
            $warehouse_id = $request->warehouse_id;
        }
        if ($date != null) {
            $orders = $orders->where('orders.created_at', '>=', date('Y-m-d 00:00:00', strtotime(explode(" to ", $date)[0])))->where('orders.created_at', '<=', date('Y-m-d 23:59:59', strtotime(explode(" to ", $date)[1])));
        } else {
            $firstdate = date('Y-m-01 00:00:00');
            $lastdate = date('Y-m-t 12:59:59');
            $orders = $orders->where('orders.created_at', '>=', date('Y-m-d 00:00:00', strtotime($firstdate)))->where('orders.created_at', '<=', date('Y-m-d 23:59:59', strtotime($lastdate)));
        }
        $orders = $orders->leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')->select('orders.*', 'order_details.delivery_status');


        if (auth()->user()->name == 'Delivery Department') {
            $orders = $orders->whereIn('order_details.delivery_status', ['on_delivery', 'delivered']);
        } else if (auth()->user()->name == 'Operation Manager') {
            $orders = $orders->whereIn('order_details.delivery_status', ['confirmed', 'on_delivery', 'delivered'])->WhereIn('warehouse', $warehousearray);
        } else if (auth()->user()->name == 'Sales Department') {
            $orders = $orders->whereIn('order_details.delivery_status', ['pending', 'cancel', 'confirmed', 'on_delivery', 'delivered']);
        }

        $orders = $orders->WhereIn('warehouse', $warehousearray)->groupBy('orders.id');
        $orders = $orders->paginate(15);

        return view('backend.staff_panel.operation_manager.all_orders.index', compact('orders', 'sort_search', 'date', 'delivery_status', 'payment_status', 'warehouses', 'warehouse_id'));
    }
}
