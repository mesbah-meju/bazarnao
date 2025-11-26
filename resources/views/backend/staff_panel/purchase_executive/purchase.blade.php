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
<style>
    #item_table .form-control {
        padding: 2px;
    }
</style>
@include('backend.staff_panel.purchase_executive.purchase_executive_nav')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Add New Purchase')}}</h5>
</div>
<div class="">
    <div class="">
        <form class="form form-horizontal mar-top" action="{{route('purchase_orders.store')}}" method="POST" enctype="multipart/form-data" id="choice_form">
            @csrf
            <input type="hidden" name="added_by" value="admin">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Supplier Information')}}</h5>
                </div>
                <div class="card-body">

                    <div class="col-md-6 pull-left">
                        <label>{{translate('Supplier Name')}} <span class="text-danger">*</span></label>

                        <select class="form-control aiz-selectpicker" name="supplier_id" id="supplier_id" data-live-search="true" required>
                            <option value="">Select Supplier</option>
                            @foreach ($supplier as $supp)
                            <option value="{{ $supp->supplier_id }}">{{ $supp->name }}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="col-md-6 pull-left">
                        <label>{{translate('Purchase Date')}} <span class="text-danger">*</span></label>

                        <input type="date" class="form-control" name="purchase_date" placeholder="{{ translate('Purchase Date') }}" onchange="update_sku()" required>

                    </div>

                    <div class="col-md-6 pull-left">
                        <label>{{translate('Remarks')}} <span class="text-danger">*</span></label>

                        <input type="text" class="form-control" name="remarks" placeholder="{{ translate('Remarks') }}" onchange="update_sku()" required>

                    </div>
                    <div class="col-md-6 pull-left">
                        <label for="name">{{translate('Wearhouse')}} <span class="text-danger">*</span></label>
                        <select name="wearhouse_id" id="wearhouse_id" class="form-control" required>
                            <option value="">{{translate('Select Wearhouse')}}</option>
                            @foreach($wearhouses as $row)
                            <option value="{{$row->id}}">{{$row->name}}</option>
                            @endforeach

                        </select>
                    </div>
                    <div class="col-md-6 pull-left">
                        <label>{{translate('Chalan No')}} <span class="text-danger">*</span></label>

                        <input type="text" class="form-control" name="chalan_no" placeholder="{{ translate('Chalan no') }}" required>
                    </div>
                    <div class="col-md-6 pull-left">
                        <label>{{translate('Chalan Voucher')}}</label>
                        <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="false">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="voucher_img" class="selected-files">
                        </div>
                        <div class="file-preview box sm"></div>
                    </div>

                    <div class="col-md-6 pull-left">
                        <label for="name">{{translate('Purchase With Barcode')}}</label>
                        <input class="form-control" type="text" id="barcode" name="keyword" placeholder="{{ translate('Scan Barcode') }}">
                    </div>

                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Product Information')}}</h5>
                </div>
                <div class="card-body">
                    <table id="item_table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="width:42%;">Product</th>
                                <th style="width:15%;">Barcode</th>
                                <th style="width:8%;">Exp: Date</th>
                                <th style="width:10%;">Stock</th>
                                <th style="width:9%;">Quantity</th>
                                <th style="width:10%;">Unit Price</th>
                                <th style="width:5%;">Total</th>

                                <th style="width:5%;"><a href="javascript:" onclick="addItemRow()" class="btn btn-sm btn-primary"><i class="las la-plus"></i></a></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="row_1">
                                <td>
                                    <select class="form-control aiz-selectpicker" onchange="changeProduct(this)" name="product[]" id="product_1" data-live-search="true" required>
                                        <option value="">Select Product</option>
                                        @foreach ($products as $product)
                                        <option data-qty="{{ $product->current_stock }}" data-price="{{ $product->purchase_price }}" value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" id="desc_1" name="desc[]" class="form-control">
                                </td>
                                <td>
                                    <input type="date" id="exp_1" name="exp[]" class="form-control" required>
                                </td>
                                <td>
                                    <input disabled type="text" id="stock_1" name="stock[]" class="form-control">
                                </td>
                                <td>
                                    <input type="number" id="qty_1" onchange="changePrice(this)" name="qty[]" class="form-control">
                                </td>
                                <td>
                                    <input type="text" id="price_1" onchange="changePrice(this)" name="price[]" class="form-control">
                                </td>
                                <td id="total_1">

                                </td>
                                <td>
                                    <a href="javascript:" onclick="removeItemRow(1)" class="btn btn-sm btn-danger"><i class="las la-minus"></i></a>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" style="text-align:right">Total
                                    <input type="hidden" name="total" id="total_input">
                                </th>
                                <th id="total"></th>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>

            <div class="mb-3 text-right">
                <button type="submit" name="button" class="btn btn-primary">{{ translate('Save Purchase') }}</button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('script')

