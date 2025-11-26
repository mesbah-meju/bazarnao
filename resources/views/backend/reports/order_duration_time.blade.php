@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Customer Order Duration Time Report')}}</h1>
    </div>
</div>

<div class="col-md-12 mx-auto">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('order_duration_time.index') }}" method="get">
                <div class="row">
                <div class="col-md-4">
                        <label style="text-align:right">{{translate('Customer')}} :</label>
                        <div class="col-md-12">
                            <select id="demo-ease" class="aiz-selectpicker" name="customer_id">
                                <option value="">All</option>
                                @foreach ($customers as $key => $customer)
                                <option value="{{ $customer->id }}" @if($customer->id == $sort_by) selected @endif >{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        </div>
                        <div class="col-md-4">
                            <label>Date Range :</label>
                            <div class="col-md-12">
                                <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                            </div>
                            <div class="col-md-12">
                                <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    <!-- <div class="col-md-6">
                        <div class="col-md-4">Date Range :</div>
                        <div class="col-md-8">
                            <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                        </div>
                        <div class="col-md-8">
                            <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                        </div>
                        <div class="clearfix"></div>
                    </div> -->

                    <div class="col-md-4">
                        <button class="btn btn-primary" type="submit">{{ translate('Filter') }}</button>
                        <button class="btn btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </form>
            <hr>
            <div class="printArea">

                @if(!empty($cust))
                <div class="col-md-12" style="text-align: center;">
                    <p><b>Customer Name : </b> {{$cust->name}}</p>
                    <!-- <p><b> Period : </b> {{date('d-m-Y',strtotime($start_date))}} to {{date('d-m-Y',strtotime($end_date))}}</p> -->

                </div>
                @endif
                <h3 style="text-align:center;">{{translate('Customer Order Duration Time Report')}}</h3>
                <p style="text-align: center;">Date : {{date('d-m-Y')}}</p>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>{{ translate('Customer ID') }}</th>
                            <th>{{ translate('Customer Name') }}</th>
                            <th>{{ translate('Email') }}</th>
                            <th>{{ translate('Phone') }}</th>
                            <th>{{ translate('Address') }}</th>
                            <th>{{ translate('Total Order') }}</th>
                            <th style="width:140px">{{ translate('Avg Duration') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $total = 0;
                        @endphp

                        @foreach($customers as $key=>$customer)
                        @php
						
                        $total += $customer->balance;
                        @endphp
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td style="text-align:center;">
                    @if(!empty($customer->customer_id))
                    
                    <a href="{{ route('customer_ledger.index') }}?customer_id={{$customer->id}}" target="_blank" title="{{ translate('View') }}">{{ $customer->customer_id }} </a>
                       @endif
                       
                    </td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ $customer->address }}</td>
                            <td style="text-align:right">{{ $customer->total_sale }}</td>
                            <td style="text-align:right">{{ $customer->diff }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection