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
               

            @if(auth()->user()->staff->role->name=='Purchase Manager')
@include('backend.staff_panel.purchase_manager.purchase_manager_nav')
@elseif(auth()->user()->staff->role->name=='Purchase Executive')
@include('backend.staff_panel.purchase_executive.purchase_executive_nav')
@else
@include('backend.staff_panel.operation_manager.operation_manager_nav')
@endif
                
                <div class="card border-bottom-0">
                <div class="card-body">
                <form action="{{ route('operation_manager_stock_report.index') }}" method="GET">
                    <div class="form-group row">
                        <div class="col-md-3">
                        <label class="col-form-label">{{translate('Sort by warehouse')}} :</label>
                            <select id="demo-ease" class="from-control aiz-selectpicker" name="category_id" data-live-search="true">
                                <option value=''>All</option>
                                @foreach ($wearhouse as $key => $row)
                                <option @php if($sort_by==$row->id)
                                    echo 'selected';
                                    @endphp
                                    value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label">{{translate('Sort by Product')}} :</label>
                            <select id="demo-ease" class="aiz-selectpicker select2" name="product_id" data-live-search="true">
                                <option value=''>All</option>
                                @foreach (DB::table('products')->select('id','name')->get(); as $key => $prod)
                                <option @php if($pro_sort_by==$prod->id)
                                    echo 'selected';
                                    @endphp
                                    value="{{ $prod->id }}">{{ $prod->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Start Date :</label>
                            <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                        </div>
                        <div class="col-md-3">
                            <label>End Date :</label>
                            <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                        </div>

                        <div class="col-md-4 mt-4">
                            <button class="btn btn-primary" type="submit">{{ translate('Filter') }}</button>
                            <button class="btn btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                        </div>
                    </div>
                </form>
                <div class="printArea">
                <style>
th{text-align:center;}
</style>
                <h3 style="text-align:center;">{{translate('WareHouse wise stock report')}}</h3>
                    <table class="table-bordered" style="width:100%;font-size: 13px;">
                        <thead>
                            <tr>
                                <th style="width:5%">{{ translate('SL') }}</th>
                                <th style="width:40%">{{ translate('Product Name') }}</th>
                                <th style="width:10%">{{ translate('Shelf name') }}</th>
                                <th style="width:10%">{{ translate('Shelf no') }}</th>
                                <th style="width:10%">{{ translate('Expire Date') }}</th>
                                <th style="width:10%">{{ translate('Expiry') }}</th>
                                <!-- <th style="width:30%">{{ translate('Category') }}</th> -->
                                <th style="width:5%">{{ translate('Unit Price') }}</th>
                                <th style="width:5%">{{ translate('Purchase Price') }}</th>
                                <th style="width:5%">{{ translate('Stock') }}</th>
                                <th style="width:10%">{{ translate('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php $total = 0; @endphp
                            @foreach ($products as $key => $product)
                            
                            @php
                            $qty = 0;
                            if ($product->variant_product) {
                            foreach ($product->stocks as $key => $stock) {
                            $qty += $stock->qty;
                            }
                            }
                            else {
                            $qty = $product->qty;
                            }
                            @endphp
                            @php $total = $total+($qty*$product->purchase_price); @endphp
                            @php
                                $currentDate = now(); 
                                $expiryDate = $product->expiry_date;
                                $daysDifference = $currentDate->diffInDays($expiryDate, false);
                            @endphp
                            <tr>
                                <td> {{ ($key+1) }}</td>
                               

                                <td>{{ $product->getTranslation('name') }}</td>
                                <td>{{ $product->self_name }}</td>
                                <td>{{ $product->self_no }}</td>
                                <td>
                                    {{ $product->expiry_date }}
                                </td>
                                <td>
                                    @if ($daysDifference < 0)
                                        Expired
                                    @else
                                        {{ $daysDifference }} day{{ $daysDifference != 1 ? 's' : '' }} remaining
                                    @endif
                                </td>
                                <!-- <td>{{ $product->getTranslation('category_name') }}</td> -->
                                <td style="text-align:right;">{{ $product->unit_price }}</td>
                                <td style="text-align:right;">{{ $product->purchase_price }}</td>
                                <td style="text-align:right;">{{ $qty }}</td>
                                <td style="text-align:right;">{{ single_price($qty*$product->purchase_price) }}</td>
                            </tr>
                            @endforeach
                            <tr>
                    <td style="text-align:right;" colspan="9"><b>Total</b></td>
                    <td style="text-align:right;"><b>{{single_price($total)}}</b></td>
                </tr>
                        </tbody>
                    </table>
                    
                    </div>

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
