@extends('frontend.layouts.app')

@section('content')

@if($happy_hour->status == 1 && strtotime(date('Y-m-d H:i:s')) <= $happy_hour->end_date) 
<div style="background-color:{{ $happy_hour->background_color }}">
    <section class="text-center mb-5">
        <img
            src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
            data-src="{{ uploaded_asset($happy_hour->banner) }}"
            alt="{{ $happy_hour->title }}"
            class="img-fit w-100 lazyload"
        >
    </section>
    <section class="mb-4">
        <div class="container-fluid">
            <div class="text-center my-4 text-{{ $happy_hour->text_color }}">
                <h1 class="h2 fw-600">{{ $happy_hour->title }}</h1>
                <div class="aiz-count-down aiz-count-down-lg ml-3 align-items-center justify-content-center" data-date="{{ date('Y/m/d H:i:s', $happy_hour->end_date) }}"></div>
            </div>
            <div class="row gutters-5 row-cols-xxl-5 row-cols-lg-4 row-cols-md-3 row-cols-2">
                @foreach ($happy_hour->happy_hour_products as $key => $happy_hour_product)
                    @php
                        $product = \App\Models\Product::find($happy_hour_product->product_id);
                    @endphp
                    @if ($product->published != 0)
                        <div class="col mb-2">
                            <div class="aiz-card-box border border-light rounded shadow-sm hov-shadow-md h-100 has-transition bg-white">
                                <div class="position-relative">
                                    <a href="{{ route('product', $product->slug) }}" class="d-block">
                                        <img
                                            class="img-fit lazyload mx-auto h-160px h-sm-200px h-md-220px h-xl-270px"
                                            src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                            data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                                            alt="{{  $product->getTranslation('name')  }}"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                        >
                                    </a>
                                    <div class="absolute-top-right aiz-p-hov-icon">
                                        <a href="javascript:void(0)" onclick="addToWishList({{ $product->id }})" data-toggle="tooltip" data-title="{{ translate('Add to wishlist') }}" data-placement="left">
                                            <i class="la la-heart-o"></i>
                                        </a>
                                        <a href="javascript:void(0)" onclick="showAddToCartModal({{ $product->id }})" data-toggle="tooltip" data-title="{{ translate('Add to cart') }}" data-placement="left">
                                            <i class="las la-shopping-cart"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="p-md-3 p-2 text-left">
                                    <div class="fs-15">
                                        @if(main_home_base_price($product->id) != main_home_discounted_price($product->id))
                                            <del class="fw-600 opacity-50 mr-1">{{ main_home_base_price($product->id) }}</del>
                                        @endif
                                        <span class="fw-700 text-primary">{{ main_home_discounted_price($product->id) }}</span>
                                    </div>
                                    <div class="rating rating-sm mt-1">
                                        {{ renderStarRating($product->rating) }}
                                    </div>
                                    <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0">
                                        <a href="{{ route('product', $product->slug) }}" class="d-block text-reset">{{  $product->getTranslation('name')  }}</a>
                                    </h3>

                                    @if (\App\Models\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Models\Addon::where('unique_identifier', 'club_point')->first()->activated)
                                        <div class="rounded px-2 mt-2 bg-soft-primary border-soft-primary border">
                                            {{ translate('Club Point') }}:
                                            <span class="fw-700 float-right">{{ $product->earn_point }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
</div>
@else
    <div style="background-color:{{ $happy_hour->background_color }}">
        <section class="text-center">
            <img src="{{ uploaded_asset($happy_hour->banner) }}" alt="{{ $happy_hour->title }}" class="img-fit w-100">
        </section>
        <section class="pb-4">
            <div class="container-fluid">
                <div class="text-center text-{{ $happy_hour->text_color }}">
                    <h1 class="h3 my-4">{{ $happy_hour->title }}</h1>
                    <p class="h4">{{  translate('This offer has been expired.') }}</p>
                </div>
            </div>
        </section>
    </div>
@endif
@endsection
