<?php

namespace App\Http\Controllers;

use App\Exports\FlatfromSaleExport;
use App\Exports\TransferListExport;
use App\Exports\TransferSummaryExport;
use App\Exports\TransferDetailsExport;
use App\Exports\WarehouseSalesCompareExport;
use App\Exports\WarehouseYearlySalesCompareExport;
use App\Exports\ProductWiseSalesReport;
use App\Exports\ProductWisePurchaseExport;
use App\Exports\ParentChildProductListExport;
use App\Exports\GroupProductListExport;
use App\Exports\SalesProfitExport;
use App\Exports\SalesByPlatformExport;
use App\Exports\SingleEmployeePerformanceExport;
use Carbon\Carbon;
use DataTables;
use App\Models\User;
use App\Models\Order;
use App\Models\Staff;
use App\Models\Coupon;
use App\Models\Damage;
use App\Models\Search;
use App\Models\Seller;
use App\Models\Target;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Transfer; 
use App\Models\Warehouse;
use App\Models\CouponUsage;
use App\Models\Referr_code;
use App\Models\OrderDetail; 
use Illuminate\Http\Request;
use App\Models\OpeningStock; 
use App\Models\ProductStock; 
use App\Models\RefundRequest;
use App\Models\OrderStatusLog;
use App\Models\Purchase;
use App\Models\Purchase_;
use App\Models\Customer_ledger;
use App\Models\Supplier_ledger;
use App\Models\PurchaseDetail;
use App\Models\Product_stock_close; 
use Illuminate\Support\Facades\Auth;
use App\Models\DeliveryExecutiveLedger;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function stock_report(Request $request)
    {
        $sort_by = null;
        $pro_sort_by = null;
        $products = Product::join('categories', 'products.category_id', '=', 'categories.id')->orderBy('products.current_stock', 'desc');
        if ($request->has('category_id') && !empty($request->category_id)) {
            $sort_by = $request->category_id;
            $products = $products->where('category_id', $sort_by);
        }
        if (!empty($request->product_id)) {
            $pro_sort_by = $request->product_id;
            $products = $products->where('products.id', $pro_sort_by);
        }
        $products = $products->select('products.*', 'categories.name as category_name')->get();
        return view('backend.reports.stock_report', compact('products', 'sort_by', 'pro_sort_by'));
    }
    public function wearhouse_wise_stock_report(Request $request)
    {
        $wearhouse = Warehouse::get();
        $sort_by = null;
        $pro_sort_by = null;
        $wearhouse_id = $wearhouse[0]->id;
        $products = Product::join('product_stocks', 'products.id', '=', 'product_stocks.product_id')->orderBy('product_stocks.qty', 'desc');
        if (!empty($request->product_id)) {
            $pro_sort_by = $request->product_id;
            $products = $products->where('products.id', $pro_sort_by);
        }
        if ($request->has('category_id') && !empty($request->category_id)) {
            $sort_by = $request->category_id;
            $products = $products->where('product_stocks.wearhouse_id', $sort_by);
            $products = $products->select('products.*', 'product_stocks.qty')->get();
        } else {
            //$products = $products->where('product_stocks.wearhouse_id', $wearhouse_id); 
            $products = $products->select('products.*', DB::raw('sum(product_stocks.qty) as qty'))->groupBy('product_stocks.product_id')->get();
        }


        return view('backend.reports.wearhouse_wise_stock_report', compact('products', 'sort_by', 'pro_sort_by', 'wearhouse'));
    }

    public function wearhouse_wise_stock_ledger_report(Request $request)
    {
        $wearhouse = Warehouse::get();
        $sort_by = null;
        $pro_sort_by = null;
        $wearhouse_id = $wearhouse[0]->id;
        $category_id = '';

        if (!empty($request->from_date) && !empty($request->to_date)) {
            $from_date = date('Y-m-d', strtotime($request->from_date));
            $to_date = date('Y-m-d', strtotime($request->to_date));

            $from_sale_date = date('Y-m-d 00:00:00', strtotime($request->from_date));
            $to_sale_date = date('Y-m-d 23:59:59', strtotime($request->to_date));

            $from_string_time = strtotime($request->from_date);
            $to_string_time = strtotime($request->to_date);

            $month = date('Y-m', strtotime($request->from_date));
            $premonth = date('Y-m', strtotime($month . " -1 month"));
        } else {
            $from_date = date('Y-m-01');
            $to_date = date('Y-m-t');


            $from_sale_date = date('Y-m-01');
            $to_sale_date = date('Y-m-01');


            $from_string_time = strtotime($from_date);
            $to_string_time = strtotime($to_date);

            $month = date('Y-m', strtotime($from_date));
            $premonth = date('Y-m', strtotime($month . " -1 month"));
        }

        $products = Product::leftjoin('categories', 'products.category_id', '=', 'categories.id');


        if (!empty($request->product_id)) {
            $pro_sort_by = $request->product_id;
            $products->where('products.id', '=', $pro_sort_by);
        } else {
            //$products = Product::limit(100);
        }

        $products->whereNull('products.parent_id');

        if (!empty($request->category_id)) {
            $category_id = $request->category_id;
            $products->where('category_id', '=', $category_id);
        } else {

            if ($request->has('wearhouse_id') && !empty($request->wearhouse_id)) {
            } else {

                $products->limit(5);
            }
        }



        $products = $products->select('products.*', 'categories.name as category_name')->get();

        if ($request->has('wearhouse_id') && !empty($request->wearhouse_id)) {
            $sort_by = $request->wearhouse_id;
        }



        foreach ($products as $key => $value) {



            $child_products = array();
            $child_products = Product::where('parent_id', '=', $value['id'])->get();



            $o_purchase_info = array();
            $o_sale_info = array();
            $o_damage_info = array();


            $transfer_receive_info = array();
            $transfer_info = array();

            $purchase_info = array();
            $sale_info = array();
            $damage_info = array();

            $opening_qty = 0;
            $opening_amount = 0;
            $purchase_qty = 0;
            $purchase_amount = 0;
            $sale_qty = 0;
            $sale_amount = 0;

            $child_sales_qty = 0;
            $child_sales_amount = 0;

            $damage_qty = 0;
            $damage_amount = 0;

          
            $pre_stock = array();
            if (!empty($sort_by)) {
                $pre_stock = Product_stock_close::where('month', $premonth)
                    ->where('wh_id', $sort_by)
                    ->where('product_id', $value['id'])
                    ->get();

                if (isset($pre_stock[0]->closing_stock_qty) && !empty($pre_stock[0]->closing_stock_qty)) {
                    $opening_qty = $pre_stock[0]->closing_stock_qty;
                    $opening_amount = $pre_stock[0]->closing_stock_amount;
                }
            } else {

                $op_sql = "select sum(closing_stock_qty) as total_closing_stock,sum(closing_stock_amount) as total_closing_amount from product_stock_close where month='$premonth' and product_id=" . $value['id'];
                $pre_stock = DB::select($op_sql);
                if (isset($pre_stock[0]->total_closing_stock) && !empty($pre_stock[0]->total_closing_amount)) {
                    $opening_qty = $pre_stock[0]->total_closing_stock;
                    $opening_amount = $pre_stock[0]->total_closing_amount;
                }
            }


            $products[$key]->opening_stock_qty = $opening_qty;
            $products[$key]->opening_stock_amount = $opening_amount;


            if (!empty($sort_by)) {
                $p_sql = "select sum(poi.qty) as total_purchase_qty,sum(poi.amount) as total_purchase_amount from purchase_details poi left join purchases po on poi.id=po.id where (po.date>='$from_date' and po.date<='$to_date') and po.status=2 and po.wearhouse_id=$sort_by and poi.product_id=" . $value['id'];
            } else {
                $p_sql = "select sum(poi.qty) as total_purchase_qty,sum(poi.amount) as total_purchase_amount from purchase_details poi left join purchases po on poi.id=po.id where (po.date>='$from_date' and po.date<='$to_date') and po.status=2 and poi.product_id=" . $value['id'];
            }
            $purchase_info = DB::select($p_sql);

            if (!empty($sort_by)) {
                //$s_sql="select sum(od.quantity) as total_sale_qty,sum(od.price) as total_sale_amount from order_details od left join orders o on od.order_id=o.id where (o.date>=$from_string_time and o.date<=$to_string_time) and o.warehouse=$sort_by and od.delivery_status IN('delivered','confirmed','on-delivery') and product_id=".$value['id'];
                $s_sql = "select sum(od.quantity) as total_sale_qty,sum(od.price) as total_sale_amount from order_details od left join orders o on od.order_id=o.id where (o.created_at>='$from_sale_date' and o.created_at<='$to_sale_date') and o.warehouse=$sort_by and od.delivery_status='delivered' and od.product_id=" . $value['id'];
                // echo $s_sql;
                // exit;
            } else {
                //$s_sql="select sum(od.quantity) as total_sale_qty,sum(od.price) as total_sale_amount from order_details od left join orders o on od.order_id=o.id where (o.date>=$from_string_time and o.date<=$to_string_time) and od.delivery_status IN('delivered','confirmed','on-delivery') and product_id=".$value['id'];
                $s_sql = "select sum(od.quantity) as total_sale_qty,sum(od.price) as total_sale_amount from order_details od left join orders o on od.order_id=o.id where (o.created_at>='$from_sale_date' and o.created_at<='$to_sale_date') and od.delivery_status='delivered' and od.product_id=" . $value['id'];
            }
            $sale_info = DB::select($s_sql);



            //Child Product Sale info Start

            if (!empty($child_products)) {

                foreach ($child_products as $chk => $chval) {
                    $child_sale_info = array();

                    if (!empty($sort_by)) {
                        //$s_sql="select sum(od.quantity) as total_sale_qty,sum(od.price) as total_sale_amount from order_details od left join orders o on od.order_id=o.id where (o.date>=$from_string_time and o.date<=$to_string_time) and o.warehouse=$sort_by and od.delivery_status IN('delivered','confirmed','on-delivery') and product_id=".$value['id'];
                        $child_s_sql = "select sum(od.quantity) as total_sale_qty,sum(od.price) as total_sale_amount from order_details od left join orders o on od.order_id=o.id where (o.created_at>='$from_sale_date' and o.created_at<='$to_sale_date') and o.warehouse=$sort_by and od.delivery_status='delivered' and od.product_id=" . $chval['id'];
                    } else {
                        //$s_sql="select sum(od.quantity) as total_sale_qty,sum(od.price) as total_sale_amount from order_details od left join orders o on od.order_id=o.id where (o.date>=$from_string_time and o.date<=$to_string_time) and od.delivery_status IN('delivered','confirmed','on-delivery') and product_id=".$value['id'];
                        $child_s_sql = "select sum(od.quantity) as total_sale_qty,sum(od.price) as total_sale_amount from order_details od left join orders o on od.order_id=o.id where (o.created_at>='$from_sale_date' and o.created_at<='$to_sale_date') and od.delivery_status='delivered' and od.product_id=" . $chval['id'];
                    }

                    $child_sale_info = DB::select($child_s_sql);

                    if (isset($child_sale_info[0]->total_sale_qty) && !empty($child_sale_info[0]->total_sale_qty)) {
                        $child_sales_qty = $child_sales_qty + ($child_sale_info[0]->total_sale_qty * $chval['deduct_qty']);
                        $child_sales_amount = $child_sales_amount + $child_sale_info[0]->total_sale_amount;
                    }
                }
            }


            // Child Product Sale Info End

            if (!empty($sort_by)) {
                $d_sql = "select sum(qty) as total_damage_qty,sum(total_amount) as total_damage_amount from damages where (date>='$from_date' and date<='$to_date') and status='Approved' and wearhouse_id=$sort_by and product_id=" . $value['id'];
            } else {
                $d_sql = "select sum(qty) as total_damage_qty,sum(total_amount) as total_damage_amount from damages where (date>='$from_date' and date<='$to_date') and status='Approved' and product_id=" . $value['id'];
            }
            $damage_info = DB::select($d_sql);



            if (!empty($sort_by)) {
                $tr_r_sql = "select sum(qty) as total_transfer_receive_qty,sum(amount) as total_transfer_receive_amount from transfers where (date>='$from_date' and date<='$to_date') and status='Approved' and to_wearhouse_id=$sort_by and product_id=" . $value['id'];
                $transfer_receive_info = DB::select($tr_r_sql);
            } else {
                $tr_r_sql = "select sum(qty) as total_transfer_receive_qty,sum(amount) as total_transfer_receive_amount from transfers where (date>='$from_date' and date<='$to_date') and status='Approved' and to_wearhouse_id>0 and product_id=" . $value['id'];
                $transfer_receive_info = DB::select($tr_r_sql);
            }


            if (!empty($sort_by)) {
                $tr_sql = "select sum(qty) as total_transfer_qty,sum(amount) as total_transfer_amount from transfers where (date>='$from_date' and date<='$to_date') and status='Approved' and from_wearhouse_id=$sort_by and product_id=" . $value['id'];
                $transfer_info = DB::select($tr_sql);
            } else {
                $tr_sql = "select sum(qty) as total_transfer_qty,sum(amount) as total_transfer_amount from transfers where (date>='$from_date' and date<='$to_date') and status='Approved' and from_wearhouse_id>0 and product_id=" . $value['id'];
                $transfer_info = DB::select($tr_sql);
            }


            if (isset($transfer_receive_info[0]->total_transfer_receive_qty) && !empty($transfer_receive_info[0]->total_transfer_receive_qty)) {
                $products[$key]->transfer_receive_qty = $transfer_receive_qty = $transfer_receive_info[0]->total_transfer_receive_qty;
                $products[$key]->transfer_receive_amount = $transfer_receive_amount = $transfer_receive_info[0]->total_transfer_receive_amount;
            } else {
                $products[$key]->transfer_receive_qty = $transfer_receive_qty = 0;
                $products[$key]->transfer_receive_amount = $transfer_receive_amount = 0;
            }


            if (isset($transfer_info[0]->total_transfer_qty) && !empty($transfer_info[0]->total_transfer_qty)) {
                $products[$key]->transfer_qty = $transfer_qty = $transfer_info[0]->total_transfer_qty;
                $products[$key]->transfer_amount = $transfer_amount = $transfer_info[0]->total_transfer_amount;
            } else {
                $products[$key]->transfer_qty = $transfer_qty = 0;
                $products[$key]->transfer_amount = $transfer_amount = 0;
            }



            if (isset($purchase_info[0]->total_purchase_qty) && !empty($purchase_info[0]->total_purchase_qty)) {
                $products[$key]->purchase_qty = $purchase_qty = $purchase_info[0]->total_purchase_qty;
                $products[$key]->purchase_amount = $purchase_amount = $purchase_info[0]->total_purchase_amount;
            } else {
                $products[$key]->purchase_qty = $purchase_qty = 0;
                $products[$key]->purchase_amount = $purchase_amount = 0;
            }

            if (isset($sale_info[0]->total_sale_qty) && !empty($sale_info[0]->total_sale_qty)) {
                $products[$key]->sale_qty = $sale_qty = $sale_info[0]->total_sale_qty + $child_sales_qty;
                $products[$key]->sale_amount = $sale_amount = $sale_info[0]->total_sale_amount + $child_sales_amount;
            } else {
                $products[$key]->sale_qty = $sale_qty = 0 + $child_sales_qty;
                $products[$key]->sale_amount = $sale_amount = 0 + $child_sales_amount;
            }

            if (isset($damage_info[0]->total_damage_qty) && !empty($damage_info[0]->total_damage_qty)) {
                $products[$key]->damage_qty = $damage_qty = $damage_info[0]->total_damage_qty;
                $products[$key]->damage_amount = $damage_amount = $damage_info[0]->total_damage_amount;
            } else {
                $products[$key]->damage_qty = 0;
                $products[$key]->damage_amount = 0;
            }

            // echo  $products[$key]->sale_qty;
            // exit;



            $products[$key]->closing_qty = ($opening_qty + $transfer_receive_qty + $purchase_qty) - ($sale_qty + $damage_qty + $transfer_qty);
            $products[$key]->closing_amount = ($opening_amount + $transfer_receive_amount + $purchase_amount) - ($sale_amount + $damage_amount + $transfer_amount);
        }

        return view('backend.reports.wearhouse_wise_stock_ledger_report', compact('products', 'sort_by', 'pro_sort_by', 'wearhouse', 'category_id', 'from_date', 'to_date'));
    }

    public function monthly_stock_ledger_report(Request $request) 
    {
        ini_set('max_execution_time', 0);
    
        $category_id = '';
        $product_id = '';
        $warehouse_id = '';
    
        // Handling Date Ranges
        if (!empty($request->from_date) && !empty($request->to_date)) {
            $from_date = date('Y-m-d 00:00:00', strtotime($request->from_date));
            $to_date = date('Y-m-d 23:59:59', strtotime($request->to_date));
            $startDate = date('Y-m-d', strtotime($request->from_date));
            $endDate = date('Y-m-d', strtotime($request->to_date));
        } else {
            $from_date = date('Y-m-01 00:00:00');
            $to_date = date('Y-m-t 23:59:59');
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
        }
    
        if ($request->has('warehouse_id') && !empty($request->warehouse_id)) {
            $warehouse_id = $request->warehouse_id;
    
            $products = Product::select('products.*', 'categories.id as cat_id', 'categories.name as cat_name')
                ->leftjoin('categories', 'products.category_id', 'categories.id')
                ->where('products.parent_id', '=', null);
    
            if (!empty($request->category_id) && !empty($request->product_id)) {
                $category_id = $request->category_id;
                $product_id = $request->product_id;
                $products = $products->where('categories.id', $request->category_id)->where('products.id', $request->product_id)->get();
            } elseif (!empty($request->category_id)) {
                $category_id = $request->category_id;
                $products = $products->where('categories.id', $request->category_id)->get();
            } else {
                $products = $products->get();
            }
    
            foreach ($products as $key => $value) {
                  
    
                $opening_stocks = OpeningStock::where('product_id', $value->id)->where('wearhouse_id', $warehouse_id)->whereBetween('created_at', array($from_date, $to_date))->get();
                foreach ($opening_stocks as $openStock) {
                    $products[$key]->opening_stock_qty += $openStock->qty;
                    $products[$key]->opening_stock_amount += $openStock->qty * $openStock->price;
                }
    
                $purchases = PurchaseDetail::select('purchase_details.id', 'purchase_details.id', 'purchase_details.product_id', 'purchase_details.wearhouse_id', 'purchase_details.qty', 'purchase_details.price', 'purchase_details.amount', 'purchases.status', 'purchases.date')    
                    ->leftjoin('purchases', 'purchases.id', '=', 'purchase_details.id')
                    ->where('purchases.status', 2)
                    ->where('purchase_details.product_id', $value->id)
                    ->where('purchase_details.wearhouse_id', $warehouse_id)
                    ->whereBetween('purchases.date', array($startDate, $endDate))
                    ->get();

                foreach ($purchases as $purchase) {
                    $products[$key]->purchase_qty += $purchase->qty;
                    $products[$key]->purchase_amount += $purchase->qty * $purchase->price;
                }
    
                $received = Transfer::select('transfers.id', 'transfers.product_id', 'transfers.to_wearhouse_id', 'transfers.qty', 'transfers.unit_price as price', 'transfers.amount', 'transfers.status', 'transfers.date')
                    ->where('product_id', $value->id)
                    ->where('status', 'Approved')
                    ->where('to_wearhouse_id', $warehouse_id)
                    ->whereBetween('date', array($startDate, $endDate))->get();
                foreach ($received as $rece) {
                    $products[$key]->receive_qty += $rece->qty;
                    $products[$key]->receive_amount += $rece->qty * $rece->price;
                }
    
                // Minus Stock Part
                $main_orders = OrderDetail::select(
                    'order_details.id', 
                    'order_details.order_id', 
                    'order_details.product_id', 
                    DB::raw('SUM(order_details.price - order_details.coupon_discount) AS price'), 
                    'order_details.quantity as qty', 
                    'order_details.delivery_status', 
                    'orders.warehouse', 
                    'order_details.created_at', 
                    'order_details.updated_at'
                )
                ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
                ->where('order_details.delivery_status', 'delivered')
                ->where('order_details.product_id', $value->id)
                ->where('orders.warehouse', $warehouse_id)
                ->whereBetween('orders.delivered_date', [$from_date, $to_date])
                ->groupBy('order_details.order_id') 
                ->get();
            
    
                $child_orders = Product::select(
                    'order_details.id', 
                    'order_details.order_id', 
                    'order_details.product_id', 
                    DB::raw('SUM(order_details.price - order_details.coupon_discount) AS price'),
                    DB::raw('products.deduct_qty * order_details.quantity as qty'), 
                    'order_details.delivery_status', 
                    'orders.warehouse', 
                    'order_details.created_at', 
                    'order_details.updated_at'
                )
                ->leftJoin('order_details', 'order_details.product_id', 'products.id')
                ->leftJoin('orders', 'orders.id', 'order_details.order_id')
                ->where('parent_id', $value->id)
                ->where('orders.warehouse', $warehouse_id)
                ->where('order_details.delivery_status', 'delivered')
                ->whereBetween('orders.delivered_date', [$from_date, $to_date])
                ->groupBy('order_details.order_id') 
                ->get();
            
    
                $orders = $main_orders->merge($child_orders);
    
                foreach ($orders as $o_key => $o_value) {
                    $products[$key]['sales_qty'] += $o_value->qty;
                    $products[$key]['sales_amount'] += $o_value->price;
                }
    
                // Handle Refunds
                $refunds = RefundRequest::leftJoin('order_details', 'order_details.id', '=', 'refund_requests.order_detail_id')
                    ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
                    ->select('refund_requests.return_qty as qty', 'refund_requests.return_amount as amount', 'orders.warehouse')
                    ->where('order_details.product_id', $value->id)
                    ->where('refund_requests.refund_status', 5)
                    ->where('orders.warehouse', $warehouse_id)
                    ->whereBetween('refund_requests.created_at', [$from_date, $to_date])
                    ->get();
    
                foreach ($refunds as $o_key => $o_value) {

                    $products[$key]['refund_qty'] = $o_value->qty;
                    $products[$key]['refund_amount'] = $o_value->amount;

                    // $products[$key]['sales_qty'] -= $o_value->return_qty;
                    // $products[$key]['sales_amount'] -= $o_value->return_amount;
                }
    
                // foreach($refunds as $refund){
                //     $refund->qty = -$refund->return_qty;
                //     $refund->price = -($refund->amount / $refund->qty);
                //     $refund->amount = -$refund->amount;
                //     $refund->date = date('Y-m-d', strtotime($refund->created_at));
                //     $orders->push($refund);
                // }
    
                $transfers = Transfer::select('transfers.id', 'transfers.product_id', 'transfers.from_wearhouse_id', 'transfers.qty', 'transfers.unit_price as price', 'transfers.amount', 'transfers.status', 'transfers.date')
                    ->where('product_id', $value->id)
                    ->where('status', 'Approved')
                    ->where('from_wearhouse_id', $warehouse_id)
                    ->whereBetween('date', array($startDate, $endDate))->get();
    
                foreach ($transfers as $t_key => $t_value) {
                    $products[$key]['transfer_qty'] += $t_value->qty;
                    $products[$key]['transfer_amount'] += $t_value->qty * $t_value->price;
                }
    
                $damages = Damage::select('damages.id', 'damages.product_id', 'damages.wearhouse_id', 'damages.qty', 'damages.total_amount as amount', 'damages.status', 'damages.date')
                    ->where('product_id', $value->id)
                    ->where('status', 'Approved')
                    ->where('wearhouse_id', $warehouse_id)
                    ->whereBetween('date', array($startDate, $endDate))
                    ->get();
    
                foreach ($damages as $d_key => $d_value) {
                    $products[$key]['damage_qty'] += $d_value->qty;
                    $products[$key]['damage_amount'] += $d_value->amount;
                }
    
                // Marge Added Stock Product
                $purchase_item = $opening_stocks->merge($purchases)->merge($received)->merge($refunds);
                
                // Marge Minus Stock Product
                $sales = $main_orders->merge($child_orders)->merge($transfers)->merge($damages);
    
                // FIFO Calculation
                $remaining_sales_qty = $products[$key]['sales_qty'] + $products[$key]['transfer_qty'] + $products[$key]['damage_qty'];
                $purchase_items = $purchase_item->sortBy('date');
    
                foreach ($purchase_items as $purchase) {
                    if ($remaining_sales_qty > 0) {
                        if ($purchase->qty >= $remaining_sales_qty) {
                            $purchase->qty -= $remaining_sales_qty;
                            $remaining_sales_qty = 0; // All sales accounted for

                        } else {
                            $remaining_sales_qty -= $purchase->qty;
                            $purchase->qty = 0; // Deplete this purchase item
                            
                        }
                    }
    
                    if ($purchase->qty > 0) {
                        
                        $products[$key]->closing_stock_qty += $purchase->qty;
                        $products[$key]->closing_stock_amount += $purchase->qty * $purchase->price;
                    }
                }
    
                // Ensure non-negative closing quantities
                if ($products[$key]->closing_stock_qty < 0) {
                    $products[$key]->closing_stock_qty = 0;
                    $products[$key]->closing_stock_amount = 0;
                }
            }
        } else {
            $products = '';
        }
        return view('backend.reports.monthly_stock_ledger_report', compact('products', 'warehouse_id', 'category_id', 'product_id', 'from_date','to_date'));
    }

    public function getProducts(Request $request) 
    {
        $products = Product::where('products.parent_id', '=', null)->where('category_id', $request->value)->get();
        return $products;
    }

    public function stock_closing(Request $request)
    {
        ini_set('max_execution_time', 0);
    
        $category_id = '';
        $product_id = '';
        $warehouse_id = '';
    
        if (!empty($request->month)) {
            $array = explode('/', $request->month);
            $month = $array[1] . '-' . $array[0];
    
            $from_date = date('Y-m-01 00:00:00', strtotime($month));
            $to_date = date('Y-m-t 23:59:59', strtotime($month));
            $startDate = date('Y-m-01', strtotime($month));
            $endDate = date('Y-m-t', strtotime($month));
        } else {
            $month = date('Y-m');
            $from_date = date('Y-m-01 00:00:00');
            $to_date = date('Y-m-t 23:59:59');
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
        }
    
        if ($request->has('warehouse_id') && !empty($request->warehouse_id)) {
            $warehouse_id = $request->warehouse_id;
    
            $products = Product::select('products.*', 'categories.id as cat_id', 'categories.name as cat_name')
                ->leftjoin('categories', 'products.category_id', 'categories.id')
                ->where('products.parent_id', '=', null);
    
            if (!empty($request->category_id) && !empty($request->product_id)) {
                $category_id = $request->category_id;
                $product_id = $request->product_id;
                $products = $products->where('categories.id', $request->category_id)->where('products.id', $request->product_id)->get();
            } elseif (!empty($request->category_id)) {
                $category_id = $request->category_id;
                $products = $products->where('categories.id', $request->category_id)->get();
            } else {
                $products = $products->get();
            }
    
            foreach ($products as $key => $value) {
                $products[$key]->opening_stock_qty = 0;
                $products[$key]->opening_stock_amount = 0;
                $products[$key]->purchase_qty = 0;
                $products[$key]->purchase_amount = 0;
                $products[$key]->receive_qty = 0;
                $products[$key]->receive_amount = 0;
                $products[$key]->sales_qty = 0;
                $products[$key]->sales_amount = 0;
                $products[$key]->transfer_qty = 0;
                $products[$key]->transfer_amount = 0;
                $products[$key]->damage_qty = 0;
                $products[$key]->damage_amount = 0;
                $products[$key]->closing_stock_qty = 0;
                $products[$key]->closing_stock_amount = 0;
    
                $opening_stocks = OpeningStock::where('product_id', $value->id)->where('wearhouse_id', $warehouse_id)->whereBetween('created_at', array($from_date, $to_date))->get();
                foreach ($opening_stocks as $openStock) {
                    $products[$key]->opening_stock_qty += $openStock->qty;
                    $products[$key]->opening_stock_amount += $openStock->qty * $openStock->price;
                }
    
                $purchases = PurchaseDetail::select('purchase_details.id', 'purchase_details.id', 'purchase_details.product_id', 'purchase_details.wearhouse_id', 'purchase_details.qty', 'purchase_details.price', 'purchase_details.amount', 'purchases.status', 'purchases.date')    
                    ->leftjoin('purchases', 'purchases.id', '=', 'purchase_details.id')
                    ->where('purchases.status', 2)
                    ->where('purchase_details.product_id', $value->id)
                    ->where('purchase_details.wearhouse_id', $warehouse_id)
                    ->whereBetween('purchases.date', array($startDate, $endDate))
                    ->get();

                foreach ($purchases as $purchase) {
                    $products[$key]->purchase_qty += $purchase->qty;
                    $products[$key]->purchase_amount += $purchase->qty * $purchase->price;
                }
    
                $received = Transfer::select('transfers.id', 'transfers.product_id', 'transfers.to_wearhouse_id', 'transfers.qty', 'transfers.unit_price as price', 'transfers.amount', 'transfers.status', 'transfers.date')
                    ->where('product_id', $value->id)
                    ->where('status', 'Approved')
                    ->where('to_wearhouse_id', $warehouse_id)
                    ->whereBetween('date', array($startDate, $endDate))->get();
                foreach ($received as $rece) {
                    $products[$key]->receive_qty += $rece->qty;
                    $products[$key]->receive_amount += $rece->qty * $rece->price;
                }
    
                // Minus Stock Part
                $main_orders = OrderDetail::select(
                    'order_details.id', 
                    'order_details.order_id', 
                    'order_details.product_id', 
                    DB::raw('SUM(order_details.price - order_details.coupon_discount) AS price'), 
                    'order_details.quantity as qty', 
                    'order_details.delivery_status', 
                    'orders.warehouse', 
                    'order_details.created_at', 
                    'order_details.updated_at'
                )
                ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
                ->where('order_details.delivery_status', 'delivered')
                ->where('order_details.product_id', $value->id)
                ->where('orders.warehouse', $warehouse_id)
                ->whereBetween('orders.delivered_date', [$from_date, $to_date])
                ->groupBy('order_details.order_id') 
                ->get();
            
    
                $child_orders = Product::select(
                    'order_details.id', 
                    'order_details.order_id', 
                    'order_details.product_id', 
                    DB::raw('SUM(order_details.price - order_details.coupon_discount) AS price'),
                    DB::raw('products.deduct_qty * order_details.quantity as qty'), 
                    'order_details.delivery_status', 
                    'orders.warehouse', 
                    'order_details.created_at', 
                    'order_details.updated_at'
                )
                ->leftJoin('order_details', 'order_details.product_id', 'products.id')
                ->leftJoin('orders', 'orders.id', 'order_details.order_id')
                ->where('parent_id', $value->id)
                ->where('orders.warehouse', $warehouse_id)
                ->where('order_details.delivery_status', 'delivered')
                ->whereBetween('orders.delivered_date', [$from_date, $to_date])
                ->groupBy('order_details.order_id') 
                ->get();
    
                $orders = $main_orders->merge($child_orders);
    
                foreach ($orders as $o_key => $o_value) {
                    $products[$key]['sales_qty'] += $o_value->qty;
                    $products[$key]['sales_amount'] += $o_value->price;
                }
    
                // Handle Refunds
                $refunds = RefundRequest::leftJoin('order_details', 'order_details.id', '=', 'refund_requests.order_detail_id')
                    ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
                    ->select('refund_requests.return_qty as qty', 'refund_requests.return_amount as amount', 'orders.warehouse')
                    ->where('order_details.product_id', $value->id)
                    ->where('refund_requests.refund_status', 5)
                    ->where('orders.warehouse', $warehouse_id)
                    ->whereBetween('refund_requests.created_at', [$from_date, $to_date])
                    ->get();
    
                foreach ($refunds as $o_key => $o_value) {
                    $products[$key]['refund_qty'] = $o_value->qty;
                    $products[$key]['refund_amount'] = $o_value->amount;
                    // $products[$key]['sales_qty'] -= $o_value->return_qty;
                    // $products[$key]['sales_amount'] -= $o_value->return_amount;
                }
    
                // foreach($refunds as $refund){
                //     $refund->qty = -$refund->return_qty;
                //     $refund->price = -($refund->return_amount / $refund->return_qty);
                //     $refund->amount = -$refund->return_amount;
                //     $refund->date = date('Y-m-d', strtotime($refund->created_at));
                //     $orders->push($refund);
                // }
    
                $transfers = Transfer::select('transfers.id', 'transfers.product_id', 'transfers.from_wearhouse_id', 'transfers.qty', 'transfers.unit_price as price', 'transfers.amount', 'transfers.status', 'transfers.date')
                    ->where('product_id', $value->id)
                    ->where('status', 'Approved')
                    ->where('from_wearhouse_id', $warehouse_id)
                    ->whereBetween('date', array($startDate, $endDate))->get();
    
                foreach ($transfers as $t_key => $t_value) {
                    $products[$key]['transfer_qty'] += $t_value->qty;
                    $products[$key]['transfer_amount'] += $t_value->qty * $t_value->price;
                }
    
                $damages = Damage::select('damages.id', 'damages.product_id', 'damages.wearhouse_id', 'damages.qty', 'damages.total_amount as amount', 'damages.status', 'damages.date')
                    ->where('product_id', $value->id)
                    ->where('status', 'Approved')
                    ->where('wearhouse_id', $warehouse_id)
                    ->whereBetween('date', array($startDate, $endDate))
                    ->get();
    
                foreach ($damages as $d_key => $d_value) {
                    $products[$key]['damage_qty'] += $d_value->qty;
                    $products[$key]['damage_amount'] += $d_value->amount;
                }
    
                // Marge Added Stock Product
                $purchase_item = $opening_stocks->merge($purchases)->merge($received)->merge($refunds);
                
                // Marge Minus Stock Product
                $sales = $main_orders->merge($child_orders)->merge($transfers)->merge($damages);
                
                // FIFO Calculation
                $remaining_sales_qty = $products[$key]['sales_qty'] + $products[$key]['transfer_qty'] + $products[$key]['damage_qty'];
                $purchase_items = $purchase_item->sortBy('date');

                foreach ($purchase_items as $purchase) {
                    if ($remaining_sales_qty > 0) {
                        if ($purchase->qty >= $remaining_sales_qty) {
                            $purchase->qty -= $remaining_sales_qty;
                            $remaining_sales_qty = 0; 
                        } else {
                            $remaining_sales_qty -= $purchase->qty;
                            $purchase->qty = 0; 
                        }
                    }
    
                    if ($purchase->qty > 0) {
                        $products[$key]->closing_stock_qty += $purchase->qty;
                        $products[$key]->closing_stock_amount += $purchase->qty * $purchase->price;
                    }
                }
    
                if ($products[$key]->closing_stock_qty < 0) {
                    $products[$key]->closing_stock_qty = 0;
                    $products[$key]->closing_stock_amount = 0;
                }
            }
        } else {
            $products = '';
        }
    
        return view('backend.reports.stock_closing', compact('products', 'warehouse_id', 'category_id', 'product_id', 'month'));
    }

    public function product_stock_closing_details(Request $request, $id)
    {
        $warehouse_id = $request->input('warehouse_id');
        $month = $request->input('month');

        $product = Product::findOrFail($id);

        $suppliers = PurchaseDetail::select(
                        'suppliers.name as supplier_name', 
                        'purchase_details.price as rate', 
                        'purchase_details.amount',
                        'purchases.created_at as purchase_date' 
                    )
                    ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
                    ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.supplier_id')
                    ->where('purchase_details.product_id', $id)
                    ->where('purchases.wearhouse_id', $warehouse_id)
                    ->whereMonth('purchases.approved_date', $month)
                    ->get();

        return view('backend.reports.product_stock_closing_details', compact('product', 'suppliers', 'warehouse_id', 'month'));
    }

    public function save_stock_closing(Request $request) 
    {
        ini_set('max_execution_time', 0);
    
        $category_id = '';
        $product_id = '';
        $warehouse_id = '';
    
        if (!empty($request->month)) {
            $array = explode('/', $request->month);
            $month = $array[1] . '-' . $array[0];
    
            $from_date = date('Y-m-01 00:00:00', strtotime($month));
            $to_date = date('Y-m-t 23:59:59', strtotime($month));
            $startDate = date('Y-m-01', strtotime($month));
            $endDate = date('Y-m-t', strtotime($month));
    
            $nextMFdate = date('Y-m-d 00:00:00', strtotime($startDate . ' + 1 months'));
            $nextMLdate = date('Y-m-t 23:59:59', strtotime($startDate . ' + 1 months'));
        } else {
            $month = date('Y-m');
            $from_date = date('Y-m-01 00:00:00');
            $to_date = date('Y-m-t 23:59:59');
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
    
            $nextMFdate = date('Y-m-d 00:00:00', strtotime($startDate . ' + 1 months'));
            $nextMLdate = date('Y-m-t 23:59:59', strtotime($startDate . ' + 1 months'));
        }
    
        if ($request->has('warehouse_id') && !empty($request->warehouse_id)) {
            $warehouse_id = $request->warehouse_id;
    
            $products = Product::select('products.*', 'categories.id as cat_id', 'categories.name as cat_name')
                ->leftjoin('categories', 'products.category_id', 'categories.id')
                ->where('products.parent_id', '=', null);
    
            if (!empty($request->category_id) && !empty($request->product_id)) {
                $category_id = $request->category_id;
                $product_id = $request->product_id;
                $products = $products->where('categories.id', $request->category_id)->where('products.id', $request->product_id)->get();
            } elseif (!empty($request->category_id)) {
                $category_id = $request->category_id;
                $products = $products->where('categories.id', $request->category_id)->get();
            } else {
                $products = $products->get();
            }
    
            if (OpeningStock::where('wearhouse_id', $warehouse_id)->whereBetween('created_at', array($nextMFdate, $nextMLdate))->delete()) {
            }else{
                foreach ($products as $key => $value) {
                    $products[$key]->opening_stock_qty = 0;
                    $products[$key]->opening_stock_amount = 0;
                    $products[$key]->purchase_qty = 0;
                    $products[$key]->purchase_amount = 0;
                    $products[$key]->receive_qty = 0;
                    $products[$key]->receive_amount = 0;
                    $products[$key]->sales_qty = 0;
                    $products[$key]->sales_amount = 0;
                    $products[$key]->transfer_qty = 0;
                    $products[$key]->transfer_amount = 0;
                    $products[$key]->damage_qty = 0;
                    $products[$key]->damage_amount = 0;
                    $products[$key]->closing_stock_qty = 0;
                    $products[$key]->closing_stock_amount = 0;
        
                    $opening_stocks = OpeningStock::where('product_id', $value->id)->where('wearhouse_id', $warehouse_id)->whereBetween('created_at', array($from_date, $to_date))->get();
                    foreach ($opening_stocks as $openStock) {
                        $products[$key]->opening_stock_qty += $openStock->qty;
                        $products[$key]->opening_stock_amount += $openStock->qty * $openStock->price;
                    }
        
                    $purchases = PurchaseDetail::select('purchase_details.id', 'purchase_details.id', 'purchase_details.product_id', 'purchase_details.wearhouse_id', 'purchase_details.qty', 'purchase_details.price', 'purchase_details.amount', 'purchases.status', 'purchases.date')    
                        ->leftjoin('purchases', 'purchases.id', '=', 'purchase_details.id')
                        ->where('purchases.status', 2)
                        ->where('purchase_details.product_id', $value->id)
                        ->where('purchase_details.wearhouse_id', $warehouse_id)
                        ->whereBetween('purchases.date', array($startDate, $endDate))
                        ->get();
    
                    foreach ($purchases as $purchase) {
                        $products[$key]->purchase_qty += $purchase->qty;
                        $products[$key]->purchase_amount += $purchase->qty * $purchase->price;
                    }
        
                    $received = Transfer::select('transfers.id', 'transfers.product_id', 'transfers.to_wearhouse_id', 'transfers.qty', 'transfers.unit_price as price', 'transfers.amount', 'transfers.status', 'transfers.date')
                        ->where('product_id', $value->id)
                        ->where('status', 'Approved')
                        ->where('to_wearhouse_id', $warehouse_id)
                        ->whereBetween('date', array($startDate, $endDate))->get();
                    foreach ($received as $rece) {
                        $products[$key]->receive_qty += $rece->qty;
                        $products[$key]->receive_amount += $rece->qty * $rece->price;
                    }
        
                    // Minus Stock Part
                    $main_orders = OrderDetail::select(
                        'order_details.id', 
                        'order_details.order_id', 
                        'order_details.product_id', 
                        DB::raw('SUM(order_details.price - order_details.coupon_discount) AS price'), 
                        'order_details.quantity as qty', 
                        'order_details.delivery_status', 
                        'orders.warehouse', 
                        'order_details.created_at', 
                        'order_details.updated_at'
                    )
                    ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
                    ->where('order_details.delivery_status', 'delivered')
                    ->where('order_details.product_id', $value->id)
                    ->where('orders.warehouse', $warehouse_id)
                    ->whereBetween('orders.delivered_date', [$from_date, $to_date])
                    ->groupBy('order_details.order_id') 
                    ->get();
                
        
                    $child_orders = Product::select(
                        'order_details.id', 
                        'order_details.order_id', 
                        'order_details.product_id', 
                        DB::raw('SUM(order_details.price - order_details.coupon_discount) AS price'),
                        DB::raw('products.deduct_qty * order_details.quantity as qty'), 
                        'order_details.delivery_status', 
                        'orders.warehouse', 
                        'order_details.created_at', 
                        'order_details.updated_at'
                    )
                    ->leftJoin('order_details', 'order_details.product_id', 'products.id')
                    ->leftJoin('orders', 'orders.id', 'order_details.order_id')
                    ->where('parent_id', $value->id)
                    ->where('orders.warehouse', $warehouse_id)
                    ->where('order_details.delivery_status', 'delivered')
                    ->whereBetween('orders.delivered_date', [$from_date, $to_date])
                    ->groupBy('order_details.order_id') 
                    ->get();
        
                    $orders = $main_orders->merge($child_orders);
        
                    foreach ($orders as $o_key => $o_value) {
                        $products[$key]['sales_qty'] += $o_value->qty;
                        $products[$key]['sales_amount'] += $o_value->price;
                    }
        
                    // Handle Refunds
                    $refunds = RefundRequest::leftJoin('order_details', 'order_details.id', '=', 'refund_requests.order_detail_id')
                        ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
                        ->select('refund_requests.return_qty', 'refund_requests.return_amount', 'orders.warehouse')
                        ->where('order_details.product_id', $value->id)
                        ->where('refund_requests.refund_status', 5)
                        ->where('orders.warehouse', $warehouse_id)
                        ->whereBetween('refund_requests.created_at', [$from_date, $to_date])
                        ->get();
        
                    foreach ($refunds as $o_key => $o_value) {
                        $products[$key]['sales_qty'] -= $o_value->return_qty;
                        $products[$key]['sales_amount'] -= $o_value->return_amount;
                    }
        
                    foreach($refunds as $refund){
                        $refund->qty = -$refund->return_qty;
                        $refund->price = -($refund->return_amount / $refund->return_qty);
                        $refund->amount = -$refund->return_amount;
                        $refund->date = date('Y-m-d', strtotime($refund->created_at));
                        $orders->push($refund);
                    }
        
                    $transfers = Transfer::select('transfers.id', 'transfers.product_id', 'transfers.from_wearhouse_id', 'transfers.qty', 'transfers.unit_price as price', 'transfers.amount', 'transfers.status', 'transfers.date')
                        ->where('product_id', $value->id)
                        ->where('status', 'Approved')
                        ->where('from_wearhouse_id', $warehouse_id)
                        ->whereBetween('date', array($startDate, $endDate))->get();
        
                    foreach ($transfers as $t_key => $t_value) {
                        $products[$key]['transfer_qty'] += $t_value->qty;
                        $products[$key]['transfer_amount'] += $t_value->qty * $t_value->price;
                    }
        
                    $damages = Damage::select('damages.id', 'damages.product_id', 'damages.wearhouse_id', 'damages.qty', 'damages.total_amount as amount', 'damages.status', 'damages.date')
                        ->where('product_id', $value->id)
                        ->where('status', 'Approved')
                        ->where('wearhouse_id', $warehouse_id)
                        ->whereBetween('date', array($startDate, $endDate))
                        ->get();
        
                    foreach ($damages as $d_key => $d_value) {
                        $products[$key]['damage_qty'] += $d_value->qty;
                        $products[$key]['damage_amount'] += $d_value->amount;
                    }
        
                    // Marge Added Stock Product
                    $purchase_item = $opening_stocks->merge($purchases)->merge($received);
                    
                    // Marge Minus Stock Product
                    $sales = $main_orders->merge($child_orders)->merge($transfers)->merge($damages);
                    
                    // FIFO Calculation
                    $remaining_sales_qty = $products[$key]['sales_qty'] + $products[$key]['transfer_qty'] + $products[$key]['damage_qty'];
                    $purchase_items = $purchase_item->sortBy('date');
    
                    $purchase_items = $purchase_item->sortBy('date');
    
                    // Save new opening stock
                    foreach ($purchase_items as $purchase) {
                        if ($remaining_sales_qty > 0) {
                            if ($purchase->qty >= $remaining_sales_qty) {
                                $purchase->qty -= $remaining_sales_qty;
                                $remaining_sales_qty = 0; 
                            } else {
                                $remaining_sales_qty -= $purchase->qty;
                                $purchase->qty = 0; 
                            }
                        }
    
                        $existing_row = OpeningStock::select('id', 'qty')->where('product_id', $value->id)
                            ->where('wearhouse_id', $warehouse_id)
                            ->whereBetween('created_at', [$nextMFdate, $nextMLdate])
                            ->where('price', $purchase->price)->first();
    
                        if (!empty($existing_row)) {
                            $item = OpeningStock::find($existing_row->id);
                            $item->product_id = $value->id;
                            $item->wearhouse_id = $warehouse_id;
                            $item->qty = $existing_row->qty + $purchase->qty;
                            $item->price = $purchase->price;
                            $item->amount = ($existing_row->qty + $purchase->qty) * $purchase->price;
                            $item->created_at = $nextMFdate;
                            $item->updated_at = $nextMFdate;
                            $item->save();
                        } else {
                            $item = new OpeningStock();
                            $item->product_id = $value->id;
                            $item->wearhouse_id = $warehouse_id;
                            $item->qty = $purchase->qty;
                            $item->price = $purchase->price;
                            $item->amount = $purchase->qty * $purchase->price;
                            $item->created_at = $nextMFdate;
                            $item->updated_at = $nextMFdate;
        
                            $item->save();
                        }

                        $product_stock =  ProductStock::where('product_id',$value->id)->where('wearhouse_id',$warehouse_id)->first();
                            
                        if(!empty($product_stock)){
                            $product_stock->qty = $item->qty;
                            $product_stock->price = $purchase->price;
                            $product_stock->opening_stock = $item->qty;
                            $product_stock->save();
                        }else
                        {
                            $product_stock = new ProductStock();
                            $product_stock->product_id = $value->id;
                            $product_stock->wearhouse_id = $warehouse_id;
                            $product_stock->qty = $item->qty;
                            $product_stock->price = $purchase->price;
                            $product_stock->opening_stock = $item->qty;
                            $product_stock->save();
                        }
                    }
                }
            }
 
        } else {
            $products = '';
        }
    
        flash(translate('Stock closed successfully'))->success();
        return redirect()->route('stock_closing');
    }

    public function customer_ledger(Request $request)
    {
        if (!empty($request->user_id) && !empty($request->warehouse)) {
            flash(__('Filter by Only Employee or Wearhouse'))->error();
            return back();
        } else {
            $wearhouse = $request->warehouse;
            $sort_by = null;
            $status = null;

            $sort_search = '';

            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
            $month = $request->month;
            $month_year = null;
            $year = $request->year;

            if (empty($request->start_date))
                $request->start_date = $start_date;
            if (empty($request->end_date))
                $request->end_date = $end_date;

            if (!empty($request->month)) {
                $month_year = date('Y', strtotime($request->month));
                $month = date('m', strtotime($request->month));
            
                $start_date = date('Y-m-01', strtotime("$month_year-$month-01"));
                $end_date = date('Y-m-t', strtotime("$month_year-$month-01"));
            }
            
            
            if (!empty($request->year)) {
                $start_date = date('Y-m-01', strtotime("$year-01-01"));
                $end_date = date('Y-m-t', strtotime("$year-12-31"));
            }

            $cust = array();
            $orders = array();

            $user_id = $request->user_id;
            $sql = "SELECT
                    u.name,
                    c.user_id,
                    c.customer_id as customer_no,
                    sum(cl.debit) as debit,
                    sum(cl.credit) as credit,
                    sum(cl.balance) as balance,
                    (select sum(cll.debit-cll.credit) from customer_ledger as cll 
                    where c.user_id=cll.customer_id and cll.date < '" . $request->start_date . "') as opening_balance
            FROM
            customers c
            LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id
            LEFT JOIN users u ON c.user_id = u.id
            LEFT JOIN orders on orders.id = cl.order_id";
            $sql .= " where 1=1 ";

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start_date = date('Y-m-d', strtotime($request->start_date));
                $end_date = date('Y-m-d', strtotime($request->end_date));
                $sql .= "	and (cl.date between '" . $start_date . "' and '" . $end_date . "' or cl.date is null) ";
            }
            if ($request->has('search')) {
                $sort_search = $s = $request->search;
                $sql .= " and (u.email like '%" . $s . "%' or u.name like '%" . $s . "%' or u.phone like '%" . $s . "%' or c.customer_id like '%" . $s . "%') ";
            }
            if (!empty($request->user_id)) {
                $sql .= " AND c.staff_id = $user_id";
            }

            if (!empty($wearhouse)) {

                $sql .= " AND orders.warehouse = $wearhouse";
            }

            $sql .= "	and (debit>0 or credit>0) 
            GROUP BY c.customer_id
            order by u.name asc";
            $customers = DB::select($sql);


            $sql2 = "SELECT
                        SUM(cl.debit) AS debit,
                        SUM(cl.credit) AS credit,
                        SUM(cl.balance) AS balance,
                        (
                            SELECT SUM(cll.debit - cll.credit)
                            FROM customer_ledger AS cll
                            WHERE c.user_id = cll.customer_id AND cll.date < '" . $request->end_date . "'
                        ) AS opening_balance
                    FROM customers c
                    LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id
                    LEFT JOIN orders on orders.id = cl.order_id
                    WHERE (cl.debit > 0 OR cl.credit > 0)";

            if (!empty($request->user_id)) {
                $user_id = $request->user_id;
                $sql2 .= " AND c.staff_id = $user_id";
            }

            if (!empty($wearhouse)) {

                $sql2 .= " AND orders.warehouse = $wearhouse";
            }

            $customers2 = DB::select($sql2);
            $due = 0;
            $opening_balance = 0;
            foreach ($customers2 as $key => $customer) {
                $due += $customer->opening_balance + $customer->debit - $customer->credit;
            }

            return view('backend.reports.customer_ledger_main', compact('customers', 'start_date', 'end_date', 'sort_search', 'user_id', 'due', 'wearhouse','month','year','month_year'));
        }
    }

    public function customer_ledger_details(Request $request)
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

        $sql .= " order by cl.updated_at asc,cl.order_id DESC,
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

        return view('backend.reports.customer_ledger', compact('customers', 'cust', 'start_date', 'end_date', 'opening'));
    }

    public function supplier_ledger(Request $request)
    {
        $wearhouse = Warehouse::where('id',getWearhouseBuUserId(Auth::user()->id))->get();
        if ($request->has('warehouse') && !empty($request->warehouse)) {
            $wearhouse = $request->warehouse;
        }else{
            $wearhouse = getWearhouseBuUserId(Auth::user()->id);
            $wearhouse = implode('',  $wearhouse);
        }
        
        $sort_by = null;
        $status = null;
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $month = $request->month;
        $month_year = null;
        $year = $request->year;

        if (empty($request->start_date))
            $request->start_date = $start_date;
        if (empty($request->end_date))
            $request->end_date = $end_date;

       if (!empty($request->month)) {
            $month_year = date('Y', strtotime($request->month));
            $month = date('m', strtotime($request->month));

            $start_date = date('Y-m-01', strtotime("$month_year-$month-01"));
            $end_date = date('Y-m-t', strtotime("$month_year-$month-01"));
        }

        if (!empty($request->year)) {
            $start_date = date('Y-m-01', strtotime("$year-01-01"));
            $end_date = date('Y-m-t', strtotime("$year-12-31"));
        }

        $cust = array();
        $orders = array();


        $sql = "SELECT
        s.supplier_id,s.name,sum(sl.debit) as debit,sum(sl.credit) as credit,sum(sl.balance) as balance,
        (select sum(sll.debit-sll.credit) from supplier_ledger as sll where s.supplier_id = sll.supplier_id and sll.date <'" . $request->start_date . "') as opening_balance
        FROM
            suppliers s
            LEFT JOIN supplier_ledger sl ON s.supplier_id = sl.supplier_id
            LEFT JOIN purchases po ON sl.purchase_id = po.id";

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d', strtotime($request->end_date));
            $sql .= "	where sl.date between '" . $start_date . "' and '" . $end_date . "' or sl.date is null ";
        } else {
            $sql .= "	where sl.date between '" . $start_date . "' and '" . $end_date . "' or sl.date is null ";
        }

        $sql .= "and po.wearhouse_id=3";

        $sql .= " and sl.debit != '0' and sl.credit != '0'
            GROUP BY s.supplier_id
            order by s.name asc";

        $customers = DB::select('select * from suppliers');

        foreach ($customers as $key => $c) {
            $opening_balance_info = array();


            if (empty($wearhouse)) {

                $opening_sql = "select sum(sll.debit-sll.credit) as opening_balance from supplier_ledger as sll where sll.supplier_id =" . $c->supplier_id . " and sll.date <'" . $request->start_date . "'";
                $opening_balance_info = DB::select($opening_sql);

                $customers[$key]->opening_balance = $opening_balance_info[0]->opening_balance;


                $debit_credit_balance_info = array();

                $dbcrb_sql = "select sum(sll.debit) as debit,sum(sll.credit) as credit,sum(sll.balance) as balance from supplier_ledger as sll where sll.supplier_id =" . $c->supplier_id . " and (sll.date >='$start_date' and sll.date <='$end_date') ";
                $debit_credit_balance_info = DB::select($dbcrb_sql);
            } else {

                $opening_sql = "select sum(sll.debit-sll.credit) as opening_balance from supplier_ledger as sll 
                    left join purchases po on sll.purchase_id=po.id
                    where po.wearhouse_id=" . $wearhouse . " and sll.supplier_id =" . $c->supplier_id . " and sll.date <'" . $request->start_date . "'";
                $opening_balance_info = DB::select($opening_sql);

                $customers[$key]->opening_balance = $opening_balance_info[0]->opening_balance;


                $debit_credit_balance_info = array();

                $dbcrb_sql = "select sum(sll.debit) as debit,sum(sll.credit) as credit,
                    sum(sll.balance) as balance from supplier_ledger as sll 
                    left join purchases po on sll.purchase_id=po.id
                    where po.wearhouse_id=" . $wearhouse . " and sll.supplier_id =" . $c->supplier_id . " and (sll.date >='$start_date' and sll.date <='$end_date') ";
                $debit_credit_balance_info = DB::select($dbcrb_sql);
            }

            $customers[$key]->debit = $debit_credit_balance_info[0]->debit;
            $customers[$key]->credit = $debit_credit_balance_info[0]->credit;
            $customers[$key]->balance = $debit_credit_balance_info[0]->balance;
        }

        return view('backend.reports.supplier_ledger_new', compact('customers', 'start_date', 'end_date', 'wearhouse', 'month', 'year', 'month_year'));
    }

    public function   supplier_ledger_for_purchase_executive(Request $request)
    {
        $sort_by = null;
        $status = null;
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        if (empty($request->start_date))
            $request->start_date = $start_date;
        if (empty($request->end_date))
            $request->end_date = $end_date;
        $cust = array();
        $orders = array();


        $sql = "SELECT
        s.supplier_id,s.name,sum(sl.debit) as debit,sum(sl.credit) as credit,sum(sl.balance) as balance,
        (select sum(sll.debit-sll.credit) from supplier_ledger as sll where s.supplier_id = sll.supplier_id and sll.date <'" . $request->start_date . "') as opening_balance
        FROM
        suppliers s
        LEFT JOIN supplier_ledger sl ON s.supplier_id = sl.supplier_id";

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d', strtotime($request->end_date));
            $sql .= "	where sl.date between '" . $start_date . "' and '" . $end_date . "' or sl.date is null ";
        } else {
            $sql .= "	where sl.date between '" . $start_date . "' and '" . $end_date . "' or sl.date is null ";
        }
        $sql .= " and sl.debit != '0' and sl.credit != '0'
        GROUP BY s.supplier_id
        order by s.name asc";
        $customers = DB::select($sql);
        return view('backend.staff_panel.purchase_executive.supplier_ledger_for_purchase_executive', compact('customers', 'start_date', 'end_date'));
    }

    public function   supplier_ledger_for_purchase_manager(Request $request)
    {
        $wearhouse = $request->warehouse;
        $sort_by = null;
        $status = null;
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        if (empty($request->start_date))
            $request->start_date = $start_date;
        if (empty($request->end_date))
            $request->end_date = $end_date;
        $cust = array();
        $orders = array();


        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d', strtotime($request->end_date));
        }

        $customers = DB::select('select * from suppliers');

        foreach ($customers as $key => $c) {
            $opening_balance_info = array();


            if (empty($wearhouse)) {

                $opening_sql = "select sum(sll.debit-sll.credit) as opening_balance from supplier_ledger as sll where sll.supplier_id =" . $c->supplier_id . " and sll.date <'" . $request->start_date . "'";
                $opening_balance_info = DB::select($opening_sql);

                $customers[$key]->opening_balance = $opening_balance_info[0]->opening_balance;


                $debit_credit_balance_info = array();

                $dbcrb_sql = "select sum(sll.debit) as debit,sum(sll.credit) as credit,sum(sll.balance) as balance from supplier_ledger as sll where sll.supplier_id =" . $c->supplier_id . " and (sll.date >='$start_date' and sll.date <='$end_date') ";
                $debit_credit_balance_info = DB::select($dbcrb_sql);
            } else {

                $opening_sql = "select sum(sll.debit-sll.credit) as opening_balance from supplier_ledger as sll 
                    left join purchases po on sll.purchase_id=po.id
                    where po.wearhouse_id=" . $wearhouse . " and sll.supplier_id =" . $c->supplier_id . " and sll.date <'" . $request->start_date . "'";
                $opening_balance_info = DB::select($opening_sql);

                $customers[$key]->opening_balance = $opening_balance_info[0]->opening_balance;


                $debit_credit_balance_info = array();

                $dbcrb_sql = "select sum(sll.debit) as debit,sum(sll.credit) as credit,
                    sum(sll.balance) as balance from supplier_ledger as sll 
                    left join purchases po on sll.purchase_id=po.id
                    where po.wearhouse_id=" . $wearhouse . " and sll.supplier_id =" . $c->supplier_id . " and (sll.date >='$start_date' and sll.date <='$end_date') ";
                $debit_credit_balance_info = DB::select($dbcrb_sql);
            }


            $customers[$key]->debit = $debit_credit_balance_info[0]->debit;
            $customers[$key]->credit = $debit_credit_balance_info[0]->credit;
            $customers[$key]->balance = $debit_credit_balance_info[0]->balance;

           // dd($customers, $start_date, $end_date);
        }
        return view('backend.staff_panel.purchase_manager.supplier_ledger', compact('customers', 'start_date', 'end_date', 'wearhouse'));
    }

    public function supplier_ledger_details(Request $request)
    {
        $cust_id = $request->cust_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $cust = Supplier::where('supplier_id', $cust_id)->first();
        $sql = "SELECT
        s.name,p.purchase_no,sl.purchase_id,sl.date,sl.type,sl.descriptions,s.supplier_id,sl.debit as debit,sl.credit as credit,sl.balance as balance
        FROM
        suppliers s
        LEFT JOIN supplier_ledger sl ON s.supplier_id = sl.supplier_id
        LEFT JOIN purchases p ON sl.purchase_id = p.id";

        $sql .= "	where s.supplier_id=$cust_id and sl.date between '" . $start_date . "' and '" . $end_date . "'";

        $sql .= " order by sl.date,sl.id asc";
        $customers = DB::select($sql);


        $sql = "SELECT sum(sl.debit-sl.credit) as opening_balance
        FROM
            suppliers s
            LEFT JOIN supplier_ledger sl ON s.supplier_id = sl.supplier_id";
        $sql .= "	where s.supplier_id=$cust_id and sl.date < '" . $start_date . "'";

        $opening = DB::select($sql);

        if (Auth::user()->user_type == 'admin') {
            return view('backend.reports.supplier_ledger', compact('customers', 'cust', 'start_date', 'end_date', 'opening'));
        } else {
            return view('backend.staff_panel.purchase_executive.supplier_ledger', compact('customers', 'cust', 'start_date', 'end_date', 'opening'));
        }
    }

    public function credit_report(Request $request)
    {
        $sort_by = null;
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $cust = array();
        $orders = array();

        $customers = User::join('customers', 'customers.user_id', '=', 'users.id')->leftJoin('addresses', 'users.id', '=', 'addresses.user_id')->where('user_type', 'customer')->where('customers.credit_limit', '>', '0');

        if ($request->has('customer_id') && !empty($request->customer_id)) {
            $sort_by = $request->customer_id;
            $customers = $customers->where('users.id', $sort_by);
            $cust = User::where('id', $sort_by)->first();
       
        }
        $is_credit = $request->is_credit;
        if ($is_credit == 'on') {
            $customers = $customers->where('users.balance', '<', 0);
        }
        $customers = $customers->select('users.*', 'customers.customer_id', 'customers.credit_limit', 'addresses.address')->groupBy('users.id')->get();

        return view('backend.reports.credit_report', compact('customers', 'sort_by', 'cust', 'start_date', 'end_date', 'is_credit'));
    }

    public function order_duration_time(Request $request)
    {
        $sort_by = null;
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $cust = array();
        $orders = array();
        $sql = "
        SELECT 
              u.*,(select address from addresses where user_id=u.id order by id asc limit 1) as address,cs.customer_id,count(*) as total_sale,CONCAT(
                   FLOOR(TIME_FORMAT(SEC_TO_TIME((avg(" . time() . "-a.date)/count(*))), '%H') / 24), 'd ',
                   MOD(TIME_FORMAT(SEC_TO_TIME((avg(" . time() . "-a.date)/count(*))), '%H'), 24), 'h:',
                   TIME_FORMAT(SEC_TO_TIME((avg(" . time() . "-a.date)/count(*))), '%im:%ss')
               ) as diff,FLOOR((avg(" . time() . "-a.date)/count(*))) as diff1
       FROM
            orders a 
            join order_details as od on a.id=od.order_id
            join users as u on u.id=a.user_id left join customers as cs on a.user_id=cs.user_id
             where 
             od.delivery_status in('delivered')";
        if ($request->has('customer_id') && !empty($request->customer_id)) {
            $sort_by = $request->customer_id;
            $cust = User::where('id', $sort_by)->first();
            $sql .= " and a.user_id =" . $sort_by;
        }
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $sql .= " and a.date between '" . strtotime($start_date) . "' and '" . strtotime($end_date) . "'";
        }
        $sql .= " group by a.user_id order by diff1 asc";
        $customers = \DB::select($sql);

        return view('backend.reports.order_duration_time', compact('customers', 'sort_by', 'cust', 'start_date', 'end_date'));
    }


    public function product_wise_sales_report_(Request $request)
    {
        // Retrieve request parameters
        
        $warehouse = $request->warehouse;
        $product_id = $request->product_id;
        $sort_by = $request->category_id ?? null;
        $pro_sort_by = $request->product_id ?? null;
        $start_date = $request->start_date ?? date('Y-m-01');
        $end_date = $request->end_date ?? date('Y-m-t');

        // Build base query
        $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
            ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('staff', 'products.user_id', '=', 'staff.user_id')
            ->leftJoin('roles', 'staff.id', '=', 'roles.id')
            ->where('num_of_sale', '>', 0)
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'products.purchase_price',
                'categories.name as category_name',
                'roles.name as role_name',
                'orders.code',
                DB::raw('sum(order_details.price) AS price'),
                DB::raw('sum(order_details.quantity) AS quantity'),
                DB::raw('count(product_id) AS num_of_sale')
                // DB::raw('sum(order_details.price) AS total_order_price'),
                // DB::raw('sum(quantity) AS total_quantity'),
                // DB::raw('count(product_id) AS num_of_sale')
            )
            ->groupBy('products.id')
            ->orderBy('num_of_sale', 'desc');

        if ($sort_by) {
            $products = $products->where('category_id', $sort_by);
        }

        if ($pro_sort_by) {
            $products = $products->where('products.id', $pro_sort_by);
        }

        if ($request->user_id) {
            $products = $products->where('products.user_id', $request->user_id);
        }

        if ($warehouse) {
            $products = $products->where('orders.warehouse', $warehouse);
        }

        $products = $products->where('order_details.delivery_status', 'delivered')
            ->whereBetween('orders.delivered_date', [$start_date, $end_date])
            ->get();

        // Preload related data to reduce queries inside the loop
        $openingStocks = OpeningStock::whereBetween('created_at', [$start_date, $end_date])
            ->where('wearhouse_id',$warehouse)
            ->get()->groupBy('product_id');



        $purchases = PurchaseDetail::whereHas('purchase', function($query) use ($start_date, $end_date) {
                $query->where('status', 2)->whereBetween('date', [$start_date, $end_date]);
            })
            ->where('wearhouse_id',$warehouse)
            ->orderBy('created_at', 'asc')
            ->get()->groupBy('product_id');

        $transfersIn = Transfer::where('status', 'Approved')
            ->whereBetween('date', [$start_date, $end_date])
            ->where('to_wearhouse_id',$warehouse)
            ->get()->groupBy('product_id');

        $transfersOut = Transfer::where('status', 'Approved')
            ->whereBetween('date', [$start_date, $end_date])
            ->where('from_wearhouse_id',$warehouse)
            ->get()->groupBy('product_id');

        $damages = Damage::where('status', 'Approved')
            ->whereBetween('date', [$start_date, $end_date])
            ->where('wearhouse_id',$warehouse)
            ->get()->groupBy('product_id');

        $refunds = RefundRequest::where('refund_status', 5)
            ->select('refund_requests.*','orders.warehouse','order_details.product_id as product_id')
            ->leftjoin('orders','orders.id','refund_requests.order_id')
            ->leftjoin('order_details','order_details.id','refund_requests.order_detail_id')
            ->whereBetween('refund_requests.created_at', [$start_date, $end_date])
            ->where('orders.warehouse',$warehouse)
            ->get()->groupBy('order_details.product_id');

        foreach ($products as $product) {
            $product_id = $product->product_id;

            // Calculate the opening stock
            $openingStockQty = $openingStockAmount = 0;
            if (isset($openingStocks[$product_id])) {
                foreach ($openingStocks[$product_id] as $openStock) {
                    $openingStockQty += $openStock->qty;
                    $openingStockAmount += $openStock->qty * $openStock->price;
                }
            }

            $product->opening_stock_qty = $openingStockQty;
            $product->opening_stock_amount = $openingStockAmount;

            // FIFO Calculation for Purchases
            $purchaseQty = $purchaseAmount = 0;
            $remainingQtyToAllocate = $product->quantity;
            if (isset($purchases[$product_id])) {
                foreach ($purchases[$product_id] as $purchase) {
                    if ($remainingQtyToAllocate <= 0) break;

                    $allocateQty = min($purchase->qty, $remainingQtyToAllocate);
                    $purchaseAmount += $allocateQty * $purchase->price;
                    $purchaseQty += $allocateQty;
                    $remainingQtyToAllocate -= $allocateQty;
                }
            }

            $product->purchase_qty = $purchaseQty;
            $product->purchase_amount = $purchaseAmount;

            // Calculate transfer in
            $transferInQty = $transferInAmount = 0;
            if (isset($transfersIn[$product_id])) {
                foreach ($transfersIn[$product_id] as $transferIn) {
                    $transferInQty += $transferIn->qty;
                    $transferInAmount += $transferIn->qty * $transferIn->price;
                }
            }

            $product->transfer_in_qty = $transferInQty;
            $product->transfer_in_amount = $transferInAmount;

            // Calculate transfer out
            $transferOutQty = $transferOutAmount = 0;
            if (isset($transfersOut[$product_id])) {
                foreach ($transfersOut[$product_id] as $transferOut) {
                    $transferOutQty += $transferOut->qty;
                    $transferOutAmount += $transferOut->qty * $transferOut->price;
                }
            }

            $product->transfer_out_qty = $transferOutQty;
            $product->transfer_out_amount = $transferOutAmount;

            // Calculate damages
            $damageQty = $damageAmount = 0;
            if (isset($damages[$product_id])) {
                foreach ($damages[$product_id] as $damage) {
                    $damageQty += $damage->qty;
                    $damageAmount += $damage->total_amount;
                }
            }

            $product->damage_qty = $damageQty;
            $product->damage_amount = $damageAmount;

            // Calculate damages
            $refundQty = $refundAmount = 0;
            if (isset($refunds[$product_id])) {
                foreach ($refunds[$product_id] as $refund) {
                    $refundQty += $refund->return_qty;
                    $refundAmount += $refund->return_amount;
                }
            }

            $product->refund_qty = $refundQty;
            $product->refund_amount = $refundAmount;

            // Calculate the closing stock
            $closingStockQty = $openingStockQty + $purchaseQty + $transferInQty - $transferOutQty - $product->quantity -$product->damage_qty + $product->refund_qty;
            if ($openingStockQty > 0) {
                $closingStockAmount = $closingStockQty * ($openingStockAmount / $openingStockQty);
            } else {
                $closingStockAmount = 0;
            }
            

            $product->closing_stock_qty = $closingStockQty;
            $product->closing_stock_amount = $closingStockAmount;

            // Calculate profit or loss
            $product->profit_loss =  $product->price - ($openingStockAmount + $purchaseAmount + $transferInAmount -$damageAmount -$refundAmount - $transferOutAmount - $closingStockAmount);
            
        }

        if ($request->input('source') === 'excel') {
            return Excel::download(new ProductWiseSalesReport(), 'product_wise_sales_report.xlsx');
        }

        return view('backend.reports.in_house_sale_report', compact('products', 'sort_by', 'pro_sort_by', 'start_date', 'end_date', 'warehouse'));
    }

    public function product_wise_sales_report(Request $request)
    {
        
        $warehouse = $request->warehouse;
        $product_id = $request->product_id;
        $sort_by = $request->category_id ?? null;
        $pro_sort_by = $request->product_id ?? null;
        $start_date = $request->start_date ?? date('Y-m-01');
        $end_date = $request->end_date ?? date('Y-m-t');

        if (empty($warehouse)) {
            $warehouse = [1, 2, 3]; 
        } else {
            $warehouse = (array) $request->warehouse;
        }
        

        // Build base query
        $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
            ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('staff', 'products.user_id', '=', 'staff.user_id')
            ->leftJoin('roles', 'staff.id', '=', 'roles.id')
            ->where('num_of_sale', '>', 0)
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'products.purchase_price',
                'categories.name as category_name',
                'roles.name as role_name',
                'orders.code',
                DB::raw('sum(order_details.price) AS price'),
                DB::raw('sum(order_details.quantity) AS quantity'),
                DB::raw('count(product_id) AS num_of_sale')
                // DB::raw('sum(order_details.price) AS total_order_price'),
                // DB::raw('sum(quantity) AS total_quantity'),
                // DB::raw('count(product_id) AS num_of_sale')
            )
            ->groupBy('products.id')
            ->orderBy('num_of_sale', 'desc');

        if ($sort_by) {
            $products = $products->where('category_id', $sort_by);
        }

        if ($pro_sort_by) {
            $products = $products->where('products.id', $pro_sort_by);
        }

        if ($request->user_id) {
            $products = $products->where('products.user_id', $request->user_id);
        }

        if ($warehouse) {
            $products = $products->whereIn('orders.warehouse', $warehouse);
        }

        $products = $products->where('order_details.delivery_status', 'delivered')
            ->whereBetween('orders.delivered_date', [$start_date, $end_date])
            ->get();

        $openingStocks = OpeningStock::whereBetween('created_at', [$start_date, $end_date])
            ->whereIn('wearhouse_id',$warehouse)
            ->get()->groupBy('product_id');



        $purchases = PurchaseDetail::whereHas('purchase', function($query) use ($start_date, $end_date) {
                $query->where('status', 2)->whereBetween('date', [$start_date, $end_date]);
            })
            ->whereIn('wearhouse_id',$warehouse)
            ->orderBy('created_at', 'asc')
            ->get()->groupBy('product_id');

        $transfersIn = Transfer::where('status', 'Approved')
            ->whereBetween('date', [$start_date, $end_date])
            ->whereIn('to_wearhouse_id',$warehouse)
            ->get()->groupBy('product_id');

        $transfersOut = Transfer::where('status', 'Approved')
            ->whereBetween('date', [$start_date, $end_date])
            ->whereIn('from_wearhouse_id',$warehouse)
            ->get()->groupBy('product_id');

        $damages = Damage::where('status', 'Approved')
            ->whereBetween('date', [$start_date, $end_date])
            ->whereIn('wearhouse_id',$warehouse)
            ->get()->groupBy('product_id');

        $refunds = RefundRequest::where('refund_status', 5)
            ->select('refund_requests.*','orders.warehouse','order_details.product_id as product_id')
            ->leftjoin('orders','orders.id','refund_requests.order_id')
            ->leftjoin('order_details','order_details.id','refund_requests.order_detail_id')
            ->whereBetween('refund_requests.created_at', [$start_date, $end_date])
            ->whereIn('orders.warehouse',$warehouse)
            ->get()->groupBy('order_details.product_id');

        foreach ($products as $product) {
            $product_id = $product->product_id;

            $openingStockQty = $openingStockAmount = 0;
            if (isset($openingStocks[$product_id])) {
                foreach ($openingStocks[$product_id] as $openStock) {
                    $openingStockQty += $openStock->qty;
                    $openingStockAmount += $openStock->qty * $openStock->price;
                }
            }

            $product->opening_stock_qty = $openingStockQty;
            $product->opening_stock_amount = $openingStockAmount;

            $purchaseQty = $purchaseAmount = 0;
            $remainingQtyToAllocate = $product->quantity;
            if (isset($purchases[$product_id])) {
                foreach ($purchases[$product_id] as $purchase) {
                    if ($remainingQtyToAllocate <= 0) break;

                    $allocateQty = min($purchase->qty, $remainingQtyToAllocate);
                    $purchaseAmount += $allocateQty * $purchase->price;
                    $purchaseQty += $allocateQty;
                    $remainingQtyToAllocate -= $allocateQty;
                }
            }

            $product->purchase_qty = $purchaseQty;
            $product->purchase_amount = $purchaseAmount;

            $transferInQty = $transferInAmount = 0;
            if (isset($transfersIn[$product_id])) {
                foreach ($transfersIn[$product_id] as $transferIn) {
                    $transferInQty += $transferIn->qty;
                    $transferInAmount += $transferIn->qty * $transferIn->price;
                }
            }

            $product->transfer_in_qty = $transferInQty;
            $product->transfer_in_amount = $transferInAmount;

            $transferOutQty = $transferOutAmount = 0;
            if (isset($transfersOut[$product_id])) {
                foreach ($transfersOut[$product_id] as $transferOut) {
                    $transferOutQty += $transferOut->qty;
                    $transferOutAmount += $transferOut->qty * $transferOut->price;
                }
            }

            $product->transfer_out_qty = $transferOutQty;
            $product->transfer_out_amount = $transferOutAmount;

            $damageQty = $damageAmount = 0;
            if (isset($damages[$product_id])) {
                foreach ($damages[$product_id] as $damage) {
                    $damageQty += $damage->qty;
                    $damageAmount += $damage->total_amount;
                }
            }

            $product->damage_qty = $damageQty;
            $product->damage_amount = $damageAmount;

            $refundQty = $refundAmount = 0;
            if (isset($refunds[$product_id])) {
                foreach ($refunds[$product_id] as $refund) {
                    $refundQty += $refund->return_qty;
                    $refundAmount += $refund->return_amount;
                }
            }

            $product->refund_qty = $refundQty;
            $product->refund_amount = $refundAmount;

            $closingStockQty = $openingStockQty + $purchaseQty + $transferInQty - $transferOutQty - $product->quantity -$product->damage_qty + $product->refund_qty;
            if ($openingStockQty > 0) {
                $closingStockAmount = $closingStockQty * ($openingStockAmount / $openingStockQty);
            } else {
                $closingStockAmount = 0;
            }
            

            $product->closing_stock_qty = $closingStockQty;
            $product->closing_stock_amount = $closingStockAmount;
            $product->profit_loss =  $product->price - ($openingStockAmount + $purchaseAmount + $transferInAmount -$damageAmount -$refundAmount - $transferOutAmount - $closingStockAmount);
            
        }

        $warehouse = $request->warehouse;

        if ($request->input('source') === 'excel') {
            return Excel::download(new ProductWiseSalesReport(), 'product_wise_sales_report.xlsx');
        }
        // return response()->json([
        //     'data' => $products,
        //     'message' => 'Product wise sales report generated successfully.',
        // ]);
        return view('backend.reports.in_house_sale_report', compact('products', 'sort_by', 'pro_sort_by', 'start_date', 'end_date', 'warehouse'));
    }



    public function number_of_invoice(Request $request)
    {
        $warehouse = $request->warehouse;
        $product_id = $request->product_id;
        $sort_by = $request->category_id ?? null;
        $pro_sort_by = $request->product_id ?? null;
        $start_date = $request->start_date ?? date('Y-m-01');
        $end_date = $request->end_date ?? date('Y-m-t');

        $order_details = OrderDetail::select(
                'order_details.*', 
                'products.name as product_name', 
                'orders.id as order_id', 
                'orders.delivered_date', 
                DB::raw('SUM(order_details.quantity) as total_quantity'),
                DB::raw('SUM(order_details.price) as total_price'),
                DB::raw('COUNT(order_details.id) as num_of_sales')
            )
            ->leftJoin('products', 'products.id', '=', 'order_details.product_id')
            ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->where('order_details.product_id', $product_id)
            ->whereBetween('order_details.created_at', [$start_date, $end_date])
            ->groupBy('orders.id') 
            ->get();
            $warehouse = (array) $request->warehouse; 
            
        $product_name = optional($order_details->first())->product_name ?? 'N/A';
    
        $total_quantity = $order_details->sum('total_quantity');
        $total_price = $order_details->sum('total_price');
        $num_of_sales = $order_details->count(); 
    
        return view('backend.reports.number_of_invoice', compact('order_details', 'product_name', 'total_quantity', 'total_price', 'num_of_sales'));
    }


    public function product_fifo_report(Request $request)
    {
        $warehouse = $request->warehouse;
        $product_id = $request->product_id;
        $sort_by = $request->category_id ?? null;
        $pro_sort_by = $request->product_id ?? null;
        $start_date = $request->start_date ?? date('Y-m-01');
        $end_date = $request->end_date ?? date('Y-m-t');
    
        if (empty($warehouse)) {
            $warehouse = [1, 2, 3]; 
        } else {
            $warehouse = (array) $request->warehouse;
        }
    
        // Base query for products
        $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
            ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('staff', 'products.user_id', '=', 'staff.user_id')
            ->leftJoin('roles', 'staff.id', '=', 'roles.id')
            ->where('num_of_sale', '>', 0)
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'products.purchase_price',
                'categories.name as category_name',
                'roles.name as role_name',
                'orders.code',
                DB::raw('SUM(order_details.price) AS price'),
                DB::raw('SUM(order_details.quantity) AS quantity'),
                DB::raw('COUNT(product_id) AS num_of_sale')
            )
            ->groupBy('products.id')
            ->orderBy('num_of_sale', 'desc');
    
        if ($sort_by) {
            $products = $products->where('category_id', $sort_by);
        }
    
        if ($pro_sort_by) {
            $products = $products->where('products.id', $pro_sort_by);
        }
    
        if ($request->user_id) {
            $products = $products->where('products.user_id', $request->user_id);
        }
    
        if ($warehouse) {
            $products = $products->whereIn('orders.warehouse', $warehouse);
        }
    
        $products = $products->where('order_details.delivery_status', 'delivered')
            ->whereBetween('orders.created_at', [$start_date, $end_date])
            ->get();
    
        // Fetch additional data
        $openingStocks = OpeningStock::whereBetween('created_at', [$start_date, $end_date])
            ->whereIn('wearhouse_id', $warehouse)
            ->get()->groupBy('product_id');
    
        $purchases = PurchaseDetail::whereHas('purchase', function($query) use ($start_date, $end_date) {
                $query->where('status', 2)->whereBetween('date', [$start_date, $end_date]);
            })
            ->whereIn('wearhouse_id', $warehouse)
            ->orderBy('created_at', 'asc')
            ->get()->groupBy('product_id');
    
        $transfersIn = Transfer::where('status', 'Approved')
            ->whereBetween('date', [$start_date, $end_date])
            ->whereIn('to_wearhouse_id', $warehouse)
            ->get()->groupBy('product_id');
    
        $transfersOut = Transfer::where('status', 'Approved')
            ->whereBetween('date', [$start_date, $end_date])
            ->whereIn('from_wearhouse_id', $warehouse)
            ->get()->groupBy('product_id');
    
        $damages = Damage::where('status', 'Approved')
            ->whereBetween('date', [$start_date, $end_date])
            ->whereIn('wearhouse_id', $warehouse)
            ->get()->groupBy('product_id');
    
        $refunds = RefundRequest::where('refund_status', 5)
            ->select('refund_requests.*', 'orders.warehouse', 'order_details.product_id as product_id')
            ->leftJoin('orders', 'orders.id', 'refund_requests.order_id')
            ->leftJoin('order_details', 'order_details.id', 'refund_requests.order_detail_id')
            ->whereBetween('refund_requests.created_at', [$start_date, $end_date])
            ->whereIn('orders.warehouse', $warehouse)
            ->get()->groupBy('product_id');
    
        foreach ($products as $product) {
            $product_id = $product->product_id;
    
            // Calculate Opening Stock
            $openingStockQty = $openingStockAmount = 0;
            if (isset($openingStocks[$product_id])) {
                foreach ($openingStocks[$product_id] as $openStock) {
                    $openingStockQty += $openStock->qty;
                    $openingStockAmount += $openStock->qty * $openStock->price;
                }
            }
            $product->opening_stock_qty = $openingStockQty;
            $product->opening_stock_amount = $openingStockAmount;
    
            // FIFO for Purchases
            $purchaseQty = $purchaseAmount = 0;
            $remainingQtyToAllocate = $product->quantity;
            if (isset($purchases[$product_id])) {
                foreach ($purchases[$product_id] as $purchase) {
                    if ($remainingQtyToAllocate <= 0) break;
                    $allocateQty = min($purchase->qty, $remainingQtyToAllocate);
                    $purchaseAmount += $allocateQty * $purchase->price;
                    $purchaseQty += $allocateQty;
                    $remainingQtyToAllocate -= $allocateQty;
                }
            }
            $product->purchase_qty = $purchaseQty;
            $product->purchase_amount = $purchaseAmount;
    
            // Transfers In and Out
            $transferInQty = $transferInAmount = $transferOutQty = $transferOutAmount = 0;
            if (isset($transfersIn[$product_id])) {
                foreach ($transfersIn[$product_id] as $transferIn) {
                    $transferInQty += $transferIn->qty;
                    $transferInAmount += $transferIn->qty * $transferIn->price;
                }
            }
            if (isset($transfersOut[$product_id])) {
                foreach ($transfersOut[$product_id] as $transferOut) {
                    $transferOutQty += $transferOut->qty;
                    $transferOutAmount += $transferOut->qty * $transferOut->price;
                }
            }
            $product->transfer_in_qty = $transferInQty;
            $product->transfer_in_amount = $transferInAmount;
            $product->transfer_out_qty = $transferOutQty;
            $product->transfer_out_amount = $transferOutAmount;
    
            // Damages
            $damageQty = $damageAmount = 0;
            if (isset($damages[$product_id])) {
                foreach ($damages[$product_id] as $damage) {
                    $damageQty += $damage->qty;
                    $damageAmount += $damage->total_amount;
                }
            }
            $product->damage_qty = $damageQty;
            $product->damage_amount = $damageAmount;
    
            // Refunds
            $refundQty = $refundAmount = 0;
            if (isset($refunds[$product_id])) {
                foreach ($refunds[$product_id] as $refund) {
                    $refundQty += $refund->return_qty;
                    $refundAmount += $refund->return_amount;
                }
            }
            $product->refund_qty = $refundQty;
            $product->refund_amount = $refundAmount;
    
            // Calculate Closing Stock
            $closingStockQty = $openingStockQty + $purchaseQty + $transferInQty - $transferOutQty - $product->quantity - $product->damage_qty + $product->refund_qty;
            $closingStockAmount = $openingStockQty > 0 ? $closingStockQty * ($openingStockAmount / $openingStockQty) : 0;
            $product->closing_stock_qty = $closingStockQty;
            $product->closing_stock_amount = $closingStockAmount;
    
            // Calculate Profit/Loss
            $product->profit_loss = $product->price - ($openingStockAmount + $purchaseAmount + $transferInAmount - $damageAmount - $refundAmount - $transferOutAmount - $closingStockAmount);
        }
         // return response()->json([
        //     'data' => $products,
        //     'message' => 'Product wise sales report generated successfully.',
        // ]);
    
        return view('backend.reports.product_fifo_report', compact('products', 'sort_by', 'pro_sort_by', 'start_date', 'end_date', 'warehouse'));
    }
    
    


    public function product_fifo_detail_report(Request $request)
    {
        // Set the date range and other parameters
        $start_date = !empty($request->start_date) ? $request->start_date : date('Y-m-01');
        $end_date = !empty($request->end_date) ? $request->end_date : date('Y-m-t');
        $product_id = $request->product_id;
        $warehouse = $request->warehouse;
    
        if (empty($warehouse)) {
            $warehouse = [1, 2, 3]; 
        } else {
            $warehouse = (array) $request->warehouse;
        }
    
        // Base query for products
        $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
            ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('staff', 'products.user_id', '=', 'staff.user_id')
            ->leftJoin('roles', 'staff.id', '=', 'roles.id')
            ->where('num_of_sale', '>', 0)
            ->where('products.id', $product_id)
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'products.purchase_price',
                'categories.name as category_name',
                'roles.name as role_name',
                'orders.code',
                DB::raw('SUM(order_details.price) AS price'),
                DB::raw('SUM(order_details.quantity) AS quantity'),
                // DB::raw('COUNT(product_id) AS num_of_sale')
            )
            ->groupBy('products.id');
            // ->orderBy('num_of_sale', 'desc');

            $products = $products->get();

            // $product_name = optional($products->first())->product_name ?? 'N/A';
        // Fetch sales
        // $sales = $products->where('order_details.delivery_status', 'delivered')
        //     ->whereBetween('orders.delivered_date', [$start_date, $end_date])
        //     ->get();
        $sales = OrderDetail::where('product_id', $product_id)
                    ->where('delivery_status', 'delivered') 
                    ->whereBetween('created_at', [$start_date, $end_date])
                    ->orderBy('created_at', 'asc')
                    ->get();
    
        // Fetch additional data
        $openingStocks = OpeningStock::whereBetween('created_at', [$start_date, $end_date])
                        ->whereIn('wearhouse_id', $warehouse)
                        ->where('product_id', $product_id)
                        ->orderBy('created_at', 'asc')
                        ->get()->groupBy('product_id');
    
        $purchases = PurchaseDetail::whereHas('purchase', function($query) use ($start_date, $end_date) {
                    $query->where('status', 2)->whereBetween('date', [$start_date, $end_date]);
                        })
                        ->whereIn('wearhouse_id', $warehouse)
                        ->where('product_id', $product_id)
                        ->orderBy('created_at', 'asc')
                        ->get()->groupBy('product_id');
    
        $transfersIn = Transfer::where('status', 'Approved')
                        ->whereBetween('date', [$start_date, $end_date])
                        ->whereIn('to_wearhouse_id', $warehouse)
                        ->where('product_id', $product_id)
                        ->orderBy('date', 'asc')
                        ->get()->groupBy('product_id');
    
        $transfersOut = Transfer::where('status', 'Approved')
                        ->whereBetween('date', [$start_date, $end_date])
                        ->whereIn('from_wearhouse_id', $warehouse)
                        ->where('product_id', $product_id)
                        ->orderBy('date', 'asc')
                        ->get()->groupBy('product_id');
    
        $damages = Damage::where('status', 'Approved')
                    ->whereBetween('date', [$start_date, $end_date])
                    ->whereIn('wearhouse_id', $warehouse)
                    ->where('product_id', $product_id)
                    ->orderBy('date', 'asc')
                    ->get()->groupBy('product_id');
    
        $refunds = RefundRequest::where('refund_status', 5)
                    ->select('refund_requests.*', 'orders.warehouse', 'order_details.product_id as product_id')
                    ->leftJoin('orders', 'orders.id', 'refund_requests.order_id')
                    ->leftJoin('order_details', 'order_details.id', 'refund_requests.order_detail_id')
                    ->whereBetween('refund_requests.created_at', [$start_date, $end_date])
                    ->whereIn('orders.warehouse', $warehouse)
                    ->where('product_id', $product_id)
                    ->orderBy('created_at', 'asc')
                    ->get()->groupBy('product_id');
    
        // Initialize arrays for FIFO details
        $fifoDetails = [];
        $total_sales = 0;
        $total_purchases = 0;
    
        // Process Sales
        foreach ($sales as $sale) {
            if (isset($sale->created_at)) { 
                $fifoDetails[] = [
                    'date' => $sale->created_at->format('Y-m-d'),
                    'transaction' => 'Sale',
                    'units' => $sale->quantity,
                    'cost' => 0,
                    'price' => $sale->price / $sale->quantity,
                    'total_costs' => 0,
                    'total_sales' =>  $sale->price,
                ];
                $total_sales +=  $sale->price;
            }
        }
    
        // Process Purchases
        foreach ($purchases as $purchase) {
            foreach ($purchase as $item) { 
                if (isset($item->created_at)) { 
                    $fifoDetails[] = [
                        'date' => $item->created_at->format('Y-m-d'),
                        'transaction' => 'Purchase',
                        'units' => $item->qty,
                        'cost' => $item->price,
                        'price' => 0,
                        'total_costs' => $item->qty * $item->price,
                        'total_sales' => 0,
                    ];
                    $total_purchases += $item->qty * $item->price;
                }
            }
        }
    
        // Process Transfers In
        foreach ($transfersIn as $transfer) {
            foreach ($transfer as $item) { 
                if (isset($item->created_at)) { 
                    $fifoDetails[] = [
                        'date' => $item->created_at->format('Y-m-d'),
                        'transaction' => 'Transfer In',
                        'units' => $item->qty,
                        'cost' => $item->unit_price,
                        'price' => 0,
                        'total_costs' => $item->qty * $item->unit_price,
                        'total_sales' => 0,
                    ];
                }
            }
        }
    
        // Process Transfers Out
        foreach ($transfersOut as $transfer) {
            foreach ($transfer as $item) { 
                if (isset($item->created_at)) { 
                    $fifoDetails[] = [
                        'date' => $item->created_at->format('Y-m-d'),
                        'transaction' => 'Transfer Out',
                        'units' => $item->qty,
                        'cost' => 0,
                        'price' => $item->unit_price,
                        'total_costs' => 0,
                        'total_sales' => -($item->qty * $item->unit_price),
                    ];
                }
            }
        }
    
        // Process Damages
        foreach ($damages as $damage) {
            foreach ($damage as $item) { 
                if (isset($item->created_at)) { 
                    $fifoDetails[] = [
                        'date' => $item->created_at->format('Y-m-d'),
                        'transaction' => 'Damage',
                        'units' => $item->qty,
                        'cost' => $item->total_amount / $item->qty,
                        'price' => 0,
                        'total_costs' => $item->total_amount,
                        'total_sales' => 0,
                    ];
                }
            }
        }
    
       
        // Process Opening Balance
        foreach ($openingStocks as $openingStock) {
            foreach ($openingStock as $item) { 
                if (isset($item->created_at)) { 
                    $fifoDetails[] = [
                        'date' => $item->created_at->format('Y-m-d'),
                        'transaction' => 'Beginning Inventory',
                        'units' => $item->qty,
                        'cost' => $item->price,
                        'price' => 0,
                        'total_costs' => $item->amount,
                        'total_sales' => 0,
                    ];
                }
            }
        }
    
        // Process Refunds
        foreach ($refunds as $refund) {
            foreach ($refund as $item) { 
                if (isset($item->created_at)) { 
                    $fifoDetails[] = [
                        'date' => $item->created_at->format('Y-m-d'),
                        'transaction' => 'Refund',
                        'units' => $item->quantity,
                        'cost' => $item->price,
                        'price' => 0,
                        'total_costs' => $item->refund_amount,
                        'total_sales' => 0,
                    ];
                }
            }
        }
        
        usort($fifoDetails, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        // Prepare the final report data
        return view('backend.reports.product_fifo_details_report', compact(
            'fifoDetails',
            'total_sales',
            'total_purchases',
            'openingStocks',
            'start_date',
            'end_date',
            'products'
        ));
    }
    
    

    

   public function product_history_yearly_report(Request $request)
{
    $warehouse = $request->warehouse;
    $sort_by = null;
    $pro_sort_by = [];
    $start_date = date('Y-m-d', strtotime('-3 years'));
    $end_date = date('Y-m-d');
    $products = collect(); // Initialize an empty collection

    $filterApplied = false;

    if (!empty($request->product_id)) {
        $pro_sort_by = $request->product_id;
        $filterApplied = true;

        $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
            ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('warehouses', 'warehouses.id', '=', 'orders.warehouse')
            ->select(
                'products.name as product_name', 'products.purchase_price', 'products.id as productId',
                'categories.name as category_name',
                'order_details.price as sale_price', 'order_details.quantity as order_qty',
                'orders.delivered_date', 'orders.code', 'orders.id as orderId', 'orders.warehouse', 'warehouses.name as warehouse_name',
                DB::raw('COALESCE(sum(order_details.price), 0) AS price'),
                DB::raw('COALESCE(sum(order_details.quantity), 0) AS total_order_qty'),
                DB::raw('COALESCE(sum(order_details.price), 0) AS total_order_price'),
                DB::raw('COALESCE(sum(quantity), 0) AS quantity'),
                DB::raw('COALESCE(count(order_details.product_id), 0) AS num_of_sale')
            )
            ->whereIn('products.id', $pro_sort_by)
            ->groupBy('products.id')
            ->groupBy(DB::raw('YEAR(orders.delivered_date)'))
            ->orderBy('orders.delivered_date', 'desc');

        if (!empty($warehouse)) {
            $products = $products->where('orders.warehouse', $warehouse);
        }

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $products = $products->whereBetween('orders.date', [
                strtotime($start_date),
                strtotime($end_date . ' +1 day')
            ]);
        } else {
            $products = $products->whereBetween('orders.date', [
                strtotime($start_date),
                strtotime($end_date)
            ]);
        }

        $products->where('order_details.delivery_status', 'delivered');
        $products->groupBy(DB::raw('YEAR(orders.date)'), DB::raw('MONTH(orders.date)'))
            ->orderBy(DB::raw('YEAR(orders.date)'), 'desc')
            ->orderBy(DB::raw('MONTH(orders.date)'), 'desc');
        $products = $products->get();
    }

    return view('backend.reports.product_history_yearly_report', compact('products', 'sort_by', 'pro_sort_by', 'start_date', 'end_date', 'filterApplied','warehouse'));
}



    public function product_history_report(Request $request)
{
    $warehouse = $request->warehouse;
    $productId = $request->productId;
    $sort_by = null;
    $pro_sort_by = null;
    $start_date = $request->start_date;
    $end_date = $request->end_date;

    DB::enableQueryLog();

    $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
        ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
        ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
        ->where('products.id', $productId)
        ->where('num_of_sale', '>', 0)
        ->select(
            'products.name as product_name', 'products.purchase_price', 'products.id as productId',
            'categories.name as category_name',
            'order_details.price as sale_price', 'order_details.quantity as order_qty',
            'orders.delivered_date', 'orders.code', 'orders.id as orderId', 'orders.warehouse', 
            DB::raw('sum(order_details.price) AS price'),
            DB::raw('sum(order_details.quantity) AS total_order_qty'),
            DB::raw('sum(order_details.price) AS total_order_price'),
            DB::raw('sum(quantity) AS quantity'),
            DB::raw('count(product_id) AS num_of_sale')
        )
        ->groupBy('products.id')
        ->groupBy(DB::raw('YEAR(orders.delivered_date)'), DB::raw('MONTH(orders.delivered_date)'))
        ->orderBy('orders.delivered_date', 'asc');
        if (!empty($warehouse)) {
            $products = $products->where('orders.warehouse', $warehouse);
        }

    if (!empty($start_date) && !empty($end_date)) {
        $start_date = date('Y-m-01', strtotime($start_date));
        $end_date = date('Y-m-t', strtotime($end_date));
        
        $products = $products->whereBetween('orders.delivered_date', [$start_date, $end_date]);
    } else {
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
    }

    $products->where('order_details.delivery_status', 'delivered');
    $products->groupBy(DB::raw('YEAR(orders.delivered_date)'), DB::raw('MONTH(orders.delivered_date)'))
        ->orderBy(DB::raw('YEAR(orders.delivered_date)'), 'desc')
        ->orderBy(DB::raw('MONTH(orders.delivered_date)'), 'desc');

    $products = $products->get();

    return view('backend.reports.product_history_report', compact('products', 'sort_by', 'pro_sort_by', 'start_date', 'end_date','warehouse'));
}



    public function product_wise_sales_history_report(Request $request)
    {
        $warehouse = $request->warehouse;
        $productId = $request->productId;
        $sort_by = null;
        $pro_sort_by = null;
        $start_date = $request->start_date;
        $end_date = $request->end_date; 
    
        DB::enableQueryLog();
    
        $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
            ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->where('num_of_sale', '>', 0)
            ->where('products.id', $productId)
            ->select(
                'products.name as product_name',
                'products.purchase_price', 'products.id as productId',
                'categories.name as category_name',
                'order_details.price as sale_price',
                'order_details.quantity as order_qty',
                'orders.delivered_date',
                'orders.code',
                'orders.id as orderId',
                'orders.warehouse',
                DB::raw('sum(order_details.price) AS price'),
                DB::raw('sum(order_details.quantity) AS total_order_qty'),
                DB::raw('sum(order_details.price) AS total_order_price'),
                DB::raw('sum(quantity) AS quantity'),
                DB::raw('count(product_id) AS num_of_sale')
            )
            ->groupBy( 'orders.delivered_date')
            ->orderBy('orders.delivered_date', 'asc');
    
        $products->where('order_details.delivery_status', 'delivered');
        $products->groupBy(DB::raw('YEAR(orders.delivered_date)'), DB::raw('MONTH(orders.delivered_date)'))
            ->orderBy(DB::raw('YEAR(orders.delivered_date)'), 'desc')
            ->orderBy(DB::raw('MONTH(orders.delivered_date)'), 'desc');
            if (!empty($warehouse)) {
                $products = $products->where('orders.warehouse', $warehouse);
            }
       
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $end_date = date('Y-m-d 23:59:59', strtotime($end_date));

            $products = $products->whereBetween('orders.delivered_date', [$start_date, $end_date]);
        } else {
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
            
            $end_date = date('Y-m-d 23:59:59', strtotime($end_date));

            $products = $products->whereBetween('orders.delivered_date', [$start_date, $end_date]);
        }
    
        $products = $products->get();
        // dd($products);
    
        return view('backend.reports.product_wise_sales_history_report', compact('products', 'sort_by', 'pro_sort_by', 'start_date', 'end_date', 'productId','warehouse'));
    }

    

    public function product_wise_daily_sales_history_report(Request $request)
    {
        $warehouse = $request->warehouse;
        $productId = $request->productId;
        $sort_by = null;
        $pro_sort_by = null;
        $start_date = $request->start_date;
        $end_date = $request->end_date; 
    
        DB::enableQueryLog();
    
        $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
            ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->where('num_of_sale', '>', 0)
            ->where('products.id', $productId)
            ->select(
                'products.name as product_name',
                'products.purchase_price',
                'categories.name as category_name',
                'order_details.price as sale_price',
                'order_details.quantity as order_qty',
                'orders.delivered_date',
                'orders.code',
                'orders.id as orderId',
                'orders.warehouse',
                DB::raw('sum(order_details.price) AS price'),
                DB::raw('sum(quantity) AS quantity'),
                DB::raw('count(product_id) AS num_of_sale')
            )
            ->groupBy('order_details.id')
            ->orderBy('orders.delivered_date', 'asc');
    
        $products->where('order_details.delivery_status', 'delivered');
        $products->groupBy(DB::raw('YEAR(orders.delivered_date)'), DB::raw('MONTH(orders.delivered_date)'))
            ->orderBy(DB::raw('YEAR(orders.delivered_date)'), 'desc')
            ->orderBy(DB::raw('MONTH(orders.delivered_date)'), 'desc');

            if (!empty($warehouse)) {
                $products = $products->where('orders.warehouse', $warehouse);
            }
    
        if (!empty($start_date) && !empty($end_date)) {
            $end_date = date('Y-m-t 23:59:59', strtotime($end_date));
            $products->whereBetween('orders.delivered_date', [$start_date, $end_date]);
        } else {
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t 23:59:59');
        }
    
        $products = $products->get();
        // dd($products);
    
        return view('backend.reports.product_wise_daily_sales_history_report', compact('products', 'sort_by', 'pro_sort_by', 'start_date', 'end_date', 'productId','warehouse'));
    }

    public function product_specific_day_sales_history_report(Request $request)
    {
        $warehouse = $request->warehouse;
        $productId = $request->productId;
        $sort_by = null;
        $pro_sort_by = null;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $delivered_date = $request->delivered_date; 
        
        DB::enableQueryLog();
    
        $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
            ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->where('num_of_sale', '>', 0)
            ->where('products.id', $productId)
            ->whereDate('orders.delivered_date', $delivered_date) 
            ->select(
                'products.name as product_name',
                'products.purchase_price',
                'categories.name as category_name',
                'order_details.price as sale_price',
                'order_details.quantity as order_qty',
                'orders.delivered_date',
                'orders.code',
                'orders.id as orderId',
                'orders.warehouse',
                DB::raw('sum(order_details.price) AS price'),
                DB::raw('sum(quantity) AS quantity'),
                DB::raw('count(product_id) AS num_of_sale')
            )
            ->groupBy('order_details.id')
            ->orderBy('num_of_sale', 'desc');
            if (!empty($warehouse)) {
                $products = $products->where('orders.warehouse', $warehouse);
            }
    
        $products->where('order_details.delivery_status', 'delivered');
    
        $products = $products->get();
    
        return view('backend.reports.product_specific_day_sales_history_report', compact('products', 'sort_by', 'pro_sort_by', 'start_date', 'end_date', 'productId', 'delivered_date','warehouse'));
    }


