@extends('backend.layouts.staff')

@section('content')
@include('backend.staff_panel.sales_executive_nav')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Product wise sales report')}}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form id="prowasales" action="{{ route('staff_product_sales_report') }}" method="get">
                    <div class="form-group row">

                        <div class="col-md-3">
                            <label class="col-form-label">{{translate('Sort by Category')}} :</label>
                            <select id="demo-ease" class="aiz-selectpicker" name="category_id">
                                <option value=''>All</option>
                                @foreach (\App\Models\Category::all() as $key => $category)
                                <option @php if($sort_by==$category->id)
                                    echo 'selected';
                                    @endphp
                                    value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label">{{translate('Sort by Product')}} :</label>
                            <select id="demo-ease" class="aiz-selectpicker select2" name="product_id">
                                <option value=''>All</option>
                                @foreach (DB::table('products')->select('id','name')->get(); as $key => $prod)
                                <option @php if($pro_sort_by==$prod->id)
                                    echo 'selected';
                                    @endphp
                                    value="{{ $prod->id }}">{{ $prod->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Date Range :</label>
                            <div class="col-md-12">
                                <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                            </div>
                            <div class="col-md-12">
                                <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <br>
                            <button class="btn btn-sm btn-primary" onclick="submitForm ('{{ route('staff_product_sales_report') }}')">{{ translate('Filter') }}</button>
                       
                            <button class="btn btn-sm btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                            {{-- <button class="btn btn-sm btn-info" onclick="submitForm('{{route('product_sales_export')}}')">Excel</button> --}}
                        </div>
                    </div>
                </form>

                <div class="printArea">
                <style>
th{text-align:center;}
</style>
                    <h3 style="text-align:center;">{{translate('Product wise sales report')}}</h3>
                    <table class="table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width:5%">SL</th>
                                <th style="width:30%">{{ translate('Product Name') }}</th>
                                <th style="width:20%">{{ translate('Category') }}</th>
                                <th style="width:10%">{{ translate('Qty') }}</th>
                                <th style="width:10%">{{ translate('Unit Price') }}</th>
                                <th style="width:10%">{{ translate('Amount') }}</th>
                                <th style="width:10%">{{ translate('Num of Sales') }}</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                        @php $total = 0;$qty = 1; @endphp
                            @foreach ($products as $key => $product)
                            @php $total = $total+($product->price); @endphp
                        
                        <?php if(!empty($product->quantity)){
                        $qty = $product->quantity;
                        }else{
                        $qty = 1;
                        }
                        ?>                   
                            <tr>
                                <td>{{ ($key+1)}}</td>
                                <td>{{ $product->getTranslation('product_name') }}</td>
                                <td>{{ $product->getTranslation('category_name') }}</td>
                                <td style="text-align:right;">{{ $product->getTranslation('quantity') }}</td>
                                <td style="text-align:right;">{{ single_price($product->getTranslation('price')/ $qty) }}</td>
                                <td style="text-align:right;">{{ single_price($product->price) }}</td>
                                <td style="text-align:center;">{{ $product->num_of_sale }}</td>
                            </tr>
                            @endforeach
                            <tr>
                    <td style="text-align:right;" colspan="5"><b>Total</b></td>
                    <td style="text-align:right;"><b>{{single_price($total)}}</b></td>
                </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
 function submitForm(url){
    $('#prowasales').attr('action',url);
    $('#prowasales').submit();
 }
</script>

@endsection