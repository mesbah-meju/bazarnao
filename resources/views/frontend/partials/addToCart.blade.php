@php
$cart = Session::get('cart');
$cartIds = array();
$cartqty = array();
$keys = array();
if (is_array($cart) || is_object($cart))
{
    foreach($cart as $key => $cartItem){
        $cartIds[] = $cartItem['id'];
        $cartqty[$cartItem['id']] = $cartItem['quantity'];
        $keys[$cartItem['id']] = $key;
    }
}
@endphp
<div class="modal-body p-4 c-scrollbar-light">
    <div class="row">
        <div class="col-lg-6">
            <div class="row gutters-10 flex-row-reverse">
                @php
                    $photos = explode(',',$product->photos);
                @endphp
                <div class="col">
                    <div class="aiz-carousel product-gallery" data-nav-for='.product-gallery-thumb' data-fade='true'>
                        @foreach ($product->stocks as $key => $stock)
                            @if ($stock->image != null)
                                <div class="carousel-box img-zoom rounded">
                                    <img
                                        class="img-fluid lazyload"
                                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                        data-src="{{ uploaded_asset($stock->image) }}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                    >
                                </div>
                            @endif
                        @endforeach
                        @foreach ($photos as $key => $photo)
                        <div class="carousel-box img-zoom rounded">
                            <img
                                class="img-fluid lazyload"
                                src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                data-src="{{ uploaded_asset($photo) }}"
                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                            >
                        </div>
                        @endforeach

                    </div>
                </div>
                <div class="col-auto w-90px">
                    <div class="aiz-carousel carousel-thumb product-gallery-thumb" data-items='5' data-nav-for='.product-gallery' data-vertical='true' data-focus-select='true'>
                        @foreach ($product->stocks as $key => $stock)
                            @if ($stock->image != null)
                                <div class="carousel-box c-pointer border p-1 rounded" data-variation="{{ $stock->variant }}">
                                    <img
                                        class="lazyload mw-100 size-50px mx-auto"
                                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                        data-src="{{ uploaded_asset($stock->image) }}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                    >
                                </div>
                            @endif
                        @endforeach
                        @foreach ($photos as $key => $photo)
                        <div class="carousel-box c-pointer border p-1 rounded">
                            <img
                                class="lazyload mw-100 size-60px mx-auto"
                                src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                data-src="{{ uploaded_asset($photo) }}"
                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                            >
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="text-left">
                <h2 class="mb-2 fs-20 fw-600">
                    {{  $product->getTranslation('name')  }}
                </h2>

                @if(main_home_price($product->id) != main_home_discounted_price($product->id))
                    <div class="row no-gutters mt-3">
                        <div class="col-2">
                            <div class="opacity-50 mt-2">{{ translate('Price')}}:</div>
                        </div>
                        <div class="col-10">
                            <div class="fs-20 opacity-60">
                                <del>
                                    {{ main_home_price($product->id) }}
                                    @if($product->unit != null)
                                        <span>/{{ $product->getTranslation('unit') }}</span>
                                    @endif
                                </del>
                            </div>
                        </div>
                    </div>

                    <div class="row no-gutters mt-2">
                        <div class="col-2">
                            <div class="opacity-50">{{ translate('Discount Price')}}:</div>
                        </div>
                        <div class="col-10">
                            <div class="">
                                <strong class="h2 fw-600 text-primary">
                                    {{ main_home_discounted_price($product->id) }}
                                </strong>
                                @if($product->unit != null)
                                    <span class="opacity-70">/{{ $product->getTranslation('unit') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row no-gutters mt-3">
                        <div class="col-2">
                            <div class="opacity-50">{{ translate('Price')}}:</div>
                        </div>
                        <div class="col-10">
                            <div class="">
                                <strong class="h2 fw-600 text-primary">
                                    {{ main_home_discounted_price($product->id) }}
                                </strong>
                                <span class="opacity-70">/{{ $product->unit }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                @if (\App\Models\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Models\Addon::where('unique_identifier', 'club_point')->first()->activated && $product->earn_point > 0)
                    <div class="row no-gutters mt-4">
                        <div class="col-2">
                            <div class="opacity-50">{{  translate('Club Point') }}:</div>
                        </div>
                        <div class="col-10">
                            <div class="d-inline-block club-point bg-soft-base-1 border-light-base-1 border">
                                <span class="strong-700">{{ $product->earn_point }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <hr>

                @php
                    $qty = 0;
                    if($product->variant_product){
                        foreach ($product->stocks as $key => $stock) {
                            $qty += $stock->qty;
                        }
                    }
                    else{
                        $qty = $product->current_stock;
                    }
                @endphp

                <form id="option-choice-form">
                    @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <!-- Quantity + Add to cart -->
                    @if($product->digital !=1)
                        @if ($product->choice_options != null)
                            @foreach (json_decode($product->choice_options) as $key => $choice)
                                <div class="row no-gutters">
                                    <div class="col-2">
                                        <div class="opacity-50 mt-2 ">{{ \App\Models\Attribute::find($choice->attribute_id)->getTranslation('name') }}:</div>
                                    </div>
                                    <div class="col-10">
                                        <div class="aiz-radio-inline">
                                            @foreach ($choice->values as $key => $value)
                                            <label class="aiz-megabox pl-0 mr-2">
                                                <input
                                                    type="radio"
                                                    name="attribute_id_{{ $choice->attribute_id }}"
                                                    value="{{ $value }}"
                                                    @if($key == 0) checked @endif
                                                >
                                                <span class="aiz-megabox-elem rounded d-flex align-items-center justify-content-center py-2 px-3 mb-2">
                                                    {{ $value }}
                                                </span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        @if (count(json_decode($product->colors)) > 0)
                            <div class="row no-gutters">
                                <div class="col-2">
                                    <div class="opacity-50 mt-2">{{ translate('Color')}}:</div>
                                </div>
                                <div class="col-10">
                                    <div class="aiz-radio-inline">
                                        @foreach (json_decode($product->colors) as $key => $color)
                                        <label class="aiz-megabox pl-0 mr-2" data-toggle="tooltip" data-title="{{ \App\Models\Color::where('code', $color)->first()->name }}">
                                            <input type="radio" name="color" value="{{ \App\Models\Color::where('code', $color)->first()->name }}" @if($key == 0) checked @endif>
                                            <span class="aiz-megabox-elem rounded d-flex align-items-center justify-content-center p-1 mb-2">
                                                <span class="size-30px d-inline-block rounded" style="background: {{ $color }};"></span>
                                            </span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @endif

                        <div class="row no-gutters">
                            <div class="col-2">
                                <div class="opacity-50 mt-2">{{ translate('Quantity')}}:</div>
                            </div>
                            <div class="col-10">
                                @php
                                if(in_array($product->id,$cartIds)==true){
                                    $pkey = $keys[$product->id];
                                    $procartqty = $cartqty[$product->id];
                                    $showt = 'block';
                                    $showf = 'none';
                                } elseif(in_array($product->id,$cartIds)==false){
                                    $pkey = '';
                                    $procartqty = 0;
                                    $showf = 'block';
                                    $showt = 'none';
                                }
                                @endphp
                                <div class="product-quantity d-flex align-items-center">
                                    <div class="row no-gutters align-items-center aiz-plus-minus mr-3" style="width: 130px;">
                                        <button class="btn col-auto btn-icon btn-sm btn-circle btn-light" type="button" data-type="minus" data-field="m_quantity" data-key="{{ $pkey }}" data-id="{{ $product->id }}">
                                            <i class="las la-minus"></i>
                                        </button>
                                        <input id="product_{{ $product->id }}" type="number" name="m_quantity" class="col border-0 text-center flex-grow-1 fs-16 input-number" placeholder="1" value="{{ $procartqty }}" min="0" max="100" readonly>
                                        <button class="btn col-auto btn-icon btn-sm btn-circle btn-light" type="button" data-type="plus" data-field="m_quantity" data-key="{{ $pkey }}" data-id="{{ $product->id }}">
                                            <i class="las la-plus"></i>
                                        </button>
                                    </div>
                                    <!-- <div class="avialable-amount opacity-60">(<span id="available-quantity">{{ $qty }}</span> {{ translate('available')}})</div> -->
                                </div>
                            </div>
                        </div>

                        <hr>
                    @endif

                    <div class="row no-gutters pb-3 d-none" id="chosen_price_div">
                        <div class="col-2">
                            <div class="opacity-50">{{ translate('Total Price')}}:</div>
                        </div>
                        <div class="col-10">
                            <div class="product-price">
                                <strong id="chosen_price" class="h4 fw-600 text-primary">

                                </strong>
                            </div>
                        </div>
                    </div>

                </form>
                <div class="mt-3">
                    @if($product->outofstock==0)
                        <a type="button" class="btn btn-primary buy-now fw-600" href="{{ route('cart') }}">
                            <i class="la la-shopping-cart"></i> {{ translate('Buy Now')}}
                        </a>
                        <a type="button" class="btn btn-primary buy-now fw-600 add-to-cart" href="{{ route('checkout.shipping_info') }}">
                            <i class="las la-credit-card"></i>
                            <span class="d-none d-md-inline-block"> {{ translate('Checkout')}}</span>
                        </a>
                    @else
                        <button type="button" class="btn btn-secondary fw-600" disabled>
                            <i class="la la-cart-arrow-down"></i> {{ translate('Out of Stock')}}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('.aiz-plus-minus button').on('click', function(e) {
        e.preventDefault();

        var id = $(this).attr("data-id");
        var key = $(this).attr("data-key");
        var type = $(this).attr("data-type");
        var fieldName = $(this).attr("data-field");
        var input = $("input[name='" + fieldName + "']");
        var currentVal = parseInt(input.val());

        if (!isNaN(currentVal)) {
            if (type == "minus") {
                if (currentVal > input.attr("min")) {
                    if(currentVal == 1) {
                        $.post('{{ route("cart.removeFromCart") }}', {
                            _token: '{{ csrf_token() }}', 
                            key: key
                        },
                        function(data) {
                            $('#product_' + id).prev('button').attr('data-key', '');
                            $('#product_' + id).next('button').attr('data-key', '');

                            $('#productlist_' + id).text(currentVal - 1);
                            $('#productlist_' + id).parents('button').attr('onclick', 'updateQuantityAdd(' + key + ', ' + (currentVal - 1) + ',' + id + ')');
                            $('#productlist_' + id).parents('button').next('button').attr('onclick', 'updateQuantityPlus(' + key + ', ' + (currentVal - 1) + ',' + id + ')');
                            $('#productlist_' + id).parents('button').prev('button').attr('onclick', 'updateQuantityMinus(' + key + ', ' + (currentVal - 1) + ',' + id + ')');

                            $('#pro_cart_in_' + id).hide();
                            $('#pro_add_to_cart_' + id).show();

                            updateNavCart();
                            $('#cart-summary').html(data);
                            AIZ.plugins.notify('success', 'Item has been removed from cart');
                            $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html()) - 1);
                        });
                    } else {
                        
                        $.post('{{ route("cart.updateQuantity") }}', { 
                            _token: '{{ csrf_token() }}', 
                            key: key,
                            quantity: (currentVal - 1) },
                        function(data) {
                            $('#productlist_' + id).text(currentVal - 1);
                            $('#productlist_' + id).parents('button').attr('onclick', 'updateQuantityAdd(' + key + ', ' + (currentVal - 1) + ',' + id + ')');
                            $('#productlist_' + id).parents('button').next('button').attr('onclick', 'updateQuantityPlus(' + key + ', ' + (currentVal - 1) + ',' + id + ')');
                            $('#productlist_' + id).parents('button').prev('button').attr('onclick', 'updateQuantityMinus(' + key + ', ' + (currentVal - 1) + ',' + id + ')');

                            updateNavCart();
                        });
                    }
                    input.val(currentVal - 1).change();
                }
                if (parseInt(input.val()) == input.attr("min")) {
                    $(this).attr("disabled", true);
                }
            } else if (type == "plus") {
                if (currentVal < input.attr("max")) {
                    input.val(currentVal + 1).change();
                    if(currentVal == 0) {
                        $('.c-preloader').show();
                        
                        $.post('{{ route("cart.addToCart") }}', {
                            _token: '{{ csrf_token() }}',
                            id: id,
                            quantity: (currentVal + 1)
                        },
                        function(data) {
                            if (data.status == 1) {
                                AIZ.plugins.notify('success', "{{ translate('Item has been added to cart list') }}");
                                var new_key = Number($('#sightsidecarttotal').text());

                                $('#product_' + id).prev('button').attr('data-key', new_key);
                                $('#product_' + id).next('button').attr('data-key', new_key);

                                $('#productlist_' + id).text(currentVal + 1);
                                $('#productlist_' + id).parents('button').attr('onclick', 'updateQuantityAdd(' + key + ', ' + (currentVal + 1) + ',' + id + ')');
                                $('#productlist_' + id).parents('button').prev('button').attr('onclick', 'updateQuantityMinus(' + key + ', ' + (currentVal + 1) + ',' + id + ')');
                                $('#productlist_' + id).parents('button').next('button').attr('onclick', 'updateQuantityPlus(' + key + ', ' + (currentVal + 1) + ',' + id + ')');

                                $('#pro_cart_in_' + id).show();
                                $('#pro_add_to_cart_' + id).hide();

                                updateNavCart();
                            } else {
                                AIZ.plugins.notify('warning', data.msg);
                            }
                            $('.c-preloader').hide();
                        });
                    } else {
                        // updateQuantityPlus(key, currentVal, id);
                        $.post('{{ route("cart.updateQuantity") }}', {
                            _token: '{{ csrf_token() }}',
                            key: key,
                            quantity: (currentVal + 1)
                        },
                        function(data) {
                            if (data == 0) {
                                $('#productlist_' + id).text(currentVal + 1);
                                $('#productlist_' + id).parents('button').attr('onclick', 'updateQuantityAdd(' + key + ', ' + (currentVal + 1) + ',' + id + ')');
                                $('#productlist_' + id).parents('button').next('button').attr('onclick', 'updateQuantityPlus(' + key + ', ' + (currentVal + 1) + ',' + id + ')');
                                $('#productlist_' + id).parents('button').prev('button').attr('onclick', 'updateQuantityMinus(' + key + ', ' + (currentVal + 1) + ',' + id + ')');

                                updateNavCart();
                            } else {
                                AIZ.plugins.notify('warning', data);
                            }
                        });
                    }
                }
                if (parseInt(input.val()) == input.attr("max")) {
                    $(this).attr("disabled", true);
                }
            }
        } else {
            input.val(0);
        }
    });

    $('.aiz-plus-minus input').on('change', function () {
        var minValue = parseInt($(this).attr("min"));
        var maxValue = parseInt($(this).attr("max"));
        var valueCurrent = parseInt($(this).val());

        name = $(this).attr("name");
        if (valueCurrent >= minValue) {
            $(this).siblings("[data-type='minus']").removeAttr("disabled");
        } else {
            alert("Sorry, the minimum value was reached");
            $(this).val($(this).data("oldValue"));
        }
        if (valueCurrent <= maxValue) {
            $(this).siblings("[data-type='plus']").removeAttr("disabled");
        } else {
            alert("Sorry, the maximum value was reached");
            $(this).val($(this).data("oldValue"));
        }
    });
    // $('#option-choice-form input').on('change', function() {
    //     getVariantPrice();
    // });
</script>
