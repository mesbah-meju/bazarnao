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
                {{-- Statistics Start here --}}
                <div class="card border-bottom-0">
                    <div class="card-header" id="headingOne">
                        <h5 class="mb-0" style="width:100%">
                            <button class="btn btn-light w-100 text-left" data-toggle="collapse" data-target="#collapseOne"
                                aria-expanded="true" aria-controls="collapseOne">
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
                           
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width:25%">Total Order Qty: </th>
                                        <td style="width:25%">{{ $data['total_order_qty'] }}</td>
                                        <th style="width:30%">Replacement product: </th>
                                        <td style="width:20%">{{ $data['replacement_product'] }}</td>
                                        
                                    </tr>
                                    <tr>
                                        <th style="width:25%">Delivered Qty : </th>
                                        <td style="width:25%">{{ $data['total_delivered_qty'] }}</td>
                                        <th style="width:25%">Achivement: </th>
                                        <td style="width:25%">{{ $data['achivement'] }}%</td>
                                    </tr>
                                    <tr>
                                        <th style="width:25%">Pending Qty : </th>
                                        <td style="width:25%">{{ $data['pending_qty'] }}</td>
                                        <th style="width:25%">Performance: </th>
                                        <td style="width:25%">
                                        {{ $data['performance'] }}
                                        <input type="hidden"  name="customer_achivement" value="{{ $data['performance'] }}%"/>
                                    </td>
                                    </tr>

                                    <tr>
                                    <th style="width:30%">Damage QTY: </th>
                                    <td style="width:20%">{{$data['damage_qty']}}</td>
                                    </tr>
                                   
                                </table>
                               
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Statistics End here --}}


                @include('backend.staff_panel.operation_manager.operation_manager_nav')
                <div class="card border-bottom-0">

                    <div class=" card-body">
                        <form action="{{route('staff.activity_save')}}" method="post">
                            @csrf
                            <table class="table table-bordered">
                                <tr>
                                    <th>Sl</th>
                                    <th style="width:180px">Order No</th>
                                    <th>Name</th>
                                    <th>ID</th>
                                    <th>Address</th>
                                    <th>Mobile</th>
                                    <th>Amount</th>
                                    <th>Delivery Man</th>
                                    <th>Status</th>
                                </tr>
                                <tbody id="activity_table">
                                    @if(count($data['order_daily_activities'])>0)
                                        @foreach($data['order_daily_activities'] as $key=>$activity)

                                        <?php
                                    $shipping_address = json_decode($activity->shipping_address);
                                    if(!empty($activity->user_id)){
                                        $c_id = $activity->customer_id;
                                    }else{
                                        $c_id = $activity->guest_id;
                                    }
                                    
                                    ?>
                                        <tr id="row_{{$key+1}}">
                                            <td>{{$key+1}}</td>
                                            <td>
                                                <input name="order_no[{{$key+1}}]" value="{{$activity->code}}" onkeyup="get_byorderid({{$key+1}})" type="text" class="form-control gtorderid" placeholder="Enter Phone">
                                                <input name="order_id[{{$key+1}}]" id="order_id_{{$key+1}}" value="{{$activity->id}}"  type="hidden" class="form-control " placeholder="Enter Phone">
                                             </td>
                                            <td>
                                                <input name="name[{{$key+1}}]" value="{{!empty($shipping_address->name) ? $shipping_address->name :''}}" type="text" class="form-control" placeholder="Enter Name">
                                             </td>
                                            <td>
                                                <input name="id[{{$key+1}}]" value="{{$c_id }}" type="text" class="form-control" placeholder="Enter ID">
                                             </td>
                                            <td>
                                                <input name="address[{{$key+1}}]" value="{{!empty($shipping_address->address) ?$shipping_address->address:'' }}"  type="text" class="form-control"  placeholder="Enter Address">
                                             </td>
                                            <td>
                                                <input name="phone[{{$key+1}}]" value="{{!empty($shipping_address->phone) ?$shipping_address->phone:'' }}" type="text" class="form-control" placeholder="Enter Phone">
                                             </td>
                                            <td>
                                                <input name="amount[{{$key+1}}]" value="{{$activity->grand_total}}" type="text" class="form-control" placeholder="Enter Amount">
                                             </td>
                                            
                                            <td>
                                            <select class="form-control" name="delivery_man[{{$key+1}}]" id="delivery_man_{{$key+1}}" onchange="update_delivery_boy(this.value,'{{$activity->id}}')">
                                        <option value="">Select Delivery Man</option>
                                        @foreach(\App\Models\Staff::where('role_id','10')->get() as $u)
                                    <option  @if($activity->delivery_boy==$u->user_id) {{'selected'}} @endif value="{{$u->user_id}}">{{$u->user->name}}</option>
                                    @endforeach
                                        </select>

                                             </td>
                                             <td>
                                                <select class="form-control" name="status[{{$key+1}}]" onchange="update_delivery_status(this.value,{{$key+1}})">
                                                @if($activity->delivery_status=='confirmed')
                                                <option @if($activity->delivery_status=='confirmed') selected @endif  value="confirmed">Confirm</option>
                                                 <option @if($activity->delivery_status=='on_delivery') selected @endif  value="on_delivery">On Delivery</option>
                                                @elseif($activity->delivery_status=='on_delivery')
                                                <option @if($activity->delivery_status=='on_delivery') selected @endif  value="on_delivery">On Delivery</option>
                                                @elseif($activity->delivery_status=='delivered')
                                                <option @if($activity->delivery_status=='delivered') selected @endif  value="delivered">Delivered</option>
                                                @endif
                                                </select>
                                             </td>
                                             
                                         </tr>
                                        @endforeach
                                    @else
                                <tr id="row_1">
                                   <td align="center" colspan="9">No Data Found</td>
                                   
                                </tr>
                                @endif
                                </tbody>
                            </table>
                        <!-- <input type="submit" class="btn btn-sm btn-primary pull-right" value="Save Activity"> -->
                    </form>
                    </div>

                </div>
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

        function addRow(){
            var row = $('#activity_table').find('tr').length;
            row++;
            var str = '<tr id="row_'+row+'"><td>'+row+'</td><td><input name="order_no['+row+']" onkeyup="get_byorderid('+row+')" type="text" class="form-control gtorderid" placeholder="Enter Order No"><input name="order_id['+row+']" id="order_id_'+row+'" type="hidden" class="form-control" placeholder="Enter Order No"></td>';
                str += '<td><input name="name['+row+']" type="text" class="form-control" placeholder="Enter Name"></td><td><input name="id['+row+']" type="text" class="form-control" placeholder="Enter ID"></td><td><input name="addree['+row+']" type="text" class="form-control" placeholder="Enter Address"></td>';
                str += '<td><input name="phone['+row+']" type="text" class="form-control" placeholder="Enter Phone"></td><td> <input name="amount['+row+']" type="text" class="form-control" placeholder="Enter Amount"></td>';
                str += '<td><select class="form-control" name="delivery_man['+row+']"><option value="">Select Delivery Man</option>';
                @foreach(\App\Models\Staff::where('role_id','10')->get() as $u)
                str += '<option  value="{{ $u->user->name }}">{{ $u->user->name }}</option>';
                @endforeach
                str += '</select> </td>';

                str += '<td><select  class="form-control" name="status['+row+']"><option value="" onchange="update_delivery_status(this.value,'+row+')">Select One</option><option value="pending">Pending</option><option value="on-delivery">On Delivery</option></select></td><td><a href="javascript:" onclick="removeRow('+row+')" class="btn btn-xs btn-danger"><i class="las la-minus"></i></a></td></tr>';
                $('#activity_table').append(str);
        }
        function removeRow(row){
            var row = $('#row_'+row).remove();
            $('#activity_table').find('tr').each(function(i,v){
                $(v).attr('id','row_'+(i+1));
                $(v).find('td').eq(0).html((i+1));
                $(v).find('td').eq(10).html('<a href="javascript:" onclick="removeRow('+(i+1)+')" class="btn btn-xs btn-danger"><i class="las la-minus"></i></a>');
            })
            
        }

        function get_byorderid(row){
            site_url = get_site_url();
        var token = "{{ csrf_token() }}";
        var ordernumber = $('#row_'+row).find('.gtorderid').val();
        if(ordernumber.length>=10)
        {
            $.ajax({
                    headers:{
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },

                  url: site_url+ '/get_customer_service_order',
                  type: 'post',
                  data: {
                    ordernumber : ordernumber,

                  },

                  success: function(data){
                var shipping = JSON.parse(data.shipping_address)
                $('#row_'+row).find('td').eq(2).find('input').val(shipping.name);
                $('#order_id_'+row).val(data.id);
                $('#row_'+row).find('td').eq(3).find('input').val(data.customer_id);
                $('#row_'+row).find('td').eq(4).find('input').val(shipping.address);
                $('#row_'+row).find('td').eq(5).find('input').val(shipping.phone);
                $('#row_'+row).find('td').eq(6).find('input').val(data.grand_total);
                  }




            })
        }
        }

        function update_delivery_boy(id,order_id){
            $.post('{{ route('orders.update_delivery_boy') }}', {_token:'{{ @csrf_token() }}',order_id:order_id,delivery_boy_id:id}, function(data){
                AIZ.plugins.notify('success', '{{ translate('Delivery boy has been assained') }}');
            });
    }

        function old_update_delivery_status(status,row){
            var order_id = $('#order_id_'+row).val();
            $.post('{{ route('orders.update_delivery_status') }}', {_token:'{{ @csrf_token() }}',order_id:order_id,status:status}, function(data){
                    AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
                });
        }

        function update_delivery_status(status,row){
            var delivery_boy = $('#delivery_man_'+row).val();
            var order_id = $('#order_id_'+row).val();
            if(delivery_boy == ''){
                alert('Please select Delivery Boy');
                return true;
            }else if(status=='on_delivery'){
                alert('Only From Online Order Scan');
            }else{
                $.post('{{ route('orders.update_delivery_status') }}', {_token:'{{ @csrf_token() }}',order_id:order_id,status:status,delivery_boy:delivery_boy}, function(data){
                    AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
                    
                });
            }
            
        }


    </script>
@endsection
