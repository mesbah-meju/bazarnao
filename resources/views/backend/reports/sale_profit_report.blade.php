@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{ translate('Sales Profit Report') }}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form id="prowasales" action="{{ route('sale_profit_report.index') }}" method="get">
                    <div class="form-group row">
                        <div class="col-md-2">
                            <label class="col-form-label">{{ translate('Sort by Category') }} :</label>
                            <select id="category_select" class="aiz-selectpicker select2" name="category_id" data-live-search="true">
                                <option value=''>All</option>
                                @foreach (\App\Models\Category::all() as $key => $category)
                                    <option @if($sort_by == $category->id) selected @endif value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="col-form-label">{{ translate('Sort by Warehouse') }} :</label>
                            <select id="warehouse_select" class="aiz-selectpicker select2" name="warehouse" data-live-search="true">
                                <option value=''>All</option>
                                @foreach (\App\Models\Warehouse::all() as $key => $warehouse)
                                    <option @if($wearhouse == $warehouse->id) selected @endif value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="col-form-label">{{ translate('Sort by Product') }} :</label>
                            <select id="product_select" class="aiz-selectpicker select2" name="product_id[]" data-live-search="true" multiple>
                                @php
                                    // Ensure $pro_sort_by is an array
                                    $pro_sort_by = is_array($pro_sort_by) ? $pro_sort_by : [];
                                @endphp
                                @foreach (DB::table('products')->select('id', 'name')->get() as $key => $prod)
                                    <option @if(in_array($prod->id, $pro_sort_by)) selected @endif value="{{ $prod->id }}">{{ $prod->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range Filter -->
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

                        <div class="col-md-4">
                            <label>&nbsp;</label>
                            <br>
                            <button class="btn btn-sm btn-primary" type="submit">{{ translate('Filter') }}</button>
                            <button class="btn btn-sm btn-secondary" type="button" onclick="resetForm()">{{ translate('Reset') }}</button>
                            <button class="btn btn-sm btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                            {{-- <button class="btn btn-sm btn-success" onclick="submitForm('{{ route('product_sales_export') }}')">Excel</button> --}}

                            {{-- <a href="{{ route('sale_profit_report.index',[
                                'type' => 'excel',
                                'start_date' => request()->input('start_date'),
                                'end_date' => request()->input('end_date'),
                                'wearhouse_id' => request()->input('wearhouse_id'),
                                'product_id' => request()->input('product_id'),
                                'supplier_id' => request()->input('supplier_id'),
                            ]) }}" target="_blank" class="btn btn-primary" type="button">Excel</a> --}}

                            <a href="{{ route('sale_profit_report.index', array_merge(
                            ['type' => 'excel'],
                            request()->query()
                        )) }}" target="_blank" class="btn btn-sm btn-success" type="button">Excel</a>

                        </div>
                    </div>
                </form>

                <div class="printArea">
                <style>
                    th { text-align: center; }
                </style>
                    <h3 style="text-align: center;">{{ translate('Sales Profit Report') }}</h3>
                    <table class="table-bordered" style="width: 100%">
                        <thead>
                            <tr>
                                <th style="width: 5%">SL</th>
                                <th style="width: 30%">{{ translate('Product Name') }}</th>
                                <th style="width: 10%">{{ translate('Category') }}</th>
                                <th style="width: 10%">{{ translate('Selling Qty') }}</th>
                                {{-- <th style="width: 10%">{{ translate('Selling Unit Price') }}</th> --}}
                                <th style="width: 10%">{{ translate('Selling Amount') }}</th>
                                <th style="width: 10%">{{ translate('Purchase Unit Price') }}</th>
                                <th style="width: 10%">{{ translate('Purchase Price') }}</th>
                                <th style="width: 15%">{{ translate('Profit') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $total = 0;
                                $total_qty = 0;
                                $total_profit = 0;
                                $total_purchase = 0;
                                $profit = 0;
                            @endphp
                            @foreach ($products as $key => $product)
                            @php 
                                $total += $product->total_order_price;
                                $total_qty += $product->total_order_qty;
                                $total_purchase += $product->purchase_price * $product->total_order_qty;
                                // $total_profit += $product->profit;
                                $profit = $product->profit_loss;
                            $total_profit += $profit;
                            @endphp                 
                            <tr>
                                <td>{{ ($key+1) }}</td>
                                <td>{{ $product->product_name }}</td>
                                <td>{{ $product->category_name }}</td>
                                <td style="text-align: center;">{{ $product->total_order_qty }}</td>
                                {{-- <td style="text-align: right;">{{ single_price($product->sale_price) }}</td> --}}
                                <td style="text-align: right;">{{ single_price($product->total_order_price) }}</td>
                                <td style="text-align: right;">{{ single_price($product->purchase_price) }}</td>
                                <td style="text-align: right;">{{ single_price($product->purchase_price * $product->total_order_qty) }}</td>
                                <td style="text-align: center;">{{ single_price($product->profit_loss) }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td style="text-align: right;" colspan="4"><b>Total</b></td>
                                <td style="text-align: right;"><b>{{ single_price($total) }}</b></td>
                                <td style="text-align: right;"></td>
                                <td style="text-align: right;"><b>{{ single_price($total_purchase) }}</b></td>
                                <td style="text-align: center;"><b>{{ single_price($total_profit) }}</b></td>
                            </tr>
                        </tbody>
                    </table>
                    <br/>
                    <br/>
                </div>
                {{-- <div class="aiz-pagination">
                    {{ $products->links() }} <!-- Add pagination links here -->
                </div> --}}
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    function resetForm() {
        document.getElementById('prowasales').reset();
    }

    function printDiv() {
        window.print();
    }
</script>
@endsection
