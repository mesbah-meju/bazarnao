<div class="text-left">
    <!-- Product Name -->
    <h2 class="mb-4 fs-16 fw-700 text-dark">
        {{ $detailedProduct->getTranslation('name') }}
    </h2>

    <div class="row align-items-center mb-3">
        @if ($detailedProduct->est_shipping_days)
        <div class="col-auto fs-14 mt-1">
            <small class="mr-1 opacity-50 fs-14">{{ translate('Estimate Shipping Time') }}:</small>
            <span class="fw-500">{{ $detailedProduct->est_shipping_days }} {{ translate('Days') }}</span>
        </div>
        @endif

    </div>
    <div class="row align-items-center">
       
        <div class="col mb-3">
            <div class="d-flex">
                <!-- Add to wishlist button -->
                <a href="javascript:void(0)" onclick="addToWishList({{ $detailedProduct->id }})" class="mr-3 fs-14 text-dark opacity-60 has-transitiuon hov-opacity-100">
                    <i class="la la-heart-o mr-1"></i>
                    {{ translate('Add to Wishlist') }}
                </a>
            </div>
            
        </div>
    </div>


    <!-- Brand Logo & Name -->
    @if ($detailedProduct->brand != null)
    <div class="d-flex flex-wrap align-items-center mb-3">
        <span class="text-secondary fs-14 fw-400 mr-4 w-50px">{{ translate('Brand') }}</span><br>
        <a href="{{ route('products.brand', $detailedProduct->brand->slug) }}" class="text-reset hov-text-primary fs-14 fw-700">{{ $detailedProduct->brand->name }}</a>
    </div>
    @endif

  
    @if (home_price($detailedProduct) != home_discounted_price($detailedProduct))
    <div class="row no-gutters mb-3">
        <div class="col-sm-2">
            <div class="text-secondary fs-14 fw-400">{{ translate('Price') }}</div>
        </div>
        <div class="col-sm-10">
            <div class="d-flex align-items-center">
                <!-- Discount Price -->
                <strong class="fs-16 fw-700 text-primary">
                    {{ home_discounted_price($detailedProduct) }}
                </strong>
                <!-- Home Price -->
                <del class="fs-14 opacity-60 ml-2">
                    {{ home_price($detailedProduct) }}
                </del>
                <!-- Unit -->
                @if ($detailedProduct->unit != null)
                <span class="opacity-70 ml-1">/{{ $detailedProduct->getTranslation('unit') }}</span>
                @endif
                <!-- Discount percentage -->
                @if (discount_in_percentage($detailedProduct) > 0)
                <span class="bg-primary ml-2 fs-11 fw-700 text-white w-35px text-center p-1" style="padding-top:2px;padding-bottom:2px;">-{{ discount_in_percentage($detailedProduct) }}%</span>
                @endif
                <!-- Club Point -->
                @if (addon_is_activated('club_point') && $detailedProduct->earn_point > 0)
                <div class="ml-2 bg-warning d-flex justify-content-center align-items-center px-3 py-1" style="width: fit-content;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12">
                        <g id="Group_23922" data-name="Group 23922" transform="translate(-973 -633)">
                            <circle id="Ellipse_39" data-name="Ellipse 39" cx="6" cy="6" r="6" transform="translate(973 633)" fill="#fff" />
                            <g id="Group_23920" data-name="Group 23920" transform="translate(973 633)">
                                <path id="Path_28698" data-name="Path 28698" d="M7.667,3H4.333L3,5,6,9,9,5Z" transform="translate(0 0)" fill="#f3af3d" />
                                <path id="Path_28699" data-name="Path 28699" d="M5.33,3h-1L3,5,6,9,4.331,5Z" transform="translate(0 0)" fill="#f3af3d" opacity="0.5" />
                                <path id="Path_28700" data-name="Path 28700" d="M12.666,3h1L15,5,12,9l1.664-4Z" transform="translate(-5.995 0)" fill="#f3af3d" />
                            </g>
                        </g>
                    </svg>
                    <small class="fs-11 fw-500 text-white ml-2">{{ translate('Club Point') }}:
                        {{ $detailedProduct->earn_point }}</small>
                </div>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="row no-gutters mb-3">
        <div class="col-sm-2">
            <div class="text-secondary fs-14 fw-400">{{ translate('Price') }}</div>
        </div>
        <div class="col-sm-10">
            <div class="d-flex align-items-center">
                <!-- Discount Price -->
                <strong class="fs-16 fw-700 text-primary">
                    {{ home_discounted_price($detailedProduct) }}
                </strong>
                <!-- Unit -->
                @if ($detailedProduct->unit != null)
                <span class="opacity-70">/{{ $detailedProduct->getTranslation('unit') }}</span>
                @endif
                <!-- Club Point -->
                @if (addon_is_activated('club_point') && $detailedProduct->earn_point > 0)
                <div class="ml-2 bg-warning d-flex justify-content-center align-items-center px-3 py-1" style="width: fit-content;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12">
                        <g id="Group_23922" data-name="Group 23922" transform="translate(-973 -633)">
                            <circle id="Ellipse_39" data-name="Ellipse 39" cx="6" cy="6" r="6" transform="translate(973 633)" fill="#fff" />
                            <g id="Group_23920" data-name="Group 23920" transform="translate(973 633)">
                                <path id="Path_28698" data-name="Path 28698" d="M7.667,3H4.333L3,5,6,9,9,5Z" transform="translate(0 0)" fill="#f3af3d" />
                                <path id="Path_28699" data-name="Path 28699" d="M5.33,3h-1L3,5,6,9,4.331,5Z" transform="translate(0 0)" fill="#f3af3d" opacity="0.5" />
                                <path id="Path_28700" data-name="Path 28700" d="M12.666,3h1L15,5,12,9l1.664-4Z" transform="translate(-5.995 0)" fill="#f3af3d" />
                            </g>
                        </g>
                    </svg>
                    <small class="fs-11 fw-500 text-white ml-2">{{ translate('Club Point') }}:
                        {{ $detailedProduct->earn_point }}</small>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif



   
    <form id="option-choice-form">
        @csrf
        <input type="hidden" name="id" value="{{ $detailedProduct->id }}">
        <!-- Quantity -->
        <input type="hidden" name="quantity" value="1">
        
        <!-- Total Price -->
        <div class="row no-gutters pb-3 d-none" id="chosen_price_div">
            <div class="col-sm-2">
                <div class="text-secondary fs-14 fw-400 mt-1">{{ translate('Total Price') }}</div>
            </div>
            <div class="col-sm-10">
                <div class="product-price">
                    <strong id="chosen_price" class="fs-20 fw-700 text-primary">

                    </strong>
                </div>
            </div>
        </div>

    </form>
   

   
    <!-- Add to cart & Buy now Buttons -->
    <div class="mt-3">

        @if ($detailedProduct->external_link != null)
        <a type="button" class="btn btn-primary buy-now fw-600 add-to-cart px-4 rounded-0" href="{{ $detailedProduct->external_link }}">
            <i class="la la-share"></i> {{ translate($detailedProduct->external_link_btn) }}
        </a>
        @else
        <button type="button" class="btn btn-warning mr-2 add-to-cart fw-600 min-w-150px rounded-0 text-white" onclick="addToCart()">
            <i class="las la-shopping-bag"></i> {{ translate('Add to cart') }}
        </button>
        <button type="button" class="btn btn-primary buy-now fw-600 add-to-cart min-w-150px rounded-0" onclick="buyNow()">
            <i class="la la-shopping-cart"></i> {{ translate('Buy Now') }}
        </button>
        @endif
        <button type="button" class="btn btn-secondary out-of-stock fw-600 d-none" disabled>
            <i class="la la-cart-arrow-down"></i> {{ translate('Out of Stock') }}
        </button>

    </div>


    <!-- Refund -->
    @php
    $refund_sticker = get_setting('refund_sticker');
    @endphp
    @if (addon_is_activated('refund_request'))
    <div class="row no-gutters mt-3">
        <div class="col-sm-2">
            <div class="text-secondary fs-14 fw-400 mt-2">{{ translate('Refund') }}</div>
        </div>
        <div class="col-sm-10">
            @if ($detailedProduct->refundable == 1)
            <a href="{{ route('returnpolicy') }}" target="_blank">
                @if ($refund_sticker != null)
                <img src="{{ uploaded_asset($refund_sticker) }}" height="36">
                @else
                <img src="{{ static_asset('assets/img/refund-sticker.jpg') }}" height="36">
                @endif
            </a>
            <a href="{{ route('returnpolicy') }}" class="text-blue hov-text-primary fs-14 ml-3" target="_blank">{{ translate('View Policy') }}</a>
            @else
            <div class="text-dark fs-14 fw-400 mt-2">{{ translate('Not Applicable') }}</div>
            @endif
        </div>
    </div>
    @endif
    <!-- Share -->
    <div class="row no-gutters mt-4">
        <div class="col-sm-2">
            <div class="text-secondary fs-14 fw-400 mt-2">{{ translate('Share') }}</div>
        </div>
        <div class="col-sm-10">
            <div class="aiz-share"></div>
        </div>
    </div>
</div>