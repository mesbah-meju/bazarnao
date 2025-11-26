@extends('frontend.layouts.app')

@section('content')
    @php
        $status = $order->orderDetails->first()->delivery_status;
    @endphp
    <section class="pt-5 mb-1">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 mx-auto">
                    <div class="row aiz-steps arrow-divider">
                        <div class="col done">
                            <div class="text-center text-success">
                                <i class="la-3x mb-2 las la-shopping-cart"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block text-capitalize">{{ translate('1. My Cart')}}</h3>
                            </div>
                        </div>
                      
                        <div class="col done">
                            <div class="text-center text-success">
                                <i class="la-3x mb-2 las la-credit-card"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block text-capitalize">{{ translate('2. Shipping & Payment')}}</h3>
                            </div>
                        </div>
                        <div class="col active">
                            <div class="text-center text-primary">
                                <i class="la-3x mb-2 las la-check-circle"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block text-capitalize">{{ translate('3. Confirmation')}}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="py-1">
        <div class="container text-left">
            <div class="row">
                <div class="col-xl-8 mx-auto">
                    <div class="card shadow-sm border-0 rounded">
                        <div class="card-body">
                            <div class="text-center py-1 mb-4">
                            <img class="mb-2" style="width:25%" src="{{ static_asset('assets/img/happy.png') }}">
                            <br>
                                <i class="la la-check-circle la-3x text-success mb-3"></i>
                                <h1 class="h3 mb-3 fw-600">{{ translate('Thank You for Your Order!')}}</h1>
                                <h2 class="h5">{{ translate('Order Code:')}} <span class="fw-700 text-primary">{{ $order->code }}</span></h2>
                                <p class="opacity-70 font-italic">{{  translate('A copy or your order summary has been sent to') }} {{ json_decode($order->shipping_address)->email }}</p>
                            </div>
                            <div class="mb-4">
                                <h5 class="fw-600 mb-3 fs-17 pb-2">{{ translate('Order Summary')}}</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table">
                                            <tr>
                                                <td class="w-50 fw-600">{{ translate('Order Code')}}:</td>
                                                <td>{{ $order->code }}</td>
                                            </tr>
                                            <tr>
                                                <td class="w-50 fw-600">{{ translate('Name')}}:</td>
                                                <td>{{ json_decode($order->shipping_address)->name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="w-50 fw-600">{{ translate('Email')}}:</td>
                                                <td>{{ json_decode($order->shipping_address)->email }}</td>
                                            </tr>
                                            <tr>
                                                <td class="w-50 fw-600">{{ translate('Shipping address')}}:</td>
                                                <td>{{ json_decode($order->shipping_address)->address }}, {{ json_decode($order->shipping_address)->city }}, {{ json_decode($order->shipping_address)->country }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table">
                                            <tr>
                                                <td class="w-50 fw-600">{{ translate('Order date')}}:</td>
                                                <td>{{ date('d-m-Y H:i A', $order->date) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="w-50 fw-600">{{ translate('Order status')}}:</td>
                                                <td>{{ translate(ucfirst(str_replace('_', ' ', $status))) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="w-50 fw-600">{{ translate('Total order amount')}}:</td>
                                                <td>{{ single_price($order->orderDetails->sum('price') + $order->orderDetails->sum('tax')) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="w-50 fw-600">{{ translate('Shipping')}}:</td>
                                                <td>{{ translate('Flat shipping rate')}}</td>
                                            </tr>
                                            <tr>
                                                <td class="w-50 fw-600">{{ translate('Payment method')}}:</td>
                                                <td>{{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h5 class="fw-600 mb-3 fs-17 pb-2">{{ translate('Order Details')}}</h5>
                                <div>
                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th width="30%">{{ translate('Product')}}</th>
                                                <th class="text-center">{{ translate('Variation')}}</th>
                                                <th class="text-center">{{ translate('Quantity')}}</th>
                                                <th class="text-center">{{ translate('Delivery Type')}}</th>
                                                <th class="text-center">{{ translate('Pre Price')}}</th>
                                                <th class="text-center">{{ translate('Price')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($order->orderDetails as $key => $orderDetail)
                                                <tr>
                                                    <td>{{ $key+1 }}</td>
                                                    <td>
                                                        @if ($orderDetail->product != null)
                                                        <?php
                                                            $group_product_check = \App\Models\Product::where('id', $orderDetail->product_id)->value('is_group_product');
                                                        ?>
                                                            @if($group_product_check == 1)
                                                                <?php
                                                                    $group_product_items = \App\Models\Group_product::where('group_product_id', $orderDetail->product_id)->get();
                                                                ?>
                                                                <strong><a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank" class="text-muted">{{ $orderDetail->product->getTranslation('name') }}</a></strong><br>
                                                                @foreach($group_product_items as $item)
                                                                    <?php
                                                                        $product_name = \App\Models\Product::where('id', $item->product_id)->value('name');
                                                                    ?>
                                                                    <li>{{ $product_name }} Qty:({{$item->qty }})</li>
                                                                @endforeach
                                                            @else
                                                                <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank" class="text-reset">
                                                                    {{ $orderDetail->product->getTranslation('name') }}
                                                                </a>
                                                            @endif
                                                        @else
                                                            <strong>{{  translate('Product Unavailable') }}</strong>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $orderDetail->variation }}
                                                    </td>
                                                    <td>
                                                        {{ $orderDetail->quantity }}
                                                    </td>
                                                    <td>
                                                        @if ($orderDetail->shipping_type != null && $orderDetail->shipping_type == 'home_delivery')
                                                            {{  translate('Home Delivery') }}
                                                        @elseif ($orderDetail->shipping_type == 'pickup_point')
                                                            @if ($orderDetail->pickup_point != null)
                                                                {{ $orderDetail->pickup_point->getTranslation('name') }} ({{ translate('Pickip Point') }})
                                                            @endif
                                                        @endif
                                                    </td>
                                                    @if($orderDetail->discount)
                                                        <td class="text-right">
                                                            <del class="text-muted">{{ single_price($orderDetail->price + $orderDetail->discount) }}</del>
                                                        </td>
                                                    @else
                                                        <td class="text-right">
                                                            <span class="text-muted"></span>
                                                        </td>
                                                    @endif
                                                    <td class="text-right">{{ single_price($orderDetail->price) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-xl-5 col-md-6 ml-auto mr-0">
                                        <table class="table ">
                                            <tbody>
                                                <tr>
                                                    <th>{{ translate('Subtotal')}}</th>
                                                    <td class="text-right">
                                                        <span class="fw-600">{{ single_price($order->orderDetails->sum('price')) }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>{{ translate('Shipping')}}</th>
                                                    <td class="text-right">
                                                        <span class="font-italic">{{ single_price($order->orderDetails->sum('shipping_cost')) }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>{{ translate('Tax')}}</th>
                                                    <td class="text-right">
                                                        <span class="font-italic">{{ single_price($order->orderDetails->sum('tax')) }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>{{ translate('Coupon Discount')}}</th>
                                                    <td class="text-right">
                                                        <span class="font-italic">{{ single_price($order->coupon_discount) }}</span>
                                                    </td>
                                                </tr> 
                                                @php 
                                                $discount = \App\Models\Customer_ledger::where('type', 'Discount')->where('order_id',$order->id)->sum('credit');
                                                @endphp
                                                <tr>
                                                    <th>{{ translate('Special Discount')}}</th>
                                                    <td class="text-right">
                                                        <span class="font-italic">{{ single_price($order->special_discount) }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>{{ translate('Offer Discount')}}</th>
                                                    <?php
                                                        $total_offer_discount = $order->orderDetails->sum('discount');
                                                    ?>
                                                    <td class="text-right">
                                                        <span class="font-italic">{{ single_price($total_offer_discount) }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><strong class="text-right">{{ translate('Total')}}</strong></th>
                                                    <td class="text-right">
                                                        <strong><span>{{ single_price($order->grand_total) }}</span></strong>
                                                    </td>
                                                </tr>
												<tr>
        				<th>
        					<strong class="text-right">{{translate('Paid')}} :</strong>
        				</th>
        				<th class="text-right">
                @php 
                $paid = 0;
                    if(!empty($order->payment_details)){
                      $payment = json_decode($order->payment_details);
                      if(!empty($payment)){
                        $paid = $payment->amount;
                      }
                    }
                @endphp
        					{{ single_price($paid) }}
                  <input type="hidden" id="total_paid" value="{{$paid}}">
        				</th>
        			</tr>
              <tr>
        				<th>
        					<strong class="text-right">{{translate('Due')}} :</strong>
        				</th>
        				<th class="text-right">
                {{ single_price($order->grand_total-$paid) }}<input type="hidden" id="total_due" value="{{$order->grand_total-$paid}}">
        				</th>
        			</tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
