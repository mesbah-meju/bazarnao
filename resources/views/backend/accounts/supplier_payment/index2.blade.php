@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Supplier Payment')}}</h5>
            </div>
            <form action="{{ route('supplier-payment.store') }}" id="supplier_paymentform" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label for="dtpDate" class="col-sm-2 col-form-label">{{ translate('Date') }} <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <input type="text" name="dtpDate" id="dtpDate" class="form-control datepicker" value="{{ date('m/d/Y') }}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="txtRemarks" class="col-sm-2 col-form-label">{{ translate('Remark') }}</label>
                        <div class="col-sm-4">
                            <textarea name="txtRemarks" id="txtRemarks" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="debtAccVoucher">
                            <thead>
                                <tr>
                                    <th class="text-center"><?php echo translate('Supplier Name')?><i class="text-danger">*</i></th>
                                    <th class="text-center"><?php echo translate('Voucher No')?></th>
                                    <th class="text-center"><?php echo translate('Due Amount')?></th>
                                    <th class="text-center"><?php echo translate('Amount')?><i class="text-danger">*</i></th>
                                </tr>
                            </thead>
                            <tbody id="debitvoucher">
                                <tr>
                                    <td class="" width="300">
                                        <!-- Hidden input field to hold purchase details, but we're passing data directly to JavaScript -->

                                        <select name="supplier_id" id="supplier_id_1" class="form-control aiz-selectpicker" data-live-search="true" onchange="load_supplier_code(this.value, 1, @json($purchase_id))" required>
                                            <option value="">Select Supplier</option>
                                            @foreach ($suppliers as $suplier)
                                                <option value="{{ $suplier->supplier_id }}" {{ (isset($supplier_id) && $supplier_id == $suplier->supplier_id) ? 'selected' : '' }}>
                                                    {{ $suplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="hidden" name="txtCode" value="" class="form-control" id="txtCode_1">
                                        <select name="voucher_no" id="voucher_no_1" class="form-control aiz-selectpicker" data-live-search="true" onchange="voucher_due(this.value)" required>
                                            <option value="">Select Voucher</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="dueAmount" value="" class="form-control  text-right" id="due_1" readonly="">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="txtAmount" value="" class="form-control total_price text-right" id="txtAmount_1" onkeyup="supplierRcvcalculation(1)" required>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>

                                    </td>
                                    <td colspan="2" class="text-right">
                                        <label for="reason" class="col-form-label"><?php echo translate('Total') ?></label>
                                    </td>
                                    <td class="text-right">
                                        <input type="text" id="grandTotal" class="form-control text-right " name="grand_total" value="" readonly="readonly" />
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        <p hidden id="old-amount"><?php echo 0;?></p>
                        <p hidden id="pay-amount"></p>
                        <p hidden id="change-amount"></p>
                        <div class="col-sm-6 border p-4">
                            <div id="adddiscount" class="display-none">
                                <div class="row gutters-5">
                                    <div class="form-group col-md-5">
                                        @php $card_type = 1020101; @endphp
                                        <label for="payments" class="col-form-label pb-2"><?php echo translate('Payment Type');?></label>
                                        <select name="multipaytype[]" id="payment_type" class="form-control card_typesl postform resizeselect aiz-selectpicker">
                                            @foreach ($payment_methods as $key => $value)
                                            <option value="{{ $key }}" {{ (isset($card_type) && $card_type == $key) ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label for="4digit" class="col-form-label pb-2"><?php echo translate('Paid Amount');?></label>
                                        <input type="text" id="pamount_by_method_1" class="form-control number pay text-right valid_number" name="pamount_by_method[]" value="" onkeyup="changedueamount()" placeholder="0.00" required />
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="payments" class="col-form-label pb-2 text-white"><?php echo translate('Payment Type');?></label>
                                        <button class="btn btn-danger" onclick="removeMethod(this,1)"><i class="las la-trash"></i></button>
                                    </div>
                                </div>
                                <div class="" id="add_new_payment">
                                </div>
                                <div class="row text-left">
                                    <div class="form-group col-sm-12 pr-0">
                                        <button type="button" id="add_new_payment_type" class="btn btn-success w-md m-b-5"><?php echo translate('New Payment Method');?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12 text-right">
                        <input type="hidden" name="finyear" id="finyear" value="{{ get_financial_year() }}">
                            <input type="submit" id="add_receive" class="btn btn-success btn-large" name="save" value="<?php echo translate('Payment') ?>" tabindex="9" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ static_asset('assets/js/accounts/supplier-payment.js') }}" type="text/javascript"></script>
<script type="text/javascript">

    var purchaseData = @json($purchase_id);

    document.addEventListener('DOMContentLoaded', function() {
        var supplierSelect = document.getElementById('supplier_id_1');
        if (supplierSelect.value) {
            load_supplier_code(supplierSelect.value, 1, purchaseData);
        }
    });
    // function load_supplier_code(id,sl,purchaseData) {
    //     $.ajax({
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         url : "{{ route('supplier-payment.due-vouchers') }}",
    //         type: "POST",
    //         data: {
    //             supplier_id: id,
    //             purchaseData: purchaseData,
    //         },
    //         success: function(data) {
    //             var obj = jQuery.parseJSON(data);
    //             $('#txtCode_'+sl).val(obj.headcode);
    //             $('#voucher_no_1').html(obj.vouchers);
    //         },
    //         error: function (jqXHR, textStatus, errorThrown) {
    //             alert('Error get data from ajax');
    //         }
    //     });
    // }

    function load_supplier_code(id, sl, purchaseData) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('supplier-payment.due-vouchers') }}",
            type: "POST",
            data: {
                supplier_id: id,
                purchaseData: purchaseData,
            },
            success: function(data) {
                var obj = jQuery.parseJSON(data);

                $('#txtCode_' + sl).val(obj.headcode);

                var voucherSelect = $('#voucher_no_1');
                voucherSelect.html(''); 
                voucherSelect.html(obj.vouchers);
                voucherSelect.selectpicker('refresh');

                if (purchaseData) {
                    voucherSelect.val(purchaseData);  // set selected value

                    voucherSelect.selectpicker('refresh');

                    // --- MANUALLY CALL voucher_due with selected purchaseData ---
                    voucher_due(purchaseData);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error getting data from ajax');
            }
        });
    }



    function voucher_due(id) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url : "{{ route('supplier-payment.due-amount') }}",
            type: "POST",
            data: {
                purchase_id: id,
            },
            success: function(data) {
                $('#due_1').val(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    $(document).on('click', '#add_new_payment_type', function() {
        var gtotal = $("#grandTotal").val();

        var total = 0;
        $(".pay").each(function() {
            total += parseFloat($(this).val()) || 0;
        });

        if (total >= gtotal) {
            AIZ.plugins.notify('danger', 'Paid amount is exceed to Total amount.');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('accounts.payment.modal') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                // Add other data if needed
            },
            success: function(data) {
                $($('#add_new_payment').append(data)).show("slow", function() {});
                var length = $(".number").length;
                $(".number:eq(" + (length - 1) + ")").val(parseFloat($("#pay-amount").text()));
                var total2 = 0;
                $(".number").each(function() {
                    total2 += parseFloat($(this).val()) || 0;
                });
            }
        });
    });
</script>
@endsection