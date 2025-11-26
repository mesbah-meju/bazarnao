@extends('frontend.layouts.app')

@section('content')

<section class="py-5">
    <div class="container">
        <div class="d-flex align-items-start">
            @include('frontend.inc.user_side_nav')
            <div class="aiz-user-panel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{ translate('Purchase History') }}</h5>
                    </div>
                    @if (count($orders) > 0)
                        <div class="card-body">
                            <table class="table aiz-table mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ translate('Code')}}</th>
                                        <th data-breakpoints="md">{{ translate('Date')}}</th>
                                        <th>{{ translate('Amount')}}</th>
                                        <th data-breakpoints="md">{{ translate('Delivery Status')}}</th>
                                        <th data-breakpoints="md">{{ translate('Payment Status')}}</th>
                                        <th class="text-right">{{ translate('Options')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $key => $order)
                                        @if (count($order->orderDetails) > 0)
                                            <tr>
                                                <td>
                                                    <a href="#{{ $order->code }}" onclick="show_purchase_history_details({{ $order->id }})">{{ $order->code }}</a>
                                                </td>
                                                <td>{{ date('d-m-Y', $order->date) }}</td>
                                                <td>
                                                    {{ single_price($order->grand_total) }}
                                                </td>
                                                <td>
                                                    {{ translate(ucfirst(str_replace('_', ' ', $order->orderDetails->first()->delivery_status))) }}
                                                    @if($order->delivery_viewed == 0)
                                                        <span class="ml-2" style="color:green"><strong>*</strong></span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($order->payment_status == 'paid')
                                                        <span class="badge badge-inline badge-success">{{translate('Paid')}}</span>
                                                    @else
                                                        <span class="badge badge-inline badge-danger">{{translate('Unpaid')}}</span>
                                                    @endif
                                                    @if($order->payment_status_viewed == 0)
                                                        <span class="ml-2" style="color:green"><strong>*</strong></span>
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    @if ($order->orderDetails->first()->delivery_status == 'pending' && $order->payment_status == 'unpaid')
                                                        <a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('orders.destroy', $order->id)}}" title="{{ translate('Cancel') }}">
                                                           <i class="las la-trash"></i>
                                                       </a>
                                                    @endif
                                                    @if ($order->orderDetails->first()->delivery_status == 'delivered' && $order->payment_status == 'paid')
                                                        <a id="received_{{$order->id}}" href="javascript:void(0)" class="btn btn-soft-primary btn-icon btn-circle btn-sm" onclick="orderReceived({{ $order->id }})" title="{{ translate('Order Received') }}">
                                                           <i class="las la-check"></i>
                                                       </a>
                                                    @endif


                                                    @if ($order->payment_status != 'paid')
                                                        <a id="payment_{{$order->id}}" target='_blank' href="{{route('order_payment.show', $order->id)}}" class="btn btn-soft-primary btn-icon btn-circle btn-sm"  title="Payment">
                                                           <i class="las la-check"></i>
                                                       </a>
                                                    @endif


                                                    <a href="javascript:void(0)" class="btn btn-soft-info btn-icon btn-circle btn-sm" onclick="show_purchase_history_details({{ $order->id }})" title="{{ translate('Order Details') }}">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                    <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="{{ route('invoice.download', $order->id) }}" title="{{ translate('Download Invoice') }}">
                                                        <i class="las la-download"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="aiz-pagination">
                                	{{ $orders->links() }}
                          	</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('modal')
    @include('modals.delete_modal')

    <div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div id="order-details-modal-body">

                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div id="payment_modal_body">

                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
        $('#order_details').on('hidden.bs.modal', function () {
            location.reload();
        })



        function orderReceived(order_id){
            if(confirm('Are you sure ? You received this order successfully !!')==true){
            var status = 'received';
            $.post('{{ route('orders.update_delivery_status') }}', {_token:'{{ @csrf_token() }}',order_id:order_id,status:status}, function(data){
                $('#received_'+order_id).hide()
                AIZ.plugins.notify('success', '{{ translate('Order Received Successfully') }}');
            });
        }
        }

    </script>

@endsection
