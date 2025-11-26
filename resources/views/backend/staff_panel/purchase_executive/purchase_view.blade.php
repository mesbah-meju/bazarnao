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

<section id="main-content">
    <section class="wrapper">
        @if(auth()->user()->staff->role->name=='Purchase Executive')
        @include('backend.staff_panel.purchase_executive.purchase_executive_nav')
        @elseif(auth()->user()->staff->role->name=='Account Executive' || auth()->user()->staff->role->name=='Account Manager')
        @include('backend.staff_panel.account_executive.account_executive_nav')
        @else
        @include('backend.staff_panel.purchase_manager.purchase_manager_nav')
        @endif
        <!-- page start-->
        <div class="row">
            <div class="col-lg-12">
                <!-- <span class="pull-left" style="margin-left: 10px;margin-bottom: 10px;"><a href="{{route('purchase_orders.index')}}"><i class="fa fa-fast-backward"></i> Back</a></span> -->
                <span class="pull-right" style="margin-right: 10px;margin-bottom: 10px;">

                    <a class="btn btn-circle btn-info" href="Javascript:" onclick="printDivd('print_contents')"><i class="fa fa-print"></i> Print</a>
                </span>
            </div>
            <!-- <div class="col-md-12">
                @if($purchase[0]->payment_status == null || $purchase[0]->payment_status == 1 || $purchase[0]->payment_status == 2)
                @php
                    
                    $data = DB::table('acc_coas')
                        ->where('pre_head_name', 'Cash')
                        ->orWhere('pre_head_name', 'Cash at Bank')
                        ->where('is_active', 1)
                        ->get();

                    $paymentTypes = [];
                    if ($data->isNotEmpty()) {
                        $paymentTypes[0] = 'Credit Purchase';
                        
                        foreach ($data as $value) {
                            $paymentTypes[$value->head_code] = $value->head_name;
                        }
                    }
                @endphp
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
            </div> -->
            <div class="clearfix"></div>
            <div class="col-lg-12" id="print_contents">
                <section class="panel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Supplier Information')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="col-md-6 pull-left">
                                <label>{{translate('Supplier Name')}} <span>:</span> {{$purchase[0]->name}}</label>
                            </div>
                            <div class="col-md-6 pull-left">
                                <label>{{translate('Purchase Date')}} <span>*</span></label>
                                {{$purchase[0]->date}}
                            </div>
                            <div class="col-md-6 pull-left">
                                <label>{{translate('Purchase No')}} <span>:</span></label>
                                {{$purchase[0]->purchase_no}}
                            </div>
                            <div class="col-md-6 pull-left">
                                <label>{{translate('Remarks')}} <span>:</span></label>
                                {{$purchase[0]->remarks}}
                            </div>
                            @if($purchase[0]->voucher_img)
                            <div class="col-md-6 pull-left">
                                <label>{{translate('Chalan Voucher')}} <span>:</span></label>
                                <a href="{{ uploaded_asset($purchase[0]->voucher_img) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="las la-eye"></i> {{ translate('View Voucher') }}
                                </a>
                            </div>
                            @endif
                             <!-- Payment Receive Button -->
                             @if (auth()->user()->name == 'Super Admin' || auth()->user()->staff->role->name == 'Purchase Manager')
                             <div class="col-md-6 pull-left">
                                <form action="{{ route('supplier-receive.purchasewise', ['id' => $purchase[0]->id]) }}" method="POST" target="_blank" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        {{ translate('Paid') }}
                                    </button>
                                </form>
                            </div>
                            @endif

                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Product Information')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <table id="add_line" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Product</th>
                                            <th>Description</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-center">Unit Price</th>
                                            <th class="text-right">Amount(Tk)</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        $total_qty = array();
                                        $total_mrp = array();
                                        $total_mrp_sum = array();
                                        $total_mrp_discounted = array();
                                        $amount = array();
                                        if ($data_item_rows) {

                                            foreach ($data_item_rows as $value) {
                                        ?>
                                                <tr id="item_row_1" class="global_item_row">
                                                    <td>{{{$i++}}}</td>
                                                    <td>{{{$value->name}}}</td>
                                                    <td>{{{$value->decs}}}</td>
                                                    <td class="text-center">{{{$total_qty[] = $value->qty}}}</td>
                                                    <td class="text-right">{{{$total_mrp[] = $value->price}}}</td>

                                                    <td class="text-right">{{{$amount[] = $value->amount}}}</td>


                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td class="text-right fwb" colspan="3">Total:</td>
                                            <td class="fwb text-center">{{ array_sum($total_qty) }}</td>
                                            <td class="text-right">{{ array_sum($total_mrp) }}</td>
                                            <td class="text-right">{{ array_sum($amount) }}</td>

                                        </tr>
                                        <tr>
                                            <td class="text-right fwb" colspan="3">Total Paid:</td>
                                            <td class="text-right fwb" colspan="3">{{ $purchase[0]->payment_amount }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right fwb" colspan="3">Total Due:</td>
                                            <td id="total_due" class="text-right fwb" colspan="3">{{ array_sum($amount)-$purchase[0]->payment_amount }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
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
<!--main content end-->
@section('script')
<script>
    $('#update_payment_status').on('change', function() {
        var order_id = {{ $purchase[0]->id }};

        var dueAmount = parseFloat($('#total_due').text().trim()); 

        var status = $('#update_payment_status').val();
        if (status == 3) {
            $('#payment-modal').modal('show');
            $('#payment_amount').val(dueAmount);
            $('#payment_amount').attr('disabled', true);
        } else if (status == 2) {

            $('#payment-modal').modal('show');
            $('#payment_amount').val('');
            $('#payment_amount').val(dueAmount);
            $('#payment_amount').attr('disabled', false);
        } else if (status == 'unpaid') {
            $.post('{{ route("orders.update_payment_status") }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            },
            function(data) {
                AIZ.plugins.notify('success', "{{ translate('Payment status has been updated ') }}");
                location.reload().setTimeOut(500);
            });
        }
    });

    function save_payment() {
        $('#save_payment').attr('disabled', true);

        var dueAmount = parseFloat($('#total_due').text().trim()); 
        var purchase_id = {{ $purchase[0]->id }};
        var status = $('#update_payment_status').val();
        var payment_type = $('#payment_type').val();
        var payment_amount = parseFloat($('#payment_amount').val());
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

        $.post('{{ route("orders.purchase_update_payment_status") }}', {
            _token: '{{ @csrf_token() }}',
            payment_amount: payment_amount,
            payment_date: payment_date,
            purchase_id: purchase_id,
            status: status,
            payment_type: payment_type
        },
        function(data) {
            AIZ.plugins.notify('success', "{{ translate('Payment status has been updated ') }}");
            $('#payment-modal').modal('hide');
            location.reload();
        });
    }

    function printDivd(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }
    
    $('#payment_amount').keyup(function() {
        var pay = parseInt($(this).val());
        var dueAmount = parseInt($('#total_due').text());
        if (pay > dueAmount) {
            alert('Paid amount must be equal OR less than due amount');
            $('#payment_amount').val(dueAmount);
        }

    })
</script>
@endsection
@stop