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

        @if($purchase[0]->payment_status == null || $purchase[0]->payment_status == 1 || $purchase[0]->payment_status == 2)
        <div class="row mb-3">
            <div class="col-md-10 d-flex justify-content-between">
                <div class="col-md-5">
                    <label for="payment_type" class="form-label">{{ translate('Select Payment Type') }}</label>
                    <select class="form-control" id="payment_type" {{ $purchase[0]->payment_status == 3 ? 'disabled' : '' }}>
                        <option value="">{{ translate('Select Payment Type') }}</option>
                        @foreach($paymentTypes as $key => $value)
                            <option value="{{ $value }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="update_payment_status" class="form-label">{{ translate('Change Payment Status') }}</label>
                    <select class="form-control" id="update_payment_status" {{ $purchase[0]->payment_status == 3 ? 'disabled' : '' }}>
                        <option value="">{{ translate('Change Payment Status') }}</option>
                        <option value="3" {{ $purchase[0]->payment_status == 3 ? 'selected' : '' }}>{{ translate('Paid') }}</option>
                        <option value="2" {{ $purchase[0]->payment_status == 2 ? 'selected' : '' }}>{{ translate('Partial Payment') }}</option>
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
                        <p><strong>{{ translate('Supplier Name') }}:</strong> {{ $purchase[0]->name }}</p>
                        <p><strong>{{ translate('Purchase No') }}:</strong> {{ $purchase[0]->purchase_no }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>{{ translate('Purchase Date') }}:</strong> {{ $purchase[0]->date }}</p>
                        <p><strong>{{ translate('Remarks') }}:</strong> {{ $purchase[0]->remarks }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>{{ translate('Chalan No') }}:</strong> {{ $purchase[0]->chalan_no }}</p>
                    </div>
                    @if($purchase[0]->voucher_img)
                    <div class="col-md-6">
                        <p><strong>{{ translate('Chalan Voucher') }}:</strong></p>
                        <a href="{{ uploaded_asset($purchase[0]->voucher_img) }}" target="_blank" class="btn btn-sm btn-info">
                            <i class="las la-eye"></i> {{ translate('View Voucher') }}
                        </a>
                    </div>
                    @endif
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
                            <th>SL</th>
                            <th>Product</th>
                            <th>Description</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Unit Price</th>
                            <th class="text-right">Amount (Tk)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $total_qty = [];
                        $total_mrp = [];
                        $amount = [];
                        if ($data_item_rows) {
                            foreach ($data_item_rows as $value) {
                        ?>
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $value->name }}</td>
                            <td>{{ $value->desc }}</td>
                            <td class="text-center">{{ $total_qty[] = $value->qty }}</td>
                            <td class="text-right">{{ $total_mrp[] = $value->price }}</td>
                            <td class="text-right">{{ $amount[] = $value->amount }}</td>
                        </tr>
                        <?php
                            }
                        }
                        ?>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                            <td class="text-center"><strong>{{ array_sum($total_qty) }}</strong></td>
                            <td class="text-right"><strong>{{ array_sum($total_mrp) }}</strong></td>
                            <td class="text-right"><strong>{{ array_sum($amount) }}</strong></td>
                        </tr>
                    </tbody>
                </table>

                <div class="float-right">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td><strong class="text-muted">{{ translate('Paid Amount') }}:</strong></td>
                                <td class="text-right">{{ $purchase[0]->payment_amount }}</td>
                            </tr>
                            <tr>
                                <td><strong class="text-muted">{{ translate('Due Amount') }}:</strong></td>
                                <td class="text-right">{{ array_sum($amount) - $purchase[0]->payment_amount }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <div class="modal fade" id="payment-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="exampleModalLabel">{{ translate('Payment') }}</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php
                        if ($purchase[0]->payment_amount == null) {
                            $amount_to_pay = $purchase[0]->total_value;
                        } else {
                            $amount_to_pay = $purchase[0]->total_value - $purchase[0]->payment_amount;
                        }
                    ?>
                    <div class="modal-body">
                        <input type="hidden" id="total_due" value="{{ $amount_to_pay }}">

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label">{{ translate('Amount') }}</label>
                            <div class="col-md-8">
                                <input type="number" id="payment_amount" value="{{ $amount_to_pay }}" class="form-control" placeholder="{{ translate('Amount') }}" required>
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
                        <button type="button" id="save_payment" onclick="save_payment()" class="btn btn-primary">{{ translate('Save') }}</button>
                    </div>
                </div>
            </div>
        </div>

    </section>
</section>

@section('script')
<script>
$('#update_payment_status').on('change', function() {
    var order_id = {{ $purchase[0]->id }};
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
    var purchase_id = {{ $purchase[0]->id }};
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

@endsection
