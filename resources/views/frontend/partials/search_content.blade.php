@if (count($products) > 0)
<div class="px-2 py-1 text-uppercase fs-10 text-right text-muted bg-soft-secondary">{{translate('Products')}}</div>
<div class="row gutters-5 row-cols-xxl-6 row-cols-xl-6 row-cols-lg-4 row-cols-md-3 row-cols-2">
    @foreach ($products as $key => $product)

    <div class="col mb-2">
        <form id="option-choice-form_{{ $product->id }}">
            @csrf
            <input type="hidden" name="id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="1">
            <div class="carousel-box">
                <div class="aiz-card-box border border-light rounded hov-shadow-md my-2 has-transition">
                    <div class="position-relative">
                        <a href="{{ route('product', $product->slug) }}" class="d-block">
                            <img class="img-fit lazyload mx-auto h-140px h-md-210px" src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ uploaded_asset($product->thumbnail_img) }}" alt="{{  $product->getTranslation('name')  }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
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
                    <div class="p-md-3 p-2 text-center">
                        <div class="fs-15">
                            @if(main_home_base_price($product->id) != main_home_discounted_base_price($product->id))
                            <del class="fw-600 opacity-50 mr-1">{{ main_home_base_price($product->id) }}</del>
                            @endif
                            <span class="fw-700 text-primary">{{ main_home_discounted_base_price($product->id) }}</span>
                        </div>
                        <div class="rating rating-sm mt-1">
                            {{ renderStarRating($product->rating) }}
                        </div>
                        @if(main_home_base_price($product->id) != main_home_discounted_base_price($product->id))
                        <h3 class="fw-600 fs-13 lh-1-4 mb-0" style="color:red">
                            @if($product->discount_type=='amount')
                            {{$product->discount}} TK Save
                            @else
                            {{$product->discount}} % Off
                            @endif
                        </h3>
                        @else
                        <h3 class="fw-600 fs-13 lh-1-4 mb-0">&nbsp;</h3>
                        @endif
                        <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0 h-35px">
                            <a href="{{ route('product', $product->slug) }}" class="d-block text-reset">{{ $product->getTranslation('name')  }}</a>
                        </h3>

                        @if (\App\Models\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Models\Addon::where('unique_identifier', 'club_point')->first()->activated)
                        <div class="rounded px-2 mt-2 bg-soft-primary border-soft-primary border">
                            {{ translate('Club Point') }}:
                            <span class="fw-700 float-right">{{ $product->earn_point }}</span>
                        </div>
                        @endif
                        <div class="mt-3">
                            @if ($product->outofstock==0)
                            <button type="button" id="addtocart_{{ $product->id }}" style="width:100%" class="btn btn-primary buy-now fw-600 add-to-cart" onclick="directAdd({{ $product->id }})">
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
        </form>
    </div>
    @endforeach
</div>
@endif

@if(\App\Models\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1)
<div class="">
    @if (count($shops) > 0)
    <div class="px-2 py-1 text-uppercase fs-10 text-right text-muted bg-soft-secondary">{{translate('Shops')}}</div>
    <ul class="list-group list-group-raw">
        @foreach ($shops as $key => $shop)
        <li class="list-group-item">
            <a class="text-reset" href="{{ route('shop.visit', $shop->slug) }}">
                <div class="d-flex search-product align-items-center">
                    <div class="mr-3">
                        <img class="size-40px img-fit rounded" src="{{ uploaded_asset($shop->logo) }}">
                    </div>
                    <div class="flex-grow-1 overflow--hidden">
                        <div class="product-name text-truncate fs-14 mb-5px">
                            {{ $shop->name }}
                        </div>
                        <div class="opacity-60">
                            {{ $shop->address }}
                        </div>
                    </div>
                </div>
            </a>
        </li>
        @endforeach
    </ul>
    @endif
</div>
@endif