@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Add Contra Voucher')}}</h5>
            </div>

            <?php
                $warehousearray = getWearhouseBuUserId(auth()->user()->id);
                $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
            ?>

            <form class="form-horizontal" action="{{ route('contra-vouchers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label for="txtVNo" class="col-sm-2 col-form-label">{{ translate('Voucher Type') }} <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <input type="text" name="txtVNo" id="txtVNo" value="Contra" class="form-control" readonly required/>
                        </div>
                    </div>
                    @if(Auth::user()->user_type == 'admin')
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">{{translate('Warehouse')}} <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <select name="warehouse" id="warehouse" class="form-control aiz-selectpicker" data-live-search="true" required>
                                <option value="">Select a warehouse</option>
                                @foreach (\App\Models\Warehouse::all() as $key => $warehouse)
                                <option  value="{{ $warehouse->id }}">{{ $warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @else
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">{{translate('Warehouse')}} <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <select name="warehouse" id="warehouse" class="form-control aiz-selectpicker" data-live-search="true" required>
                                <option value="">Select a warehouse</option>
                                @foreach ($warehouses as $key => $warehouse)
                                <option  value="{{ $warehouse->id }}">{{ $warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif

                    <div class="form-group row">
                        <label for="cmbDebit" class="col-sm-2 col-form-label">{{ translate('Reverse Account Head') }} <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <select name="cmbDebit" id="cmbDebit" class="form-control aiz-selectpicker" data-live-search="true" required>
                                <option value="" data-isbank="">Select Option</option>
                                @foreach ($acc as $cracc)
                                <option value="{{ $cracc->head_code }}" data-isbank="{{ $cracc->is_bank_nature }}">{{ $cracc->head_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
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
                        <table class="table table-bordered table-hover" id="contraAccVoucher">
                            <thead>
                                <tr>
                                    <th width="20%" class="text-center">{{ translate('Account Name') }} <span class="text-danger">*</span></th>
                                    <th width="30%" class="text-center">{{ translate('Ledger Comment') }}</th>
                                    <th width="20%" class="text-center">{{ translate('Debit') }} <span class="text-danger">*</span></th>
                                    <th width="20%" class="text-center">{{ translate('Credit') }} <span class="text-danger">*</span></th>
                                    <th width="10%" class="text-center">{{ translate('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody id="contravoucher">
                                <tr>
                                    <td class="expenseincometd">
                                        <select name="cmbCode[]" id="cmbCode_1" required class="form-control aiz-selectpicker" data-live-search="true">
                                            <option value="">Please Option</option>
                                            @foreach ($acc as $acc1)
                                            <option value="{{ $acc1->head_code }}">{{ $acc1->head_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="txtComment[]" value="" class="form-control" id="txtComment_1">
                                    </td>
                                    <td>
                                        <input type="number" name="txtAmount[]" step=".01" value="0.00" placeholder="0.00" class="form-control total_price text-right" id="txtAmount_1" onkeyup="calculationContrav(1)" required>
                                    </td>
                                    <td>
                                        <input type="number" name="txtAmountcr[]" step=".01" value="0.00" placeholder="0.00" class="form-control total_price1 text-right" id="txtAmount1_1" onkeyup="calculationContrav(1)" required>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger red text-right" type="button" value="{{ translate('Delete') }}" onclick="deleteRowContrav(this)"><i class="las la-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>
                                        <input type="button" id="add_more" class="btn btn-info" name="add_more" onClick="addaccountContrav('contravoucher');" value="{{ translate('Add More') }}" />
                                    </td>
                                    <td colspan="1" class="text-right">
                                        <label for="reason" class="col-form-label">{{ translate('Total') }}</label>
                                    </td>
                                    <td class="text-right">
                                        <input type="text" id="grandTotal" class="form-control text-right" name="grand_total" value="" readonly="readonly" value="0" />
                                    </td>
                                    <td class="text-right">
                                        <input type="text" id="grandTotal1" class="form-control text-right" name="grand_total1" value="" readonly="readonly" value="0" />
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <input type="hidden" name="finyear" value="{{ get_financial_year() }}">
                    <div class="form-group form-group-margin row">
                        <div class="col-sm-12 text-right">
                            <input type="submit" id="add_receive" class="btn btn-success btn-large" name="save" value="{{ translate('Save') }}" tabindex="9" />
                            <input type="hidden" name="" id="base_url" value="{{ url('/') }}">
                            <input type="hidden" name="" id="headoption" value="<option value=''> Select Option</option>@foreach ($acc as $acc2)<option value='{{ $acc2->head_code }}'>{{ $acc2->head_name }}</option>@endforeach">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="{{ static_asset('assets/js/accounts/contra-vouchers.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        $('#add_receive').on('click', function(e) {
            e.preventDefault(); // Stop the normal form submit for now
            
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to save this Contra Voucher?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If user confirms, then submit the form
                    $(this).closest('form').submit();
                }
            })
        });
    });
</script>
@endsection