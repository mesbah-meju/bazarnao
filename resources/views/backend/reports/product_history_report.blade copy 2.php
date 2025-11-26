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
                <form id="prowasales" action="{{ route('product_history_report.index') }}" method="get">
                    <div class="form-group row">

                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label>From Date:</label>
                                <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label>To Date:</label>
                                <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                            </div>
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
                            <label class="col-form-label">{{translate('Sort by Warehouse')}} :</label>
                            <select id="warehouse" class="aiz-selectpicker select2" name="warehouse" data-live-search="true">
                                <option value=''>All</option>
                                @foreach (\App\Models\Warehouse::all() as $key => $warehous)
                                <option @php if($wearhouse ==$warehous->id)
                                    echo 'selected';
                                    @endphp
                                    value="{{ $warehous->id }}">{{ $warehous->name}}</option>
                                @endforeach
                            </select>
                        </div>
                       
                        <div class="col-md-3">
                            
                            <label>&nbsp;</label>
                            <br>
                            <div class="d-flex">
                            <button class="btn btn-sm btn-primary" onclick="submitForm ('{{ route('product_history_report.index') }}')">{{ translate('Filter') }}</button>
                            <br>
                            <button class="btn btn-sm btn-info mx-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                            <button class="btn btn-sm btn-success" onclick="submitForm('{{route('product_sales_export')}}')">Excel</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="printArea">
                <style>
                    th{text-align:center;}
                </style>
                    <h3 style="text-align:center;">{{translate('Product Sales History Report')}}</h3>
                    <table class="table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width:5%">SL</th>
                                <th style="width:20%">{{ translate('Product Name') }}</th>
                                <th style="width:10%">{{ translate('Category') }}</th>
                                <th style="width:10%">{{ translate('Warehouse') }}</th>
                                <th style="width:10%">{{ translate('Delivery Date') }}</th>
                                <th style="width:10%">{{ translate('Month-year') }}</th>
                                <th style="width:10%">{{ translate('Order Code') }}</th>
                                {{-- <th style="width:5%">{{ translate('Qty') }}</th> --}}
                                {{-- <th style="width:10%">{{ translate('Purchase Price') }}</th> --}}
                                <th style="width:10%">{{ translate('Unit Price') }}</th>
                                <th style="width:5%">{{ translate('Num of Sales') }}</th>
                                <th style="width:10%">{{ translate('Amount') }}</th>
                                {{-- <th style="width:15%">{{ translate('Profit') }}</th> --}}
                                
                            </tr>
                        </thead>
                        <tbody>
                        @php 
                            $total = 0;
                            $qty = 1;
                            $order_qty=1;
                            $total_profit=0;
                            $total_qty=0 
                         @endphp
                            @foreach ($products as $key => $product)
                            @php 
                             $total_qty = $total_qty+($product->order_qty);
                             $total = $total+($product->price);
                             $total_profit= $total_profit + ($product->price - ($product->purchase_price*$product->quantity));
                             @endphp
                        
                        <?php if(!empty($product->quantity)){
                        $qty = $product->quantity;
                        }else{
                        $qty = 1;
                        }
                        ?>                   
                        <?php if(!empty($product->order_qty)){
                        $order_qty = $product->order_qty;
                        }else{
                        $order_qty = 1;
                        }
                        ?>                   
                            <tr>
                                <td>{{ ($key+1)}}</td>
                                <td>{{ $product->getTranslation('product_name') }}</td>
                                <td>{{ $product->getTranslation('category_name') }}</td>
                                <td>{{ $product->getTranslation('warehouse_name') }}</td>
                                <td>{{ $product->delivered_date ? date("Y-m-d", strtotime($product->delivered_date)) : 'undefined' }}</td>
                                <td>{{ $product->delivered_date ? date("F Y", strtotime($product->delivered_date)) : 'undefined' }}</td>
                                <td style="text-align:center;">
                                    <a href="{{route('all_orders.show', encrypt($product->orderId))}}" target="_blank" title="{{ translate('View') }}">{{ $product->code }}</a>
                                </td>
                                
                                {{-- <td style="text-align:right;">{{ $product->getTranslation('quantity') }}</td> --}}
                                {{-- <td style="text-align:right;">{{ single_price($product->purchase_price) }}</td> --}}
                                <td style="text-align:right;">{{ single_price($product->getTranslation('sale_price')/ $product->order_qty) }}</td>
                                <td style="text-align:center;">{{ $product->order_qty }}</td>
                                <td style="text-align:right;">{{ single_price($product->sale_price) }}</td>
                                {{-- <td style="text-align:center;">{{ single_price(($product->price - ($product->purchase_price*$product->quantity))) }}</td> --}}
                            </tr>
                            @endforeach
                            <tr>
                    <td style="text-align:right;" colspan="8"><b>Total</b></td>
                    {{-- <td style="text-align:right;" colspan="1"><b></b></td> --}}
                    <td style="text-align:center;" colspan="1"><b>{{$total_qty}}</b></td>
                    <td style="text-align:right;"><b>{{single_price($total)}}</b></td> 
                    {{-- <td style="text-align:right;" colspan="1"><b></b></td>
                    <td style="text-align:right;"><b>{{single_price($total_profit)}}</b></td> --}}
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

<script>
    document.getElementById('month').addEventListener('input', function() {
        document.getElementById('year').selectedIndex = 0;
        document.getElementById('start_date').value = '';
        document.getElementById('end_date').value = '';
    });

    document.getElementById('year').addEventListener('input', function() {
        document.getElementById('month').value = '';
    });
</script>

@endsection