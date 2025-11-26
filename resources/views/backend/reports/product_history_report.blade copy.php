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
                        {{-- <div class="col-md-3">
                            <label class="col-form-label">{{ __('Sort by Sales Executive') }}:</label>
                            <select id="demo-ease" class="aiz-selectpicker select2" name="user_id" data-live-search="true">
                                <option value=''>{{ __('All') }}</option>
                                @foreach (DB::table('staff')->join('users','users.id','staff.user_id')->where('role_id',9)->get() as $key => $staff)
                                    <option @if($pro_sort_by == $staff->user_id) selected @endif value="{{ $staff->user_id }}">{{ $staff->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        
                        {{-- <div class="col-md-3 ">
                            <label>Date Range :</label>
                            <div class="col-md-12">
                                <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                            </div>
                            <div class="col-md-12">
                                <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                            </div>
                            <div class="clearfix"></div>
                        </div> --}}
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
                    <td style="text-align:right;" colspan="7"><b>Total</b></td>
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

@endsection


@extends('backend.layouts.app')

@section('content')
    <div class="container">
        <h1>{{ translate('Product Sales History') }}</h1>
        <h2>{{ $product_id }}</h2>
        <table>
            <thead>
                <tr>
                    <th>{{ translate('Year') }}</th>
                    <th>{{ translate('Month') }}</th>
                    <th>{{ translate('Total Quantity') }}</th>
                    <th>{{ translate('Average Sales Amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales_history as $sale)
                    <tr>
                        <td>{{ $sale->year }}</td>
                        <td>{{ $sale->month }}</td>
                        <td>{{ $sale->total_quantity }}</td>
                        <td>{{ $sale->average_sales_amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

{{-- <div class="col-lg-2">
                            <div class="form-group mb-0">
                                <label>Month :</label>
                                <input type="month" name="month" id="month" class="form-control" @isset($month) value="{{ $month_year }}-{{ $month }}" @endisset>
                            </div>
                        </div> --}}
                        {{-- <div class="col-lg-2">
                            <div class="form-group mb-0">
                                <label>Year :</label>
                                <select name="year" id="year" class="form-control">
                                    <option value="">Select One</option>
                                    @php
                                    $currentYear = date('Y');
                                    $startYear = $currentYear - 3;
                                    $endYear = $currentYear + 5; 
                                    @endphp
                                    @for ($i = $startYear; $i <= $endYear; $i++)
                                        <option value="{{ $i }}" @if(isset($year) && $year == $i) selected @endif>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div> --}}


<!-- 
    public function product_history_report(Request $request)
    {
        $wearhouse = $request->warehouse;
        $sort_by = null;
        $pro_sort_by = null;
        $start_date = date('Y-m-d', strtotime('-3 years'));
        $end_date = date('Y-m-d');
        $month = null;
        // $start_date = date('Y-m-01 00:00:00');
        // $end_date = date('Y-m-t 23:59:59');
        DB::enableQueryLog();

        $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
            ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('wearhouses', 'wearhouses.id', '=', 'orders.warehouse')
            ->leftJoin('staff', 'products.user_id', '=', 'staff.user_id')
            ->leftJoin('roles', 'staff.id', '=', 'roles.id')
            ->where('num_of_sale', '>', 0)
            ->select(
                'products.name as product_name','products.purchase_price',
                'categories.name as category_name', 'order_details.price as sale_price','order_details.quantity as order_qty',
                'roles.name as role_name', 'orders.delivered_date','orders.code','orders.id as orderId', 'orders.warehouse','wearhouses.name as warehouse_name',
                DB::raw('sum(order_details.price) AS price'),
                DB::raw('sum(quantity) AS quantity'),
                DB::raw('count(product_id) AS num_of_sale')
            )
            ->groupBy('order_details.id')
            ->orderBy('num_of_sale', 'desc');


        // if ($request->has('category_id') && !empty($request->category_id)) {
        //     $sort_by = $request->category_id;
        //     $products = $products->where('category_id', $sort_by);
        // }

        if (!empty($request->product_id)) {
            $pro_sort_by = $request->product_id;
            $products = $products->where('products.id', $pro_sort_by);
        }

        // if (!empty($request->user_id)) {
        //     $pro_sort_by = $request->user_id;
        //     $products = $products->where('products.user_id', $pro_sort_by);
        // }

        if (!empty($wearhouse)) {
            $products = $products->where('orders.warehouse', $wearhouse);
        }

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = strtotime($request->start_date);
            $end_date = strtotime($request->end_date . ' +1 day');
            $products = $products->whereBetween('orders.date', [
                $start_date,
                $end_date
            ]);
        } else {
            $products = $products->whereBetween('orders.date', [
                strtotime($start_date),
                strtotime($end_date)
            ]);
        }

        $products->where('order_details.delivery_status', 'delivered');
        $products->groupBy(DB::raw('YEAR(orders.date)'), DB::raw('MONTH(orders.date)'))
        ->orderBy(DB::raw('YEAR(orders.date)'), 'desc')
        ->orderBy(DB::raw('MONTH(orders.date)'), 'desc');
        $products = $products->get();
        // dd($products);

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        } else {
            $start_date = date('Y-m-01',strtotime('-3 years'));
            $end_date = date('Y-m-t');
        }
        // $end_date = date('Y-m-d', strtotime($end_date . ' -1 day'));
        //$query = DB::getQueryLog();

        return view('backend.reports.product_history_report', compact('products', 'sort_by', 'pro_sort_by', 'start_date', 'end_date', 'wearhouse'));
    }
 -->
 public function product_history_report(Request $request)
    {
        $wearhouse = $request->warehouse;
        $sort_by = null;
        $pro_sort_by = null;
        // $start_date = date('Y-m-d', strtotime('-3 years'));
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        // $date = $request->date;
        // $month = $request->month;
        // $month_year = null;
        // $year = $request->year;
        // $from_year = $request->from_year ? $request->from_year: null;
        // $to_year = $request->to_year ? $request->to_year: null;
    

        DB::enableQueryLog();

        $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
            ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('wearhouses', 'wearhouses.id', '=', 'orders.warehouse')
            ->leftJoin('staff', 'products.user_id', '=', 'staff.user_id')
            ->leftJoin('roles', 'staff.id', '=', 'roles.id')
            ->where('num_of_sale', '>', 0)
            ->select(
                'products.name as product_name','products.purchase_price',
                'categories.name as category_name', 'order_details.price as sale_price','order_details.quantity as order_qty',
                'roles.name as role_name', 'orders.delivered_date','orders.code','orders.id as orderId', 'orders.warehouse','wearhouses.name as warehouse_name',
                DB::raw('sum(order_details.price) AS price'),
                DB::raw('sum(quantity) AS quantity'),
                DB::raw('count(product_id) AS num_of_sale')
            )
            ->groupBy('order_details.id')
            ->orderBy('num_of_sale', 'desc');

        if (!empty($request->product_id)) {
            $pro_sort_by = $request->product_id;
            $products = $products->where('products.id', $pro_sort_by);
        }
        if (!empty($wearhouse)) {
            $products = $products->where('orders.warehouse', $wearhouse);
        }

        // if (!empty($request->month)) {
        //     $month_year = date('Y', strtotime($request->month));
        //     $month = date('m', strtotime($request->month));
        
        //     $start_date = date('Y-m-01', strtotime("$month_year-$month-01"));
        //     $end_date = date('Y-m-t', strtotime("$month_year-$month-01"));
        // }
        // if (!empty($request->year)) {
        //     $year = $request->year;
        //     $start_date = date('Y-m-01', strtotime("$year-01-01"));
        //     $end_date = date('Y-m-t', strtotime("$year-12-31"));
        // }
        
        
        // if (!empty($request->year)) {
        //     $start_date = date('Y-m-01', strtotime("$year-01-01"));
        //     $end_date = date('Y-m-t', strtotime("$year-12-31"));
        // }


        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = strtotime($request->start_date);
            $end_date = strtotime($request->end_date . ' +1 day');
            $products = $products->whereBetween('orders.date', [
                $start_date,
                $end_date
            ]);
        } else {
            $products = $products->whereBetween('orders.date', [
                strtotime($start_date),
                strtotime($end_date)
            ]);
        }

        $products->where('order_details.delivery_status', 'delivered');
        $products->groupBy(DB::raw('YEAR(orders.date)'), DB::raw('MONTH(orders.date)'))
        ->orderBy(DB::raw('YEAR(orders.date)'), 'desc')
        ->orderBy(DB::raw('MONTH(orders.date)'), 'desc');
        $products = $products->get();
        // dd($products);

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        // } 
        // else if(!empty($request->month)) {
        //     $start_date = date('Y-m-01', strtotime($month));
        //     $end_date = date('Y-m-t', strtotime($month));
        }else {
            $start_date = date('Y-m-01',strtotime('-3 years'));
            $end_date = date('Y-m-t');
        }
     
        return view('backend.reports.product_history_report', compact('products', 'sort_by', 'pro_sort_by', 'start_date', 'end_date', 'wearhouse'));
    }