@extends('backend.layouts.staff')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Staff Pos Ledger')}}</h1>
    </div>
</div>
@include('backend.staff_panel.sales_executive_nav')
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <form id="culexpo" class="" action="" method="GET">

                <div class="row">
                    <button class="btn btn-sm btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                    <hr>
                    @if((auth()->user()->staff->role->name == 'Sales Executive'))
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#paymentModal" type="button">{{ translate('Pay To Accounts') }}</button>
                    @endif
                </div>
                <hr>
                <div class="printArea">
                    <style>
                        th {
                            text-align: center;
                        }
                    </style>
                    @if(!empty($cust))
                    <div class=row>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label>Date Range :</label>
                                <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                                <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label>Filter By Payment :</label>
                                <select class="form-control" name="pos_type" id="pos_type">
                                    <option value="">Select One</option>
                                    <option value="Payment" @if($pos_type == "Payment") selected @endif >Payment </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label>Status of Payment :</label>
                                <select class="form-control" name="status" id="status">
                                    <option value="">Select One</option>
                                    <option value="Pending" @if($status == "Pending") selected @endif >Pending </option>
                                    <option value="Accepted" @if($status == "Accepted") selected @endif >Accepted </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <button class="btn btn-sm btn-primary" onclick="submitForm ('{{ route('staff_pos_ledger') }}')">{{ translate('Filter') }}</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12" style="text-align: center;">
                            <p><b>POS Ledger Details</b></p>
                            <p><b> Period : </b> {{date('d-m-Y',strtotime($start_date))}} to {{date('d-m-Y',strtotime($end_date))}}</p>
                        </div>
                    </div>
                    @endif
                    <table class="table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Date') }}</th>
                                <th>{{ translate('Ordr ID') }}</th>
                                <th>{{ translate('Type') }}</th>
                                <th>{{ translate('Order Amount') }}</th>
                                <th>{{ translate('Due') }}</th>
                                <th>{{ translate('Debit') }}</th>
                                <th>{{ translate('Credit') }}</th>
                                <th>{{ translate('Balance') }}</th>
                                <th>{{ translate('Invoice') }}</th>
                                <th>{{ translate('Delivery Status') }}</th> <!-- New Column for Delivery Status -->
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $order_amount = 0;
                            $due_amount = 0;
                            $debit = 0;
                            $credit = 0;
                            $balance = $opening[0]->opening_balance;
                            $invoice_number = 0;
                            $invoice_code = 0;
                            @endphp

                            <tr>
                                <td colspan="9" style="text-align:right">Opening Balance</td>
                                <td style="text-align:right;">{{ single_price($balance) }}</td>
                                <td></td> <!-- Empty cell for Delivery Status -->
                            </tr>
                            @foreach($statements as $key=>$statement)
                            <?php
                            $debit += $statement->debit;
                            $order_amount += $statement->order_amount;
                            $due_amount = $order_amount - $debit;

                            if($statement->accounts_executive_status == "Pending"){
                            $acamount = 0;
                            }else{
                            $acamount = $statement->credit;
                            }

                            $credit += $acamount;

                            if($statement->accounts_executive_status == "Pending"){
                            $balance += $statement->debit - 0;
                            }else{
                            $balance += $statement->debit - $statement->credit;
                            }

                            $invoice_number = \App\Models\Order::select('code')
                                ->where('id', $statement->order_id)
                                ->first();

                            if ($invoice_number) {
                                $invoice_code = $invoice_number->code;
                            } else {
                                $invoice_code = 'N/A'; // Or any default value you prefer
                            }

                            $delivery_status = \App\Models\Order::where('id', $statement->order_id)
                                ->value('online_order_delivery_status');

                            // Fetch the delivery status from your model or define logic for it
                            ?>
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ date('d-m-Y',strtotime($statement->date)) }}</td>
                                <td>
                                    <a href="{{route('all_orders.show', encrypt($statement->order_id))}}" target="_blank" title="{{ translate('View') }}">{{ $statement->order_id }}</a>
                                </td>
                                <td>{{ $statement->type }}</td>
                                <td style="text-align:right">{{ $statement->order_amount }}</td>
                                <td style="text-align:right">{{ single_price($statement->due) }}</td>
                                <td style="text-align:right">{{ single_price($statement->debit) }}</td>
                                <td style="text-align:right">
                                    @if(($statement->accounts_executive_status == 'Pending') && (auth()->user()->staff->role->name == 'Account Executive' || auth()->user()->staff->role->name == 'Account Manager'))
                                    <a href="{{ route('pos_amount_transfer_accept',$statement->poslid)}}" class="btn btn-xs btn-info" onclick="return confirm('Are you sure?')">Accept</a>
                                    @endif
                                    <span class="@if($statement->accounts_executive_status == 'Pending')text-danger @else text-success @endif">
                                        {{($statement->accounts_executive_status) }}
                                    </span>
                                    {{single_price($statement->credit) }}
                                </td>
                                <td style="text-align:right;">{{ single_price($balance) }}</td>
                                <td style="text-align:center">
                                    <a href="{{ url('/pos/gen-invoice/'.$statement->order_id) }}" target="_blank" title="{{ translate('Invoice') }}">{{ $invoice_code  }}</a>
                                </td>
                                <td>
                                    <select class="form-control aiz-selectpicker" id="update_delivery_status_{{ $statement->order_id }}" data-minimum-results-for-search="Infinity" onchange="handleStatusChange(this, {{ $statement->order_id }})" @if ($delivery_status == 'delivered') disabled @endif>
                                        <option value=""></option>
                                        <option value="delivered" @if ($delivery_status == 'delivered') selected @endif>
                                            {{ translate('Delivered') }}
                                        </option>
                                        <option value="cancel" @if ($delivery_status == 'cancel') selected @endif>
                                            {{ translate('Cancelled') }}
                                        </option>
                                    </select>
                                </td>


                                <!-- New column showing the delivery status -->
                            </tr>
                            @endforeach
                            <tr>
                                <th colspan="4" style="text-align:right">Total</th>
                                <th style="text-align:right">{{single_price($order_amount,2)}}</th>
                                <th style="text-align:right">{{single_price($due_amount,2)}}</th>
                                <th style="text-align:right">{{single_price($debit,2)}}</th>
                                <th style="text-align:right">{{single_price($credit,2)}}</th>
                                <th style="text-align:right;"><b>{{single_price($balance)}}</b></th>
                                <th style="text-align:right;"></th> <!-- Empty cell for the delivery status -->
                                <th style="text-align:right;"></th> <!-- Empty cell for the delivery status -->
                            </tr>
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
</div>


