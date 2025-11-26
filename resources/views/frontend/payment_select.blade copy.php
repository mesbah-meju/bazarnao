@extends('frontend.layouts.app')
@section('content')
    <section class="pt-5 mb-4">
        <div class="container">
            <div class="row">
                <div class="col-xl-5 mx-auto">
                    <div class="row gutters-5 sm-gutters-10">
                        <div class="col active">
                            <div class="text-center border border-bottom-6px p-2 text-primary">
                                <i class="la-3x mb-2 las la-credit-card cart-animate" style="margin-right: -100px; transition: 2s;"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('1. Payment') }}</h3>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-center border border-bottom-6px p-2">
                                <i class="la-3x mb-2 opacity-50 las la-check-circle"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('2. Confirmation') }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-4">
        <div class="container text-left">
            <div class="row">
                <div class="col-lg-8">
                <form class="form-default" data-toggle="validator" action="{{ route('checkout.store_shipping_infostore') }}" role="form" method="POST">
                        @csrf
                        @if(Auth::check())
                            <div class="border bg-white p-4 mb-4">
                                @foreach (Auth::user()->addresses as $key => $address)
                                <div class="border mb-4">
                                        <div class="col-md-12">
                                            <label class="aiz-megabox d-block bg-white mb-0">
                                                <input type="radio" name="address_id" value="{{ $address->id }}" @if ($address->set_default)
                                                    checked
                                                @endif required>
                                                <span class="d-flex p-3 aiz-megabox-elem border-0">
                                                    <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                    <span class="flex-grow-1 pl-3 text-left">
                                                        <div class="row">
                                                            <span class="fs-14 text-secondary col-3">{{ translate('Address') }}</span>
                                                            <span class="fs-14 text-dark fw-500 ml-2 col">{{ $address->address }}</span>
                                                        </div>
                                                       
                                                        <div class="row">
                                                            <span class="fs-14 text-secondary col-3">{{ translate('Phone') }}</span>
                                                            <span class="fs-14 text-dark fw-500 ml-2 col">{{ $address->phone }}</span>
                                                        </div>
                                                    </span>
                                                </span>
                                            </label>
                                       
                                        <div class="col-md-4 p-3 text-right">
                                            <a class="btn btn-sm btn-warning text-white mr-4 rounded-0 px-4" onclick="edit_address('{{$address->id}}')">{{ translate('Edit Address') }}</a>
                                        </div>

                                        
                                    </div>
                                    
                                </div>
                                @endforeach
                                
                                <input type="hidden" name="checkout_type" value="logged">
                                <div class="mb-12" >
                                    <div class="border p-3 c-pointer text-center bg-light has-transition hov-bg-soft-light h-100 d-flex flex-column justify-content-center" onclick="add_new_address()">
                                        <i class="las la-plus la-2x mb-3"></i>
                                        <div class="alpha-7 fw-700">{{ translate('Add New Address') }}</div>
                                    </div>
                                </div>
                                <div class="row align-items-center">
                                    <div class="col-md-6 text-center text-md-left order-1 order-md-0">
                                        <a href="{{ route('home') }}" class="btn btn-link fs-14 fw-700 px-0">
                                            <i class="las la-arrow-left fs-16"></i>
                                            {{ translate('Return to shop')}}
                                        </a>
                                    </div>
                                </div>
                            </div>




                    <div class="shadow-sm bg-white rounded mb-4">
                        <div class="row gutters-5">
                            @foreach (Auth::user()->addresses as $key => $address)
                            <div class="col-md-6 mb-3">
                                <label class="aiz-megabox d-block bg-white mb-0">
                                    <input type="radio" name="address_id" value="{{ $address->id }}" @if ($address->set_default)
                                    checked
                                    @endif required>
                                    <span class="d-flex p-3 aiz-megabox-elem">
                                        <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                        <span class="flex-grow-1 pl-3 text-left">
                                            <div>
                                                <span class="opacity-60">{{ translate('Address') }}:</span>
                                                <span class="fw-600 ml-2">{{ $address->address }}</span>
                                            </div>
                                            <div>
                                                <span class="opacity-60">{{ translate('Postal Code') }}:</span>
                                                <span class="fw-600 ml-2">{{ $address->postal_code }}</span>
                                            </div>
                                            <div>
                                                <span class="opacity-60">{{ translate('City') }}:</span>
                                                <span class="fw-600 ml-2">{{ $address->city }}</span>
                                            </div>
                                            <div>
                                                <span class="opacity-60">{{ translate('Country') }}:</span>
                                                <span class="fw-600 ml-2">{{ $address->country }}</span>
                                            </div>
                                            <div>
                                                <span class="opacity-60">{{ translate('Phone') }}:</span>
                                                <span class="fw-600 ml-2">{{ $address->phone }}</span>
                                            </div>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            @endforeach
                            <input type="hidden" name="checkout_type" id="checkout_type" value="logged">
                            <div class="col-md-6 mx-auto mb-3">
                                <div class="border p-3 rounded mb-3 c-pointer text-center bg-white h-100 d-flex flex-column justify-content-center" onclick="add_new_address()">
                                    <i class="las la-plus la-2x mb-3"></i>
                                    <div class="alpha-7">{{ translate('Add New Address') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                        @endif
                    </form>
                    <form action="{{ route('payment.checkout') }}" class="form-default" role="form" method="POST"
                        id="checkout-form">
                        @csrf
                        <input type="hidden" name="owner_id" value="{{ $carts[0]['owner_id'] }}">
                        <div class="card rounded-0 border shadow-none">
                            <div class="card-header p-4 border-bottom-0">
                                <h3 class="fs-16 fw-700 text-dark mb-0">
                                    {{ translate('Any additional Instruction?') }}
                                </h3>
                            </div>
                            <div class="form-group px-4">
                                <input name="additional_info" rows="5" class="form-control rounded-0" placeholder="{{ translate('Type Instruction...') }}"></input>
                            </div>

                            <div class="card-header p-4 border-bottom-0">
                                <h3 class="fs-16 fw-700 text-dark mb-0">
                                    {{ translate('Select a payment option') }}
                                </h3>
                            </div>
                           
                            <div class="card-body text-center px-4 pt-0">
                                <div class="row gutters-10">
                                    @if (get_setting('sslcommerz_payment') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="sslcommerz" class="online_payment" type="radio"
                                                    name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/sslcommerz.png') }}"
                                                        class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                            class="d-block fw-600 fs-15">{{ translate('sslcommerz') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                    @if (get_setting('nagad') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="nagad" class="online_payment" type="radio"
                                                    name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/nagad.png') }}"
                                                        class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                            class="d-block fw-600 fs-15">{{ translate('Nagad') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                    @if (get_setting('bkash') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="bkash" class="online_payment" type="radio"
                                                    name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/bkash.png') }}"
                                                        class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                            class="d-block fw-600 fs-15">{{ translate('Bkash') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                    @if (get_setting('aamarpay') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="aamarpay" class="online_payment" type="radio"
                                                    name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/aamarpay.png') }}"
                                                        class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                            class="d-block fw-600 fs-15">{{ translate('Aamarpay') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                    @if (get_setting('cash_payment') == 1)
                                            <div class="col-6 col-xl-3 col-md-4">
                                                <label class="aiz-megabox d-block mb-3">
                                                    <input value="cash_on_delivery" class="online_payment"
                                                        type="radio" name="payment_option" checked>
                                                    <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                        <img src="{{ static_asset('assets/img/cards/cod.png') }}"
                                                            class="img-fit mb-2">
                                                        <span class="d-block text-center">
                                                            <span
                                                                class="d-block fw-600 fs-15">{{ translate('Cash on Delivery') }}</span>
                                                        </span>
                                                    </span>
                                                </label>
                                            </div>
                                    @endif
                                </div>
                            </div>

                            <div class="pt-3 px-4 fs-14">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" required id="agree_checkbox">
                                    <span class="aiz-square-check"></span>
                                    <span>{{ translate('I agree to the') }}</span>
                                </label>
                                <a href="{{ route('terms') }}" class="fw-700">{{ translate('terms and conditions') }}</a>,
                                <a href="{{ route('returnpolicy') }}" class="fw-700">{{ translate('return policy') }}</a> &
                                <a href="{{ route('privacypolicy') }}" class="fw-700">{{ translate('privacy policy') }}</a>
                            </div>

                            <div class="row align-items-center pt-3 px-4 mb-4">
                                <div class="col-6">
                                    <a href="{{ route('home') }}" class="btn btn-link fs-14 fw-700 px-0">
                                        <i class="las la-arrow-left fs-16"></i>
                                        {{ translate('Return to shop') }}
                                    </a>
                                </div>
                                <div class="col-6 text-right">
                                    <button type="button" onclick="submitOrder(this)"
                                        class="btn btn-primary fs-14 fw-700 rounded-0 px-4">{{ translate('Complete Order') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4 mt-lg-0 mt-4" id="cart_summary">
                    @include('frontend.partials.cart_summary')
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script type="text/javascript">
        var minimum_order_amount_check = {{ get_setting('minimum_order_amount_check') == 1 ? 1 : 0 }};
        var minimum_order_amount =
            {{ get_setting('minimum_order_amount_check') == 1 ? get_setting('minimum_order_amount') : 0 }};

        function use_wallet() {
            $('input[name=payment_option]').val('wallet');
            if ($('#agree_checkbox').is(":checked")) {
                ;
                if (minimum_order_amount_check && $('#sub_total').val() < minimum_order_amount) {
                    AIZ.plugins.notify('danger',
                        '{{ translate('You order amount is less then the minimum order amount') }}');
                } else {
                    $('#checkout-form').submit();
                }
            } else {
                AIZ.plugins.notify('danger', '{{ translate('You need to agree with our policies') }}');
            }
        }

        function submitOrder(el){
            $(el).prop('disabled', true);
            if ($('#agree_checkbox').is(":checked")){
                if (minimum_order_amount_check && $('#sub_total').val() < minimum_order_amount) {
                    AIZ.plugins.notify('danger',
                        '{{ translate('You order amount is less then the minimum order amount') }}');
                } else {
                        $('#checkout-form').submit();
                }
            } else {
                AIZ.plugins.notify('danger', '{{ translate('You need to agree with our policies') }}');
                $(el).prop('disabled', false);
            }
        }

      

        $(document).on("click", "#coupon-apply", function() {
            var data = new FormData($('#apply-coupon-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: "{{ route('checkout.apply_coupon_code') }}",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data, textStatus, jqXHR) {
                    AIZ.plugins.notify(data.response_message.response, data.response_message.message);
                    $("#cart_summary").html(data.html);
                }
            })
        });

        $(document).on("click", "#coupon-remove", function() {
            var data = new FormData($('#remove-coupon-form')[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: "{{ route('checkout.remove_coupon_code') }}",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data, textStatus, jqXHR) {
                    $("#cart_summary").html(data);
                }
            })
        })
    </script>
@endsection
