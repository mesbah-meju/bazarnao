@extends('backend.layouts.app')

@section('content')
<style>
    table th{
padding: 0;
    }
</style>
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Product wise stock ledger report')}}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <!--card body-->
            <div class="card-body">
            <form id="culexpo" class="" action="" method="GET">
                    <div class="form-group row">
                        
                        
                        <div class="col-md-4">
                        <label class="col-form-label">{{translate('Sort by warehouse')}} :</label>
                            <select id="demo-ease" class="from-control aiz-selectpicker select2" name="wearhouse_id" data-live-search="true">
                                <option value=''>All</option>
                                @foreach ($wearhouse as $key => $row)
                                <option @php if($sort_by==$row->id)
                                    echo 'selected';
                                    @endphp
                                    value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
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

                        <div class="col-md-4">
                        <label class="col-form-label">{{translate('Sort by Category')}} :</label>
                            <select id="demo-ease" class="from-control aiz-selectpicker" name="category_id">
                                <option value=''>All</option>
                                @foreach (\App\Models\Category::all() as $key => $category)
                                <option @php if($category_id==$category->id)
                                    echo 'selected';
                                    @endphp
                                    value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Date Range :</label>
                            <div class="col-md-12">
                                <input type="date" name="from_date" class="form-control" value="{{$from_date}}">
                            </div>
                            <div class="col-md-12">
                                <input type="date" name="to_date" class="form-control" value="{{$to_date}}">
                            </div>
                            <div class="clearfix"></div>
                        </div>


                        <div class="col-md-4 mt-4">
                            <button class="btn btn-primary" type="submit" onclick="submitForm ('{{ route('wearhouse_wise_stock_ledger_report.index') }}')">{{ translate('Filter') }}</button>
                            <button class="btn btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                            <button class="btn btn-info" onclick="submitForm('{{route('export_wearhouse_wise_stock_ledger_report')}}')">Excel</button>
                        </div>
                    </div>
                </form>
                <div class="printArea">
                <style>
