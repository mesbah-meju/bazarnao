@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{translate('Product Sales Yearly History Report')}}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form id="prowasales" action="{{ route('product_history_yearly_report.index') }}" method="get">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label>From Date:</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $start_date }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label>To Date:</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $end_date }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="col-form-label">{{translate('Sort by Product')}} :</label>
                            <select id="product_select" class="aiz-selectpicker select2" name="product_id[]" data-live-search="true"  multiple>
                                {{-- <option value=''>All</option> --}}
                                @foreach (DB::table('products')->select('id', 'name')->get() as $key => $prod)
                                <option @if(in_array($prod->id, $pro_sort_by ?? [])) selected @endif value="{{ $prod->id }}">{{ $prod->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        
                        <div class="col-md-3">
                            <label class="col-form-label">{{translate('Sort by Warehouse')}} :</label>
                            <select id="warehouse_select" class="aiz-selectpicker select2" name="warehouse" data-live-search="true">
                                <option value=''>All</option>
                                @foreach (\App\Models\Warehouse::all() as $key => $warehous)
                                <option @if($warehouse == $warehous->id) selected @endif value="{{ $warehous->id }}">{{ $warehous->name }}</option>
                                @endforeach
                            </select>
                        </div>
                       
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <br>
                            <div class="d-flex">
                                <button class="btn btn-sm btn-primary" onclick="submitForm('{{ route('product_history_yearly_report.index') }}')">{{ translate('Filter') }}</button>
                                <br>
                                <button class="btn btn-sm btn-secondary mx-1" type="button" onclick="resetForm()">{{ translate('Reset') }}</button>
                                <button class="btn btn-sm btn-info mx-1" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                                <button class="btn btn-sm btn-success" onclick="submitForm('{{route('product_sales_export')}}')">{{ translate('Excel') }}</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="printArea">
                    <style>
                        th { text-align: center; }
                    </style>
                    @if($filterApplied)
                        @if(count($products) > 0)
                            <h3 style="text-align: center;">{{translate('Product Sales Yearly History Report')}} <span style="font-size: 16px">({{ $start_date }} to {{ $end_date }})</span></h3>
                            <table class="table-bordered" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">SL</th>
                                        <th style="width: 20%">{{ translate('Product Name') }}</th>
                                        <th style="width: 10%">{{ translate('Category') }}</th>
                                        <th style="width: 10%">{{ translate('Year') }}</th>
                                        <th style="width: 5%">{{ translate('Num of Sales') }}</th>
                                        <th style="width: 10%">{{ translate('Amount') }}</th>
                                        <th style="width: 10%">{{ translate('Average Amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php 
                                        $total = 0;
                                        $total_qty = 0;
                                    @endphp
                                    @foreach ($products as $key => $product)
                                    @php 
                                        $total_qty += $product->total_order_qty;
                                        $total += $product->total_order_price;
                                    @endphp
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            @if($product->delivered_date)
                                                @php
                                                    $year = date("Y", strtotime($product->delivered_date));
                                                    $start_date = date("Y-01-01", strtotime($product->delivered_date));
                                                    $end_date = date("Y-12-t", strtotime($product->delivered_date));
                                                @endphp
                                                <a href="{{ route('product_history_report.index',
                                                 ['productId' => $product->productId, 
                                                 'warehouse' => $warehouse, 
                                                 'start_date' => $start_date, 
                                                 'end_date' => $end_date]) }}" target="_blank">
                                                    {{ $product->product_name }}
                                                </a>
                                            @else
                                                {{ $product->product_name }}
                                            @endif
                                        </td>
                                        
                                        <td>{{ $product->category_name }}</td>
                                      
                                        <td>{{ $product->delivered_date ? date("Y", strtotime($product->delivered_date)) : 'undefined' }}</td>
                                                                
                                        <td style="text-align: center;">{{ $product->total_order_qty }}</td>
                                        <td style="text-align: right;">{{ single_price($product->total_order_price) }}</td>
                                        <td style="text-align: right;">
                                            {{ $product->total_order_qty > 0 ? single_price($product->total_order_price / $product->total_order_qty) : single_price(0) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td style="text-align: right;" colspan="4"><b>Total</b></td>
                                        <td style="text-align: center;" colspan="1"><b>{{ $total_qty }}</b></td>
                                        <td style="text-align: right;"><b>{{ single_price($total) }}</b></td> 
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <h3 style="text-align: center;">There are no sales records for the selected filters.</h3>
                        @endif
                    @else
                        <h3 style="text-align: center;">Please select a product to view the report.</h3>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function submitForm(url) {
    $('#prowasales').attr('action', url);
    $('#prowasales').submit();
}

function resetForm() {
    document.getElementById('prowasales').reset();
    // Reset select2 elements
    $('#product_select').val(null).trigger('change');
}

function printDiv() {
    // Implementation of print function
}
</script>

@endsection
