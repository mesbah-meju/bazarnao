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
                                aria-expanded="false" aria-controls="collapseOne">
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
                                        <th style="width:35%">Total Purchase Product Qty : </th>
                                        <td style="width:20%">{{ $data['total_purchase_qty'] }}</td>
                                        <th style="width:25%">Vendor Create: </th>
                                        <td style="width:20%">{{ $data['new_supplier'] }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width:25%">Purchase Amount : </th>
                                        <td style="width:25%">{{ $data['total_purchase_amount'] }}</td>
                                        <th style="width:25%">Total Vendor: </th>
                                        <td style="width:25%">{{ $data['total_supplier']  }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width:25%">Damage Product Qty : </th>
                                        <td style="width:25%">{{ $data['damage_product_qty'] }}</td>
                                        <th style="width:25%">Total Supplier Credit: </th>
                                        <td style="width:25%">{{  $data['final_total_creadit']  }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width:25%">Damage Product Amount : </th>
                                        <td style="width:25%">{{ $data['damage_product_amount'] }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Statistics End here --}}


                @include('backend.staff_panel.purchase_manager.purchase_manager_nav')
                <div class="card border-bottom-0">

                    <div class=" card-body">
                    <form action="{{route('staff.activity_save')}}" method="post">
                            @csrf
                            <table class="table table-bordered w-100">
                            <tr>
                                <th>Sl</th>
                                <th style="width:180px">Purchase Order No</th>
                                <th>Vendor Name</th>
                                <th>ID</th>
                                <th>Address</th>
                                <th>Mobile</th>
                                <th>Paid Amount</th>
                                <th style="width:160px">Purchase Amount</th>
                                <th>Due Balance</th>
                             </tr>
                            <tbody id="activity_table">
                                @if(count($data['purchasedetails'])>0)
                                    @foreach($data['purchasedetails'] as $key=>$activity)
                                    <tr id="row_{{$key+1}}">
                                        <td>{{$key+1}}</td>
                                        <td>
                                            <input name="order_no[{{$key+1}}]" value="{{$activity->purchase_no}}"  type="text" class="form-control gtpurchaseno">
                                            <input name="purchase_id[{{$key+1}}]" value="{{$activity->id}}"  type="hidden" class="form-control">
                                         </td>
                                        <td>
                                            <input name="name[{{$key+1}}]" value="{{$activity->name}}" type="text" class="form-control" placeholder="Enter Name">
                                         </td>
                                        <td>
                                            <input name="id[{{$key+1}}]" value="{{$activity->supplier_id}}" type="text" class="form-control" placeholder="Enter ID">
                                         </td>
                                        <td>
                                            <input name="address[{{$key+1}}]" value="{{$activity->address}}" type="text" class="form-control" placeholder="Enter Address">
                                         </td>
                                        <td>
                                            <input name="phone[{{$key+1}}]" value="{{$activity->phone}}" type="text" class="form-control" placeholder="Enter Comment">
                                         </td>
                                        <td>
                                            <input name="paid_amount[{{$key+1}}]" id="paid_amount_{{$key+1}}" value="{{$activity->payment_amount}}" onkeyup="paidAmount({{$key+1}})" type="text" class="form-control" placeholder="Enter Complain">
                                         </td>
                                        
                                        <td>
                                            <input name="purchase_amount[{{$key+1}}]" value="{{$activity->total_value}}" type="text" id="purchase_amount_{{$key+1}}" class="form-control" readonly >
                                         </td>
                                        <td>
                                            <input name="due_balance[{{$key+1}}]" id="due_balance_{{$key+1}}" value="{{$activity->total_value-$activity->payment_amount}}" type="text" class="form-control" placeholder="Enter Order ID" readonly >
                                         </td>
                                         
                                     </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td align="center" colspan="9">No data Available</td>
                                    </tr>
                                
                            @endif
                            </tbody>
                        </table>
                        @if(count($data['purchasedetails'])>0)
                        <input type="submit" class="btn btn-sm btn-primary pull-right" value="Save Activity">
                        @endif
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
            var str = '<tr id="row_'+row+'"><td>'+row+'</td><td><input onkeyup="get_purchaseno('+row+')" name="order_no['+row+']" type="text" class="form-control gtpurchaseno" placeholder="Enter Order No"></td>';
                str += '<td><input name="name['+row+']" type="text" class="form-control" placeholder="Enter Name"></td><td><input name="id['+row+']" type="text" class="form-control" placeholder="Enter ID"></td><td><input name="addree['+row+']" type="text" class="form-control" placeholder="Enter Address"></td>';
                str += '<td><input name="phone['+row+']" type="text" class="form-control" placeholder="Enter Phone"></td><td><input name="paid_amount['+row+']" type="text" class="form-control" placeholder="Enter Paid Amount"></td><td> <input name="purchase_amount['+row+']" type="text" class="form-control" placeholder="Enter Purchase Amount"></td>';
                str += '<td><input name="due_balance['+row+']" type="text" class="form-control" placeholder="Enter Due Balance"></td><td><a href="javascript:" onclick="removeRow('+row+')" class="btn btn-xs btn-danger"><i class="las la-minus"></i></a></td></tr>';
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

        function get_purchaseno(row){
            site_url = get_site_url();
            var token = "{{ csrf_token() }}";
            var purchaseno = $('#row_'+row).find('.gtpurchaseno').val();
                $.ajax({

                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                  url: site_url + '/get_purchase_details',
                  type: "POST",
                  data:{
                    purchaseno : purchaseno,
                  },

                  success: function(data){
                    $('#row_'+row).find('td').eq(2).find('input').val(data.name);
                    $('#row_'+row).find('td').eq(3).find('input').val(data.supplier_id);
                    $('#row_'+row).find('td').eq(4).find('input').val(data.address);
                    $('#row_'+row).find('td').eq(5).find('input').val(data.phone);
                    $('#row_'+row).find('td').eq(6).find('input').val(data.payment_amount);
                    $('#row_'+row).find('td').eq(7).find('input').val(data.total_value);
                    $('#row_'+row).find('td').eq(8).find('input').val((data.total_value-data.payment_amount).toFixed(2));
                  }
                })
        }

        function paidAmount(id){
           var paid_amount = parseInt($('#paid_amount_'+id).val());
           var purchase_amount = parseInt($('#purchase_amount_'+id).val());
           if(paid_amount){
            $('#due_balance_'+id).val(purchase_amount-paid_amount);
           }else{
            $('#due_balance_'+id).val(purchase_amount-0);
           }
           if(paid_amount > purchase_amount){
            alert('Paid amount must be less than to purchase amount')
            $('#paid_amount_'+id).val(purchase_amount);
            $('#due_balance_'+id).val(0);
           }

        }
    </script>
@endsection
