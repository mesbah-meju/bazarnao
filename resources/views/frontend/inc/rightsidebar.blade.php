<div class="rightsidenav">
    <div class="rightnavheader" style="height: 50px;background-color: #e4e0e1;">

        <div class="pt-3">
            <svg version="1.1" id="Calque_1" x="0px" y="0px" style="fill:#AE3C86;stroke:#AE3C86;margin-left:15px;" width="21px" height="24px" viewBox="0 0 100 160.13" data-reactid=".7ol3jbti1y.3.0.1.2.0.0">
                <g data-reactid=".7ol3jbti1y.3.0.1.2.0.0.0">
                    <polygon points="11.052,154.666 21.987,143.115 35.409,154.666  " data-reactid=".7ol3jbti1y.3.0.1.2.0.0.0.0"></polygon>
                    <path d="M83.055,36.599c-0.323-7.997-1.229-15.362-2.72-19.555c-2.273-6.396-5.49-7.737-7.789-7.737   c-6.796,0-13.674,11.599-16.489,25.689l-3.371-0.2l-0.19-0.012l-5.509,1.333c-0.058-9.911-1.01-17.577-2.849-22.747   c-2.273-6.394-5.49-7.737-7.788-7.737c-8.618,0-17.367,18.625-17.788,37.361l-13.79,3.336l0.18,1.731h-0.18v106.605l17.466-17.762   l18.592,17.762V48.06H9.886l42.845-10.764l2.862,0.171c-0.47,2.892-0.74,5.865-0.822,8.843l-8.954,1.75v106.605l48.777-10.655   V38.532l0.073-1.244L83.055,36.599z M36.35,8.124c2.709,0,4.453,3.307,5.441,6.081c1.779,5.01,2.69,12.589,2.711,22.513   l-23.429,5.667C21.663,23.304,30.499,8.124,36.35,8.124z M72.546,11.798c2.709,0,4.454,3.308,5.44,6.081   c1.396,3.926,2.252,10.927,2.571,18.572l-22.035-1.308C61.289,21.508,67.87,11.798,72.546,11.798z M58.062,37.612l22.581,1.34   c0.019,0.762,0.028,1.528,0.039,2.297l-23.404,4.571C57.375,42.986,57.637,40.234,58.062,37.612z M83.165,40.766   c-0.007-0.557-0.01-1.112-0.021-1.665l6.549,0.39L83.165,40.766z" data-reactid=".7ol3jbti1y.3.0.1.2.0.0.0.1"></path>
                </g>
            </svg>
            @if(Session::has('cart'))
            {{ count(Session::get('cart'))}} Item
            @else
            0 Item
            @endif

            <button onclick="closerightCart()" id="rightnavclose" style="border: 1px solid #666;position: absolute;right: 10px;">Close</button>
        </div>
    </div>

    @if(Session::has('cart'))
    @if(count($cart = Session::get('cart')) > 0)
    <!-- <div class="p-3 fs-15 fw-600 p-3 border-bottom">
                {{translate('Cart Items')}}
            </div> -->
            <ul class="h-350px overflow-auto c-scrollbar-light list-group list-group-flush">
    @php
    $total = 0;
    $total_discount = 0;
    $offer_discount = Session::get('offer_discount');
    @endphp

    @foreach($cart as $key => $cartItem)
    @php
    $product = App\Models\Product::find($cartItem['id']);
    $discount_price = 0;

    if ($product) {
        // Happy Hour Discount
        $happy_hour = \App\Models\HappyHour::with('happy_hour_products')
            ->where('status', 1)
            ->where('end_date', '>=', now())
            ->first();
        $happy_hour_product = $happy_hour ? $happy_hour->happy_hour_products->where('product_id', $product->id)->first() : null;

        if ($happy_hour_product) {
            if($happy_hour_product->discount_type =="percent"){
                $discount_price = ($product->unit_price * $happy_hour_product->discount) / 100;
            }else{
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
            $group_products = \App\Models\Group_product::where('group_product_id', $product->id)->get();
            $total_new_price = 0;
            $total_main_price = 0;

            foreach ($group_products as $item) {
                $main_price = App\Models\Product::where('id', $item->product_id)->value('unit_price');
                $total_main_price += $main_price * $item->qty;
                $total_new_price += $item->price;
            }

            $productwisediscount = $total_main_price - $total_new_price;
            $total_discount += $productwisediscount * $cartItem['quantity'];
        }

        // Update Total Price and Discounts
        $total += ($cartItem['price']) * $cartItem['quantity'];
        $total_discount += $discount_price * $cartItem['quantity'];
    }
    @endphp

    @if ($product != null)
    <li class="list-group-item">
        <span class="d-flex align-items-center">
            <div style="width: 10%;">
                <button onclick="updateQuantityPlus({{ $key }}, {{ $cartItem['quantity'] }},{{$cartItem['id']}})" class="btn btn-sm btn-icon " style="padding-left: 0px;">
                    <i class="la la-angle-up"></i>
                </button><br />
                <span id="rightqty_{{$cartItem['id']}}" style="padding-left: 8px;">{{ $cartItem['quantity'] }}</span><br />
                <button onclick="updateQuantityMinus({{ $key }}, {{ $cartItem['quantity'] }},{{$cartItem['id']}})" @php if($cartItem['quantity']==1) echo 'disabled ' @endphp onclick="minuscartqty({{ $key }})" class="btn btn-sm btn-icon" style="padding-left: 0px;">
                    <i class="la la-angle-down"></i>
                </button>
            </div>
            <a href="{{ route('product', $product->slug) }}" class="text-reset d-flex align-items-center flex-grow-1">
                <img
                    src="{{ static_asset('assets/img/placeholder.jpg') }}"
                    data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                    class="img-fit lazyload size-60px rounded"
                    alt="{{  $product->getTranslation('name')  }}">
                <span class="minw-0 pl-2 flex-grow-1">
                    <span class="fw-600 mb-1 text-truncate-2">
                        {{ $product->getTranslation('name')  }}
                    </span>
                    <span class="">{{ $cartItem['quantity'] }}x</span>
                    @if($discount_price)
                    <del class="text-muted">{{ single_price($cartItem['price'] + $discount_price) }}</del>
                    @endif
                    <span class="text-primary">{{ single_price($cartItem['price']) }}</span>
                </span>
            </a>
            <span class="">
                <button onclick="removeFromCart({{ $key }}, {{$cartItem['id']}})" class="btn btn-sm btn-icon stop-propagation">
                    <i class="la la-close"></i>
                </button>
            </span>
        </span>
    </li>
    @endif
    @endforeach
</ul>


    @php
    if(Session::has('offer_discount')){
    $total -= Session::get('offer_discount');
    }
    @endphp
    @if (Session::has('offer_discount'))
    <div class="px-3 py-2 fs-15 border-top d-flex justify-content-between">
        <span class="opacity-60">
            {{ translate('Offer Discount') }}
        </span>
        <span class="fw-600">
            {{ single_price($offer_discount) }}
        </span>
    </div>
    @endif

    @if ($total_discount > 0)
    <div class="px-3 py-2 fs-15 border-top d-flex justify-content-between">
        <span class="opacity-60">
            {{ translate('Product Discount') }}
        </span>
        <span class="fw-600">
            {{ single_price($total_discount) }}
        </span>
    </div>
    @endif

    <div class="px-3 py-2 fs-15 border-top d-flex justify-content-between">
        <span class="opacity-60">{{translate('Subtotal')}}</span>
        <span class="fw-600">{{ single_price($total) }}</span>
    </div>
    <div class="px-3 py-2 text-center border-top">
        <ul class="list-inline mb-0">
            <!-- <li class="list-inline-item">
                        <a href="{{ route('cart') }}" class="btn btn-soft-primary btn-sm">
                            {{translate('View cart')}}
                        </a>
                    </li> -->

            @if (Auth::check())
            <li class="list-inline-item">
                <a href="{{ route('checkout.shipping_info') }}" class="btn btn-primary btn-sm">
                    {{translate('Checkout')}}
                </a>
            </li>

            @else
            <li class="list-inline-item">
                <button class="btn btn-primary fw-600" onclick="showCheckoutModal()">Continue to Shipping</button>
            </li>
            @endif
        </ul>
    </div>
    @else
    <div class="text-center p-3">
        <i class="las la-frown la-3x opacity-60 mb-3"></i>
        <h3 class="h6 fw-700">{{translate('Your Cart is empty')}}</h3>
    </div>
    @endif
    @else
    <div class="text-center p-3">
        <i class="las la-frown la-3x opacity-60 mb-3"></i>
        <h3 class="h6 fw-700">{{translate('Your Cart is empty')}}</h3>
    </div>
    @endif

</div>

<div class="rightsidecart" id="list_menu_right" onclick="openrightCart()">
    <div class="rightcarttop">
        <svg version="1.1" id="Calque_1" x="0px" y="0px" style="fill:#AE3C86;stroke:#AE3C86;margin-top: 3px;margin-left: 22px;" width="16px" height="24px" viewBox="0 0 100 160.13" data-reactid=".7ol3jbti1y.3.0.1.2.0.0">
            <g data-reactid=".7ol3jbti1y.3.0.1.2.0.0.0">
                <polygon points="11.052,154.666 21.987,143.115 35.409,154.666  " data-reactid=".7ol3jbti1y.3.0.1.2.0.0.0.0"></polygon>
                <path d="M83.055,36.599c-0.323-7.997-1.229-15.362-2.72-19.555c-2.273-6.396-5.49-7.737-7.789-7.737   c-6.796,0-13.674,11.599-16.489,25.689l-3.371-0.2l-0.19-0.012l-5.509,1.333c-0.058-9.911-1.01-17.577-2.849-22.747   c-2.273-6.394-5.49-7.737-7.788-7.737c-8.618,0-17.367,18.625-17.788,37.361l-13.79,3.336l0.18,1.731h-0.18v106.605l17.466-17.762   l18.592,17.762V48.06H9.886l42.845-10.764l2.862,0.171c-0.47,2.892-0.74,5.865-0.822,8.843l-8.954,1.75v106.605l48.777-10.655   V38.532l0.073-1.244L83.055,36.599z M36.35,8.124c2.709,0,4.453,3.307,5.441,6.081c1.779,5.01,2.69,12.589,2.711,22.513   l-23.429,5.667C21.663,23.304,30.499,8.124,36.35,8.124z M72.546,11.798c2.709,0,4.454,3.308,5.44,6.081   c1.396,3.926,2.252,10.927,2.571,18.572l-22.035-1.308C61.289,21.508,67.87,11.798,72.546,11.798z M58.062,37.612l22.581,1.34   c0.019,0.762,0.028,1.528,0.039,2.297l-23.404,4.571C57.375,42.986,57.637,40.234,58.062,37.612z M83.165,40.766   c-0.007-0.557-0.01-1.112-0.021-1.665l6.549,0.39L83.165,40.766z" data-reactid=".7ol3jbti1y.3.0.1.2.0.0.0.1"></path>
            </g>
        </svg>
        @if(Session::has('cart'))
        <p style="color:#AE3C86;text-align:center;font-weight: bold;"><span id="sightsidecarttotal">{{ count(Session::get('cart'))}}</span> Item</p>
        @else
        <p style="color:#AE3C86;text-align:center;font-weight: bold;">0 Item</p>
        @endif
    </div>
    <div class="rightcartbottom">
        <p style="color:#AE3C86;text-align:center;font-weight: bold;">TK<?php if (!empty($total)) { echo $total; } else { echo 0; }?> </p>
    </div>
</div>