<!-- The modal -->
<div class="modal" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Pay To Accounts</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="paymentAmount">Amount:</label>
                    <input type="number" class="form-control" id="Amount" name="Amount" value="{{ $balance }}" readonly>
                </div>
                <div class="form-group">
                    <label for="paymentAmount">Payment Amount:</label>
                    <input type="number" class="form-control" id="paymentAmount" name="paymentAmount" required>
                </div>
                <button type="button" onclick="submitAmount()" class="btn btn-primary">Submit Payment</button>
            </div>
        </div>
    </div>
</div>

<script>

    // Function to handle delivery status change
    function handleStatusChange(selectElement, order_id) {
        var status = $(selectElement).val();
        
        // Disable the select input if the status is "delivered"
        if (status == 'delivered') {
            $(selectElement).prop('disabled', true); // Disable the select input
        } else {
            $(selectElement).prop('disabled', false); // Enable the select input if status is not delivered
        }

        // Call the function to update the delivery status
        updateDeliveryStatus(order_id, status);
    }

    // Function to update the delivery status
    function updateDeliveryStatus(order_id, status) {
        if (status == 'on_delivery') {
            alert('Please only utilize "On Delivery" from the order scanning process.');
        } else if (status == 'delivered') {
            $.post('{{ route('orders.product_stock_qty_check') }}', {
                _token: '{{ csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {
                if (data.message == 'false') {
                    AIZ.plugins.notify('warning', data.product + ' Stock Qty Not Enough for Delivery');
                    return false;
                } else if (data.message == 'warehouse') {
                    AIZ.plugins.notify('warning', 'Warehouse is not selected');
                    return false;
                } else {
                    $.post('{{ route('orders.update_delivery_status') }}', {
                        _token: '{{ csrf_token() }}',
                        order_id: order_id,
                        status: status
                    }, function(data) {
                        if (data.message == 'true') {
                            AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
                            location.reload();
                        } else if (data.message == 'false') {
                            AIZ.plugins.notify('warning', data.product + ' Stock Qty Not Enough for Delivery');
                        }
                    });
                }
            });
        } else {
            $.post('{{ route('orders.update_delivery_status') }}', {
                _token: '{{ csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {
                if (data.message == 'true') {
                    AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
                    location.reload();
                }
            });
        }
    }


    function submitAmount(){
        let paymentAmountbyname = $('input[name=paymentAmount]').val();
        let paymentAmountbyid = parseInt($('#paymentAmount').val());
        let total_due = parseInt({{ $balance }});
         if(paymentAmountbyid > total_due ){
            AIZ.plugins.notify('danger', '{{ translate('Pay amount must be less than or equal balance') }}');
            $('#paymentModal').modal('hide');
            return false;
         }else{
            $.post('{{ route('pos_amount_transfer') }}',{
                _token                  : AIZ.data.csrf, 
                paymentAmountbyid       : paymentAmountbyid,
            },function(data){
                if(data.success==1){
                $('#paymentModal').modal('hide');
                 AIZ.plugins.notify('success','Amount Submited Successfully');
                 location.reload().setTimeOut(500);
                }else{
                 $('#paymentModal').modal('hide');
                 AIZ.plugins.notify('danger','Not Allow If Has Any Pending Request');
                }
            });
         }
    }

    function submitForm(url) {
        $('#culexpo').attr('action', url);
        $('#culexpo').submit();
    }

</script>
@endsection
