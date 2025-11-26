@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Product Sales History Report')}}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form id="prowasales" action="{{ route('product_history_compared_report.index') }}" method="get">
                    <div class="form-group row">

                        {{-- <div class="col-md-3">
                            <label class="col-form-label">{{translate('Sort by Category')}} :</label>
                            <select id="demo-ease" class="aiz-selectpicker select2" name="category_id" data-live-search="true">
                                <option value=''>All</option>
                                @foreach (\App\Models\Category::all() as $key => $category)
                                <option @php if($sort_by==$category->id)
                                    echo 'selected';
                                    @endphp
                                    value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                @endforeach
                            </select>
                        </div> --}}

                        <div class="form-group mb-0">
                            <label>Date Range :</label>
                            <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                            <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
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
                       
                        <div class="col-md-3">
                            
                            <label>&nbsp;</label>
                            <br>
                            <div class="d-flex">
                            <button class="btn btn-sm btn-primary" onclick="submitForm ('{{ route('product_history_compared_report.index') }}')">{{ translate('Filter') }}</button>
                            <br>
                            <button class="btn btn-sm btn-info mx-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                            {{-- <button class="btn btn-sm btn-success" onclick="submitForm('{{route('product_sales_export')}}')">Excel</button> --}}
                            </div>
                        </div>
                    </div>
                </form>
                <button class="btn btn-sm btn-info mx-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>

                <div class="printArea">
                <style>
                    th{text-align:center;}
                </style>
                  
                  <div class="container">
                    <h2>Last 3 Years of Months</h2>
                    
                <h6>Product's Name: <span> {{ $products->isNotEmpty() ? $products[0]->product_name : "Undefined/Empty" }} </span></h6>
                
                
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th rowspan="2">Month</th>
                                @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                                    <th colspan="3">{{ $year }}</th>
                                @endfor
                            </tr>
                            <tr>
                                @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                                    <th>Qty</th>
                                    <th>Average Sale</th>
                                    <th>Amount</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($months as $monthData)
                            <tr>
                                <td>{{ $monthData['name'] }}</td>
                                @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                                    <td>{{ $monthData[$year]['qty'] }}</td>
                                    <td>{{ number_format($monthData[$year]['average_sale'], 2, '.', ',') }}</td>
                                    <td>{{ $monthData[$year]['amount'] }}</td>
                                @endfor
                            </tr>
                            @endforeach
                            <tr>
                                <td style="text-align:right;" colspan="1"><b>Total</b></td>
                                @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                                    <td style="text-align:right;"><b>{{ $totals[$year]['qty'] }}</b></td>
                                    <td style="text-align:right;"><b>-</b></td> 
                                    <td style="text-align:right;"><b>{{ single_price($totals[$year]['amount']) }}</b></td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>
                @endsection