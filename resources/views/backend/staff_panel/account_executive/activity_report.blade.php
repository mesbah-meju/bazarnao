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
              @include('backend.staff_panel.account_executive.account_executive_nav')
                <div class="card border-bottom-0">

                    <div class=" card-body">
                    <form id="culexpo" action="{{ route('account_activity_report.index') }}" method="GET">
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
                                <button type="submit" class="btn btn-primary" >{{ translate('Filter') }}</button>
                            </div>
                        </div>
                    </form>
                    <table class="table table-bordered">
                            <tr>
                                <th>Sl</th>
                                <th>paid Date</th>
                                <th>Order ID</th>
                                <th>Name</th>
                                <th>Collection Amount</th>
                                <th>Delivery Name</th>
                                <th>Status</th>
                                
                            </tr>
                            <tbody id="activity_table2">
                                @php $total= 0; @endphp
                                @if(count($delivery_executive_ledger)>0)
                                @foreach($delivery_executive_ledger as $key=>$activity)
                                @php $total += $activity->credit; @endphp
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>
                                    {{$activity->paid_date}}
                                    </td>
                                    <td>
                                    <a href="{{route('all_order_details.show',encrypt($activity->id))}}" target="blank">{{ $activity->order_no}}</a>
                                    </td>
                                  
                                    <td>
                                    {{$activity->name}}
                                    </td>
                                   
                                    <td>
                                    {{$activity->credit}}
                                    </td>
                                    <td>
                                    @foreach(App\Models\Staff::where('user_id',$activity->user_id)->get() as $u)
                                    {{$u->user->name}}
                                    @endforeach
                                    </td>
                                
                                    <td>
                                        {{$activity->status}}
                                    </td>

                                </tr>
                                @endforeach
                                <tfoot>
                                    <td colspan="4">Total</td>
                                    <td style="text-align: left;">{{single_price($total)}}</td>
                                </tfoot>
                                @else
                                <tr id="row_1">
                                    <td colspan="8" style="text-align:center ;">No Data Available</td>
                                    
                                </tr>
                                @endif
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
