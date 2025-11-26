@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Product wise stock report')}}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <!--card body-->
            <div class="card-body">
                <form action="{{ route('wearhouse_wise_stock_report.index') }}" method="GET">
                    <div class="form-group row">
                        
                        <!-- <div class="col-md-4">
                        <label class="col-form-label">{{translate('Sort by Category')}} :</label>
                            <select id="demo-ease" class="from-control aiz-selectpicker" name="category_id">
                                <option value=''>All</option>
                                @foreach (\App\Models\Category::all() as $key => $category)
                                <option @php if($sort_by==$category->id)
                                    echo 'selected';
                                    @endphp
                                    value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                @endforeach
                            </select>
                        </div> -->
                        <div class="col-md-4">
                        <label class="col-form-label">{{translate('Sort by warehouse')}} :</label>
                            <select id="demo-ease" class="from-control aiz-selectpicker select2" name="category_id" data-live-search="true">
                                <option value=''>All</option>
                                @foreach ($wearhouse as $key => $row)
                                <option @php if($sort_by==$row->id)
                                    echo 'selected';
                                    @endphp
                                    value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label">{{translate('Sort by Product')}} :</label>
                            <select id="demo-ease" class="aiz-selectpicker select2" name="product_id" data-live-search="true">
                                <option value=''>All</option>
                                @foreach (DB::table('products')->select('id','name')->get(); as $key => $prod)
                                <option @php if($pro_sort_by==$prod->id)
                                    echo 'selected';
                                    @endphp
                                    value="{{ $prod->id }}">{{ $prod->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mt-4">
                            <button class="btn btn-primary" type="submit">{{ translate('Filter') }}</button>
                            <button class="btn btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                        </div>
                    </div>
                </form>
                <div class="printArea">
                <style>
th{text-align:center;}
</style>
                <h3 style="text-align:center;">{{translate('Product wise stock report')}}</h3>
                    <table class="table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width:5%">{{ translate('SL') }}</th>
                                <th style="width:42%">{{ translate('Product Name') }}</th>
                                <th style="width:8%">{{ translate('Expiry') }}</th>
                                <!-- <th style="width:30%">{{ translate('Category') }}</th> -->
                                <th style="width:10%">{{ translate('Unit Price') }}</th>
                                <th style="width:10%">{{ translate('Purchase Price') }}</th>
                                <th style="width:10%">{{ translate('Stock') }}</th>
                                <th style="width:15%">{{ translate('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php $total = 0; @endphp
                            @foreach ($products as $key => $product)
                            
                            @php
                            $qty = 0;
                            if ($product->variant_product) {
                            foreach ($product->stocks as $key => $stock) {
                            $qty += $stock->qty;
                            }
                            }
                            else {
                            $qty = $product->qty;
                            }
                            @endphp
                            @php $total = $total+($qty*$product->purchase_price); @endphp
                            <tr>
                                <td>{{ ($key+1) }}</td>
                                <td>{{ $product->getTranslation('name') }}</td>
                                <td>{{ daysDiff ($product->expiry_date )}}</td>
                                <!-- <td>{{ $product->getTranslation('category_name') }}</td> -->
                                <td style="text-align:right;">{{ $product->unit_price }}</td>
                                <td style="text-align:right;">{{ $product->purchase_price }}</td>
                                <td style="text-align:right;">{{ $qty }}</td>
                                <td style="text-align:right;">{{ single_price($qty*$product->purchase_price) }}</td>
                            </tr>
                            @endforeach
                            <tr>
                    <td style="text-align:right;" colspan="6"><b>Total</b></td>
                    <td style="text-align:right;"><b>{{single_price($total)}}</b></td>
                </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>


    @endsection