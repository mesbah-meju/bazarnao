@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{translate('Daily Sales Profit report')}}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form id="prowasales" action="{{ route('daily_sale_profit_report.index') }}" method="get">
                    <!-- Filter Section -->
                    <div class="border rounded p-3 mb-3" style="background-color: #f8f9fa;">
                        <div class="row mb-3">
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label class="form-label fw-bold">{{translate('Category')}}</label>
                                <select id="category_select" class="aiz-selectpicker select2 form-control" name="category_id" data-live-search="true">
                                    <option value=''>{{translate('All Categories')}}</option>
                                    @foreach (\App\Models\Category::all() as $key => $category)
                                    <option @if(request('category_id') == $category->id) selected @endif
                                        value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label class="form-label fw-bold">{{translate('Warehouse')}}</label>
                                <select id="warehouse_select" class="aiz-selectpicker select2 form-control" name="warehouse" data-live-search="true">
                                    <option value=''>{{translate('All Warehouses')}}</option>
                                    @foreach (\App\Models\Warehouse::all() as $key => $warehous)
                                    <option @if(request('warehouse') == $warehous->id) selected @endif
                                        value="{{ $warehous->id }}">{{ $warehous->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-4 col-md-6 mb-3">
                                <label class="form-label fw-bold">{{translate('Product')}}</label>
                                <select id="product_select" class="aiz-selectpicker select2 form-control" name="product_id" data-live-search="true">
                                    <option value=''>{{translate('All Products')}}</option>
                                    @foreach (DB::table('products')->select('id','name')->get() as $key => $prod)
                                    <option @if(request('product_id') == $prod->id) selected @endif
                                        value="{{ $prod->id }}">{{ $prod->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label fw-bold">{{translate('Start Date')}}</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', date('Y-m-d', strtotime('-1 month'))) }}">
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label fw-bold">{{translate('End Date')}}</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', date('Y-m-d')) }}">
                            </div>

                            <div class="col-lg-6 col-md-12 d-flex align-items-end mb-3">
                                <div class="btn-group w-100" role="group">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="las la-filter"></i> {{ translate('Filter') }}
                                    </button>
                                    <button class="btn btn-secondary" type="button" onclick="resetForm()">
                                        <i class="las la-redo-alt"></i> {{ translate('Reset') }}
                                    </button>
                                    <button class="btn btn-info" onclick="printDiv()" type="button">
                                        <i class="las la-print"></i> {{ translate('Print') }}
                                    </button>
                                    <button class="btn btn-success" onclick="submitForm('{{ route('product_sales_export') }}')">
                                        <i class="las la-file-excel"></i> {{ translate('Excel') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="printArea">
                    <style>
                        th{text-align:center;}
                    </style>
                    <h3 style="text-align:center;">{{translate('Daily Sales Profit report')}}</h3>
                    <table class="table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width:5%">SL</th>
                                <th style="width:20%">{{ translate('Product Name') }}</th>
                                <th style="width:10%">{{ translate('Category') }}</th>
                                <th style="width:10%">{{ translate('Delivered Date') }}</th>
                                <th style="width:10%">{{ translate('Selling Qty') }}</th>
                                <th style="width:10%">{{ translate('Selling Unit Price') }}</th>
                                <th style="width:10%">{{ translate('Selling Amount') }}</th>
                                <th style="width:10%">{{ translate('Purchase Unit Price') }}</th>
                                <th style="width:10%">{{ translate('Purchase Price') }}</th>
                                <th style="width:15%">{{ translate('Profit') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; $total_profit = 0; $total_purchase = 0; @endphp
                            @foreach ($products as $key => $product)
                                @php 
                                    $qty = $product->quantity ?? 1;
                                    $selling_total = $product->total_order_price; // This is sum(order_details.price)
                                    $selling_unit_price = $qty > 0 ? $selling_total / $qty : 0;
                                    $purchase_unit_price = $product->purchase_unit_price ?? $product->purchase_price ?? 0;
                                    $purchase_total = $product->purchase_total ?? ($purchase_unit_price * $qty);
                                    $profit = $selling_total - $purchase_total;
                                    
                                    $total += $selling_total;
                                    $total_purchase += $purchase_total;
                                    $total_profit += $profit;
                                @endphp
                        
                                <tr>
                                    <td>{{ ($key+1) }}</td>
                                    <td>{{ $product->product_name }}</td>
                                    <td>{{ $product->category_name }}</td>
                                    <td>{{ date('d-m-Y H:i:s', strtotime($product->delivered_date)) }}</td>
                                    <td style="text-align:center;">{{ $product->quantity }}</td>
                                    <td style="text-align:right;">{{ single_price($selling_unit_price) }}</td>
                                    <td style="text-align:right;">{{ single_price($selling_total) }}</td>
                                    <td style="text-align:right;">{{ single_price($purchase_unit_price) }}</td>
                                    <td style="text-align:right;">{{ single_price($purchase_total) }}</td>
                                    <td style="text-align:right;">{{ single_price($profit) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td style="text-align:right;" colspan="6"><b>Total</b></td>
                                <td style="text-align:right;"><b>{{ single_price($total) }}</b></td>
                                <td style="text-align:right;"><b></b></td>
                                <td style="text-align:right;"><b>{{ single_price($total_purchase) }}</b></td>
                                <td style="text-align:right;"><b>{{ single_price($total_profit) }}</b></td>
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
        $('#prowasales').attr('action', url);
        $('#prowasales').submit();
    }

    function printDiv() {
        var printContents = document.querySelector('.printArea').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

    function resetForm() {
        document.getElementById('prowasales').reset();
        // Reset select2 elements
        $('#category_select').val(null).trigger('change');
        $('#warehouse_select').val(null).trigger('change');
        $('#product_select').val(null).trigger('change');
    }
</script>

@endsection
