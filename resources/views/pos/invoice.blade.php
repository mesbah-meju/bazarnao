<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Module</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .pos-module {
            width: 80mm;
            margin: 0px auto;
            
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            text-align: center;
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .info-pair {
            font-size: 15px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .info-item table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px; 
        }

        .info-item th, .info-item td {
            border: 1px solid #ddd;
            padding: 1px;
            text-align: center;
        }

        .info-item th {
            background-color: #f2f2f2;
        }

        .info-pair-two{
            font-size: 15px;
			display: flex;	
			justify-content: end;
            margin-right: 5px;

        }
        .info-level-footer{
            
			justify-content: space-between;

        }

        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>
@php
    $logo = get_setting('header_logo');

    $customer_phone = \App\Models\Customer::join('users','users.id','=','customers.user_id')->where('customers.user_id', $order->user_id)->first()->phone;

    if($order->user_id != null){
        $customer_id = \App\Models\Customer::where('user_id', $order->user_id)->first()->customer_id;
        $customer_name = \App\Models\Customer::where('user_id', $order->user_id)->first()->customer_id;
    }else{
        $customer_id = 'Guest ('.$order->guest_id.')';}
@endphp
<div class="pos-module">
    <div class="header">
        <h2 class="text-center">BazarNao</h2>
    </div>

    <div class="info-pair">
        <span class="info-label"><b>Date:</b></span>
        <span class="info-value">{{ $order->created_at->format('Y-m-d H:i:s') }}</span>
    </div>

    <!-- Add more info-pair sections as needed -->
    <div class="info-pair">
        <span class="info-label"><b>Invoice: </b></span>
        <span class="info-value">{{ $order->code }}</span>
    </div>

    <div class="info-pair">
        <span class="info-label"><b>Customer ID: </b></span>
        <span class="info-value">{{ $customer_id }}</span>
    </div>

    @if(isset($customer_information->name))
    <div class="info-pair">
        <span class="info-label"><b>Name: </b></span>
        <span class="info-value">{{ $customer_information->name }}</span>
    </div>
    @endif


    <!-- @if(isset($customer_information->address)) -->
    <div class="info-pair">
        <span class="info-label"><b>Address: </b></span>
        <span class="info-value">{{ $customer_information->address }}</span>
    </div>
    <!-- @endif -->

    <div class="info-pair">
        <span class="info-label"><b>Phone: </b></span>
        <span class="info-value">{{ $customer_phone }}</span>
    </div>

    <!-- Item details section -->
    <div class="info-item">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <!-- <th>Total</th> -->
                    <th>Discount</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <!-- Order Details Rows -->
                <!-- ... Your PHP-generated order details here ... -->
                @php
                $total = 0;
                $subtotal = 0;
                $totalDisc = 0;
                $special = 0;
                @endphp
                @foreach ($order->orderDetails as $key => $orderDetail)
                    @if ($orderDetail->product != null)
                            @php
                                $total += $orderDetail->price+$orderDetail->tax;
                                $subtotal += ($orderDetail->price+$orderDetail->discount)+$orderDetail->tax;
                                $totalDisc += $orderDetail->discount;
                                $special += $orderDetail->special_discount;
                            @endphp
                            <tr class="prd">
                            <td><b>{{ $key+1 }}</b></td>
                            <td class="gry-color small">
                                @if ($orderDetail->product != null)
                                    @php
                                        $group_product_check = \App\Models\Product::where('id', $orderDetail->product_id)->value('is_group_product');
                                    @endphp

                                    @if ($group_product_check == 1)
                                        @php
                                            $group_product_items = \App\Models\Group_product::where('group_product_id', $orderDetail->product_id)->get();
                                        @endphp

                                        <b>{{ $orderDetail->product->getTranslation('name') }}</b>
                                        @if($orderDetail->variation != null)
                                            ({{ $orderDetail->variation }})
                                        @endif
                                        <br>
                                        @foreach ($group_product_items as $item)
                                            @php
                                                $product_name = \App\Models\Product::where('id', $item->product_id)->value('name');
                                            @endphp
                                            <li>{{ $product_name }} Qty: ({{ $item->qty }})</li>
                                        @endforeach
                                    @else
                                        <b>{{ $orderDetail->product->getTranslation('name') }}</b>
                                        @if($orderDetail->variation != null)
                                            ({{ $orderDetail->variation }})
                                        @endif
                                    @endif
                                @else
                                    <strong>{{ translate('Product Unavailable') }}</strong>
                                @endif
                            </td>

                            <!-- <td class="gry-color small"><b>{{ $orderDetail->product->getTranslation('name') }} @if($orderDetail->variation != null) ({{ $orderDetail->variation }}) @endif</b></td> -->
                            <td class="gry-color" style="text-align:center"><b>{{ $orderDetail->quantity }}</b></td>
                            <!-- <td class="gry-color currency" style="text-align:center"><b>{{ number_format($orderDetail->price+$orderDetail->discount/$orderDetail->quantity,2) }}/-</b></td> -->
                            <td class="gry-color currency" style="text-align:center"><b>{{ number_format(($orderDetail->price+$orderDetail->discount)/$orderDetail->quantity,2) }}/-</b></td>
                            <!-- <td class="text-right currency"><b>{{ number_format(($orderDetail->price+$orderDetail->discount)+$orderDetail->tax,2) }}/-</b></td> -->
                            <td class="text-right currency"><b>{{ number_format($orderDetail->discount,2) }}/-</b></td>
                            <td class="text-right currency"><b>{{ number_format($orderDetail->price+$orderDetail->tax,2) }}/-</b></td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td style="text-align:right;font-weight:bold" colspan="3">Total</td>
                    <td class="text-right currency" style="font-weight:bold">{{ $subtotal }}/-</td>
                    <td class="text-right currency" style="font-weight:bold">{{ $totalDisc }}/-</td>
                    <td class="text-right currency" style="font-weight:bold">{{ $total }}/-</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Add more sections for Coupon Discount, Special Discount, Shipping Cost, Grand Total, Paid, Due, Received Amount, Change Amount, etc. -->
    @php
        $shipping_address = json_decode($order->shipping_address);
        if(!empty($order->payment_details) && $order->payment_type =='bkash'){
        $payment = json_decode($order->payment_details);
        if(!empty($payment)){
            if(!empty($payment->paymentID)){
                $payment_type = 'bkash';
            }else{
                $payment_type = ucfirst('Nagad');
            }
        }
        }else{
            $payment_type = $order->payment_type;
        }
    @endphp
            <br>
                <div style="width:100%;float:right;">
                        <div class="mb-4">
                            <div class="info-item two">
                                <!-- <div class="info-pair-two">
                                <div class="info-level-footer">
                                    <span class="info-label"><b>Sub Total: </b></span>
                                    <span class="info-value">{{ single_price($total) }}</span>
                                    </div>
                                </div> -->

                                @if(!empty($order->coupon_discount))
                                    <div class="info-pair-two">
                                        <div class="info-level-footer">
                                            <span class="info-label"><b>Coupon Discount: </b></span>
                                            <span class="info-value">{{ single_price($order->coupon_discount) }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($order->special_discount))
                                    <div class="info-pair-two">
                                        <div class="info-level-footer">
                                            <span class="info-label"><b>Special Discount: </b></span>
                                            <span class="info-value">{{ single_price($order->special_discount) }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($order->orderDetails->sum('shipping_cost')))
                                <div class="info-pair-two">
                                    <div class="info-level-footer">
                                        <span class="info-label"><b>Shipping Cost: </b></span>
                                        <span class="info-value">{{ single_price($order->orderDetails->sum('shipping_cost')) }}</span>
                                    </div>
                                </div>
                                @endif
                                
                                @if(!empty($order->special_discount))
                                    <div class="info-pair-two">
                                    <div class="info-level-footer">
                                        <span class="info-label"><b>Special Discount: </b></span>
                                        <span class="info-value">{{ single_price($order->special_discount) }}</span>
                                        </div>
                                    </div>
                                @endif

                                @php 
                                $paid = 0;
                                    if(!empty($order->payment_details)){
                                    $payment = json_decode($order->payment_details);
                                    if(!empty($payment)){
                                        $paid = $payment->amount;
                                    }
                                    }
                                @endphp

                                <div class="info-pair-two">
                                    <div class="info-level-footer">
                                        <span class="info-label"><b>Total: </b></span>
                                        <span class="info-value">{{ single_price($order->grand_total) }}</span>
                                    </div>
                                </div>

                                <div class="info-pair-two">
                                    <div class="info-level-footer">
                                        <span class="info-label"><b>Paid: </b></span>
                                        <span class="info-value">{{ single_price($paid) }}</span>
                                    </div>
                                </div>

                                <div class="info-pair-two">
                                    <div class="info-level-footer">
                                        <span class="info-label"><b>Due: </b></span>
                                        <span class="info-value">{{ single_price($order->grand_total-$paid) }}</span>
                                    </div>
                                </div>

                                <div class="info-pair-two">
                                    <div class="info-level-footer">
                                        <span class="info-label"><b>Recieved Amount: </b></span>
                                        <span class="info-value">{{ single_price($order->received_amount) }}</span>
                                    </div>
                                </div>

                                <div class="info-pair-two">
                                    <div class="info-level-footer">
                                        <span class="info-label"><b>Change Amount: </b></span>
                                        <span class="info-value">{{ single_price($order->change_amount) }}</span>
                                    </div>
                                </div>
                                
                                <div class="info-pair-two">
                                    <div class="info-level-footer">
                                        <span class="info-label"><b>Total Due: </b></span>
                                        <span class="info-value">
                                            {{ single_price(($total_due_count ?? 0)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <br>
                        </div>
                    </div>
    <div class="footer">
        <hr>
        <b><p>Thank you for shopping with us!</p><b>
    </div>
</div>

<script type="text/javascript">
    function auto_print() {
        window.print();
    }
    setTimeout(auto_print, 1000);
</script>
</body>
</html>
