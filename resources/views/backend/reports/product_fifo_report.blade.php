@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Product Fifo report')}}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form id="prowasales" action="{{ route('product_fifo_report.index') }}" method="get">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="col-form-label">{{translate('Sort by Category')}} :</label>
                            <select id="demo-ease" class="aiz-selectpicker select2" name="category_id" data-live-search="true">
                                <option value=''>All</option>
                                @foreach (\App\Models\Category::all() as $key => $category)
                                <option @php if($sort_by==$category->id) echo 'selected'; @endphp value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="col-form-label">{{translate('Sort by Warehouse')}} :</label>
                            <select id="warehouse" class="aiz-selectpicker select2" name="warehouse" data-live-search="true">
                                <option value=''>All</option>
                                @foreach (\App\Models\Warehouse::all() as $key => $warehous)
                                <option @php if($warehouse==$warehous->id) echo 'selected'; @endphp value="{{ $warehous->id }}">{{ $warehous->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="col-form-label">{{translate('Sort by Product')}} :</label>
                            <select id="demo-ease" class="aiz-selectpicker select2" name="product_id" data-live-search="true">
                                <option value=''>All</option>
                                @foreach (DB::table('products')->select('id','name')->get() as $key => $prod)
                                <option @php if($pro_sort_by==$prod->id) echo 'selected'; @endphp value="{{ $prod->id }}">{{ $prod->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label">{{ __('Sort by Sales Executive') }}:</label>
                            <select id="demo-ease" class="aiz-selectpicker select2" name="user_id" data-live-search="true">
                                <option value=''>{{ __('All') }}</option>
                                @foreach (DB::table('staff')->join('users','users.id','staff.user_id')->where('role_id',9)->get() as $key => $staff)
                                <option @if($pro_sort_by==$staff->user_id) selected @endif value="{{ $staff->user_id }}">{{ $staff->name }}</option>
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
                            <div class="d-flex">
                                <button class="btn btn-sm btn-primary" onclick="submitForm('{{ route('product_fifo_report.index') }}')">{{ translate('Filter') }}</button>
                                <button class="btn btn-sm btn-info mx-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                                <button class="btn btn-sm btn-success" onclick="submitForm('{{route('product_sales_export')}}')">Excel</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="printArea">
                    <style>
                        th,td {
                            text-align: center;
                        }
                    </style>
                    <h3 style="text-align: center;">{{translate('Product Fifo report')}}</h3>
                    <table class="table-bordered" style="width: 100%">
                        <thead>
                            <tr>
                                <th style="width: 5%">SL</th>
                                <th style="width: 30%">{{ translate('Product Name') }}</th>
                                <th style="width: 20%">{{ translate('Category') }}</th>
                                <th style="width: 10%">{{ translate('Qty') }}</th>
                                <th style="width: 10%">{{ translate('Unit Rate') }}</th>
                                <th style="width: 10%">{{ translate('Total Amount') }}</th>
                                <th style="width: 10%">{{ translate('Num of Sales') }}</th>
                                <th style="width: 10%">{{ translate('Profit') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; $qty = 1; $total_profit = 0; @endphp
                            @foreach ($products as $key => $product)
                            @php
                            $total += $product->price;
                            $profit = $product->profit_loss;
                            $total_profit += $profit;
                            $qty = !empty($product->quantity) ? $product->quantity : 1;
                            @endphp
                            <tr>
                                <td>{{ ($key + 1) }}</td>
                                <td>
                                    <a target="_blank" href="{{ route('product_fifo_detail_report.index', [
                                        'category_id' => request()->input('category_id', ''),
                                        'warehouse' => request()->input('warehouse', ''),
                                        'product_id' => $product->product_id,
                                        'user_id' => request()->input('user_id', ''),
                                        'start_date' => request()->input('start_date', ''),
                                        'end_date' => request()->input('end_date', '')
                                    ]) }}">
                                        {{ $product->product_name }}
                                    </a>
                                </td>
                                
                                <td>{{ $product->category_name }}</td>
                                <td style="text-align: center;">{{ $product->quantity }}</td>
                                <td style="text-align: right;">{{ single_price($product->price / $qty) }}</td>
                                <td style="text-align: right;">{{ single_price($product->price) }}</td>
                                <td style="text-align: center;">

                                    <a target="_blank" href="{{route('number_of_invoice',[
                                    'category_id' => request()->input('category_id', ''),
                                    'warehouse' => request()->input('warehouse', ''),
                                    'product_id' => $product->product_id,
                                    'user_id' => request()->input('user_id', ''),
                                    'start_date' => request()->input('start_date', ''),
                                    'end_date' => request()->input('end_date', '')
                                    ])}}">{{ $product->num_of_sale }}</a>
                                </td>

                                <td style="text-align: center;">{{ single_price($profit) }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td style="text-align: right;" colspan="5"><b>Total</b></td>
                                <td style="text-align: right;"><b>{{ single_price($total) }}</b></td>
                                <td style="text-align: right;" colspan="1"><b>Total Profit</b></td>
                                <td style="text-align: right;"><b>{{ single_price($total_profit) }}</b></td>
                            </tr>
                        </tbody>
                    </table>
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
</script>

@endsection