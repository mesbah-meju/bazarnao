@extends('backend.layouts.staff')

@section('content')

@include('backend.staff_panel.delivery_executive.delivery_executive_nav')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Delivery Ledger report')}}</h1>
    </div>
</div>

<div class="col-md-12 mx-auto">
    <div class="card">
        <div class="card-body">
            <form id="culexpo" action="{{ route('delivery_executive_collection_payment.index') }}" method="get">
                <div class="row">
                  
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
                        <button class="btn btn-sm btn-primary" onclick="submitForm('{{ route('delivery_executive_collection_payment.index') }}')">{{ translate('Filter') }}</button>
                        <button class="btn btn-sm btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                    
                    </div>
                    <div class="clearfix"></div>
                </div>
            </form>
            <hr>
            <div class="printArea">
                <style>
                    th {
                        text-align: center;
                    }
                </style>
                <div class="col-md-12" style="text-align: center;">
                    <p><b>Delivery Ledger Summary</b></p>
                    <p><b> Period : </b> {{date('d-m-Y',strtotime($start_date))}} to {{date('d-m-Y',strtotime($end_date))}}</p>

                </div>
                <table class="table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>{{ translate('Order ID') }}</th>
                            <th>{{ translate('Customer Name') }}</th>
                            <th>{{ translate('Collection Date') }}</th>
                            <th>{{ translate('Paid to') }}</th>
                            <th>{{ translate('Total Collection') }}</th>
                            <th>{{ translate('Total Payment') }}</th>
                            <th>{{ translate('Due') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $debit = 0;
                        $credit = 0;
                        $balance = 0;
                        @endphp
                        @foreach($deelivery_ledgers as $key=>$dledger)
                        @php

                        $debit += $dledger->debit;
                        $credit += $dledger->credit;
                        $balance += $dledger->debit-$dledger->credit;
                        @endphp
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td style="text-align:left">{{ $dledger->order_no }}</td>
                            <td style="text-align:left">{{ getCustomerNameByOrderno($dledger->order_no) }}</td>
                            <td style="text-align:left">{{ ($dledger->created_at) }}</td>
                            <td style="text-align:left">{{ getUserNameByuserID($dledger->note) }}</td>
                            
                            <td style="text-align:right">{{ single_price($dledger->debit) }}</td>
                            <td style="text-align:right">{{ single_price($dledger->credit) }}</td>
                            <td style="text-align:right;">{{ single_price($dledger->debit-$dledger->credit) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <th colspan="5" style="text-align:right">Total</th>
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
    function submitForm(url) {
        $('#culexpo').attr('action', url);
        $('#culexpo').submit();
    }
</script>

@endsection