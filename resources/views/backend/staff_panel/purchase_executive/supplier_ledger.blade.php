@extends('backend.layouts.staff')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Supplier Ledger report')}}</h1>
    </div>
</div>
@include('backend.staff_panel.purchase_executive.purchase_executive_nav')
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('customer_ledger.index') }}" method="get">
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
                    <p><b>Supplier Ledger Details</b></p>
                    <p><b>Supplier Name : </b> {{$cust->name}}</p>
                    <p><b>Supplier ID : </b> {{$cust->supplier_id}}</p>
                    <p><b> Period : </b> {{date('d-m-Y',strtotime($start_date))}} to {{date('d-m-Y',strtotime($end_date))}}</p>

                </div>
                @endif
                <table class="table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ translate('Date') }}</th>
                            <th>{{ translate('Purchase ID') }}</th>
                            <th>{{ translate('Particulars') }}</th>
                            <th>{{ translate('Debit') }}</th>
                            <th>{{ translate('Credit') }}</th>
                            <th>{{ translate('Balance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $debit = 0;
                        $credit = 0;
                        $balance = $opening[0]->opening_balance;
                        @endphp

                        <tr>
                            <td colspan="6" style="text-align:right">Opening Balance</td>
                            <td style="text-align:right;">{{ single_price($balance) }}</td>
                        </tr>
                        @foreach($customers as $key=>$customer)
                        @php
                      
                        $debit += $customer->debit;
                        $credit += $customer->credit;
                        $balance += $customer->debit-$customer->credit;
                        @endphp
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ date('d-m-Y',strtotime($customer->date)) }}</td>
                            <td>
                            <a href="{{route('purchase_orders_view', $customer->purchase_id)}}" target="_blank" title="{{ translate('View') }}">{{ $customer->purchase_no }}</a>
                            </td>
                            <td>{{ $customer->descriptions }} - {{ $customer->type }}</td>
                            <td style="text-align:right">{{ single_price($customer->debit) }}</td>
                            <td style="text-align:right">{{ single_price($customer->credit) }}</td>
                            <td style="text-align:right;">{{ single_price($balance) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                        <th colspan="4" style="text-align:right">Total</th>
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

@endsection