@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Add Debit Voucher')}}</h5>
            </div>
            <?php
                $warehousearray = getWearhouseBuUserId(auth()->user()->id);
                $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
            ?>

            <form class="form-horizontal" action="{{ route('debit-vouchers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label for="txtVNo" class="col-sm-2 col-form-label">{{ translate('Voucher Type') }} <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <input type="text" name="txtVNo" id="txtVNo" value="Debit" class="form-control" readonly required/>
                        </div>
                    </div>
                    @if(Auth::user()->user_type == 'admin')
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">{{translate('Warehouse')}} <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <select id="warehouse" class="form-control aiz-selectpicker select2" name="warehouse" data-live-search="true" required>
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
                            <select id="warehouse" class="form-control aiz-selectpicker select2" name="warehouse" data-live-search="true" required>
                                <option value="">Select a warehouse</option>
                                @foreach ($warehouses as $key => $warehouse)
                                <option  value="{{ $warehouse->id }}">{{ $warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif

                    <div class="form-group row">
                        <label for="ac" class="col-sm-2 col-form-label">{{ translate('Credit Account Head') }} <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <select name="cmbCredit" id="cmbCredit" class="form-control aiz-selectpicker" data-live-search="true" required>
                                <option value="" data-isbank="">Select Option</option>
                                @foreach ($crcc as $cracc)
                                <option value="{{ $cracc->head_code }}" data-isbank="{{ $cracc->is_bank_nature }}">{{ $cracc->head_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="isbanknature" style="display:none">
                        <div class="form-group row">
                            <label for="chequeNo" class="col-sm-2 col-form-label">{{ translate('Cheque No') }}</label>
                            <div class="col-sm-4">
                                <input type="text" name="chequeNo" id="chequeNo" class="form-control" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="chequeDate" class="col-sm-2 col-form-label">{{ translate('Cheque Date') }}</label>
                            <div class="col-sm-4">
                                <input type="text" name="chequeDate" id="chequeDate" class="form-control datepicker financialyear" value="{{ date('m/d/Y') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="ishonours" class="col-sm-2 col-form-label">{{ translate('Is Honours') }}</label>
                            <div class="col-sm-4">
                                <input type="checkbox" value="1" name="ishonours" id="ishonours" size="28">
                            </div>
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

                    <div class="">
                        <table class="table table-bordered table-hover" id="debitAccVoucher">
                            <thead>
                                <tr>
                                    <th width="15%" class="text-center">{{ translate('Account Name') }} <span class="text-danger">*</span></th>
                                    <th width="15%" class="text-center">{{ translate('Sub Type') }} <span class="text-danger">*</span></th>
                                    <th width="15%" class="text-center">{{ translate('Relational Type') }}</th>
                                    <th width="15%" class="text-center">{{ translate('Relational Value') }}</th>
                                    <th width="20%" class="text-center">{{ translate('Ledger Comment') }}</th>
                                    <th width="15%" class="text-center">{{ translate('Amount') }} <span class="text-danger">*</span></th>
                                    <th width="5%" class="text-center">{{ translate('Action') }}</th>
                                </tr>
                            </thead>

                            <tbody id="debitvoucher">
                                <tr>
                                    <td class="expenseincometd">
                                        <select name="cmbCode[]" id="cmbCode_1" required class="form-control aiz-selectpicker" data-live-search="true" onchange="load_subtypeDebitv(this.value,1)">
                                            <option value="">Please Option</option>
                                            @foreach ($acc as $acc1)
                                            <option value="{{ $acc1->head_code }}">{{ $acc1->head_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="subtype[]" id="subtype_1" class="form-control aiz-selectpicker" data-live-search="true" required>
                                            <option value="">Please Option</option>
                                        </select>
                                    </td>
                                    <td class="reltype">
                                        <select name="reltype[]" id="reltype_1" class="form-control aiz-selectpicker" data-live-search="true" onchange="load_relvalue(this.value,1)">
                                            <option value="">Please Option</option>
                                            @foreach ($rel_types as $rel_type)
                                            <option value="{{ $rel_type->id }}">{{ $rel_type->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="relvalue[]" id="relvalue_1" class="form-control aiz-selectpicker" data-live-search="true" disabled>
                                            <option value="">Please Option</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="hidden" name="isSubtype[]" id="isSubtype_1" value="1" />
                                        <input type="text" name="txtComment[]" value="" class="form-control" id="txtComment_1">
                                    </td>
                                    <td>
                                        <input type="number" name="txtAmount[]" required step=".01" value="" class="form-control total_price text-right" id="txtAmount_1" onkeyup="calculationDebitv(1)">
                                    </td>
                                    <td>
                                        <button class="btn btn-danger red text-right" type="button" value="{{ translate('Delete') }}" onclick="deleteRowDebitv(this)"><i class="las la-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>
                                        <input type="button" id="add_more" class="btn btn-info" name="add_more" onClick="addaccountDebit('debitvoucher');" value="{{ translate('Add More') }}" />
                                    </td>
                                    <td colspan="4" class="text-right">
                                        <label for="reason" class=" col-form-label">{{ translate('Total') }}</label>
                                    </td>
                                    <td class="text-right">
                                        <input type="text" id="grandTotal" class="form-control text-right" name="grand_total" value="" readonly="readonly" />
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
                            <input type="hidden" name="" id="reltypeoption" value="<option value=''> Select Option</option>@foreach ($rel_types as $rel_type2)<option value='{{ $rel_type2->id }}'>{{ $rel_type2->name }}</option>@endforeach">
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

<script src="{{ static_asset('assets/js/accounts/debit-vouchers.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    function load_subtypeDebitv(id, sl) {
        get_subtypeCode(id, sl);
        $.ajax({
            url: "{{ route('opening-balances.subtypecode', '') }}/" + id,
            type: "GET",
            dataType: "json",
            success: function(data) {
                if (data != '') {
                    $('#subtype_' + sl).html(data);
                    $('#subtype_' + sl).removeAttr("disabled");
                } else {
                    $('#subtype_' + sl).attr("disabled", "disabled");
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function get_subtypeCode(id, sl) {
        $.ajax({
            url: "{{ route('opening-balances.subtypebyid', '') }}/" + id,
            type: "GET",
            dataType: "json",
            success: function(data) {
                if (data.sub_type != 1) {
                    $('#isSubtype_' + sl).val(data.sub_type);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }

    function load_relvalue(id, sl) {
        $.ajax({
            url: "{{ route('opening-balances.relvaluebyid', '') }}/" + id,
            type: "GET",
            dataType: "json",
            success: function(data) {
                if (data != '') {
                    $('#relvalue_' + sl).html(data);
                    $('#relvalue_' + sl).removeAttr("disabled");
                } else {
                    $('#relvalue_' + sl).attr("disabled", "disabled");
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#relvalue_' + sl).html();
                $('#relvalue_' + sl).attr("disabled", "disabled");
                alert('Error get data from ajax');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form[action="{{ route('debit-vouchers.store') }}"]');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Prevent the default form submission

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to save this debit voucher?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, save it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If confirmed, submit the form
                        form.submit();
                    }
                });
            });
        }
    });
</script>

@endsection