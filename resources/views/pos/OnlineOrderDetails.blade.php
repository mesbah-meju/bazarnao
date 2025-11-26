<div class="" id="OnlineOrder-details">
    <div class="aiz-pos-cart-list mb-4 mt-3 c-scrollbar-light">
        @php
        $subtotal = 0;
        $tax = 0;
        $shipping = 0;
        $discount = 0;
        @endphp

        @if (Session::has('online.orderDetails'))
        <ul class="list-group list-group-flush">
            @forelse (Session::get('online.orderDetails') as $key => $OnlineOrderDetail)
            @php
            $product = \App\Models\Product::where('id', $OnlineOrderDetail['product_id'])
                ->select('id', 'name', 'barcode', 'min_qty', 'is_group_product')
                ->first();
            $price = $OnlineOrderDetail['price'] / $OnlineOrderDetail['quantity'];
            @endphp

            <li class="list-group-item py-0 pl-2">
                <div class="row gutters-5 align-items-center">
                    @if ($product->is_group_product)
                        @php
                            $groupProducts = \App\Models\Group_product::where('group_product_id', $product->id)
                                ->with('product:id,name,barcode,min_qty')
                                ->get();
                            $total_price = 0;
                            foreach ($groupProducts as $groupProduct){
                                $total_price += ($groupProduct->price / $groupProduct->qty) * $groupProduct->qty * $OnlineOrderDetail['quantity'];
                            }
                        @endphp

                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title mb-0">{{ $product->name }}</h5>
                                        <p class="mb-0">Barcode: {{ $product->barcode }}</p>
                                    </div>
                                    <!-- <div class="col">
                                        <p class="mb-0">Qty</p>
                                        <p class="text-truncate-2">{{ $groupProduct->qty * $OnlineOrderDetail['quantity'] }}</p>
                                    </div> -->
                                    <div class="text-right">
                                        <p class="mb-0"><strong>Qty: {{ $OnlineOrderDetail['quantity'] }}</strong></p>
                                        <p class="mb-0">Total: <span class="fs-15 fw-600">{{ single_price($total_price) }}</span></p>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @foreach ($groupProducts as $groupProduct)
                                        <div class="row align-items-center mb-3">
                                            <div class="col">
                                                <p class="mb-0">Qty</p>
                                                <p class="text-truncate-2">{{ $groupProduct->qty * $OnlineOrderDetail['quantity'] }}</p>
                                            </div>
                                            <!-- <div class="col-auto">
                                                <label for="qty-{{ $key }}-{{ $groupProduct->id }}">Qty</label>
                                                <input type="number" name="qty-{{ $key }}-{{ $groupProduct->id }}" id="qty-{{ $key }}-{{ $groupProduct->id }}" class="form-control text-center" value="{{ $groupProduct->qty * $OnlineOrderDetail['quantity'] }}" max="{{ $groupProduct->product->max_qty }}">
                                            </div> -->
                                            <div class="col">
                                                <p class="mb-0">Name</p>
                                                <p class="text-truncate-2">{{ $groupProduct->product->name }}</p>
                                            </div>
                                            <div class="col">
                                                <p class="mb-0">Unit Price & Qty</p>
                                                <p class="fs-12 opacity-60">{{ single_price($groupProduct->price / $groupProduct->qty) }} x {{ $groupProduct->qty * $OnlineOrderDetail['quantity'] }}</p>
                                            </div>
                                            <div class="col">
                                                <p class="mb-0">Shipping</p>
                                                <p class="fs-12 opacity-60">{{ single_price($OnlineOrderDetail['shipping_cost']) }}</p>
                                            </div>
                                            <div class="col">
                                                <p class="mb-0">Discount</p>
                                                <p class="fs-12 opacity-60">{{ single_price($OnlineOrderDetail['discount']) }}</p>
                                            </div>
                                            <div class="col">
                                                <p class="mb-0">Total</p>
                                                <p class="fs-15 fw-600">{{ single_price(($groupProduct->price / $groupProduct->qty) * $groupProduct->qty * $OnlineOrderDetail['quantity']) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title mb-0">{{ $product->name }}</h5>
                                        <p class="mb-0">Barcode: {{ $product->barcode }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="mb-0"><strong>Qty: {{ $OnlineOrderDetail['quantity'] }}</strong></p>
                                        <p class="mb-0">Total: <span class="fs-15 fw-600">{{ single_price($OnlineOrderDetail['price'] * $OnlineOrderDetail['quantity']) }}</span></p>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row align-items-center mb-3">
                                        <div class="col">
                                            <span>Qty</span>
                                            <p class="text-truncate-2">{{$OnlineOrderDetail['quantity'] }}</p>
                                        </div>
                                        <div class="col">
                                            <span>Name</span>
                                            <div class="text-truncate-2">{{ $product->name }}</div>
                                        </div>
                                        <!-- <div class="col">
                                            <span>Barcode</span>
                                            <div class="text-truncate-2">{{ $product->barcode }}</div>
                                        </div> -->
                                        <div class="col">
                                            <span>Unit Price & Qty</span>
                                            <div class="fs-12 opacity-60">{{ single_price($OnlineOrderDetail['price']) }} x {{ $OnlineOrderDetail['quantity'] }}</div>
                                        </div>
                                        <div class="col">
                                            <span>Shipping</span>
                                            <div class="fs-12 opacity-60">{{ single_price($OnlineOrderDetail['shipping_cost']) }}</div>
                                        </div>
                                        <div class="col">
                                            <span>Discount</span>
                                            <div class="fs-12 opacity-60">{{ single_price($OnlineOrderDetail['discount']) }}</div>
                                        </div>
                                        <div class="col">
                                            <span>Total</span>
                                            <div class="fs-15 fw-600">{{ single_price($OnlineOrderDetail['price'] * $OnlineOrderDetail['quantity']) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
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
</div>
