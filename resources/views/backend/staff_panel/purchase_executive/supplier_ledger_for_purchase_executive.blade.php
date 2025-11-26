@extends('backend.layouts.staff')
<style>
    tr,
    th,
    td {
        padding: 3px !important;
    }

    th {
        background: #AE3C86;
        color: #fff;
        font-weight: bold
    }

    li.nav-item {
        width: 100%;
    }

    .navbar-nav {
        width: 100%;
    }
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/2.4.85/css/materialdesignicons.css"
    rel="stylesheet" />

@section('content')
@include('backend.staff_panel.purchase_executive.purchase_executive_nav')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Supplier Ledger report')}}</h1>
    </div>
</div>

<div class="col-md-12 mx-auto">
    <div class="card">
        <div class="card-body">
            <form id="culexpo" action="{{ route('supplier_ledger_for_purchase_executive.index') }}" method="get">
                <div class="row">
                  
                    <div class="col-md-4">
                        <label>Start Date :</label>
                            <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                            
                    </div>
                    <div class="col-md-4">
                        <label>End Date :</label>
                        <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                            
                    </div>
        
                    <div class="col-md-4">
                    <label style="margin-top:35px;">&nbsp;<br></label>
                    <button class="btn btn-sm btn-primary" onclick="submitForm ('{{ route('supplier_ledger_for_purchase_executive.index') }}')">{{ translate('Filter') }}</button>
                        <button class="btn btn-sm btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                        <button class="btn btn-sm btn-info" onclick="submitForm('{{ route('supplier_ledger_export') }}')">Excel</button>
                    </div>
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
                    <p><b>Supplier Ledger Summary</b></p>
                    <p><b> Period : </b> {{date('d-m-Y',strtotime($start_date))}} to {{date('d-m-Y',strtotime($end_date))}}</p>

                </div>
              
                <table class="table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>{{ translate('supplier ID') }}</th>
                            <th>{{ translate('supplier Name') }}</th>
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
                        $opening_balance =0;
                        @endphp

                        @foreach($customers as $key=>$customer)
                        @php
                      
                        $debit += $customer->debit;
                        $credit += $customer->credit;
                        $balance += $customer->opening_balance+$customer->debit-$customer->credit;
                        $opening_balance += $customer->opening_balance;
                        @endphp
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>
                            <a href="{{route('supplier_ledger_details.index')}}?cust_id={{$customer->supplier_id}}&start_date={{$start_date}}&end_date={{$end_date}}" target="_blank" title="{{ translate('View') }}">{{ $customer->supplier_id }}</a>
                            </td>
                            <td>{{ $customer->name }}</td>
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