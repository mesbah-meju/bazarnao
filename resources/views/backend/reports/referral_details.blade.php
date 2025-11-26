@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Referral Details report')}}</h1>
    </div>
</div>

<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('referral_report') }}" method="get">
                <div class="row">
                    
                        <button class="btn btn-sm btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
             
                </div>
            <hr>
            <div class="printArea">
            <style>
th{
    text-align:center;
}
</style>
                @if(!empty($cust))
                <div class="col-md-12" style="text-align: center;">
                <p><b>Referral Details</b></p>
                    <p><b>Customer Name : </b> {{$cust->name}}</p>
                    <p><b>Customer ID : </b> {{$cust->customer->customer_id}}</p>

                </div>
                @endif
                <table class="table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ translate('Customer Name') }}</th>
                             <th>{{ translate('Customer ID') }}</th>
                            <th>{{ translate('Phone') }}</th>
                            <th>{{ translate('Address') }}</th>
                            <th>{{ translate('1st Order Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
					 @php
                        $balance =0;
                        @endphp
                        @foreach($data as $key=>$customer)
                        @php
						if(!empty($customer->order))
							$balance += $customer->order[0]->grand_total;
                        @endphp
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $customer->name }}</td>
							     <td style="text-align:center;">
                    @if(!empty($customer->customer_id))
                    
                    <a href="{{ route('customer_ledger_details.index') }}?customer_id={{$customer->used_by}}" target="_blank" title="{{ translate('View') }}">{{ $customer->customer_id }} </a>
                       @endif
                       
                    </td>
							<td>{{ $customer->phone }}</td>
							<td>{{ $customer->address }}</td>
                            <td style="text-align:right;">@if(!empty($customer->order)) {{ single_price($customer->order[0]->grand_total) }} @else {{single_price(0)}} @endif</td>
                        </tr>
                        @endforeach
                        <tr>
                        <th colspan="5" style="text-align:right">Total</th>
                            <td style="text-align:right;"><b>{{single_price($balance)}}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection