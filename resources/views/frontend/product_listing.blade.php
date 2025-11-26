@extends('frontend.layouts.app')

@if (isset($category_id))
@php
$meta_title = \App\Models\Category::find($category_id)->meta_title;
$meta_description = \App\Models\Category::find($category_id)->meta_description;
@endphp
@elseif (isset($brand_id))
@php
$meta_title = \App\Models\Brand::find($brand_id)->meta_title;
$meta_description = \App\Models\Brand::find($brand_id)->meta_description;
@endphp
@else
@php
$meta_title = get_setting('meta_title');
$meta_description = get_setting('meta_description');
@endphp
@endif

@section('meta_title'){{ $meta_title }}@stop
@section('meta_description'){{ $meta_description }}@stop

@section('meta')
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
<!-- Schema.org markup for Google+ -->
<meta itemprop="name" content="{{ $meta_title }}">
<meta itemprop="description" content="{{ $meta_description }}">

<!-- Twitter Card data -->
<meta name="twitter:title" content="{{ $meta_title }}">
<meta name="twitter:description" content="{{ $meta_description }}">

<!-- Open Graph data -->
<meta property="og:title" content="{{ $meta_title }}" />
<meta property="og:description" content="{{ $meta_description }}" />
@endsection

@section('content')

<section class="mb-4 pt-3">
    <div class="container-fluid sm-px-0">
            <div class="row">
                <div class="col-xl-12">

                    <ul class="breadcrumb bg-transparent p-0">
                        <li class="breadcrumb-item opacity-50">
                            <a class="text-reset" href="{{ route('home') }}">{{ translate('Home')}}</a>
                        </li>
                        @if(!isset($category_id))
                        <li class="breadcrumb-item fw-600  text-dark">
                            <a class="text-reset" href="{{ route('search') }}">"{{ translate('All Categories')}}"</a>
                        </li>
                        @else
                        <li class="breadcrumb-item opacity-50">
                            <a class="text-reset" href="{{ route('search') }}">{{ translate('All Categories')}}</a>
                        </li>
                        @endif
                        @if(isset($category_id))
                        <li class="text-dark fw-600 breadcrumb-item">
                            <a class="text-reset" href="{{ route('products.category', \App\Models\Category::find($category_id)->slug) }}">"{{ \App\Models\Category::find($category_id)->getTranslation('name') }}"</a>
                        </li>
                        @endif
                    </ul>

                    <div class="text-left">
                    <form class="" id="search-form" action="{{ route('search') }}" method="GET">
                        <div class="d-flex align-items-center">
                            <div>
                                <h1 class="h6 fw-600 text-body">
                                    @if(isset($category_id))
                                    {{ \App\Models\Category::find($category_id)->getTranslation('name') }}
                                    @elseif(isset($query))
                                    {{ translate('Search result for ') }}"{{ $query }}"
                                    @else
                                    {{ translate('All Products') }}
                                    @endif
                                </h1>
                            </div>
                            
                            <div class="form-group ml-auto mr-0 w-200px d-none d-xl-block">
                                <label class="mb-0 opacity-50">{{ translate('Brands')}}</label>
                                <select class="form-control form-control-sm aiz-selectpicker" data-live-search="true" name="brand" onchange="filter()">
                                    <option value="">{{ translate('All Brands')}}</option>
                                    @foreach (\App\Models\Brand::all() as $brand)
                                    <option value="{{ $brand->slug }}" @isset($brand_id) @if ($brand_id==$brand->id) selected @endif @endisset>{{ $brand->getTranslation('name') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group w-200px ml-0 ml-xl-3">
                                <label class="mb-0 opacity-50">{{ translate('Sort by')}}</label>
                                <select class="form-control form-control-sm aiz-selectpicker" name="sort_by" onchange="filter()">
                                    <option value="newest" @isset($sort_by) @if ($sort_by=='newest' ) selected @endif @endisset>{{ translate('Newest')}}</option>
                                    <option value="oldest" @isset($sort_by) @if ($sort_by=='oldest' ) selected @endif @endisset>{{ translate('Oldest')}}</option>
                                    <option value="price-asc" @isset($sort_by) @if ($sort_by=='price-asc' ) selected @endif @endisset>{{ translate('Price low to high')}}</option>
                                    <option value="price-desc" @isset($sort_by) @if ($sort_by=='price-desc' ) selected @endif @endisset>{{ translate('Price high to low')}}</option>
                                </select>
                            </div>
                           
                            <div class="d-xl-none ml-auto ml-xl-3 mr-0 form-group align-self-end">
                                <button type="button" class="btn btn-icon p-0" data-toggle="class-toggle" data-target=".aiz-filter-sidebar">
                                    <i class="la la-filter la-2x"></i>
                                </button>
                            </div>
                        </div>
                        </form>
                    </div>
                                
                        <div class="col-xl-12" id="product_block">
        
                            <input type="hidden" name="min_price" value="">
                            <input type="hidden" name="max_price" value="">
                            @include('frontend.load_product_listing')
                            </div>
                              <!-- Data Loader -->
                    
                            {{-- <div class="aiz-pagination aiz-pagination-center mt-4">
                                {{ $products->links() }}
                            </div> --}}
                            <div class="ajax-load text-center" style="display:none">
                                <p><img width="15%" src="{{ static_asset('assets/img/happy.png') }}"></p>
                            </div>
                       </div>
                
                </div> 
            </div>
       
    </div>
</section>

@endsection

@section('script')
<script type="text/javascript">
    function filter() {
        $('#search-form').submit();
    }

    function rangefilter(arg) {
        $('input[name=min_price]').val(arg[0]);
        $('input[name=max_price]').val(arg[1]);
        filter();
    }
</script>
<script type="text/javascript">
	var page = 1;
	$(window).scroll(function() {
	    if($(window).scrollTop() + $(window).height() >= $(document).height()) {
	        page++;
	        loadMoreData(page)
;
	    }
	});

	function loadMoreData(page){
	  $.ajax(

	        {
	            url: '?page=' + page,
	            type: "get",
	            beforeSend: function()
	            {
	                $('.ajax-load').show();
	            }
	        })
	        .done(function(data)
	        {
	            if(data.html == " "){
	                $('.ajax-load').html("No more records found");
	                return;
	            }
	            $('.ajax-load').hide();
	            $("#product_block").append(data.html);
	        })
	        .fail(function(jqXHR, ajaxOptions, thrownError)
	        {
	              alert('server not responding...');
	        });
	}
</script>
@endsection