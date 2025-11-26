<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\RefundRequest;
use App\Models\OrderDetail;
use App\Models\Seller;
use App\Models\Wallet;
use App\Models\User;
use App\Models\ProductStock;
use Auth;

class RefundRequestController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //Store Customer Refund Request
    public function request_store(Request $request, $id)
    {
        $order_detail = OrderDetail::where('id', $id)->first();
        $refund = new RefundRequest;
        $refund->user_id = Auth::user()->id;
        $refund->order_id = $order_detail->order_id;
        $refund->order_detail_id = $order_detail->id;
        $refund->seller_id = $order_detail->seller_id;
        $refund->seller_approval = 0;
        $refund->reason = $request->reason;
        $refund->admin_approval = 0;
        $refund->admin_seen = 0;
        $refund->refund_amount = $order_detail->price + $order_detail->tax;
        $refund->refund_status = 0;

        if($refund->save()){
            return response()->json([
                'result' => true,
                'message' => 'Your refund request has been placed successfully'
            ]);
        }
        return response()->json([
            'result' => false,
            'message' => 'Your refund request Failed'
        ]); 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function vendor_index()
    {
        $refunds = RefundRequest::where('seller_id', Auth::user()->id)->latest()->paginate(10);
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return view('refund_request.frontend.recieved_refund_request.index', compact('refunds'));
        } else {
            return view('refund_request.frontend.recieved_refund_request.index', compact('refunds'));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function customer_index($id)
    {
        $refunds = RefundRequest::join('orders','orders.id','refund_requests.order_id')
        ->join('order_details','order_details.order_id','refund_requests.order_id')
        ->join('products','products.id','order_details.product_id')
        ->select('orders.code','refund_requests.*','products.name')
        ->where('refund_requests.user_id', $id)
        ->latest()->paginate(10);
        return response()->json([
            'refunds' => $refunds
        ]);
    }

    //Set the Refund configuration
    public function refund_config()
    {
        return view('refund_request.config');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function refund_time_update(Request $request)
    {
        $business_settings = BusinessSetting::where('type', $request->type)->first();
        if ($business_settings != null) {
            $business_settings->value = $request->value;
            $business_settings->save();
        } else {
            $business_settings = new BusinessSetting;
            $business_settings->type = $request->type;
            $business_settings->value = $request->value;
            $business_settings->save();
        }
        flash(translate("Refund Request sending time has been updated successfully"))->success();
        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function refund_sticker_update(Request $request)
    {
        $business_settings = BusinessSetting::where('type', $request->type)->first();
        if ($business_settings != null) {
            $business_settings->value = $request->logo;
            $business_settings->save();
        } else {
            $business_settings = new BusinessSetting;
            $business_settings->type = $request->type;
            $business_settings->value = $request->logo;
            $business_settings->save();
        }
        flash(translate("Refund Sticker has been updated successfully"))->success();
        return back();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_index()
    {
        $refunds = RefundRequest::where('refund_status', 0)->latest()->paginate(15);
        return view('refund_request.index', compact('refunds'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function paid_index()
    {
        $refunds = RefundRequest::where('refund_status', 1)->latest()->paginate(15);
        return view('refund_request.paid_refund', compact('refunds'));
    }

    public function rejected_index()
    {
        $refunds = RefundRequest::where('refund_status', 2)->latest()->paginate(15);
        return view('refund_request.rejected_refund', compact('refunds'));
    }

    public function resolved_request_index()
    {
        $refunds = RefundRequest::where('refund_status', 6)->latest()->paginate(15);
        return view('refund_request.resolved_request', compact('refunds'));
    }

    public function resolved_index()
    {
        $refunds = RefundRequest::where('refund_status', 5)->latest()->paginate(15);
        return view('refund_request.resolved_refund', compact('refunds'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function request_approval_vendor(Request $request)
    {
        $refund = RefundRequest::findOrFail($request->el);
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            $refund->seller_approval = 1;
            $refund->admin_approval = 1;
        } else {
            $refund->seller_approval = 1;
        }

        if ($refund->save()) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function refund_pay(Request $request)
    {
        $refund = RefundRequest::findOrFail($request->el);

        if ($refund->seller_approval == 1) {
            $seller = Seller::where('user_id', $refund->seller_id)->first();
            if ($seller != null) {
                $seller->admin_to_pay -= $refund->refund_amount;
                $seller->save();
            }
        }

        $wallet = new Wallet;
        $wallet->user_id = $refund->user_id;
        $wallet->amount = $refund->refund_amount;
        $wallet->payment_method = 'Refund';
        $wallet->payment_details = 'Product Money Refund';
        $wallet->save();

        $user = User::findOrFail($refund->user_id);
        //$user->balance += $refund->refund_amount;
        //$user->save();

        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            $refund->admin_approval = 1;
            $refund->refund_status = 5;
        }

        if ($refund->save()) {
            // Check refund_type and update product quantity accordingly
            if ($refund->refund_type == 1) { // If refund_type is 1 (Refund)
                if ($refund->orderDetail && $refund->orderDetail->product) {
                    $productStock = ProductStock::where('product_id', $refund->orderDetail->product->id)
                        ->where('wearhouse_id', $refund->order->warehouse)
                        ->first();

                    if ($productStock) {
                        $productStock->increment('qty', $refund->return_qty); // Increment quantity by product quantity of that order
                    }
                }
            }

            return 1; // Success
        } else {
            return 0; // Failure
        }
    }

    public function reject_refund_request(Request $request)
    {
        $refund = RefundRequest::findOrFail($request->refund_id);
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            $refund->admin_approval = 2;
            $refund->refund_status  = 2;
            $refund->reject_reason  = $request->reject_reason;
        } else {
            $refund->seller_approval = 2;
            $refund->reject_reason  = $request->reject_reason;
        }

        if ($refund->save()) {
            flash(translate('Refund request rejected successfully.'))->success();
            return back();
        } else {
            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function refund_request_send_page($id)
    {
        $order_detail = OrderDetail::findOrFail($id);
        if ($order_detail->product != null && $order_detail->product->refundable == 1) {
            return view('refund_request.frontend.refund_request.create', compact('order_detail'));
        } else {
            return back();
        }
    }

    /**
     * Show the form for view the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //Shows the refund reason
    public function reason_view($id)
    {
        $refund = RefundRequest::findOrFail($id);
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            if ($refund->orderDetail != null) {
                $refund->admin_seen = 1;
                $refund->save();
                return view('refund_request.reason', compact('refund'));
            }
        } else {
            return view('refund_request.frontend.refund_request.reason', compact('refund'));
        }
    }

    public function reject_reason_view($id)
    {
        $refund = RefundRequest::findOrFail($id);
        return $refund->reject_reason;
    }

    public function approve_refund_request(Request $request)
    {
        $refund = RefundRequest::findOrFail($request->el);

        if ($refund->seller_approval == 1) {
            $seller = Seller::where('user_id', $refund->seller_id)->first();
            if ($seller != null) {
                $seller->admin_to_pay -= $refund->refund_amount;
                $seller->save();
            }
        }

        $wallet = new Wallet;
        $wallet->user_id = $refund->user_id;
        $wallet->amount = $refund->refund_amount;
        $wallet->payment_method = 'Refund';
        $wallet->payment_details = 'Product Money Refund';
        $wallet->save();

        $user = User::findOrFail($refund->user_id);
        //$user->balance += $refund->refund_amount;
        //$user->save();

        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            $refund->admin_approval = 1;
            $refund->refund_status = 1;

            // Check the selected action and set refund_type accordingly
            if ($request->refund_type == 'refund') {
                $refund->refund_type = 1; // Set refund_type to 1 for 'refund'
            } elseif ($request->refund_type == 'return') {
                $refund->refund_type = 2; // Set refund_type to 2 for 'return'
            }
        }



        if ($refund->save()) {
            return 1;
        } else {
            return 0;
        }
    }

    public function delivered_refund_request(Request $request)
    {
        try {
            $refund = RefundRequest::findOrFail($request->delivered_refund_id);
            $orderDetail = OrderDetail::leftJoin('refund_requests', 'order_details.id', '=', 'refund_requests.order_detail_id')
                ->where('order_details.id', $refund->order_detail_id)
                ->select('order_details.quantity')
                ->first();

            if (!$orderDetail) {
                flash(__('Order detail not found.'))->error();
                return back();
            }

            if ($request->return_qty <= $orderDetail->quantity && $refund->refund_amount >= $request->return_amount) {
                // Update refund details
                $refund->refund_status = 4;
                $refund->return_qty = $request->return_qty;
                $refund->return_amount = $request->return_amount;
                $refund->return_remark = $request->return_remark;

                if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
                    $refund->admin_approval = 1;
                    $refund->refund_status = 6;
                }

                if ($refund->save()) {
                    flash(translate('Return Delivered successfully.'))->success();
                } else {
                    flash(__('Failed to save refund details.'))->error();
                }
            } else if ($request->return_qty > $orderDetail->quantity && $refund->refund_amount >= $request->return_amount) {
                flash(__('Requested Quantity is larger than Ordered Quantity.'))->error();
            } else if ($refund->refund_amount < $request->return_amount && $request->return_qty <= $orderDetail->quantity) {
                flash(__('Requested Amount is larger than Ordered Amount.'))->error();
            } else {
                flash(__('Requested Quantity & Amount is larger than Ordered Quantity & Amount.'))->error();
            }
        } catch (Exception $e) {
            flash(__('An error occurred.'))->error();
        }

        return back();
    }

    public function delivery_man_refund_request(Request $request)
    {
        $refund = RefundRequest::findOrFail($request->delivery_boy_refund_id);
        $refund->refund_status  = 3;
        $refund->delivery_boy  = $request->delivery_boy;
        if ($refund->save()) {
            flash(translate('Refund request Processed successfully.'))->success();
            return back();
        } else {
            flash(translate('Please Select Delivery Man.'))->warning();
            return back();
        }
    }

    public function assain_delivery_boy($id, $delivery_boy)
    {
        $get_refund = RefundRequest::findOrFail($id);
        $get_refund->delivery_boy  = $delivery_boy;
        $get_refund->save();
    }
}
