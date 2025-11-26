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
    .form-control {
    padding: 10px 2px !important;
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
                                aria-expanded="false" aria-controls="collapseOne">
                                <i class="mdi mdi-chevron-up float-right"></i>
                                Statistics of : {{ auth()->user()->name }}
                            </button>
                        </h5>
                    </div>
  

                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                        {{-- <!-- <div class="col-md-3 ml-3">
                            <div class="card">
                                <div class="card-header text-white" style="background-color:#A73986">
                                    <h4 class="mb-0">Warehouse:</h4>
                                    <ul class="list-unstyled mb-0">
                                        @foreach($data['warehousearray'] as $warehouse)
                                        <li>{{ $warehouse }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div> --> --}}

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
                            <div class="col-md-12">
                            <table class="table table-bordered">
                                    <tr>
                                        <th style="width:15%">Total Order Qty : </th>
                                        <td style="width:15%">{{ $data['total_order_qty'] }}</td>
                                        <th style="width:10%">Sales Target: </th>
                                        <td style="width:10%">{{ $data['target'] }}</td>
                                        <th style="width:15%">Sales Achivement: </th>
                                        
                                        <td style="width:15%">
                                        {{ $data['achivement'] }}%
                                        <input type="hidden"  name="sales_achivement" value="{{ $data['achivement'] }}%" />
                                       </td>

                                    </tr>
                                    <tr>
                                        <th style="width:15%">Total Sales Amount : </th>
                                        <td style="width:15%">{{ $data['total_sales_amount'] }}</td>
                                        <th style="width:25%">New Customer Target: </th>
                                        <td style="width:15%">{{ $data['terget_customer'] }}</td>
                                        <th style="width:21%">Achivement New Customer: </th>

                                        <td style="width:15%">
                                        {{ $data['customer_achivement']}}%
                                        <input type="hidden"  name="customer_achivement" value="{{ $data['achivement'] }}%" />
                                    </td>
                                    
                                    </tr>
                                    <tr>
                                        <th style="width:15%">Total Due : </th>
                                        <td style="width:15%">
                                            {{ $data['all_time_due'] }}
                                            <input type="hidden"  name="total_due" value="{{ $data['total_due'] }}" />
                                        </td>
                                        <th style="width:15%">Performance: </th>
                                        <td style="width:15%">{{ $data['performance'] }}</td>
                                      </tr>
                                    <tr>
                                        <th style="width:22%">New Customer Created : </th>
                                        <td style="width:15%">{{ $data['new_customer'] }}</td>
                                        <th style="width:15%">Recovery Target: </th>
                                        <td style="width:15%">{{ $data['recovery_target'] }}</td>                                    
                                       
                                    </tr>
                                    <tr>
                                        <th style="width:15%">Total Customer : </th>
                                        <td style="width:15%">{{ $data['total_customer'] }}</td>
                                    </tr>

                                    <tr>
                                        <th style="width:18%">Total POS Sales Amount : </th>
                                        <td style="width:15%">{{ $data['total_POS_sales_amount'] }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Statistics End here --}}
                @include('backend.staff_panel.customer_service.customer_executive_nav')

                
                <div class="card border-bottom-0">

                         <!-- form1 start -->
                    
                <form action="{{route('staff.customer_service_activity_save')}}" id="customer_service_activity_save" method="post">
                            @csrf
                        <table class="table table-bordered">
                            <tr>
                                <th>Sl</th>
                                <th>Order NO</th>
                                <th>Name</th>
                                <th>Customer ID</th>
                                <th>Exe. Name</th>
                                <th>Mobile No</th>
                                <th>Address</th>
                                <th>Amount</th>
                                <th>Warehouse</th>
                                <th>Status</th>
                                
                            </tr>
                            <tbody id="activity_table1">
                                @if(count($data1['customerServiceOrder'])>0)
                                    @foreach($data1['customerServiceOrder'] as $key=>$activity)
                                    <?php
                                    $shipping_address = json_decode($activity->shipping_address);
                                    if(!empty($activity->user_id)){
                                        $c_id = $activity->customer_id;
                                    }else{
                                        $c_id = $activity->guest_id;
                                    }
                                    $staff_name = getUsernameBycustomerstaffId($activity->staff_id);
                                    ?>
                                    <tr id="row_{{$key+1}}">
                                        <td>{{$key+1}}</td>
                                        <td>
                                            <input  name="order_id[{{$key+1}}]" value="{{$activity->id}}"  type="hidden" class="form-control" placeholder="Enter OrderNo">
                                            <input  name="order_no[{{$key+1}}]" value="{{$activity->code}}" onkeyup="getorder_id({{$key+1}})" type="text" class="form-control order_no" placeholder="Enter OrderNo" readonly>
                                            
                                         </td>
                                        <td>
                                            <input name="name[{{$key+1}}]" value="{{!empty($shipping_address->name) ? $shipping_address->name :''}}" type="text" class="form-control" placeholder="Enter Name" readonly>
                                         </td>

                                        <td>
                                            <input name="customer_id[{{$key+1}}]" value="{{$c_id}}" type="text" class="form-control" placeholder="Enter ID" readonly>
                                         </td>
                                        <td>
                                            <input name="executive_name[{{$key+1}}]" value="{{$staff_name}}" type="text" class="form-control" placeholder="Enter ID" readonly>
                                         </td>
                                         <td>
                                            <input name="phone[{{$key+1}}]" value="{{!empty($shipping_address->phone) ?$shipping_address->phone:'' }}" type="number" class="form-control" placeholder="Enter Phone" readonly>
                                         </td>
                                       
                                        <td>
                                            <input name="address[{{$key+1}}]" value="{{!empty($shipping_address->address) ?$shipping_address->address:'' }}" type="text" class="form-control" title="{{!empty($shipping_address->address)?$shipping_address->address:''}}" placeholder="Enter Address" readonly>
                                         </td>
                                        
                                         <td>
                                            <input name="amount[{{$key+1}}]" value="{{$activity->grand_total}}" type="number" class="form-control" placeholder="Enter Amount" readonly>
                                         </td>
                                       
                                        <td>
                                         <select class="form-control" name="warehouse[{{$key+1}}]" id="warehouse_id_{{$key+1}}" onchange="update_warehouse(this.value,'{{$activity->id}}')">
                                        <option value="">Select One</option>
                                        @foreach(\App\Models\Warehouse::all() as $warehousees)
                                        <option  @if($activity->warehouse == $warehousees->id) {{'selected'}} @endif value="{{$warehousees->id}}" >{{ $warehousees->name}}</option>
                                        @endforeach
                                        </select>
                                         </td>

                                         <td>
                                            <select class="form-control" name="status[{{$key+1}}]" onchange="update_delivery_status(this.value,'{{$activity->id}}',{{$key+1}})">
                                            @if($activity->delivery_status=='pending')
                                             <option @if($activity->delivery_status=='pending') selected @endif value="pending">Pending</option>
                                             <option @if($activity->delivery_status=='confirmed') selected @endif value="confirmed">Confirm</option>
                                             <option @if($activity->delivery_status=='cancel') selected @endif value="cancel">Cancel</option>
                                             @elseif($activity->delivery_status=='confirmed' || $activity->delivery_status=='cancel')
                                             <option @if($activity->delivery_status=='pending') selected @endif value="pending">Pending</option>
                                             <option @if($activity->delivery_status=='confirmed') selected @endif value="confirmed">Confirm</option>
                                             <option @if($activity->delivery_status=='cancel') selected @endif value="cancel">Cancel</option>
                                             @elseif($activity->delivery_status=='on_delivery')
                                             <option @if($activity->delivery_status=='on_delivery') selected @endif  value="on_delivery">On Delivery</option>
                                             <option @if($activity->delivery_status=='cancel') selected @endif value="cancel">Cancel</option>
                                             @elseif($activity->delivery_status=='delivered')
                                                <option @if($activity->delivery_status=='delivered') selected @endif  value="delivered">Delivered</option>
                                             @endif
                                            </select>
                                         </td>
                                         
                                     </tr>
                                    @endforeach
                                @else
                            <tr id="row_1">
                               <td colspan="10">No Data Found</td>
                               
                            </tr>
                            @endif
                            </tbody>
                            
                        </table>
                        <!-- <input type="submit"   class="btn btn-sm btn-primary pull-right" value="Save Activity"> -->
                    </form>
                    <!-- form1 end -->


                    <!-- form2 start -->
                    <div class="row card-body">
                        <h3>Customer Support</h3>
                        <form action="{{route('staff.activity_save')}}" method="post" id="activity_save">
                            @csrf
                        <table class="table table-bordered">
                            <tr>
                                <th>Sl</th>
                                <th>Mobile No</th>
                                <th>Name</th>
                                <th>ID</th>
                                <th>Area</th>
                                <th>Address</th>
                                <th>Comment</th>
                                <th>Complain</th>
                                <th>Order Confirm</th>
                                <th>Order ID</th>
                                <th>Order No</th>
                                <th><a href="javascript:" onclick="addRow2()" class="btn btn-xs btn-success"><i class="las la-plus"></i></a></th>
                            </tr>
                            <tbody id="activity_table">
                                @if(count($data['daily_activities'])>0)
                                    @foreach($data['daily_activities'] as $key=>$activity)
                                    <tr id="row2_{{$key+1}}">
                                        <td>{{$key+1}}</td>
                                        <td>
                                            <input name="phone[{{$key+1}}]" value="{{$activity->phone}}" onkeyup="get_byphone({{$key+1}})" type="text" class="form-control phone" placeholder="Enter Phone">
                                            <input  name="type[{{$key+1}}]" value="1"  type="hidden" class="form-control">
                                         </td>
                                        <td>
                                            <input name="name[{{$key+1}}]" value="{{$activity->name}}" type="text" class="form-control" placeholder="Enter Name">
                                         </td>
                                        <td>
                                            <input name="id[{{$key+1}}]" value="{{$activity->customer_id}}" type="text" class="form-control" placeholder="Enter ID">
                                         </td>
                                        <td>
                                            <input name="area[{{$key+1}}]" value="{{$activity->area}}" type="text" class="form-control" placeholder="Enter Area">
                                         </td>
                                        <td>
                                            <input name="address[{{$key+1}}]" value="{{$activity->address}}" type="text" class="form-control" title="{{$activity->address}}" placeholder="Enter Address">
                                         </td>
                                        <td>
                                            <input name="comment[{{$key+1}}]" value="{{$activity->comment}}" type="text" class="form-control" placeholder="Enter Comment">
                                         </td>
                                        <td>
                                            <input name="complain[{{$key+1}}]" value="{{$activity->complain}}" type="text" class="form-control" placeholder="Enter Complain">
                                         </td>
                                        <td>
                                            <select class="form-control" name="order_confirm[{{$key+1}}]">
                                             <option value="">Select One</option>
                                             <option @if($activity->order_confirm=='yes') {{'selected'}} @endif value="yes">Yes</option>
                                             <option @if($activity->order_confirm=='no') {{'selected'}} @endif value="no">No</option>
                                            </select>
                                         </td>
                                        <td>
                                            <input name="order_id[{{$key+1}}]" value="{{$activity->order_id}}" type="text" class="form-control" placeholder="Enter Order ID">
                                         </td>
                                        <td>
                                            <input name="order_no[{{$key+1}}]" value="{{$activity->order_no}}" type="text" class="form-control" placeholder="Enter Order No.">
                                         </td>
                                         <td><a href="javascript:" onclick="removeRow2({{$key+1}})" class="btn btn-xs btn-danger"><i class="las la-minus"></i></a></td>
                                     </tr>
                                    @endforeach
                                @else
                            <tr id="row2_1">
                               <td>1</td>
                               <td>
                                   <input name="phone[1]" type="text" onkeyup="get_byphone(1)" class="form-control phone" placeholder="Enter Phone">
                                   <input  name="type[1]" value="1"  type="hidden" class="form-control">
                                </td>
                               <td>
                                   <input name="name[1]" type="text" class="form-control" placeholder="Enter Name">
                                </td>
                               <td>
                                   <input name="id[1]" type="text" class="form-control" placeholder="Enter ID">
                                </td>
                               <td>
                                   <input name="area[1]" type="text" class="form-control" placeholder="Enter Area">
                                </td>
                               <td>
                                   <input name="address[1]" type="text" class="form-control" placeholder="Enter Address">
                                </td>
                               <td>
                                   <input name="comment[1]" type="text" class="form-control" placeholder="Enter Comment">
                                </td>
                               <td>
                                   <input name="complain[1]" type="text" class="form-control" placeholder="Enter Complain">
                                </td>
                               <td>
                                   <select class="form-control" name="order_confirm[1]">
                                    <option value="">Select One</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                   </select>
                                </td>
                               <td>
                                   <input name="order_id[1]" type="text" class="form-control" placeholder="Enter Order ID">
                                </td>
                               <td>
                                   <input name="order_no[1]" type="text" class="form-control" placeholder="Enter Order No.">
                                </td>
                            </tr>
                            @endif
                            </tbody>
                        </table>
                        <input type="submit" class="btn btn-sm btn-primary pull-right" value="Save Activity">
                    </form>
                    </div>
                    <!-- form2 end  -->
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="modal fade" id="deliveryboy-modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-zoom" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h6 class="modal-title" id="exampleModalLabel">{{ translate('Reason of cancel')}}</h6>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
              <div class="modal-body">
                  <div class="p-3">
                      <div class="row">
                          
                          <div class="col-md-12">
                          <label>{{ translate('Reason of cancel')}}</label>
                          <input type="hidden" id="cancel_order_id">
                              <textarea name="reason_of_cancel" id="reason_of_cancel" class="form-control" required></textarea>
                          </div>
                      </div>
                      
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" id="save_payment" onclick="save_delivery_man()" class="btn btn-primary">{{  translate('Save') }}</button>
              </div>
      </div>
  </div>
</div>
@endsection
@section('script')
    <script type="text/javascript">
        function activity_save(id){
            $('#'+id).submit();
        }
        function toggleChevron(e) {
            $(e.target)
                .prev('.card-header')
                .find("i.mdi")
                .toggleClass('mdi-chevron-down mdi-chevron-up');
        }

        $('#accordion').on('hidden.bs.collapse', toggleChevron);
        $('#accordion').on('shown.bs.collapse', toggleChevron);

    
        function addRow2(){
            var row = $('#activity_table').find('tr').length;
            row++;
            var str = '<tr id="row2_'+row+'"><td>'+row+'</td><td><input onkeyup="get_byphone('+row+')" name="phone['+row+']" type="text" class="form-control phone" placeholder="Enter Phone"></td> <input  name="type['+row+']" value="1"  type="hidden" class="form-control">';
                str += '<td><input name="name['+row+']" type="text" class="form-control" placeholder="Enter Name"></td><td><input name="id['+row+']" type="text" class="form-control" placeholder="Enter ID"></td><td><input name="area['+row+']" type="text" class="form-control" placeholder="Enter Area"></td>';
                str += '<td><input name="address['+row+']" type="text" class="form-control" placeholder="Enter Address"></td><td><input name="comment['+row+']" type="text" class="form-control" placeholder="Enter Comment"></td><td> <input name="complain['+row+']" type="text" class="form-control" placeholder="Enter Complain"></td>';
                str += '<td><select class="form-control" name="order_confirm['+row+']"><option value="">Select One</option><option value="yes">Yes</option><option value="no">No</option></select></td>';
                str += '<td><input name="order_id['+row+']" type="text" class="form-control" placeholder="Enter Order ID"></td>';
                str += '<td><input name="order_no['+row+']" type="text" class="form-control" placeholder="Enter Order No"></td><td><a href="javascript:" onclick="removeRow2('+row+')" class="btn btn-xs btn-danger"><i class="las la-minus"></i></a></td></tr>';
                $('#activity_table').append(str);
        }
        

        function removeRow2(row){
            $('#row2_' + row).remove();
            $('#activity_table').find('tr').each(function(index, element){
                var newRowId = 'row2_' + (index + 1);
                $(element).attr('id', newRowId);
                $(element).find('td').eq(0).html(index + 1);
                $(element).find('td').eq(11).html('<a href="javascript:" onclick="removeRow2(' + (index + 1) + ')" class="btn btn-xs btn-danger"><i class="las la-minus"></i></a>');
            });
        }


        
function get_byphone(row){
    
    let token = "{{ csrf_token() }}";
    let phone = $('#row2_'+row).find('.phone').val();

      if(phone.length>=11){
          $.ajax({
              headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 },
                 url:'/get_customer_by_phone',
                 type: "POST",
                 data: {
                phonenumber:phone,
                },
  
              success: function (data) {
                $('#row2_'+row).find('td').eq(2).find('input').val(data.name);
                $('#row2_'+row).find('td').eq(3).find('input').val(data.customer_id);
                $('#row2_'+row).find('td').eq(4).find('input').val(data.areaname);
                $('#row2_'+row).find('td').eq(5).find('input').val(data.address);
                
              }
          });
      }
  
  }

  // for form2 end


  function update_delivery_status(status,order_id,key){
            let warehouse_id = $('#warehouse_id_'+key).val();

            if(status=='confirmed'){
                if(warehouse_id == ''){
                alert('Please select warehouse');
                return true;
            }
            }
            
            if(status=='on_delivery'){
                alert('Only From Online Order Scan');
            }
            if(status=='cancel'){
                $('#cancel_order_id').val(order_id);
              $('#deliveryboy-modal').modal('show');
            }else{
            $.post('{{ route('orders.update_delivery_status') }}', {_token:'{{ @csrf_token() }}',order_id:order_id,status:status}, function(data){
                    AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
                    location.reload();
                });
            }
        }

        function save_delivery_man(){
          let order_id = $('#cancel_order_id').val();
            let status = 'cancel';
            let reason_of_cancel = $('#reason_of_cancel').val();
            
            $.post('{{ route('orders.update_delivery_status') }}', {_token:'{{ @csrf_token() }}',order_id:order_id,status:status,reason_of_cancel:reason_of_cancel}, function(data){
                    AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
                    $('#deliveryboy-modal').modal('hide')
                    location.reload().setTimeOut(500);
                });
        }

        function update_warehouse(id,order_id){
            
                $.post('{{ route('orders.update_warehouse') }}', {_token:'{{ @csrf_token() }}',order_id:order_id,warehouse_id:id}, function(data){
                    AIZ.plugins.notify('success', '{{ translate('Warehouse has been updated') }}');
                });
        }
    
    </script>
@endsection
