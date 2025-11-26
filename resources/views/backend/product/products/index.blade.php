@extends('backend.layouts.app')

@section('content')

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #eef2f7;
    }

    .aiz-titlebar h1 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #34495e;
    }

    .btn-primary, .btn-secondary {
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: #3498db;
        border: none;
    }

    .btn-primary:hover {
        background-color: #2980b9;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border: none;
        background: #fff;
        padding: 20px;
    }

    .table thead th {
        background-color: #f7f9fb;
        color: #34495e;
        font-weight: 600;
    }

    .table tbody tr:nth-child(even) {
        background-color: #f3f7fa;
    }

    .table tbody tr:hover {
        background-color: #eaf0f5;
    }

    .aiz-switch .slider.round {
        background-color: #e0e0e0;
        transition: all 0.3s ease;
    }

    .aiz-switch input:checked + .slider.round {
        background-color: #3498db;
    }

    .form-control {
        border-radius: 8px;
        padding: 0.5rem 1rem;
    }

    .aiz-pagination .page-item.active .page-link {
        background-color: #3498db;
        border-color: #3498db;
    }
</style>

<div class="aiz-titlebar text-left mt-2 mb-4">
    <h1>{{ translate('Product Management') }}</h1>
</div>

<div class="card shadow-sm border-0">
    <form id="sort_products" action="{{ route('products.all') }}" method="GET">
        <div class="d-flex align-items-center mb-4">
            <h5 class="text-muted mr-auto">{{ translate('All Products') }}</h5>
            <div>
                <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm mr-2">{{ translate('Add New Product') }}</a>
                <a href="{{ route('group_products.create') }}" class="btn btn-success btn-sm">{{ translate('Add Group Product') }}</a>
            </div>
        </div>

        <div class="row gutters-5 mb-3">
            <div class="col-md-3">
                <select class="form-control aiz-selectpicker" name="product_id[]" multiple>
                    <option value="">{{ translate('All Products') }}</option>
                    @foreach (DB::table('products')->select('id', 'name')->get() as $prod)
                        <option @if(in_array($prod->id, (array)$pro_sort_by)) selected @endif value="{{ $prod->id }}">{{ $prod->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control aiz-selectpicker" name="user_id">
                    <option value="">{{ translate('All Sellers') }}</option>
                    @foreach (\App\Models\User::whereIn('user_type', ['admin', 'seller'])->get() as $seller)
                        <option value="{{ $seller->id }}" @if($seller->id == $seller_id) selected @endif>{{ $seller->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control aiz-selectpicker" name="type" id="type">
                    <option value="">{{ translate('Sort By') }}</option>
                    <option value="name,asc" @if(request('type') == 'name,asc') selected @endif>{{ translate('A to Z') }}</option>
                    <option value="name,desc" @if(request('type') == 'name,desc') selected @endif>{{ translate('Z to A') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="{{ translate('Search by name') }}">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-sm">{{ translate('Filter') }}</button>
    </form>

    <div class="table-responsive">
        <table class="table mt-3">
            <thead>
                <tr>
                <th data-breakpoints="lg">#</th>
                    <th width="30%">{{translate('Name')}}</th>
                    @if($type == 'Seller' || $type == 'All')
                        <th data-breakpoints="lg">{{translate('Added By')}}</th>
                    @endif
                    <th>{{translate('Barcode')}}</th>
                    <th>{{translate('Num of Sale')}}</th>
                    <th>{{translate('Total Stock')}}</th>
                    <th>{{translate('Unit Price')}}</th>
                    <th data-breakpoints="lg">{{translate('Todays Deal')}}</th>
                    <th data-breakpoints="lg">{{translate('Rating')}}</th>
                    <th>{{translate('Published')}}</th>
                    <th>{{translate('Featured')}}</th>
                    <th>{{translate('Refundable')}}</th>
                    <th>{{translate('OutofStock')}}</th>
                    <th class="text-right">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $key => $product)
                    <tr>
                        <td>{{ ($key+1) + ($products->currentPage() - 1)*$products->perPage() }}</td>
                        <td>
							<div class="row gutters-5">
								<div class="col-auto">
									<img src="{{ uploaded_asset($product->thumbnail_img)}}" alt="Image" class="size-50px img-fit">
								</div>
								<div class="col">
									<span class="text-muted text-truncate-2">{{ $product->getTranslation('name') }}</span>
								</div>
							</div>
                        </td>
                        @if($type == 'Seller' || $type == 'All')
                            @if(isset($product->user->name))
                            <td>{{ $product->user->name }}</td>
                            @else
                            <td> </td>
                            @endif
                        @endif
                        <td>{{ $product->barcode }}</td>
                        <td>{{ $product->num_of_sale }} {{translate('times')}}</td>
                        <td>
                            @php
                                $qty = 0;
                                if($product->variant_product){
                                    foreach ($product->stocks as $key => $stock) {
                                        $qty += $stock->qty;
                                    }
                                }
                                else{
                                    $qty = $product->current_stock;
                                }
                                echo $qty;
                            @endphp
                        </td>
                        <td>{{ number_format($product->unit_price,2) }}</td>
                        <td>
							<label class="aiz-switch aiz-switch-success mb-0">
                              <input onchange="update_todays_deal(this)" value="{{ $product->id }}" type="checkbox" <?php if($product->todays_deal == 1) echo "checked";?> >
                              <span class="slider round"></span>
							</label>
						</td>
                        <td>{{ $product->rating }}</td>
                        <td>
							<label class="aiz-switch aiz-switch-success mb-0">
                              <input onchange="update_published(this)" value="{{ $product->id }}" type="checkbox" <?php if($product->published == 1) echo "checked";?> >
                              <span class="slider round"></span>
							</label>
						</td>
                      	<td>
							<label class="aiz-switch aiz-switch-success mb-0">
	                            <input onchange="update_featured(this)" value="{{ $product->id }}" type="checkbox" <?php if($product->featured == 1) echo "checked";?> >
	                            <span class="slider round"></span>
							</label>
						</td>
                        <td>
                        <label class="aiz-switch aiz-switch-success mb-0">
	                            <input onchange="update_refundable(this)" value="{{ $product->id }}" type="checkbox" <?php if($product->refundable == 1) echo "checked";?> >
	                            <span class="slider round"></span>
							</label>
						</td>
                        <td>
                        <label class="aiz-switch aiz-switch-success mb-0">
	                            <input onchange="update_outofstock(this)" value="{{ $product->id }}" type="checkbox" <?php if($product->outofstock == 1) echo "checked";?> >
	                            <span class="slider round"></span>
							</label>
						</td>
						<td class="text-right">
                            <a class="btn btn-soft-success btn-icon btn-circle btn-sm"  href="{{ route('product', $product->slug) }}" target="_blank" title="{{ translate('View') }}">
                               <i class="las la-eye"></i>
                           </a>
							@if ($type == 'Seller')
    	                      <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('products.seller.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
    	                          <i class="las la-edit"></i>
    	                      </a>
							@else
                                @if($product->is_group_product)
								<a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('group_products.admin.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Group Product Edit') }}">
								   <i class="las la-edit"></i>
							   </a>
                               @else
                               <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('products.admin.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
								   <i class="las la-edit"></i>
							   </a>
                               @endif
							@endif
							<!-- <a class="btn btn-soft-success btn-icon btn-circle btn-sm" href="{{route('products.duplicate', ['id'=>$product->id, 'type'=>$type]  )}}" title="{{ translate('Duplicate') }}">
							   <i class="las la-copy"></i>
						   </a> -->
                           @if(auth()->user()->user_type == 'admin')
                                @if($product->is_group_product)
                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('group_products.destroy', $product->id)}}" title="{{ translate('Group Product Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                                @else
                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('products.destroy', $product->id)}}" title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                                @endif
                           @endif
                      </td>
                  	</tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $products->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        function update_status(el, route) {
            const status = el.checked ? 1 : 0;
            $.post(route, {_token: '{{ csrf_token() }}', id: el.value, status: status}, function(data) {
                AIZ.plugins.notify(data == 1 ? 'success' : 'danger', '{{ translate('Status updated') }}');
            });
        }

        $(document).ready(function(){
            //$('#container').removeClass('mainnav-lg').addClass('mainnav-sm');
        });

        function update_todays_deal(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.todays_deal') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Todays Deal updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_published(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.published') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Published products updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_featured(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.featured') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Featured products updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_refundable(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.refundable') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Refundable updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_outofstock(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.outofstock') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Out Of Stock updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }


        function sort_products(el){
            $('#sort_products').submit();
        }
    </script>
@endsection
