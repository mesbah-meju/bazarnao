@extends('backend.layouts.app')

@section('content')

<style>
    #product-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
    }

    .product-card {
        position: relative;
        overflow: hidden;
        height: 100px; /* Set a fixed height for the card */
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease-in-out;
    }

    .product-card:hover {
        transform: scale(1.05);
    }

    .card-content {
        padding: 15px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    .product-name {
        font-weight: 500;
        font-size: 1.2rem;
        margin-bottom: 10px;
    }

    .product-price {
        font-size: 1rem;
        color: #555;
        margin-bottom: 10px;
    }

    .add-plus {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.6);
        color: #fff;
        font-size: 1.5rem;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }

    .product-card:hover .add-plus {
        opacity: 1;
    }
</style>

<section class="">
    <form class="" action="" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row gutters-5">
            <div class="col-md" id="invoice-container">
                <div class="row gutters-5 mb-3">
                    <div class="col-md-3 col-6">
                        <select name="poscategory" class="form-control form-control-lg aiz-selectpicker" data-live-search="true" onchange="filterProducts()">
                            <option value="">{{ translate('All Categories') }}</option>
                            @foreach (\App\Models\Category::all() as $key => $category)
                            <option value="category-{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-6">
                        <select name="brand" class="form-control form-control-lg aiz-selectpicker" data-live-search="true" onchange="filterProducts()">
                            <option value="">{{ translate('All Brands') }}</option>
                            @foreach (\App\Models\Brand::all() as $key => $brand)
                            <option value="{{ $brand->id }}">{{ $brand->getTranslation('name') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="form-group mb-0">
                            <input class="form-control" type="text" id="name" name="name" placeholder="{{ translate('Product Name') }}" onkeydown="filterProducts()">
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="form-group mb-0">
                            <input class="form-control" type="text" id="barcode" name="keyword" placeholder="{{ translate('Barcode') }}">
                        </div>
                    </div>
                </div>
                <div class="aiz-pos-product-list c-scrollbar-light">
                    <div class="d-flex flex-wrap justify-content-center" id="product-list">
                    </div>
                    <div id="load-more" class="text-center">
                        <div class="fs-14 d-inline-block fw-600 btn btn-soft-primary c-pointer" onclick="loadMoreProduct()">{{ translate('Loading..') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-auto w-md-350px w-lg-400px w-xl-500px">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex border-bottom">
                            <div class="flex-grow-1">
                                <select name="user_id" class="form-control aiz-selectpicker pos-customer" data-live-search="true" onchange="getShippingAddress()">
                                    <option value="">Select Customer</option>
                                    @foreach ($customers as $key => $customer)
                                    <option value="{{ $customer->id }}" data-contact="{{ $customer->email }}">
                                        {{ $customer->phone }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" class="btn btn-icon btn-soft-dark ml-3 mr-0" data-target="#new-customer" data-toggle="modal">
                            <i class="las la-plus"></i>
                            </button>
                        </div>

                        <div class="" id="cart-details">
                            <div class="aiz-pos-cart-list mb-1 mt-1">
                                @php
                                $subtotal = 0;
                                $tax = 0;
                                @endphp

                                @if (Session::has('pos.cart'))
                                <ul class="list-group list-group-flush">
                                    @forelse (Session::get('pos.cart') as $key => $cartItem)
                                    @php
                                    $subtotal += $cartItem['price']*$cartItem['quantity'];
                                    $tax += $cartItem['tax']*$cartItem['quantity'];
                                    $product = \App\Models\Product::find($cartItem['id']);
                                    @endphp
                                    <li class="list-group-item py-0 pl-2">
                                        <div class="row  align-items-center">
                                            <div class="col-auto w-60px">
                                                <div class="row align-items-center flex-column aiz-plus-minus">
                                                    <button class="btn col-auto btn-icon btn-sm fs-15" type="button" data-type="plus" data-field="qty-{{ $key }}">
                                                        <i class="las la-plus"></i>
                                                    </button>

                                                    <input type="text"
                                                        name="qty-{{ $key }}"
                                                        id="qty-{{ $key }}"
                                                        class="col border-0 text-center flex-grow-1 fs-16 input-number"
                                                        placeholder="1"
                                                        value="{{ $cartItem['quantity'] }}"
                                                        min="{{ $product->min_qty }}"
                                                        max="{{ $product->max_qty }}"
                                                        onchange="updateQuantity({{ $key }})">

                                                    <button class="btn col-auto btn-icon btn-sm fs-15" type="button" data-type="minus" data-field="qty-{{ $key }}">
                                                        <i class="las la-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="text-truncate-2">{{ $product->name }}</div>

                                            </div>
                                            <div class="col-auto">
                                              
                                                <input style="width: 50px; font-weight: bold;text-align: center" class="fs-12 fw-250" id="price-{{$key}}" type="text" value="{{$cartItem['price']}}" onchange="updatePrice({{ $key }})"> x {{ $cartItem['quantity'] }}

                                                <div class="fs-15 fw-600" id="total_price-{{$key}}" >{{ single_price($cartItem['price']*$cartItem['quantity']) }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-circle btn-icon btn-sm btn-soft-danger ml-2 mr-0" onclick="removeFromCart({{ $key }})">
                                                    <i class="las la-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                    @empty
                                    <li class="list-group-item">
                                        <div class="text-center">
                                            <i class="las la-frown la-3x opacity-50"></i>
                                            <p>{{ translate('No Product Added') }}</p>
                                        </div>
                                    </li>
                                    @endforelse
                                </ul>
                                @else
                                <div class="text-center">
                                    <i class="las la-frown la-3x opacity-50"></i>
                                    <p>{{ translate('No Product Added') }}</p>
                                </div>
                                @endif
                            </div>
                            <div>
                               
                                <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                                    <span>{{translate('Shipping')}}</span>
                                    <span>{{ single_price(Session::get('pos.shipping', 0)) }}</span>
                                </div>
                                <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                                    <span>{{translate('Discount')}}</span>
                                    <span>{{ single_price(Session::get('pos.discount', 0)) }}</span>
                                </div>
                                <div class="d-flex justify-content-between fw-600 fs-18 border-top pt-2">
                                    <span>{{translate('Total')}}</span>
                                    <span>{{ single_price($subtotal+$tax+Session::get('pos.shipping', 0) - Session::get('pos.discount', 0)) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pos-footer mar-btm">
                    <div class="d-flex flex-column flex-md-row justify-content-between">
                        <div class="d-flex">
                            <div class="dropdown mr-3 ml-0 dropup">
                                <button class="btn btn-outline-dark btn-styled dropdown-toggle" type="button" data-toggle="dropdown">
                                    {{translate('Shipping')}}
                                </button>
                                <div class="dropdown-menu p-3 dropdown-menu-lg">
                                    <div class="input-group">
                                        <input type="number" min="0" placeholder="Amount" name="shipping" class="form-control" value="{{ Session::get('pos.shipping', 0) }}" required onchange="setShipping()">
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ translate('Flat') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown dropup">
                                <button class="btn btn-outline-dark btn-styled dropdown-toggle" type="button" data-toggle="dropdown">
                                    {{translate('Discount')}}
                                </button>
                                <div class="dropdown-menu p-3 dropdown-menu-lg">
                                    <div class="input-group">
                                        <input type="number" min="0" placeholder="Amount" name="discount" class="form-control" value="{{ Session::get('pos.discount', 0) }}" required onchange="setDiscount()">
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ translate('Flat') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="my-2 my-md-0">
                            <button type="button" class="btn btn-primary btn-block" onclick="orderConfirmation()">{{ translate('Place Order') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

@endsection

@section('modal')
<!-- Address Modal -->
<div id="new-customer" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom" role="document">
        <div class="modal-content">
            <div class="modal-header bord-btm">
                <h4 class="modal-title h6">{{translate('Add Customer Info')}}</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="shipping_form">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <label class="col-sm-2 control-label" for="name">{{translate('Name')}}</label>
                            <div class="col-sm-10">
                                <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label class="col-sm-2 control-label" for="address">{{translate('Address')}}</label>
                            <div class="col-sm-10">
                                <textarea placeholder="{{translate('Address')}}" id="address" name="address" class="form-control" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label class="col-sm-2 control-label" for="phone">{{translate('Phone')}}</label>
                            <div class="col-sm-10">
                                <input type="number" min="0" placeholder="{{translate('Phone')}}" id="phone" name="phone" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-styled btn-base-3" data-dismiss="modal" id="close-button">{{translate('Close')}}</button>
                    <button type="submit" class="btn btn-primary btn-styled btn-base-1" id="confirm-address">{{translate('Confirm')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- new address modal -->
<div id="new-address-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom" role="document">
        <div class="modal-content">
            <div class="modal-header bord-btm">
                <h4 class="modal-title h6">{{translate('Shipping Address')}}</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <form class="form-horizontal" action="{{ route('addresses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="customer_id" id="set_customer_id" value="">
                    <div class="form-group">
                        <div class="row">
                            <label class="col-sm-2 control-label" for="address">{{translate('Address')}}</label>
                            <div class="col-sm-10">
                                <textarea placeholder="{{translate('Address')}}" id="address" name="address" class="form-control" required></textarea>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="warehouse_id" name="warehouse_id" value="{{$wearhouses[0]->id}}" class="form-control" required>
                    <div class="form-group">
                        <div class="row">
                            <label class="col-sm-2 control-label" for="phone">{{translate('Phone')}}</label>
                            <div class="col-sm-10">
                                <input type="number" min="0" placeholder="{{translate('Phone')}}" id="phone" name="phone" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-styled btn-base-3" data-dismiss="modal">{{translate('Close')}}</button>
                    <button type="submit" class="btn btn-primary btn-styled btn-base-1">{{translate('Save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="order-confirm" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-xl">
        <div class="modal-content" id="variants">
            <div class="modal-header bord-btm">
                <h4 class="modal-title h6">{{translate('Order Summary')}}</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body" id="order-confirmation">
                <div class="p-4 text-center">
                    <i class="las la-spinner la-spin la-3x"></i>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-base-3" data-dismiss="modal">{{translate('Close')}}</button>
                <button type="button" onclick="submitOrder('cash_on_delivery')" class="btn btn-base-1 btn-success">{{translate('Confirm')}}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
        var products = null;
        $(document).ready(function(){
            $('body').addClass('side-menu-closed');
            $('#product-list').on('click','.add-plus:not(.c-not-allowed)',function(){
                var product_id = $(this).data('product_id');
                $.post('{{ route('pos.addToCart') }}',{_token:AIZ.data.csrf,product_id:product_id}, function(data){
                    if(data.success == 1){
                        updateCart(data.view);
                    }else{
                        AIZ.plugins.notify('danger', data.message);
                    }
                });
            });
            filterProducts();
            getShippingAddress();
        });
        
        window.onload = function () {
            var barcodeElement = document.getElementById('barcode');
            barcodeElement.focus();
        }
        
        $('#barcode').keypress(function (event){
            setTimeout(() =>{
                const barcode = $(this).val();
                if(barcode=='' || barcode==null){
                    return false;
                }
                $('#barcode').val('');
                $.post("{{ route('pos.addToCart') }}",{_token:AIZ.data.csrf, barcode:barcode}, function(data){
                    if(data.success == 1){
                        updateCart(data.view);
                    }else{
                        $('#barcode').val('');
                        AIZ.plugins.notify('danger', data.message);
                    }
                });
            }, 100);
            
        });

        function setProductList(data){
            for (var i = 0; i < data.data.length; i++) {
                $('#product-list').append(
                    `<div class="w-20px w-xl-180px w-xxl-210px">
                        <div class="card bg-white c-pointer product-card hov-container">
                            <div class="position-relative">
                            <span class="badge badge-inline badge-success fs-11 mb-1">${data.data[i].barcode}</span>
                            </div>
                            <div class="card" style="height: 60px;"> <!-- Set a fixed height here -->
                                <div class=" fw-500 fs-12" style="margin-left: 1rem;">${data.data[i].name}</div>
                                <div class="">
                                    ${
                                        `<del class="ml-0 fw-500 fs-12" style="margin-right: 1rem;"></del><span>${data.data[i].price}</span>`
                                    }
                                </div>
                            </div>
                            <div class="add-plus absolute-full rounded overflow-hidden hov-box" data-product_id="${data.data[i].id}">
                                <div class="absolute-full bg-dark opacity-50">
                                </div>
                            </div>
                            <div class="add-plus" data-product_id="${data.data[i].id}">
                                <div class="absolute-full bg-dark opacity-50"></div>
                                Add to Cart
                            </div>
                        </div>
                    </div>`
                );
            }
            if (data.links.next != null) {
                $('#load-more').find('.btn').html('{{ translate('Load More.') }}');
            }
            else {
                $('#load-more').find('.btn').html('{{ translate('Nothing more found.') }}');
            }
        }

        function filterProducts(){
            var keyword = $('input[name=name]').val();
            var category = $('select[name=poscategory]').val();
            var brand = $('select[name=brand]').val();
            var warehouse_id = $('select[name=warehouse_id]').val();
            $.get('{{ route('pos.search_product') }}',{keyword:keyword, category:category, brand:brand, warehouse_id:warehouse_id}, function(data){
                products = data;
                $('#product-list').html(null);
                setProductList(data);
            });
        }
        
        $("#confirm-address").click(function (){
            var data = new FormData($('#shipping_form')[0]);
            var phoneNumber = document.getElementById('phone').value;
            var phoneRegex = /^[0-9]{11}$/;

            if (phoneRegex.test(phoneNumber)) {

                if (phoneNumber.slice(0, 2) === "01") {
                    $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': AIZ.data.csrf
                    },
                    method: "POST",
                    url: "{{route('pos.set-shipping-address')}}",
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data, textStatus, jqXHR) {
                        if(data == 0){
                            AIZ.plugins.notify('danger', 'Customer Already Exist With This Number');
                            return false;
                        }
                    location.reload();
                    }
                })
                }else{
                    AIZ.plugins.notify('danger', 'Please Insert Valid Phpone Number');
                    return false;
                }  
            }else
            {
                AIZ.plugins.notify('danger', 'Please enter a valid 11-digit phone number');
                return false;
            }
        });

        function updateCart(data){
            $('#cart-details').html(data);
            AIZ.extra.plusMinus();
        }

        function loadMoreProduct(){
            if(products != null && products.links.next != null){
                $('#load-more').find('.btn').html('{{ translate('Loading..') }}');
                $.get(products.links.next,{}, function(data){
                    products = data;
                    setProductList(data);
                });
            }
        }  

        function removeFromCart(key){
            $.post('{{ route('pos.removeFromCart') }}', {_token:AIZ.data.csrf, key:key}, function(data){
                updateCart(data);
            });
        }

        function updateQuantity(key){
            $.post('{{ route('pos.updateQuantity') }}', {
                _token: AIZ.data.csrf,
                key: key,
                quantity: $('#qty-' + key).val()
            }, function(data){
                if(data.success == 1){
                    updateCart(data.view);
                } else {
                    AIZ.plugins.notify('danger', data.message);
                }
            });
        }

        $(document).on('click', '.aiz-plus-minus button', function() {
            var button = $(this);
            var inputId = button.data('field');
            var input = $('#' + inputId);
            var oldValue = parseFloat(input.val());
            var min = parseFloat(input.attr('min')) || 1;
            var max = parseFloat(input.attr('max')) || 99999;
            var newVal;

            if (button.data('type') === 'plus') {
                newVal = Math.min(oldValue + 1, max);
            } else {
                newVal = Math.max(oldValue - 1, min);
            }

            input.val(newVal); // Update value
            updateQuantity(inputId.split('-')[1]); // Get the numeric key from ID (qty-KEY)
        });

        function updatePrice(key){
            let price = $('#price-'+key).val();
            $.post('{{ route('pos.updatePrice') }}',{_token:AIZ.data.csrf, key:key, price:price }, function(data){
                if(data.success == 1){
                    updateCart(data.view);
                }else{
                    AIZ.plugins.notify('danger', data.message);
                }
            });
        }

        function setDiscount(){
            var discount = $('input[name=discount]').val();
            $.post('{{ route('pos.setDiscount') }}',{_token:AIZ.data.csrf, discount:discount}, function(data){
                updateCart(data);
            });
        }

        function setShipping(){
            var shipping = $('input[name=shipping]').val();
            $.post('{{ route('pos.setShipping') }}',{_token:AIZ.data.csrf, shipping:shipping}, function(data){
                updateCart(data);
            });
        }

        function getShippingAddress(){
            $.post('{{ route('pos.getShippingAddress') }}',{_token:AIZ.data.csrf, id:$('select[name=user_id]').val()}, function(data){
                $('#shipping_address').html(data);
            });
        }

        function add_new_address(){
            var customer_id = $('#customer_id').val();
            $('#set_customer_id').val(customer_id);
            $('#new-address-modal').modal('show');
            $("#close-button").click();
        }

        function orderConfirmation(){
            let user_id = $('select[name=user_id]').val();
            let shipping = @json(Session::get('pos.shipping_info'));
            if( (user_id == '') && (shipping == null)){
                AIZ.plugins.notify('danger', 'Please Select Or Create Customer');
                return false;
            }
            $('#order-confirmation').html(`<div class="p-4 text-center"><i class="las la-spinner la-spin la-3x"></i></div>`);
            $('#order-confirm').modal('show');
            $.post('{{ route('pos.getOrderSummary') }}',{_token:AIZ.data.csrf}, function(data){
                $('#order-confirmation').html(data);
            });
        }

        function calculateChange() {
            let totalAmount = parseFloat(document.getElementById('total_amount').value) || 0;
            let receivedAmount = parseFloat(document.getElementById('received_amount').value) || 0;
            let change = receivedAmount - totalAmount;
            document.getElementById('change').value = change.toFixed(2);
        }

        function submitOrder(payment_type) 
        {
            let user_id = $('select[name=user_id]').val();
            let shipping = $('input[name=shipping]:checked').val();
            let discount = $('input[name=discount]').val();
            let shipping_address = $('input[name=address_id]:checked').val();
            let total_amount = $('input[name=total_amount]').val();
            let receive_amount = $('input[name=received_amount]').val();
            let change_amount = $('input[name=change_amount]').val();

            if (receive_amount == '') 
            {
                AIZ.plugins.notify('danger', '{{translate("Please Enter Received Amount")}}');
                return false;
            } 
            else 
            {
                $('#order-confirm button[type="button"]').prop('disabled', true);
                $.post('{{ route('pos.order_place') }}', {
                    _token: AIZ.data.csrf,
                    user_id: user_id,
                    shipping_address: shipping_address,
                    payment_type: payment_type,
                    shipping: shipping,
                    discount: discount,
                    total_amount: total_amount,
                    receive_amount: receive_amount,
                    change_amount: change_amount,
                }, function(response) 
                {
                    let newWindow = window.open('', '_blank');
                    if (response.success == 1) {
                        $('#order-confirm').modal('hide');
                        location.reload();
                        if (response.redirect) {
                            newWindow.location.href = response.redirect;
                        }
                    } else {
                        AIZ.plugins.notify('danger', response.message);
                        location.reload();
                    }
                }).always(function() 
                {
                    $('#order-confirm button[type="button"]').prop('disabled', false);
                });
            }
        }

        //address
        $(document).on('change', '[name=country_id]', function() {
            var country_id = $(this).val();
            get_states(country_id);
        });

        $(document).on('change', '[name=state_id]', function() {
            var state_id = $(this).val();
            get_city(state_id);
        });
        
        function get_states(country_id) {
            $('[name="state"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('get-state')}}",
                type: 'POST',
                data: {
                    country_id  : country_id
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('[name="state_id"]').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }

        function get_city(state_id) {
            $('[name="city"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('get-city')}}",
                type: 'POST',
                data: {
                    state_id: state_id
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('[name="city_id"]').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }
    </script>
@endsection

