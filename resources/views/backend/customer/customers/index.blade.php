@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{translate('All Customers')}}</h1>
		</div>
		<div class="col-md-6 text-md-right">
			<a href="{{ route('customers.create') }}" class="btn btn-circle btn-info">
				<span>{{translate('Add New Customer')}}</span>
			</a>
		</div>
	</div>
</div>


<div class="card">
    <div class="card-header d-block d-lg-flex">
        <div class="col-md-12">
            <form action="{{ route('customers.index') }}" method="get" id="prowasales">
                <div class="row">
                    <div class="col-md-3">
                        <label>Email or name & Enter :</label>
                        <input type="text" class="form-control" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type email or name & Enter') }}">
                    </div>
                    <div class="col-md-2">
                        <label>Start Date :</label>
                        <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                    </div>
                    <div class="col-md-2">
                        <label>End Date :</label>
                        <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                    </div>
                    <div class="col-md-2">
                        <label class="col-form-label">{{ translate('Sort by Customer Type') }} :</label>
                        <select class="form-control" id="customer_type" name="customer_type">
                            <option value=''>Select One</option>
                            <option value="Normal">Normal</option>
                            <option value="Premium">Premium</option>
                            <option value="Corporate">Corporate</option>
                            <option value="Employee">Employee</option>
                            <option value="Retail">Retail</option>
                            <option value="Website">Website</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label style="margin-top:35px;">&nbsp;<br></label>
                        <!-- Submit button for filtering -->
                        <button class="btn btn-sm btn-primary" type="submit" onclick="setFormAction('{{ route('customers.index') }}')">{{ translate('Filter') }}</button>
                        <!-- Button to export to Excel -->
                        <button class="btn btn-sm btn-success" type="button" onclick="submitForm('{{ route('customer_bulk_export.index') }}')">{{ translate('Excel') }}</button>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="card-body bg-white">
    <table class="table aiz-table table-hover mb-0">
        <thead>
            <tr>
                <th data-breakpoints="lg">#</th>
                <th>{{translate('Name')}}</th>
                <th>{{translate('Customer Type')}}</th>
                <th>{{translate('Customer ID')}}</th>
                <th style="width: 5px; ">{{translate('Total Order')}}</th>
                <th data-breakpoints="lg">{{translate('Email Address')}}</th>

                <th>{{translate('Phone')}}</th>
                <th>{{translate('Birth')}}</th>
                <th>{{translate('Area')}}</th>
                <th>{{translate('Created')}}</th>
                <th>{{translate('Purchase Amount')}}</th>
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
                <td>{{single_price($customer->total_sales)}}</td>
                <td class="text-right">

                   
                    <a href="{{route('customers.login', encrypt($customer->id))}}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Log in as this Customer') }}">
                        <i class="las la-sign-in-alt"></i>
                    </a>

                    @if(Auth::user()->user_type == 'admin' || Auth::user()->staff->role->name == 'Manager')
                        @if($customer->user->banned != 1)
                        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm" onclick="confirm_ban('{{route('customers.ban', $customer->id)}}');" title="{{ translate('Ban this Customer') }}">
                            <i class="las la-user-slash"></i>
                        </a>
                        @else
                        <a href="#" class="btn btn-soft-success btn-icon btn-circle btn-sm" onclick="confirm_unban('{{route('customers.ban', $customer->id)}}');" title="{{ translate('Unban this Customer') }}">
                            <i class="las la-user-check"></i>
                        </a>
                       
                        @endif

                        <a href="{{route('customers.edit', $customer->id)}}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Edit') }}">
                            <i class="las la-edit"></i>
                        </a>

                    @if($customer->user->balance>0)
                    <a href="#" class="btn btn-soft-primary btn-icon btn-circle btn-sm" onclick="show_payment_modal('{{$customer->user_id}}','{{$customer->user->balance}}');" title="{{ translate('Pay Now') }}">
                        <i class="las la-money-bill"></i>
                    </a>
                    @endif

                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('customers.destroy', $customer->id)}}" title="{{ translate('Delete') }}">
                        <i class="las la-trash"></i>
                    </a>
                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm" onclick="creadit_due('{{$customer->user_id}}','{{$customer->user->balance}}');" title="{{ translate('Creadit Due') }}">
                    <i class="las la-money-bill-wave-alt"></i>
                    </a>
                    
                   @endif

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

    function setFormAction(url) {
        document.getElementById('prowasales').action = url;
    }

    function submitForm(url) {
        setFormAction(url);
        document.getElementById('prowasales').submit();
    }
</script>

@endsection