<script type="text/javascript">
    function changeProduct(e) {
        var id = $(e).attr('id').split('_')[1];
        var product_id = $('#product_' + id).val();
        var wearhouse_id = $('#wearhouse_id').val();
        var price = Number($(e).find('option:selected').data('price'));
        if (wearhouse_id === '' || wearhouse_id === undefined || wearhouse_id === null) {
            alert('Please select wearhouse');
            return false;
        }
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('purchase_orders.get_puracher_product')}}",
            type: 'POST',
            data: {
                product_id: product_id,
                wearhouse_id: wearhouse_id
            },
            //dataType: 'html',
            success: function(data) {

                var total = price * 1;
                $('#qty_' + id).val(1);
                $('#price_' + id).val(price);
                $('#stock_' + id).val(data.qty);
                $('#total_' + id).html(total);
            }
        });

    }

    function changePrice(e) {
        var id = $(e).attr('id').split('_')[1];
        var price = Number($('#price_' + id).val());
        var qty = Number($('#qty_' + id).val());
        if (!qty)
            qty = 1;
        var total = price * qty;
        $('#total_' + id).html(total);
        calculateTotal();
    }

    function calculateTotal() {
        var total = 0;
        $('#item_table').find('tbody>tr').each(function() {
            var id = $(this).attr('id').split('_')[1];
            total += Number($('#total_' + id).html());
        });
        $('#total').html(total);
        $('#total_input').val(total);
    }

    function addItemRow() {
        var row = $('#item_table').find('tbody>tr').length;
        row++;
        var str = "<tr id='row_" + row + "'>";
        str += '<td><select class="form-control aiz-selectpicker"  onchange="changeProduct(this)" name="product[]" id="product_' + row + '" data-live-search="true" required><option value="">Select Product</option>';
        @foreach($products as $product)
        str += '<option data-qty="{{ $product->current_stock }}" data-price="{{ $product->purchase_price }}" value="{{ $product->id }}">{{ $product->name }}</option>';
        @endforeach
        str += '</select> </td>';
        str += '<td> <input type="text" id="desc_' + row + '" name="desc[]" class="form-control"> </td>';
        str += '<td> <input type="date" id="exp_' + row + '" name="exp[]" class="form-control"> </td>';
        str += '<td><input disabled type="text" id="stock_' + row + '" name="stock[]" class="form-control"></td>';
        str += ' <td><input type="number" id="qty_' + row + '" onchange="changePrice(this)" name="qty[]" class="form-control"></td>';
        str += '<td><input type="text" id="price_' + row + '" onchange="changePrice(this)" name="price[]" class="form-control"></td>';
        str += '<td id="total_' + row + '"></td>';
        str += ' <td><a href="javascript:" onclick="removeItemRow(' + row + ')" class="btn btn-sm btn-danger"><i class="las la-minus"></i></a></td>';
        str += "</tr>";

        $('#item_table').find('tbody').append(str);
        $('.aiz-selectpicker').selectpicker();
        calculateTotal();
    }

    function removeItemRow(id) {
        if (confirm('Are you sure to Remove ? ') == true) {
            $('#row_' + id).remove();
            calculateTotal();
        }
    }

    window.onload = function() {
        var barcodeElement = document.getElementById('barcode');
        barcodeElement.focus();
    }

    $('#barcode').keypress(function(event) {
        setTimeout(() => {
            const barcode = $(this).val();
            if (barcode == '') {
                return false;
            }
            $('#barcode').val('');

            var wearhouse_id = $('#wearhouse_id').val();
            if (wearhouse_id === '' || wearhouse_id === undefined || wearhouse_id === null) {
                AIZ.plugins.notify('danger', 'Please select warehouse First');
                $('#barcode').val('');
                return false;
            }

            $.post("{{ route('purchase.withbarcode') }}", {
                _token: AIZ.data.csrf,
                barcode: barcode,
                wearhouse_id: wearhouse_id
            }, function(data) {
                console.log(data); // Log the data to check its structure

                if (data.success == 1) {
                    var row = $('#item_table').find('tbody>tr').length;
                    row++;
                    var str = "<tr id='row_" + row + "'>";
                    str += '<td><select class="form-control aiz-selectpicker" onchange="changeProduct(this)" name="product[]" id="product_' + row + '" data-live-search="true" required><option value="">Select Product</option>';

                    // Check if 'data.product' is an object, and wrap it in an array if so
                    var products = Array.isArray(data.product) ? data.product : [data.product];

                    products.forEach(function(product) {
                        var isSelected = (product.id == data.product_id);
                        str += '<option data-qty="' + product.stock + '" data-price="' + product.unit_price + '" value="' + product.id + '" ' + (isSelected ? 'selected' : '') + '>' + product.name + '</option>';
                    });

                    str += '</select> </td>';
                    str += '<td> <input type="text" id="desc_' + row + '" name="desc[]" class="form-control" value="' + data.barcode + '"> </td>';
                    str += '<td> <input type="date" id="exp_' + row + '" name="exp[]" class="form-control"> </td>';
                    str += '<td><input disabled type="text" id="stock_' + row + '" name="stock[]" class="form-control" value="' + data.stock + '"></td>';
                    str += ' <td><input type="number" id="qty_' + row + '" onchange="changePrice(this)" name="qty[]" class="form-control" value="' + data.qty + '"></td>';
                    str += '<td><input type="text" id="price_' + row + '" onchange="changePrice(this)" name="price[]" class="form-control" value="' + data.unit_price + '"></td>';
                    str += '<td id="total_' + row + '" ></td>';
                    str += ' <td><a href="javascript:" onclick="removeItemRow(' + row + ')" class="btn btn-sm btn-danger"><i class="las la-minus"></i></a></td>';
                    str += "</tr>";

                    $('#item_table').find('tbody').prepend(str);
                    $('.aiz-selectpicker').selectpicker();
                    calculateTotal();
                    $('#barcode').val('');
                } else {
                    $('#barcode').val('');
                    AIZ.plugins.notify('danger', data.message);
                }
            });
        }, 100);
    });

</script>

@endsection