public function product_history_compared_report(Request $request)
{
    $productId= $request->product_id;
    $sort_by = null;
    $pro_sort_by = null;
    $start_date = date('Y-m-01', strtotime('-3 years'));
    $end_date = date('Y-m-t');

    $months = [];
    $totals = [];
    $currentYear = Carbon::now()->year;
    $startYear = $currentYear - 2;

    for ($year = $currentYear; $year >= $startYear; $year--) {
        $totals[$year] = [
            'qty' => 0,
            'average_sale' => 0,
            'amount' => 0
        ];
    }

    for ($month = 1; $month <= 12; $month++) {
        $monthData = [
            'name' => Carbon::create(null, $month, 1)->format('F')
        ];
        for ($year = $currentYear; $year >= $startYear; $year--) {
            $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
                ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
                ->where('num_of_sale', '>', 0)
                ->where('products.id',  $productId)
                ->whereYear('orders.delivered_date', $year)
                ->whereMonth('orders.delivered_date', $month)
                ->select(
                    'products.name as product_name', 'products.purchase_price', 'products.id as productId',
                    'categories.name as category_name',
                    'order_details.price as sale_price', 'order_details.quantity as order_qty',
                    'orders.delivered_date', 'orders.code', 'orders.id as orderId', 'orders.warehouse',
                    DB::raw('sum(order_details.price) AS total_order_price'),
                    DB::raw('sum(order_details.quantity) AS total_order_qty'),
                    DB::raw('count(product_id) AS num_of_sale')
                )
                ->groupBy('products.id', 'orders.delivered_date')
                ->orderBy('num_of_sale', 'desc')
                ->get();

            $qty = $products->sum('total_order_qty');
            $amount = $products->sum('total_order_price');
            $average_sale = $qty > 0 ? $amount / $qty : 0;

            $monthData[$year] = [
                'qty' => $qty,
                'average_sale' => $average_sale,
                'amount' => $amount
            ];

            $totals[$year]['qty'] += $qty;
            $totals[$year]['amount'] += $amount;
        }
        $months[] = $monthData;
    }

    $productsQuery = Product::join('categories', 'products.category_id', '=', 'categories.id')
        ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
        ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
        ->where('num_of_sale', '>', 0)
        ->where('products.id',$productId )
        ->where('order_details.delivery_status', 'delivered')
        ->select(
            'products.name as product_name', 'products.purchase_price', 'products.id as productId',
            'categories.name as category_name',
            'order_details.price as sale_price', 'order_details.quantity as order_qty',
            'orders.delivered_date', 'orders.code', 'orders.id as orderId', 'orders.warehouse',
            DB::raw('sum(order_details.price) AS total_order_price'),
            DB::raw('sum(order_details.quantity) AS total_order_qty'),
            DB::raw('count(product_id) AS num_of_sale')
        )
        ->groupBy('products.id', 'orders.delivered_date');

    if (!empty($request->product_id)) {
        $pro_sort_by = $request->product_id;
        $productsQuery->where('products.id', $pro_sort_by);
    }

    if (!empty($request->start_date) && !empty($request->end_date)) {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $productsQuery->whereBetween('orders.delivered_date', [$start_date, $end_date]);
    }

    $products = $productsQuery->orderBy('num_of_sale', 'desc')->get();

    if (empty($request->start_date) || empty($request->end_date)) {
        $start_date = date('Y-m-01', strtotime('-3 years'));
        $end_date = date('Y-m-t');
    }

    return view('backend.reports.product_history_compared_report', compact('products', 'sort_by', 'pro_sort_by', 'start_date', 'end_date', 'months', 'currentYear', 'totals','productId'));
}

