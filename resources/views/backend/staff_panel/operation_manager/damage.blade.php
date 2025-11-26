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
               

            @include('backend.staff_panel.operation_manager.operation_manager_nav')
                
                <div class="card border-bottom-0">
            <div class=" card-body">
            <a class="btn btn-info mb-3" href="{{ route('damage.create')}}">Add Damage</a>
            <form id="culexpo" action="" method="GET">
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
                                <th>Date</th>
                                <th>Product</th>
                                <th>Wearhoue</th>
                                <th>Qty</th>
                                <th>Amount</th>
                                <th>Remarks</th>
                                <th>Status</th>
                                <th>Action</th>
                                
                            </tr>
                            <tbody id="activity_table">
                               
                                    @foreach($damages as $key=>$row)
                                    <tr id="row_{{$key+1}}">
                                        <td>{{$key+1}}</td>
                                        <td>{{$row->date}}</td>
                                        <td>{{$row->product->name}}</td>
                                        <td>{{ getWearhouseName($row->wearhouse_id) }} </td>
                                        <td>{{$row->qty}}</td>
                                        <td>{{$row->total_amount}}</td>
                                        <td>{{$row->remarks}}</td>
                                        <td>{{$row->status}}</td>
                                        <td>
                                        @if($row->status == 'Pending')
                                        <a href="{{route('damage.edit', $row->id)}}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Edit') }}">
                                   <i class="las la-edit"></i>
                            </a>
                            
                        
                            <a href="{{route('damage.destroy', $row->id)}}" class="btn btn-soft-danger btn-icon btn-circle btn-sm" onclick="return confirm('Are you sure?')"  title="{{ translate('Cancel') }}">
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
            var str = '<tr id="row_'+row+'"><td>'+row+'</td><td><input name="order_no['+row+']" onkeyup="get_byorderid('+row+')" type="text" class="form-control gtorderid" placeholder="Enter Order No"></td>';
                str += '<td><input name="name['+row+']" type="text" class="form-control" placeholder="Enter Name"></td><td><input name="id['+row+']" type="text" class="form-control" placeholder="Enter ID"></td><td><input name="addree['+row+']" type="text" class="form-control" placeholder="Enter Address"></td>';
                str += '<td><input name="phone['+row+']" type="text" class="form-control" placeholder="Enter Phone"></td><td> <input name="amount['+row+']" type="text" class="form-control" placeholder="Enter Amount"></td>';
                str += '<td><select class="form-control" name="delivery_man['+row+']"><option value="">Select Delivery Man</option>';
                @foreach(App\Models\Staff::where('role_id','10')->get() as $u)
                str += '<option  value="{{ $u->user->name }}">{{ $u->user->name }}</option>';
                @endforeach
                str += '</select> </td>';

                str += '<td><select class="form-control" name="status['+row+']"><option value="">Select One</option><option value="pending">Pending</option><option value="confirm">Confirmed</option><option value="on-delivery">On Delivery</option><option value="delivered">Delivered</option></select></td><td><a href="javascript:" onclick="removeRow('+row+')" class="btn btn-xs btn-danger"><i class="las la-minus"></i></a></td></tr>';
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
                $('#row_'+row).find('td').eq(3).find('input').val(data.customer_id);
                $('#row_'+row).find('td').eq(4).find('input').val(shipping.address);
                $('#row_'+row).find('td').eq(5).find('input').val(shipping.phone);
                $('#row_'+row).find('td').eq(6).find('input').val(data.grand_total);
                  }




            })
        }
        }
    </script>
@endsection
