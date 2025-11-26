@if (App\Models\BusinessSetting::where('type', 'best_selling')->first()->value == 1)
<section class="mb-4">
    <div class="container-fluid">
        <div class="px-2 py-4 px-md-4 py-md-3 bg-white shadow-sm rounded">
            <div class="d-flex mb-3 align-items-baseline border-bottom">
                <h3 class="h5 fw-700 mb-0">
                    <span class="border-bottom border-primary border-width-2 pb-3 d-inline-block">{{ translate('Best Selling') }}</span>
                </h3>
                <a href="javascript:void(0)" class="ml-auto mr-0 btn btn-primary btn-sm shadow-md">{{ translate('Top 20') }}</a>
            </div>
            <div class="aiz-carousel gutters-10 half-outside-arrow" data-items="6" data-xl-items="5" data-lg-items="4" data-md-items="3" data-sm-items="2" data-xs-items="2" data-arrows='true' data-infinite='true'>
                @foreach (filter_products(App\Models\Product::where('published', 1)->where('is_group_product',0)->orderBy('num_of_sale', 'desc'))->limit(12)->get() as $key => $product)
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
                            <div class="p-md-3 p-2 text-center feature_text_blog">
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

                                @if (App\Models\Addon::where('unique_identifier', 'club_point')->first() != null && App\Models\Addon::where('unique_identifier', 'club_point')->first()->activated)
                                <div class="rounded px-2 mt-2 bg-soft-primary border-soft-primary border">
                                    {{ translate('Club Point') }}:
                                    <span class="fw-700 float-right">{{ $product->earn_point }}</span>
                                </div>
                                @endif
                                <div class="mt-3 feature_button_block">
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
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif