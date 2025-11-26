@extends('backend.layouts.app')
@section('content')
<div class="card">
    <form id="culexpo" class="" action="{{ route('POSsalesReport.index') }}" method="GET">
        <div class="card-header row gutters-5">
            <div class="col-lg-4">
                <div class="form-group mb-0">
                    <label>Date Range:</label>
                    <div class="input-group">
                        <input type="date" name="start_date" class="form-control" value="{{ $start_date }}">
                        <input type="date" name="end_date" class="form-control" value="{{ $end_date }}">
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <label>Filter By Warehouse:</label>
                    <select class="form-control" name="warehouse" id="warehouse">
                        <option value="">Select One</option>
                        @foreach(\App\Models\Warehouse::all() as $warehousees)
                            <option value="{{ $warehousees->id }}" @if($wearhouse == $warehousees->id) selected @endif>{{ $warehousees->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <label>Filter By Employee:</label>
                    <select class="form-control" name="user_id" id="user_id">
                        <option value="">Select One</option>
                        @foreach(\App\Models\Staff::whereBetween('role_id', [9, 14])->get() as $executive)
                            <option value="{{ $executive->user_id }}" @if($user_id == $executive->user_id) selected @endif>{{ $executive->user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <label>Search Order:</label>
                    <input type="text" class="form-control" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type Order code & hit Enter') }}">
                </div>
            </div>
            <div class="col-auto">
                <div class="form-group mb-0 mt-2">
                    <button class="btn btn-sm btn-primary" onclick="submitForm ('{{ route('POSsalesReport.index') }}')">{{ translate('Filter') }}</button>
                    <button class="btn btn-sm btn-info" type="button" onclick="printDiv()">{{ translate('Print') }}</button>
                    <button class="btn btn-sm btn-success" onclick="submitForm('{{ route('pos_sales_ledger_export') }}')">Excel</button>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body printArea">
        <style>
            th {
                text-align: center;
            }
        </style>
        <h3 style="text-align:center;">{{translate('POS Sales Report')}}</h3>
        <table class="table table-sm table-hover table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Date</th>
                    <th>Order Code</th>
                    <th>Warehouse</th>
                    <th>Customer ID</th>
                    <th>Customer Name</th>
                    <th>Customer Type</th>
                    <th>Phone</th>
                    <th>Area</th>
                    <th>Amount</th>
                    <th>Paid</th>
                    <th>Due</th>
                </tr>
            </thead>
            <tbody>
                @php
                $i = 0;
                $total = 0;
                $totalpaid = 0;
                $totaldue = 0;
                @endphp

                @foreach ($orders as $key => $order)
                @php
                if(!empty(\App\Models\Customer::where('user_id', $order->user_id)->first()))
                $customer_id = \App\Models\Customer::where('user_id', $order->user_id)->first();
                else
                $customer_id = '';

                if(!empty($customer_id)){
                    if(!empty($customer_id->staff_id)){
                        $executive = \App\Models\User::where('id',$customer_id->staff_id)->first();
                    }
                 }
                
                 $payment_details = json_decode($order->payment_details);
                if(!empty($payment_details) && !empty($payment_details->status) && ($payment_details->status=='VALID' || $payment_details->status=='Success' )){
                $totalpaid+=$payment_details->amount;
                $paid =$payment_details->amount;
                $totaldue+=($order->grand_total-$paid);
                $due = $order->grand_total-$paid;
                } else if(!empty($payment_details) && !empty($payment_details->transactionStatus) && ($payment_details->transactionStatus=='Completed')){
                $totalpaid+=$payment_details->amount;
                $paid =$payment_details->amount;
                $totaldue+=($order->grand_total-$paid);
                $due = $order->grand_total-$paid;
                }else{
                $totaldue+=$order->grand_total;
                $due = $order->grand_total;
                $paid = 0;
                }
                $total+=$order->grand_total;
                

                $warehouse = \App\Models\Warehouse::where('id',$order->warehouse)->first();
                @endphp
                
                @unless ($order->grand_total == 0 && $paid == 0 && $due == 0)
                @php
                $i++;
                @endphp
                <tr>
                    <td style="text-align:center;">
                        {{ ($i) }}
                    </td>
                    <td style="text-align:center;">
                        {{ date('Y-m-d',$order->date) }}
                    </td>
                    <td style="text-align:center;">
                        <a href="{{route('all_orders.show', encrypt($order->id))}}" target="_blank" title="{{ translate('View') }}">{{ $order->code }}</a>
                    </td>

                    <td style="text-align:center;">
                        {{ $warehouse->name }}
                    </td>
                    <td style="text-align:center;">
                        @if ($order->user != null)
                        <a href="{{ route('customer_ledger_details.index') }}?cust_id={{$order->user_id}}&start_date={{$start_date}}&end_date={{$end_date}}" target="_blank" title="{{ translate('View') }}">{{ $customer_id->customer_id }} </a>
                        @else
                        {{ $order->guest_id }}
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @if ($order->user != null)
                        {{ $order->user->name }}
                        @else
                        Guest
                        @endif
                    </td>
                    
                    <td style="text-align:center;">
                        @if ($order->user != null)
                        {{ $customer_id->customer_type}}
                        @else
                        Guest
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @if ($order->user != null)
                        {{ $order->user->phone }}
                        @else
                        Guest
                        @endif
                    </td>
                    <td style="text-align:center;">
                       @if ($order->user != null)
                       {{ get_customer_area_name($order->user_id)[0] }}
                       @else
                        @php 
                            $shipping = json_decode($order->shipping_address);
                            if(!empty($shipping->area)){
                                echo $shipping->area;
                            }else{
                                echo 'N/A';
                            }
                        @endphp
                      @endif
                    </td>
                    <td style="text-align:right;">
                        {{ single_price($order->grand_total) }}
                    </td>
                    <td style="text-align:right;">
                        {{ single_price($paid) }}
                    </td>
                    <td style="text-align:right;">
                        {{ single_price($due) }}
                    </td>
                </tr>
                @endunless
                @endforeach
                <tr>
                    <td style="text-align:right;" colspan="9"><b>Total</b></td>
                    <td style="text-align:right;"><b>{{single_price($total)}}</b></td>
                    <td style="text-align:right;"><b>{{single_price($totalpaid)}}</b></td>
                    <td style="text-align:right;"><b>{{single_price($totaldue)}}</b></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    function submitForm(url) {
        $('#culexpo').attr('action', url);
        $('#culexpo').submit();
    }
</script>

@endsection