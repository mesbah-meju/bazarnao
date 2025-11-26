@extends('backend.layouts.app')
@php
    $user_name = auth()->user()->name;
@endphp

@if ($user_name == 'Super Admin' || auth()->user()->staff->role->name == 'Sales Executive')
    {{-- $staff_role = 'Sales Executive'; --}}
@endif
@section('content')

@section('content')

    <div class="card">
        <div class="card-header">
            <h1 class="h2 fs-16 mb-0">{{ translate('Order Details') }}</h1>
        </div>
        @if ($user_name == 'Delivery Department')
            <div class="card-header row gutters-5">
                <div class="col text-center text-md-left">
                </div>
                @php
                    $delivery_status = $order->orderDetails->first()->delivery_status;
                    $payment_status = $order->orderDetails->first()->payment_status;
                @endphp
                <div class="col-md-3 ml-auto">
                    {{-- <label for="update_delivery_status">{{ translate('Delivery Status') }}</label> --}}
                    <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                        id="update_delivery_status">
                        <option value="pending" @if ($delivery_status == 'pending') selected @endif>{{ translate('Pending') }}
                        </option>
                        <option value="cancel" @if ($delivery_status == 'cancel') selected @endif>{{ translate('Cancel') }}
                        </option>
                        <option value="confirmed" @if ($delivery_status == 'confirmed') selected @endif>
                            {{ translate('Confirmed') }}</option>
                        <option value="on_delivery" @if ($delivery_status == 'on_delivery') selected @endif>
                            {{ translate('On delivery') }}</option>
                        <option value=""></option>
                        <option value="delivered" @if ($delivery_status == 'delivered') selected @endif>
                            {{ translate('Delivered') }}</option>
                        <option value="received" @if ($delivery_status == 'received') selected @endif>
                            {{ translate('Received') }}</option>
                    </select>
                </div>
            </div>
        @elseif($user_name == 'Operational Department')
            <div class="card-header row gutters-5">
                <div class="col text-center text-md-left">
                </div>
                @php
                    $delivery_status = $order->orderDetails->first()->delivery_status;
                    $payment_status = $order->orderDetails->first()->payment_status;
                @endphp
                <div class="col-md-3 ml-auto">
                    {{-- <label for="update_delivery_status">{{ translate('Delivery Status') }}</label> --}}
                    <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                        id="update_delivery_status">
                        <option value=""></option>
                        <option value="on_delivery" @if ($delivery_status == 'on_delivery') selected @endif>
                            {{ translate('On delivery') }}</option>
                    </select>
                </div>
            </div>
        @elseif($user_name == 'Sales Department')
            <div class="card-header row gutters-5">
                <div class="col text-center text-md-left">
                </div>
                @php
                    $delivery_status = $order->orderDetails->first()->delivery_status;
                    $payment_status = $order->orderDetails->first()->payment_status;
                @endphp
                <div class="col-md-3 ml-auto">
                    {{-- <label for="update_delivery_status">{{ translate('Delivery Status') }}</label> --}}
                    <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                        id="update_delivery_status">
                        <option value=""></option>
                        <option value="pending" @if ($delivery_status == 'pending') selected @endif>
                            {{ translate('Pending') }}</option>
                        <option value="confirmed" @if ($delivery_status == 'confirmed') selected @endif>
                            {{ translate('Confirmed') }}</option>
                        <option value="cancel" @if ($delivery_status == 'cancel') selected @endif>{{ translate('Cancel') }}
                        </option>
                    </select>
                </div>
            </div>
        @else
            <div class="card-header row gutters-5">
                @php
                    $delivery_status = $order->orderDetails->first()->delivery_status;
                    $payment_status = $order->orderDetails->first()->payment_status;
                @endphp
                @if ($user_name == 'Super Admin' || $user_name == 'Accountant Malibagh' || $user_name == 'Accountant Mirpur' || $user_name == 'Account Department' || $user_name == 'Sales Executive')
                <div class="col text-center text-md-left">
                    <a href="javascript:" class="btn btn-primary" onClick="return customer_received('<?php echo $order->id; ?>', '<?php echo $order->user_id; ?>');">Payment Received</a>
                </div>
                @endif
                
                <div class="col-md-3 ml-auto text-md-right">
                    {{-- <label for="update_payment_status">{{ translate('Payment Status') }}</label>
                    <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                        id="update_payment_status">
                        @if ($user_name == 'Super Admin' || $user_name == 'Accountant Malibagh' || $user_name == 'Accountant Mirpur' || $user_name == 'Account Department' || $user_name == 'Sales Executive')
                        <option value="">{{ translate('Select Payment Status') }}</option>
                        <option value="paid" {{ $payment_status == 'paid' ? 'selected' : '' }}>{{ translate('Paid') }}</option>
                        <option value="partial" {{ $payment_status == 'partial' ? 'selected' : '' }}>{{ translate('Partial Payment') }}</option>
                        @endif
                    </select> --}}
                </div>

                @if ($user_name == 'Super Admin')
                    <div class="col-md-3 ml-auto">
                        {{-- <label for="update_delivery_status">{{ translate('Delivery Status') }}</label> --}}
                        @if ($delivery_status != 'delivered' && $delivery_status != 'cancel')
                            <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                                id="update_delivery_status">
                                @if ($user_name == 'Super Admin' || $user_name == 'Account Department')
                                    @if ($delivery_status == 'pending')
                                        <option value="pending" @if ($delivery_status == 'pending') selected @endif>
                                            {{ translate('Pending') }}</option>
                                        <option value="cancel" @if ($delivery_status == 'cancel') selected @endif>
                                            {{ translate('Cancel') }}</option>
                                        <option value="confirmed" @if ($delivery_status == 'confirmed') selected @endif>
                                            {{ translate('Confirmed') }}</option>
                                    @elseif($delivery_status == 'confirmed')
                                        <option value="confirmed" @if ($delivery_status == 'confirmed') selected @endif>
                                            {{ translate('Confirmed') }}</option>
                                        <option value="cancel" @if ($delivery_status == 'cancel') selected @endif>
                                            {{ translate('Cancel') }}</option>
                                        <option value="on_delivery" @if ($delivery_status == 'on_delivery') selected @endif>
                                            {{ translate('On delivery') }}</option>
                                    @elseif($delivery_status == 'on_delivery')
                                        <option value="on_delivery" @if ($delivery_status == 'on_delivery') selected @endif>
                                            {{ translate('On delivery') }}</option>
                                        <option value="delivered" @if ($delivery_status == 'delivered') selected @endif>
                                            {{ translate('Delivered') }}</option>
                                        <option value="received" @if ($delivery_status == 'received') selected @endif>
                                            {{ translate('Received') }}</option>
                                        <option value="cancel" @if ($delivery_status == 'cancel') selected @endif>
                                            {{ translate('Cancel') }}</option>
                                    @else
                                        <option value="delivered" @if ($delivery_status == 'delivered') selected @endif>
                                            {{ translate('Delivered') }}</option>
                                        <option value="received" @if ($delivery_status == 'received') selected @endif>
                                            {{ translate('Received') }}</option>
                                    @endif
                                @else
                                    <option value="pending" @if ($delivery_status == 'pending') selected @endif>
                                        {{ translate('Pending') }}</option>
                                    <option value="cancel" @if ($delivery_status == 'cancel') selected @endif>
                                        {{ translate('Cancel') }}</option>
                                    <option value="confirmed" @if ($delivery_status == 'confirmed') selected @endif>
                                        {{ translate('Confirmed') }}</option>
                                @endif
                            </select>
                        @else
                            <input type="text" class="form-control" value="{{ $delivery_status }}" disabled>
                        @endif
                    </div>
                @endif
            </div>

        @endif

        <?php
        use App\Models\RefundRequest;
        $refunds = RefundRequest::where('order_id', $order->id)->where('refund_status',5)->get();
        ?>

        <div class="card-body">
            <div class="row gutters-5">
                <div class="col text-center text-md-left">
                    <address>
                        @if (!empty($order->shipping_address))
                            <strong class="text-main">{{ json_decode($order->shipping_address)->name }}</strong><br>
                            {{ json_decode($order->shipping_address)->email ?? '' }}<br>
                            {{ json_decode($order->shipping_address)->phone ?? '' }}<br>
                            {{ json_decode($order->shipping_address)->address ?? '' }},
                            {{ json_decode($order->shipping_address)->city ?? '' }},
                            {{ json_decode($order->shipping_address)->postal_code ?? '' }}<br>
                            {{ json_decode($order->shipping_address)->country ?? '' }}
                        @endif

                    </address>
                    @if ($order->manual_payment && is_array(json_decode($order->manual_payment_data, true)))
                        <br>
                        <strong class="text-main">{{ translate('Payment Information') }}</strong><br>
                        {{ translate('Name') }}: {{ json_decode($order->manual_payment_data)->name }},
                        {{ translate('Amount') }}: {{ single_price(json_decode($order->manual_payment_data)->amount) }},
                        {{ translate('TRX ID') }}: {{ json_decode($order->manual_payment_data)->trx_id }}
                        <br>
                        <a href="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}" target="_blank"><img src="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}" alt="" height="100"></a>
                    @endif
                </div>
                <div class="col-md-4 ml-auto d-flex justify-content-end">
                    <table>
                        <tbody>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Order #') }}</td>
                                <td class="text-right text-info text-bold"> {{ $order->code }}</td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Order Status') }}</td>
                                @php
                                    $status = $order->orderDetails->first()->delivery_status;
                                @endphp
                                <td class="text-right">
                                    @if ($status == 'delivered')
                                        <span
                                            class="badge badge-inline badge-success">{{ translate(ucfirst(str_replace('_', ' ', $status))) }}</span>
                                    @else
                                        <span
                                            class="badge badge-inline badge-info">{{ translate(ucfirst(str_replace('_', ' ', $status))) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Payment Status') }} </td>
                                <td class="text-right">{{ $payment_status }}</td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Order Date') }} </td>
                                <td class="text-right">{{ date('d-m-Y h:i A', $order->date) }}</td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Total amount') }} </td>
                                <td class="text-right">
                                    {{ single_price($order->grand_total) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Payment method') }}</td>
                                <td class="text-right">{{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <hr class="new-section-sm bord-no">
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table table-bordered aiz-table invoice-summary">
                        <thead>
                            <tr class="bg-trans-dark">
                                <th class="min-col">#</th>
                                <th width="10%">{{ translate('Photo') }}</th>
                                <th class="text-uppercase">{{ translate('Description') }}</th>
                                <th class="text-uppercase">{{ translate('Delivery Type') }}</th>
                                <th class="min-col text-center text-uppercase">{{ translate('Qty') }}</th>
                                <th class="min-col text-center text-uppercase">{{ translate('Price') }}</th>
                                <th class="min-col text-right text-uppercase">{{ translate('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderDetails as $key => $orderDetail)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        @if ($orderDetail->product != null)
                                            <a href="{{ route('product', $orderDetail->product->slug) }}"
                                                target="_blank"><img height="50"
                                                    src="{{ uploaded_asset($orderDetail->product->thumbnail_img) }}"></a>
                                        @else
                                            <strong>{{ translate('N/A') }}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($orderDetail->product != null)
                                            <strong><a href="{{ route('product', $orderDetail->product->slug) }}"
                                                    target="_blank"
                                                    class="text-muted">{{ $orderDetail->product->getTranslation('name') }}</a></strong>
                                            <small>{{ $orderDetail->variation }}</small>
                                        @else
                                            <strong>{{ translate('Product Unavailable') }}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($orderDetail->shipping_type != null && $orderDetail->shipping_type == 'home_delivery')
                                            {{ translate('Home Delivery') }}
                                        @elseif ($orderDetail->shipping_type == 'pickup_point')
                                            @if ($orderDetail->pickup_point != null)
                                                {{ $orderDetail->pickup_point->getTranslation('name') }}
                                                ({{ translate('Pickup Point') }})
                                            @else
                                                {{ translate('Pickup Point') }}
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $orderDetail->quantity }}</td>
                                    <td class="text-center">
                                        {{ single_price(($orderDetail->price + $orderDetail->discount) / $orderDetail->quantity) }}
                                    </td>
                                    <td class="text-center">{{ single_price($orderDetail->price + $orderDetail->discount) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="clearfix float-right">
                <table class="table">
                    <tbody>
                        <tr>
                            <td>
                                <strong class="text-muted">{{ translate('Sub Total') }} :</strong>
                            </td>
                            <td>
                                {{ single_price($order->orderDetails->sum('price') + $order->orderDetails->sum('discount')) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="text-muted">{{ translate('Tax') }} :</strong>
                            </td>
                            <td>
                                {{ single_price($order->orderDetails->sum('tax')) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="text-muted">{{ translate('Shipping') }} :</strong>
                            </td>
                            <td>
                                {{ single_price($order->orderDetails->sum('shipping_cost')) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="text-muted">{{ translate('Coupon') }} :</strong>
                            </td>
                            <td>
                                {{ single_price($order->coupon_discount) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="text-muted">{{ translate('Discount') }} :</strong>
                            </td>
                            <td class="text-muted h5">
                                <?php
                                $total_discount = $order->orderDetails->sum('discount');
                                ?>
                                @if ($total_discount)
                                    {{ single_price($total_discount) }}
                                @elseif($order->order_from == 'Web')
                                    {{ single_price(\App\Models\Customer_ledger::where('type', 'Discount')->where('order_id', $order->id)->sum('credit')) }}
                                @else
                                    {{ single_price($order->special_discount) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="text-muted">{{ translate('TOTAL') }} :</strong>
                            </td>
                            <td class="text-muted h5">
                                {{ single_price($order->grand_total) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="text-muted">{{ translate('Paid') }} :</strong>
                            </td>
                            <td class="text-muted h5">
                                @php
                                    $paid = 0;
                                    if (!empty($order->payment_details)) {
                                        $payment = json_decode($order->payment_details);
                                        if (!empty($payment)) {
                                            $paid = $payment->amount;
                                        }
                                    }
                                @endphp
                                {{ single_price($paid) }}
                                <input type="hidden" id="total_paid" value="{{ $paid }}">
                            </td>
                        </tr>
                        @if ($refunds->isNotEmpty())
                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('Total Refund') }} :</strong>
                                </td>
                                <?php
                                $total_refund = 0;
                                
                                foreach ($refunds as $refund) {
                                    $total_refund += $refund->return_amount;
                                }
                                ?>
                                <td class="text-muted h5">
                                    {{ single_price($total_refund) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('Due') }} :</strong>
                                </td>
                                <td class="text-muted h5">
                                    {{ single_price($order->grand_total - $paid) }}<input type="hidden" id="total_due"
                                        value="{{ $order->grand_total - $paid }}">
                                </td>
                            </tr>
                            @php
                                $due_count = 0;
                                $refund_amount = 0;
                                $order_ids = $user_all_orders_with_recent->pluck('id');
                                $total_refunds = App\Models\RefundRequest::whereIn('order_id', $order_ids)
                                    ->where('refund_status', 5)
                                    ->pluck('refund_amount', 'order_id');

                                foreach ($user_all_orders_with_recent as $user_order) {
                                        $refund_amount = $total_refunds[$user_order->id] ?? 0;

                                    if ($user_order->due_amount - $refund_amount > 0) {
                                        $due_count += $user_order->due_amount - $refund_amount;
                                    }
                                }
                            @endphp

                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('Total Due') }} :</strong>
                                </td>
                                <td class="text-muted h5">
                                    {{ single_price($due_count) }}
                                    <input type="hidden" id="in_total_due" value="{{ $order->grand_total - $paid - $total_refund + $due_count }}">
                                </td>
                            </tr>

                        @else
                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('Due') }} :</strong>
                                </td>
                                <td class="text-muted h5">
                                    {{ single_price($order->grand_total - $paid) }}<input type="hidden" id="total_due"
                                        value="{{ $order->grand_total - $paid }}">
                                </td>
                            </tr>
                            @php
                                $due_count = 0;
                                $refund_amount = 0;
                                $order_ids = $user_all_orders_with_recent->pluck('id');
                                $total_refunds = App\Models\RefundRequest::whereIn('order_id', $order_ids)
                                    ->where('refund_status', 5)
                                ->pluck('refund_amount', 'order_id');

                                foreach ($user_all_orders_with_recent as $user_order) {
                                        $refund_amount = $total_refunds[$user_order->id] ?? 0;

                                    if ($user_order->due_amount - $refund_amount > 0) {
                                        $due_count += $user_order->due_amount - $refund_amount;
                                    }
                                }
                            @endphp

                            <tr>
                                <td>
                                    <strong class="text-muted">{{ translate('Total Due') }} :</strong>
                                </td>
                                <td class="text-muted h5">
                                    @php       
                                        $total_due_count = $due_count;
                                    @endphp
                                    {{ single_price($total_due_count) }}
                                    <input type="hidden" id="in_total_due" value="{{ $total_due_count }}">
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="text-right no-print mb-2">
                    <a target="_blank" href="{{ route('invoice.download', $order->id) }}" type="button"
                        class="btn btn-icon btn-light"><i class="las la-print"></i></a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <h4 class="text-center mt-5">{{ translate('Previous Due with order number') }}</h4>
                    <div class="table-responsive" style="overflow-x: hidden; white-space: nowrap;">
                        <table class="table table-sm table-bordered aiz-table text-center">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width:5%">#</th>
                                    <th style="width:10%">{{ translate('Order Code') }}</th>
                                    <th style="width:10%">{{ translate('Total') }}</th>
                                    <th style="width:10%">{{ translate('Paid') }}</th>
                                    <th style="width:10%">{{ translate('Due') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $previous_due_count = 0; 
                                    $previous_refund_amount = 0;
                                    $i = 1;
                                    $previous_order_ids = $user_all_orders_without_recent->pluck('id');
                                    $previous_refunds = App\Models\RefundRequest::whereIn('order_id', $order_ids)
                                        ->where('refund_status', 5)
                                        ->pluck('refund_amount', 'order_id');
                                @endphp
                                @foreach ($user_all_orders_without_recent as $user_order)
                                    @php
                                        $previous_refund_amount = $previous_refunds[$user_order->id] ?? 0;
                                    @endphp
                                    @if ($user_order->due_amount - $previous_refund_amount > 0) 
                                        @php 
                                            $previous_due_count += $user_order->due_amount - $previous_refund_amount;
                                        @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>
                                                <a href="{{ route('all_orders.show', encrypt($user_order->id)) }}" target="_blank"
                                                title="{{ translate('View') }}">{{ $user_order->code }}</a>
                                            </td>
                                            <td>{{ single_price($user_order->grand_total) }}</td>
                                            <td>{{ single_price($user_order->grand_total - $user_order->due_amount) }}</td>
                                            <td>{{ single_price($user_order->due_amount - $previous_refund_amount) }}</td>
                                        </tr>
                                    @endif
                                @endforeach

                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="4" class="text-end"><strong>{{ translate('Total Previous due') }}:</strong></td>
                                    <td><strong>{{ single_price($previous_due_count) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="payment-modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">{{ translate('Payment') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-3">
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Amount') }}</label>
                            </div>
                            <div class="col-md-10">
                                <input type="number" id="payment_amount" value="{{ $order->grand_total }}"
                                    class="form-control textarea-autogrow mb-3" placeholder="{{ translate('Amount') }}"
                                    rows="1" name="amount" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Payment Date') }}</label>
                            </div>
                            <div class="col-md-10">
                                <input type="date" id="payment_date" value=""
                                    class="form-control textarea-autogrow mb-3" placeholder="{{ translate('Date') }}"
                                    rows="1" name="date" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="save_payment" onclick="save_payment()"
                        class="btn btn-primary">{{ translate('Save') }}</button>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="deliveryboy-modal" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">{{ translate('Select Delivery Man') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-3">
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Select Delivery Man') }}</label>
                            </div>
                            <div class="col-md-10">
                                <select class="form-control" id="delivery_boy">
                                    <option value="">Select Delivery Man</option>
                                    @foreach (App\Models\Staff::where('role_id', '10')->get() as $u)
                                        <option value="{{ $u->user->id }}">{{ $u->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="save_payment" onclick="save_delivery_man()"
                        class="btn btn-primary">{{ translate('Save') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('modals.common_modal')
@endsection

@section('script')
    <script type="text/javascript">
        $('#update_delivery_status').on('change', function()
        {
            var order_id = {{ $order->id }};
            var status = $('#update_delivery_status').val();
            
            if(status=='on_delivery')
            {
              alert('Please only utilize on delivery from the order scanning ');
              // $('#deliveryboy-modal').modal('show');
            }
            else if(status=='delivered')
            {
              $.post('{{ route('orders.product_stock_qty_check') }}',
               {_token:'{{ @csrf_token() }}',
               order_id:order_id,status:status},
                function(data){
                  if(data.message == 'false'){
                    AIZ.plugins.notify('warning', data.product+'Stock Qty Not Enough for Delivery');
                    return false;
                  }else if(data.message == 'warehouse'){
                    AIZ.plugins.notify('warning', 'Warehouse is not selected');
                    return false;
                  }
                  else{
                    $.post('{{ route('orders.update_delivery_status') }}', {
                        _token: '{{ @csrf_token() }}',
                        order_id: order_id,
                        status: status
                    }, function(data) {
                        if (data.message == 'true') {
                            AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
                            location.reload();
                        } else if(data.message == 'false') {
                            AIZ.plugins.notify('warning', data.product + ' Stock Qty Not Enough for Delivery');
                            location.reload();
                        }
                    });
              }
            });
            }else{
                $.post('{{ route('orders.update_delivery_status') }}', {_token:'{{ @csrf_token() }}',order_id:order_id,status:status}, function(data){
                        AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
                        location.reload();
                    });
            }
        });

        $('#update_payment_status').on('change', function() {
            var order_id = {{ $order->id }};
            var dueAmount = parseInt($('#total_due').val());

            var status = $('#update_payment_status').val();
            if (status == 'paid') {
                $('#payment-modal').modal('show');
                $('#payment_amount').val(dueAmount);
                $('#payment_amount').attr('disabled', true);
            } else if (status == 'partial') {

                $('#payment-modal').modal('show');
                $('#payment_amount').val('');
                $('#payment_amount').val(dueAmount);
                $('#payment_amount').attr('disabled', false);
            } else if (status == 'unpaid') {
                $.post('{{ route('orders.update_payment_status') }}', {
                    _token: '{{ @csrf_token() }}',
                    order_id: order_id,
                    status: status
                }, function(data) {
                    AIZ.plugins.notify('success', '{{ translate('Payment status has been updated') }}');
                    location.reload().setTimeOut(500);
                });
            }

        });

        function save_payment() {
            $('#save_payment').attr('disabled', true);
            var dueAmount = parseInt($('#total_due').val());

            var order_id = {{ $order->id }};
            var status = $('#update_payment_status').val();
            var payment_amount = parseInt($('#payment_amount').val());
            var payment_date = $('#payment_date').val();

            if (payment_amount > dueAmount) {
                alert('Paid amount must be less than or equal from due amount');
                $('#payment_amount').val('');
                $('#save_payment').attr('disabled', false);
                return false;
            }

            if (payment_date == '') {
                alert('Please enter the date');
                $('#save_payment').attr('disabled', false);
                return false;
            }

            $.post('{{ route('orders.update_payment_status') }}', {
                _token: '{{ @csrf_token() }}',
                payment_amount: payment_amount,
                payment_date: payment_date,
                order_id: order_id,
                status: status
            }, function(data) {
                AIZ.plugins.notify('success', '{{ translate('Payment status has been updated') }}');
                $('#payment-modal').modal('hide')
                location.reload().setTimeOut(500);
            });
        }

        function save_delivery_man() {
            var order_id = {{ $order->id }};
            var status = 'on_delivery';
            var delivery_boy = $('#delivery_boy').val();

            $.post('{{ route('orders.update_delivery_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status,
                delivery_boy: delivery_boy
            }, function(data) {
                AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
                $('#deliveryboy-modal').modal('hide')
                location.reload().setTimeOut(500);
            });
        }

        function customer_received(order_id, customer_id) {
            $('#common-modal .modal-title').html('');
            $('#common-modal .modal-body').html('');

            var title = 'Customer Received';

            $.post('{{ route("customer-receive.orderwise") }}', {
                _token      : AIZ.data.csrf, 
                order_id    : order_id,
                customer_id : customer_id
            }, function(data){
                $('#common-modal .modal-title').html(title);
                $('#common-modal .modal-body').html(data);
                $('#common-modal .modal-dialog').removeClass('modal-lg');
                $('#common-modal .modal-dialog').addClass('modal-xl');
                $('#common-modal').modal('show');
            });
        }
    </script>
@endsection
