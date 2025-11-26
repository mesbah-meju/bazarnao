@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Supplier Ledger report')}}</h1>
    </div>
</div>

<div class="col-md-12 mx-auto">
    <div class="card">
        <div class="card-body">
            <form id="culexpo" action="{{ route('supplier_ledger.index') }}" method="get">
                <div class="row">

                    <div class="col-md-3">
                        <label class="col-form-label">{{translate('Sort by Warehouse')}} :</label>
                        <select id="warehouse" class="aiz-selectpicker select2" name="warehouse" data-live-search="true">
                            <option value=''>All</option>
                            @if(in_array(Auth::user()->id, [9, 135, 137, 138]))
                            @foreach (\App\Models\Warehouse::all() as $key => $warehous)
                            <option @php if($wearhouse==$warehous->id)
                                echo 'selected';
                                @endphp
                                value="{{ $warehous->id }}">{{ $warehous->name}}</option>
                            @endforeach
                            @else()

                            @foreach(\App\Models\Warehouse::whereIn('id',getWearhouseBuUserId(Auth::user()->id))->get() as $warehousees)
                            <option value="{{ $warehousees->id }}" @if($wearhouse==$warehousees->id) selected @endif>{{ $warehousees->name }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="col-form-label">{{translate('Sort by Supplier')}} :</label>
                        <select id="supplier_id" class="aiz-selectpicker select2" name="supplier_id" data-live-search="true">
                            <option value=''>All</option>
                            
                            @foreach (\App\Models\Supplier::all() as $key => $supplier)
                            <option @php if($supplier_id==$supplier->supplier_id)
                                echo 'selected';
                                @endphp
                                value="{{ $supplier->supplier_id }}">{{ $supplier->name}}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-2">
                        <label>Start Date :</label>
                        <input type="date" name="start_date" class="form-control" value="{{$start_date}}">

                    </div>
                    <div class="col-md-2">
                        <label>End Date :</label>
                        <input type="date" name="end_date" class="form-control" value="{{$end_date}}">

                    </div>
                    <div class="col-lg-2">
                        <div class="form-group mb-0">
                            <label>Month :</label>
                            <input type="month" name="month" id="month" class="form-control" @isset($month) value="{{ $month_year }}-{{ $month }}" @endisset>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group mb-0">
                            <label>Year :</label>
                            <select name="year" id="year" class="form-control">
                                <option value="">Select One</option>
                                @php
                                $currentYear = date('Y');
                                $startYear = $currentYear - 5;
                                $endYear = $currentYear + 10;
                                @endphp
                                @for ($i = $startYear; $i <= $endYear; $i++)
                                    <option value="{{ $i }}" @if(isset($year) && $year==$i) selected @endif>{{ $i }}</option>
                                    @endfor
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label style="margin-top:35px;">&nbsp;<br></label>
                        <button class="btn btn-sm btn-primary" onclick="submitForm ('{{ route('supplier_ledger.index') }}')">{{ translate('Filter') }}</button>
                        <button class="btn btn-sm btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                        <button class="btn btn-sm btn-success" onclick="submitForm('{{ route('supplier_ledger_export') }}')">Excel</button>
                    </div>
                </div>
                <div class="clearfix"></div>
        </div>
        </form>
        <hr>
        <div class="">
            <style>
                th {
                    text-align: center;
                }
            </style>
            <div class="col-md-12" style="text-align: center;">
                <p><b>Supplier Ledger Summary</b></p>
                <p><b> Period : </b> {{date('d-m-Y',strtotime($start_date))}} to {{date('d-m-Y',strtotime($end_date))}}</p>

            </div>
            <div class="printArea">
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
                        $i=0;
                        @endphp

                        @foreach($customers as $key=>$customer)
                        @php

                        if($customer->debit==0 and $customer->credit==0 and $customer->balance==0){
                        continue;
                        }

                        $debit += $customer->debit;
                        $credit += $customer->credit;
                        $balance += $customer->opening_balance+$customer->debit-$customer->credit;
                        $opening_balance += $customer->opening_balance;
                        $i++;
                        @endphp
                        <tr style="text-align: center;">
                            <td>{{ $i }}</td>
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
                            <td style="text-align:right;"><b>{{single_price($balance)}}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
<script>
    document.getElementById('month').addEventListener('input', function() {
        document.getElementById('year').selectedIndex = 0;
    });

    document.getElementById('year').addEventListener('input', function() {
        document.getElementById('month').value = '';
    });
</script>
@endsection