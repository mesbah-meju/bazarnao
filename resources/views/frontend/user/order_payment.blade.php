@extends('frontend.layouts.app')

@section('content')

<section class="py-5">
    <div class="container">
        <div class="d-flex align-items-start">
            @include('frontend.inc.user_side_nav')
            <div class="aiz-user-panel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{ translate('Order Payment') }}</h5>
                    </div>

                    <div class="card-body">

                        <form action="{{route('purchase_history.payment.checkout')}}" method="POST">
                            @csrf
                            <div class="row">
                                <input type="hidden" value="{{ $order->id }}" name="order_id" />
                                <div class="col-xxl-8 col-xl-10 mx-auto">
                                    <div class="row gutters-10">

                                        @if(\App\Models\BusinessSetting::where('type', 'sslcommerz_payment')->first()->value == 1)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="sslcommerz" class="online_payment" type="radio" name="payment_option" checked>
                                                <span class="d-block p-3 aiz-megabox-elem">
                                                    <img src="{{ static_asset('assets/img/cards/sslcommerz.png')}}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ translate('sslcommerz')}}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        @endif

                                        @if(\App\Models\BusinessSetting::where('type', 'nagad')->first()->value == 1)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="nagad" class="online_payment" type="radio" name="payment_option" checked>
                                                <span class="d-block p-3 aiz-megabox-elem">
                                                    <img src="{{ static_asset('assets/img/cards/nagad.png')}}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ translate('Nagad')}}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        @endif
                                        @if(\App\Models\BusinessSetting::where('type', 'bkash')->first()->value == 1)

                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="bkash" class="online_payment" type="radio" name="payment_option" checked>
                                                <span class="d-block p-3 aiz-megabox-elem">
                                                    <img src="{{ static_asset('assets/img/cards/bkash.png')}}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ translate('Bkash')}}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        @endif
                                        @if(\App\Models\Addon::where('unique_identifier', 'african_pg')->first() != null && \App\Models\Addon::where('unique_identifier', 'african_pg')->first()->activated)
                                        @if(\App\Models\BusinessSetting::where('type', 'mpesa')->first()->value == 1)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="mpesa" class="online_payment" type="radio" name="payment_option" checked>
                                                <span class="d-block p-3 aiz-megabox-elem">
                                                    <img src="{{ static_asset('assets/img/cards/mpesa.png')}}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ translate('mpesa')}}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        @endif
                                        @if(\App\Models\BusinessSetting::where('type', 'flutterwave')->first()->value == 1)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="flutterwave" class="online_payment" type="radio" name="payment_option" checked>
                                                <span class="d-block p-3 aiz-megabox-elem">
                                                    <img src="{{ static_asset('assets/img/cards/flutterwave.png')}}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ translate('flutterwave')}}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        @endif
                                        @if(\App\Models\BusinessSetting::where('type', 'payfast')->first()->value == 1)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="payfast" class="online_payment" type="radio" name="payment_option" checked>
                                                <span class="d-block p-3 aiz-megabox-elem">
                                                    <img src="{{ static_asset('assets/img/cards/payfast.png')}}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ translate('payfast')}}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        @endif
                                        @endif
                                        @if(\App\Models\Addon::where('unique_identifier', 'paytm')->first() != null && \App\Models\Addon::where('unique_identifier', 'paytm')->first()->activated)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="paytm" class="online_payment" type="radio" name="payment_option" checked>
                                                <span class="d-block p-3 aiz-megabox-elem">
                                                    <img src="{{ static_asset('assets/img/cards/paytm.jpg')}}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ translate('Paytm')}}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        @endif
                                        @if(\App\Models\BusinessSetting::where('type', 'cash_payment')->first()->value == 1)
                                        @php
                                        $digital = 0;
                                        $cart_item_info=Session::get('cart');
                                        if(!empty($cart_item_info)){
                                        foreach(Session::get('cart') as $cartItem){
                                        if($cartItem['digital'] == 1){
                                        $digital = 1;
                                        }
                                        }
                                        }

                                        @endphp
                                        @if($digital != 1)

                                        @endif
                                        @endif
                                        @if (Auth::check())
                                        @if (\App\Models\Addon::where('unique_identifier', 'offline_payment')->first() != null && \App\Models\Addon::where('unique_identifier', 'offline_payment')->first()->activated)
                                        @foreach(\App\Models\ManualPaymentMethod::all() as $method)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="{{ $method->heading }}" type="radio" name="payment_option" onchange="toggleManualPaymentData({{ $method->id }})" data-id="{{ $method->id }}" checked>
                                                <span class="d-block p-3 aiz-megabox-elem">
                                                    <img src="{{ uploaded_asset($method->photo) }}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ $method->heading }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        @endforeach

                                        @foreach(\App\Models\ManualPaymentMethod::all() as $method)
                                        <div id="manual_payment_info_{{ $method->id }}" class="d-none">
                                            @php echo $method->description @endphp
                                            @if ($method->bank_info != null)
                                            <ul>
                                                @foreach (json_decode($method->bank_info) as $key => $info)
                                                <li>{{ translate('Bank Name') }} - {{ $info->bank_name }}, {{ translate('Account Name') }} - {{ $info->account_name }}, {{ translate('Account Number') }} - {{ $info->account_number}}, {{ translate('Routing Number') }} - {{ $info->routing_number }}</li>
                                                @endforeach
                                            </ul>
                                            @endif
                                        </div>
                                        @endforeach
                                        @endif
                                        @endif
                                    </div>
                                </div>
                            </div>








                            <div class="card-header row gutters-5">
                                <div class="col text-center text-md-left">
                                </div>
                                @php
                                $delivery_status = $order->orderDetails->first()->delivery_status;
                                $payment_status = $order->orderDetails->first()->payment_status;
                                @endphp

                            </div>
                            <div class="card-body">
                                <input type="hidden" name="user_id" value="{{$order->user_id}}">
                                <div class="row gutters-5">
                                    <div class="col text-center text-md-left">
                                        <address>
                                            <strong class="text-main">{{ json_decode($order->shipping_address)->name }}</strong><br>
                                            {{ json_decode($order->shipping_address)->email }}<br>
                                            {{ json_decode($order->shipping_address)->phone }}<br>
                                            {{ json_decode($order->shipping_address)->address }}, {{ json_decode($order->shipping_address)->city }}, {{ json_decode($order->shipping_address)->postal_code }}<br>
                                            {{ json_decode($order->shipping_address)->country }}
                                        </address>
                                        @if ($order->manual_payment && is_array(json_decode($order->manual_payment_data, true)))
                                        <br>
                                        <strong class="text-main">{{ translate('Payment Information') }}</strong><br>
                                        {{ translate('Name') }}: {{ json_decode($order->manual_payment_data)->name }}, {{ translate('Amount') }}: {{ single_price(json_decode($order->manual_payment_data)->amount) }}, {{ translate('TRX ID') }}: {{ json_decode($order->manual_payment_data)->trx_id }}
                                        <br>
                                        <a href="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}" target="_blank"><img src="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}" alt="" height="100"></a>
                                        @endif
                                    </div>
                                    <div class="col-md-4 ml-auto">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td class="text-main text-bold">{{translate('Order #')}}</td>
                                                    <td class="text-right text-info text-bold"> {{ $order->code }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-main text-bold">{{translate('Order Status')}}</td>
                                                    @php
                                                    $status = $order->orderDetails->first()->delivery_status;
                                                    @endphp
                                                    <td class="text-right">
                                                        @if($status == 'delivered')
                                                        <span class="badge badge-inline badge-success">{{ translate(ucfirst(str_replace('_', ' ', $status))) }}</span>
                                                        @else
                                                        <span class="badge badge-inline badge-info">{{ translate(ucfirst(str_replace('_', ' ', $status))) }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-main text-bold">{{translate('Order Date')}} </td>
                                                    <td class="text-right">{{ date('d-m-Y h:i A', $order->date) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-main text-bold">{{translate('Total amount')}} </td>
                                                    <td class="text-right">
                                                        {{ single_price($order->grand_total) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-main text-bold">{{translate('Payment method')}}</td>
                                                    <td class="text-right">{{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <hr class="new-section-sm bord-no">
                                <div class="row">
                                    <div class="col-lg-12 table-responsive">

                                        <table class="table table-bordered  invoice-summary">
                                            <thead>
                                                <tr class="bg-trans-dark">

                                                    <th class="min-col">#</th>
                                                    <th width="50%">{{translate('Product')}}</th>
                                                    <!-- <th class="text-uppercase">{{translate('Description')}}</th> -->
                                                    <!-- <th class="text-uppercase">{{translate('Delivery Type')}}</th> -->
                                                    <th class="min-col text-center text-uppercase">{{translate('Qty')}}</th>
                                                    <th class="min-col text-center text-uppercase">{{translate('Price')}}</th>
                                                    <th class="min-col text-right text-uppercase">{{translate('Total')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="item_table">
                                                @foreach ($order->orderDetails as $key => $orderDetail)
                                                <tr class="tablerow" id="row_{{ $key+1 }}">



                                                    <td>{{ $key+1 }}</td>
                                                    <td>
                                                        <select class="form-control  aiz-selectpicker" name="product[{{$key+1}}]" id="product_{{$key+1}}" onchange="productChange(this)" data-live-search="true">
                                                            @foreach($products as $product)
                                                            @php
                                                            $acPrice = 0;
                                                            $disAmount = 0;
                                                            @endphp
                                                            @if($product->discount > 0)
                                                            @if($product->discount_type == 'amount')
                                                            @php
                                                            $disAmount = $product->discount;
                                                            @endphp
                                                            @else
                                                            @php
                                                            $disAmount = ($product->unit_price * $product->discount) / 100;
                                                            $disAmount = round($disAmount);

                                                            @endphp
                                                            @endif
                                                            @php
                                                            $acPrice =$product->unit_price- $disAmount;

                                                            @endphp
                                                            @else
                                                            @php
                                                            $acPrice = $product->unit_price
                                                            @endphp
                                                            @endif

                                                            <option @if($orderDetail->product->id==$product->id) {{'selected'}} @endif value="{{$product->id}}" data-price="{{ $acPrice }}" data-discount="{{ $disAmount }}">{{$product->name}}</option>
                                                            @endforeach
                                                        </select>

                                                    </td>

                                                    <td class="text-center">
                                                        <input type="number" value="{{ $orderDetail->quantity }}" name="qty[{{$key+1}}]" class="form-control" onblur="caculatePrice({{$key+1}})" id="qty_{{$key+1}}">
                                                        <input type="hidden" value="{{ $orderDetail->quantity }}" name="oldqty[{{$key+1}}]" class="form-control">
                                                    </td>
                                                    <td class="text-center"><input step="any" type="number" value="{{ $orderDetail->price/$orderDetail->quantity }}" name="rate[{{$key+1}}]" class="form-control" id="rate_{{$key+1}}"></td>
                                                    <td class="text-center">
                                                        <input id="dis_amount_{{$key+1}}" name="dis_amount[{{$key+1}}]" class="dis_amount" type="hidden" value="{{ $orderDetail->discount }}">
                                                        <input id="special_discount_{{$key+1}}" name="special_discount[{{$key+1}}]" class="special_discount" type="hidden" value="{{ $orderDetail->special_discount }}">
                                                        <input step="any" type="number" value="{{ $orderDetail->price }}" name="total[{{$key+1}}]" class="form-control" id="total_{{$key+1}}">
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
                                                    <strong class="text-muted">{{translate('Sub Total')}} :</strong>
                                                </td>
                                                <td>
                                                    <input class="form-control" type="text" name="sub_total" id="sub_total" value="{{ $order->orderDetails->sum('price') }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong class="text-muted">{{translate('Tax')}} :</strong>
                                                </td>
                                                <td>
                                                    {{ single_price($order->orderDetails->sum('tax')) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong class="text-muted">{{translate('Shipping')}} :</strong>
                                                </td>
                                                <td>
                                                    <input class="form-control" type="text" name="shippingcost" id="shippingcost" value="{{ $order->orderDetails->sum('shipping_cost') }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong class="text-muted">{{translate('Coupon')}} :</strong>
                                                </td>
                                                <td>
                                                    {{ single_price($order->coupon_discount) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong class="text-muted">{{translate('TOTAL')}} :</strong>
                                                </td>
                                                <td class="text-muted h5">
                                                    <input class="form-control" type="text" name="grand_total" id="grand_total" value="{{ $order->grand_total }}">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="text-right no-print">
                                        <a href="{{ route('invoice.download', $order->id) }}" type="button" class="btn btn-icon btn-light"><i class="las la-print"></i></a>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group col-lg-5">
                                <button type="submit" class="btn btn-primary">Confirm</button>

                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('modal')
@include('modals.delete_modal')

<div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div id="order-details-modal-body">

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div id="payment_modal_body">

            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
    $('#order_details').on('hidden.bs.modal', function() {
        location.reload();
    })



    function orderReceived(order_id) {
        if (confirm('Are you sure ? You received this order successfully !!') == true) {
            var status = 'received';
            $.post('{{ route('orders.update_delivery_status') }}', {
                    _token: '{{ @csrf_token() }}',
                    order_id: order_id,
                    status: status
                },
                function(data) {
                    $('#received_' + order_id).hide()
                    AIZ.plugins.notify('success', '{{ translate('
                        Order Received Successfully ') }}');
                });
        }
    }
</script>

@endsection