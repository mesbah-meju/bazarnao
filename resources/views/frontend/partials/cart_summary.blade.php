<div class="card border-0 shadow-sm rounded">
    <div class="card-header">
        <h3 class="fs-16 fw-600 mb-0">{{translate('Summary')}}</h3>
        <div class="text-right">
            <span class="badge badge-inline badge-primary">{{ count(Session::get('cart')->where('owner_id', Session::get('owner_id'))) }} {{translate('Items')}}</span>
        </div>
    </div>

    <div class="card-body">
        @if (\App\Models\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Models\Addon::where('unique_identifier', 'club_point')->first()->activated)
            @php
                $total_point = 0;
            @endphp
            @foreach (Session::get('cart')->where('owner_id', Session::get('owner_id')) as $key => $cartItem)
                @php
                    $product = \App\Models\Product::find($cartItem['id']);
                    $total_point += $product->earn_point*$cartItem['quantity'];
                @endphp
            @endforeach
            <div class="rounded px-2 mb-2 bg-soft-primary border-soft-primary border">
                {{ translate("Total Club point") }}:
                <span class="fw-700 float-right">{{ $total_point }}</span>
            </div>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th class="product-name">{{translate('Product')}}</th>
                    <th class="product-name">{{translate('Pre Price')}}</th>
                    <th class="product-total text-right">{{translate('Totals')}}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $subtotal = 0;
                    $tax = 0;
                    $shipping = 0;
                    $total_discount = 0;
                    $offer_discount = 0;

           // dd(Session::get('cart')->where('owner_id', Session::get('owner_id')));
                @endphp
                @foreach (Session::get('cart') as $key => $cartItem)
                @php
                    $product = \App\Models\Product::find($cartItem['id']);
                    $discount_price = 0;
                    $productwisediscount = 0;
                    $total_main_price = 0;
                    $total_new_price = 0;
                    $inFlashDeal = false;
                    $inHappyHour = false;

                    if ($product) {
                        // Check for Happy Hour
                        $happy_hour = \App\Models\HappyHour::with('happy_hour_products')
                            ->where('status', 1)
                            ->where('end_date', '>=', now())
                            ->first();
                        if ($happy_hour) {
                            $happy_hour_product = $happy_hour->happy_hour_products
                                ->where('product_id', $product->id)
                                ->first();
                            if ($happy_hour_product) {
                                if($happy_hour_product->discount_type =="percent"){
                                    $discount_price = ($product->unit_price * $happy_hour_product->discount) / 100 * $cartItem['quantity'];
                                }else{
                                    $discount_price = $happy_hour_product->discount * $cartItem['quantity'];
                                }
                                $inHappyHour = true;
                            }
                        }

                        // Check for Flash Deal
                        if (!$inHappyHour) {
                            $flash_deals = \App\Models\FlashDeal::where('status', 1)->get();
                            $todaytime = strtotime(date('H:i:s'));

                            foreach ($flash_deals as $flash_deal) {
                                $flashstart = strtotime(date('H:i:s', $flash_deal->start_date));
                                $flashend = strtotime(date('H:i:s', $flash_deal->end_date));
                                if ($flashstart <= $todaytime && $flashend >= $todaytime) {
                                    $flash_deal_product = \App\Models\FlashDealProduct::where('flash_deal_id', $flash_deal->id)
                                        ->where('product_id', $product->id)
                                        ->first();
                                    if ($flash_deal_product) {
                                        $discount_price = ($product->unit_price * $flash_deal_product->discount_percent) / 100 * $cartItem['quantity'];
                                        $inFlashDeal = true;
                                        break;
                                    }
                                }
                            }
                        }

                        // Group Product Discount
                        if (!$inHappyHour && !$inFlashDeal && $product->is_group_product) {
                            $group_products = \App\Models\Group_product::where('group_product_id', $product->id)->get();
                            foreach ($group_products as $item) {
                                $main_price = \App\Models\Product::where('id', $item->product_id)->value('unit_price');
                                $discounted_price = $item->price;
                                $total_main_price += $main_price * $item->qty;
                                $total_new_price += $discounted_price;
                            }
                            $productwisediscount = $total_main_price - $total_new_price;
                            $discount_price = $productwisediscount;
                            $total_discount += $productwisediscount * $cartItem['quantity'];
                        }

                        // Regular Discount
                        if (!$inHappyHour && !$inFlashDeal && !$product->is_group_product) {
                            if ($product->discount_type == 'percent') {
                                $discount_price = ($product->unit_price * $product->discount) / 100 * $cartItem['quantity'];
                            } elseif ($product->discount_type == 'amount') {
                                $discount_price = $product->discount * $cartItem['quantity'];
                            }
                        }
                    }

                    $offer_discount += $discount_price;


                    // Calculate subtotal, tax, and shipping
                    $subtotal += $cartItem['price'] * $cartItem['quantity'];
                    $tax += $cartItem['tax'] * $cartItem['quantity'];
                    $shipping += $cartItem['shipping'];

                    // Handle product name with variant
                    $product_name_with_choice = $product ? $product->getTranslation('name') : '';
                    if ($cartItem['variant'] != null) {
                        $product_name_with_choice .= ' - ' . $cartItem['variant'];
                    }
                @endphp

    <tr class="cart_item">
        <td class="product-name">
            @if ($product && $product->is_group_product)
                <strong>
                    <a href="{{ route('product', $product->slug) }}" target="_blank" class="text-muted">
                        {{ $product_name_with_choice }}
                    </a>
                </strong>
                <strong class="product-quantity">× {{ $cartItem['quantity'] }}</strong><br>
                @foreach ($group_products as $item)
                    <li>{{ $item->product->name }} Qty: ({{ $item->qty }})</li>
                @endforeach
            @else
                {{ $product_name_with_choice }}
                <strong class="product-quantity">× {{ $cartItem['quantity'] }}</strong>
            @endif
        </td>
        @if ($discount_price > 0)
            <td class="text-right">
                <del class="text-muted">{{ single_price($cartItem['price'] + $discount_price) }}</del>
            </td>
        @else
            <td class="text-right">
                <del class="text-muted"></del>
            </td>
        @endif
        <td class="product-total text-right">
            <span class="pl-4">{{ single_price($cartItem['price'] * $cartItem['quantity']) }}</span>
        </td>
    </tr>
