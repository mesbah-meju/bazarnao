@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3"></h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
            <form id="prowasales" action="{{ route('order_status_changer_report.index') }}" method="get">
                    <div class="form-group row">

                        <div class="col-md-3">
                            <label>Date Range:</label>
                            <input type="date" name="from_date" class="form-control" value="{{ $from_date }}">
                            <div class="clearfix"></div>
                        </div>
                        
                        <div class="col-md-3">
                            <label>Date Range:</label>
                            <input type="date" name="to_date" class="form-control" value="{{ $to_date }}">
                            <div class="clearfix"></div>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="col-form-label">{{ translate('Executive Name') }}:</label>
                            <select id="demo-ease" class="aiz-selectpicker select2" name="user_name" data-live-search="true">
                                <option value="">Select User</option>
                                @php $users = \App\Models\Staff::leftJoin('users', 'staff.user_id', '=', 'users.id')
                                                    ->leftJoin('roles', 'staff.role_id', '=', 'roles.id')
                                                        ->select('staff.*', 'users.id as userId', 'users.name')
                                                        ->where('roles.id', 9)
                                                        ->get();
                                    @endphp
                                     @foreach ($users as $user)
                                     <option @if($user_name == $user->userId) selected @endif value="{{ $user->userId }}">{{ $user->name }}</option>
                                 @endforeach
                                {{-- @foreach (\App\Models\User::where('user_type','staff')->get() as $user)
                                    <option @if($user_name == $user->id) selected @endif value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach --}}
                            </select>
                            <div class="clearfix"></div>
                        </div>

                        <div class="col-md-3">
                            <label class="col-form-label">{{ translate('Order Status') }}:</label>
                            <select id="demo-ease" class="aiz-selectpicker select2" name="order_status" data-live-search="true">
                                <option value="">Select Status</option>
                                @foreach (\App\Models\OrderStatusLog::all()->groupBy('order_status') as $status => $orders)
                                    <option @if(isset($order_status) && $order_status == $status) selected @endif value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                            <div class="clearfix"></div>
                        </div>
                        
                        
                        
                        
                        <div class="col-lg-3">
                        <label>Filter By Order ID :</label>
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type Order Id & hit Enter') }}">
                </div>
            </div>

                        <div class="col-md-3 ">
                            <label>&nbsp;</label>
                            <br>
                            <div class="d-flex">
                            <button class="btn btn-sm btn-primary" onclick="submitForm ('{{ route('order_status_changer_report.index') }}')">{{ translate('Filter') }}</button>
                            <br>
                            <button class="btn btn-sm btn-info mx-1" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                        
                            <button class="btn btn-sm btn-success" onclick="submitForm('{{route('order_status_changer_report_export')}}')">Excel</button> 
                        </div>
                        </div>
                    </div>
                </form>

                <div class="printArea">
                <style>
th{text-align:center;}
</style>
                    <h3 style="text-align:center;">{{translate('Daily Order Activitis Report')}}</h3>
                    <table class="table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width:5%">SL</th>
                                <th style="width:30%">{{ translate('Executive Name') }}</th>
                                <th style="width:20%">{{ translate('Date') }}</th>
                                <th style="width:10%">{{ translate('Order Id') }}</th>
                                <th style="width:10%">{{ translate('Status') }}</th>
                                <th style="width:10%">{{ translate('Remarks') }}</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                       
                            @foreach ($order_status_logs as $key => $order_status_log)
                                      
                            <tr>
                                <td>{{ ($key+1)}}</td>
                                <td>{{ $order_status_log->user->name ?? 'N/A' }}</td>
                                <td >{{$order_status_log->created_at}}</td>
                                <td >{{$order_status_log->order_code}}</td>
                                <td >{{$order_status_log->order_status}}</td>
                                <td >{{$order_status_log->remarks}}</td>
                            </tr>
                            @endforeach
                            <tr>
                    <td style="text-align:right;" colspan="5"><b>Total</b></td>
                    <td style="text-align:right;"><b></b></td>
                </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
 function submitForm(url){
    $('#prowasales').attr('action',url);
    $('#prowasales').submit();
 }
</script>

@endsection