public function multiple_product_history_compared_report(Request $request)
{
    $productIds = $request->input('product_id', []); 
    $sort_by = null;
    $pro_sort_by = null;
    $start_date = $request->input('start_date', date('Y-m-01', strtotime('-3 years')));
    $end_date = $request->input('end_date', date('Y-m-t'));

    $currentYear = Carbon::now()->year;
    $startYear = $currentYear - 2;

    $productsData = [];

    foreach ($productIds as $productId) {
        $months = [];
        $totals = [];

        for ($year = $currentYear; $year >= $startYear; $year--) {
            $totals[$year] = [
                'qty' => 0,
                'average_sale' => 0,
                'amount' => 0
            ];
        }

        for ($month = 1; $month <= 12; $month++) {
            $monthData = [
                'name' => Carbon::create(null, $month, 1)->format('F')
            ];
            for ($year = $currentYear; $year >= $startYear; $year--) {
                $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
                    ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
                    ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
                    ->where('num_of_sale', '>', 0)
                    ->where('products.id',  $productId)
                    ->whereYear('orders.delivered_date', $year)
                    ->whereMonth('orders.delivered_date', $month)
                    ->select(
                        'products.name as product_name', 'products.purchase_price', 'products.id as productId',
                        'categories.name as category_name',
                        'order_details.price as sale_price', 'order_details.quantity as order_qty',
                        'orders.delivered_date', 'orders.code', 'orders.id as orderId', 'orders.warehouse',
                        DB::raw('sum(order_details.price) AS total_order_price'),
                        DB::raw('sum(order_details.quantity) AS total_order_qty'),
                        DB::raw('count(product_id) AS num_of_sale')
                    )
                    ->groupBy('products.id', 'orders.delivered_date')
                    ->orderBy('num_of_sale', 'desc')
                    ->get();

                $qty = $products->sum('total_order_qty');
                $amount = $products->sum('total_order_price');
                $average_sale = $qty > 0 ? $amount / $qty : 0;

                $monthData[$year] = [
                    'qty' => $qty,
                    'average_sale' => $average_sale,
                    'amount' => $amount
                ];

                $totals[$year]['qty'] += $qty;
                $totals[$year]['amount'] += $amount;
            }
            $months[] = $monthData;
        }

        $product_name = 'Undefined/Empty';
        if ($products->isNotEmpty()) {
            $product_name = $products[0]->product_name;
        } else {
            $product = Product::find($productId);
            if ($product) {
                $product_name = $product->name;
            }
        }

        $productsData[] = [
            'productId' => $productId,
            'product_name' => $product_name,
            'months' => $months,
            'totals' => $totals
        ];
    }

    return view('backend.reports.multiple_product_history_compared_report', compact('productsData', 'sort_by', 'pro_sort_by', 'start_date', 'end_date', 'currentYear'));
}


