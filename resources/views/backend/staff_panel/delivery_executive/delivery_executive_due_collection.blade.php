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
    .form-control{
        padding: 5px !important;
    }
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/2.4.85/css/materialdesignicons.css" rel="stylesheet" />

@section('content')
<div class="row gutters-10">
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
                
            </div>
            {{-- Statistics End here --}}

            @include('backend.staff_panel.delivery_executive.delivery_executive_nav')

            <div class="card border-bottom-0">

                <div class="card-body p-0">
                    <form action="{{route('due_collection.index')}}" method="post">
                        @csrf
                        <table class="table table-bordered">
                            <tr>
                                <th>Sl</th>
                                <th style="width:100px">Order ID</th>
                                <th>Name</th>
                                <th>Customer ID</th>
                                <th>Address</th>
                                <th>Mobile</th>
                                <th>Area</th>
                                <th>Amount</th>
                                <th>Total Collection</th>
                                <th>Cash Collection</th>
                                <th><a href="javascript:" onclick="addRow()" class="btn btn-xs btn-success"><i class="las la-plus"></i></a></th>
                               
                                
                            </tr>
                            <tbody id="activity_table">
                                @if(count($order_daily_activities)>0)
                                @foreach($order_daily_activities as $key=>$activity)
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
                                <tr id="row_{{$key+1}}">
                                    <td>{{$key+1}}</td>
                                    <td>
                                        <input name="order_no[{{$key+1}}]" value="{{$activity->order_no}}" onblur="due_collection_get_byorderid('{{$key+1}}')" type="text" class="form-control gtorderid" placeholder="Order ID" readonly>
                                        <input name="order_id[{{$key+1}}]" id="order_id_{{$key+1}}" value="{{$activity->id}}"  type="hidden" class="form-control " placeholder="Enter Phone">
                                    </td>
                                    <td>
                                        <input name="name[{{$key+1}}]" value="{{$activity->name}}" type="text" class="form-control" placeholder="Enter Name">
                                    </td>
                                    <td>
                                        <input name="id[{{$key+1}}]" value="{{$activity->customer_id}}" type="text" class="form-control" placeholder="Enter ID">
                                    </td>
                                    <td>
                                        <input name="address[{{$key+1}}]" value="{{$activity->address}}" type="text" class="form-control" placeholder="Enter Address">
                                    </td>
                                    <td>
                                        <input name="phone[{{$key+1}}]" value="{{$activity->phone}}" type="text" class="form-control" placeholder="Enter Phone">
                                    </td>

                                    <td>
                                        <input name="area[{{$key+1}}]" value="{{$activity->area}}" type="text" class="form-control" placeholder="Enter Area">
                                    </td>

                                    <td>
                                        <input name="amount[{{$key+1}}]" id="total_amount_{{$key+1}}" value="{{$activity->amount}}" type="text" class="form-control" readonly placeholder="Enter Amount">
                                    </td>

                                    <td>
                                        <input name="total_collection[{{$key+1}}]" id="total_collection_{{$key+1}}" data-tcol="{{$activity->total_collection}}" value="{{$activity->total_collection}}" type="text"  class="form-control" placeholder="Enter Amount" readonly>
                                    </td>

                                    <td>
                                        <input name="cash_collection[{{$key+1}}]" id="cash_collection_{{$key+1}}" value="{{$activity->cash_collection}}" type="text" class="form-control" placeholder="Enter cash collection" onkeyup="checkAmount({{$key+1}},this.value)" @if($activity->amount >= $activity->total_collection) readonly @endif >
                                    </td>
                                    
                                    
                                </tr>
                                @endforeach
                                @else
                                @php 
                                $key = 0;
                                @endphp
                                <tr id="row_1">
                                    <td>{{$key+1}}</td>
                                    <td>
                                        <input name="order_no[{{$key+1}}]" value="" onblur="due_collection_get_byorderid('{{$key+1}}')" type="text" class="form-control gtorderid" placeholder="Order ID">
                                        
                                        
                                    </td>
                                    <td>
                                        <input name="name[{{$key+1}}]" value="" type="text" class="form-control" placeholder="Enter Name">
                                    </td>
                                    <td>
                                        <input name="id[{{$key+1}}]" value="" type="text" class="form-control" placeholder="Enter ID">
                                    </td>
                                    <td>
                                        <input name="address[{{$key+1}}]" value="" type="text" class="form-control" title="" placeholder="Enter Address">
                                    </td>
                                    <td>
                                        <input name="phone[{{$key+1}}]" value="" type="text" class="form-control" placeholder="Enter Phone">
                                    </td>

                                    <td>
                                        <input name="area[{{$key+1}}]" value="" type="text" class="form-control" placeholder="Enter Area">
                                    </td>

                                    <td>
                                        <input name="amount[{{$key+1}}]" id="total_amount_1" value="" type="text" class="form-control" placeholder="Enter Amount" readonly>
                                    </td>
                                    <td>
                                        <input name="total_collection[{{$key+1}}]" id="total_collection_1" data-tcol="0" value="0" type="text" class="form-control" placeholder="Enter Amount" readonly>
                                    </td>

                                    <td>
                                        <input name="cash_collection[{{$key+1}}]" id="cash_collection_1" value="" type="text" class="form-control" placeholder="Enter cash collection" onkeyup="checkAmount(1,this.value)" required>
                                    </td>
                                    
                                    
                                </tr>
                                    
                                
                                @endif
                            </tbody>
                        </table>
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

    function addRow() {
        var row = $('#activity_table').find('tr').length;
        row++;
        var str = '<tr id="row_' + row + '"><td>' + row + '</td><td><input name="order_no[' + row + ']" onblur="due_collection_get_byorderid(' + row + ')" type="text" class="form-control gtorderid" placeholder="Enter Order ID"></td>';
        str += '<td><input name="name[' + row + ']" type="text" class="form-control" placeholder="Enter Name"></td><td><input name="id[' + row + ']" type="text" class="form-control" placeholder="Enter Customer ID"></td><td><input name="address[' + row + ']" type="text" class="form-control" placeholder="Enter Address"></td>';
        str += '<td><input name="phone[' + row + ']" type="text" class="form-control" placeholder="Enter Phone"></td>  <td><input name="area[' + row + ']" type="text" class="form-control" placeholder="Enter Area"></td> <td> <input name="amount[' + row + ']" id="total_amount_' + row + '" type="text" class="form-control" placeholder="Enter Amount" readonly></td>';
        str += '<td><input name="total_collection[' + row + ']" id="total_collection_' + row + '" data-tcol="" value="0" type="text" class="form-control" readonly></td>';
        str += '<td><input name="cash_collection[' + row + ']" id="cash_collection_' + row + '" type="text" class="form-control" placeholder="Enter Cash Collection" onkeyup="checkAmount(' + row + ',this.value)"></td><td><a href="javascript:" onclick="removeRow(' + row + ')" class="btn btn-xs btn-danger"><i class="las la-minus"></i></a></td></tr>';
        $('#activity_table').append(str);
    }

    function removeRow(row) {
        var row = $('#row_' + row).remove();
        $('#activity_table').find('tr').each(function(i, v) {
            $(v).attr('id', 'row_' + (i + 1));
            $(v).find('td').eq(0).html((i + 1));
            $(v).find('td').eq(10).html('<a href="javascript:" onclick="removeRow(' + (i + 1) + ')" class="btn btn-xs btn-danger"><i class="las la-minus"></i></a>');
        })
    }

    function due_collection_get_byorderid(row) {

        site_url = location.hostname;
        let token = "{{ csrf_token()}}";
        let ordernumber = $('#row_' + row).find('.gtorderid').val();
        if (ordernumber.length >= 8) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                url: '/get_customer_service_order',
                type: "POST",
                data: {

                    ordernumber: ordernumber,
                },

                success: function(data) {
                    var shipping = JSON.parse(data.shipping_address)
                    $('#row_' + row).find('td').eq(2).find('input').val(shipping.name);
                    $('#row_' + row).find('td').eq(3).find('input').val(data.customer_id);
                     $('#row_' + row).find('td').eq(4).find('input').val(shipping.address);
                     $('#row_' + row).find('td').eq(5).find('input').val(shipping.phone);
                     $('#row_' + row).find('td').eq(6).find('input').val(data.areaname);
                     $('#row_' + row).find('td').eq(7).find('input').val(data.grand_total);
                     $('#row_' + row).find('td').eq(8).find('input').val(data.cash_collection);
                     $('#row_' + row).find('td').eq(8).find('input').attr('data-tcol',data.cash_collection);
                }

            })


        }


    }

    function get_byorderid(row) {

        site_url = get_site_url();
        var token = "{{ csrf_token()}}";
        var ordernumber = $('#row2_' + row).find('.gtorderid').val();
        if (ordernumber.length >= 8) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                //url: site_url + '/get_customer_service_order',
                url: site_url + '/get_delivery_ledger_by_order',
                type: "POST",
                data: {

                    ordernumber: ordernumber,
                },

                success: function(data) {
                    var shipping = JSON.parse(data.shipping_address)
                    $('#row2_' + row).find('td').eq(2).find('input').val(shipping.name);
                    //$('#row_' + row).find('td').eq(3).find('input').val(data.cash_collection);
                    // $('#row_' + row).find('td').eq(4).find('input').val(shipping.address);
                    $('#row2_' + row).find('td').eq(5).find('input').val(data.date);
                    // $('#row_' + row).find('td').eq(6).find('input').val(data.areaname);
                    // $('#row_' + row).find('td').eq(7).find('input').val(data.grand_total);
                }

            })


        }


    }


    function addRow2() {
        var row = $('#activity_table2').find('tr').length;
        row++;
        var str = '<tr id="row2_' + row + '"><td>' + row + '</td><td><input name="order_no[' + row + ']"  type="number" class="form-control gtorderid" placeholder="Enter Order No" maxlength="10" required></td>';
        str += '<td><input name="name[' + row + ']" type="text" class="form-control" placeholder="Enter Name"></td>';
        str += '<td><input name="credit[' + row + ']" type="number" class="form-control" placeholder="Enter Payment Amount"></td><td><input name="note[' + row + ']" type="text" class="form-control" placeholder="Enter Paid To"></td>';
        str += '<td> <input name="date[' + row + ']" type="date" class="form-control" placeholder="Enter date"></td>';
        str += '<td><select class="form-control" name="status[' + row + ']"><option value="">Select One</option><option value="Pending">Pending</option><option value="Paid">Paid</option></select></td><td><a href="javascript:" onclick="removeRow2(' + row + ')" class="btn btn-xs btn-danger"><i class="las la-minus"></i></a></td></tr>';
        $('#activity_table2').append(str);
    }

    function removeRow2(row) {
        var row = $('#row2_' + row).remove();
        $('#activity_table2').find('tr').each(function(i, v) {
            $(v).attr('id', 'row2_' + (i + 1));
            $(v).find('td').eq(0).html((i + 1));
            $(v).find('td').eq(10).html('<a href="javascript:" onclick="removeRow2(' + (i + 1) + ')" class="btn btn-xs btn-danger"><i class="las la-minus"></i></a>');
        })
    }

    function update_delivery_status(status,order_id){
            //var order_id = $('#order_id_'+row).val();
            $.post('{{ route('orders.update_delivery_status') }}', {_token:'{{ @csrf_token() }}',order_id:order_id,status:status}, function(data){
                    AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
                });
        }


        function checkAmount(key,value){
        var amount = parseInt($('#total_amount_'+key).val());
        var total_collection = parseInt($('#total_collection_'+key).data('tcol'));
        $('#total_collection_'+key).val(total_collection+parseInt(value))
        if(value > (amount-total_collection)){
            alert('Cash Collection must be less than total amount');
            $('#cash_collection_'+key).val((amount-total_collection));
            $('#total_collection_'+key).val(amount);
        }
        
        }

    //     $( document ).ready(function() {
    //         setTimeout(function() { 
    //             location.reload(true);
    // }, 50000);
            
    //        });

</script>
@endsection