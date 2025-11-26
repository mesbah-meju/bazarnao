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
        <h5 class="mb-0 h6">{{translate('Refund Request All')}}</h5>
    </div>
    <div class="col-md-12">
            <form action="" method="get">
                <div class="row">
                    
                    <div class="col-md-3">
                        <label>Start Date :</label>
                        <input type="date" name="start_date" class="form-control" value="{{$start_date}}">

                    </div>
                    <div class="col-md-3">
                        <label>End Date :</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $end_date}}">

                    </div>

                    <div class="col-md-3">
                    <label>Filter By Warehouse :</label>
                    <select class="form-control" name="warehouse" id="warehouse">
                    <option value="">Select One</option>
                    @foreach(\App\Models\Warehouse::all() as $warehousees)
                    <option  value="{{$warehousees->id}}" >{{ $warehousees->name}}</option>
                    @endforeach
                    </select>
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
                    <th>{{translate('Order Code')}}</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>{{translate('Product')}}</th>
                    <th>{{translate('Quantity')}}</th>
                    <th>{{translate('Price')}}</th>
                    <th>{{translate('Refund Status')}}</th>
                    {{-- <th>{{translate('Type')}}</th> --}}
                    <th class="text-right">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($refunds as $key => $refund)

                <?php
                    $shipping_address = json_decode($refund->shipping_address);
                    $name = $shipping_address->name;
                    $phone = $shipping_address->phone;
                    $address = $shipping_address->address;
                ?>
                    <tr>
                        <td>{{ ($key+1) + ($refunds->currentPage() - 1)*$refunds->perPage() }}</td>
                        <td>
                            @if($refund->order != null)
                            <a href="{{route('all_order_details.show',encrypt($refund->order->id))}}" target="blank">{{ $refund->order->code}}</a>
                            @else
                                {{ translate('Order deleted') }}
                            @endif
                        </td>
                        <td>{{$name}}</td>
                        <td>{{$phone}}</td>
                        <td>{{$address}}</td>
                        <td>
                            @if ($refund->orderDetail != null && $refund->orderDetail->product != null)
                              <a href="{{ route('product', $refund->orderDetail->product->slug) }}" target="_blank" class="media-block">
                                <div class="row">
                                  <div class="col-auto">
                                    <img src="{{ uploaded_asset($refund->orderDetail->product->thumbnail_img) }}" alt="Image" class="size-50px">
                                  </div>
                                  <div class="col">
                                    <div class="media-body text-truncate-2">{{ $refund->orderDetail->product->getTranslation('name') }}</div>
                                  </div>
                                </div>
                              </a>
                            @endif
                        </td>
                        <td>
                            @if ($refund->orderDetail != null)
                                {{$refund->refund_qty}}
                            @endif
                        </td>
                        <td>
                            @if ($refund->orderDetail != null)
                                {{single_price($refund->refund_amount)}}
                            @endif
                        </td>
                        
                   
                        <td>
                            @if ($refund->refund_status == 0)
                              <span class="badge badge-inline badge-success">{{translate('Pending')}}</span>
                            @elseif($refund->refund_status == 1)
                            <span class="badge badge-inline badge-success">{{translate('Approved')}}</span>
                            @elseif($refund->refund_status == 2)
                            <span style="cursor: pointer;" onclick="reject_refund_request('{{ route('reject_reason_show', $refund->id )}}', '{{$refund->id}}', '{{ $refund->order->code }}')" class="badge badge-inline badge-danger">{{translate('Rejectd')}}</span>
                            @elseif($refund->refund_status == 3)
                            <span class="badge badge-inline badge-info">{{translate('Processed')}}</span>
                            @elseif($refund->refund_status == 4)
                            <span class="badge badge-inline badge-success">{{translate('Delivered')}}</span>
                            @elseif($refund->refund_status == 6)
                            <span class="badge badge-inline badge-warning">{{translate('Resolve Pending')}}</span>
                            
                              @else
                              <span class="badge badge-inline badge-warning">{{translate('Resolved')}}</span>
                            @endif
                        </td>
                        {{-- <td>
                            @if ($refund->refund_type == 1)
                              <span class="badge badge-inline badge-success">{{translate('Refund')}}</span>
                            @else 
                              <span class="badge badge-inline badge-success">{{translate('Return')}}</span>
                            @endif
                        </td> --}}
                        <td class="text-right d-flex justify-content-end">
                        @if(auth()->user()->staff->role->name=='Customer Service Executive')
                        @if($refund->refund_status == 0)

                            <div class="dropdown mr-2">
                                <button class="btn btn-soft-primary btn-sm dropdown-toggle" type="button" id="approvalDropdown{{ $refund->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ translate('Select') }}
                                </button>
                                <div class="dropdown-menu" aria-labelledby="approvalDropdown{{ $refund->id }}">
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="approve_refund('{{ $refund->id }}', 'refund')">{{ translate('Approve Refund') }}</a>
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="approve_refund('{{ $refund->id }}', 'return')">{{ translate('Approve Return') }}</a>
                                </div>
                            </div>
                            
                            <a class="btn btn-soft-danger btn-icon btn-circle btn-sm mr-2" onclick="reject_refund_request('{{ route('reject_reason_show', $refund->id )}}', '{{$refund->id}}', '{{ $refund->order->code }}')"  title="{{ translate('Reject Refund Request') }}">
                                <i class="las la-trash"></i>
                            </a>
                            @endif
                            <a href="{{ route('reason_show', $refund->id) }}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('View Reason') }}">
                                <i class="las la-eye"></i>
                            </a>

                            @elseif(auth()->user()->staff->role->name=='Operation Manager')
                            @if($refund->refund_status == 1 || $refund->refund_status == 4 && $refund->refund_type == 1)
                            <a class="btn btn-soft-success btn-icon btn-circle btn-sm" onclick="refund_request_money('{{ $refund->id }}')" title="{{ translate('Refund Now') }}">
                                <i class="las la-forward"></i>
                            </a>
                            @endif
                            @if($refund->refund_status == 1)
                            <a class="btn btn-soft-danger btn-icon btn-circle btn-sm" onclick="select_delivery_man('{{ $refund->id }}')"  title="{{ translate('Select Delivery Man') }}">
                                <i class="las la-user"></i>
                            </a>
                            @endif
                            <a href="{{ route('reason_show', $refund->id) }}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('View Reason') }}">
                                <i class="las la-eye"></i>
                            </a>
                            @else
                            @if($refund->refund_status == 3)
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" onclick="delivered_refund_request_modal('{{$refund->id}}')" title="{{ translate('Delivered') }}">
                            <i class="lar la-check-circle"></i>
                            </a>
                            @endif
                        @endif

                        </td>
                    </tr>

                    @endforeach
            </tbody>
        </table>
        <div class="clearfix">
            <div class="pull-right">
                {{ $refunds->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')
  <div class="modal fade reject_refund_request" id="modal-basic">
    	<div class="modal-dialog">
    		<div class="modal-content">
            <form class="form-horizontal member-block" action="{{ route('reject_refund_request')}}" method="POST">
                @csrf
                <input type="hidden" name="refund_id" id="refund_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Reject Refund Request !')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Order Code')}}</label>
                        <div class="col-md-9">
                          <input type="text" value="" id="order_id" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Reject Reason')}}</label>
                        <div class="col-md-9">
                            <textarea type="text" name="reject_reason" id="reject_reason" rows="5" class="form-control" placeholder="{{translate('Reject Reason')}}" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Close')}}</button>
                    <button type="submit" class="btn btn-success">{{translate('Submit')}}</button>
                </div>
            </form>
      	</div>
    	</div>
    </div>


    <div class="modal fade approved_refund_request_modal" id="approved_refund_request_modal">
    	<div class="modal-dialog">
    		<div class="modal-content">
            <form class="form-horizontal member-block" action="{{route('approve_refund_request')}}" method="POST">
                @csrf
                <input type="hidden" name="type_refund_id" id="type_refund_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Approved Refund Request !')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                <div class="row">
                          <div class="col-md-4">
                              <label>{{ translate('Select Refund Type')}}</label>
                          </div>
                          <div class="col-md-8">
                              <select class="form-control" name="refund_type" id="refund_type">
                                <option value="">Select Type</option>
                                <option value="Return">Return</option>
                                <option value="Refund">Refund</option>
                                
                              </select>
                          </div>
                      </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Close')}}</button>
                    <button type="submit" class="btn btn-success">{{translate('Submit')}}</button>
                </div>
            </form>
      	</div>
    	</div>
    </div>
    <div class="modal fade delivered_refund_request_modal" id="delivered_refund_request_modal">
        <?php
           $total_debit = App\Models\Customer_ledger::where('customer_id', $refund->user_id)->sum('debit');
           $total_credit = App\Models\Customer_ledger::where('customer_id', $refund->user_id)->sum('credit'); 
           $customer_balance = $total_debit - $total_credit;
           
        ?>
    	<div class="modal-dialog">
    		<div class="modal-content">
            <form class="form-horizontal member-block" action="{{route('delivered_refund_request')}}" method="POST">
                @csrf
                <input type="hidden" name="delivered_refund_id" id="delivered_refund_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Delivered Refund Request !')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                <div class="row">
                        <div class="col-md-4">
                            <label>{{ translate('Return QTY')}}</label>
                        </div>
                        <div class="col-md-8">
                            <input class="form-control" name="return_qty" value="{{$refund->refund_qty}}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label>{{ translate('Return Amount')}}</label>
                        </div>
                        <div class="col-md-8">
                            <input class="form-control" name="return_amount_balance" value="{{$refund->refund_amount}}" readonly>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-md-4">
                            <label>{{ translate('Balance')}}</label>
                        </div>
                        <div class="col-md-8">
                            <input class="form-control" name="customer_balance" value="{{$customer_balance}}" readonly>
                        </div>
                    </div> -->
                    <div class="row">
                        <div class="col-md-4">
                            <label>{{ translate('Payment Amount')}}</label>
                        </div>
                        <div class="col-md-8">
                            <input class="form-control" name="return_amount" value="{{$refund->refund_amount}}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label>{{ translate('Return Remarks')}}</label>
                        </div>
                        <div class="col-md-8">
                            <input class="form-control" name="return_remark">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Close')}}</button>
                    <button type="submit" class="btn btn-success">{{translate('Submit')}}</button>
                </div>
            </form>
      	</div>
    	</div>
    </div>


    
                    <!-- modal start  -->

<div class="modal fade" id="deliveryboy-modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-zoom" role="document">
      <div class="modal-content">
      <form class="form-horizontal member-block" action="{{route('delivery_man_refund_request')}}" method="POST">
                @csrf
                <input type="hidden" name="delivery_boy_refund_id" id="delivery_boy_refund_id" value="">
          <div class="modal-header">
              <h6 class="modal-title" id="exampleModalLabel">{{ translate('Select Delivery Man')}}</h6>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
              <div class="modal-body">
                  <div class="p-3">
                      <div class="row">
                          <div class="col-md-2">
                              <label>{{ translate('Select Delivery Man')}}</label>
                          </div>
                          <div class="col-md-10">
                              <select name="delivery_boy" class="form-control" id="delivery_boy" required>
                                <option value="">Select Delivery Man</option>
                                @foreach(App\Models\Staff::where('role_id','10')->get() as $u)
                                <option value="{{$u->user->id}}">{{$u->user->name}}</option>
                                @endforeach
                              </select>
                          </div>
                      </div>
                      
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="submit" id="save_payment"  class="btn btn-primary">{{  translate('Save') }}</button>
              </div>
              </form>
      </div>
  </div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
        function update_refund_approval(el){
            $.post('{{ route('vendor_refund_approval') }}',{_token:'{{ @csrf_token() }}', el:el}, function(data){
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Approval has been done successfully') }}');
                }
                else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function approve_refund(el, refund_type){
            $.post('{{ route('approve_refund_request') }}', {_token: '{{ @csrf_token() }}', el: el, refund_type: refund_type}, function(data){
                if (data == 1) {
                    location.reload();
                    AIZ.plugins.notify('success', '{{ translate('Refund has been sent successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function refund_request_money(el){
            if (confirm('Are you Sure ?') == false) {
            return;
            }
            $.post('{{ route('refund_request_money_by_admin') }}',{_token:'{{ @csrf_token() }}', el:el}, function(data){
                if (data == 1) {
                    location.reload();
                    AIZ.plugins.notify('success', '{{ translate('Refund has been sent successfully') }}');
                }
                else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function reject_refund_request(url, id, order_id){
          $.get(url, function(data){
              $('.reject_refund_request').modal('show');
              $('#refund_id').val(id);
              $('#order_id').val(order_id);
              $('#reject_reason').html(data);
          });
         }


         function select_delivery_man(id){
          
            $('#delivery_boy_refund_id').val(id);
              $('#deliveryboy-modal').modal('show');

          
         }
         function approved_refund_request_modal(id){
            
          $('#type_refund_id').val(id);
              $('#approved_refund_request_modal').modal('show');

          
         }
         function delivered_refund_request_modal(id){
            
          $('#delivered_refund_id').val(id);
              $('#delivered_refund_request_modal').modal('show');

          
         }
    </script>
@endsection
