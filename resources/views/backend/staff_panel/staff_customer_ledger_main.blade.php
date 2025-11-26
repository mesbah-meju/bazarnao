@extends('backend.layouts.staff')

@section('content')
@if(auth()->user()->staff->role->name=='Sales Executive')
    @include('backend.staff_panel.sales_executive_nav')
@elseif(auth()->user()->staff->role->name=='Customer Service Executive')
    @include('backend.staff_panel.customer_service.customer_executive_nav')
@else
    @include('backend.staff_panel.sales_executive_nav')
@endif
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Customer Ledger report')}}</h1>
    </div>
</div>

<div class="col-md-12 mx-auto">
    <div class="card">
        <div class="card-body">
        <form id="culexpo" action="{{ route('staff_customer_ledger') }}" method="get">
                <div class="row">
                    <div class="col-md-3"> 
                        <label class="col-form-label">{{translate('Sort by Warehouse')}} :</label>
                        <select id="warehouse" class="aiz-selectpicker select2" name="warehouse" data-live-search="true">
                            <option value=''>All</option>
                            @foreach (\App\Models\Warehouse::all() as $key => $warehous)
                            <option @php if($wearhouse == $warehous->id)
                                echo 'selected';
                                @endphp
                                value="{{ $warehous->id }}">{{ $warehous->name}}</option>
                            @endforeach
                        </select>
                    </div>
                  <div class="col-md-3">
				  <label>Email or name & Enter :</label>
				  <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type email or name & Enter') }}">
				  </div>
                    <div class="col-md-3">
                        <label>Start Date :</label>
                            <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                            
                    </div>
                    <div class="col-md-3">
                        <label>End Date :</label>
                        <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                            
                    </div>
        
                    <div class="col-md-3">
                    <label style="margin-top:35px;">&nbsp;<br></label>
                    <button class="btn btn-sm btn-primary" onclick="submitForm('{{ route('staff_customer_ledger') }}')">{{ translate('Filter') }}</button>
                        <button class="btn btn-sm btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                        {{-- <button class="btn btn-sm btn-info" onclick="submitForm('{{ route('customer_ledger_export') }}')">Excel</button> --}}

                   
                    </div>
                    <div class="clearfix"></div>
                </div>
            </form>
            <hr>
            <div class="printArea">
            <style>
th{
    text-align:center;
}
</style>
              <div class="col-md-12" style="text-align: center;">
                    <p><b>Customer Ledger Summary</b></p>
                    <p><b> Period : </b> {{date('d-m-Y',strtotime($start_date))}} to {{date('d-m-Y',strtotime($end_date))}}</p>

                </div>
                <table class="table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>{{ translate('Customer ID') }}</th>
                            
                            <th>{{ translate('Customer Name') }}</th>
                            <th>{{ translate('Opening Balance') }}</th>
                            <th>{{ translate('Debit') }}</th>
                            <th>{{ translate('Credit') }}</th>
                            <th>{{ translate('Balance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $debit = 0;
                        $credit = 0;
                        $balance = 0;
                        $opening_balance = 0;
                        @endphp

                        @foreach($customers as $key=>$customer)
                        @php
						if(empty($customer->name)){
							continue;
						}
                        $debit += $customer->debit;
                        $credit += $customer->credit;
                        $balance += $customer->opening_balance+$customer->debit-$customer->credit;
                        $opening_balance += $customer->opening_balance;
                        @endphp
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td style="text-align:center">
                            <a href="{{route('staff_customer_ledger_details')}}?cust_id={{$customer->user_id}}&start_date={{$start_date}}&end_date={{$end_date}}" target="_blank" title="{{ translate('View') }}">{{ $customer->customer_no }}</a>
                            </td>
                            <td style="text-align:left">{{ $customer->name }}</td>
                            <td style="text-align:right">{{ single_price($customer->opening_balance) }}</td>
                            <td style="text-align:right">{{ single_price($customer->debit) }}</td>
                            <td style="text-align:right">{{ single_price($customer->credit) }}</td>
                            <td style="text-align:right;">{{ single_price($customer->opening_balance+$customer->debit-$customer->credit) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <th colspan="3" style="text-align:right">Total</th>
                            <th style="text-align:right">{{single_price($opening_balance,2)}}</th>
                            <th style="text-align:right">{{single_price($debit,2)}}</th>
                            <th style="text-align:right">{{single_price($credit,2)}}</th>
                            <th style="text-align:right;"><b>{{single_price($balance)}}</b></th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
 function submitForm(url){
    $('#culexpo').attr('action',url);
    $('#culexpo').submit();
 }
</script>

@endsection