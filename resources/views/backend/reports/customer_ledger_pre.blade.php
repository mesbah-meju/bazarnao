@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Customer Ledger report')}}</h1>
    </div>
</div>

<div class="col-md-12 mx-auto">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('customer_ledger.index') }}" method="get">
                <div class="row">
                    <div class="col-md-4">
                        <label>{{translate('Customer')}} :</label>
                            <select id="demo-ease" class="form-control aiz-selectpicker"  data-minimum-results-for-search="Infinity" name="customer_id">
                                <option value="">All</option>
                                @foreach ($customers as $key => $customer)
                                <option value="{{ $customer->user_id }}" @if($customer->user_id == $sort_by) selected @endif >{{ $customer->name }}({{$customer->customer_id}})</option>
                                @endforeach
                            </select>
                    </div>
                    <div class="col-md-4">
                        <label>Date Range :</label>
                            <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                            <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" name="status">
                            <option value="">All</option>
                            <option @if($status=='paid') {{'selected'}} @endif value="paid">Paid</option>
                            <option @if($status=='unpaid') {{'selected'}} @endif value="unpaid">Unpaid</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-sm btn-primary" type="submit">{{ translate('Filter') }}</button>
                        <button class="btn btn-sm btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
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
                @if(!empty($cust))
                <div class="col-md-12" style="text-align: center;">
                    <p><b>Customer Name : </b> {{$cust->name}}</p>
                    <p><b>Customer ID : </b> {{$cust->customer->customer_id}}</p>
                    <p><b> Period : </b> {{date('d-m-Y',strtotime($start_date))}} to {{date('d-m-Y',strtotime($end_date))}}</p>

                </div>
                @endif
                <table class="table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ translate('Order ID') }}</th>
                            <th>{{ translate('Date') }}</th>
                            <th>{{ translate('Payment Type') }}</th>
                            <th>{{ translate('Discount') }}</th>
                            <th>{{ translate('Order Amount') }}</th>
                            <th>{{ translate('Paid') }}</th>
                            <th>{{ translate('Due') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $total = 0;
                        $discount = 0;
                        $totalpaid = 0;
                $totaldue = 0;
                    $paid = 0;
                     $due = 0;
                        @endphp

                        @foreach($orders as $key=>$order)
                        @php
                        $payment_details = json_decode($order->payment_details);
                if(!empty($payment_details)){
                    $totalpaid+=$payment_details->amount;
                    $paid =$payment_details->amount;
                    $totaldue+=($order->grand_total-$paid);
                    $due = $order->grand_total-$paid;
                }else{
                    $totaldue+=$order->grand_total;
                    $due = $order->grand_total;
                    $paid = 0;
                }
                        $total += $order->grand_total;
                        $discount += $order->coupon_discount;
                        @endphp
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>
                            <a href="{{route('all_orders.show', encrypt($order->id))}}" target="_blank" title="{{ translate('View') }}">{{ $order->code }}</a>
                            </td>
                            <td>{{ date('d-m-Y',$order->date) }}</td>
                            <td>{{ $order->payment_type }}</td>
                            <td style="text-align:right">{{ single_price($order->coupon_discount) }}</td>
                            <td style="text-align:right">{{ single_price($order->grand_total,2) }}</td>
                            <td style="text-align:right;">{{ single_price($paid) }}</td>
                            <td style="text-align:right;">{{ single_price($due) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <th colspan="4" style="text-align:right">Total</th>
                            <th style="text-align:right">{{single_price($discount,2)}}</th>
                            <th style="text-align:right">{{single_price($total,2)}}</th>
                            <td style="text-align:right;"><b>{{single_price($totalpaid)}}</b></td>
                    <td style="text-align:right;"><b>{{single_price($totaldue)}}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection