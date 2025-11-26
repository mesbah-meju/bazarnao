@extends('backend.layouts.staff')
<style>
     @media screen and (max-width: 480px) {
    #activity_table{
        width: 1000px;
  }
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
                            <div class="col-md-12">
                           
                            <table class="table table-bordered">
                                    <tr>
                                        <th style="width:18%">Total Order Qty : </th>
                                        <td style="width:15%">{{ $data['total_order_qty'] }}</td>
                                        <th style="width:15%">Sales Target: </th>
                                        <td style="width:15%">{{ $data['target'] }}</td>
                                        <th style="width:20%">Sales Achivement: </th>
                                        <td style="width:10%">
                                        {{ $data['sales_target_recovery'] }}%
                                        <input type="hidden"  name="sales_achivement" value="{{ $data['sales_target_recovery'] }}%" />
                                      </td>
                                    </tr>

                                    <tr>
                                        <th style="width:18%">Total Sales Amount : </th>
                                        <td style="width:15%">{{ $data['total_sales_amount'] }}</td>
                                        <th style="width:20%">Target New Customer: </th>
                                        <td style="width:10%">{{ $data['terget_customer'] }}</td>
                                        <th style="width:15%">Achivement New Customer: </th>
                                        <td style="width:15%">
                                        {{ $data['customer_achivement'] }}%
                                        <input type="hidden"  name="customer_achivement" value="{{ $data['customer_achivement'] }}%" />
                                    </td>
                                    </tr>

                                    <tr>
                                        <th style="width:18%">Total Due : </th>
                                        <td style="width:15%">{{ $data['all_time_due'] }}</td>
                                        <!-- <th></th>
                                        <td></td> -->
                                        <th style="width:18%">Performance: </th>
                                        <td style="width:10%">{{ $data['performance'] }}</td>
                                    </tr>

                                    <tr>
                                        <th style="width:18%">New Customer Created : </th>
                                        <td style="width:15%">{{ $data['new_customer'] }}</td>
                                    </tr>

                                    <tr>
                                    <th style="width:18%">Total Customer : </th>
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


                @include('backend.staff_panel.sales_executive_nav')
                <div class="card border-bottom-0">

                    <div class="row card-body">
                        <form action="{{route('staff.activity_save')}}" method="post">
                            @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered" id="activity_table">
                            <tr>
                                <th>Sl</th>
                                <th>Mobile No</th>
                                <th>Name</th>
                                <th>ID</th>
                                <th>Area</th>
                                <th>Address</th>
                                <th>Comment</th>
                                <th>Complain</th>
                                <th style="width:130px">Order Confirm</th>
                                <th>Order ID</th>
                                <th><a href="javascript:" onclick="addRow()" class="btn btn-xs btn-success"><i class="las la-plus"></i></a></th>
                            </tr>
                            <tbody id="activity_table">
                                @if(count($data['daily_activities'])>0)
                                    @foreach($data['daily_activities'] as $key=>$activity)
                                    <tr id="row_{{$key+1}}">
                                        <td>{{$key+1}}</td>
                                        <td>
                                            <input name="phone[{{$key+1}}]" value="{{$activity->phone}}" onkeyup="get_byphone({{$key+1}})" type="text" class="form-control gtphone" placeholder="Enter Phone">
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
                                            <input name="address[{{$key+1}}]" value="{{$activity->address}}" type="text" class="form-control" placeholder="Enter Address">
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
                                         <td><a href="javascript:" onclick="removeRow({{$key+1}})" class="btn btn-xs btn-danger"><i class="las la-minus"></i></a></td>
                                     </tr>
                                    @endforeach
                                @else
                            <tr id="row_1">
                               <td>1</td>
                               <td>
                                   <input name="phone[1]" type="text" onkeyup="get_byphone(1)" class="form-control gtphone" placeholder="Enter Phone">
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
                                   <input name="Comment[1]" type="text" class="form-control" placeholder="Enter Comment">
                                </td>
                               <td>
                                   <input name="Complain[1]" type="text" class="form-control" placeholder="Enter Complain">
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
                            </tr>
                            @endif
                            </tbody>
                        </table>
                        </div>
                        <input type="submit" class="btn btn-sm btn-primary pull-right" value="Save Activity">
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
            var str = '<tr id="row_'+row+'"><td>'+row+'</td><td><input onkeyup="get_byphone('+row+')" name="phone['+row+']" type="text" class="form-control gtphone" placeholder="Enter Phone"></td>';
                str += '<td><input name="name['+row+']" type="text" class="form-control" placeholder="Enter Name"></td><td><input name="id['+row+']" type="text" class="form-control" placeholder="Enter ID"></td><td><input name="area['+row+']" type="text" class="form-control" placeholder="Enter Area"></td>';
                str += '<td><input name="address['+row+']" type="text" class="form-control" placeholder="Enter Address"></td><td><input name="Comment['+row+']" type="text" class="form-control" placeholder="Enter Comment"></td><td> <input name="Complain['+row+']" type="text" class="form-control" placeholder="Enter Complain"></td>';
                str += '<td><select class="form-control" name="order_confirm['+row+']"><option value="">Select One</option><option value="yes">Yes</option><option value="no">No</option></select></td>';
                str += '<td><input name="order_id['+row+']" type="text" class="form-control" placeholder="Enter Order ID"></td><td><a href="javascript:" onclick="removeRow('+row+')" class="btn btn-xs btn-danger"><i class="las la-minus"></i></a></td></tr>';
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

        function get_byphone(row){
            site_url = get_site_url();
            var token = "{{ csrf_token() }}";
            var phone = $('#row_'+row).find('.gtphone').val();
         if(phone.length>=11)

$.ajax({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },

        url: site_url + '/get_customer_by_phone',
        type: "POST",
        data:{
            phonenumber:phone,
        },

        success: function(data){
            $('#row_'+row).find('td').eq(2).find('input').val(data.name);
            $('#row_'+row).find('td').eq(3).find('input').val(data.customer_id);
            $('#row_'+row).find('td').eq(4).find('input').val(data.areaname);
            $('#row_'+row).find('td').eq(5).find('input').val(data.address);

        }

})

        }
    </script>
@endsection