public function purchase_report_history(Request $request)
{
    $productIds = $request->input('product_id', []); 
    $sort_by = null;
    $pro_sort_by = null;
    $start_date = $request->input('start_date', date('Y-m-01', strtotime('-3 years')));
    $end_date = $request->input('end_date', date('Y-m-t'));

    $currentYear = Carbon::now()->year;
    $startYear = $currentYear - 2;

    $productsData = [];

    foreach ($productIds as $productId) {
        $months = [];
        $totals = [];

        for ($year = $currentYear; $year >= $startYear; $year--) {
            $totals[$year] = [
                'qty' => 0,
                'average_sale' => 0,
                'amount' => 0
            ];
        }

        for ($month = 1; $month <= 12; $month++) {
            $monthData = [
                'name' => Carbon::create(null, $month, 1)->format('F')
            ];
            for ($year = $currentYear; $year >= $startYear; $year--) {
                $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
                    ->leftJoin('purchase_details', 'products.id', '=', 'purchase_details.product_id')
                    ->leftJoin('purchases', 'purchases.id', '=', 'purchase_details.id')
                    // ->where('num_of_sale', '>', 0)
                    ->where('products.id',  $productId)
                    ->where('purchases.payment_status', 3)
                    ->whereYear('purchase_details.created_at', $year)
                    ->whereMonth('purchase_details.created_at', $month)
                    ->select(
                        'products.name as product_name', 'products.purchase_price', 'products.id as productId',
                        'categories.name as category_name',
                        'purchase_details.price as sale_price', 'purchase_details.qty as order_qty',
                        'purchase_details.created_at',  'purchases.id as poId', 'purchases.wearhouse_id',
                        DB::raw('sum(purchase_details.amount) AS total_order_price'),
                        DB::raw('sum(purchase_details.qty) AS total_order_qty'),
                        DB::raw('count(purchase_details.product_id) AS num_of_sale')
                    )
                    ->groupBy('products.id', 'purchase_details.created_at')
                    ->orderBy('num_of_sale', 'desc')
                    ->get();

                $qty = $products->sum('total_order_qty');
                $amount = $products->sum('total_order_price');
                $average_sale = $qty > 0 ? $amount / $qty : 0;

                $monthData[$year] = [
                    'qty' => $qty,
                    'average_sale' => $average_sale,
                    'amount' => $amount
                ];

                // Update totals
                $totals[$year]['qty'] += $qty;
                $totals[$year]['amount'] += $amount;
            }
            $months[] = $monthData;
        }

        $product_name = 'Undefined/Empty';
        if ($products->isNotEmpty()) {
            $product_name = $products[0]->product_name;
        } else {
            $product = Product::find($productId);
            if ($product) {
                $product_name = $product->name;
            }
        }

        $productsData[] = [
            'productId' => $productId,
            'product_name' => $product_name,
            'months' => $months,
            'totals' => $totals
        ];
    }

    return view('backend.reports.purchase_report_history', compact('productsData', 'sort_by', 'pro_sort_by', 'start_date', 'end_date', 'currentYear'));
}

