@extends('frontend.layouts.app')

@section('content')

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

<section class="pt-4 mb-4">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-6 text-center text-lg-left">
                <h1 class="fw-600 h4">{{ translate('Flash Deals')}}</h1>
            </div>
            <div class="col-lg-6">
                <ul class="breadcrumb bg-transparent p-0 justify-content-center justify-content-lg-end">
                    <li class="breadcrumb-item opacity-50">
                        <a class="text-reset" href="{{ route('home') }}">
                            {{ translate('Home')}}
                        </a>
                    </li>
                    <li class="text-dark fw-600 breadcrumb-item">
                        <a class="text-reset" href="{{ route('flash-deals') }}">
                            "{{ translate('Flash Deals') }}"
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="mb-4">
    <div class="container">
        <div class="row row-cols-1 row-cols-lg-1 gutters-10">
            @foreach($data as $single)
            <div class="col">
                <div class="bg-white rounded shadow-sm mb-3">
                    <a href="{{ route('flash-deal-details', $single->slug) }}" class="d-block text-reset">
                        <img
                            src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                            data-src="{{ uploaded_asset($single->banner) }}"
                            alt="{{ $single->title }}"
                            class="img-fluid lazyload rounded">
                    </a>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</section>

<section class="mb-4 pt-3">
    <div class="container sm-px-0">


        <div class="row mb-2">
            <div class="col-md-4">
                <hr style="background:#AE3C86;height:2px;">
            </div>
            <div class="col-md-4" style="font-size:24px;font-weight:bold;text-align:center;">Flash Deals Product</div>
            <div class="col-md-4">
                <hr style="background:#AE3C86;height:2px;">
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row">


            <div class="col-xl-12">

                <input type="hidden" name="min_price" value="">
                <input type="hidden" name="max_price" value="">
                <div class="row gutters-5 row-cols-xxl-4 row-cols-xl-3 row-cols-lg-4 row-cols-md-3 row-cols-sm-3 row-cols-2">
                    @foreach ($products as $key => $product)
                    <form class="pro_list_block" id="option-choice-form_{{ $product->id }}">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <div class="col mb-3">
                            <div class="aiz-card-box h-100 border border-light rounded shadow-sm hov-shadow-md has-transition bg-white">
                                <div class="position-relative">
                                    <a style="text-align:center;" href="{{ route('product', $product->slug) }}" class="d-block">
                                        <img class="lazyload mx-auto h-160px" src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ uploaded_asset($product->thumbnail_img) }}" alt="{{  $product->getTranslation('name')  }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                    </a>
                                    <div class="absolute-top-right aiz-p-hov-icon">
                                        <a href="javascript:void(0)" onclick="addToWishList({{ $product->id }})" data-toggle="tooltip" data-title="{{ translate('Add to wishlist') }}" data-placement="left">
                                            <i class="la la-heart-o"></i>
                                        </a>
                                        <!-- <a href="javascript:void(0)" onclick="addToCompare({{ $product->id }})" data-toggle="tooltip" data-title="{{ translate('Add to compare') }}" data-placement="left">
                                                <i class="las la-sync"></i>
                                            </a> -->
                                        <a href="javascript:void(0)" onclick="showAddToCartModal({{ $product->id }})" data-toggle="tooltip" data-title="{{ translate('Add to cart') }}" data-placement="left">
                                            <i class="las la-shopping-cart"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="p-md-3 p-2 text-left">
                                    <div class="fs-15">
                                        @if(main_home_base_price($product->id) != main_home_flash_deal_price($product->id))
                                        <del class="fw-600 opacity-50 mr-1">{{ main_home_base_price($product->id) }}</del>
                                        @endif
                                        <span class="fw-700 text-primary">{{ main_home_flash_deal_price($product->id) }}</span>
                                    </div>
                                    <div class="rating rating-sm mt-1">
                                        {{ renderStarRating($product->rating) }}
                                    </div>
                                    <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0">
                                        <a href="{{ route('product', $product->slug) }}" class="d-block text-reset">{{ $product->getTranslation('name') }}</a>
                                    </h3>

                                    @if (\App\Models\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Models\Addon::where('unique_identifier', 'club_point')->first()->activated)
                                    <div class="rounded px-2 mt-2 bg-soft-primary border-soft-primary border">
                                        {{ translate('Club Point') }}:
                                        <span class="fw-700 float-right">{{ $product->earn_point }}</span>
                                    </div>
                                    @endif
                                    <div class="mt-3">
                                        @php
                                        if(in_array($product->id,$cartIds)==true){
                                        $pkey = $keys[$product->id];
                                        $procartqty = $cartqty[$product->id];
                                        $showt = 'block';
                                        $showf = 'none';

                                        }elseif(in_array($product->id,$cartIds)==false){
                                        $pkey = '';
                                        $procartqty = '';
                                        $showf = 'block';
                                        $showt = 'none';

                                        }


                                        @endphp
                                        @if ($product->outofstock==0)
                                        <div id="pro_cart_in_{{$product->id}}" style="display: {{$showt}};">
                                            <button type="button" onclick="updateQuantityMinus({{ $pkey }}, {{$procartqty}},{{ $product->id }})" style="width:15%;padding: 5px 4px;background-color: #dba9c9;border: 1px solid #dba9c9;border-top-right-radius: 0;border-bottom-right-radius: 0;" class="btn btn-primary buy-now fw-600 ">
                                                <i class="la la-minus"></i></button>
                                            <button type="button" style="width:65%;padding:5px 10px;border-radius: 0;" class="btn btn-primary buy-now fw-600 add-to-cart" onclick="updateQuantityAdd({{ $pkey }}, {{$procartqty}},{{ $product->id }})">
                                                <i class="la la-shopping-cart"></i>
                                                <span class="d-none d-md-inline-block"><span id="productlist_{{$product->id}}">{{$procartqty}}</span> in cart</span>
                                            </button>
                                            <button type="button" onclick="updateQuantityPlus({{ $pkey }}, {{$procartqty}},{{ $product->id }})" style="width:15%;padding: 5px 4px;background-color: #dba9c9;border: 1px solid #dba9c9;border-top-left-radius: 0;border-bottom-left-radius: 0;" class="btn btn-primary buy-now fw-600 ">
                                                <i class="la la-plus"></i></button>
                                        </div>

                                        <button id="pro_add_to_cart_{{$product->id}}" style="padding:5px 10px;width:100%;display: {{$showf}};" type="button" style="width:100%;padding:5px 10px" class="btn btn-primary buy-now fw-600 add-to-cart" onclick="directAdd({{ $product->id }})">
                                            <i class="la la-shopping-cart"></i>
                                            <span class="d-none d-md-inline-block"> Add to cart</span>
                                        </button>

                                        @else
                                        <button type="button" class="btn btn-secondary fw-600" disabled>
                                            <i class="la la-cart-arrow-down"></i> {{ translate('Out of Stock')}}
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    @endforeach
                </div>
                <div class="aiz-pagination aiz-pagination-center mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>

    </div>
</section>
@endsection