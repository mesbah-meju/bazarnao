@extends('backend.layouts.app')
@section('content')
<div class="card">
    <form id="culexpo" class="" action="" method="GET">
        <div class="card-header row gutters-5">
            <!-- <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Group Product Sales Report') }}</h5>
            </div> -->
            <div class="col-lg-3">
                <div class="form-group mb-0">
                    <label>Date Range :</label>
                    <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                    <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                </div>
            </div>
            <div class="col-md-3">
                <label>Filter By Warehouse :</label>
                <select class="form-control" name="warehouse" data-live-search="true" id="warehouse">
                    <option value="">Select One</option>
                    @foreach(\App\Models\Warehouse::all() as $warehousees)
                        <option value="{{$warehousees->id}}" @if($warehouse == $warehousees->id) selected @endif>{{ $warehousees->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Filter By Employee :</label>
                <select class="form-control" name="user_id" data-live-search="true" id="user_id">
                    <option value="">Select One</option>
                    @foreach(\App\Models\Staff::whereBetween('role_id', [9, 14])->get() as $executive)      
                        <option value="{{$executive->user_id}}" @if($user_id == $executive->user_id) selected @endif>{{ $executive->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type Order code & hit Enter') }}">
                </div>
            </div>
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button class="btn btn-sm btn-primary" onclick="submitForm ('{{ route('group_product_salesReport.index') }}')">{{ translate('Filter') }}</button>
                    <button class="btn btn-sm btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                    <button class="btn btn-sm btn-success" onclick="submitForm('{{ route('sales_ledger_export') }}')">Excel</button>
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
        <h3 style="text-align:center;">{{translate('Group Product Sales Report')}}</h3>
        <table class="table-sm table-striped table-hover table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Delivery Date</th>
                    <th>Order Code</th>
                    <th>Customer ID</th>
                    <th>Customer Name</th>
                    <th>Executive Name</th>
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
                        $executive = !empty($order->user->customer) && !empty($order->user->customer->staff_id) ? \App\Models\User::where('id',$order->user->customer->staff_id)->select('name')->first() : null;
                        $payment_details = json_decode($order->payment_details);
                        $paid = 0;
                        if (!empty($payment_details) && !empty($payment_details->status) && ($payment_details->status == 'VALID' || $payment_details->status == 'Success')) {
                            $paid = $payment_details->amount;
                        } elseif (!empty($payment_details) && !empty($payment_details->transactionStatus) && ($payment_details->transactionStatus == 'Completed')) {
                            $paid = $payment_details->amount;
                        }
                        
                        $totalpaid += $paid;
                        $due = $order->grand_total - $paid;
                        $totaldue += $due;
                        $total += $order->grand_total;
                        $i++;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $i }}</td>
                        <td class="text-center">{{ date("Y-m-d", strtotime($order->delivered_date)) }}</td>
                        <td class="text-center">
                            <a href="{{ route('all_orders.show', encrypt($order->id)) }}" target="_blank" title="{{ translate('View') }}">{{ $order->code }}</a>
                        </td>
                        <td class="text-center">
                            @if (!empty($order->user->customer->customer_id))
                                <a href="{{ route('customer_ledger_details.index') }}?cust_id={{ $order->user_id }}&start_date={{ $start_date }}&end_date={{ $end_date }}" target="_blank" title="{{ translate('View') }}">{{ $order->user->customer->customer_id }}</a>
                            @else
                                {{ $order->guest_id }}
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($order->user)
                                {{ $order->user->name }}
                            @else
                                Guest
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($executive)
                                {{ $executive->name }}
                            @else
                                No Define
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($order->user)
                                {{ $order->user->customer->customer_type ?? 'N/A' }}
                            @else
                                Guest
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($order->user)
                                {{ $order->user->phone }}
                            @else
                                Guest
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($order->user)
                                {{ get_customer_area_name($order->user_id)[0] ?? 'N/A' }}
                            @else
                                @php 
                                    $shipping = json_decode($order->shipping_address);
                                    echo $shipping->area ?? 'N/A';
                                @endphp
                            @endif
                        </td>
                        <td class="text-right">{{ single_price($order->grand_total) }}</td>
                        <td class="text-right">{{ single_price($paid) }}</td>
                        <td class="text-right">{{ single_price($due) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="text-right" colspan="9"><b>Total</b></td>
                    <td class="text-right"><b>{{ single_price($total) }}</b></td>
                    <td class="text-right"><b>{{ single_price($totalpaid) }}</b></td>
                    <td class="text-right"><b>{{ single_price($totaldue) }}</b></td>
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
