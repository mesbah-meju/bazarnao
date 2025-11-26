@extends('backend.layouts.app')
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
                                @if(count($pending_order)>0)
                                    @foreach( $pending_order as $key=>$activity)
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
                                             <!-- <option @if($activity->delivery_status=='cancel') selected @endif value="cancel">Cancel</option> -->
                                             @elseif($activity->delivery_status=='confirmed' || $activity->delivery_status=='cancel')
                                             <option @if($activity->delivery_status=='pending') selected @endif value="pending">Pending</option>
                                             <option @if($activity->delivery_status=='confirmed') selected @endif value="confirmed">Confirm</option>
                                             <!-- <option @if($activity->delivery_status=='cancel') selected @endif value="cancel">Cancel</option> -->
                                             @elseif($activity->delivery_status=='on_delivery')
                                             <option @if($activity->delivery_status=='on_delivery') selected @endif  value="on_delivery">On Delivery</option>
                                             <!-- <option @if($activity->delivery_status=='cancel') selected @endif value="cancel">Cancel</option> -->
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


                  
                </div>
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
                str += '<td><input name="order_id['+row+']" type="text" class="form-control" placeholder="Enter Order ID"></td><td><a href="javascript:" onclick="removeRow2('+row+')" class="btn btn-xs btn-danger"><i class="las la-minus"></i></a></td></tr>';
                $('#activity_table').append(str);
        }
        function removeRow2(row){
            var row = $('#row2_'+row).remove();
            $('#activity_table').find('tr').each(function(i,v){
                $(v).attr('id','row2_'+(i+1));
                $(v).find('td').eq(0).html((i+1));
                $(v).find('td').eq(10).html('<a href="javascript:" onclick="removeRow2('+(i+1)+')" class="btn btn-xs btn-danger"><i class="las la-minus"></i></a>');
            })
            
        }


        
function get_byphone(row){
    
    site_url = get_site_url();
   
    var token = "{{ csrf_token() }}";
    var phone = $('#row2_'+row).find('.phone').val();

      if(phone.length>=11){
          $.ajax({
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              url: site_url + '/get_customer_by_phone',
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
