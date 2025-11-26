@extends('backend.layouts.app')

@section('content')
@php
    $refund_request_addon = App\Models\Addon::where('unique_identifier', 'refund_request')->first();
@endphp
<div class="card">
      <form class="" action="" method="GET">
        <div class="card-header row gutters-5">
          <div class="col text-center text-md-left">
            <h5 class="mb-md-0 h6"><a href="{{ Route('transfer.create')}}" class="btn btn-info">{{ translate('Add Transfer') }}</a></h5>
          </div>
          <div class="col-lg-2">
              <div class="form-group mb-0">
                  <input type="text" class="aiz-date-range form-control" value="" name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
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
                    <th data-breakpoints="md">{{ translate('Product') }}</th>
                    <th data-breakpoints="md">{{ translate('From wearhouse') }}</th>
                    <th data-breakpoints="md">{{ translate('To wearhouse') }}</th>
                    <th data-breakpoints="md">{{ translate('Qty') }}</th>
                	<th data-breakpoints="md">{{ translate('Unit Price') }}</th>
                    <th data-breakpoints="md">{{ translate('date') }}</th>
                    <th data-breakpoints="md">{{ translate('Approved Date') }}</th>
                    <th data-breakpoints="md">{{ translate('Status') }}</th>
                    <th data-breakpoints="md">{{ translate('Transfer ID') }}</th>
                    <th class="text-right" width="15%">{{translate('options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transfer as $key => $order)
                    <tr>
                        <td>
                            {{ ($key+1) + ($transfer->currentPage() - 1)*$transfer->perPage() }}
                        </td>
                        <td>
                            {{ $order->product->name }}
                        </td>
                        <td>
                        {{ getWearhouseName($order->from_wearhouse_id) }} 
                        </td>
                        <td>
                        {{ getWearhouseName($order->to_wearhouse_id) }}
                        </td>
                        <td>
                            {{ $order->qty }}
                        </td>
                    
                    	<td>
                            {{ $order->unit_price }}
                        </td>
                    
                        <td>
                            {{ $order->date }}
                        </td>
                        <td>
                            {{ $order->approved_date }}
                        </td>
                        <td>
                            {{ $order->status }}
                        </td>
                        <td>
                            {{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                        </td>
                        
                        
                        <td class="text-right">
                            @if($order->status =='Pending')
                           <a href="{{route('transfer.edit', $order->id)}}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Edit') }}">
                                   <i class="las la-edit"></i>
                            </a>
                            <a class="btn btn-soft-success btn-icon btn-circle btn-sm" href="{{route('transfer.approve', $order->id)}}" title="{{ translate('Approve') }}">
                                <i class="las la-check-circle"></i>
                            </a>
                        
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('transfer.destroy', $order->id)}}" title="{{ translate('Cancel') }}">
                                <i class="las la-trash"></i>
                            </a>
                          @endif 
                            
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $transfer->appends(request()->input())->links() }}
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
