@extends('frontend.layouts.app')

@section('content')

@php
if(Session::has('locale')){
$locale = Session::get('locale', Config::get('app.locale'));
}else{
$locale = 'en';
}

if($locale=='en'){
$app_banner_images = get_setting('app_banner_images');
$app_banner_links = get_setting('app_banner_links');
$home_banner1_images = get_setting('home_banner1_images');
$home_banner1_links = get_setting('home_banner1_links');
$home_banner2_images = get_setting('home_banner2_images');
$home_banner2_links = get_setting('home_banner2_links');
$home_banner3_images = get_setting('home_banner3_images');
$home_banner3_links = get_setting('home_banner3_links');
}else{
$app_banner_images = get_setting('app_banner_bangla_images');
$app_banner_links = get_setting('app_banner_bangla_links');
$home_banner1_images = get_setting('home_banner1_bangla_images');
$home_banner1_links = get_setting('home_banner1_bangla_links');
$home_banner2_images = get_setting('home_banner2_bangla_images');
$home_banner2_links = get_setting('home_banner2_bangla_links');
$home_banner3_images = get_setting('home_banner3_bangla_images');
$home_banner3_links = get_setting('home_banner3_bangla_links');
}

$num_todays_deal = count(filter_products(\App\Models\Product::where('published', 1)->where('todays_deal', 1 ))->get());
$featured_categories = \App\Models\Category::where('featured', 1)->get();
$happy_hour = \App\Models\HappyHour::where('status', 1)->where('featured', 1)->first();

if($locale=='en'){
$app_banner_images = get_setting('app_banner_images');
$app_banner_links = get_setting('app_banner_links');
$home_banner1_images = get_setting('home_banner1_images');
$home_banner1_links = get_setting('home_banner1_links');
$home_banner2_images = get_setting('home_banner2_images');
$home_banner2_links = get_setting('home_banner2_links');
$home_banner3_images = get_setting('home_banner3_images');
$home_banner3_links = get_setting('home_banner3_links');
}else{
$app_banner_images = get_setting('app_banner_bangla_images');
$app_banner_links = get_setting('app_banner_bangla_links');
$home_banner1_images = get_setting('home_banner1_bangla_images');
$home_banner1_links = get_setting('home_banner1_bangla_links');
$home_banner2_images = get_setting('home_banner2_bangla_images');
$home_banner2_links = get_setting('home_banner2_bangla_links');
$home_banner3_images = get_setting('home_banner3_bangla_images');
$home_banner3_links = get_setting('home_banner3_bangla_links');
}
@endphp

<style>
    .featured-category-box {
        transition: all 0.3s ease;
    }

    .hov-scale-img {
        display: block;
        width: 100%;
        height: 100%;
        overflow: hidden;
        border-radius: 50%;
        padding: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        background: rgba(234, 234, 234, 0);
    }

    .hov-scale-img img {
        transition: transform 0.4s ease;
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .hov-scale-img:hover img {
        transform: scale(1.05);
    }

    .category-name-link {
        text-decoration: none;
        transition: all 0.3s;
    }

    .category-name-link:hover {
        text-decoration: underline;
    }
</style>


<style>
    .fb-story-container {
        display: flex;
        overflow-x: auto;
        gap: 12px;
        padding: 20px 10px;
        background: #fff;
        scroll-behavior: smooth;
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .fb-story-container::-webkit-scrollbar { display: none; }

    .fb-story-card {
        flex: 0 0 auto;
        width: 120px;
        height: 200px;
        border-radius: 15px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        cursor: pointer;
        transition: transform 0.3s ease;
    }
    .fb-story-card:hover { transform: scale(1.03); }
    .fb-story-bg {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .fb-story-overlay {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.5));
        z-index: 1;
    }
    .fb-story-name {
        position: absolute;
        bottom: 10px; left: 10px; right: 10px;
        color: #fff;
        font-weight: 600;
        font-size: 14px;
        text-align: center;
        z-index: 2;
    }
    .story-carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgba(0,0,0,0.4);
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        font-size: 20px;
        z-index: 10;
        cursor: pointer;
    }
    .story-carousel-btn.left { left: 0; }
    .story-carousel-btn.right { right: 0; }
    @media (max-width: 576px) {
        .story-carousel-btn { display: none; }
    }

    /* Modal Styles */
    .story-modal {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.85);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        display: none;
    }
    .story-modal.active { display: flex; }

    .story-content {
        position: relative;
        max-width: 90%;
        max-height: 90%;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        background: black;
    }
    .story-content img,
    .story-content video {
        width: 100%;
        height: 100%;
        object-fit: contain;
        background: #000;
    }

    .modal-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgba(0,0,0,0.6);
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        font-size: 20px;
        cursor: pointer;
        z-index: 11;
        user-select: none;
    }
    .modal-nav-btn.left { left: -50px; }
    .modal-nav-btn.right { right: -50px; }

    .modal-close-btn {
        position: absolute;
        top: 10px; right: 10px;
        background: #fff;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        font-size: 16px;
        cursor: pointer;
        z-index: 12;
        user-select: none;
        line-height: 0;
    }
</style>

@php
    $storyFilesSetting = \App\Models\BusinessSetting::where('type', 'story_files')->first();
    $storyLinksSetting = \App\Models\BusinessSetting::where('type', 'story_links')->first();
    $storyNamesSetting = \App\Models\BusinessSetting::where('type', 'story_names')->first();

    $storyFiles = $storyFilesSetting && $storyFilesSetting->value ? json_decode($storyFilesSetting->value, true) : [];
    $storyLinks = $storyLinksSetting && $storyLinksSetting->value ? json_decode($storyLinksSetting->value, true) : [];
    $storyNames = $storyNamesSetting && $storyNamesSetting->value ? json_decode($storyNamesSetting->value, true) : [];

    $stories = [];
    $count = max(count($storyFiles), count($storyLinks), count($storyNames));
    for ($i = 0; $i < $count; $i++) {
        $stories[] = [
            'bg' => $storyFiles[$i] ?? null,
            'link' => $storyLinks[$i] ?? '#',
            'name' => $storyNames[$i] ?? 'Story',
        ];
    }

    foreach ($stories as $k => $story) {
        $stories[$k]['full_url'] = $story['bg'] ? uploaded_asset($story['bg']) : static_asset('assets/img/placeholder.jpg');
    }
@endphp

@if(count($stories) > 0)
<div class="container-fluid position-relative">
    <button class="story-carousel-btn left" onclick="scrollStories(-300)">
        <i class="las la-angle-left"></i>
    </button>
    <button class="story-carousel-btn right" onclick="scrollStories(300)">
        <i class="las la-angle-right"></i>
    </button>

    <div id="fbStoryContainer" class="fb-story-container">
        @foreach ($stories as $index => $story)
            @php
                $extension = strtolower(pathinfo($story['full_url'], PATHINFO_EXTENSION));
                $isVideo = in_array($extension, ['mp4', 'webm', 'ogg']);
            @endphp
            <div class="fb-story-card" onclick="openStory({{ $index }})" role="button" tabindex="0" onkeydown="if(event.key === 'Enter') openStory({{ $index }});">
                @if ($isVideo)
                    <video class="fb-story-bg" muted autoplay loop playsinline>
                        <source src="{{ $story['full_url'] }}" type="video/{{ $extension }}">
                    </video>
                @else
                    <img src="{{ $story['full_url'] }}" class="fb-story-bg" alt="story" />
                @endif
                <div class="fb-story-overlay"></div>
                <div class="fb-story-name"><b>{{ $story['name'] }}</b></div>
            </div>
        @endforeach
    </div>

    <!-- Modal -->
    <div id="storyModal" class="story-modal" role="dialog" aria-modal="true" aria-labelledby="storyName">
        <div class="story-content" tabindex="0">
            <button class="modal-close-btn" aria-label="Close story" onclick="event.stopPropagation(); closeStory()">×</button>

            <button class="modal-nav-btn left" aria-label="Previous story" onclick="navigateStory(-1)">‹</button>
            <div id="storyMediaContainer"></div>
            <button class="modal-nav-btn right" aria-label="Next story" onclick="navigateStory(1)">›</button>
        </div>
    </div>
</div>
@endif