public function warehouse_sales_compare(Request $request ,$type=null)
{
    $warehouseIds = $request->input('warehouse', []); 
    $sort_by = null;
    $pro_sort_by = null;
    $start_date = $request->input('start_date', date('Y-m-01', strtotime('-3 years')));
    $end_date = $request->input('end_date', date('Y-m-t'));

    $currentYear = Carbon::now()->year;
    $startYear = $currentYear - 2;

    $productsData = [];

    $warehouses = Warehouse::all();
    if (!empty($warehouseIds)) {
        $warehouses = $warehouses->whereIn('id', $warehouseIds);
    }

    foreach ($warehouses as $warehouse) {
        $months = [];
        $totals = [];

        for ($year = $currentYear; $year >= $startYear; $year--) {
            $totals[$year] = [
                'qty' => 0,
                'average_sale' => 0,
                'amount' => 0
            ];
        }

        for ($month = 1; $month <= 12; $month++) {
            $monthData = [
                'name' => Carbon::create(null, $month, 1)->format('F')
            ];
            for ($year = $currentYear; $year >= $startYear; $year--) {
                $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
                    ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
                    ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
                    ->leftJoin('warehouses', 'warehouses.id', '=', 'orders.warehouse')
                    ->where('num_of_sale', '>', 0)
                    ->whereYear('orders.delivered_date', $year)
                    ->whereMonth('orders.delivered_date', $month)
                    ->where('orders.warehouse', $warehouse->id)
                    ->select(
                        'products.name as product_name', 'products.purchase_price', 'products.id as productId',
                        'categories.name as category_name',
                        'order_details.price as sale_price', 'order_details.quantity as order_qty',
                        'orders.delivered_date', 'orders.code', 'orders.id as orderId', 'orders.warehouse', 'warehouses.name as warehouse_name',
                        DB::raw('sum(order_details.price) AS total_order_price'),
                        DB::raw('sum(order_details.quantity) AS total_order_qty'),
                        DB::raw('count(product_id) AS num_of_sale')
                    )
                    ->groupBy('products.id', 'orders.delivered_date')
                    ->orderBy('num_of_sale', 'desc')
                    ->get();

                $qty = $products->sum('total_order_qty');
                $amount = $products->sum('total_order_price');
                $average_sale = $qty > 0 ? $amount / $qty : 0;

                $monthData[$year] = [
                    'qty' => $qty,
                    'average_sale' => $average_sale,
                    'amount' => $amount
                ];

                $totals[$year]['qty'] += $qty;
                $totals[$year]['amount'] += $amount;
            }
            $months[] = $monthData;
        }

        $warehouse_name = 'Undefined/Empty';
        if ($products->isNotEmpty()) {
            $warehouse_name = $products[0]->warehouse_name;
        } else {
            $warehouse_name = $warehouse->name;
        }

        $productsData[] = [
            'warehouseId' => $warehouse->id,
            'warehouse_name' => $warehouse_name,
            'months' => $months,
            'totals' => $totals
        ];
    }

    if ($request->input('export') == 'excel') {
        return Excel::download(new WarehouseSalesCompareExport($productsData, $currentYear), 'warehouseSalesCompareReport.xlsx');
    }

    return view('backend.reports.warehouse_sales_compare', compact('productsData', 'sort_by', 'pro_sort_by', 'start_date', 'end_date', 'currentYear', 'warehouseIds'));
}


public function warehouse_yearly_sales_compare(Request $request, $type = null)
{
    $warehouseIds = $request->input('warehouse', []);
    $start_date = $request->input('start_date', date('Y-m-01', strtotime('-3 years')));
    $end_date = $request->input('end_date', date('Y-m-t'));

    $currentYear = Carbon::now()->year;
    $startYear = $currentYear - 2;

    $productsData = [];

    $warehouses = Warehouse::all();
    if (!empty($warehouseIds)) {
        $warehouses = $warehouses->whereIn('id', $warehouseIds);
    }

    foreach ($warehouses as $warehouse) {
        $totals = [];

        for ($year = $startYear; $year <= $currentYear; $year++) {
            $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
                ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
                ->leftJoin('warehouses', 'warehouses.id', '=', 'orders.warehouse')
                ->where('num_of_sale', '>', 0)
                ->whereYear('orders.delivered_date', $year)
                ->where('orders.warehouse', $warehouse->id)
                ->select(
                    DB::raw('SUM(order_details.price) AS total_order_price')
                )
                ->groupBy('orders.warehouse')
                ->first();

            $totals[$year] = $products ? $products->total_order_price : 0;
        }

        $productsData[] = [
            'warehouse_name' => $warehouse->name,
            'warehouse_id' => $warehouse->id,
            'totals' => $totals
        ];
    }

    if ($request->query('export') == 'excel') {
        return Excel::download(
            new WarehouseYearlySalesCompareExport($productsData, $startYear, $currentYear),
            'warehouseYearlySalesCompare.xlsx'
        );
    }

    return view('backend.reports.warehouse_yearly_sales_compare', compact('productsData', 'start_date', 'end_date', 'currentYear', 'startYear', 'warehouseIds'));
}



public function warehouse_monthly_sales_report(Request $request)
{
    $year = $request->input('year');
    $warehouseIds = $request->input('warehouse', []); 
    $warehouses = Warehouse::all();

    if (!empty($warehouseIds)) {
        $warehouses = $warehouses->whereIn('id', $warehouseIds);
    }
    
    $months = [];
    $totals = [];

    for ($month = 1; $month <= 12; $month++) {
        $monthData = [];
        $monthTotal = 0;

        foreach ($warehouses as $warehouse) {
            $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
                ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
                ->where('num_of_sale', '>', 0)
                ->whereYear('orders.delivered_date', $year)
                ->whereMonth('orders.delivered_date', $month)
                ->where('orders.warehouse', $warehouse->id)
                ->select(DB::raw('SUM(order_details.price) AS total_order_price'))
                ->first();

            $monthData[$warehouse->id] = $products ? $products->total_order_price : 0;
            $monthTotal += $monthData[$warehouse->id];
        }

        $months[$month] = $monthData;
        $totals[$month] = $monthTotal;
    }

    return view('backend.reports.warehouse_monthly_sales_report', compact('months', 'totals', 'year', 'warehouses'));
}



public function warehouse_stock_summery(Request $request)
{
    $warehouseIds = $request->input('warehouse', []); 
    $start_date = $request->input('start_date', date('Y-m-01', strtotime('-3 years')));
    $end_date = $request->input('end_date', date('Y-m-t'));

    $currentYear = Carbon::now()->year;
    $startYear = $currentYear - 2;

    $warehouses = Warehouse::all();
    if (!empty($warehouseIds)) {
        $warehouses = $warehouses->whereIn('id', $warehouseIds);
    }

    $yearData = [];
    foreach (range($currentYear, $startYear) as $year) {
        $yearData[$year] = [
            'warehouses' => [],
            'totals' => [
                'opening_stock' => 0,
                'purchase' => 0,
                'transfer_in' => 0,
                'sales' => 0,
                'transfer_out' => 0,
                'closing_stock' => 0,
                'profit_loss' => 0
            ]
        ];

        foreach ($warehouses as $warehouse) {
            $warehouseId = $warehouse->id;

            $openingStocks = OpeningStock::where('wearhouse_id', $warehouseId)
                ->whereYear('created_at', $year)
                ->get();

            $openingStockQty = 0;
            $openingStockAmount = 0;
            foreach ($openingStocks as $openStock) {
                $openingStockQty += $openStock->qty;
                $openingStockAmount += $openStock->qty * $openStock->price;
            }

            $purchases = PurchaseDetail::select('purchase_details.id', 'purchase_details.id', 'purchase_details.product_id', 'purchase_details.wearhouse_id', 'purchase_details.qty', 'purchase_details.price', 'purchase_details.amount', 'purchases.status', 'purchases.date')
                ->leftJoin('purchases', 'purchases.id', '=', 'purchase_details.id')
                ->where('purchases.status', 2)
                ->where('purchase_details.wearhouse_id', $warehouseId)
                ->whereYear('purchases.date', $year)
                ->get();

            $purchaseQty = 0;
            $purchaseAmount = 0;
            foreach ($purchases as $purchase) {
                $purchaseQty += $purchase->qty;
                $purchaseAmount += $purchase->qty * $purchase->price;
            }

            $transfersIn = Transfer::select('transfers.id', 'transfers.product_id', 'transfers.to_wearhouse_id', 'transfers.qty', 'transfers.unit_price as price', 'transfers.amount', 'transfers.status', 'transfers.date')
                ->where('status', 'Approved')
                ->where('to_wearhouse_id', $warehouseId)
                ->whereYear('date', $year)
                ->get();

            $transferInQty = 0;
            $transferInAmount = 0;
            foreach ($transfersIn as $transferIn) {
                $transferInQty += $transferIn->qty;
                $transferInAmount += $transferIn->qty * $transferIn->price;
            }

            $transfersOut = Transfer::select('transfers.id', 'transfers.product_id', 'transfers.from_wearhouse_id', 'transfers.qty', 'transfers.unit_price as price', 'transfers.amount', 'transfers.status', 'transfers.date')
                ->where('status', 'Approved')
                ->where('from_wearhouse_id', $warehouseId)
                ->whereYear('date', $year)
                ->get();

            $transferOutQty = 0;
            $transferOutAmount = 0;
            foreach ($transfersOut as $transferOut) {
                $transferOutQty += $transferOut->qty;
                $transferOutAmount += $transferOut->qty * $transferOut->price;
            }

            $sales = Product::join('order_details', 'products.id', '=', 'order_details.product_id')
                ->join('orders', 'orders.id', '=', 'order_details.order_id')
                ->where('orders.warehouse', $warehouseId)
                ->whereYear('orders.delivered_date', $year)
                ->sum('order_details.price');

            $closingStockItems = OpeningStock::where('wearhouse_id', $warehouseId)
            ->whereYear('created_at', $year)
            ->get();

            $closingStockItems = $closingStockItems->merge($purchases);

            $closingStockItems = $closingStockItems->merge($transfersIn);

            $closingStockItems = $closingStockItems->reject(function ($item) use ($transfersOut) {
            return $transfersOut->contains('id', $item->id);
            });

            $closingStockQty = 0;
            $closingStockAmount = 0;
            foreach ($closingStockItems as $closingStockItem) {
            $closingStockQty += $closingStockItem->qty;
            $closingStockAmount += $closingStockItem->qty * $closingStockItem->price;
            }

            $totals = [
            'opening_stock' => $openingStockAmount,
            'purchase' => $purchaseAmount,
            'transfer_in' => $transferInAmount,
            'sales' => $sales,
            'transfer_out' => $transferOutAmount, 
            'closing_stock' => $closingStockAmount,
            'profit_loss' => 0 
            ];


            $totals['profit_loss'] = $totals['sales'] - ($totals['opening_stock'] + $totals['purchase'] + $totals['transfer_in'] - $totals['transfer_out'] - $totals['closing_stock']);

            $yearData[$year]['warehouses'][] = [
                'name' => $warehouse->name,
                'data' => $totals
            ];

            foreach ($totals as $key => $value) {
                $yearData[$year]['totals'][$key] += $value;
            }
        }
    }

    return view('backend.reports.warehouse_stock_summery', compact('yearData', 'start_date', 'end_date', 'currentYear', 'startYear', 'warehouseIds'));
}


public function monthly_warehouse_stock_summery(Request $request, $year)
{
    $warehouseIds = $request->input('warehouse', []);

    if (empty($warehouseIds)) {
        $warehouseIds = Warehouse::pluck('id')->toArray(); 
    } elseif (is_string($warehouseIds)) {
        $warehouseIds = explode(',', $warehouseIds); 
    }

    $warehouseIds = array_map('intval', $warehouseIds);

    $warehouses = Warehouse::whereIn('id', $warehouseIds)->get();
    
    $months = range(1, 12); 
    $monthlyData = [];

    foreach ($months as $month) {
        $monthData = [
            'warehouses' => [],
            'totals' => [
                'opening_stock' => 0,
                'purchase' => 0,
                'transfer_in' => 0,
                'sales' => 0,
                'transfer_out' => 0,
                'closing_stock' => 0,
                'profit_loss' => 0
            ]
        ];

        foreach ($warehouses as $warehouse) {
            $warehouseId = $warehouse->id;

            $openingStockQty = 0;
            $openingStockAmount = 0;
            $purchaseQty = 0;
            $purchaseAmount = 0;
            $transferInQty = 0;
            $transferInAmount = 0;
            $salesAmount = 0;
            $transferOutQty = 0;
            $transferOutAmount = 0;

            $openingStocks = OpeningStock::where('wearhouse_id', $warehouseId)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->get();

            foreach ($openingStocks as $openStock) {
                $openingStockQty += $openStock->qty;
                $openingStockAmount += $openStock->qty * $openStock->price;
            }

            $purchases = PurchaseDetail::select('purchase_details.qty', 'purchase_details.price')
                ->leftJoin('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')
                ->where('purchase_details.wearhouse_id', $warehouseId)
                ->whereYear('purchases.date', $year)
                ->whereMonth('purchases.date', $month)
                ->where('purchases.status', 2)
                ->get();

            foreach ($purchases as $purchase) {
                $purchaseQty += $purchase->qty;
                $purchaseAmount += $purchase->qty * $purchase->price;
            }

            $transfersIn = Transfer::select('qty', 'unit_price as price')
                ->where('status', 'Approved')
                ->where('to_wearhouse_id', $warehouseId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();

            foreach ($transfersIn as $transferIn) {
                $transferInQty += $transferIn->qty;
                $transferInAmount += $transferIn->qty * $transferIn->price;
            }

            $transfersOut = Transfer::select('qty', 'unit_price as price')
                ->where('status', 'Approved')
                ->where('from_wearhouse_id', $warehouseId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();

            foreach ($transfersOut as $transferOut) {
                $transferOutQty += $transferOut->qty;
                $transferOutAmount += $transferOut->qty * $transferOut->price;
            }

            $salesAmount = Product::join('order_details', 'products.id', '=', 'order_details.product_id')
                ->join('orders', 'orders.id', '=', 'order_details.order_id')
                ->where('orders.warehouse', $warehouseId)
                ->whereYear('orders.delivered_date', $year)
                ->whereMonth('orders.delivered_date', $month)
                ->sum('order_details.price');

            $closingStockQty = $openingStockQty + $purchaseQty + $transferInQty - $transferOutQty; 
            $closingStockAmount = $openingStockAmount + $purchaseAmount + $transferInAmount - $transferOutAmount; 

            $profitLoss = $salesAmount - ($openingStockAmount + $purchaseAmount + $transferInAmount - $transferOutAmount - $closingStockAmount);

            $monthData['warehouses'][] = [
                'name' => $warehouse->name,
                'data' => [
                    'opening_stock' => $openingStockAmount,
                    'purchase' => $purchaseAmount,
                    'transfer_in' => $transferInAmount,
                    'sales' => $salesAmount,
                    'transfer_out' => $transferOutAmount,
                    'closing_stock' => $closingStockAmount,
                    'profit_loss' => $profitLoss
                ]
            ];

            $monthData['totals']['opening_stock'] += $openingStockAmount;
            $monthData['totals']['purchase'] += $purchaseAmount;
            $monthData['totals']['transfer_in'] += $transferInAmount;
            $monthData['totals']['sales'] += $salesAmount;
            $monthData['totals']['transfer_out'] += $transferOutAmount;
            $monthData['totals']['closing_stock'] += $closingStockAmount;
            $monthData['totals']['profit_loss'] += $profitLoss;
        }

        $monthlyData[$month] = $monthData;
    }

    return view('backend.reports.monthly_warehouse_stock_summery', compact('monthlyData', 'year', 'warehouseIds'));
}



public function sales_report(Request $request)
{
    $warehouseIds = $request->input('warehouse', []); 
    $start_date = $request->input('start_date', date('Y-m-01', strtotime('-3 years')));
    $end_date = $request->input('end_date', date('Y-m-t'));

    $currentYear = Carbon::now()->year;
    $startYear = $currentYear - 2;

    $productsData = [];

    $warehouses = Warehouse::all();
    if (!empty($warehouseIds)) {
        $warehouses = $warehouses->whereIn('id', $warehouseIds);
    }

    foreach ($warehouses as $warehouse) {
        $totals = [];
        $totalCustomers = [];
        $totalOrders = [];

        for ($year = $startYear; $year <= $currentYear; $year++) {
            $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
                ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
                ->leftJoin('warehouses', 'warehouses.id', '=', 'orders.warehouse')
                ->where('num_of_sale', '>', 0)
                ->whereYear('orders.delivered_date', $year)
                ->where('orders.warehouse', $warehouse->id)
                ->select(
                    DB::raw('SUM(order_details.price) AS total_order_price'),
                    DB::raw('COUNT(DISTINCT orders.user_id) AS total_customers'),
                    DB::raw('COUNT(DISTINCT orders.id) AS total_orders')
                )
                ->first();

            $totals[$year] = $products ? $products->total_order_price : 0;
            $totalCustomers[$year] = $products ? $products->total_customers : 0;
            $totalOrders[$year] = $products ? $products->total_orders : 0;
        }

        $productsData[] = [
            'warehouse_name' => $warehouse->name,
            'totals' => $totals,
            'total_customers' => $totalCustomers,
            'total_orders' => $totalOrders
        ];
    }

    return view('backend.reports.sales_report_yearly', compact('productsData', 'start_date', 'end_date', 'currentYear', 'startYear', 'warehouseIds'));
}

public function sales_report_monthly(Request $request)
{
    $year = $request->input('year', Carbon::now()->year);
    $warehouseIds = $request->input('warehouse', []); 
    $start_date = $request->input('start_date', date('Y-m-01', strtotime('-3 years')));
    $end_date = $request->input('end_date', date('Y-m-t'));

    $warehouses = Warehouse::all();
    if (!empty($warehouseIds)) {
        $warehouses = $warehouses->whereIn('id', $warehouseIds);
    }

    $months = [];
    $totalCustomersYearly = 0;
    $totalOrdersYearly = 0;
    $totalSalesYearly = 0;

    for ($month = 1; $month <= 12; $month++) {
        $totalCustomers = 0;
        $totalOrders = 0;
        $totalSales = 0;

        foreach ($warehouses as $warehouse) {
            $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
                ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
                ->leftJoin('warehouses', 'warehouses.id', '=', 'orders.warehouse')
                ->where('num_of_sale', '>', 0)
                ->whereYear('orders.delivered_date', $year)
                ->whereMonth('orders.delivered_date', $month)
                ->where('orders.warehouse', $warehouse->id)
                ->select(
                    DB::raw('SUM(order_details.price) AS total_order_price'),
                    DB::raw('COUNT(DISTINCT orders.user_id) AS total_customers'),
                    DB::raw('COUNT(DISTINCT orders.id) AS total_orders')
                )
                ->first();

            $totalSales += $products ? $products->total_order_price : 0;
            $totalCustomers += $products ? $products->total_customers : 0;
            $totalOrders += $products ? $products->total_orders : 0;
        }

        $totalCustomersYearly += $totalCustomers;
        $totalOrdersYearly += $totalOrders;
        $totalSalesYearly += $totalSales;

        $months[$month] = [
            'total_sales' => $totalSales,
            'total_customers' => $totalCustomers,
            'total_orders' => $totalOrders
        ];
    }

    return view('backend.reports.sales_report_monthly', compact('months', 'year', 'totalCustomersYearly', 'totalOrdersYearly', 'totalSalesYearly'));
}

public function sales_by_platform(Request $request, $type = null)
{
    $warehouseIds = $request->input('warehouse', []); 
    $start_date = $request->input('start_date', date('Y-m-01'));
    $end_date = $request->input('end_date', date('Y-m-t'));

    $currentYear = Carbon::now()->year;
    $startYear = $currentYear - 4;

    $warehouses = Warehouse::all();
    if (!empty($warehouseIds)) {
        $warehouses = $warehouses->whereIn('id', $warehouseIds);
    }

    $platformData = [];

    foreach ($warehouses as $warehouse) {
        for ($year = $startYear; $year <= $currentYear; $year++) {
            $platforms = Order::whereYear('orders.delivered_date', $year)
                ->whereNull('orders.canceled_by')
                ->where('orders.warehouse', $warehouse->id)
                ->whereBetween('orders.delivered_date', [$start_date, $end_date]) // Apply date filter here
                ->groupBy('order_from')
                ->select(
                    'order_from',
                    DB::raw('SUM(order_details.price) AS total_order_price'),
                    DB::raw('COUNT(DISTINCT orders.user_id) AS total_customers'),
                    DB::raw('COUNT(DISTINCT orders.id) AS total_orders')
                )
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->get();

            foreach ($platforms as $platform) {
                $platformKey = $platform->order_from;

                if (!isset($platformData[$platformKey])) {
                    $platformData[$platformKey] = [
                        'total_order_price' => 0,
                        'total_customers' => 0,
                        'total_orders' => 0,
                    ];
                }

                $platformData[$platformKey]['total_order_price'] += $platform->total_order_price;
                $platformData[$platformKey]['total_customers'] += $platform->total_customers;
                $platformData[$platformKey]['total_orders'] += $platform->total_orders;
            }
        }
    }
    if ($type == 'excel') {
        return Excel::download(new SalesByPlatformExport(['platformData' => $platformData]), 'SalesByPlatformReport.xlsx');
    }

    return view('backend.reports.sales_by_platform', compact('platformData', 'start_date', 'end_date', 'currentYear', 'startYear', 'warehouseIds'));
}


public function single_employee_sales_performance(Request $request, $type = null)
{
    $user_id = $request->user_id;
    $warehouse = $request->warehouse;
    $start_date = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : date('Y-01-01', strtotime('-2 years'));
    $end_date = $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : date('Y-m-t');

    if (!empty($request->start_date) && !empty($request->end_date)) {
        $start_date = date('Y-m-d 00:00:00', strtotime($request->start_date));
        $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
    }

    $orders = Order::leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')->select([
            'orders.id', 
            'orders.user_id',
            DB::raw('SUM(order_details.price) AS total_price'),
            DB::raw('SUM(order_details.quantity) AS total_quantity'), 
            DB::raw('YEAR(orders.delivered_date) AS year'),
            DB::raw('MONTH(orders.delivered_date) AS month')
        ])
        ->whereNotNull('delivered_by')
        ->whereNull('canceled_by')
        ->orderBy('orders.delivered_date', 'ASC');

    if (!is_null($user_id) && $user_id !== '') {
        $orders->join('customers', 'customers.user_id', '=', 'orders.user_id')
               ->where('customers.staff_id', $user_id);
    } elseif ($user_id === '') {
        $orders->whereNull('orders.user_id');  
    }

    if (!empty($warehouse)) {
        $orders->where('orders.warehouse', $warehouse);  
    }

    $orders->whereBetween('orders.delivered_date', [$start_date, $end_date]);

    $orders = $orders->groupBy('year','month')->get();

    $months = [];
    $totals = [];
    $currentYear = Carbon::now()->year;
    $startYear = $currentYear - 2;

    $selectedUser = User::where('id', $user_id)->first();
    $selectedUserName = $selectedUser ? $selectedUser->name : "Select Employee from dropdown";

    for ($year = $currentYear; $year >= $startYear; $year--) {
        $totals[$year] = [
            'grand_total' => 0,
            'quantity' => 0
        ];
    }

    for ($month = 1; $month <= 12; $month++) {
        $monthData = [
            'name' => Carbon::create(null, $month, 1)->format('F')
        ];
        for ($year = $currentYear; $year >= $startYear; $year--) {
            $monthData[$year] = [
                'grand_total' => 0,
                'quantity' => 0
            ];
        }
        $months[] = $monthData;
    }

    foreach ($orders as $order) {
        $year = $order->year;
        $month = $order->month;

        $totals[$year]['grand_total'] += $order->total_price;
        $totals[$year]['quantity'] += $order->total_quantity;

        foreach ($months as &$monthData) {
            if ($monthData['name'] === Carbon::create(null, $month, 1)->format('F')) {
                $monthData[$year]['grand_total'] += $order->total_price;
                $monthData[$year]['quantity'] += $order->total_quantity;
            }
        }
    }

    $overallTotalSales = collect($totals)->sum(function ($total) {
        return $total['grand_total'];
    });

    if ($type == 'excel') {
        $data['orders'] = $orders;
        return Excel::download(new SingleEmployeePerformanceExport($data, $currentYear, $totals, $months), 'SingleEmployeePerformance.xlsx');
    }  

    return view('backend.reports.single_employee_sales_performance', compact(
        'orders', 'start_date', 'end_date', 'months', 'currentYear', 'totals', 'user_id', 'selectedUserName', 'overallTotalSales'
    ));
}

public function employee_sales_performance_compare(Request $request)
{
    $user_ids = $request->input('user_id', []);
    $warehouse = $request->input('warehouse', null);
    $start_date = $request->start_date ? date('Y-m-01', strtotime($request->start_date)) : date('Y-01-01', strtotime('-3 years'));
    $end_date = $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : date('Y-12-t');

    $currentYear = Carbon::now()->year;
    $startYear = $currentYear - 2;

    $users = Staff::leftJoin('users', 'staff.user_id', '=', 'users.id')
        ->leftJoin('roles', 'staff.role_id', '=', 'roles.id')
        ->select('staff.*', 'users.id as userId', 'users.name')
        ->get();

    $employeeData = [];

    foreach ($users as $user) {
        if (!empty($user_ids) && !in_array($user->userId, $user_ids)) {
            continue;
        }

        $userId = $user->userId;
        $totals = [];

        for ($year = $currentYear; $year >= $startYear; $year--) {
            $totals[$year] = [
                'grand_total' => 0,
                'quantity' => 0
            ];
        }

        $orders = Order::join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->select(
                DB::raw('SUM(order_details.price) AS total_price'),
                DB::raw('SUM(order_details.quantity) AS total_quantity'),
                DB::raw('YEAR(orders.delivered_date) AS year'),
            )
            ->whereNotNull('orders.delivered_by')
            ->whereNull('orders.canceled_by')
            ->whereBetween('orders.delivered_date', [$start_date, $end_date]);

        if (!empty($userId)) {
            $orders->join('customers', 'customers.user_id', '=', 'orders.user_id')
                   ->where('customers.staff_id', $userId);
        }

        if (!empty($warehouse)) {
            $orders->where('orders.warehouse', $warehouse);
        }

        $orders = $orders->groupBy('year')->get();

        foreach ($orders as $order) {
            $year = $order->year;
            if (isset($totals[$year])) {
                $totals[$year]['grand_total'] += $order->total_price;
                $totals[$year]['quantity'] += $order->total_quantity;
            }
        }

        $employeeData[] = [
            'name' => $user->name,
            'totals' => $totals
        ];
    }

    return view('backend.reports.employee_sales_performance_compare', compact(
        'employeeData', 'start_date', 'end_date', 'currentYear', 'users', 'user_ids'
    ));
}




public function employee_sales_performance_compare_per_year(Request $request)
{
    $year = $request->input('year', Carbon::now()->year);
    $user_ids = $request->input('user_id', []);

    $users = Staff::leftJoin('users', 'staff.user_id', '=', 'users.id')
        ->leftJoin('roles', 'staff.role_id', '=', 'roles.id')
        ->select('staff.*', 'users.id as userId', 'users.name')
        ->when(!empty($user_ids), function($query) use ($user_ids) {
            return $query->whereIn('users.id', $user_ids);
        })
        ->get();

    $employeeData = [];
    $months = range(1, 12);

    foreach ($users as $user) {
        $user_id = $user->user_id;

        $totals = array_fill(1, 12, ['grand_total' => 0, 'quantity' => 0]);

        $orders = Order::leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')
        ->join('customers', 'customers.user_id', '=', 'orders.user_id')
            ->select([
                DB::raw('SUM(order_details.price) AS total_price'),
                DB::raw('SUM(order_details.quantity) AS total_quantity'),
                DB::raw('MONTH(orders.delivered_date) AS month')
            ])
            ->where('customers.staff_id', $user_id)
            ->whereNotNull('orders.delivered_by')
            ->whereNull('orders.canceled_by')
            ->whereYear('orders.delivered_date', $year)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get();

        foreach ($orders as $order) {
            $month = $order->month;
            $totals[$month]['grand_total'] += $order->total_price;
            $totals[$month]['quantity'] += $order->total_quantity;
        }

        $employeeData[] = [
            'name' => $user->name,
            'totals' => $totals
        ];
    }

    return view('backend.reports.employee_sales_performance_compare_per_year', compact('employeeData', 'year', 'months', 'user_ids'));
}



public function detailed_sales_report(Request $request)
{
    $warehouse = $request->warehouse;
    $product_id = $request->product_id;
    $customer_type = $request->customer_type;
    $user_id = $request->user_id;
    $sort_by = null;
    $pro_sort_by = null;

    $start_date = $request->start_date ?? date('Y-m-01');
    $end_date = $request->end_date ?? date('Y-m-t');

    $salesTypes = Order::join('order_details', 'orders.id', '=', 'order_details.order_id')
        ->leftJoin('products', 'order_details.product_id', '=', 'products.id')
        ->leftJoin('customers', 'orders.user_id', '=', 'customers.user_id')
        ->leftJoin('users as executives', 'customers.staff_id', '=', 'executives.id') 
        ->leftJoin('warehouses', 'orders.warehouse', '=', 'warehouses.id')
        ->select(
            'customers.customer_type',
            'warehouses.name as warehouse_name',
            DB::raw('GROUP_CONCAT(DISTINCT executives.name ORDER BY executives.name SEPARATOR ", ") as executive_name'), 
            DB::raw('SUM(order_details.price) AS total_price'),
            DB::raw('SUM(order_details.quantity) AS total_quantity'),
            DB::raw('COUNT(DISTINCT order_details.order_id) AS num_of_sale'),
            DB::raw('COUNT(COALESCE(customers.customer_type, "Unknown")) AS total_customer_type')
        )
        ->whereNotNull('orders.delivered_by')
        ->whereNull('orders.canceled_by')
        ->groupBy('customers.customer_type', 'warehouses.name')
        ->orderBy('customers.customer_type', 'ASC');

    if (!empty($request->user_id)) {
        $salesTypes->where('executives.id', $request->user_id);
    }

    if (!empty($warehouse)) {
        $salesTypes->where('orders.warehouse', $warehouse);
    }

    if (!empty($customer_type)) {
        $salesTypes->where('customers.customer_type', $customer_type);
    }

    $salesTypes->whereBetween('orders.date', [
        strtotime($start_date),
        strtotime($end_date . ' +1 day')
    ]);

    $salesTypes = $salesTypes->where('order_details.delivery_status', 'delivered')->get();
    return view('backend.reports.detailed_sales_report', compact('salesTypes', 'sort_by', 'pro_sort_by', 'start_date', 'end_date', 'warehouse', 'customer_type'));
}



public function sale_profit_report(Request $request, $type = null)
{
    $wearhouse = $request->input('warehouse');
    $sort_by = $request->input('category_id');
    $pro_sort_by = $request->input('product_id', []);
    $start_date = $request->input('start_date', date('Y-01-01'));
    $end_date = $request->input('end_date', date('Y-12-t'));
    $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
        ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
        ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
        ->leftJoin('warehouses', 'warehouses.id', '=', 'orders.warehouse')
        ->select(
            'products.name as product_name',
            'products.purchase_price',
            'order_details.price as sale_price',
            'products.id as product_id',
            'categories.name as category_name',
            DB::raw('SUM(order_details.price * order_details.quantity) AS total_order_price'),
            DB::raw('SUM(order_details.quantity) AS total_order_qty')
        )
        ->where('order_details.delivery_status', 'delivered')
        ->whereBetween('orders.delivered_date', [$start_date, $end_date])
        ->when($wearhouse, function ($query, $wearhouse) {
            return $query->where('orders.warehouse', $wearhouse);
        })
        ->when($sort_by, function ($query, $sort_by) {
            return $query->where('products.category_id', $sort_by);
        })
        ->when(count($pro_sort_by) > 0, function ($query) use ($pro_sort_by) {
            return $query->whereIn('products.id', $pro_sort_by);
        })
        ->groupBy('products.id', 'products.name', 'products.purchase_price', 'categories.name')
        ->orderBy('products.name', 'asc')
        ->get();

    foreach ($products as $product) {
        $product_id = $product->product_id;

        $openingStocks = OpeningStock::where('product_id', $product_id)
            ->whereBetween('created_at', [$start_date, $end_date])
            ->orderBy('created_at', 'asc') 
            ->get();

        $openingStockQty = $openingStocks->sum('qty');
        $openingStockAmount = $openingStocks->sum(function($stock) {
            return $stock->qty * $stock->price;
        });

        $purchases = PurchaseDetail::whereHas('purchase', function($query) use ($start_date, $end_date) {
            $query->where('status', 2)->whereBetween('approved_date', [$start_date, $end_date]);
        })->where('product_id', $product_id)
          ->orderBy('created_at', 'asc') 
          ->get();

        $purchaseQty = 0;
        $purchaseAmount = 0;
        $remainingQtyToAllocate = $product->total_order_qty;

        foreach ($purchases as $purchase) {
            if ($remainingQtyToAllocate <= 0) break;

            $allocateQty = min($purchase->qty, $remainingQtyToAllocate);
            $purchaseAmount += $allocateQty * $purchase->price;
            $purchaseQty += $allocateQty;
            $remainingQtyToAllocate -= $allocateQty;
        }

        $transfersIn = Transfer::where('status', 'Approved')
            ->where('product_id', $product_id)
            ->whereBetween('date', [$start_date, $end_date])
            ->get();

        $transferInQty = $transfersIn->sum('qty');
        $transferInAmount = $transfersIn->sum(function($transfer) {
            return $transfer->qty * $transfer->price;
        });

        $transfersOut = Transfer::where('status', 'Approved')
            ->where('product_id', $product_id)
            ->whereBetween('date', [$start_date, $end_date])
            ->get();

        $transferOutQty = $transfersOut->sum('qty');
        $transferOutAmount = $transfersOut->sum(function($transfer) {
            return $transfer->qty * $transfer->price;
        });

        $damages = Damage::where('status', 'Approved')
            ->where('product_id', $product_id)
            ->whereBetween('date', [$start_date, $end_date])
            ->get();

        $damageQty = $damages->sum('qty');
        $damageAmount = $damages->sum('total_amount');

        $closingStockQty = $openingStockQty + $purchaseQty + $transferInQty - $transferOutQty - $product->total_order_qty;
        $closingStockAmount = ($closingStockQty * $product->purchase_price) - $damageAmount;

        $product->profit_loss = $product->total_order_price - 
            ($openingStockAmount + $purchaseAmount + $transferInAmount - $damageAmount - $transferOutAmount - $closingStockAmount);
    }

    if ($type == 'excel') {
        $data['products'] = $products;
        return Excel::download(new SalesProfitExport($data), 'product_wise_purchase.xlsx');
    }

    return view('backend.reports.sale_profit_report', compact('products', 'sort_by', 'pro_sort_by', 'start_date', 'end_date', 'wearhouse'));
}


public function daily_sale_profit_report(Request $request)
{
    $wearhouse = $request->warehouse;
    $sort_by = $request->category_id;
    $pro_sort_by = $request->product_id;

    $start_date = $request->start_date ? date('Y-m-d 00:00:00', strtotime($request->start_date)) : null;
    $end_date = $request->end_date ? date('Y-m-d 23:59:59', strtotime($request->end_date)) : null;

    if (!$start_date && !$end_date) {
        $start_date = date('Y-m-01 00:00:00');
        $end_date = date('Y-m-t 23:59:59');
    }

    $productsQuery = Product::join('categories', 'products.category_id', '=', 'categories.id')
        ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
        ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
        ->where('num_of_sale', '>', 0)
        ->where('order_details.delivery_status', 'delivered')
        ->select(
            'products.id as product_id',
            'products.name as product_name',
            'products.purchase_price',
            'categories.name as category_name',
            'orders.delivered_date',
            'order_details.price',
            DB::raw('sum(order_details.price) AS total_order_price'),
            DB::raw('sum(quantity) AS quantity')
        )
        ->groupBy('products.id')
        ->orderBy('products.name', 'desc');

    if (!empty($sort_by)) {
        $productsQuery->where('products.category_id', $sort_by);
    }

    if (!empty($pro_sort_by)) {
        $productsQuery->where('products.id', $pro_sort_by);
    }

    if (!empty($wearhouse)) {
        $productsQuery->where('orders.warehouse', $wearhouse);
    }

    if ($start_date && $end_date) {
        $productsQuery->whereBetween('orders.delivered_date', [$start_date, $end_date]);
    }

    $products = $productsQuery->get();

    foreach ($products as $product) {
        $product_id = $product->product_id;

        $openingStocks = OpeningStock::where('product_id', $product_id)
            ->whereBetween('created_at', [$start_date, $end_date])
            ->orderBy('created_at', 'asc')
            ->get();

        $openingStockQty = 0;
        $openingStockAmount = 0;
        foreach ($openingStocks as $openStock) {
            $openingStockQty += $openStock->qty;
            $openingStockAmount += $openStock->qty * $openStock->price;
        }

        $product->opening_stock_qty = $openingStockQty;
        $product->opening_stock_amount = $openingStockAmount;

        $purchases = PurchaseDetail::whereHas('purchase', function($query) use ($start_date, $end_date) {
            $query->where('status', 2)->whereBetween('approved_date', [$start_date, $end_date]);
        })->where('product_id', $product_id)
        ->orderBy('created_at', 'asc')
        ->get();

        $purchaseQty = 0;
        $purchaseAmount = 0;
        $remainingQtyToAllocate = $product->quantity;

        foreach ($purchases as $purchase) {
            if ($remainingQtyToAllocate <= 0) break;

            $allocateQty = min($purchase->qty, $remainingQtyToAllocate);
            $purchaseAmount += $allocateQty * $purchase->price;
            $purchaseQty += $allocateQty;
            $remainingQtyToAllocate -= $allocateQty;
        }

        $product->purchase_qty = $purchaseQty;
        $product->purchase_amount = $purchaseAmount;

        $transfersIn = Transfer::where('status', 'Approved')
            ->where('product_id', $product_id)
            ->whereBetween('date', [$start_date, $end_date])
            ->get();

        $transferInQty = 0;
        $transferInAmount = 0;
        foreach ($transfersIn as $transferIn) {
            $transferInQty += $transferIn->qty;
            $transferInAmount += $transferIn->qty * $transferIn->price;
        }

        $product->transfer_in_qty = $transferInQty;
        $product->transfer_in_amount = $transferInAmount;

        $transfersOut = Transfer::where('status', 'Approved')
            ->where('product_id', $product_id)
            ->whereBetween('date', [$start_date, $end_date])
            ->get();

        $transferOutQty = 0;
        $transferOutAmount = 0;
        foreach ($transfersOut as $transferOut) {
            $transferOutQty += $transferOut->qty;
            $transferOutAmount += $transferOut->qty * $transferOut->price;
        }

        $product->transfer_out_qty = $transferOutQty;
        $product->transfer_out_amount = $transferOutAmount;

        $damages = Damage::where('status', 'Approved')
            ->where('product_id', $product_id)
            ->whereBetween('date', [$start_date, $end_date])
            ->get();

        $damageQty = 0;
        $damageAmount = 0;
        foreach ($damages as $damage) {
            $damageQty += $damage->qty;
            $damageAmount += $damage->total_amount;
        }

        $product->damage_qty = $damageQty;
        $product->damage_amount = $damageAmount;

        $closingStockQty = $openingStockQty + $product->purchase_qty + $product->transfer_in_qty - $product->transfer_out_qty - $product->quantity;
        $closingStockAmount = ($closingStockQty * $product->purchase_price) - $product->damage_amount;

        $product->closing_stock_qty = $closingStockQty;
        $product->closing_stock_amount = $closingStockAmount;

        $product->profit_loss = $product->total_order_price - 
            ($openingStockAmount + $product->purchase_amount + $product->transfer_in_amount - $product->damage_amount - $product->transfer_out_amount - $closingStockAmount);
    }

    return view('backend.reports.daily_sale_profit_report', compact('products', 'sort_by', 'pro_sort_by', 'start_date', 'end_date', 'wearhouse'));
}


    public function seller_sale_report(Request $request)
    {
        $sort_by = null;
        $sellers = Seller::orderBy('created_at', 'asc');
        if ($request->has('verification_status')) {
            $sort_by = $request->verification_status;
            $sellers = $sellers->where('verification_status', $sort_by);
        }
        $sellers = $sellers->paginate(10);
        return view('backend.reports.seller_sale_report', compact('sellers', 'sort_by'));
    }



    public function user_search_report(Request $request)
    {
        $searches = Search::orderBy('count', 'desc')->paginate(10);
        return view('backend.reports.user_search_report', compact('searches'));
    }



    public function coupon_report(Request $request)
    {
        $sort_by = null;
        $status = null;
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        if (empty($request->start_date))
            $request->start_date = $start_date;
        if (empty($request->end_date))
            $request->end_date = $end_date;
        $cust = array();
        $orders = array();

        //$coupons = CouponUsage::join('coupons','coupons.id','=','coupon_usages.coupon_id')->join('orders','orders.id','=','coupon_usages.order_id')->where('coupon_usages.user_id', Auth::user()->id)->select('coupons.code as coupon_code','coupon_usages.coupon_id','orders.*')->orderBy('orders.date', 'desc')->get();
        $coupons = Coupon::get();
        $customers = CouponUsage::join('users', 'coupon_usages.user_id', '=', 'users.id')->select('users.id', 'users.name')->groupBy('users.id')->get();

        $sql = "SELECT u.name,c.user_id,c.customer_id as customer_no,coupons.code,sum(orders.coupon_discount) as coupon_discount,count(cu.id) as usagee 
        FROM coupon_usages cu LEFT JOIN coupons ON coupons.id=cu.coupon_id 
        LEFT JOIN orders ON orders.id=cu.order_id 
        LEFT JOIN users u ON cu.user_id = u.id 
        LEFT JOIN customers c ON c.user_id = u.id ";
        $where = ' where 1=1 ';
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d', strtotime($request->end_date));
            $where .= " and (cu.created_at between '" . $start_date . "' and '" . $end_date . "') ";
        }
        $customer_id = '';
        $coupon_id = '';
        if (!empty($request->coupon_id)) {
            $where .= " and cu.coupon_id=" . $request->coupon_id;
            $coupon_id = $request->coupon_id;
        }
        if (!empty($request->customer_id)) {
            $where .= " and u.id=" . $request->customer_id;
            $customer_id = $request->customer_id;
        }
        $sql .= $where . " 	
        GROUP BY cu.user_id,cu.coupon_id
        order by u.name asc";
        $data = DB::select($sql);
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));
        return view('backend.reports.coupon', compact('data', 'customers', 'coupons', 'start_date', 'end_date', 'customer_id', 'coupon_id'));
    }

    public function referral_report(Request $request)
    {
        // $orders = Order::get();
        
        // foreach($orders as $order)
        // {
        //     if($order->coupon_discount > 0.0){
        //         $order_details = OrderDetail::where('order_id',$order->id)->get();
        //         $totalQuantity = collect($order_details)->sum(function($order_details) {
        //             return (int) $order_details['quantity'];
        //         });
        //         $PerProductDiscount = $order->coupon_discount/$totalQuantity;
        //         foreach($order_details as $order_detail){
        //             $order_detail->coupon_discount = $PerProductDiscount * $order_detail->quantity;
        //             $order_detail->save();
        //         }
        //     }

        // }

        $orders = Order::with('orderDetails')->where('coupon_discount', '>', 0.0)->get();

        foreach ($orders as $order) {
            $order_details = $order->orderDetails;
            $totalQuantity = $order_details->sum('quantity');
            
            if ($totalQuantity > 0) {
                $PerProductDiscount = $order->coupon_discount / $totalQuantity;
                $updatedOrderDetails = [];

                foreach ($order_details as $order_detail) {
                    $order_detail->coupon_discount = $PerProductDiscount * $order_detail->quantity;
                    $updatedOrderDetails[] = $order_detail->toArray();
                }
                OrderDetail::upsert($updatedOrderDetails, ['id'], ['coupon_discount']);
            }
        }

        $sort_by = null;
        $status = null;
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        if (empty($request->start_date))
            $request->start_date = $start_date;
        if (empty($request->end_date))
            $request->end_date = $end_date;
        $cust = array();
        $orders = array();

        //$coupons = CouponUsage::join('coupons','coupons.id','=','coupon_usages.coupon_id')->join('orders','orders.id','=','coupon_usages.order_id')->where('coupon_usages.user_id', Auth::user()->id)->select('coupons.code as coupon_code','coupon_usages.coupon_id','orders.*')->orderBy('orders.date', 'desc')->get();
        //$coupons = Coupon::get();
        $customers = Referr_code::join('users', 'referr_codes.user_id', '=', 'users.id')->orWhereNotNull('used_by')->select('users.id', 'users.name')->groupBy('users.id')->get();

        $sql = "SELECT u.name,rc.user_id,rc.used_by,c.customer_id,count(*) as qty,group_concat(rc.used_by) as ids FROM `referr_codes` as rc join users as u on rc.user_id=u.id join customers as c on rc.user_id=c.user_id WHERE rc.used_by IS NOT NULL and u.id IS NOT NULL";

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
            $where = " and (rc.created_at between '" . $start_date . "' and '" . $end_date . "') ";
        }
        $customer_id = '';
        $coupon_id = '';

        if (!empty($request->customer_id)) {
            $where .= " and rc.user_id=" . $request->customer_id;
            $customer_id = $request->customer_id;
        }
        $sql .= $where . " 	
        group by rc.user_id
        order by u.name asc";
        $data = DB::select($sql);

        $amt = \App\Models\AffiliateOption::where('type', 'download_app')->first()->percentage;
        foreach ($data as $key => $each) {
            $gtotal = 0;
            $gqty = 0;
            $ggqty = 0;
            foreach (explode(',', $each->ids) as $iid) {
                $o = DB::select("select o.grand_total from orders as o join order_details od on o.id=od.order_id where user_id=" . $iid . " and od.delivery_status!='cancel' group by o.id order by o.id asc");
                if (!empty($o)) {
                    $gtotal += $o[0]->grand_total;
                    if ($o[0]->grand_total >= 1000)
                        $ggqty += 1;
                }
                if (User::where('id', $iid)->first())
                    $gqty += 1;
            }
            $data[$key]->gtotal = $gtotal;
            $data[$key]->qty = $gqty;
            $data[$key]->gqty = $ggqty;
        }
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));
        return view('backend.reports.referral', compact('data', 'customers', 'start_date', 'end_date', 'customer_id', 'amt'));
    }
    public function referral_details(Request $request)
    {
        $user_id = $request->user_id;
        if (empty($user_id))
            return Redirect::back();

        $cust = User::where('id', $user_id)->first();
        $sql = "SELECT u.name,u.phone,(select address from addresses where user_id=u.id order by id asc limit 1) as address,rc.used_by,c.customer_id FROM `referr_codes` as rc join users as u on rc.used_by=u.id join customers as c on rc.used_by=c.user_id WHERE rc.user_id=" . $user_id . " and rc.used_by IS NOT NULL and u.id is not null";
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
            $sql .= " and (rc.created_at between '" . $start_date . "' and '" . $end_date . "') ";
        }
        $sql .= " order by u.name asc";
        $data = DB::select($sql);
        foreach ($data as $key => $each) {
            $sql = "select o.grand_total from orders as o join order_details od on o.id=od.order_id where o.user_id=" . $each->used_by . " and od.delivery_status!='cancel' group by o.id order by o.id asc limit 1";
            $data[$key]->order = DB::select($sql);
        }
        return view('backend.reports.referral_details', compact('cust', 'data'));
    }

    public function customer_ledger_fix(Request $request)
    {
        Customer_ledger::whereBetween('date', ['2021-09-01 00:00:00', '2022-10-31 00:00:00'])->delete();
        $orders = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->select('orders.*', DB::raw('SUM(order_details.discount) AS total_discount'))
            ->whereNotNull('orders.user_id')
            ->whereBetween('orders.created_at', ['2021-09-01 00:00:00', '2022-10-31 00:00:00']);
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

    public function supplier_ledger_fix(Request $request)
    {
        Supplier_ledger::truncate();
        $orders = Purchase::get();

        foreach ($orders as $order) {
            $total = $order->total_value;
            $paid = 0;
            if (!empty($order->payment_amount)) {
                $paid = $order->payment_amount;
            }

            $supplier_ledger = new Supplier_ledger();
            $supplier_ledger->supplier_id = $order->supplier_id;
            $supplier_ledger->purchase_id = $order->id;
            $supplier_ledger->descriptions = 'Purchase Order';
            $supplier_ledger->type = 'Purchase';
            $supplier_ledger->debit = $total;
            $supplier_ledger->credit = 0;
            $supplier_ledger->date = $order->date;
            $supplier_ledger->save();

            if (!empty($paid)) {

                $pmt = DB::table('supplier_ledger_old')->where('supplier_id', $order->supplier_id)->where('purchase_id', $order->id)->where('type', 'Payment')->get();
                if (count($pmt) > 0) {
                    $datee = $pmt[0]->date;
                } else {
                    $datee = $order->date;
                }
                $supplier_ledger = new Supplier_ledger();
                $supplier_ledger->supplier_id = $order->supplier_id;
                $supplier_ledger->purchase_id = $order->id;
                $supplier_ledger->descriptions = 'Purchase Order';
                $supplier_ledger->type = 'Payment';
                $supplier_ledger->debit = 0;
                $supplier_ledger->credit = $paid;
                $supplier_ledger->date = $datee;
                $supplier_ledger->save();
            }
        }
    }


    public function wish_report(Request $request)
    {
        $products = DB::table('wishlists')
            ->join('users', 'wishlists.user_id', '=', 'users.id')
            ->join('products', 'wishlists.product_id', '=', 'products.id')
            ->select('products.name', 'wishlists.product_id', DB::raw('count(wishlists.user_id) AS total_customer'), 'users.name as uname',)
            ->groupByRaw('products.name')
            ->get();
        return view('backend.reports.wish_report', compact('products'));
    }

    public function customerwishlish($productid)
    {
        $wisher = DB::table('wishlists')
            ->join('users', 'wishlists.user_id', '=', 'users.id')
            ->join('products', 'wishlists.product_id', '=', 'products.id')
            ->join('customers', 'wishlists.user_id', '=', 'customers.user_id')
            ->select('products.name', 'products.id', 'users.name as uname', 'customers.customer_id', 'customers.user_id')
            ->where('product_id', $productid)
            ->get();

        return view('backend.reports.wish_customer', compact('wisher'));
    }
    

    public function group_child_product_report(Request $request, $type = null)
{
    $query = Product::query();

    if ($request->has('parent_id') && $request->parent_id != null) {
        $query->where('parent_id', $request->parent_id);
    }

    $children = $query->whereNotNull('parent_id')
                      ->get()
                      ->groupBy('parent_id');

    $parentIdsWithChildren = $children->keys();
    $parentsQuery = Product::whereIn('id', $parentIdsWithChildren);

    if ($request->has('parent_id') && $request->parent_id != null) {
        $parentsQuery->where('id', $request->parent_id);
    }

    $parents = $parentsQuery->orderBy('name', 'ASC')->get();

    if ($type == 'excel') {
        return Excel::download(new ParentChildProductListExport(['parents' => $parents, 'children' => $children]), 'ParentChildProductListExport.xlsx');
    }

    return view('backend.reports.group_child_product_report', compact('parents', 'children'));
}

public function group_product_salesReport(Request $request)
{
    $warehouse = $request->warehouse;
    $start_date = date('Y-m-01');
    $end_date = date('Y-m-t');
    $date = $request->date;
    $month = $request->month;
    $month_year = null;
    $year = $request->year;
    $sort_search = null;
    $user_id = $request->user_id;
    
    $orders = Order::select([
            'orders.id', 
            'orders.user_id', 
            'orders.code', 
            'orders.payment_details', 
            'orders.grand_total', 
            'orders.delivered_date', 
            'orders.guest_id', 
            'orders.shipping_address'
        ])
        ->join('order_details', 'orders.id', '=', 'order_details.order_id') 
        ->join('products', 'products.id', '=', 'order_details.product_id') 
        ->join('group_products', 'group_products.group_product_id', '=', 'products.id') 
        ->where('products.is_group_product', 1)
        ->whereNotNull('orders.delivered_by')
        ->whereNull('orders.canceled_by') 
        ->orderBy('orders.delivered_date', 'ASC'); 

    if ($request->has('search')) {
        $sort_search = $request->search;
        $orders->where('orders.code', 'like', '%' . $sort_search . '%');
    }
   
    if (!empty($request->start_date) && !empty($request->end_date)) {
        $start_date = date('Y-m-d 00:00:00', strtotime($request->start_date));
        $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
    }
    
    if (!empty($request->month)) {
        $month_year = date('Y', strtotime($request->month));
        $month = date('m', strtotime($request->month));

        $start_date = date('Y-m-01', strtotime("$month_year-$month-01"));
        $end_date = date('Y-m-t', strtotime("$month_year-$month-01"));
    }

    if (!empty($request->year)) {
        $start_date = date('Y-m-01', strtotime("$year-01-01"));
        $end_date = date('Y-m-t', strtotime("$year-12-31"));
    }
  
    if (!empty($user_id)) {
        $orders->join('customers', 'customers.user_id', '=', 'orders.user_id')
               ->where('customers.staff_id', $user_id);
    }
   
    if (!empty($warehouse)) {
        $orders->where('orders.warehouse', $warehouse); 
    }

    $orders->whereBetween('orders.delivered_date', [$start_date, $end_date]);

    $orders = $orders->get();

    $start_date = date('Y-m-d', strtotime($start_date));
    $end_date = date('Y-m-d', strtotime($end_date));

    return view('backend.reports.group_product_sales', compact('orders', 'sort_search', 'date', 'start_date', 'end_date', 'warehouse', 'user_id', 'month', 'year', 'month_year'));
}




public function group_product_report(Request $request, $type = null)
{
    $selectedGroupIds = $request->input('group_id', []);

    $groupProducts = \App\Models\Product::where('is_group_product', 1)
        ->when(!empty($selectedGroupIds), function ($query) use ($selectedGroupIds) {
            return $query->whereIn('id', $selectedGroupIds);
        })
        ->orderBy('name', 'ASC')
        ->get();

    $productDetails = DB::table('group_products')
        ->join('products', 'group_products.product_id', '=', 'products.id')
        ->select('group_products.*', 'products.name as product_name', 'products.unit_price as product_price')
        ->when(!empty($selectedGroupIds), function ($query) use ($selectedGroupIds) {
            return $query->whereIn('group_products.group_product_id', $selectedGroupIds);
        })
        ->get()
        ->groupBy('group_product_id');

    if ($type == 'excel') {
        return Excel::download(new GroupProductListExport(['groupProducts' => $groupProducts, 'productDetails' => $productDetails]), 'GroupProductListExport.xlsx');
    }

    return view('backend.reports.group_product_report', compact('groupProducts', 'productDetails', 'selectedGroupIds'));
}







    public function operation_manager_stock_report(Request $request)
    {
        $wearhouse = Warehouse::get();
        $sort_by = null;
        $pro_sort_by = null;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $wearhouse_id = $wearhouse[0]->id;

        $productsQuery = Product::join('product_stocks', 'products.id', '=', 'product_stocks.product_id')->orderBy('product_stocks.qty', 'desc');

        if (!empty($request->product_id)) {
            $pro_sort_by = $request->product_id;
            $productsQuery = $productsQuery->where('products.id', $pro_sort_by);
        }

        if ($request->has('category_id') && !empty($request->category_id)) {
            $sort_by = $request->category_id;
            $productsQuery = $productsQuery->where('product_stocks.wearhouse_id', $sort_by);
        }

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $productsQuery = $productsQuery->whereBetween('products.expiry_date', [$start_date, $end_date]);
        } else {
        }

        // If not filtering by category, select products and sum the quantity
        $products = $productsQuery->select('products.*', DB::raw('sum(product_stocks.qty) as qty'))->groupBy('product_stocks.product_id')->get();

        return view('backend.staff_panel.operation_manager.stock_report', compact('products', 'sort_by', 'pro_sort_by', 'wearhouse', 'start_date', 'end_date'));
    }

    public function salesReport(Request $request)
    {
        $warehouse = $request->warehouse;
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $date = $request->date;
        $month = $request->month;
        $month_year = null;
        $year = $request->year;
        $sort_search = null;
        $user_id = $request->user_id;
    
        // Select columns with explicit table names to avoid ambiguity
        $orders = Order::leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')
        ->select([
                'orders.id', 
                'orders.user_id', 
                'orders.code', 
                'orders.payment_details',
                'orders.delivered_date', 
                'orders.guest_id', 
                'orders.shipping_address',
                DB::raw('SUM(order_details.price - order_details.coupon_discount) AS grand_total')
            ])
            ->whereNotNull('delivered_by')
            ->whereNull('canceled_by')
            ->groupBy('orders.code')
            ->orderBy('orders.delivered_date', 'ASC');  // Changed from 'date' to 'delivered_date'
    
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders->where('orders.code', 'like', '%' . $sort_search . '%');
        }
    
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d 00:00:00', strtotime($request->start_date));
            $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
        }
    
        if (!empty($request->month)) {
            $month_year = date('Y', strtotime($request->month));
            $month = date('m', strtotime($request->month));
    
            $start_date = date('Y-m-01', strtotime("$month_year-$month-01"));
            $end_date = date('Y-m-t', strtotime("$month_year-$month-01"));
        }
    
        if (!empty($request->year)) {
            $start_date = date('Y-m-01', strtotime("$year-01-01"));
            $end_date = date('Y-m-t', strtotime("$year-12-31"));
        }
    
        if (!empty($user_id)) {
            $orders->join('customers', 'customers.user_id', '=', 'orders.user_id')
                   ->where('customers.staff_id', $user_id);
        }
    
        if (!empty($warehouse)) {
            $orders->where('orders.warehouse', $warehouse);  // Explicit table reference
        }
    
        // Filter by delivered_date
        $orders->whereBetween('orders.delivered_date', [$start_date, $end_date]);
    
        $orders = $orders->get();
    
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));
    
        return view('backend.reports.sales', compact(
            'orders', 'sort_search', 'date', 'start_date', 'end_date', 'warehouse', 'user_id', 'month', 'year', 'month_year'
        ));
    }
    

    public function POSsalesReport(Request $request)
    {

        $wearhouse = $request->warehouse;
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $date = $request->date;
        $month = $request->month;
        $month_year = null;
        $year = $request->year;
        $sort_search = null;
        $user_id = $request->user_id;

        $orders = Order::select('orders.*')
            ->where('orders.order_from','POS')
            ->orderBy('orders.date', 'ASC');


        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('orders.code', 'like', '%' . $sort_search . '%');
        }

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d 00:00:00', strtotime($request->start_date));
            $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
        }
        if (!empty($request->month)) {
            $month_year = date('Y', strtotime($request->month));
            $month = date('m', strtotime($request->month));
        
            $start_date = date('Y-m-01', strtotime("$month_year-$month-01"));
            $end_date = date('Y-m-t', strtotime("$month_year-$month-01"));
        }
        
        
        if (!empty($request->year)) {
            $start_date = date('Y-m-01', strtotime("$year-01-01"));
            $end_date = date('Y-m-t', strtotime("$year-12-31"));
        }

        // echo $start_date.' '.$end_date;
        // exit;

        if (!empty($user_id)) {
            $orders = $orders->select('orders.*', 'customers.staff_id')
                ->join('customers', 'customers.user_id', '=', 'orders.user_id')
                ->where('customers.staff_id', $user_id)
                ->whereBetween('orders.delivered_date', [$start_date, $end_date]);
        } else {
            $orders = $orders->whereBetween('orders.delivered_date', [$start_date, $end_date]);
        }

        if (!empty($wearhouse)) {
            $orders = $orders->whereBetween('orders.delivered_date', [$start_date, $end_date])
                ->where('orders.warehouse', $wearhouse);
        } else {
            $orders = $orders->whereBetween('orders.delivered_date', [$start_date, $end_date]);
        }

        $orders = $orders->get();

        // dd($orders, $wearhouse);
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));

        return view('backend.reports.pos_sales_report', compact('orders', 'sort_search', 'date', 'start_date', 'end_date', 'wearhouse', 'user_id'));
    }

    public function PlatformSalesReport(Request $request ,$type=null)
    {
        
        $wearhouse = $request->warehouse;
        $order_from = $request->order_from;
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $date = $request->date;
        $month = $request->month;
        $month_year = null;
        $year = $request->year;
        $sort_search = null;
        $user_id = $request->user_id;
        $warehousearray = getWearhouseBuUserId(Auth::user()->id);

        $orders = Order::select('orders.*')->leftJoin('order_details', 'order_details.order_id', '=', 'orders.id')
            ->where('order_details.delivery_status','delivered')
            ->groupBy('orders.code')->orderBy('orders.date', 'ASC');

        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('orders.code', 'like', '%' . $sort_search . '%');
        }


        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d 00:00:00', strtotime($request->start_date));
            $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
        }
        if (!empty($request->month)) {
            $month_year = date('Y', strtotime($request->month));
            $month = date('m', strtotime($request->month));
        
            $start_date = date('Y-m-01', strtotime("$month_year-$month-01"));
            $end_date = date('Y-m-t', strtotime("$month_year-$month-01"));
        }
        
        
        if (!empty($request->year)) {
            $start_date = date('Y-m-01', strtotime("$year-01-01"));
            $end_date = date('Y-m-t', strtotime("$year-12-31"));
        }

        if (!empty($user_id)) {
            $orders = $orders->select('orders.*', 'customers.staff_id')
                ->join('customers', 'customers.user_id', '=', 'orders.user_id')
                ->where('customers.staff_id', $user_id)
                // ->where('orders.delivery_status', 'delivered')
                ->whereBetween('orders.created_at', [$start_date, $end_date]);
        } else {
            $orders = $orders->whereBetween('orders.created_at', [$start_date, $end_date]);
        }

        if (!empty($wearhouse)) {
            if(in_array(Auth::user()->id, [9, 135, 137, 138])){
            $orders = $orders->whereBetween('orders.created_at', [$start_date, $end_date])
                ->where('orders.warehouse', $wearhouse);
            }else{
                $orders = $orders->whereBetween('orders.created_at', [$start_date, $end_date])
                ->where('orders.warehouse', $warehousearray);
            }
        } else {
            if(Auth::user()->id == 9 || Auth::user()->id == 135 || Auth::user()->id == 137 || Auth::user()->id == 138){
            $orders = $orders->whereBetween('orders.created_at', [$start_date, $end_date]);
            }else{
                $orders = $orders->whereBetween('orders.created_at', [$start_date, $end_date])
                ->where('orders.warehouse', $warehousearray);
            }
        }

        if (!empty($order_from)) {
            $orders = $orders->whereBetween('orders.created_at', [$start_date, $end_date])
                ->where('orders.order_from', $order_from );
        } else {
            $orders = $orders->whereBetween('orders.created_at', [$start_date, $end_date]);
        }

        $orders = $orders->get();
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));

        if($type == 'excel'){
            $data['orders'] = $orders;
            return Excel::download(new FlatfromSaleExport($data), 'flatfromsale.xlsx');
        }

        return view('backend.reports.platform_sales_report', compact('orders', 'sort_search', 'date', 'start_date', 'end_date', 'wearhouse', 'user_id', 'order_from','warehousearray'));
    }

    
    public function operation_sales_report(Request $request)
    {
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        if (!$warehousearray) {
            $warehousearray = array();
        }
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $date = $request->date;
        $sort_search = null;

        $orders = Order::orderBy('orders.created_at', 'ASC');

        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('orders.code', 'like', '%' . $sort_search . '%');
        }
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d 00:00:00', strtotime($request->start_date));
            $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
        }

        $orders = $orders->whereBetween('orders.created_at', [$start_date, $end_date])->WhereIn('warehouse', $warehousearray)->groupBy('orders.id');
        $orders = $orders->get();
        
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));

        return view('backend.staff_panel.operation_manager.sales_report', compact('orders', 'sort_search', 'date', 'start_date', 'end_date'));
    }

    public function order_status_changer_report(Request $request)
    {
        $from_date = date('Y-m-01');
        $to_date = date('Y-m-t');
        $sort_search = $request->search;
        $user_name = $request->user_name;  
        $order_status = $request->order_status;  

        if (!empty($request->from_date) && !empty($request->to_date)) {
            $from_date = date('Y-m-d', strtotime($request->from_date));
            $to_date = date('Y-m-d', strtotime($request->to_date));
        }

        if (!empty($sort_search)) {
            $order_status_logs = OrderStatusLog::whereBetween('order_status_logs.created_at', [$from_date, $to_date])
                ->where('order_id', $sort_search);
        } else {
            $order_status_logs = OrderStatusLog::whereBetween('order_status_logs.created_at', [$from_date, $to_date]);
        }

        if (!empty($request->user_name)) {
       
            $order_status_logs = $order_status_logs->join('users','order_status_logs.user_id','=', 'users.id')->where('users.id', $user_name);
        }
        if (!empty($request->order_status)) {
            $order_status_logs = $order_status_logs->where('order_status_logs.order_status', $order_status);
        }

        $order_status_logs = $order_status_logs->get();
        //dd($order_status_logs);

        return view('backend.reports.order_status_changer_reports', compact('order_status_logs', 'from_date', 'to_date', 'sort_search','user_name','order_status'));
    }
    



    public function operation_customer_report(Request $request)
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
        //$customers->where('staff_id', Auth::user()->id);
        $customers = $customers->paginate(15);
        return view('backend.staff_panel.operation_manager.customer_report', compact('customers', 'sort_search', 'start_date', 'end_date',));
    }

    public function transfer_list_report(Request $request, $type = null)
    {
        $wearhouse = $request->warehouse;
        $to_wearhouse = $request->to_warehouse;
        $product_ids = $request->product_id;
        $sort_by = null;
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
    
        if (!empty($request->start_date)) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        }
    
        $transfer = Transfer::leftJoin('products', 'transfers.product_id', '=', 'products.id')
            ->whereBetween('transfers.date', [$start_date, $end_date])
            ->orderBy('transfers.date', 'asc');
    
        if (!empty($wearhouse)) {
            $transfer = $transfer->where('transfers.from_wearhouse_id', $wearhouse);
        }
        if (!empty($to_wearhouse)) {
            $transfer = $transfer->where('transfers.to_wearhouse_id', $to_wearhouse);
        }
        if (!empty($product_ids)) {
            $transfer = $transfer->whereIn('transfers.product_id', $product_ids);
        }
    
        $transfers = $transfer->select('transfers.*', 'products.purchase_price')->get();
        
        if ($type == 'excel') {
            return Excel::download(new TransferListExport(['transfers' => $transfers]), 'transferListExport.xlsx');
        }

        return view('backend.reports.transfer_list', compact('transfers', 'wearhouse', 'to_wearhouse', 'product_ids', 'sort_by', 'start_date', 'end_date'));
    }


    public function fifo_transfer_list_report(Request $request, $type = null)
{
    $warehouse = $request->warehouse;
    $to_warehouse = $request->to_warehouse;
    $product_ids = $request->product_id;
    $sort_by = null;
    $start_date = date('Y-m-01');
    $end_date = date('Y-m-t');

    if (!empty($request->start_date)) {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
    }

    $transfer = Transfer::leftJoin('products', 'transfers.product_id', '=', 'products.id')
        ->whereBetween('transfers.date', [$start_date, $end_date])
        ->orderBy('transfers.date', 'asc');

    if (!empty($warehouse)) {
        $transfer = $transfer->where('transfers.from_warehouse_id', $warehouse);
    }
    if (!empty($to_warehouse)) {
        $transfer = $transfer->where('transfers.to_warehouse_id', $to_warehouse);
    }
    if (!empty($product_ids)) {
        $transfer = $transfer->whereIn('transfers.product_id', $product_ids);
    }

    $transfers = $transfer->select('transfers.*', 'products.purchase_price')->get();

    // Apply FIFO logic
    foreach ($transfers as $key => $transfer) {
        // Get related product data for FIFO calculation
        $product_id = $transfer->product_id;
        $warehouse_id = $transfer->from_warehouse_id;

        // Fetch purchase items for the product (sorted by date for FIFO)
        // $purchases = PurchaseDetail::where('product_id', $product_id)
        //     ->where('wearhouse_id', $warehouse_id)
        //     ->whereBetween('date', [$start_date, $end_date])
        //     ->orderBy('date', 'asc')
        //     ->get();

            $purchases = PurchaseDetail::select('purchase_details.id', 'purchase_details.id', 'purchase_details.product_id', 'purchase_details.wearhouse_id', 'purchase_details.qty', 'purchase_details.price', 'purchase_details.amount', 'purchases.status', 'purchases.date')    
            ->leftjoin('purchases', 'purchases.id', '=', 'purchase_details.id')
            ->where('purchases.status', 2)
            ->where('purchase_details.product_id', $product_id)
            ->where('purchase_details.wearhouse_id', $warehouse_id)
            ->whereBetween('purchases.date', array($start_date, $end_date))
            ->get();

        $remaining_transfer_qty = $transfer->qty;
        $closing_stock_qty = 0;
        $closing_stock_amount = 0;

        foreach ($purchases as $purchase) {
            if ($remaining_transfer_qty > 0) {
                if ($purchase->qty >= $remaining_transfer_qty) {
                    $purchase->qty -= $remaining_transfer_qty;
                    $remaining_transfer_qty = 0; // All transfer accounted for
                } else {
                    $remaining_transfer_qty -= $purchase->qty;
                    $purchase->qty = 0; // Deplete this purchase item
                }
            }

            if ($purchase->qty > 0) {
                $closing_stock_qty += $purchase->qty;
                $closing_stock_amount += $purchase->qty * $purchase->price;
            }
        }

        // Attach FIFO-calculated closing stock to the transfer record
        $transfer->closing_stock_qty = $closing_stock_qty;
        $transfer->closing_stock_amount = $closing_stock_amount;
    }

    if ($type == 'excel') {
        return Excel::download(new TransferListExport(['transfers' => $transfers]), 'transferListExport.xlsx');
    }

    return view('backend.reports.fifo_transfer_list', compact('transfers', 'warehouse', 'to_warehouse', 'product_ids', 'sort_by', 'start_date', 'end_date'));
}


    
    

    public function product_transfer_summery(Request $request, $type = null)
{
    $wearhouse = $request->warehouse;
    $to_wearhouse = $request->to_warehouse;
    $product_ids = $request->product_id;
    $start_date = $request->start_date ?? date('Y-m-01');
    $end_date = $request->end_date ?? date('Y-m-t');

    $transferQuery = Transfer::leftJoin('products', 'transfers.product_id', '=', 'products.id')
        ->whereBetween('transfers.approved_date', [$start_date, $end_date])
        ->select(
            'transfers.from_wearhouse_id',
            'transfers.to_wearhouse_id',
            DB::raw('SUM(transfers.qty) AS total_qty'),
            DB::raw('SUM(transfers.qty * products.purchase_price) AS total_amount')
        )
        ->groupBy('transfers.from_wearhouse_id', 'transfers.to_wearhouse_id');

    if (!empty($wearhouse)) {
        $transferQuery->where('transfers.from_wearhouse_id', $wearhouse);
    }
    if (!empty($to_wearhouse)) {
        $transferQuery->where('transfers.to_wearhouse_id', $to_wearhouse);
    }
    if (!empty($product_ids)) {
        $transferQuery->whereIn('transfers.product_id', $product_ids);
    }

    $transfers = $transferQuery->get();

    $totalQty = $transfers->sum('total_qty');
    $totalAmount = $transfers->sum('total_amount');

    if ($type == 'excel') {
        return Excel::download(new TransferSummaryExport($transfers, $totalQty, $totalAmount), 'transferSummaryExport.xlsx');
    }

    return view('backend.reports.product_transfer_summery', compact('transfers', 'wearhouse', 'to_wearhouse', 'product_ids', 'start_date', 'end_date', 'totalQty', 'totalAmount'));
}



    public function transfer_list_details(Request $request, $type = null)
{
    $from_warehouse_id = $request->from_warehouse_id;
    $to_warehouse_id = $request->to_warehouse_id;
    $product_ids = $request->product_id;
    $start_date = $request->start_date ?? date('Y-m-01');
    $end_date = $request->end_date ?? date('Y-m-t');

    $transferQuery = Transfer::leftJoin('products', 'transfers.product_id', '=', 'products.id')
        ->whereBetween('transfers.approved_date', [$start_date, $end_date])
        ->select(
            'transfers.*',
            DB::raw('SUM(transfers.qty) AS total_qty'),
            DB::raw('SUM(transfers.qty * transfers.unit_price) AS total_amount')
        )
        ->orderBy('transfers.approved_date', 'asc')
        ->groupBy('transfers.id', 'products.name', 'transfers.date', 'transfers.from_wearhouse_id', 'transfers.to_wearhouse_id', 'transfers.qty', 'transfers.unit_price', 'transfers.status');

    if (!empty($from_warehouse_id)) {
        $transferQuery->where('transfers.from_wearhouse_id', $from_warehouse_id);
    }
    if (!empty($to_warehouse_id)) {
        $transferQuery->where('transfers.to_wearhouse_id', $to_warehouse_id);
    }
    if (!empty($product_ids)) {
        $transferQuery->whereIn('transfers.product_id', $product_ids);
    }

    $transfers = $transferQuery->get();
    $totalQty = $transfers->sum('total_qty');
    $totalAmount = $transfers->sum('total_amount');

    if ($type == 'excel') {
        return Excel::download(new TransferDetailsExport($transfers), 'TransferDetailsExport.xlsx');
    }

    return view('backend.reports.transfer_list_details', compact('transfers', 'from_warehouse_id', 'to_warehouse_id', 'product_ids', 'start_date', 'end_date', 'totalQty', 'totalAmount'));
}

public function fifo_transfer_list_details(Request $request, $type = null)
{
    $from_warehouse_id = $request->from_warehouse_id;
    $to_warehouse_id = $request->to_warehouse_id;
    $product_ids = $request->product_id;
    $start_date = $request->start_date ?? date('Y-m-01');
    $end_date = $request->end_date ?? date('Y-m-t');

    // Fetching transfers based on filters
    $transferQuery = Transfer::leftJoin('products', 'transfers.product_id', '=', 'products.id')
        ->whereBetween('transfers.approved_date', [$start_date, $end_date])
        ->select('transfers.*', 'products.name')
        ->orderBy('transfers.approved_date', 'asc');

    if (!empty($from_warehouse_id)) {
        $transferQuery->where('transfers.from_wearhouse_id', $from_warehouse_id);
    }
    if (!empty($to_warehouse_id)) {
        $transferQuery->where('transfers.to_wearhouse_id', $to_warehouse_id);
    }
    if (!empty($product_ids)) {
        $transferQuery->whereIn('transfers.product_id', $product_ids);
    }

    $transfers = $transferQuery->get();

    // Applying FIFO logic
    foreach ($transfers as $transfer) {
        $product_id = $transfer->product_id;
        $warehouse_id = $transfer->from_warehouse_id;

        // Fetch purchase items for the product (sorted by date for FIFO)
        $purchases = PurchaseDetail::select('purchase_details.id', 'purchase_details.product_id', 'purchase_details.wearhouse_id', 'purchase_details.qty', 'purchase_details.price')
            ->leftJoin('purchases', 'purchases.id', '=', 'purchase_details.purchase_id') // Ensure you're joining with the correct purchase ID
            ->where('purchases.status', 2) // Adjust the status filter based on your requirements
            ->where('purchase_details.product_id', $product_id)
            ->where('purchase_details.wearhouse_id', $warehouse_id)
            ->whereBetween('purchases.date', [$start_date, $end_date])
            ->orderBy('purchases.date', 'asc') // Order by purchase date for FIFO
            ->get();

        $remaining_transfer_qty = $transfer->qty;
        $closing_stock_qty = 0;
        $closing_stock_amount = 0;

        // Process FIFO logic
        foreach ($purchases as $purchase) {
            if ($remaining_transfer_qty > 0) {
                if ($purchase->qty >= $remaining_transfer_qty) {
                    $closing_stock_qty += $remaining_transfer_qty; // Count the qty used
                    $closing_stock_amount += $remaining_transfer_qty * $purchase->price; // Calculate amount
                    $purchase->qty -= $remaining_transfer_qty; // Deduct from purchase qty
                    $remaining_transfer_qty = 0; // All transfer accounted for
                } else {
                    $closing_stock_qty += $purchase->qty; // Count remaining purchase qty
                    $closing_stock_amount += $purchase->qty * $purchase->price; // Calculate amount
                    $remaining_transfer_qty -= $purchase->qty; // Deduct this purchase qty
                    $purchase->qty = 0; // Deplete this purchase item
                }
            }
        }

        // Attach FIFO-calculated closing stock to the transfer record
        $transfer->closing_stock_qty = $closing_stock_qty;
        $transfer->closing_stock_amount = $closing_stock_amount; // Ensure closing stock amount is set
        $transfer->total_amount = $closing_stock_amount; // Set the total amount for display
        $transfer->total_qty = $closing_stock_qty; // Set the total qty for display
    }

    // Calculate total quantities and amounts
    $totalQty = $transfers->sum('total_qty');
    $totalAmount = $transfers->sum('total_amount');
// dd($transfers);
    // Handle Excel export
    if ($type == 'excel') {
        return Excel::download(new TransferDetailsExport($transfers), 'TransferDetailsExport.xlsx');
    }

    return view('backend.reports.fifo_transfer_list_details', compact('transfers', 'from_warehouse_id', 'to_warehouse_id', 'product_ids', 'start_date', 'end_date', 'totalQty', 'totalAmount'));
}


    






    public function damage_report(Request $request)
    {
        $wearhouse = Warehouse::get();
        $sort_by = null;
        $start_date = date('Y-m-01 00:00:00');
        $end_date = date('Y-m-t 23:59:59');
        $gte_damage_products = Damage::orderBy('created_at', 'asc');

        if (!empty($request->wearhous_id)) {

            $sort_by = $request->wearhous_id;
            $gte_damage_products =  $gte_damage_products->where('wearhouse_id', $sort_by);
        }
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d', strtotime($request->end_date));
            $gte_damage_products = $gte_damage_products->whereBetween('date', [$start_date, $end_date]);
        } else {
            $gte_damage_products = $gte_damage_products->whereBetween('date', [$start_date, $end_date]);
        }
        $gte_damage_products =  $gte_damage_products->get();
        return view('backend.staff_panel.purchase_executive.damage_list', compact('gte_damage_products', 'wearhouse', 'sort_by', 'start_date', 'end_date'));
    }

    // public function product_wise_purchase_report(Request $request, $type=null)
    // {
    //     $pro_sort_by = null;
    //     $sup_sort_by = null;
    //     $start_date = date('Y-m-01 00:00:00');
    //     $end_date = date('Y-m-t 23:59:59');
    //     $month = $request->month;
    //     $month_year = null;
    //     $year = $request->year;
    //     $wearhouse_id = null;
    //     $warehousearray = getWearhouseBuUserId(auth()->user()->id);
    //     $wearhouses = Warehouse::whereIn('id', $warehousearray)->get();
    //     if (empty($request->start_date))
    //         $request->start_date = $start_date;
    //     if (empty($request->end_date))
    //         $request->end_date = $end_date;

    //     if (!empty($request->month)) {
    //         $month_year = date('Y', strtotime($request->month));
    //         $month = date('m', strtotime($request->month));
        
    //         $start_date = date('Y-m-01', strtotime("$month_year-$month-01"));
    //         $end_date = date('Y-m-t', strtotime("$month_year-$month-01"));
    //     }
        
        
    //     if (!empty($request->year)) {
    //         $start_date = date('Y-m-01', strtotime("$year-01-01"));
    //         $end_date = date('Y-m-t', strtotime("$year-12-31"));
    //     }

    //     $staff = Staff::where('role_id',9)->get();
    //     $product_wise_purchase_list = Purchase::leftjoin(
    //         'suppliers',
    //         'suppliers.supplier_id',
    //         '=',
    //         'purchases.supplier_id'
    //     )
    //         ->join('purchase_details', 'purchases.id', 'purchase_details.id')
    //         ->join('products', 'purchase_details.product_id', 'products.id')
    //         ->select(
    //             'products.name',
    //             'suppliers.name as suppliername',
    //             'purchases.purchase_no',
    //             'purchases.date',
    //             'purchases.approved_date',
    //             'purchase_details.qty',
    //             'purchase_details.discount',
    //             'purchase_details.price'
    //         )
    //         ->where('purchases.status', '=', 2);

    //     if (!empty($request->wearhouse_id)) {
    //         $wearhouse_id = $request->wearhouse_id;
    //         $product_wise_purchase_list =  $product_wise_purchase_list->where('purchase_details.wearhouse_id', $wearhouse_id);
    //     }
    //     if (!empty($request->product_id)) {
    //         $pro_sort_by = $request->product_id;
    //         $product_wise_purchase_list = $product_wise_purchase_list->whereIn('purchase_details.product_id', $pro_sort_by);
    //     }
        
    //     if (!empty($request->supplier_id)) {
    //         $sup_sort_by = $request->supplier_id;
    //         $product_wise_purchase_list =  $product_wise_purchase_list->where('purchases.supplier_id', $sup_sort_by);
    //     }
    //     if (!empty($request->start_date) && !empty($request->end_date)) {
    //         $start_date = date('Y-m-d', strtotime($request->start_date));
    //         $end_date = date('Y-m-d', strtotime($request->end_date));
    //         $product_wise_purchase_list = $product_wise_purchase_list->whereBetween('purchases.approved_date', [$start_date, $end_date]);
    //     } else {
    //         $product_wise_purchase_list = $product_wise_purchase_list->whereBetween('purchases.approved_date', [$start_date, $end_date]);
    //     }


    //     $product_wise_purchase_list =   $product_wise_purchase_list->get();

    //     if($type == 'excel'){
    //         $data['product_wise_purchase_list'] = $product_wise_purchase_list;
    //         return Excel::download(new ProductWisePurchaseExport($data), 'product_wise_purcase.xlsx');
    //     }
    //     return view('backend.staff_panel.purchase_executive.product_wise_purchase_report', compact('product_wise_purchase_list', 'pro_sort_by', 'start_date', 'end_date', 'sup_sort_by', 'wearhouses', 'wearhouse_id','month','year','month_year'));
    // }

    public function product_wise_purchase_report(Request $request, $type = null)
{
    $pro_sort_by = null;
    $sup_sort_by = null;
    $start_date = date('Y-m-01 00:00:00');
    $end_date = date('Y-m-t 23:59:59');
    $month = $request->month;
    $month_year = null;
    $year = $request->year;
    $wearhouse_id = null;
    $warehousearray = getWearhouseBuUserId(auth()->user()->id);
    $wearhouses = Warehouse::whereIn('id', $warehousearray)->get();

    // Set default start and end dates if not provided
    if (empty($request->start_date)) {
        $request->start_date = $start_date;
    }
    if (empty($request->end_date)) {
        $request->end_date = $end_date;
    }

    // Adjust dates based on month and year
    if (!empty($request->month)) {
        $month_year = date('Y', strtotime($request->month));
        $month = date('m', strtotime($request->month));
        $start_date = date('Y-m-01', strtotime("$month_year-$month-01"));
        $end_date = date('Y-m-t', strtotime("$month_year-$month-01"));
    }

    if (!empty($request->year)) {
        $start_date = date('Y-m-01', strtotime("$year-01-01"));
        $end_date = date('Y-m-t', strtotime("$year-12-31"));
    }

    // Query to fetch product-wise purchase list
    $product_wise_purchase_list = Purchase::leftJoin('suppliers', 'suppliers.supplier_id', '=', 'purchases.supplier_id')
        ->join('purchase_details', 'purchases.id', '=', 'purchase_details.purchase_id')
        ->join('products', 'purchase_details.product_id', '=', 'products.id')
        ->select(
            'products.name',
            'suppliers.name as suppliername',
            'purchases.purchase_no',
            'purchases.date',
            'purchases.approved_date',
            'purchase_details.qty',
            'purchase_details.discount',
            'purchase_details.price'
        )
        ->where('purchases.status', '=', 2);

    // Filter by warehouse ID
    if (!empty($request->wearhouse_id)) {
        $wearhouse_id = $request->wearhouse_id;
        $product_wise_purchase_list = $product_wise_purchase_list->where('purchase_details.wearhouse_id', $wearhouse_id);
    }

    // Filter by product ID
    if (!empty($request->product_id)) {
        $pro_sort_by = $request->product_id;
        $product_wise_purchase_list = $product_wise_purchase_list->whereIn('purchase_details.product_id', $pro_sort_by);
    }

    // Filter by supplier ID
    if (!empty($request->supplier_id)) {
        $sup_sort_by = $request->supplier_id;
        $product_wise_purchase_list = $product_wise_purchase_list->where('purchases.supplier_id', $sup_sort_by);
    }

    // Filter by date range
    if (!empty($request->start_date) && !empty($request->end_date)) {
        $start_date = date('Y-m-d', strtotime($request->start_date));
        $end_date = date('Y-m-d', strtotime($request->end_date));
        $product_wise_purchase_list = $product_wise_purchase_list->whereBetween('purchases.approved_date', [$start_date, $end_date]);
    } else {
        $product_wise_purchase_list = $product_wise_purchase_list->whereBetween('purchases.approved_date', [$start_date, $end_date]);
    }

    // Get the final list
    $product_wise_purchase_list = $product_wise_purchase_list->get();

    // Export to Excel if requested
    if ($type == 'excel') {
        $data['product_wise_purchase_list'] = $product_wise_purchase_list;
        return Excel::download(new ProductWisePurchaseExport($data), 'product_wise_purchase.xlsx');
    }

    // Return the view with the required data
    return view('backend.staff_panel.purchase_executive.product_wise_purchase_report', compact(
        'product_wise_purchase_list',
        'pro_sort_by',
        'start_date',
        'end_date',
        'sup_sort_by',
        'wearhouses',
        'wearhouse_id',
        'month',
        'year',
        'month_year'
    ));
}


    public function add_purchase_for_purchase_executive()
    {
        $products = Product::where('parent_id', '=', null)->get();
        $supplier = Supplier::all();
        $title =  'Purchase Add';
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $wearhouses = Warehouse::whereIn('id', $warehousearray)->get();

        return view('backend.staff_panel.purchase_executive.purchase', compact('products', 'supplier', 'title', 'wearhouses'));
    }

    public function employee_performance(Request $request)
    {
        if (!empty($request->user_id) && !empty($request->role)) {
            $user_id = $request->user_id;
            $role_id = $request->role;

            $check = Staff::where('staff.user_id', $user_id)
                ->join('roles', 'staff.role_id', '=', 'roles.id')
                ->where('roles.id', $role_id)
                ->exists();

            if (!$check) {

                // return redirect()->back()->with('error', 'An error occurred.');
                flash(__('This User does not belong to the Selected Employee Role'))->error();
                return back();
            }
        }

        if ($request->role == 9) {

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
            } else {
                $start_date = date('Y-m-01');
                $end_date = date('Y-m-t');
            }

            $month_start = date('m', strtotime($start_date));
            $month_end = date('m', strtotime($end_date));
            $role_id = $request->role;
            $user_id = $request->user_id;

            $targets = Target::query();

            if (!empty($user_id)) {
                $targets->where('targets.user_id', $request->user_id)
                    ->whereBetween('targets.month', [$month_start, $month_end]);
            } else {
                $targets->whereBetween('targets.month', [$month_start, $month_end]);
            }
            
            if (!empty($role_id)) {
                $targets->join('staff', 'targets.user_id', 'staff.user_id')
                    ->join('roles', 'staff.role_id', '=', 'roles.id')
                    ->where('staff.role_id', $request->role);
            }
            
            $targets = $targets->selectRaw('targets.user_id, 
                SUM(targets.target) AS total_target, 
                SUM(targets.recovery_target) AS total_recovery_target, 
                SUM(targets.terget_customer) AS total_target_customer, 
                SUM(targets.customer_achivement) AS total_customer_achievement')
                ->orderBy('targets.month', 'DESC')
                ->groupBy('targets.user_id')
                ->get();
            // dd($targets);

            foreach ($targets as $key => $com) {


                $orders = Order::where('orders.delivered_by', '>', '0')
                    ->whereNull('orders.canceled_by')
                    ->join('customers', 'customers.user_id', '=', 'orders.user_id')
                    ->where('customers.staff_id', $com->user_id)
                    ->whereBetween('orders.created_at', [$start_date, $end_date])
                    ->sum('orders.grand_total');

                $targets[$key]->total_sales = $orders;


                if (isset($com->total_target) && $com->total_target !== 0) {
                    $sales_achievement = round($targets[$key]->total_sales * 100 / ($targets[$key]->total_target ?: 1));
                } else {
                    $sales_achievement = 0;
                }


                $targets[$key]->sales_achievement = $sales_achievement;

                $customerCount = Customer::where('customers.staff_id', $com->user_id)->whereBetween('updated_at', [$start_date, $end_date])
                    ->get()->count();

                $targets[$key]->customer_count = $customerCount;

                if (isset($com->total_target_customer) && $com->total_target_customer !== 0) {
                    $customer_achivement = round(($customerCount * 100) / ($targets[$key]->total_target_customer ?: 1));
                } else {
                    $customer_achivement = 0;
                }

                $targets[$key]->customer_achivement = $customer_achivement;


                $sql2 = "SELECT
                        SUM(cl.debit) AS debit,
                        SUM(cl.credit) AS credit,
                        SUM(cl.balance) AS balance,
                        (
                            SELECT SUM(cll.debit - cll.credit)
                            FROM customer_ledger AS cll
                            WHERE c.user_id = cll.customer_id AND cll.date < '" . $end_date . "'
                        ) AS opening_balance
                    FROM customers c
                    LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id
                    WHERE (cl.debit > 0 OR cl.credit > 0)";
                $sql2 .= " AND c.staff_id = $com->user_id";

                $customers2 = DB::select($sql2);
                $due = 0;
                $opening_balance = 0;
                foreach ($customers2 as $key => $customer) {
                    $due += $customer->opening_balance + $customer->debit - $customer->credit;
                }

                $com->totaldue = $due;

                $monthlyDebit = Customer_ledger::leftjoin('customers', 'customer_ledger.customer_id', '=', 'customers.user_id')
                    ->where('customers.staff_id', $com->user_id)
                    ->whereBetween('customer_ledger.date', [$start_date, $end_date])
                    ->sum('customer_ledger.debit');

                $monthlyTotaldue = Customer_ledger::leftjoin('customers', 'customer_ledger.customer_id', '=', 'customers.user_id')
                    ->where('customers.staff_id', $com->user_id)
                    ->whereBetween('customer_ledger.date', [$start_date, $end_date])
                    ->sum('customer_ledger.credit');


                $com->monthlyTotaldue = $monthlyDebit - $monthlyTotaldue;
            }

            return view('backend.reports.employee_performance_report_sales_executive', compact('targets', 'start_date', 'end_date', 'role_id', 'user_id'));
        }
        if ($request->role == 10) {
            $role_id = $request->role;
            $delivery_staffs = Staff::join('roles', 'staff.role_id', '=', 'roles.id')
                ->where('staff.role_id', $role_id)
                ->get();

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
            } else {
                $start_date = date('Y-m-01');
                $end_date = date('Y-m-t');
            }

            $month_start = date('m', strtotime($start_date));
            $month_end = date('m', strtotime($end_date));
            $user_id = $request->user_id;


            $delivery_staffs_main = Staff::join('roles', 'staff.role_id', '=', 'roles.id')
                ->where('staff.role_id', $role_id);

            if (!empty($user_id)) {
                $delivery_staffs_main->where('staff.user_id', $request->user_id);
            }

            $delivery_staffs_main = $delivery_staffs_main->get();

            foreach ($delivery_staffs_main as $key => $com) {

                $total_order_qty = Order::where('delivery_boy', $com->user_id)
                    ->whereBetween('created_at', [$start_date, $end_date])
                    ->whereNull('cancel_date')
                    ->count();


                $delivery_staffs_main[$key]->total_order_quantity = $total_order_qty;

                $delivered_qty = Order::where('delivery_boy', $com->user_id)
                    ->whereBetween('created_at', [$start_date, $end_date])
                    ->whereNotNull('delivered_date')
                    ->count();

                $delivery_staffs_main[$key]->delivered_qty = $delivered_qty;
                $delivery_staffs_main[$key]->pending_qty = $total_order_qty - $delivered_qty;

                $cash_balance = DeliveryExecutiveLedger::where('delivery_executive_ledger.user_id', $com->user_id)
                    ->where('type', 'Order')->whereBetween('delivery_executive_ledger.created_at', [$start_date, $end_date])
                    ->sum('debit');

                $delivery_staffs_main[$key]->cash_balance = $cash_balance;

                $achivement = round($delivered_qty * 100 / ($total_order_qty ?: 1));

                $delivery_staffs_main[$key]->achivement = $achivement;
            }

            return view('backend.reports.employee_performance_report_delivery_executive', compact('role_id', 'delivery_staffs', 'start_date', 'end_date', 'user_id', 'delivery_staffs_main'));
        }

        if ($request->role == 11) {

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
            } else {
                $start_date = date('Y-m-01');
                $end_date = date('Y-m-t');
            }

            $month_start = date('m', strtotime($start_date));
            $month_end = date('m', strtotime($end_date));
            $role_id = $request->role;
            $user_id = $request->user_id;

            if (!empty($user_id)) {
                $delivery_staffs = Staff::join('roles', 'staff.role_id', '=', 'roles.id')
                    ->where('staff.role_id', $role_id)
                    ->where('staff.user_id', $user_id)
                    ->get();
            } else {
                $delivery_staffs = Staff::join('roles', 'staff.role_id', '=', 'roles.id')
                    ->where('staff.role_id', $role_id)
                    ->get();
            }


            foreach ($delivery_staffs as $key => $com) {

                $warehousearray = getWearhouseBuUserId($com->user_id);


                $total_order_qty = Order::WhereNotNull('confirm_date')
                    ->WhereNull('cancel_date')
                    ->WhereIn('warehouse', $warehousearray)
                    ->whereBetween('created_at', [$start_date, $end_date])->count();

                $com->total_order_qty = $total_order_qty;

                $total_delivered_qty = Order::WhereIn('warehouse', $warehousearray)
                    ->WhereNotNull('delivered_date')
                    ->whereBetween('created_at', [$start_date, $end_date])
                    ->count();

                $com->total_delivered_qty = $total_delivered_qty;

                $com->pending_qty = $total_order_qty  - $total_delivered_qty;

                $damage_qty = Damage::WhereIn('wearhouse_id', $warehousearray)
                    ->whereBetween('created_at', [$start_date, $end_date])
                    ->sum('qty');

                $com->damage_qty = $damage_qty;

                $replacement_product = RefundRequest::leftjoin('order_details', 'refund_requests.order_detail_id', 'order_details.id')
                    ->leftjoin('orders', 'order_details.order_id', 'orders.id')
                    ->select('order_details.quantity', 'orders.warehouse')->whereBetween('refund_requests.created_at', [$start_date, $end_date])
                    ->WhereIn('warehouse', $warehousearray)
                    ->whereIn('refund_requests.refund_status', [2, 3, 4])
                    ->sum('quantity');
                $com->replacement_product = $replacement_product;

                $com->achivement = round(($total_delivered_qty * 100) / ($total_order_qty ?: 1), 2);
            }


            return view('backend.reports.employee_performance_report_operation_manager', compact('start_date', 'end_date', 'role_id', 'user_id', 'delivery_staffs'));
        }

        if ($request->role == 12) {
            $role_id = $request->role;
            $delivery_staffs = Staff::join('roles', 'staff.role_id', '=', 'roles.id')
                ->where('staff.role_id', $role_id)
                ->get();

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
            } else {
                $start_date = date('Y-m-01');
                $end_date = date('Y-m-t');
            }

            $month_start = date('m', strtotime($start_date));
            $month_end = date('m', strtotime($end_date));
            $user_id = $request->user_id;


            $delivery_staffs_main = Staff::join('roles', 'staff.role_id', '=', 'roles.id')
                ->where('staff.role_id', $role_id);

            if (!empty($user_id)) {
                $delivery_staffs_main->where('staff.user_id', $request->user_id);
            }




            $delivery_staffs_main = $delivery_staffs_main->get();



            foreach ($delivery_staffs_main as $key => $com) {


                $total_purchase_qty = Purchase::leftjoin('purchase_details', 'purchases.id', 'purchase_details.id')
                    ->whereBetween('purchases.created_at', [$start_date, $end_date])
                    ->where('created_by', $com->user_id)
                    ->where('status', '2')
                    ->select('purchase_details.qty')
                    ->sum('qty');



                $delivery_staffs_main[$key]->total_purchase_qty = $total_purchase_qty;


                $total_purchase_amount = Purchase::leftjoin('purchase_details', 'purchases.id', 'purchase_details.id')
                    ->whereBetween('purchases.created_at', [$start_date, $end_date])
                    ->where('created_by', $com->user_id)
                    ->where('status', '2')
                    ->select('amount')
                    ->sum('amount');

                $delivery_staffs_main[$key]->total_purchase_amount = $total_purchase_amount;

                $damage_product_qty = Damage::whereBetween('created_at', [$start_date, $end_date])
                    ->where('status', 'Approved')
                    ->select('amount')->sum('qty');


                $delivery_staffs_main[$key]->damage_product_qty = $damage_product_qty;

                $damage_product_amount = Damage::whereBetween('created_at', [$start_date, $end_date])
                    ->where('status', 'Approved')
                    ->sum('total_amount');


                $delivery_staffs_main[$key]->damage_product_amount = $damage_product_amount;

                $vendor_create = Supplier::where('staff_id', $com->user_id)
                    ->whereBetween('created_at', [$start_date, $end_date])
                    ->where('status', 1)
                    ->count();

                $delivery_staffs_main[$key]->vendor_create = $vendor_create;

                $total_vendor = Supplier::where('staff_id', $com->user_id)
                    ->where('status', 1)
                    ->count();


                $delivery_staffs_main[$key]->total_vendor = $total_vendor;

                if ($total_purchase_qty == 0) {
                    $achivement = 0;
                } else if ($damage_product_qty == 0) {
                    $achivement = 100;
                } else {
                    $achivement = 100 - round(($damage_product_qty) / ($total_purchase_qty ?: 1) * 100, 2);
                }

                $delivery_staffs_main[$key]->achivement = $achivement;
            }

            return view('backend.reports.employee_performance_report_purchase_executive', compact('role_id', 'delivery_staffs', 'start_date', 'end_date', 'user_id', 'delivery_staffs_main'));
        }

        if ($request->role == 13) {
            $role_id = $request->role;
            $delivery_staffs = Staff::join('roles', 'staff.role_id', '=', 'roles.id')
                ->where('staff.role_id', $role_id)
                ->get();

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
            } else {
                $start_date = date('Y-m-01');
                $end_date = date('Y-m-t');
            }

            $month_start = date('m', strtotime($start_date));
            $month_end = date('m', strtotime($end_date));
            $user_id = $request->user_id;


            $delivery_staffs_main = Staff::join('roles', 'staff.role_id', '=', 'roles.id')
                ->where('staff.role_id', $role_id);

            if (!empty($user_id)) {
                $delivery_staffs_main->where('staff.user_id', $request->user_id);
            }




            $delivery_staffs_main = $delivery_staffs_main->get();



            foreach ($delivery_staffs_main as $key => $com) {

                $total_purchase_qty = Purchase::leftjoin('purchase_details', 'purchases.id', 'purchase_details.id')
                    ->whereBetween('purchases.created_at', [$start_date, $end_date])
                    ->where('purchases.created_by', $com->user_id)
                    ->where('purchases.status', '2')
                    ->select('purchase_details.qty')
                    ->sum('purchase_details.qty');


                $delivery_staffs_main[$key]->total_purchase_qty = $total_purchase_qty;

                $total_purchase_amount = Purchase::leftjoin('purchase_details', 'purchases.id', 'purchase_details.id')
                    ->where('purchases.created_by', $com->user_id)
                    ->whereBetween('purchases.created_at', [$start_date, $end_date])
                    ->where('status', '2')->select('amount')->sum('amount');

                $delivery_staffs_main[$key]->total_purchase_amount = $total_purchase_amount;

                $damage_product_qty = Damage::whereBetween('created_at', [$start_date, $end_date])
                    ->where('status', 'Approved')
                    ->select('amount')->sum('qty');

                $delivery_staffs_main[$key]->damage_product_qty = $damage_product_qty;

                $damage_product_amount = Damage::whereBetween('created_at', [$start_date, $end_date])
                    ->where('status', 'Approved')
                    ->sum('total_amount');

                $delivery_staffs_main[$key]->damage_product_amount = $damage_product_amount;



                $vendor_create = Supplier::whereBetween('created_at', [$start_date, $end_date])
                    ->where('suppliers.staff_id', $com->user_id)
                    ->where('status', 1)
                    ->count();

                $delivery_staffs_main[$key]->vendor_create = $vendor_create;



                $total_vendor = Supplier::whereBetween('created_at', [$start_date, $end_date])
                    ->where('suppliers.staff_id', $com->user_id)
                    ->where('status', 1)
                    ->count();
                $delivery_staffs_main[$key]->total_vendor = $total_vendor;

                $user = $com->user_id;

                $total_supplier_cr = "SELECT 
                                            SUM(credit) AS cred,
                                            SUM(debit) AS deb,
                                            SUM(debit) - SUM(credit) AS total
                                        FROM 
                                            supplier_ledger
                                        JOIN 
                                            suppliers ON suppliers.supplier_id = supplier_ledger.supplier_id
                                        WHERE 
                                            suppliers.staff_id = $user";

                $total_supplier_credit = DB::select(DB::raw($total_supplier_cr));

                if (!empty($total_supplier_credit)) {
                    $delivery_staffs_main[$key]->total_supplier_credit = $total_supplier_credit[0]->total;
                } else {
                    $delivery_staffs_main[$key]->total_supplier_credit = 0;
                }


                $delivery_staffs_main[$key]->total_vendor = $total_vendor;

                if ($total_purchase_qty == 0) {
                    $achivement = 0;
                } else if ($damage_product_qty == 0) {
                    $achivement = 100;
                } else {
                    $achivement = 100 - round(($damage_product_qty) / ($total_purchase_qty ?: 1) * 100, 2);
                }

                $delivery_staffs_main[$key]->achivement = $achivement;
            }

            return view('backend.reports.employee_performance_report_purchase_manager', compact('role_id', 'delivery_staffs', 'start_date', 'end_date', 'user_id', 'delivery_staffs_main'));
        }

        if ($request->role == 14) {

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
            } else {
                $start_date = date('Y-m-01');
                $end_date = date('Y-m-t');
            }

            $month_start = date('m', strtotime($start_date));
            $month_end = date('m', strtotime($end_date));
            $role_id = $request->role;
            $user_id = $request->user_id;

            $targets = Target::query();

            if (!empty($user_id)) {
                $targets->where('targets.user_id', $request->user_id)
                    ->whereBetween('targets.created_at', [$month_start, $month_end])
                    ->orderBy('targets.month', 'DESC');
            } else {
                $targets->whereBetween('targets.month', [$month_start, $month_end])
                    ->orderBy('targets.month', 'DESC');;
            }

            if (!empty($role_id)) {
                $targets = $targets->join('staff', 'targets.user_id', 'staff.user_id')
                    ->join('roles', 'staff.role_id', '=', 'roles.id')
                    ->where('staff.role_id', $request->role);
            }

            $targets = $targets->selectRaw('targets.user_id, 
                SUM(targets.target) AS total_target, 
                SUM(targets.recovery_target) AS total_recovery_target, 
                SUM(targets.terget_customer) AS total_target_customer, 
                SUM(targets.customer_achivement) AS total_customer_achievement')
                ->orderBy('targets.month', 'DESC')
                ->groupBy('targets.user_id')
                ->get();
        
            // dd($targets);


            foreach ($targets as $key => $com) {


                $orders = Order::where('orders.delivered_by', '>', '0')
                    ->whereNull('orders.canceled_by')
                    ->join('customers', 'customers.user_id', '=', 'orders.user_id')
                    ->where('customers.staff_id', $com->user_id)
                    ->whereBetween('orders.created_at', [$start_date, $end_date])
                    ->sum('orders.grand_total');



                $targets[$key]->total_sales = $orders;


                if (isset($com->total_target) && $com->total_target != 0) {
                    $sales_achievement = round($targets[$key]->total_sales * 100 / ($targets[$key]->total_target ?: 1));
                } else {
                    $sales_achievement = 0;
                }


                $targets[$key]->sales_achievement = $sales_achievement;

                $customerCount = Customer::where('customers.staff_id', $com->user_id)->whereBetween('created_at', [$start_date, $end_date])
                    ->get()->count();

                $targets[$key]->customer_count = $customerCount;

                if (isset($com->total_target_customer) && $com->total_target_customer != 0) {
                    $customer_achivement = round(($customerCount * 100) / ($targets[$key]->total_target_customer ?: 1));
                } else {
                    $customer_achivement = 0;
                }

                $targets[$key]->customer_achivement = $customer_achivement;

                $sql2 = "SELECT
                        SUM(cl.debit) AS debit,
                        SUM(cl.credit) AS credit,
                        SUM(cl.balance) AS balance,
                        (
                            SELECT SUM(cll.debit - cll.credit)
                            FROM customer_ledger AS cll
                            WHERE c.user_id = cll.customer_id AND cll.date < '" . $end_date . "'
                        ) AS opening_balance
                    FROM customers c
                    LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id
                    WHERE (cl.debit > 0 OR cl.credit > 0)";
                $sql2 .= " AND c.staff_id = $com->user_id";

                $customers2 = DB::select($sql2);
                $due = 0;
                $opening_balance = 0;
                foreach ($customers2 as $key => $customer) {
                    $due += $customer->opening_balance + $customer->debit - $customer->credit;
                }

                $com->totaldue = $due;

                $monthlyDebit = Customer_ledger::leftjoin('customers', 'customer_ledger.customer_id', '=', 'customers.user_id')
                    ->where('customers.staff_id', $com->user_id)
                    ->whereBetween('customer_ledger.date', [$start_date, $end_date])
                    ->sum('customer_ledger.debit');

                $monthlyTotaldue = Customer_ledger::leftjoin('customers', 'customer_ledger.customer_id', '=', 'customers.user_id')
                    ->where('customers.staff_id', $com->user_id)
                    ->whereBetween('customer_ledger.date', [$start_date, $end_date])
                    ->sum('customer_ledger.credit');


                $com->monthlyTotaldue = $monthlyDebit - $monthlyTotaldue;
            }

            return view('backend.reports.employee_performance_report_customer_service_executive', compact('targets', 'start_date', 'end_date', 'role_id', 'user_id'));
        } else {
            return view('backend.reports.employee_performance_report');
        }
    }
}
