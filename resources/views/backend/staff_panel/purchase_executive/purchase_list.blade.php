@extends('backend.layouts.staff')
<style>
    tr,
    th,
    td {
        padding: 3px !important;
    }

    th {
        background: #AE3C86;
        color: #fff;
        font-weight: bold
    }

    li.nav-item {
        width: 100%;
    }

    .navbar-nav {
        width: 100%;
    }
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/2.4.85/css/materialdesignicons.css"
    rel="stylesheet" />

@section('content')
    <div class="row gutters-10">
        <div class="col-lg-12">

            <div id="accordion">
              @include('backend.staff_panel.purchase_executive.purchase_executive_nav')
                <div class="card border-bottom-0">
<a class="mt-3" href="{{route('add_purchase_for_purchase_executive.index')}}"><button  class="btn btn-info float-right">Add Purchase</button></a>
                    <div class=" card-body">
                    <form id="culexpo" action="{{ route('purchase_list_for_purchase_executive.index') }}" method="GET">
                        <div class="form-group row">
                            
                            <div class="col-md-3">
                        <label>Start Date :</label>
                            <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                            
                    </div>
                    <div class="col-md-3">
                        <label>End Date :</label>
                        <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                            
                    </div>
                           
                            <div class="col-md-3 mt-4">
                                <button class="btn btn-primary" onclick="submitForm ('{{ route('purchase_list_for_purchase_executive.index') }}')">{{ translate('Filter') }}</button>
                            </div>
                        </div>
                    </form>
                    <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th data-breakpoints="md">{{ translate('Date') }}</th>
                    <th data-breakpoints="md">{{ translate('Purchase No') }}</th>
                    
                    <th data-breakpoints="md">{{ translate('Supplier') }}</th>
                    <th data-breakpoints="md">{{ translate('Total') }}</th>
                    <th data-breakpoints="md">{{ translate('Status') }}</th>
                    <th class="text-right" width="18%">{{translate('options')}}</th>
                </tr>
            </thead>
            <tbody>
         
                @foreach ($all_purchase as $key => $order)
                    <tr>
                        <td>
                        {{ ($key+1)  }}
                        </td>
                        <td>
                            {{ $order->date }}
                        </td>
                        <td>
                        <a  href="{{route('purchase_orders_view', $order->id)}}" title="{{ translate('View') }}">
                        {{ $order->purchase_no }}
                        </a>
                           
                        </td>
                        <td>
                        {{ $order->name }}
                        </td>
                        <td>
                            {{ single_price($order->total_value) }}
                        </td>

                   
                        <td>
                        @if($order->purstatus == 2)
                           @php  
                           echo 'Approved'
                           @endphp
                        @else
                        @php
                        echo "Pending" 
                        @endphp
                        @endif
                        </td>
                        
                        <td class="text-right">
                        @if($order->purstatus == 2)
                       
                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('purchase_orders_view', $order->id)}}" title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                        </a>
                       
                        @else
                      

                        <a href="{{route('puracher_edit', $order->id)}}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Edit') }}">
                        <i class="las la-edit"></i>
                        </a>
                        
                        <a href="{{route('purchase_delete.index', $order->id)}}" onclick="return confirm('Are you sure?')" class="btn btn-soft-danger btn-icon btn-circle btn-sm"  title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                        </a>
                        @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
         
                    </div>

                </div>
            </div>
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
