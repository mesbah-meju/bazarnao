@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Details Sales Report Year & Month Base')}}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form id="prowasales" action="{{ route('detailed_sales_report.index') }}" method="get">
                    <div class="form-group row">
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
                            <label class="col-form-label">{{translate('Sort by Warehouse')}} :</label>
                            <select id="warehouse" class="aiz-selectpicker select2" name="warehouse" data-live-search="true">
                                <option value=''>All</option>
                                @foreach (\App\Models\Warehouse::all() as $key => $warehous)
                                <option @php if($warehouse ==$warehous->id) echo 'selected'; @endphp value="{{ $warehous->id }}">{{ $warehous->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- <div class="col-md-3">
                            <label class="col-form-label">{{translate('Sort by Product')}} :</label>
                            <select id="demo-ease" class="aiz-selectpicker select2" name="product_id" data-live-search="true">
                                <option value=''>All</option>
                                @foreach (DB::table('products')->select('id','name')->get() as $key => $prod)
                                <option @php if($pro_sort_by==$prod->id) echo 'selected'; @endphp value="{{ $prod->id }}">{{ $prod->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div class="col-md-3">
                            <label class="col-form-label">{{ __('Sort by Sales Executive') }}:</label>
                            <select id="demo-ease" class="aiz-selectpicker select2" name="user_id" data-live-search="true">
                                <option value=''>{{ __('All') }}</option>
                                @foreach (DB::table('staff')
                                    ->join('users', 'users.id', '=', 'staff.user_id')
                                    ->select('staff.user_id', 'users.name') 
                                    ->get() as $staff)
                                    <option value="{{ $staff->user_id }}" @if(request('user_id') == $staff->user_id) selected @endif>
                                        {{ $staff->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        

                        <div class="col-md-3">
                            <label class="col-form-label">{{ __('Sort by Sales Type') }}:</label>
                            <select id="demo-ease" class="aiz-selectpicker select2" name="customer_type" data-live-search="true">
                                <option value=''>{{ __('All') }}</option>
                                @foreach (DB::table('customers')->select('customer_type')->distinct()->get() as $salesType)
                                    <option @if($customer_type == $salesType->customer_type) selected @endif value="{{ $salesType->customer_type }}">{{ $salesType->customer_type }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <br>
                            <div class="d-flex">
                                <button class="btn btn-sm btn-primary" onclick="submitForm('{{ route('detailed_sales_report.index') }}')">{{ translate('Filter') }}</button>
                                <button class="btn btn-sm btn-info mx-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                               
                            </div>
                        </div>
                    </div>
                </form>

                <div class="printArea">
                    <style>
                        th { text-align: center; }
                    </style>
                    <h3 style="text-align: center;">{{translate('Details Sales Report Year & Month Base')}}</h3>
                   
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ translate('Sales Type') }}</th>
                                <th>{{ translate('Warehouse') }}</th>
                                <th>{{ translate('Executive') }}</th>
                                <th>{{ translate('Customer Qty') }}</th>
                                <th>{{ translate('Qty Of Orders') }}</th>
                                <th>{{ translate('Total Sales Value') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_customer_qty=0; 
                                $total_qty=0; 
                                $grand_total=0; 
                            @endphp
                            @foreach ($salesTypes as $saleType)
                            <tr>
                                <td>{{ translate($saleType->customer_type?? 'Undefine') }}</td>
                                <td>{{ $saleType->warehouse_name }}</td>
                                <td>{{ $saleType->executive_name ?? '-' }}</td> 
                                <td class="text-right">{{ $saleType->total_customer_type ?? 'Undefine'}}</td>
                                <td class="text-right">{{$saleType->num_of_sale}}</td>
                                <td class="text-right">{{single_price($saleType->total_price)}}</td>
                            </tr>

                            @php
                                $total_customer_qty += $saleType->total_customer_type;
                                $total_qty += $saleType->num_of_sale;
                                $grand_total += $saleType->total_price;
                            @endphp
                            @endforeach

                            <tr>
                                <td colspan="3" class="text-right"><b>Total:</b></td>
                               
                                    <td class="text-right"><b>{{ $total_customer_qty }}</b></td>
                                    <td class="text-right"><b>{{ $total_qty }}</b></td>                          
                                    <td class="text-right"><b>{{ single_price($grand_total, 2) }}</b></td>
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
