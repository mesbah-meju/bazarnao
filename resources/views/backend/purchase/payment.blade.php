@extends('backend.layouts.app')
@section('content')

    <section id="main-content">
        <section class="wrapper">
            <!-- page start-->
            <div class="row mb-3">
                <div class="col-lg-12 d-flex justify-content-between align-items-center">
                    <a href="{{ route('purchase_orders.index') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-fast-backward"></i> Back
                    </a>
                    <a href="javascript:;" onclick="printDivd('print_contents')" class="btn btn-info">
                        <i class="fa fa-print"></i> Print
                    </a>
                </div>
            </div>
            @php
                $data = DB::table('acc_coas')
                    ->where('pre_head_name', 'Cash')
                    ->orWhere('pre_head_name', 'Cash at Bank')
                    ->get();

                $paymentTypes = [];

                if ($data->isNotEmpty()) {
                    $paymentTypes[0] = 'Credit Purchase';

                    foreach ($data as $value) {
                        $paymentTypes[$value->head_code] = $value->head_name;
                    }
                }
            @endphp

            @if ($purchase->payment_status == null || $purchase->payment_status == 1 || $purchase->payment_status == 2)
                <div class="row mb-3">
                    <div class="col-md-10 d-flex justify-content-between">
                        <div class="col-md-5">
                            <label for="payment_type" class="form-label">{{ translate('Select Payment Type') }}</label>
                            <select class="form-control" id="payment_type"
                                {{ $purchase->payment_status == 3 ? 'disabled' : '' }}>
                                <option value="">{{ translate('Select Payment Type') }}</option>
                                @foreach ($paymentTypes as $key => $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="update_payment_status"
                                class="form-label">{{ translate('Change Payment Status') }}</label>
                            <select class="form-control" id="update_payment_status"
                                {{ $purchase->payment_status == 3 ? 'disabled' : '' }}>
                                <option value="">{{ translate('Change Payment Status') }}</option>
                                <option value="3" {{ $purchase->payment_status == 3 ? 'selected' : '' }}>
                                    {{ translate('Paid') }}</option>
                                <option value="2" {{ $purchase->payment_status == 2 ? 'selected' : '' }}>
                                    {{ translate('Partial Payment') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Supplier Information Section -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0 h6">{{ translate('Supplier Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>{{ translate('Supplier Name') }}:</strong> {{ $purchase->name }}</p>
                            <p><strong>{{ translate('Purchase No') }}:</strong> {{ $purchase->purchase_no }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ translate('Purchase Date') }}:</strong> {{ $purchase->date }}</p>
                            <p><strong>{{ translate('Remarks') }}:</strong> {{ $purchase->remarks }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ translate('Chalan No') }}:</strong> {{ $purchase->chalan_no }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Information Section -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0 h6">{{ translate('Product Information') }}</h5>
                </div>
                <div class="card-body">
                    <table id="add_line" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Description</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Unit Price</th>
                                <th class="text-right" width="250px">Amount (Tk)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                                $total_amount = 0;
                            @endphp
                            @if ($data_item_rows->isNotEmpty())
                                @foreach ($data_item_rows as $value)
                                    @php
                                        $total_amount += $value->amount;
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" placeholder="Product Name"
                                                value="{{ $value->name }}" readonly />
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="Product Description"
                                                value="{{ $value->desc }}" readonly />
                                        </td>
                                        <td>
                                            <input type="text" class="text-right form-control" placeholder="0.00"
                                                value="{{ $value->qty }}" readonly />
                                        </td>
                                        <td>
                                            <input type="text" class="text-right form-control" placeholder="0.00"
                                                value="{{ number_format($value->price, 2, '.', '') }}" readonly />
                                        </td>
                                        <td>
                                            <input type="text" class="text-right form-control" placeholder="0.00"
                                                value="{{ number_format($value->amount, 2, '.', '') }}" readonly />
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            {{-- <tr>
                                <td class="text-right" colspan="5">
                                    <b>{{ translate('Total') }}</b>
                                </td>
                                <td class="text-right">
                                    <input type="text" id="Total" class="text-right form-control" name="total"
                                        value="{{ number_format($total_amount, 2, '.', '') }}" readonly />
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right" colspan="5">
                                    <b>{{ translate('Discount') }}</b>
                                </td>
                                <td class="text-right">
                                    <input type="text" name="discount" id="discount"
                                        class="text-right form-control discount total_discount_val"
                                        onkeyup="calculate_store(1)" placeholder="0.00" value="" />
                                </td>
                            </tr> --}}
                            <tr>
                                <td class="text-right" colspan="4">
                                    <b>{{ translate('Grand Total') }}</b>
                                </td>
                                <td class="text-right">
                                    <input type="text" id="grandTotal" class="text-right form-control grandTotalamnt"
                                        name="grand_total_price" placeholder="0.00"
                                        value="{{ number_format($total_amount, 2, '.', '') }}" readonly />
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right" colspan="4">
                                    <b><?php echo translate('Previous Paid Amount'); ?></b>
                                </td>
                                <td class="text-right">
                                    <input type="text" id="previousPaidAmount" class="text-right form-control"
                                        name="previous_paid_amount" placeholder="0.00"
                                        value="{{ number_format($purchase->payment_amount, 2, '.', '') }}" readonly />
                                </td>
                            </tr>
                            @php
                                $due_amount = $total_amount - $purchase->payment_amount;
                                if ($advance_payment >= $due_amount) {
                                    $advance_amount = $due_amount;
                                } else {
                                    $advance_amount = $advance_payment;
                                }
                            @endphp
                            <tr>
                                <td class="text-right" colspan="4">
                                    <b><?php echo translate('Advance Paid Amount'); ?></b>
                                </td>
                                <td class="text-right">
                                    <input type="text" id="advancePaidAmount" class="text-right form-control"
                                        name="advance_paid_amount" placeholder="0.00"
                                        value="{{ number_format($advance_amount, 2, '.', '') }}" readonly />
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right" colspan="4">
                                    <b><?php echo translate('Current Paid Amount'); ?></b>
                                </td>
                                <td class="text-right">
                                    <input type="text" id="currentPaidAmount" class="text-right form-control"
                                        name="paid_amount" placeholder="0.00" value=""
                                        onKeyup="invoice_paidamount()" />
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right" colspan="4"><b><?php echo translate('Remaining Due Amount'); ?></b></td>
                                <td class="text-right">
                                    <input type="text" id="dueAmmount" class="text-right form-control"
                                        name="due_amount" placeholder="0.00"
                                        value="{{ number_format($due_amount, 2, '.', '') }}"
                                        readonly />
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    @php
                        $payment_methods = DB::table('acc_coas')
                            ->where('pre_head_name', 'Cash')
                            ->orWhere('pre_head_name', 'Cash at Bank')
                            ->where('is_active', 1)
                            ->get();

                        $payment_types = [];

                        if ($payment_methods->isNotEmpty()) {
                            $payment_types[0] = 'Credit Purchase';

                            foreach ($payment_methods as $method) {
                                $payment_types[$method->head_code] = $method->head_name;
                            }
                        }
                    @endphp

                    @if ($purchase->payment_status == null || $purchase->payment_status == 1 || $purchase->payment_status == 2)
                        <input type="hidden" name="finyear" value="<?php echo get_financial_year(); ?>">
                        <p hidden id="pay-amount"></p>
                        <p hidden id="change-amount"></p>
                        <div class="col-sm-6 table-bordered p-20">
                            <div id="adddiscount" class="display-none">
                                <div class="row no-gutters">
                                    <div class="form-group col-md-6">
                                        <label for="payments" class="col-form-label pb-2"><?php echo translate('Payment Type'); ?></label>
                                        <?php
                                        $card_type = 111000001;
                                        ?>
                                        <select name="multipaytype[]"
                                            class="form-control card_typesl postform aiz-selectpicker"
                                            id="payment_type"onchange="check_creditsale(this.value)">
                                            <option value="">{{ translate('Select Payment Method') }}</option>
                                            @foreach ($payment_types as $key => $value)
                                                <option value="{{ $key }}" @selected($card_type == $key)>
                                                    {{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="4digit" class="col-form-label pb-2"><?php echo translate('Paid Amount'); ?></label>
                                        <input type="number" id="pamount_by_method" class="form-control number pay"
                                            name="pamount_by_method[]" value="" onkeyup="changedueamount()"
                                            placeholder="0" />
                                    </div>
                                </div>

                                <div class="" id="add_new_payment">

                                </div>
                                <div class="form-group text-right">
                                    <div class="col-sm-12 pr-0">
                                        <button type="button" id="add_new_payment_type"
                                            class="btn btn-success w-md m-b-5"><?php echo translate('New Payment Method'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Modal -->
            <div class="modal fade" id="payment-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title" id="exampleModalLabel">{{ translate('Payment') }}</h6>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php
                        if ($purchase->payment_amount == null) {
                            $amount_to_pay = $purchase->total_value;
                        } else {
                            $amount_to_pay = $purchase->total_value - $purchase->payment_amount;
                        }
                        ?>
                        <div class="modal-body">
                            <input type="hidden" id="total_due" value="{{ $amount_to_pay }}">

                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">{{ translate('Amount') }}</label>
                                <div class="col-md-8">
                                    <input type="number" id="payment_amount" value="{{ $amount_to_pay }}"
                                        class="form-control" placeholder="{{ translate('Amount') }}" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">{{ translate('Payment Date') }}</label>
                                <div class="col-md-8">
                                    <input type="date" id="payment_date" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="save_payment" onclick="save_payment()"
                                class="btn btn-primary">{{ translate('Save') }}</button>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </section>
@endsection

@section('script')
    <script>
        // ******* new payment add start *******
        $(document).on("click", "#add_new_payment_type", function() {
            var gtotal = $("#currentPaidAmount").val();

            var total = 0;
            $(".pay").each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            var is_credit_edit = $("#is_credit_edit").val();
            if (total >= gtotal) {
                alert("Paid amount is exceed to Total amount.");

                return false;
            }

            $.ajax({
                type: "POST",
                url: "{{ route('purchase.payment.modal') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    // Add other data if needed
                },
                success: function(data) {
                    $($("#add_new_payment").append(data));
                    var length = $(".number").length;
                    var total3 = 0;
                    $(".pay").each(function() {
                        total3 += parseFloat($(this).val()) || 0;
                    });

                    var nextamnt = gtotal - total3;

                    $(".number:eq(" + (length - 1) + ")").val(nextamnt.toFixed(2, 2));
                    var total2 = 0;
                    $(".number").each(function() {
                        total2 += parseFloat($(this).val()) || 0;
                    });
                    var dueamnt = parseFloat(gtotal) - total2;
                },
            });
        });

        function changedueamount() {
            var inputval = parseFloat(0);
            var maintotalamount = $(".grandTotalamnt").val();
            var paidAmount = $("#currentPaidAmount").val();
            var dueAmmount = $("#dueAmmount").val();

            $(".number").each(function() {
                var inputdata = parseFloat($(this).val());
                if (isNaN(inputdata)) {
                    inputdata = 0;
                }
                inputval = inputval + inputdata;
            });

            // $("#currentPaidAmount").val(inputval);

            restamount = parseFloat(maintotalamount) - parseFloat(inputval);
            var changes = restamount.toFixed(2);

            if (changes <= 0) {
                $("#change-amount").text(Math.abs(changes));
                $("#pay-amount").text(0);
                // $("#dueAmmount").val(0);
            } else {
                $("#change-amount").text(0);
                $("#pay-amount").text(changes);
                // $("#dueAmmount").val(changes);
            }
        }

        function invoice_paidamount() {
            var t = $("#grandTotal").val(),
                a = $("#currentPaidAmount").val(),
                e = t - a;
            if (e > 0) {
                $("#dueAmmount").val(e.toFixed(2, 2));
            } else {
                $("#dueAmmount").val(0);
            }

            $("#add_new_payment").empty();
            $("#pamount_by_method").val(a);
            $("#pay-amount").text("0");
        }

        function check_creditsale(value) {
            var card_typesl = value;
            if (card_typesl == 0) {
                $("#add_new_payment").empty();
                var gtotal = $(".grandTotalamnt").val();
                $("#pamount_by_method").val(gtotal);
                $("#currentPaidAmount").val(0);
                $("#dueAmmount").val(gtotal);
                $(".number:eq(0)").val(0);
                $("#add_new_payment_type").prop("disabled", true);
            } else {
                $("#add_new_payment_type").prop("disabled", false);
            }

            $("#pay-amount").text("0");
        }

        $('#update_payment_status').on('change', function() {
            var order_id = {{ $purchase->id }};
            var dueAmount = parseInt($('#total_due').val());

            var status = $('#update_payment_status').val();
            if (status == 3) {
                $('#payment-modal').modal('show');
                $('#payment_amount').val(dueAmount);
                $('#payment_amount').attr('disabled', true);
            } else if (status == 2) {
                $('#payment-modal').modal('show');
                $('#payment_amount').val(dueAmount);
                $('#payment_amount').attr('disabled', false);
            } else if (status == 'unpaid') {
                $.post('{{ route('orders.update_payment_status') }}', {
                    _token: '{{ @csrf_token() }}',
                    order_id: order_id,
                    status: status
                }, function(data) {
                    AIZ.plugins.notify('success', '{{ translate('Payment status has been updated') }}');
                    location.reload();
                });
            }
        });

        function save_payment() {
            $('#save_payment').attr('disabled', true);

            var dueAmount = parseInt($('#total_due').val());
            var purchase_id = {{ $purchase->id }};
            var status = $('#update_payment_status').val();
            var payment_type = $('#payment_type').val();
            var payment_amount = parseInt($('#payment_amount').val());
            var payment_date = $('#payment_date').val();

            if (!payment_type) {
                alert('Please select a Payment Type');
                $('#save_payment').attr('disabled', false);
                return false;
            }

            if (payment_amount > dueAmount) {
                alert('Paid amount must be less than or equal to due amount');
                $('#payment_amount').val('');
                $('#save_payment').attr('disabled', false);
                return false;
            }

            if (payment_date === '') {
                alert('Please enter the date');
                $('#save_payment').attr('disabled', false);
                return false;
            }

            $.post('{{ route('orders.purchase_update_payment_status') }}', {
                _token: '{{ @csrf_token() }}',
                payment_amount: payment_amount,
                payment_date: payment_date,
                purchase_id: purchase_id,
                status: status,
                payment_type: payment_type
            }, function(data) {
                AIZ.plugins.notify('success', '{{ translate('Payment status has been updated') }}');
                $('#payment-modal').modal('hide');
                location.reload();
            });
        }
    </script>
@endsection
