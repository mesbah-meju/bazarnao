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
            <div class="row card-body">
            <div class="">
            <form class="form form-horizontal mar-top" action="{{route('damage.update',$transfer->id)}}" method="POST" enctype="multipart/form-data" id="choice_form">
            @csrf
            <input type="hidden" name="added_by" value="admin">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Transfer')}}</h5>
                </div>
                <div class="card-body">
                <div class="col-md-6 pull-left">
                        <label>{{translate('From Wearhouse')}} <span class="text-danger">*</span></label>

                        <select class="form-control aiz-selectpicker" name="wearhouse_id" id="wearhouse_id" data-live-search="true" required>
                            @foreach ($wearhouses as $supp)
                            <option <?php if($transfer->wearhouse_id == $supp->id) echo 'selected';?> value="{{ $supp->id }}">{{ $supp->name }}</option>
                            @endforeach
                        </select>

                    </div>
                    
                    <div class="col-md-6 pull-left">
                        <label>{{translate('Product')}} <span class="text-danger">*</span></label>

                        <select class="form-control aiz-selectpicker" name="product_id" id="product_id" onchange="changeProduct(this.value)"  data-live-search="true" required>
                                        <option value="">Select Product</option>
                                        @foreach ($products as $product)
                                        <option <?php if($transfer->product_id == $product->id) echo 'selected';?>  value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>

                    </div>
                    
                    <div class="col-md-6 pull-left">
                        <label>{{translate('Damage Qty')}} <span class="text-danger">*</span></label>

                        <input type="number" class="form-control" name="qty" id="transfer_qty" max="{{$transfer->stock_qty}}" value="{{$transfer->qty}}"  required>

                    </div>
                    
                    <div class="col-md-6 pull-left">
                        <label>{{translate('Damage Date')}} <span class="text-danger">*</span></label>

                        <input type="date" class="form-control" name="date" value="{{$transfer->date}}"  required>

                    </div>

                    
                    
                    <div class="col-md-6 pull-left">
                        <label>{{translate('Remarks')}} <span class="text-danger">*</span></label>

                        <input type="text" class="form-control" name="remarks" placeholder="{{ translate('Remarks') }}" value="{{$transfer->remarks}}"  required>

                    </div>
                    
                    <div class="clearfix"></div>
                </div>
            </div>
            

            <div class="mb-3 text-right">
                <button type="submit" name="button" class="btn btn-primary">{{ translate('Save Damage') }}</button>
            </div>
        </form>
    </div>
            
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
