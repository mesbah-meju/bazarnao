


@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Edit Cash Transfer')}}</h5>
            </div>

            <?php
                $warehousearray = getWearhouseBuUserId(auth()->user()->id);
                $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
            ?>
            <form class="form-horizontal" action="{{ route('cash-transfers.update', $transfer->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <!-- Line 1: Voucher Type and Date -->
                        <div class="form-group col-md-6">
                            <label for="txtVNo" class="form-label">{{ translate('Voucher Type') }} <span class="text-danger">*</span></label>
                            <select name="txtVNo" id="txtVNo" class="form-control aiz-selectpicker" required>
                                <option value="">Select Voucher Type</option>
                                <option value="Cash to Cash" @selected($transfer->voucher_type == 'WCTC')>Cash to Cash</option>
                                <option value="Cash to Bank" @selected($transfer->voucher_type == 'WCTB')>Cash to Bank</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="dtpDate" class="form-label">{{ translate('Date') }} <span class="text-danger">*</span></label>
                            <input type="text" name="dtpDate" id="dtpDate" class="form-control datepicker" value="{{ date('m/d/Y', strtotime($transfer->voucher_date)) }}" required>
                        </div>

                        <!-- Line 2: From Warehouse and Account Head for From Warehouse -->
                        @if(Auth::user()->user_type == 'admin')   
                        <div class="form-group col-md-6">
                            <label for="from_warehouse" class="form-label">{{translate('From Warehouse')}} <span class="text-danger">*</span></label>
                            <select name="from_warehouse" id="from_warehouse" class="form-control aiz-selectpicker" data-live-search="true" required>
                                <option value="">Select a warehouse</option>
                                @foreach (\App\Models\Warehouse::all() as $key => $warehouse)
                                <option value="{{ $warehouse->id }}" @selected($warehouse->id == $transfer->from_warehouse_id)>{{ $warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <div class="form-group col-md-6">
                            <label for="from_warehouse" class="form-label">{{translate('From Warehouse')}} <span class="text-danger">*</span></label>
                            <select name="from_warehouse" id="from_warehouse" class="form-control aiz-selectpicker" data-live-search="true" required>
                                <option value="">Select a warehouse</option>
                                @foreach ($warehouses as $key => $warehouse)
                                <option value="{{ $warehouse->id }}" @selected($warehouse->id == $transfer->from_warehouse_id)>{{ $warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="form-group col-md-6">
                            <label for="cmbCredit" class="form-label">{{ translate('Account Head for From Warehouse') }} <span class="text-danger">*</span></label>
                            <select name="cmbCredit" id="cmbCredit" class="form-control aiz-selectpicker" data-live-search="true" required>
                                <option value="">Select Account Head</option>
                                @foreach ($crcc as $cracc)
                                <option value="{{ $cracc->head_code }}" data-isbank="{{ $cracc->is_bank_nature }}" @selected($cracc->head_code == $transfer->from_coa_id)>{{ $cracc->head_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Line 3: To Warehouse and Account Head for To Warehouse -->
                        <div class="form-group col-md-6">
                            <label for="to_warehouse" class="form-label">{{translate('To Warehouse')}} <span class="text-danger">*</span></label>
                            <select name="to_warehouse" id="to_warehouse" class="form-control aiz-selectpicker" data-live-search="true" required>
                                <option value="">Select a warehouse</option>
                                @foreach (\App\Models\Warehouse::where('id','!=', $transfer->from_warehouse_id)->get() as $key => $warehouse)
                                <option value="{{ $warehouse->id }}" @selected($warehouse->id == $transfer->to_warehouse_id)>{{ $warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="cmbDebit" class="form-label">{{ translate('Account Head for To Warehouse') }} <span class="text-danger">*</span></label>
                            <select name="cmbDebit" id="cmbDebit" class="form-control aiz-selectpicker" data-live-search="true" required>
                                <!-- Options will be populated dynamically based on voucher type -->
                            </select>
                        </div>

                        <!-- Line 4: Amount and Remark -->
                        <div class="form-group col-md-6">
                            <label for="txtAmount" class="form-label">{{ translate('Amount') }} <span class="text-danger">*</span></label>
                            <input type="number" name="txtAmount" id="txtAmount" class="form-control" step=".01" value="{{ $transfer->amount }}" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="txtRemarks" class="form-label">{{ translate('Remark') }}</label>
                            <textarea name="txtRemarks" id="txtRemarks" class="form-control">{{ $transfer->ledger_comment }}</textarea>
                        </div>
                    </div>

                    <input type="hidden" name="finyear" value="{{ get_financial_year() }}">
                    <div class="form-group form-group-margin row">
                        <div class="col-sm-12 text-right">
                            <input type="submit" id="add_receive" class="btn btn-success btn-large" name="save" value="{{ translate('Update Cash Transfer') }}" tabindex="9" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function () {
        var selectedToCoa = '{{ $transfer->to_coa_id ?? '' }}'; // Store the saved to_coa_id
        
        // Handle from warehouse change
        $('#from_warehouse').on('change', function () {
            var from_warehouse_id = $(this).val();
            $('#to_warehouse').find('option:not(:first)').remove();

            @foreach (\App\Models\Warehouse::all() as $warehouse)
                if ({{ $warehouse->id }} != from_warehouse_id) {
                    $('#to_warehouse').append(new Option('{{ $warehouse->name }}', '{{ $warehouse->id }}'));
                }
            @endforeach
            
            AIZ.plugins.bootstrapSelect('refresh');
        });

        // Handle voucher type change for Account Head for To Warehouse
        $('#txtVNo').on('change', function () {
            var voucherType = $(this).val();
            $('#cmbDebit').empty();
            
            // Add default option
            $('#cmbDebit').append(new Option('Select Account Head', ''));

            if (voucherType === 'Cash to Cash') {
                // Use $crcc for Cash to Cash
                @foreach ($crcc as $cracc)
                    var option = new Option('{{ $cracc->head_name }}', '{{ $cracc->head_code }}');
                    if ('{{ $cracc->head_code }}' === selectedToCoa) {
                        option.selected = true;
                    }
                    $('#cmbDebit').append(option);
                @endforeach
            } else if (voucherType === 'Cash to Bank') {
                // Use $crcc2 for Cash to Bank
                @foreach ($crcc2 as $cracc2)
                    var option = new Option('{{ $cracc2->head_name }}', '{{ $cracc2->head_code }}');
                    if ('{{ $cracc2->head_code }}' === selectedToCoa) {
                        option.selected = true;
                    }
                    $('#cmbDebit').append(option);
                @endforeach
            }
            
            AIZ.plugins.bootstrapSelect('refresh');
        });

        // Initialize Account Head for To Warehouse on page load with saved value
        $('#txtVNo').trigger('change');
    });
</script>
@endsection