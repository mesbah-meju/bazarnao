@extends('backend.layouts.staff')

@section('content')
@include('backend.staff_panel.operation_manager.operation_manager_nav')
@php
    $refund_request_addon = \App\Models\Addon::where('unique_identifier', 'refund_request')->first();
 $user_name = auth()->user()->name;
@endphp
<div class="card">
      <form class="" action="" method="GET">
        <div class="card-header row gutters-5">
          <div class="col text-center text-md-left">
            <h5 class="mb-md-0 h6">{{ translate('All Orders') }}</h5>
          </div>
          <div class="col-lg-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="delivery_status" id="delivery_status">delivered 
                    <option value="">{{translate('Filter by Delivery Status')}}</option>
                    <option value="pending" @if ($delivery_status == 'Pending') selected @endif>{{translate('Pending')}}</option>
                    <option value="confirmed" @if ($delivery_status == 'Confirmed') selected @endif>{{translate('Confirmed')}}</option>
                    <option value="on_delivery" @if ($delivery_status == 'on_delivery') selected @endif>{{translate('On delivery')}}</option>
                    <option value="delivered" @if ($delivery_status == 'Delivered') selected @endif>{{translate('Delivered')}}</option>
                    <option value="Cancel" @if ($delivery_status == 'Cancel') selected @endif>{{translate('Canceled')}}</option>
                </select>
            </div>

            <div class="col-lg-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="payment_status" id="payment_status">
                    <option value="">{{translate('Filter by Payment Status')}}</option>
                    <option value="paid" @if ($payment_status == 'paid') selected @endif>{{translate('Paid')}}</option>
                    <option value="unpaid" @if ($payment_status == 'unpaid') selected @endif>{{translate('Un-Paid')}}</option>
                </select>
            </div>
          <div class="col-lg-2">
              <div class="form-group mb-0">
                  <input type="text" class="aiz-date-range form-control" value="{{ $date }}" name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
              </div>
          </div>

          <div class="col-lg-2">
            <div class="form-group mb-0">
              <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type Order code & hit Enter') }}">
            </div>
          </div>
          <div class="col-auto">
            <div class="form-group mb-0">
              <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
            </div>
          </div>
        </div>
    </form>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('Date') }}</th>
                    <th>{{ translate('Order Code') }}</th>
                    <th data-breakpoints="md">{{ translate('Customer ID') }}</th>
                    <th data-breakpoints="md">{{ translate('Customer') }}</th>
                    <th data-breakpoints="md">{{ translate('Amount') }}</th>
                    <th data-breakpoints="md">{{ translate('Delivery Status') }}</th>
                    <th data-breakpoints="md">{{ translate('Status') }}</th>
                    <th data-breakpoints="md">Warehouse</th>
                    <th data-breakpoints="md">Delivery Man</th>
					<th data-breakpoints="lg">Status Change Date</th>
                    @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                        <!-- <th>{{ translate('Refund') }}</th> -->
                    @endif
                    <!-- <th class="text-right" width="18%">{{translate('options')}}</th> -->
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $key => $order)
            @php
                                $status = $order->delivery_status;
                              
                            @endphp
          
             @php
            if(!empty(\App\Models\Customer::where('user_id', $order->user_id)->first()))
            $customer_id = \App\Models\Customer::where('user_id', $order->user_id)->first()->customer_id;
            else
            $customer_id = '';
            @endphp
                    <tr>
                        <td>
                            {{ ($key+1) + ($orders->currentPage() - 1)*$orders->perPage() }}
                        </td>
                        <td>
                            {{ date('d-m-Y',$order->date) }}
                        </td>
                        <td>
                            <a href="{{route('all_orders.show', encrypt($order->id))}}" target="_blank" title="{{ translate('View') }}">{{ $order->code }}</a>
                        </td>
                        <td>
                            @if ($order->user != null)
                    <a href="{{ route('customer_ledger.index') }}?customer_id={{$order->user_id}}" target="_blank" title="{{ translate('View') }}">{{ $customer_id }} </a>
                        @else
                        {{ $order->guest_id }}
                        @endif
                        </td>
                        <td>
                            @if ($order->user != null)
                                {{ $order->user->name }}
                            @else
                                Guest
                            @endif
                        </td>
                        <td>
                            {{ single_price($order->grand_total) }}
                        </td>
                        <td>
						{{ translate($status) }}
							
                        </td>
                        
                        <td>
                            @if ($order->payment_status == 'paid')
                                <span class="badge badge-inline badge-success">{{translate('Paid')}}</span>
                            @else
                                <span class="badge badge-inline badge-danger">{{translate('Unpaid')}}</span>
                            @endif
                        </td>
                        <td>{{ !empty($order->warehouse) ?getWearhouseName($order->warehouse) : ''}}</td>
                        <td>{{!empty($order->delivery_boy) ? getUserNameByuserID($order->delivery_boy) : ''}}</td>
                        <td><span style="font-size:11px;color:green">Confirm Date : @if(!empty($order->confirm_date)) {{ date('d/m/Y h:i:s A',strtotime($order->confirm_date)) }}@endif</br>
						On Delivery Date : @if(!empty($order->on_delivery_date)) {{ date('d/m/Y h:i:s A',strtotime($order->on_delivery_date)) }}@endif <br>
						Delivered Date : @if(!empty($order->delivered_date)) {{ date('d/m/Y h:i:s A',strtotime($order->delivered_date)) }}@endif <br>
						Cancel Date : @if(!empty($order->cancel_date)) {{ date('d/m/Y h:i:s A',strtotime($order->cancel_date)) }}@endif<br>
                        Cancel Reason: @if(!empty($order->reason_of_cancel)) {{ $order->reason_of_cancel }}@endif<br>
                        Canceled By: @if(!empty($order->cancel_user_id)) {{ getUserNameByuserID($order->cancel_user_id) }}@endif
                    </span>
                    </td>
                        @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                            <!-- <td>
                                @if (count($order->refund_requests) > 0)
                                    {{ count($order->refund_requests) }} {{ translate('Refund') }}
                                @else
                                    {{ translate('No Refund') }}
                                @endif
                            </td> -->
                        @endif
                        
                        <!-- <td class="text-right">
                          @if($status == 'pending' || $status == 'confirmed')
                        <a href="{{route('staff_order_edit.index', $order->id)}}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Edit') }}">
                                   <i class="las la-edit"></i>
                               </a>
                               @endif
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('staff_order_show.show', encrypt($order->id))}}" title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                            
                         @if(Auth::user()->user_type == 'admin')
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('orders.destroy', $order->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                         @endif
                        </td> -->
                    
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $orders->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">

    </script>
@endsection