<script>
    const stories = @json($stories);
    let currentStoryIndex = 0;

    function scrollStories(amount) {
        const container = document.getElementById('fbStoryContainer');
        container.scrollLeft += amount;
    }

    function openStory(index) {
        currentStoryIndex = index;
        const story = stories[index];
        const url = story.full_url;
        const extension = url.split('.').pop().toLowerCase();
        const isVideo = ['mp4', 'webm', 'ogg'].includes(extension);

        let mediaHtml = isVideo ?
            `<video autoplay muted loop playsinline controls style="max-height: 80vh; width: auto; display: block; margin: 0 auto; border-radius: 8px;">
                <source src="${url}" type="video/${extension}">
                Your browser does not support the video tag.
            </video>` :
            `<img src="${url}" alt="${story.name}" style="max-height: 80vh; width: auto; display: block; margin: 0 auto; border-radius: 8px;" />`;

        let linkHtml = (story.link && story.link !== '#') ? `
            <div style="text-align: center; margin-top: 16px; padding-bottom: 10px;">
                <a href="${story.link}" target="_blank" rel="noopener noreferrer"
                    style="display: inline-block; padding: 10px 22px; background-color: #1877f2; color: white; border-radius: 24px; font-weight: 600; text-decoration: none; font-size: 16px; box-shadow: 0 3px 8px rgba(24,119,242,0.6); transition: background-color 0.3s ease;"
                    onmouseover="this.style.backgroundColor='#145dbf';"
                    onmouseout="this.style.backgroundColor='#1877f2';"
                    onclick="event.stopPropagation();">
                    View
                </a>
            </div>` : '';

        document.getElementById('storyMediaContainer').innerHTML = `
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                ${mediaHtml}
                ${linkHtml}
            </div>`;

        document.getElementById('storyModal').classList.add('active');
        document.getElementById('storyModal').focus();
    }

    function closeStory() {
        document.getElementById('storyModal').classList.remove('active');
        document.getElementById('storyMediaContainer').innerHTML = '';
    }

    function navigateStory(direction) {
        currentStoryIndex += direction;
        if (currentStoryIndex < 0) currentStoryIndex = stories.length - 1;
        if (currentStoryIndex >= stories.length) currentStoryIndex = 0;
        openStory(currentStoryIndex);
    }

    document.addEventListener('keydown', (e) => {
        const modal = document.getElementById('storyModal');
        if (!modal.classList.contains('active')) return;

        if (e.key === 'ArrowRight') navigateStory(1);
        else if (e.key === 'ArrowLeft') navigateStory(-1);
        else if (e.key === 'Escape' || document.getElementById('storyModal').classList.remove('active')) closeStory();
    });

    document.getElementById('storyModal').addEventListener('click', (e) => {
        if (e.target === e.currentTarget) closeStory();
    });

    document.querySelector('.story-content').addEventListener('click', function(event) {
        if (event.target.closest('a[href]')) return;
        navigateStory(1);
    });