th{text-align:center;}
</style>
                <h3 style="text-align:center;">{{translate('Product wise stock ledger report')}}</h3>
                    <table class="table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width:5%">{{ translate('SL') }}</th>
                                <th style="width:42%">{{ translate('Product Name') }}</th>
                                <th style="width:42%">{{ translate('Category Name') }}</th>

                                <th style="width:8%">{{ translate('O.S.Qty') }}</th>                    
                                <th style="width:10%">{{ translate('O.S.Amount') }}</th>


                                <th style="width:5%">{{ translate('Trns. R.Qty') }}</th>
                                <th style="width:10%">{{ translate('Trns R.Amount') }}</th> 


                                <th style="width:5%">{{ translate('P.Qty') }}</th>
                                <th style="width:10%">{{ translate('P.Amount') }}</th>

                                  

                                <th style="width:5%">{{ translate('Sa.Qty') }}</th>
                                <th style="width:10%">{{ translate('Sa.Amount') }}</th>

                                <th style="width:5%">{{ translate('Damage.Qty') }}</th>
                                <th style="width:10%">{{ translate('Damage.Amount') }}</th>

                                <th style="width:5%">{{ translate('Trns. Qty') }}</th>
                                <th style="width:10%">{{ translate('Trns. Amount') }}</th>    


                                <th style="width:5%">{{ translate('C.Qty') }}</th>
                                <th style="width:10%">{{ translate('C.Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                        @php $total = 0; 
                        $total_opening_stock_qty = 0;
                        $total_opening_stock_amount = 0;
                        $total_purchase_qty =0;
                        $total_purchase_amount = 0;
                        $tota_sale_qty =0;
                        $tota_sale_amount =0;
                        $tota_damage_qty =0;
                        $tota_damage_amount =0;
                        $total_transfer_receive_qty=0;
                        $total_transfer_receive_amount=0;
                        $total_transfer_qty=0;
                        $total_transfer_amount=0;
                        $tota_closing_qty = 0;
                        $tota_closing_amount = 0;
                        @endphp


                            @foreach ($products as $key => $product)
                           
                            @php
                            $qty = 0;
                        
                        	
                        	
                            
                            @endphp
                            @php $total = $total+($qty*$product->purchase_price); @endphp
                            <tr>
                                <td>{{ ($key+1) }}</td>
                                <td>{{ $product->getTranslation('name') }}</td>
                                <td>{{ $product->category_name }}</td>

                                <td> <?php $total_opening_stock_qty+=$product->opening_stock_qty;?> {{ $product->opening_stock_qty }}</td>            
                                <td style="text-align:right;"> <?php $total_opening_stock_amount+= $product->opening_stock_amount; ?>{{ single_price($product->opening_stock_amount) }}</td>


                                <td> <?php $total_transfer_receive_qty+=$product->transfer_receive_qty;?> {{ $product->transfer_receive_qty }}</td>            
                                <td style="text-align:right;"> <?php $total_transfer_receive_amount += $product->transfer_receive_amount; ?>{{ single_price($product->transfer_receive_amount) }}</td>

                                <td style="text-align:right;"><?php $total_purchase_qty+=$product->purchase_qty; ?>{{ $product->purchase_qty }}</td>
                                <td style="text-align:right;"><?php $total_purchase_amount+=$product->purchase_amount; ?>{{ single_price($product->purchase_amount) }}</td>

                                <td style="text-align:right;"><?php $tota_sale_qty+=$product->sale_qty; ?>{{ $product->sale_qty }}</td>
                                <td style="text-align:right;"><?php $tota_sale_amount+= $product->sale_amount; ?>{{ single_price($product->sale_amount) }}</td>

                                <td style="text-align:right;"><?php $tota_damage_qty+=$product->damage_qty; ?>{{ $product->damage_qty }}</td>
                                <td style="text-align:right;"><?php $tota_damage_amount+=$product->damage_amount; ?>{{ single_price($product->damage_amount) }}</td>


                                <td> <?php $total_transfer_qty+=$product->transfer_qty;?> {{ $product->transfer_qty }}</td>            
                                <td style="text-align:right;"> <?php $total_transfer_amount+=$product->transfer_amount; ?>{{ single_price($product->transfer_amount) }}</td>


                                <td style="text-align:right;"><?php $tota_closing_qty+=$product->closing_qty; ?>{{ $product->closing_qty }}</td>
                                <td style="text-align:right;"><?php $tota_closing_amount+=$product->closing_amount; ?>{{ single_price($product->closing_amount) }}</td>
                            </tr>
                            @endforeach
                           <tr style="font-weight:bold;">
                            <td></td>
                            <td></td>
                            <td>Total:</td>
                            <td>{{$total_opening_stock_qty}}</td>
                            <td>TK {{$total_opening_stock_amount}}</td>

                            <td style="text-align:right;">{{$total_transfer_receive_qty}}</td>
                            <td style="text-align:right;">TK {{$total_transfer_receive_amount}}</td>

                            <td style="text-align:right;">{{$total_purchase_qty}}</td>
                            <td style="text-align:right;">TK {{$total_purchase_amount}}</td>

                            <td style="text-align:right;">{{$tota_sale_qty}}</td>
                            <td style="text-align:right;">TK {{$tota_sale_amount}}</td>

                            <td style="text-align:right;">{{$tota_damage_qty}}</td>
                            <td style="text-align:right;">TK {{$tota_damage_amount}}</td>


                            <td style="text-align:right;">{{$total_transfer_qty}}</td>
                            <td style="text-align:right;">TK {{$total_transfer_amount}}</td>

                            <td style="text-align:right;">{{$tota_closing_qty}}</td>
                            <td style="text-align:right;">TK {{$tota_closing_amount}}</td>
                           </tr>
                        </tbody>
                    </table>

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