@endforeach

            @php
            $shipping_skip_total = \App\Models\BusinessSetting::where('type', 'flat_rate_shipping_cost_total')->first()->value;
    if($shipping_skip_total > $subtotal){
        $shipping = \App\Models\BusinessSetting::where('type', 'flat_rate_shipping_cost')->first()->value;
    }else{
            $shipping = 0;
            }
           // echo $shipping;exit;
            @endphp
            </tbody>
        </table>

        <table class="table">

            <tfoot>
                <tr class="cart-subtotal">
                    <th>{{translate('Subtotal')}}</th>
                    <td class="text-right">
                        <span class="fw-600">{{ single_price($subtotal) }}</span>
                    </td>
                </tr>

                <tr class="cart-tax">
                    <th>{{translate('Tax')}}</th>
                    <td class="text-right">
                        <span class="font-italic">{{ single_price($tax) }}</span>
                    </td>
                </tr>

                <tr class="cart-shipping">
                    <th>{{translate('Total Shipping')}}</th>
                    <td class="text-right">
                        <span class="font-italic">{{ single_price($shipping) }}</span>
                    </td>
                </tr>

                @if (Session::has('coupon_discount'))
                    <tr class="cart-discount">
                        <th>{{translate('Coupon Discount')}}</th>
                        <td class="text-right">
                            <span class="font-italic">{{ single_price(Session::get('coupon_discount')) }}</span>
                        </td>
                    </tr>
                @endif
                
                @if (Session::has('offer_discount'))
                    <tr class="cart-discount">
                        <th>{{translate('Offer Discount')}}</th>
                        <td class="text-right">
                            <span class="font-italic">{{ single_price($offer_discount) }}</span>
                        </td>
                    </tr>
                @endif

                @if ($total_discount > 0)
                    <tr class="cart-discount">
                        <th>
                            {{ translate('Bundle Product Discount') }}
                        </th>
                        <td class="text-right">
                            <span class="font-italic">{{ single_price($total_discount) }}</span>
                        </td>
                    </tr>
                @endif

                @php
                    $total = $subtotal+$tax+$shipping;
                    if(Session::has('coupon_discount')){
                        $total -= Session::get('coupon_discount');
                    }
                @endphp
                @php
                    if(Session::has('offer_discount')){
                        $total -= Session::get('offer_discount');
                    }
                @endphp

                <tr class="cart-total">
                    <th><span class="strong-600">{{translate('Total')}}</span></th>
                    <td class="text-right">
                        <strong><span>{{ single_price($total) }}</span></strong>
                    </td>
                </tr>
            </tfoot>
        </table>

        @if (Auth::check() && \App\Models\BusinessSetting::where('type', 'coupon_system')->first()->value == 1)
            @if (Session::has('coupon_discount'))
                <div class="mt-3">
                    <form class="" action="{{ route('checkout.remove_coupon_code') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group">
                            <div class="form-control">@if(Session::has('coupon_id')) {{  \App\Models\Coupon::find(Session::get('coupon_id'))->code }} @endif </div>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">{{translate('Change Coupon')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
            @else
                <div class="mt-3">
                    <form class="" action="{{ route('checkout.apply_coupon_code') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group">
                            <input type="text" class="form-control" name="code" placeholder="{{translate('Have coupon code? Enter here')}}" required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">{{translate('Apply')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        @endif

    </div>
</div>