</script>


    <div class="container-fluid">
        <div class="px-2 py-4 px-md-4 py-md-3 bg-white shadow-sm rounded">
            @if (count($featured_categories) > 0)
            <div class="d-flex justify-content-between align-items-center mb-3 px-2 px-md-0">
                <h5 class="mb-0 fw-bold text-dark" style="font-size: 1.25rem; border-left: 4px solid #FF5722; padding-left: 10px;">
                    <i class="las la-star text-warning me-1"></i> Featured Categories
                </h5>
            </div>
            <div class="row gx-2 gy-3 px-2 px-md-3">
                @foreach ($featured_categories as $key => $category)
                @php
                $category_name = $category->getTranslation('name');
                @endphp
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6 d-flex justify-content-center mb-2">
                    <div class="featured-category-box d-flex flex-column align-items-center overflow-hidden">
                        <div class="hov-scale-img shadow-md" style="width: 150px; height: 150px;">
                            <a href="{{ route('products.category', $category->slug) }}">
                                <img src="{{ isset($category->banner) ? uploaded_asset($category->banner) : static_asset('assets/img/placeholder.jpg') }}"
                                    class="lazyload img-fluid"
                                    alt="{{ $category_name }}"
                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                            </a>
                        </div>
                        <p class="mt-2 mb-0 fs-13 fw-600 text-center text-truncate-2 px-2">
                            <a class="text-reset category-name-link" href="{{ route('products.category', $category->slug) }}">
                                {{ $category_name }}
                            </a>
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Flash Deal --}}
    @php
    $flash_deal = \App\Models\FlashDeal::where('status', 1)->where('featured', 1)->first();
    @endphp

    @if($flash_deal != null && strtotime(date('Y-m-d H:i:s')) >= $flash_deal->start_date && strtotime(date('Y-m-d H:i:s')) <= $flash_deal->end_date)
    <section class="mb-4">
        <div class="container-fluid">
            <div class="px-2 py-4 px-md-4 py-md-3 bg-white shadow-sm rounded">

                <div class="d-flex flex-wrap mb-3 align-items-baseline border-bottom">
                    <h3 class="h5 fw-700 mb-0">
                        <span class="border-bottom border-primary border-width-2 pb-3 d-inline-block">{{ translate('Flash Sale') }}</span>
                    </h3>
                    <div class="aiz-count-down ml-auto ml-lg-3 align-items-center" data-date="{{ date('Y/m/d H:i:s', $flash_deal->end_date) }}"></div>
                    <a href="{{ route('flash-deal-details', $flash_deal->slug) }}" class="ml-auto mr-0 btn btn-primary btn-sm shadow-md w-100 w-md-auto">{{ translate('View More') }}</a>
                </div>

                <div class="aiz-carousel gutters-10 half-outside-arrow" data-items="6" data-xl-items="5" data-lg-items="4" data-md-items="3" data-sm-items="2" data-xs-items="2" data-arrows='true' data-infinite='true'>
                    @foreach ($flash_deal->flash_deal_products as $key => $flash_deal_product)
                    @php
                    $product = \App\Models\Product::find($flash_deal_product->product_id);
                    @endphp
                    @if ($product != null && $product->published != 0)
                    <div class="carousel-box">
                        <div class="aiz-card-box border border-light rounded hov-shadow-md my-2 has-transition">
                            <div class="position-relative">
                                <a href="{{ route('product', $product->slug) }}" class="d-block">
                                    <img
                                        class="img-fit lazyload mx-auto h-140px h-md-210px"
                                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                        data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                                        alt="{{  $product->getTranslation('name')  }}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
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
                                    @if(main_home_base_price($product->id) != main_home_discounted_base_price($product->id))
                                    <del class="fw-600 opacity-50 mr-1">{{ main_home_base_price($product->id) }}</del>
                                    @endif
                                    <span class="fw-700 text-primary">{{ main_home_discounted_base_price($product->id) }}</span>
                                </div>
                                <div class="rating rating-sm mt-1">
                                    {{ renderStarRating($product->rating) }}
                                </div>
                                <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0 h-35px">
                                    <a href="{{ route('product', $product->slug) }}" class="d-block text-reset">{{ $product->getTranslation('name')  }}</a>
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
        </div>
    </section>
    @endif

    {{-- Featured Section --}}
    <div id="section_featured">

    </div>

    {{-- Best Selling  --}}
    <div id="section_best_selling">

    </div>

    {{-- Category wise Products --}}
    <div id="section_home_categories">

    </div>

    <div id="group_product_section">

    </div>

    {{-- Best Seller --}}
    @if (\App\Models\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1)
    <div id="section_best_sellers">

    </div>
    @endif

    {{-- Top 10 categories and Brands --}}
    <section class="mb-4">
        <div class="container-fluid">
            <div class="row gutters-10 px-2 py-4 px-md-4 py-md-3">

                @if (get_setting('top10_categories') != null)
                <div class="col-lg-12">
                    <div class="d-flex mb-3 align-items-baseline border-bottom">
                        <h3 class="h5 fw-700 mb-0">
                            <span class="border-bottom border-primary border-width-2 pb-3 d-inline-block">{{ translate('Top 10 Brands') }}</span>
                        </h3>
                        <a href="{{ route('brands.all') }}" class="ml-auto mr-0 btn btn-primary btn-sm shadow-md">{{ translate('View All Brands') }}</a>
                    </div>
                    <div class="row gutters-5">
                        @php $top10_brands = json_decode(get_setting('top10_brands')); @endphp
                        @foreach ($top10_brands as $key => $value)
                        @php $brand = \App\Models\Brand::find($value); @endphp
                        @if ($brand != null)
                        <div class="col-sm-2">
                            <a href="{{ route('products.brand', $brand->slug) }}" class="bg-white border d-block text-reset rounded p-2 hov-shadow-md mb-2">
                                <div class="row align-items-center no-gutters">
                                    <div class="col-4 text-center">
                                        <img
                                            src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                            data-src="{{ uploaded_asset($brand->logo) }}"
                                            alt="{{ $brand->getTranslation('name') }}"
                                            class="img-fluid img lazyload h-60px"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';">
                                    </div>
                                    <div class="col-6">
                                        <div class="text-truncate-2 pl-3 fs-14 fw-600 text-left">{{ $brand->getTranslation('name') }}</div>
                                    </div>
                                    <div class="col-2 text-center">
                                        <i class="la la-angle-right text-primary"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>

    @endsection


        @section('script')
