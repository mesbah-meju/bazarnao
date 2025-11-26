<div class="aiz-pos-cart-list mb-4 mt-3 c-scrollbar-light">
@php
$subtotal = 0;
$tax = 0;
@endphp
    @if (Session::has('online.orderConfirm'))
    <ul class="list-group list-group-flush">
        @forelse (Session::get('online.orderConfirm') as $key => $onlineOrderConf)
        @php
        $subtotal += $onlineOrderConf['price']*$onlineOrderConf['quantity'];
        $tax += $onlineOrderConf['tax']*$onlineOrderConf['quantity'];
        $product = \App\Models\Product::find($onlineOrderConf['product_id']);
        @endphp
        <li class="list-group-item py-0 pl-2">
            <div class="row gutters-5 align-items-center">
                <div class="col-auto w-60px">
                    <div class="row no-gutters align-items-center flex-column aiz-plus-minus">
                        
                        <input type="text" name="qty-{{ $key }}" id="qty-{{ $key }}" class="col border-0 text-center flex-grow-1 fs-16 input-number" placeholder="1" value="{{ $onlineOrderConf['quantity'] }}" min="{{ $product->min_qty }}" max="{{ $product->max_qty }}" onchange="updateQuantity({{ $key }})">
                        
                    </div>
                </div>
                <div class="col">
                    <div class="text-truncate-2">{{ $product->name }}</div>
                </div>

                <div class="col-auto">
                    <div class="fs-12 opacity-60">{{ single_price($onlineOrderConf['price']) }} x {{ $onlineOrderConf['quantity'] }}</div>
                    <div class="fs-15 fw-600">{{ single_price($onlineOrderConf['price']*$onlineOrderConf['quantity']) }}</div>
                </div>

            </div>
        </li>
        @empty
        <li class="list-group-item">
            <div class="text-center">
                <i class="las la-frown la-3x opacity-50"></i>
                <p>{{ translate('No Order Added') }}</p>
            </div>
        </li>
        @endforelse
    </ul>
    @else
    <div class="text-center">
        <i class="las la-frown la-3x opacity-50"></i>
        <p>{{ translate('No Order Added') }}</p>
    </div>
    @endif
</div>

<div>
    <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
        <span>{{translate('Sub Total')}}</span>
        <span>{{ single_price($subtotal) }}</span>
    </div>
    <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
        <span>{{translate('Tax')}}</span>
        <span>{{ single_price($tax) }}</span>
    </div>
    <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
        <span>{{translate('Shipping')}}</span>
        <span>{{ single_price() }}</span>
    </div>
    <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
        <span>{{translate('Discount')}}</span>
        <span>{{ single_price() }}</span>
    </div>
    <div class="d-flex justify-content-between fw-600 fs-18 border-top pt-2">
        <span>{{translate('Total')}}</span>
        <span>{{ single_price($subtotal+$tax) }}</span>
    </div>
</div>