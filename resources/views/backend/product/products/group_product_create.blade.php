@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Add New Group Product')}}</h5>
</div>
<div class="">
	<div class="">
		<form class="form form-horizontal mar-top" action="{{route('group_products.store')}}" method="POST" enctype="multipart/form-data" id="choice_form">
			@csrf
			<input type="hidden" name="added_by" value="admin">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Product Information') }}</h5>
                </div>
                <div class="card-body">

					<div class="form-group row">
						<label class="col-md-3 col-form-label">{{ translate('Name') }} <span class="text-danger">*</span></label>
						<div class="col-md-8">
							<input type="text" lang="en" class="form-control" name="name" id="name" required>
						</div>
					</div>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label" for="products">{{ translate('Products') }}</label>
                        <div class="col-md-8">
							<select name="products[]" id="products" class="form-control aiz-selectpicker" multiple required data-placeholder="{{ translate('Choose Products') }}" data-live-search="true" data-selected-text-format="count">
								@foreach(\App\Models\Product::where('is_group_product', 0)->orWhereNull('is_group_product')->orderBy('name', 'asc')->get() as $product)
									<option value="{{ $product->id }}">{{ $product->getTranslation('name') }}</option>
								@endforeach
							</select>
						</div>
						
                    </div>

                    <div class="form-group row" id="group_product_table">
                    </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Minimum Qty') }} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="number" lang="en" class="form-control" name="min_qty" value="1" min="1" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Maximum Order Qty') }}</label>
                            <div class="col-md-8">
                                <input type="number" lang="en" class="form-control" name="max_qty" value="1" min="1">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Maximum App Order Qty') }}</label>
                            <div class="col-md-8">
                                <input type="number" lang="en" class="form-control" name="app_max_qty" value="1" min="1">
                            </div>
                        </div>

						@php
							$pos_addon = \App\Models\Addon::where('unique_identifier', 'pos_system')->first();
						@endphp
						@if ($pos_addon != null && $pos_addon->activated == 1)
							<div class="form-group row">
								<label class="col-md-3 col-from-label">{{translate('Barcode')}}</label>
								<div class="col-md-8">
									<input type="text" class="form-control" name="barcode" placeholder="{{ translate('Barcode') }}">
								</div>
							</div>
						@endif

                        @php
                            $refund_request_addon = \App\Models\Addon::where('unique_identifier', 'refund_request')->first();
                        @endphp
                        @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{{ translate('Refundable') }}</label>
                                <div class="col-md-8">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" name="refundable" checked>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

			<div class="card">
				<div class="card-header">
					<h5 class="mb-0 h6">{{translate('Product Images')}}</h5>
				</div>
				<div class="card-body">
	        <div class="form-group row">
	            <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Gallery Images')}} <small>(600x600)</small></label>
	            <div class="col-md-8">
	                <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
	                    <div class="input-group-prepend">
	                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
	                    </div>
	                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
	                    <input type="hidden" name="photos" class="selected-files">
	                </div>
	                <div class="file-preview box sm">
	                </div>
	                <small class="text-muted">{{translate('These images are visible in product details page gallery. Use 600x600 sizes images.')}}</small>
	            </div>
	        </div>
	        <div class="form-group row">
	            <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Thumbnail Image')}} <small>(300x300)</small></label>
	            <div class="col-md-8">
	                <div class="input-group" data-toggle="aizuploader" data-type="image">
	                    <div class="input-group-prepend">
	                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
	                    </div>
	                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
	                    <input type="hidden" name="thumbnail_img" class="selected-files">
	                </div>
	                <div class="file-preview box sm">
	                </div>
	                <small class="text-muted">{{translate('This image is visible in all product box. Use 300x300 sizes image. Keep some blank space around main object of your image as we had to crop some edge in different devices to make it responsive.')}}</small>
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
						<label class="col-md-3 col-from-label">{{translate('Description')}}</label>
						<div class="col-md-8">
							<textarea class="aiz-text-editor" name="description"></textarea>
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
							<textarea class="aiz-text-editor" name="notice"></textarea>
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
							<div class="col-md-3">
								<h5 class="mb-0 h6">{{translate('Free Shipping')}}</h5>
							</div>
							<div class="col-md-9">
								<div class="form-group row">
									<label class="col-md-3 col-from-label">{{translate('Status')}}</label>
									<div class="col-md-8">
										<label class="aiz-switch aiz-switch-success mb-0">
											<input type="radio" name="shipping_type" value="free" checked>
											<span></span>
										</label>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-3">
								<h5 class="mb-0 h6">{{translate('Flat Rate')}}</h5>
							</div>
							<div class="col-md-9">
								<div class="form-group row">
									<label class="col-md-3 col-from-label">{{translate('Status')}}</label>
									<div class="col-md-8">
										<label class="aiz-switch aiz-switch-success mb-0">
											<input type="radio" name="shipping_type" value="flat_rate" checked>
											<span></span>
										</label>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-md-3 col-from-label">{{translate('Shipping cost')}}</label>
									<div class="col-md-8">
										<input type="number" lang="en" min="0" value="0" step="0.01" placeholder="{{ translate('Shipping cost') }}" name="flat_shipping_cost" class="form-control" required>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
            @endif


			<div class="mb-3 text-right">
				<button type="submit" name="button" class="btn btn-primary">{{ translate('Save Product') }}</button>
			</div>
		</form>
	</div>
</div>



@endsection

@section('script')


<script type="text/javascript">

    $(document).ready(function(){
            $('#products').on('change', function(){
                var product_ids = $('#products').val();
                if(product_ids.length > 0){
                    $.post('{{ route('group_products.list') }}', {_token:'{{ csrf_token() }}', product_ids:product_ids}, function(data){
                        $('#group_product_table').html(data);
                        AIZ.plugins.bootstrapSelect();
                        AIZ.plugins.fooTable();
                    });
                }
                else{
                    $('#group_product_table').html(null);
                }
            });
        });
</script>

@endsection
