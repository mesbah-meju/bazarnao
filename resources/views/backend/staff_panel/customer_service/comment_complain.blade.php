@extends('backend.layouts.staff')

@section('content')
@if(auth()->user()->staff->role->name=='Sales Executive')
@include('backend.staff_panel.sales_executive_nav')
@elseif(auth()->user()->staff->role->name=='Customer Service Executive')
@include('backend.staff_panel.customer_service.customer_executive_nav')
@elseif(auth()->user()->staff->role->name=='Delivery Executive')
@include('backend.staff_panel.delivery_executive.delivery_executive_nav')
@elseif(auth()->user()->staff->role->name=='Operation Manager')
@include('backend.staff_panel.operation_manager.operation_manager_nav')
@else
@include('backend.staff_panel.sales_executive_nav')
@endif
<!-- Basic Data Tables -->
<!--===================================================-->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{translate('Comment Complain')}}</h5>
    </div>
    <div class="col-md-12">
        <form action="" method="get">
            <div class="row">

                <div class="col-md-3">
                    <label>Start Date :</label>
                    <input type="date" name="start_date" class="form-control" value="{{ \Carbon\Carbon::parse($start_date)->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label>End Date :</label>
                    <input type="date" name="end_date" class="form-control" value="{{ \Carbon\Carbon::parse($end_date)->format('Y-m-d') }}">
                </div>

                <div class="col-md-3">
                    <label style="margin-top:35px;">&nbsp;<br></label>
                    <button class="btn btn-sm btn-primary" type="submit">{{ translate('Filter') }}</button>
                </div>
                <div class="clearfix"></div>
            </div>
        </form>
    </div>
    <div class="card-body">
        <table class="table aiz-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Executive Name</th>
                    <th>{{translate('Order ID')}}</th>
                    <th>{{translate('Order No')}}</th>
                    <th>{{translate('Customer Name')}}</th>
                    <th>{{translate('Customer Id')}}</th>
                    <th>Comment</th>
                    <th>Complain</th>
                    <th>Total Order</th>
                </tr>
            </thead>
            <tbody>
                @foreach($comment_complain as $key => $comment_com)
                <tr>
                    <td>{{ ($key+1) }}</td>
                    <td>{{$comment_com->created_at}}</td>
                    <td>{{executive_name($comment_com->user_id)}}</td>
                    <td>{{ $comment_com->order_id }}</td>
                    <td>{{ $comment_com->order_no }}</td>
                    <td>{{ $comment_com->name }}</td>
                    <td>
                        <a href="{{route('customer_ledger_details.index')}}?cust_id={{$comment_com->cuser_id}}" target="_blank" title="{{ translate('View') }}">{{$comment_com->customer_id}}</a>
                    </td>
                    <td>{{$comment_com->comment}}</td>
                    <td>{{$comment_com->complain}}</td>
                    <td>{{total_order($comment_com->customer_id)}}</td>

                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
@endsection