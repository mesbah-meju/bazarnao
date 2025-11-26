<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Customer Receive')}}</h5>
            </div>

            <form action="{{ route('customer-receive.store') }}" id="customer_receive_form" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label for="dtpDate" class="col-sm-2 col-form-label">{{ translate('Date') }}<i class="text-danger">*</i></label>
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
                                    <th class="text-center">{{ translate('Customer Name') }}<i class="text-danger">*</i></th>
                                    <th class="text-center">{{ translate('Voucher No') }}</th>
                                    <th class="text-center">{{ translate('Due Amount') }}</th>
                                    <th class="text-center">{{ translate('Amount') }}<i class="text-danger">*</i></th>
                                </tr>
                            </thead>
                            <tbody id="debitvoucher">
                                <tr>
                                    <td class="" width="300">
                                        <select name="customer_id" id="customer_id_1" class="form-control aiz-selectpicker" data-live-search="true" onchange="load_customer_code(this.value,1)" required>
                                            <option value="">Select Customer</option>
                                            @foreach ($customers as $customer)
                                            @if($customer->user)
                                            <option value="{{ $customer->user->id }}" @selected($customer_id == $customer->user->id)>{{ $customer->user->name }}@if($customer->user->phone) ({{ $customer->user->phone }}) @endif</option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="hidden" name="txtCode" id="txtCode_1" class="form-control" value="" readonly="">
                                        <select name="voucher_no" id="voucher_no_1" class="form-control aiz-selectpicker" onchange="voucher_due(this.value)" required>
                                            <option value="">Select Voucher</option>
                                            @foreach ($vouchers as $voucher)
                                                <option value="{{ $voucher->id }}" @selected($voucher->id == $order_id)>{{ $voucher->code }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="dueAmount" value="{{ $due_amount }}" class="form-control text-right" id="due_1" readonly="">
                                    </td>
                                    <td>
                                        <input type="number" name="txtAmount" id="txtAmount_1" class="form-control total_price text-right" step="0.01" value="" onkeyup="CustomerRcvcalculation(1)" required>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>
                                    </td>
                                    <td colspan="2" class="text-right">
                                        <label for="reason" class="col-form-label">{{ translate('Total') }}</label>
                                    </td>
                                    <td class="text-right">
                                        <input type="text" id="grandTotal" class="form-control text-right" name="grand_total" value="" readonly="readonly" />
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        <input type="hidden" name="finyear" id="finyear" value="{{ get_financial_year() }}">
                        <p hidden id="old-amount">0</p>
                        <p hidden id="pay-amount"></p>
                        <p hidden id="change-amount"></p>
                        <div class="col-sm-6 border p-4">
                            <div id="adddiscount" class="display-none">
                                <div class="row gutters-5">
                                    <div class="form-group col-md-5">
                                        <label for="payments" class="col-form-label pb-2"><?php echo translate('Payment Type');?></label>
                                        @php $card_type = 1020101; @endphp
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
                            <input type="submit" id="add_receive" class="btn btn-success btn-large" name="save" value="<?php echo translate('Save') ?>" tabindex="9" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ static_asset('assets/js/accounts/customer-receive.js') }}" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    function load_customer_code(id,sl) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url : "{{ route('customer-receive.due-vouchers') }}",
            type: "POST",
            data: {
                customer_id: id,
            },
            success: function(data) {
                var obj = jQuery.parseJSON(data);
                $('#txtCode_'+sl).val(obj.headcode);
                $('#voucher_no_1').html(obj.vouchers);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function voucher_due(id) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url : "{{ route('customer-receive.due-amount') }}",
            type: "POST",
            data: {
                order_id: id,
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

    $(document).ready(function () {
        $('#add_receive').on('click', function (e) {
            e.preventDefault(); // prevent form submission

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to save this customer receive entry?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#customer_receive_form').submit();
                }
            });
        });
    });
</script>

