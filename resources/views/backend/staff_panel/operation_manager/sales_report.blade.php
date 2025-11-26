@extends('backend.layouts.staff')

@section('content')
@if(auth()->user()->staff->role->name=='Purchase Manager')
@include('backend.staff_panel.purchase_manager.purchase_manager_nav')
@elseif(auth()->user()->staff->role->name=='Account Executive' || auth()->user()->staff->role->name=='Account Manager')
@include('backend.staff_panel.account_executive.account_executive_nav')
@else
@include('backend.staff_panel.operation_manager.operation_manager_nav')
@endif

<div class="card">
    <form id="culexpo" class="" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Sales Report') }}</h5>
            </div>
            <div class="col-lg-3">
                <div class="form-group mb-0">
                    <label>Date Range :</label>
                    <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                    <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type Order code & hit Enter') }}">
                </div>
            </div>
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button class="btn btn-sm btn-primary" onclick="submitForm ('{{ route('operation_sales_report.index') }}')">{{ translate('Filter') }}</button>
                    <button class="btn btn-sm btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                    {{-- <button class="btn btn-sm btn-info" onclick="submitForm('{{ route('sales_ledger_export') }}')">Excel</button> --}}
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
        <h3 style="text-align:center;">{{translate('Sales Report')}}</h3>
        <table class="table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>{{ translate('Date') }}</th>
                    <th>{{ translate('Order Code') }}</th>
                    <th data-breakpoints="md">{{ translate('Customer ID') }}</th>
                    <th data-breakpoints="md">{{ translate('Customer Name') }}</th>
                    <th data-breakpoints="md">{{ translate('Phone') }}</th>
                    <th data-breakpoints="md">{{ translate('Area') }}</th>
                    <th data-breakpoints="md">{{ translate('Amount') }}</th>
                    <th data-breakpoints="md">{{ translate('Paid') }}</th>
                    <th data-breakpoints="md">{{ translate('Due') }}</th>

                </tr>
            </thead>
            <tbody>
                @php
                $total = 0;
                $totalpaid = 0;
                $totaldue = 0;
                $i = 0;
                @endphp
                @foreach ($orders as $key => $order)
                @php
                $delivery_status = $order->orderDetails->first();
                $error = 0;
                if(empty($delivery_status)){
                $error = 1;
                continue;
                }else{
                if($delivery_status->delivery_status=='cancel' || $delivery_status->delivery_status=='pending'){
                $error = 1;
                continue;
                }
                }



                if(!empty(\App\Models\Customer::where('user_id', $order->user_id)->first()))
                $customer_id = \App\Models\Customer::where('user_id', $order->user_id)->first()->customer_id;
                else
                $customer_id = '';
                $payment_details = json_decode($order->payment_details);
                if(!empty($payment_details) && !empty($payment_details->status) && ($payment_details->status=='VALID')){
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
                @endphp
                @if( $error == 0)

                @php
                $i++;
                @endphp
                <tr>
                    <td>
                        {{ ($i) }}
                    </td>
                    <td>
                        {{ date('d-m-Y',$order->date) }}
                    </td>
                    <td style="text-align:center;">
                        <a href="{{route('all_orders.show', encrypt($order->id))}}" target="_blank" title="{{ translate('View') }}">{{ $order->code }}</a>
                    </td>
                    <td style="text-align:center;">


                        @if ($order->user != null)
                        {{ $customer_id }}
                        @else
                        {{ $order->guest_id }}
                        @endif

                    </td>
                    <td>
                        @if ($order->user != null)
                        {{ $order->user->name }}
                        @else
                        @php
                        $shipping = json_decode($order->shipping_address);
                        if(!empty($shipping)){
                        echo $shipping->name;
                        }else{
                        echo 'Guest';
                        }
                        @endphp
                        @endif
                    </td>
                    <td>
                        @if ($order->user != null)
                        {{ $order->user->phone }}
                        @else
                        @php
                        $shipping = json_decode($order->shipping_address);
                        if(!empty($shipping)){
                        echo $shipping->phone;
                        }else{
                        echo 'Guest';
                        }
                        @endphp
                        @endif
                    </td>

                    <td>

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
                @endif
                @endforeach
                <tr>
                    <td style="text-align:right;" colspan="7"><b>Total</b></td>
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

@section('modal')
@include('modals.delete_modal')
@endsection

@section('script')


@endsection