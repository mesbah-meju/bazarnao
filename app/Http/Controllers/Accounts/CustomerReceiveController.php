<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OrderController;
use App\Models\AccCoa;
use App\Models\AccTransaction;
use App\Models\AccVoucher;
use App\Models\Customer;
use App\Models\FinancialYear;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class CustomerReceiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers          = get_customer();
        $voucher_no         = customer_receive();
        $payment_methods    = payment_methods();

        return view('backend.accounts.customer_receive.index', compact('customers', 'voucher_no', 'payment_methods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'txtCode'     => 'nullable|max:100',
            'dueAmount'   => 'required',
            'voucher_no'  => 'required',
            'txtAmount'   => 'required|max:30',
        ]);

        $receive_date = insert_customer_receive($request);
        
        if ($receive_date) {
            $info['customer_info']  = get_customer_info($request->customer_id);
            $info['payment_info']   = get_customer_receive_info($request->voucher_no, $receive_date);
            // $info['company_info']   = get_company_info();
            $info['message']        = translate('Save Successfully');
            $info['details']        = view('backend.accounts.customer_receive.receipt', $info)->render();
            $info['status']         = true;
            return response()->json($info);
        } else {
            $info['exception']      = translate('Please Try Again');
            $info['status']         = false;
            return response()->json($info);
        }
    }

    public function due_vouchers(Request $request)
    {
        $coa = AccCoa::where('customer_id', $request->customer_id)->first();
        $code = $coa ? $coa->head_code : '';

        $vouchers = Order::where('user_id', $request->customer_id)
            ->where('due_amount', '>', 0)
            ->orderBy('delivered_date', 'asc')
            ->get();

        $html = '';
        if ($vouchers->isEmpty()) {
            $html .= "No Chalan Found!";
        } else {
            $html .= '<select name="voucher_no" id="voucher_no_1" class="voucher_no form-control aiz-selectpicker">';
            $html .= '<option>' . 'Select Voucher' . '</option>';

            foreach ($vouchers as $voucher) {
                $html .= '<option value="' . $voucher->id . '">' . $voucher->code . '</option>';
            }

            $html .= '</select>';
        }

        echo json_encode(['headcode' => $code, 'vouchers' => $html]);
    }

    public function due_amount(Request $request)
    {
        $order_id = $request->order_id;
        $order = Order::where('id', $order_id)->first();

        echo ($order->due_amount ? $order->due_amount : 0);
    }

    /**
     * Display a listing of the resource.
     */
    public function orderwise(Request $request)
    {
        $order_id   = $request->input('order_id');
        $customer_id= $request->input('customer_id');

        $order = Order::where('id', $order_id)->first();
        
        $customers          = get_customer($customer_id);
        $payment_methods    = payment_methods();

        $vouchers = Order::where('user_id', $customer_id)
            ->where('due_amount', '>', 0)
            ->where('online_order_delivery_status', 'delivered')
            ->whereNull('canceled_by')
            ->whereNull('cancel_date')
            ->orderBy('delivered_date', 'asc')
            ->get();
        // dd($vouchers);

        if(isset($order)){
            $due_amount = $order->due_amount ? $order->due_amount : 0;
        }else{
            $due_amount = 0;
        }
        // dd($order_id);

        return view('backend.accounts.customer_receive.orderwise', compact('customers', 'vouchers', 'payment_methods', 'order_id', 'customer_id', 'due_amount'));
    }
}
