@extends('backend.layouts.staff')

@section('content')
@if(auth()->user()->staff->role->name=='Sales Executive')
    @include('backend.staff_panel.sales_executive_nav')
@elseif(auth()->user()->staff->role->name=='Customer Service Executive')
    @include('backend.staff_panel.customer_service.customer_executive_nav')
@else
    @include('backend.staff_panel.sales_executive_nav')
@endif
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{translate('All Customers')}}</h1>
    </div>
</div>


<div class="card">
    <div class="card-header d-block d-lg-flex">
        <div class="col-md-12">
            <form action="" method="get">
                <div class="row">
                    <div class="col-md-3">
                        <label>Email or name & Enter :</label>
                        <input type="text" class="form-control" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type email or name & Enter') }}">
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
                        <label style="margin-top:35px;">&nbsp;<br></label>
                        <button class="btn btn-sm btn-primary" type="submit">{{ translate('Filter') }}</button>
                        <a href="{{route('customer_bulk_export.index')}}" class="btn btn-sm btn-info">
                            <span class="aiz-side-nav-text">{{translate('Export')}}</span>
                        </a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </form>
        </div>

    </div>
</div>
<div class="card-body">
    <table class="table aiz-table mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>{{translate('Name')}}</th>
                <th>{{translate('Customer Type')}}</th>
                <th>{{translate('Customer ID')}}</th>
                <th style="width: 5px; ">{{translate('Total Order')}}</th>
                <th>{{translate('Email Address')}}</th>

                <th>{{translate('Phone')}}</th>
                <th>{{translate('Birth')}}</th>
                <th>{{translate('Area')}}</th>
                <th>{{translate('Created')}}</th>
                <th>{{translate('Options')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $key => $customer)
            @if ($customer->user != null)
            <tr>
                <td>{{ ($key+1) + ($customers->currentPage() - 1)*$customers->perPage() }}</td>
                <td>@if($customer->user->banned == 1) <i class="fa fa-ban text-danger" aria-hidden="true"></i> @endif {{$customer->user->name}}</td>
                <td>{{$customer->customer_type}}</td>
                <td>

                    <a href="{{route('customer_ledger_details.index')}}?cust_id={{$customer->user_id}}" target="_blank" title="{{ translate('View') }}">{{$customer->customer_id}}</a>
                </td>
                <td >{{count($customer->orders())}}</td>
                <td>{{$customer->user->email}}</td>
                <td>{{$customer->user->phone}}</td>
                   
                <td>
                @if ($customer->dob != null)
                
                {{date('d-m-Y',strtotime($customer->dob))}}
              
                @else
                   @if($customer->user->birth !=null)

                   {{date('d-m-Y',strtotime($customer->user->birth))}}
                   @else
                    N/A
                   @endif
                @endif
               </td>

                <td>{{$customer->areacode}}</td>
                <td>
                    {{date('d-m-Y h:i:A',strtotime($customer->user->created_at))}}
                </td>
                <td class="text-right">

                <a href="{{route('customers.login', encrypt($customer->id))}}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Log in as this Customer') }}">
                        <i class="las la-sign-in-alt"></i>
                    </a>
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    <div class="aiz-pagination">
        {{ $customers->appends(request()->input())->links() }}
    </div>
</div>
</div>


<div class="modal fade" id="confirm-ban">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{translate('Do you really want to ban this Customer?')}}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                <a type="button" id="confirmation" class="btn btn-primary">{{translate('Proceed!')}}</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirm-unban">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{translate('Do you really want to unban this Customer?')}}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                <a type="button" id="confirmationunban" class="btn btn-primary">{{translate('Proceed!')}}</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')
@include('modals.delete_modal')

<div class="modal fade" id="payment_modal">
    <div class="modal-dialog">
        <div class="modal-content" id="modal-content">
            <form action="{{ route('customer.wallet_refund') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title h6">Wallet Payment</h5>
                    <button type="button" class="close" data-dismiss="modal">
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped table-bordered">
                        <tbody>
                            <tr>
                                <td>Wallet Amount</td>
                                <td><strong>TK <span id="wlt_amt"></span></strong></td>
                            </tr>

                        </tbody>
                    </table>

                    <input type="hidden" id="user_id" name="user_id" value="">
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="amount">Amount</label>
                        <div class="col-sm-9">
                            <input type="number" min="0" step="0.01" name="amount" id="amount" value="" class="form-control" required="">
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-primary" type="submit">Pay</button>
                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- due  modal -->

<div class="modal fade" id="due_modal">
    <div class="modal-dialog">
        <div class="modal-content" id="modal-content">
            <form action="{{ route('customer.creadit_due') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title h6">Wallet Payment</h5>
                    <button type="button" class="close" data-dismiss="modal">
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped table-bordered">
                        <tbody>
                            <tr>
                                <td>Wallet Amount</td>
                                <td><strong>TK <span id="due_wlt_amt"></span></strong></td>
                            </tr>

                        </tbody>
                    </table>

                    <input type="hidden" id="due_user_id" name="due_user_id" value="">
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="due_amount">Amount</label>
                        <div class="col-sm-9">
                            <input type="number" min="0" step="0.01" name="due_amount" id="due_amount" value="" class="form-control" required="">
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-primary" type="submit">Pay</button>
                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>



@endsection

@section('script')
<script type="text/javascript">
    function show_payment_modal(id, total) {
        $('#wlt_amt').html(total);
        $('#amount').val(total);
        $('#user_id').val(id);
        $('#payment_modal').modal('show', {
            backdrop: 'static'
        });
    }

    function sort_customers(el) {
        $('#sort_customers').submit();
    }

    function confirm_ban(url) {
        $('#confirm-ban').modal('show', {
            backdrop: 'static'
        });
        document.getElementById('confirmation').setAttribute('href', url);
    }

    function confirm_unban(url) {
        $('#confirm-unban').modal('show', {
            backdrop: 'static'
        });
        document.getElementById('confirmationunban').setAttribute('href', url);
    }
</script>




<script type="text/javascript">
    function creadit_due(id, total) {
        $('#due_wlt_amt').html(total);

        if(total<0){
        $('#due_amount').val (Math.abs (total));
    }
        $('#due_user_id').val(id);
        $('#due_modal').modal('show', {
            backdrop: 'static'
        });
    }

  
</script>
@endsection