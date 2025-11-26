@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Customer Ledger report')}}</h1>
    </div>
</div>

<?php
    $warehousearray = getWearhouseBuUserId(auth()->user()->id);
    $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
    $user_name = auth()->user()->name;

?>

<div class="col-md-12 mx-auto">
    <div class="card">
        <div class="card-body">
        <form id="culexpo" action="{{ route('customer_ledger.index') }}" method="get">
                <div class="row">
                  <div class="col-md-3">
				  <label>Email or name & Enter :</label>
				  <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type email or name & Enter') }}">
				  </div>
                    <div class="col-md-3">
                        <label>Start Date :</label>
                            <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                            
                    </div>
                    <div class="col-md-3">
                        <label>End Date :</label>
                        <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                            
                    </div>

                    <div class="col-md-3">
                        <label>Filter By Employee :</label>
                        <select class="form-control" name="user_id" id="user_id">
                        <option value="">Select One</option>
                        @foreach(\App\Models\Staff::whereBetween('role_id',[9, 14])->get() as $executive)      
                        <option value="{{$executive->user_id}}" @if($user_id == $executive->user_id) selected @endif >{{ $executive->user->name}}</option>
                        @endforeach
                        </select>
                    </div>

                    @if(Auth::user()->user_type == 'admin' || $user_name == 'Account Department')   
                    <div class="col-md-3">
                        <label class="col-form-label">{{translate('Sort by Warehouse')}} :</label>
                        <select id="warehouse" class="aiz-selectpicker select2" name="warehouse" data-live-search="true">
                            <option value=''>All</option>
                            @foreach (\App\Models\Warehouse::all() as $key => $warehous)
                            <option 
                            @php if($wearhouse == $warehous->id)
                                echo 'selected';
                                @endphp
                                value="{{ $warehous->id }}">{{ $warehous->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <div class="col-md-3">
                        <label class="col-form-label">{{translate('Sort by Warehouse')}} :</label>
                        <select id="warehouse" class="aiz-selectpicker select2" name="warehouse" data-live-search="true">
                            <option value=''>Select Warehouse</option>
                            @foreach ($warehouses as $key => $warehous)
                            <option 
                            @php if($wearhouse == $warehous->id)
                                echo 'selected';
                                @endphp
                                value="{{ $warehous->id }}">{{ $warehous->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
        
                    <div class="col-md-3">
                    <label style="margin-top:35px;">&nbsp;<br></label>
                    <button class="btn btn-sm btn-primary" onclick="submitForm('{{ route('customer_ledger.index') }}')">{{ translate('Filter') }}</button>
                        <button class="btn btn-sm btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                        <button class="btn btn-sm btn-success" onclick="submitForm('{{ route('customer_ledger_export') }}')">Excel</button>

                   
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
                    <p><b>Customer Ledger Summary</b></p>
                    <p><b> Period : </b> {{date('d-m-Y',strtotime($start_date))}} to {{date('d-m-Y',strtotime($end_date))}}</p>

                </div>
                <table class="table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>{{ translate('Customer ID') }}</th>
                            
                            <th>{{ translate('Customer Name') }}</th>
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
                        $total_balance = 0;
                        $opening_balance = 0;
                        @endphp

                        @foreach($customers as $key=>$customer)
                        @php
						if(empty($customer->name)){
							continue;
						}
                        $debit += $customer->debit;
                        $credit += $customer->credit;
                        $total_balance += $customer->opening_balance+$customer->debit-$customer->credit;
                        $opening_balance += $customer->opening_balance;
                        @endphp
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td style="text-align:center">
                            <a href="{{route('customer_ledger_details.index')}}?cust_id={{$customer->user_id}}&start_date={{$start_date}}&end_date={{$end_date}}" target="_blank" title="{{ translate('View') }}">{{ $customer->customer_no }}</a>
                            </td>
                            <td style="text-align:left">{{ $customer->name }}</td>
                            <td style="text-align:right">{{ single_price($customer->opening_balance) }}</td>
                            <td style="text-align:right">{{ single_price($customer->debit) }}</td>
                            <td style="text-align:right">{{ single_price($customer->credit) }}</td>
                            <td style="text-align:right;">
                                @php
                                    $balance = $customer->opening_balance + $customer->debit - $customer->credit;
                                @endphp

                                @if($balance > 0)
                                    <a href="javascript:" 
                                    class="" 
                                    onClick="return customer_received('{{ $customer->user_id }}');">
                                        {{ single_price($balance) }}
                                    </a>
                                @else
                                    {{ single_price($balance) }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <th colspan="3" style="text-align:right">Total</th>
                            <th style="text-align:right">{{single_price($opening_balance,2)}}</th>
                            <th style="text-align:right">{{single_price($debit,2)}}</th>
                            <th style="text-align:right">{{single_price($credit,2)}}</th>
                            <td style="text-align:right;"><b>{{single_price($total_balance)}}</b></td>
                        </tr>
                            <th colspan="6" style="text-align:right">Total Due</th>
                            <td style="text-align:right;"><b>{{single_price($due)}}</b></td>
                        <tr>

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')
    @include('modals.common_modal')
@endsection




@section('script')
<script type="text/javascript">
    function submitForm(url){
        $('#culexpo').attr('action',url);
        $('#culexpo').submit();
    }

    function customer_received(customer_id) {
            $('#common-modal .modal-title').html('');
            $('#common-modal .modal-body').html('');

            var title = 'Customer Received';

            $.post('{{ route("customer-receive.orderwise") }}', {
                _token      : AIZ.data.csrf, 
                customer_id : customer_id
            }, function(data){
                $('#common-modal .modal-title').html(title);
                $('#common-modal .modal-body').html(data);
                $('#common-modal .modal-dialog').removeClass('modal-lg');
                $('#common-modal .modal-dialog').addClass('modal-xl');
                $('#common-modal').modal('show');
            });
        }
</script>
@endsection