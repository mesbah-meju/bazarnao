@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h1 class="mb-0 h6">{{ translate('Edit Group Product') }}</h5>
</div>
<div class="">
	<div class="">
		<form class="form form-horizontal mar-top" action="{{route('group_products.update', $product->id)}}" method="POST" enctype="multipart/form-data" id="choice_form">
			<input name="_method" type="hidden" value="POST">
			<input type="hidden" name="id" value="{{ $product->id }}">
	        <input type="hidden" name="lang" value="{{ $lang }}">
			@csrf
			<div class="card">

				<div class="card-body">
					<div class="form-group row">
	                    <label class="col-lg-3 col-from-label">{{translate('Product Name')}} <i class="las la-language text-danger" title="{{translate('Translatable')}}"></i></label>
	                    <div class="col-lg-8">
	                        <input type="text" class="form-control" name="name" placeholder="{{translate('Product Name')}}" value="{{ $product->getTranslation('name', $lang) }}" required>
	                    </div>
	                </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-from-label" for="products">{{translate('Products')}}</label>
                        <div class="col-lg-8">
                            <select name="products[]" id="products" class="form-control aiz-selectpicker" multiple required data-placeholder="{{ translate('Choose Products') }}" data-live-search="true" data-selected-text-format="count">
                                @foreach(\App\Models\Product::where('is_group_product',0)->orderBy('created_at', 'desc')->get() as $product_table)
                                    @php
                                        $group_product = \App\Models\Group_product::where('product_id', $product_table->id)->where('group_product_id',$product->id)
                                        ->first();
                                    @endphp
                                    <option value="{{$product_table->id}}" <?php if($group_product != null) echo "selected";?> >{{ $product_table->getTranslation('name') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row" id="group_product_table">
                    </div>
	                <div class="form-group row">
						<label class="col-lg-3 col-from-label">{{translate('Minimum Qty')}}</label>
						<div class="col-lg-8">
							<input type="number" lang="en" class="form-control" name="min_qty" value="@if($product->min_qty <= 1){{1}}@else{{$product->min_qty}}@endif" min="1" required>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-lg-3 col-from-label">{{translate('Maximum Order Qty')}}</label>
						<div class="col-lg-8">
							<input type="number" lang="en" class="form-control" name="max_qty" value="{{$product->max_qty}}" min="1">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-3 col-form-label">{{ translate('Maximum App Order Qty') }}</label>
						<div class="col-md-8">
							<input type="number" lang="en" class="form-control" name="app_max_qty" value="{{$product->app_max_qty}}" min="1">
						</div>
                    </div>

					@php
					    $pos_addon = \App\Models\Addon::where('unique_identifier', 'pos_system')->first();
					@endphp
					@if ($pos_addon != null && $pos_addon->activated == 1)
						<div class="form-group row">
							<label class="col-lg-3 col-from-label">{{translate('Barcode')}}</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" name="barcode" placeholder="{{ translate('Barcode') }}" value="{{ $product->barcode }}">
							</div>
						</div>
					@endif


					@php
					    $refund_request_addon = \App\Models\Addon::where('unique_identifier', 'refund_request')->first();
					@endphp
					@if ($refund_request_addon != null && $refund_request_addon->activated == 1)
						<div class="form-group row">
							<label class="col-lg-3 col-from-label">{{translate('Refundable')}}</label>
							<div class="col-lg-8">
								<label class="aiz-switch aiz-switch-success mb-0" style="margin-top:5px;">
									<input type="checkbox" name="refundable" @if ($product->refundable == 1) checked @endif>
		                            <span class="slider round"></span></label>
								</label>
							</div>
						</div>
					@endif
				</div>
			</div>
			<div class="card">
				<div class="card-header">
					<h5 class="mb-0 h6">{{translate('Product Images')}}</h5>
				</div>
				<div class="card-body">

	                <div class="form-group row">
	                    <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Gallery Images')}}</label>
	                    <div class="col-md-8">
	                        <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
	                            <div class="input-group-prepend">
	                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
	                            </div>
	                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
	                            <input type="hidden" name="photos" value="{{ $product->photos }}" class="selected-files">
	                        </div>
	                        <div class="file-preview box sm">
	                        </div>
	                    </div>
	                </div>
	                <div class="form-group row">
	                    <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Thumbnail Image')}} <small>(290x300)</small></label>
	                    <div class="col-md-8">
	                        <div class="input-group" data-toggle="aizuploader" data-type="image">
	                            <div class="input-group-prepend">
	                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
	                            </div>
	                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
	                            <input type="hidden" name="thumbnail_img" value="{{ $product->thumbnail_img }}" class="selected-files">
	                        </div>
	                        <div class="file-preview box sm">
	                        </div>
	                    </div>
	                </div>
				</div>
			</div>

			<div class="card">
				<div class="card-header">
					<h5 class="mb-0 h6">{{translate('Product Description')}}</h5>
				</div>
				<div class="card-body">
					<div class="form-group row">
	                    <label class="col-lg-3 col-from-label">{{translate('Description')}} <i class="las la-language text-danger" title="{{translate('Translatable')}}"></i></label>
	                    <div class="col-lg-9">
	                        <textarea class="aiz-text-editor" name="description">{{ $product->getTranslation('description', $lang) }}</textarea>
	                    </div>
	                </div>
				</div>
			</div>

			<div class="card">
				<div class="card-header">
					<h5 class="mb-0 h6">{{translate('Product Notice')}}</h5>
				</div>
				<div class="card-body">
					<div class="form-group row">
						<label class="col-md-3 col-from-label">{{translate('Notice')}}</label>
						<div class="col-md-8">
							<textarea class="aiz-text-editor" name="notice">{{ $product->getTranslation('notice', $lang) }}</textarea>
						</div>
					</div>
				</div>
			</div>

			@if (\App\Models\BusinessSetting::where('type', 'shipping_type')->first()->value == 'product_wise_shipping')
	            <div class="card">
					<div class="card-header">
						<h5 class="mb-0 h6">{{translate('Product Shipping Cost')}}</h5>
					</div>
					<div class="card-body">
	                    <div class="form-group row">
							<div class="col-lg-3">
								<div class="card-heading">
									<h5 class="mb-0 h6">{{translate('Free Shipping')}}</h5>
								</div>
							</div>
							<div class="col-lg-9">
								<div class="form-group row">
									<label class="col-lg-3 col-from-label">{{translate('Status')}}</label>
									<div class="col-lg-8">
										<label class="aiz-switch aiz-switch-success mb-0">
											<input type="radio" name="shipping_type" value="free" @if($product->shipping_type == 'free') checked @endif>
											<span></span>
										</label>
									</div>
								</div>
							</div>
						</div>

	                    <div class="form-group row">
							<div class="col-lg-3">
								<div class="card-heading">
									<h5 class="mb-0 h6">{{translate('Flat Rate')}}</h5>
								</div>
							</div>
							<div class="col-lg-9">
								<div class="form-group row">
									<label class="col-lg-3 col-from-label">{{translate('Status')}}</label>
									<div class="col-lg-8">
										<label class="aiz-switch aiz-switch-success mb-0">
											<input type="radio" name="shipping_type" value="flat_rate" @if($product->shipping_type == 'flat_rate') checked @endif>
											<span></span>
										</label>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-lg-3 col-from-label">{{translate('Shipping cost')}}</label>
									<div class="col-lg-8">
										<input type="number" lang="en" min="0" value="{{ $product->shipping_cost }}" step="0.01" placeholder="{{ translate('Shipping cost') }}" name="flat_shipping_cost" class="form-control" required>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
	        @endif

			<div class="mb-3 text-right">
				<button type="submit" name="button" class="btn btn-info">{{ translate('Update Product') }}</button>
			</div>
		</form>
	</div>
</div>

@endsection

@section('script')

<script type="text/javascript">

        $(document).ready(function(){
            get_flash_deal_discount();
            $('#products').on('change', function(){
                get_flash_deal_discount();
            });

            function get_flash_deal_discount(){
                var product_ids = $('#products').val();
                if(product_ids.length > 0){
                    $.post('{{ route('group_products.edit_list') }}', {_token:'{{ csrf_token() }}', product_ids:product_ids, group_product_id:{{ $product->id }}}, function(data){
                        $('#group_product_table').html(data);
                        AIZ.plugins.bootstrapSelect();
                        AIZ.plugins.fooTable();
                    });
                }
                else{
                    $('#group_product_table').html(null);
                }
            }
        });

	

</script>

@endsection
