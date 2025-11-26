@extends('backend.layouts.app')

@section('content')
<style>
    table th {
        padding: 0;
    }
</style>
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Monthly Product Stock Ledger Report')}}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <!--card body-->
            <div class="card-body">
                <form id="culexpo" action="" method="GET">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="form-label">{{translate('Sort by Warehouse')}} :</label>
                            <select id="demo-ease" class="aiz-selectpicker form-control select2" name="warehouse_id" data-live-search="true">
                                @foreach (\App\Models\Warehouse::all() as $key => $row)
                                <option @php if($warehouse_id==$row->id) {echo 'selected'; } @endphp value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{translate('Sort by Category')}} :</label>
                            <select id="demo-ease" class="aiz-selectpicker form-control select2" name="category_id" onchange="getProducts(this.value)" data-live-search="true">
                                <option value=''>All Categories</option>
                                @foreach (\App\Models\Category::all() as $key => $category)
                                <option @php if($category_id==$category->id) { echo 'selected'; } @endphp value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">{{translate('Sort by Product')}} :</label>
                            <select class="aiz-selectpicker form-control select2" name="product_id" id="product_id" data-live-search="true">
                                <option value=''>All Products</option>
                                @if(!empty($category_id))
                                @foreach ($products as $key => $product)
                                <option @php if($product_id==$product->id) { echo 'selected'; } @endphp value="{{ $product->id }}">{{ $product->getTranslation('name') }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">From Date :</label>
                            <input type="text" name="from_date" class="form-control aiz-datepicker datepicker" value="<?php if (!empty($from_date)) echo date('m/d/Y', strtotime($from_date)); ?>">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">To Date :</label>
                            <input type="text" name="to_date" class="form-control aiz-datepicker datepicker" value="<?php if (!empty($to_date)) echo date('m/d/Y', strtotime($to_date)); ?>">
                        </div>

                        <div class="col-md-4 mt-4">
                            <button class="btn btn-primary" type="submit" onclick="submitForm(this.value)" value="{{ route('monthly_stock_ledger_report.index') }}">{{ translate('Filter') }}</button>
                            <button class="btn btn-info" type="button" onclick="printDiv()">{{ translate('Print') }}</button>
                            <button class="btn btn-info" type="submit" onclick="submitForm(this.value)" value="{{ route('export_monthly_stock_ledger_report') }}">{{ translate('Excel') }}</button>
                        </div>

                    </div>
                </form>
                <div class="printArea">
                    <style>
                        th {
                            text-align: center;
                        }
                    </style>
                    <h3 style="text-align:center;">{{translate('Monthly Product  stock ledger report')}}</h3>
                    <table class="table-bordered" style="width:100%">
                        <thead>
                            <tr>

                                <th style="width:3%">{{ translate('SL') }}</th>
                                <th style="width:3%">{{ translate('P.ID') }}</th>
                                <th style="width:15%">{{ translate('Product Name') }}</th>
                                <!-- <th style="width:10%">{{ translate('Category Name') }}</th> -->

                                <th style="width:3%">{{ translate('O.S.Qty') }}</th>
                                <th style="width:8%">{{ translate('O.S.Amount') }}</th>

                                <th style="width:3%">{{ translate('Trns.R.Qty') }}</th>
                                <th style="width:8%">{{ translate('Trns.R.Amount') }}</th>

                                <th style="width:3%">{{ translate('P.Qty') }}</th>
                                <th style="width:8%">{{ translate('P.Amount') }}</th>

                                <th style="width:3%">{{ translate('Sa.Qty') }}</th>
                                <th style="width:8%">{{ translate('Sa.Amount') }}</th>

                                <th style="width:3%">{{ translate('Rf.Qty') }}</th>
                                <th style="width:8%">{{ translate('Rf.Amount') }}</th>

                                <th style="width:3%">{{ translate('D.Qty') }}</th>
                                <th style="width:8%">{{ translate('D.Amount') }}</th>

                                <th style="width:3%">{{ translate('Trns.Qty') }}</th>
                                <th style="width:8%">{{ translate('Trns.Amount') }}</th>

                                <th style="width:3%">{{ translate('C.Qty') }}</th>
                                <th style="width:8%">{{ translate('C.Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php

                            $total = 0;
                            $total_opening_stock_qty = 0;
                            $total_opening_stock_amount = 0;
                            $total_purchase_qty =0;
                            $total_purchase_amount = 0;
                            $total_sale_qty =0;
                            $total_sale_amount =0;
                            $total_damage_qty =0;
                            $total_damage_amount =0;
                            $total_refund_qty =0;
                            $total_refund_amount =0;

                            $total_receive_qty=0;
                            $total_receive_amount=0;

                            $total_transfer_qty=0;
                            $total_transfer_amount=0;

                            $total_closing_qty = 0;
                            $total_closing_amount = 0;

                            @endphp

                            @if(!empty($products))
                            @foreach ($products as $key => $product)

                           
                            <tr>
                                <td style="text-align:center;">{{ ($key+1) }}</td>
                                <td>{{ $product->id }}</td>
                                <td><a href="{{ route('product_wise_fifo.report', ['warehouse_id' => $warehouse_id, 'product_id' => $product->id, 'from_date' => date('Y-m-d', strtotime($from_date)), 'to_date' => date('Y-m-d', strtotime($to_date))]) }}" target="_blank">{{ $product->name }}</a></td>


                                <td style="text-align:center;"><?php $total_opening_stock_qty += $product->opening_stock_qty ?>{{ $product->opening_stock_qty ? $product->opening_stock_qty : '0' }}</td>
                                <td style="text-align:right;"><?php $total_opening_stock_amount += $product->opening_stock_amount; ?> {{ single_price($product->opening_stock_amount) }}</td>

                                <td style="text-align:center;"><?php $total_receive_qty += $product->receive_qty; ?>{{ $product->receive_qty ? $product->receive_qty : '0' }}</td>
                                <td style="text-align:right;"> <?php $total_receive_amount += $product->receive_amount; ?>{{ single_price($product->receive_amount) }}</td>

                                <td style="text-align:center;"><?php $total_purchase_qty += $product->purchase_qty; ?>{{ $product->purchase_qty ? $product->purchase_qty : '0' }}</td>
                                <td style="text-align:right;"><?php $total_purchase_amount += $product->purchase_amount; ?>{{ single_price($product->purchase_amount) }}</td>

                                <td style="text-align:center;"><?php $total_sale_qty += $product->sales_qty; ?>{{ $product->sales_qty ? $product->sales_qty : '0' }}</td>
                                <td style="text-align:right;"><?php $total_sale_amount += $product->sales_amount; ?>{{ single_price($product->sales_amount) }}</td>

                                <td style="text-align:center;"><?php $total_refund_qty += $product->refund_qty; ?>{{ $product->refund_qty ? $product->refund_qty : '0' }}</td>
                                <td style="text-align:right;"><?php $total_refund_amount += $product->refund_amount; ?>{{ single_price($product->refund_amount) }}</td>

                                <td style="text-align:center;"><?php $total_damage_qty += $product->damage_qty; ?>{{ $product->damage_qty ? $product->damage_qty : '0' }}</td>
                                <td style="text-align:right;"><?php $total_damage_amount += $product->damage_amount; ?>{{ single_price($product->damage_amount) }}</td>

                                <td style="text-align:center;"> <?php $total_transfer_qty += $product->transfer_qty; ?>{{ $product->transfer_qty ? $product->transfer_qty : '0' }}</td>
                                <td style="text-align:right;"> <?php $total_transfer_amount += $product->transfer_amount; ?>{{ single_price($product->transfer_amount) }}</td>

                                <td style="text-align:center;"><?php $total_closing_qty += $product->closing_stock_qty; ?>{{ $product->closing_stock_qty ? $product->closing_stock_qty : '0' }}</td>
                                <td style="text-align:right;"><?php $total_closing_amount += $product->closing_stock_amount; ?>{{ single_price($product->closing_stock_amount) }}</td>
                            </tr>
                            @endforeach
                            <tr style="font-weight:bold;">
                                <td style="text-align:center;" colspan="3">Total</td>
                                <td style="text-align:center;">{{$total_opening_stock_qty}}</td>
                                <td style="text-align:right;">{{ single_price($total_opening_stock_amount) }}</td>

                                <td style="text-align:center;">{{$total_receive_qty}}</td>
                                <td style="text-align:right;">{{ single_price($total_receive_amount) }}</td>

                                <td style="text-align:center;">{{$total_purchase_qty}}</td>
                                <td style="text-align:right;">{{ single_price($total_purchase_amount) }}</td>

                                <td style="text-align:center;">{{$total_sale_qty}}</td>
                                <td style="text-align:right;">{{ single_price($total_sale_amount)}}</td>

                                <td style="text-align:center;">{{$total_refund_qty}}</td>
                                <td style="text-align:right;">{{ single_price($total_refund_amount)}}</td>

                                <td style="text-align:center;">{{$total_damage_qty}}</td>
                                <td style="text-align:right;">{{ single_price($total_damage_amount)}}</td>

                                <td style="text-align:center;">{{$total_transfer_qty}}</td>
                                <td style="text-align:right;">{{ single_price($total_transfer_amount) }}</td>

                                <td style="text-align:center;">{{$total_closing_qty}}</td>
                                <td style="text-align:right;">{{ single_price($total_closing_amount) }}</td>
                            </tr>
                            @else
                            <tr>
                                <td class="text-center p-3" colspan="17">No Data Found</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function submitForm(url) {
        $('#culexpo').attr('action', url);
        $('#culexpo').submit();
    }
</script>
@endsection

@section('script')
<script type="text/javascript">
    function getProducts(val) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('product.categories')}}",
            type: 'GET',
            data: {
                value: val
            },
            success: function(data) {
                $('#product_id').html('<option value="">All Products</option>');
                $.each(data, function(key, value) {
                    $('#product_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                });
            }
        });
    }
</script>
@endsection