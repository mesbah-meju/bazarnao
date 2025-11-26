@extends('backend.layouts.staff')

@section('content')
<div class="row gutters-10">
    <div class="col-lg-12">
        <div id="accordion">
            <div class="card border-bottom-0">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0" style="width:100%">
                        <button class="btn btn-light w-100 text-left" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <i class="mdi mdi-chevron-up float-right"></i>
                            Statistics of : {{ auth()->user()->name }}
                        </button>
                    </h5>
                </div>
                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="row card-body">
                        <div class="col-md-3">
                            <label><b>Employee Name : {{ auth()->user()->name }}</b></label> <br>
                            <label><b>Employee ID : {{ auth()->user()->id }}</b></label>
                        </div>
                    </div>
                </div>
            </div>

            @include('backend.staff_panel.account_executive.account_executive_nav')

            <div class="card border-bottom-0">
                <div class="card-body">
                    <h3 style="text-align:center">Delivery Executive Payment list</h3>
                    <form id="prowasales" action="{{ route('staff.dashboard') }}" method="get">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label class="col-form-label">{{ __('Sort by Delivery Name') }}:</label>
                                <select id="demo-ease" class="aiz-selectpicker select2" name="user_id" data-live-search="true">
                                    <option value=''>{{ __('All') }}</option>
                                    @foreach (DB::table('staff')->join('users', 'users.id', 'staff.user_id')->where('role_id', 10)->orderBy('users.name', 'asc')->get() as $staff)
                                        <option @if(request('user_id') == $staff->user_id) selected @endif value="{{ $staff->user_id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="col-form-label">{{ __('Sort by Status') }}:</label>
                                <select id="demo-ease" class="aiz-selectpicker select2" name="status" data-live-search="true">
                                    <option value=''>{{ __('All') }}</option>
                                    @foreach (DB::table('delivery_executive_ledger')->select('status')->distinct()->get() as $salesType)
                                        <option @if(request('status') == $salesType->status) selected @endif value="{{ $salesType->status }}">{{ $salesType->status }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <br>
                                <div class="d-flex">
                                    <button class="btn btn-sm btn-primary" type="submit">{{ translate('Filter') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table class="table table-bordered">
                        <tr>
                            <th>Sl</th>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Collection Amount</th>
                            <th>Delivery Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        <tbody id="activity_table2">
                            @if(count($data2['delivery_executive_ledger']) > 0)
                                @foreach($data2['delivery_executive_ledger'] as $key => $activity)
                                    @if($activity->order_paid_amount == null || $activity->order_paid_amount == 0.00)
                                    <tr id="row2_{{ $key + 1 }}">
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <input name="order_no[{{ $key + 1 }}]" value="{{ $activity->order_no }}" type="number" class="form-control gtorderid" placeholder="Enter Order ID" maxlength="10" readonly>
                                        </td>
                                        <td>
                                            <input name="date[{{ $key + 1 }}]" value="{{ $activity->date }}" type="date" class="form-control" placeholder="Enter date" readonly>
                                        </td>
                                        <td>
                                            <input name="name[{{ $key + 1 }}]" value="{{ $activity->name }}" type="text" class="form-control" placeholder="Enter Name" readonly>
                                        </td>
                                        <td>
                                            <input name="credit[{{ $key + 1 }}]" value="{{ $activity->credit }}" type="number" class="form-control" placeholder="Enter Payment Amount" readonly>
                                        </td>
                                        <td>
                                            <select class="form-control" name="note[{{ $key + 1 }}]" disabled>
                                                @foreach(\App\Models\Staff::where('user_id', $activity->user_id)->get() as $u)
                                                    <option @if($activity->user_id == $u->user_id) selected @endif value="{{ $u->user_id }}">{{ $u->user->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" name="status[{{ $key + 1 }}]" disabled>
                                                <option @if($activity->status == 'Pending') selected @endif value="Pending">Pending</option>
                                                <option @if($activity->status == 'Paid') selected @endif value="Paid">Paid</option>
                                            </select>
                                        </td>
                                        <td>
                                            @if($activity->status == 'Pending')
                                                <!-- <a href="{{ route('delivery_payment_paid.index', $activity->id) }}" class="btn btn-xs btn-info" onclick="return confirm('Are you sure?')">Paid</a> -->
                                                <a href="javascript:" 
                                                    class="btn btn-xs btn-info" 
                                                    onClick="return customer_received('<?php echo $activity->order_id; ?>', '<?php echo $activity->customer_id; ?>', '<?php echo $activity->credit; ?>');">
                                                    Payment Received
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            @else
                                <tr id="row_1">
                                    <td colspan="8" style="text-align:center;">No Data Available</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
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
    function toggleChevron(e) {
        $(e.target)
            .prev('.card-header')
            .find("i.mdi")
            .toggleClass('mdi-chevron-down mdi-chevron-up');
    }

    $('#accordion').on('hidden.bs.collapse', toggleChevron);
    $('#accordion').on('shown.bs.collapse', toggleChevron);

    function customer_received(order_id, customer_id, delivery_man_collection) {
            $('#common-modal .modal-title').html('');
            $('#common-modal .modal-body').html('');

            var title = 'Customer Received';

            $.post('{{ route("customer-receive.orderwise") }}', {
                _token      : AIZ.data.csrf, 
                order_id    : order_id,
                customer_id : customer_id,
                delivery_man_collection : delivery_man_collection
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
