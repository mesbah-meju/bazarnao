@extends('frontend.layouts.app')

@section('content')
    <section class="pt-5 mb-4">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 mx-auto">
                    <div class="row aiz-steps arrow-divider">
                        <div class="col done">
                            <div class="text-center text-success">
                                <i class="la-3x mb-2 las la-shopping-cart"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block text-capitalize">{{ translate('1. My Cart') }}
                                </h3>
                            </div>
                        </div>

                        <div class="col active">
                            <div class="text-center text-primary">
                                <i class="la-3x mb-2 las la-credit-card"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block text-capitalize">
                                    {{ translate('2. Shipping & Payment') }}</h3>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-center">
                                <i class="la-3x mb-2 opacity-50 las la-check-circle"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50 text-capitalize">
                                    {{ translate('3. Confirmation') }}</h3>
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
                    <form action="{{ route('payment.checkout') }}" class="form-default" role="form" method="POST"
                        id="checkout-form">
                        @csrf

                        @if (Auth::check())
                            <div class="shadow-sm bg-white p-4 rounded mb-4">
                                <div class="row gutters-5">
                                    @foreach (Auth::user()->addresses as $key => $address)
                                        <div class="col-md-6 mb-3">
                                            <label class="aiz-megabox d-block bg-white mb-0">
                                                <input type="radio" name="address_id" value="{{ $address->id }}"
                                                    @if ($address->set_default) checked @endif required>
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
                                        <div class="border p-3 rounded mb-3 c-pointer text-center bg-white h-100 d-flex flex-column justify-content-center"
                                            onclick="add_new_address()">
                                            <i class="las la-plus la-2x mb-3"></i>
                                            <div class="alpha-7">{{ translate('Add New Address') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="shadow-sm bg-white p-4 rounded mb-4">
                                <div class="form-group">
                                    <label class="control-label">{{ translate('Name') }} <span
                                            style="color:red">*</span></label>
                                    <input type="text" class="form-control" name="name"
                                        placeholder="{{ translate('Name') }}" id="guest_name" required>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">{{ translate('Email') }}</label>
                                    <input type="text" class="form-control" name="email"
                                        placeholder="{{ translate('Email') }}" id="guest_email">
                                </div>

                                <div class="form-group">
                                    <label class="control-label">{{ translate('Address') }} <span
                                            style="color:red">*</span></label>
                                    <input type="text" class="form-control" name="address"
                                        placeholder="{{ translate('Address') }}" id="guest_address" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{ translate('Select your country') }}</label>
                                            <select class="form-control aiz-selectpicker" data-live-search="true"
                                                name="country">
                                                @foreach (\App\Models\Country::where('status', 1)->get() as $key => $country)
                                                    <option value="{{ $country->name }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">
                                            @if (\App\Models\BusinessSetting::where('type', 'shipping_type')->first()->value == 'area_wise_shipping')
                                                <label class="control-label">{{ translate('City') }}</label>
                                                <select class="form-control aiz-selectpicker" data-live-search="true"
                                                    name="city" required>
                                                    @foreach (\App\Models\City::get() as $key => $city)
                                                        <option value="{{ $city->name }}">
                                                            {{ $city->getTranslation('name') }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <label class="control-label">{{ translate('City') }}</label>
                                                <input type="text" class="form-control"
                                                    placeholder="{{ translate('City') }}" name="city" required>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- <div class="col-md-6">
                                                                                            <div class="form-group has-feedback">
                                                                                                <label class="control-label">{{ translate('Postal code') }}</label>
                                                                                                <input type="text" class="form-control" placeholder="{{ translate('Postal code') }}" name="postal_code" required>
                                                                                            </div>
                                                                                        </div> -->
                                    <div class="col-md-6">
                                        <div class="form-group has-feedback">
                                            <label class="control-label">{{ translate('Phone') }} <span
                                                    style="color:red">*</span></label>
                                            <input type="number" lang="en" min="0" class="form-control"
                                                placeholder="{{ translate('Phone') }}" name="phone" id="guest_phone"
                                                required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{ translate('Select your Area') }}</label>
                                            <select class="form-control aiz-selectpicker" data-live-search="true"
                                                name="area">
                                                @foreach (\App\Models\Area::get() as $key => $country)
                                                    <option value="{{ $country->name }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                </div>

                                <input type="hidden" name="checkout_type" id="checkout_type" value="guest">
                            </div>
                        @endif
                        <div class="shadow-sm bg-white p-4 rounded mb-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ translate('Write Order Instruction') }}</label>
                                        <input type="text" class="form-control"
                                            placeholder="{{ translate('Order Instruction') }}" name="note"
                                            id="note">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card shadow-sm border-0 rounded">
                            <div class="card-header p-3">
                                <h3 class="fs-16 fw-600 mb-0">
                                    {{ translate('Select a payment option') }}
                                </h3>
                            </div>
                            <div class="card-body text-center">
                                <div class="row">
                                    <div class="col-xxl-8 col-xl-10 mx-auto">
                                        <div class="row gutters-10">
                                            @if (\App\Models\BusinessSetting::where('type', 'sslcommerz_payment')->first()->value == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="sslcommerz" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block p-3 aiz-megabox-elem">
                                                            <img src="{{ static_asset('assets/img/cards/sslcommerz.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('sslcommerz') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (\App\Models\BusinessSetting::where('type', 'nagad')->first()->value == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="nagad" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block p-3 aiz-megabox-elem">
                                                            <img src="{{ static_asset('assets/img/cards/nagad.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('Nagad') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (\App\Models\BusinessSetting::where('type', 'bkash')->first()->value == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="bkash" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block p-3 aiz-megabox-elem">
                                                            <img src="{{ static_asset('assets/img/cards/bkash.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('Bkash') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (
                                                \App\Models\Addon::where('unique_identifier', 'african_pg')->first() != null &&
                                                    \App\Models\Addon::where('unique_identifier', 'african_pg')->first()->activated)
                                                @if (\App\Models\BusinessSetting::where('type', 'mpesa')->first()->value == 1)
                                                    <div class="col-6 col-md-4">
                                                        <label class="aiz-megabox d-block mb-3">
                                                            <input value="mpesa" class="online_payment" type="radio"
                                                                name="payment_option" checked>
                                                            <span class="d-block p-3 aiz-megabox-elem">
                                                                <img src="{{ static_asset('assets/img/cards/mpesa.png') }}"
                                                                    class="img-fluid mb-2">
                                                                <span class="d-block text-center">
                                                                    <span
                                                                        class="d-block fw-600 fs-15">{{ translate('mpesa') }}</span>
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                                @if (\App\Models\BusinessSetting::where('type', 'flutterwave')->first()->value == 1)
                                                    <div class="col-6 col-md-4">
                                                        <label class="aiz-megabox d-block mb-3">
                                                            <input value="flutterwave" class="online_payment"
                                                                type="radio" name="payment_option" checked>
                                                            <span class="d-block p-3 aiz-megabox-elem">
                                                                <img src="{{ static_asset('assets/img/cards/flutterwave.png') }}"
                                                                    class="img-fluid mb-2">
                                                                <span class="d-block text-center">
                                                                    <span
                                                                        class="d-block fw-600 fs-15">{{ translate('flutterwave') }}</span>
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                                @if (\App\Models\BusinessSetting::where('type', 'payfast')->first()->value == 1)
                                                    <div class="col-6 col-md-4">
                                                        <label class="aiz-megabox d-block mb-3">
                                                            <input value="payfast" class="online_payment" type="radio"
                                                                name="payment_option" checked>
                                                            <span class="d-block p-3 aiz-megabox-elem">
                                                                <img src="{{ static_asset('assets/img/cards/payfast.png') }}"
                                                                    class="img-fluid mb-2">
                                                                <span class="d-block text-center">
                                                                    <span
                                                                        class="d-block fw-600 fs-15">{{ translate('payfast') }}</span>
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                            @endif

                                            @if (
                                                \App\Models\Addon::where('unique_identifier', 'paytm')->first() != null &&
                                                    \App\Models\Addon::where('unique_identifier', 'paytm')->first()->activated)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="paytm" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block p-3 aiz-megabox-elem">
                                                            <img src="{{ static_asset('assets/img/cards/paytm.jpg') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('Paytm') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif

                                            @if (\App\Models\BusinessSetting::where('type', 'cash_payment')->first()->value == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="cash_on_delivery" class="online_payment"
                                                            type="radio" name="payment_option" checked>
                                                        <span class="d-block p-3 aiz-megabox-elem">
                                                            <img src="{{ static_asset('assets/img/cards/cod.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('Cash on
                                                                                                                                Delivery') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif

                                            @if (Auth::check())
                                                @if (
                                                    \App\Models\Addon::where('unique_identifier', 'offline_payment')->first() != null &&
                                                        \App\Models\Addon::where('unique_identifier', 'offline_payment')->first()->activated)
                                                    @foreach (\App\Models\ManualPaymentMethod::all() as $method)
                                                        <div class="col-6 col-md-4">
                                                            <label class="aiz-megabox d-block mb-3">
                                                                <input value="{{ $method->heading }}" type="radio"
                                                                    name="payment_option"
                                                                    onchange="toggleManualPaymentData({{ $method->id }})"
                                                                    data-id="{{ $method->id }}" checked>
                                                                <span class="d-block p-3 aiz-megabox-elem">
                                                                    <img src="{{ uploaded_asset($method->photo) }}"
                                                                        class="img-fluid mb-2">
                                                                    <span class="d-block text-center">
                                                                        <span
                                                                            class="d-block fw-600 fs-15">{{ $method->heading }}</span>
                                                                    </span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    @endforeach

                                                    @foreach (\App\Models\ManualPaymentMethod::all() as $method)
                                                        <div id="manual_payment_info_{{ $method->id }}" class="d-none">
                                                            @php echo $method->description @endphp
                                                            @if ($method->bank_info != null)
                                                                <ul>
                                                                    @foreach (json_decode($method->bank_info) as $key => $info)
                                                                        <li>{{ translate('Bank Name') }} -
                                                                            {{ $info->bank_name }},
                                                                            {{ translate('Account Name') }} -
                                                                            {{ $info->account_name }},
                                                                            {{ translate('Account Number') }} -
                                                                            {{ $info->account_number }},
                                                                            {{ translate('Routing Number') }} -
                                                                            {{ $info->routing_number }}</li>
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

                                @if (
                                    \App\Models\Addon::where('unique_identifier', 'offline_payment')->first() != null &&
                                        \App\Models\Addon::where('unique_identifier', 'offline_payment')->first()->activated)
                                    <div class="bg-white border mb-3 p-3 rounded text-left d-none">
                                        <div id="manual_payment_description"></div>
                                    </div>
                                @endif

                                @if (Auth::check() && \App\Models\BusinessSetting::where('type', 'wallet_system')->first()->value == 1)
                                    <div class="separator mb-3">
                                        <span class="bg-white px-3">
                                            <span class="opacity-60">{{ translate('Or') }}</span>
                                        </span>
                                    </div>

                                    <div class="text-center py-4">
                                        <div class="h6 mb-3">
                                            <span class="opacity-80">{{ translate('Your wallet balance :') }}</span>
                                            <span class="fw-600">{{ single_price(Auth::user()->balance) }}</span>
                                        </div>
                                        @if (Auth::check() && Auth::user()->user_type == 'customer')
                                            @php
                                                $cust = \App\Models\Customer::where('user_id', Auth::user()->id)->get();
                                                if (count($cust) > 0) {
                                                    $credit_limit = $cust[0]->credit_limit;
                                                } else {
                                                    $credit_limit = 0;
                                                }
                                            @endphp
                                            @if (Auth::user()->balance + $credit_limit <= 0)
                                                <button type="button" class="btn btn-secondary"
                                                    disabled>{{ translate('Insufficient balance') }}</button>
                                            @else
                                                <button type="button" onclick="use_wallet()"
                                                    class="btn btn-primary fw-600">{{ translate('Pay with wallet') }}</button>
                                            @endif
                                        @else
                                            @if (Auth::user()->balance <= 0)
                                                <button type="button" class="btn btn-secondary"
                                                    disabled>{{ translate('Insufficient balance') }}</button>
                                            @else
                                                <button type="button" onclick="use_wallet()"
                                                    class="btn btn-primary fw-600">{{ translate('Pay with wallet') }}</button>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="pt-3">
                            {{-- <label class="aiz-checkbox">
                            <input type="checkbox" required id="agree_checkbox">
                            <span class="aiz-square-check"></span>
                            <span>{{ translate('I agree to the') }}</span>
                        </label> --}}
                            <a href="{{ route('terms') }}" target="_blank">{{ translate('Terms and Conditions') }}</a>,
                            <a href="{{ route('returnpolicy') }}" target="_blank">{{ translate('Return Policy') }}</a> &
                            <a href="{{ route('privacypolicy') }}" target="_blank">{{ translate('Privacy Policy') }}</a>
                        </div>

                        <div class="row align-items-center pt-3">
                            <div class="col-6">
                                <a href="{{ route('home') }}" class="link link--style-3">
                                    <i class="las la-arrow-left"></i>
                                    {{ translate('Return to shop') }}
                                </a>
                            </div>
                            <div class="col-6 text-right">
                                <button type="button" onclick="submitOrder(this)"
                                    class="btn btn-primary fw-600">{{ translate('Complete Order') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4 mt-4 mt-lg-0">
                    @include('frontend.partials.cart_summary')
                </div>
            </div>
        </div>
    </section>
@endsection
@section('modal')
    <div class="modal fade" id="new-address-modal" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">{{ translate('New Address') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-default" role="form" action="{{ route('addresses.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="p-0">
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('Address') }}</label>
                                </div>
                                <div class="col-md-10">
                                    <textarea class="form-control textarea-autogrow mb-3" placeholder="{{ translate('Your Address') }}" rows="1"
                                        name="address" required></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('Country') }}</label>
                                </div>
                                <div class="col-md-10">
                                    <select class="form-control mb-3 aiz-selectpicker" data-live-search="true"
                                        name="country" required>
                                        @foreach (\App\Models\Country::where('status', 1)->get() as $key => $country)
                                            <option value="{{ $country->name }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if (\App\Models\BusinessSetting::where('type', 'shipping_type')->first()->value == 'area_wise_shipping')
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>{{ translate('City') }}</label>
                                    </div>
                                    <div class="col-md-10">
                                        <select class="form-control mb-3 aiz-selectpicker" data-live-search="true"
                                            name="city" required>
                                            @foreach (\App\Models\City::get() as $key => $city)
                                                <option value="{{ $city->name }}">{{ $city->getTranslation('name') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>{{ translate('City') }}</label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control mb-3"
                                            placeholder="{{ translate('Your City') }}" name="city" value=""
                                            required>
                                    </div>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-md-2">
                                    <!-- <label>{{ translate('Postal code') }}</label> -->
                                    <label>{{ translate('Select your Area') }}</label>
                                </div>
                                <div class="col-md-10">
                                    <!-- <input type="text" class="form-control mb-3" placeholder="{{ translate('Your Postal Code') }}" name="postal_code" value="" required> -->
                                    <select class="form-control aiz-selectpicker" data-live-search="true" name="area">
                                        @foreach (\App\Models\Area::get() as $key => $country)
                                            <option value="{{ $country->name }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('Phone') }}</label>
                                </div>
                                <div class="col-md-10">
                                    <input type="number" class="form-control mb-3"
                                        placeholder="{{ translate('+880') }}" name="phone" value="" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".online_payment").click(function() {
                $('#manual_payment_description').parent().addClass('d-none');
            });
            toggleManualPaymentData($('input[name=payment_option]:checked').data('id'));
        });

        function use_wallet() {
            $('input[name=payment_option]').val('wallet');
            // if($('#agree_checkbox').is(":checked")){
            $('#checkout-form').submit();
            // }else{
            //     AIZ.plugins.notify('danger','{{ translate('You need to agree with our policies') }}');
            // }
        }

        function submitOrder(el) {
            var error = 0;
            if ($('#checkout_type').val() == 'guest') {
                if ($('#guest_name').val() == '') {
                    $('#guest_name').focus();
                    error = 1;
                    alert('Please enter guest name');
                    return false;
                }
                // if ($('#guest_email').val() == '') {
                //     $('#guest_email').focus();
                //     error = 1;
                //     alert('Please enter guest email');
                //     return false;
                // }
                if ($('#guest_address').val() == '') {
                    $('#guest_address').focus();
                    error = 1;
                    alert('Please enter guest address');
                    return false;
                }
                if ($('#guest_phone').val() == '') {
                    error = 1;
                    alert('Please enter guest phone');
                    $('#guest_phone').focus();
                    return false;
                }
            }
            if (!error)
                $('#checkout-form').submit();
            // }else{
            //     AIZ.plugins.notify('danger','{{ translate('You need to agree with our policies') }}');
            //     $(el).prop('disabled', false);
            // }
        }

        function toggleManualPaymentData(id) {
            $('#manual_payment_description').parent().removeClass('d-none');
            $('#manual_payment_description').html($('#manual_payment_info_' + id).html());
        }
    </script>
    <script type="text/javascript">
        function add_new_address() {
            $('#new-address-modal').modal('show');
        }
    </script>
@endsection