<script>
    $(document).ready(function(){
        $.post('{{ route('home.section.featured') }}', {_token:'{{ csrf_token() }}'}, function(data){
            $('#section_featured').html(data);
            AIZ.plugins.slickCarousel();
        });
        $.post('{{ route('home.section.best_selling') }}', {_token:'{{ csrf_token() }}'}, function(data){
            $('#section_best_selling').html(data);
            AIZ.plugins.slickCarousel();
        });
        $.post('{{ route('home.section.group_product') }}', {_token:'{{ csrf_token() }}'}, function(data){
            $('#group_product_section').html(data);
            AIZ.plugins.slickCarousel();
        });
        $.post('{{ route('home.section.home_categories') }}', {_token:'{{ csrf_token() }}'}, function(data){
            $('#section_home_categories').html(data);
            AIZ.plugins.slickCarousel();
        });

        @if (\App\Models\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1)
        $.post('{{ route('home.section.best_sellers') }}', {_token:'{{ csrf_token() }}'}, function(data){
            $('#section_best_sellers').html(data);
            AIZ.plugins.slickCarousel();
        });
        @endif
    });

    function directAdd(id, checking = false) {
        $('.c-preloader').show();

        // Fetch the CSRF token from the page's meta tag
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        var proqty = parseInt($('#productlist_' + id).text());
        proqty = (!proqty) ? 0 : proqty;

        $.ajax({
            type: "POST",
            url: '{{ route("cart.addToCart") }}',
            data: {
                _token: csrfToken, // Add CSRF token here
                product_id: id, // Ensure you send the product ID as well
                quantity: proqty + 1, // Include quantity if necessary
            },
            success: function(data) {
                if (data.status == 1) {
                    AIZ.plugins.notify('success', "{{ translate('Item has been added to cart list') }}");
                    //     $('#addToCart').modal();
                    //     $('#addToCart-modal-body').html(null);
                    //    $('.c-preloader').hide();
                    //    $('#modal-size').removeClass('modal-lg');
                    //    $('#addToCart-modal-body').html(data.view);
                    if (!checking)
                        var key = Number($('#sightsidecarttotal').text());
                    else {
                        var key = Number(($('#productlist_' + id).parents('button').prev('button').attr('onclick').split(',')[0]).split('(')[1]);
                    }

                    $('#productlist_' + id).parents('button').attr('onclick', 'updateQuantityAdd(' + key + ', ' + (proqty + 1) + ',' + id + ')');
                    $('#productlist_' + id).parents('button').prev('button').attr('onclick', 'updateQuantityMinus(' + key + ', ' + (proqty + 1) + ',' + id + ')');
                    $('#productlist_' + id).parents('button').next('button').attr('onclick', 'updateQuantityPlus(' + key + ', ' + (proqty + 1) + ',' + id + ')');
                    updateNavCart();
                    //cartAnimation(id);
                    //  $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html())+1);
                } else {
                    AIZ.plugins.notify('warning', data.msg);
                }
                $('.c-preloader').hide();
            }
        });

        if (proqty >= 1) {
            $('#productlist_' + id).text(proqty + 1);
        } else {
            $('#productlist_' + id).text(1);
        }

        $('#pro_cart_in_' + id).show();
        $('#pro_add_to_cart_' + id).hide();
    }

</script>
@endsection