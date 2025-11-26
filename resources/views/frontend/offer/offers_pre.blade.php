@extends('frontend.layouts.app')

@section('content')
@php
$privacy_policy = \App\Models\Page::where('type', 'privacy_policy_page')->first();
@endphp
<style>
    .offer_section {

        border: 1px solid #ddd;
        padding-left: 5px;
        padding-right: 5px;
    }

    .offer-left {
        width: 65%;
        float: left;
    }

    .offer-right {
        width: 35%;
        float: left;
    }
</style>
<section class="pt-4 mb-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-lg-left">
                <h1 class="fw-600 h4">{{translate('Special Offers') }}</h1>
            </div>

        </div>
        <div class="row">
            <div class="col-lg-6 text-center text-lg-left offer_section" style="display:none;">
                <div class="offer-left">
                    <img class="img-fluid" src="{{ static_asset('assets/img/offer.webp') }}">
                </div>
                <div class="offer-right">
                    <form id="option-choice-form_47">
                        <input type="hidden" name="_token" value="cg4swAowGXhNMvmoxc1hZ4svl2vj8TUWwJK7SJsu"> <input type="hidden" name="id" value="47">
                        <input type="hidden" name="quantity" value="1">
                        <div class="">
                            <div class="aiz-card-box h-100 border border-light rounded shadow-sm hov-shadow-md has-transition bg-white">
                                <div class="position-relative">
                                    <a style="text-align:center;" href="https://bazarnao.shop/product/-tyg1a" class="d-block">
                                        <img class="mx-auto h-160px lazyloaded" src="https://bazarnao.shop/public/uploads/all/2020/06/pran-chinigura-rice-2-kg-1.jpg" data-src="https://bazarnao.shop/public/uploads/all/2020/06/pran-chinigura-rice-2-kg-1.jpg" alt="PRAN Chinigura Rice- 2 kg" onerror="this.onerror=null;this.src='https://bazarnao.shop/public/assets/img/placeholder.jpg';">
                                    </a>
                                    <div class="absolute-top-right aiz-p-hov-icon">
                                        <a href="javascript:void(0)" onclick="addToWishList(47)" data-toggle="tooltip" data-title="Add to wishlist" data-placement="left" data-original-title="" title="">
                                            <i class="la la-heart-o"></i>
                                        </a>
                                        <!-- <a href="javascript:void(0)" onclick="addToCompare(47)" data-toggle="tooltip" data-title="Add to compare" data-placement="left">
                                                <i class="las la-sync"></i>
                                            </a> -->
                                        <a href="javascript:void(0)" onclick="showAddToCartModal(47)" data-toggle="tooltip" data-title="Add to cart" data-placement="left" data-original-title="" title="">
                                            <i class="las la-shopping-cart"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="p-2 text-left">
                                    <div class="fs-15">
                                        <span class="fw-700 text-primary">TK 247.00</span>
                                    </div>
                                    <div class="rating rating-sm mt-1">

                                    </div>
                                    <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0">
                                        <a href="https://bazarnao.shop/product/-tyg1a" class="d-block text-reset">PRAN Chinigura Rice- 2 kg</a>
                                    </h3>

                                    <div class="mt-3">
                                        <button type="button" style="width:100%;padding:5px 10px" class="btn btn-primary buy-now fw-600 add-to-cart" onclick="directAdd(47)">
                                            <i class="la la-shopping-cart"></i>
                                            <span class="d-none d-md-inline-block"> Add to cart</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>


            </div>

        </div>
        <h2 style="text-align:center;">NO OFFER AVAILABLE</h2>
    </div>
</section>

@endsection