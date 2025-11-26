@extends('backend.layouts.staff')
<style>
 @media screen and (max-width: 480px) {
    #activity_table{
        width: 1000px;
  }
  #activity_table2{
        width: 1000px;
  }
}
</style>
 <link href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/2.4.85/css/materialdesignicons.css" rel="stylesheet" />

 @section('content')

    <div class="col-lg-12">
        <div id="accordion">
            {{-- Statistics Start here --}}
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
                    {{-- <div class="col-md-3 ml-3">
                        <div class="card">
                            <div class="card-header text-white" style="background-color:#A73986">
                                <h4 class="mb-0">Warehouse</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    @foreach($data['warehousearray'] as $warehouse)
                                    <h5><li>{{ $warehouse }}</li></h5>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div> --}}
                    <div class="row card-body">
                        <div class="col-md-3">
                            <label><b>Employee Name : {{ Auth::user()->name }}</b></label> <br>
                            <label><b>Employee ID : {{ Auth::user()->id }}</b></label>
                        </div>
                        <div class="col-md-9">
                        <div style="overflow-x:auto;">
                       
                        <table class="table table-bordered">
                                <tr>
                                    <th style="width:25%">Total Order Qty : </th>
                                    <td style="width:25%">{{ $data['total_order_qty'] }}</td>
                                    <th style="width:25%">Achivement: </th>
                                    <td style="width:25%">
                                    {{ $data['achivement'] }}%
                                    <input type="hidden"  name="customer_achivement" value="{{ $data['achivement'] }}%"/>
                                
                                </td>
                                </tr>
                                <tr>
                                    <th style="width:25%">Delivered Qty : </th>
                                    <td style="width:25%">{{ $data['delivered_qty'] }}</td>
                                    <th style="width:25%">Performance: </th>
                                    <td style="width:25%">{{ $data['performance'] }}</td>
                                </tr>
                                <tr>
                                    <th style="width:25%">Pending Qty : </th>
                                    <td style="width:25%">{{ $data['pending_qty'] }}</td>
                                </tr>
                                <tr>
                                    <th style="width:25%">Total cash amount: </th>
                                    <td style="width:25%">{{ single_price($data['cash_balance']) }}</td>
                                </tr>
                            </table>
                           
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Statistics End here --}}

            @include('backend.staff_panel.delivery_executive.delivery_executive_nav')

            <div class="card border-bottom-0">
                <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="activity_table">
                    <tbody >
                            <tr>
                                <th>Sl</th>
                                <th>Order ID</th>
                                <th>Name</th>
                                <th>Customer ID</th>
                                <th>Address</th>
                                <th>Mobile</th>
                                <th>Area</th>
                                <th>Amount</th>
                                <th>Cash Collection</th>
                                <th>Status</th>
                            </tr>
                           
                                @if(count($data['order_daily_activities'])>0)
                                @foreach($data['order_daily_activities'] as $key=>$activity)
                                <?php
                                    $shipping_address = json_decode($activity->shipping_address);
                                    if(!empty($activity->user_id)){
                                        $c_id = $activity->customer_id;
                                        $area = $activity->areaname;
                                    }else{
                                        $c_id = $activity->guest_id;
                                        $area = $shipping_address->area;
                                    }
                                    
                                    ?>
                                <tr id="erow_{{$key+1}}">
                                    <td>{{$key+1}}</td>
                                    <td>
                                        <input name="order_no[{{$key+1}}]" value="{{$activity->code}}" onkeyup="get_byorderid('{{$key+1}}')" type="text" class="form-control gtorderid" placeholder="Order ID" readonly>
                                        <input name="order_id[{{$key+1}}]" id="order_id_{{$key+1}}" value="{{$activity->id}}"  type="hidden" class="form-control " placeholder="Enter Phone">
                                    </td>
                                    <td>
                                        <input name="name[{{$key+1}}]" value="{{ !empty($shipping_address->name) ?  $shipping_address->name : '' }}" type="text" class="form-control" placeholder="Enter Name" readonly>
                                    </td>
                                    <td>
                                        <input name="id[{{$key+1}}]" value="{{$c_id}}" type="text" class="form-control" placeholder="Enter ID" readonly>
                                    </td>
                                    <td>
                                        <input name="address[{{$key+1}}]"  value="{{ !empty($shipping_address->address) ? $shipping_address->address : '' }}" type="text" class="form-control"  placeholder="Enter Address" readonly>
                                    </td>
                                    <td>
                                        <input name="phone[{{$key+1}}]" value="{{ !empty($shipping_address->phone) ? $shipping_address->phone : '' }}" type="text" class="form-control" placeholder="Enter Phone" readonly>
                                    </td>

                                    <td>
                                        <input name="area[{{$key+1}}]"  value="{{$area}}" type="text" class="form-control" placeholder="Enter Area" readonly>
                                    </td>

                                    <td>
                                        <input name="amount[{{$key+1}}]" id="grand_total{{$key+1}}" value="{{$activity->grand_total}}" type="text" class="form-control" placeholder="Enter Amount" readonly>
                                    </td>

                                    <td>
                                        <input name="cash_collection[{{$key+1}}]" id="cash_collection{{$key+1}}" value="{{$activity->cash_collection}}" type="text" class="form-control" placeholder="Enter cash collection" required>
                                    </td>
                                    <td>
                                        <select class="form-control" name="status[{{$key+1}}]" onchange="update_delivery_status(this.value,'{{$activity->id}}',{{$key+1}})">
                                        @if($activity->delivery_status=='on_delivery')
                                        <option value="">Change status</option>
                                            <option @if($activity->delivery_status=='delivered') {{'selected'}} @endif value="delivered">Delivered</option>
                                            @endif
                                        </select>
                                    </td>
                                    
                                </tr>
                                @endforeach
                                @else
                                <tr id="erow_1">
                                    <td style="text-align: center;" colspan="10">No Data Found</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                        </div>
                    
                </div>

            <div class="container">
                <h3 style="text-align:center"> For Delivery Executive Ledger</h3>
                   
                <div class="table-responsive">
                   <table class="table table-bordered" id="activity_table">
                   <tbody>
                            <tr>
                                <th style="width: 5%">Sl</th>
                                <th style="width: 25%">Order ID</th>
                                <th style="width: 20%"> Name </th>
                                <th style="width: 10%">Collection Amount</th>
                                <th style="width: 10%">Paid To</th>
                                <th style="width: 10%">Date</th>
                                <th style="width: 10%">Status</th>
                               
                            </tr>
                            
                                @if(count($data2['delivery_executive_ledger'])>0)
                                @foreach($data2['delivery_executive_ledger'] as $key=>$activity)
                                <tr id="row2_{{$key+1}}">
                                    <td>{{$key+1}}</td>
                                    <td>
                                        <input type="hidden" name="order_row_id[{{$key+1}}]" value="{{$activity->id}}">
                                        <input name="order_no[{{$key+1}}]" value="{{$activity->order_no}}" type="number" class="form-control gtorderid" placeholder="Enter Order ID" maxlength="10" readonly required>
                                    </td>
                                  
                                    <td>
                                        <input name="name[{{$key+1}}]" value="{{$activity->name}}" type="text" class="form-control" placeholder="Enter Name" readonly>
                                    </td>
                                   
                                    <td>
                                        <input name="credit[{{$key+1}}]" value="{{$activity->debit}}" type="number" class="form-control" placeholder="Enter Payment Amount" readonly>
                                    </td>
                                    <td>
                                        <select class="form-control" name="note[{{$key+1}}]"  onchange="paidTo(this.value,'{{$activity->id}}')">
                                        <option value="">Select One</option>
                                        @foreach(\App\Models\Staff::where('role_id','15')->get() as $u)
                                        <?php $ware_h_info = unserialize($u->warehouse_id);
                                            if(in_array($warehousearray[0],$ware_h_info)){
                                         ?>
                                     <option @if($activity->note==$u->user_id) {{'selected'}} @endif @if(!empty($activity->note)) {{'disabled'}} @endif value="{{$u->user_id}}">{{$u->user->name}}</option>
                                    <?php } ?>
                                    @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input name="date[{{$key+1}}]" value="{{$activity->date}}" type="date" class="form-control" readonly>
                                    </td>

                                    <td>
                                        <select class="form-control" name="status[{{$key+1}}]">
                                       @if($activity->status == 'Paid')
                                            <option value="{{$activity->status}}">{{$activity->status}}</option>
                                       @else
                                       <option value="Pending">Pending</option>
                                       @endif    
                                        </select>
                                    </td>
                                </tr>
                                @endforeach
                                
                                @else
                                <tr id="erow_1">
                                    <td style="text-align: center;" colspan="10">No Data Found</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                        </div>
                    
            </div>
        </div>
    </div>

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

    function update_delivery_status_old(status,order_id,key){
        let  cash_collection = $('#cash_collection'+key).val();

        
        let amount = $('#grand_total'+key).val();
        if(cash_collection == ''){
            alert('Please Insert Cash Collection');
            return true;
         }else{
            if(Number(cash_collection) > Number(amount)){
                alert('Over Amount Not Acceptable');
                return true;
            }else{
                $.post('{{ route('orders.product_stock_qty_check') }}',
               {_token:'{{ @csrf_token() }}',
               order_id:order_id,status:status},
                function(data){
                  if(data.message == 'true'){
                    AIZ.plugins.notify('warning', data.product+' Stock Qty Not Enough For Delivery');
                    return false;
                  }else{
                    $.post('{{ route('orders.update_delivery_status') }}', {_token:'{{ @csrf_token() }}',order_id:order_id,status:status,cash_collection:cash_collection}, function(data){
                    AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
                    location.reload();
                });

              }

                });
            }
        }
 
        }

        function paidTo(paid_to,id){
           if(paid_to !== ''){
            let confrrmation =  confirm( 'Are you sure?');
            if (confrrmation == true) {
                $.post('{{ route('orders.update_paid_to') }}', {_token:'{{ @csrf_token() }}',paid_to:paid_to,id:id}, function(data){
                AIZ.plugins.notify('success', '{{ translate('Paid To Selected SuccessFully')}}');
                location.reload();
            });

                 }
           }
    }

    function update_delivery_status(status, order_id, key) {
        let cash_collection = $('#cash_collection' + key).val();
        let amount = $('#grand_total' + key).val();

        if (cash_collection == '') {
            alert('Please Insert Cash Collection');
            return true;
        } else {
            if (Number(cash_collection) > Number(amount)) {
                alert('Over Amount Not Acceptable');
                return true;
            } else {
                $.post('{{ route('orders.product_stock_qty_check') }}',
               {_token:'{{ @csrf_token() }}',
               order_id:order_id,status:status},
                function(data){
                  if(data.message == 'false'){
                    AIZ.plugins.notify('warning', data.product+'Stock Qty Not Enough for Delivery');
                    return false;
                  }else if(data.message == 'warehouse'){
                    AIZ.plugins.notify('warning', 'Warehouse is not selected');
                    return false;
                  }
                  else{
                    $.post('{{ route('orders.update_delivery_status') }}', {
                        _token: '{{ @csrf_token() }}',
                        order_id: order_id,
                        status: status,
                        cash_collection: cash_collection
                    }, function(data) {
                        if (data.message == 'true') {
                            AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
                            location.reload();
                        } else if(data.message == 'false') {
                            AIZ.plugins.notify('warning', data.product + ' Stock Qty Not Enough for Delivery');
                            location.reload();
                        }
                    });
              }
            });
            }
        }
    }



</script>
@endsection