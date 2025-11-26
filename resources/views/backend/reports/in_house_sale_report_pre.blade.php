@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Product wise sales report')}}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('product_wise_sales_report.index') }}" method="GET">
                    <div class="form-group row">

                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <label>Date Range :</label>
                            <div class="col-md-12">
                                <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                            </div>
                            <div class="col-md-12">
                                <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label>
                            <br>
                            <button class="btn btn-primary" type="submit">{{ translate('Filter') }}</button>
                            <br>
                            <button class="btn btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                        </div>
                    </div>
                </form>

                <div class="printArea">
                    <h3 style="text-align:center;">{{translate('Product wise sales report')}}</h3>
                    <table class="table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width:5%">#</th>
                                <th style="width:30%">{{ translate('Product Name') }}</th>
                                <th style="width:20%">{{ translate('Category') }}</th>
                                <th style="width:10%">{{ translate('Unit Price') }}</th>
                                <th style="width:15%">{{ translate('Purchase Price') }}</th>
                                <th style="width:10%">{{ translate('Num of Sales') }}</th>
                                <th style="width:10%">{{ translate('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $key => $product)
                            <tr>
                                <td>{{ ($key+1)}}</td>
                                <td>{{ $product->getTranslation('name') }}</td>
                                <td>{{ $product->getTranslation('category_name') }}</td>
                                <td>{{ $product->getTranslation('unit_price') }}</td>
                                <td>{{ $product->getTranslation('purchase_price') }}</td>
                                <td>{{ $product->num_of_sale }}</td>
                                <td>{{ $product->sales_value }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection