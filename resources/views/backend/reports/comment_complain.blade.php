@extends('backend.layouts.app')

@section('content')

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
                    <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d', strtotime($start_date)) }}">
                </div>
                <div class="col-md-3">
                    <label>End Date :</label>
                    <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d', strtotime($end_date)) }}">
                </div>
                <div class="col-md-3">
                    <label style="margin-top:35px;">&nbsp;<br></label>
                    <button class="btn btn-sm btn-primary" type="submit">{{ translate('Filter') }}</button>
                    <button class="btn btn-sm btn-info" type="button" onclick="printDiv()">{{ translate('Print') }}</button>
                    <a href="{{ route('customers_comments_complain',[
                                'type' => 'excel',
                                'start_date' => request()->input('start_date'),
                                'end_date' => request()->input('end_date'),
                                'search' => request()->input('search'),
                            ]) }}" target="_blank" class="btn btn-sm btn-success" type="button">Excel</a>
                </div>
                <div class="clearfix"></div>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="printArea">
            <div class="text-center">
                <h3 class=" text-center">{{translate('Comment Complain')}}</h3>
            </div>
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
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $comment_com->created_at->format('Y-m-d') }}</td>
                            <td>{{ executive_name($comment_com->user_id) }}</td>
                            <td>{{ $comment_com->order_id }}</td>
                            <td>{{ $comment_com->order_no }}</td>
                            <td>{{ $comment_com->name }}</td>
                            <td>
                                <a href="{{ route('customer_ledger_details.index', ['cust_id' => $comment_com->cuser_id]) }}" target="_blank" title="{{ translate('View') }}">
                                    {{ $comment_com->customer_id }}
                                </a>
                            </td>
                            <td>{{ $comment_com->comment }}</td>
                            <td>{{ $comment_com->complain }}</td>
                            <td>{{ total_order($comment_com->customer_id) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
