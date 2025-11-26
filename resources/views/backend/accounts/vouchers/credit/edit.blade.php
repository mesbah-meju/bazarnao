@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Edit Credit Voucher')}}</h5>
            </div>

            <?php
                $warehousearray = getWearhouseBuUserId(auth()->user()->id);
                $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
            ?>

            <form class="form-horizontal" action="{{ route('credit-vouchers.update', $credit->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label for="txtVNo" class="col-sm-2 col-form-label">{{ translate('Voucher No') }} <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <input type="text" name="txtVNo" id="txtVNo" value="{{ $credit->voucher_no }}" class="form-control" required readonly/>
                            <input type="hidden" name="isApproved" id="isApproved" value="{{ $credit->is_approved }}">
                            <input type="hidden" name="fyear" id="fyear" value="{{ $credit->fyear }}">
                            <input type="hidden" name="CreateBy" id="CreateBy" value="{{ $credit->created_by }}">
                            <input type="hidden" name="CreateDate" id="CreateDate" value="{{ $credit->created_at }}">
                            <input type="hidden" name="finyear" value="{{ get_financial_year() }}">
                        </div>
                    </div>

                    @if(Auth::user()->user_type == 'admin')   
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">{{translate('Warehouse')}} <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <select name="warehouse" id="warehouse" class="form-control aiz-selectpicker" data-live-search="true" required>
                                <option value="">Select a warehouse</option>
                                @foreach (\App\Models\Warehouse::all() as $key => $warehouse)
                                <option  value="{{ $warehouse->id }}" @selected($credit->warehouse_id==$warehouse->id)>{{ $warehouse->name}}</option>
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
                                <option  value="{{ $warehouse->id }}" @selected($credit->warehouse_id==$warehouse->id)>{{ $warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif

                    <div class="form-group row">
                        <label for="ac" class="col-sm-2 col-form-label">{{ translate('Debit Account Head') }} <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <select name="cmbDebit" id="cmbDebit" class="form-control aiz-selectpicker" data-live-search="true" required>
                                <option value="" data-isbank="">Select Option</option>
                                @foreach ($crcc as $cracc)
                                <option value="{{ $cracc->head_code }}" data-isbank="{{ $cracc->is_bank_nature }}" @selected($credit->rev_code == $cracc->head_code)>{{ $cracc->head_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @php
                        $is_bank_nature = $credit->rev_coa->is_bank_nature;
                    @endphp

                    <div id="isbanknature" style="display: <?php echo $is_bank_nature == 1 ? 'block' : 'none' ?>;">
                        <div class="form-group row">
                            <label for="chequeNo" class="col-sm-2 col-form-label">{{ translate('Cheque No') }}</label>
                            <div class="col-sm-4">
                                <input type="text" name="chequeNo" id="chequeNo" class="form-control" value="{{ $credit->cheque_no }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="chequeDate" class="col-sm-2 col-form-label">{{ translate('Cheque Date') }}</label>
                            <div class="col-sm-4">
                                <input type="text" name="chequeDate" id="chequeDate" class="form-control datepicker financialyear" value="{{ $credit->cheque_date ? date('m/d/Y', strtotime($credit->cheque_date)) : date('m/d/Y') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="ishonours" class="col-sm-2 col-form-label">{{ translate('Is Honours') }}</label>
                            <div class="col-sm-4">
                                <input type="checkbox" value="1" name="ishonours" id="ishonours" size="28" {{ $credit->is_honour ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="dtpDate" class="col-sm-2 col-form-label">{{ translate('Date') }} <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <input type="text" name="dtpDate" id="dtpDate" class="form-control datepicker" value="{{ date('m/d/Y', strtotime($credit->voucher_date)) }}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="txtRemarks" class="col-sm-2 col-form-label">{{ translate('Remark') }}</label>
                        <div class="col-sm-4">
                            <textarea name="txtRemarks" id="txtRemarks" class="form-control">{{ $credit->narration }}</textarea>
                        </div>
                    </div>

                    <div class="">
                        <table class="table table-bordered table-hover" id="creditAccVoucher">
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

                            <tbody id="creditvoucher">
                                @php 
                                    $sl = 0; 
                                    $total = 0; 
                                @endphp

                                @foreach($credit->vouchers as $voucher)
                                @php
                                    $sl++; 
                                    $total += $voucher->credit;
                                @endphp
                                <tr>
                                    <td class="expenseincometd">
                                        <select name="cmbCode[]" id="cmbCode_{{ $sl }}" class="form-control aiz-selectpicker" data-live-search="true" onchange="load_subtypeCreditv(this.value,<?php echo $sl; ?>)" required>
                                            <option value="">Select Option</option>
                                            @foreach ($acc as $acc1)
                                            <option value="{{ $acc1->head_code }}" @selected($voucher->coa_id == $acc1->head_code)>{{ $acc1->head_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    @if ($voucher->sub_type != 1)
                                        <td>
                                            <select name="subtype[]" id="subtype_{{ $sl }}" class="form-control aiz-selectpicker" data-live-search="true" required>
                                                @foreach ($voucher->subtypes as $subtype)
                                                    <option value="{{ $subtype->id }}" @selected($voucher->sub_code == $subtype->id)>{{ $subtype->name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="isSubtype[]" id="isSubtype_{{ $sl }}" value="{{ $voucher->sub_type }}" />
                                        </td>
                                    @else
                                        <td>
                                            <select name="subtype[]" id="subtype_{{ $sl }}" class="form-control aiz-selectpicker" data-live-search="true" required disabled>
                                                <option value="">Select Option</option>
                                            </select>
                                            <input type="hidden" name="isSubtype[]" id="isSubtype_{{ $sl }}" value="1" />
                                        </td>
                                    @endif
                                    <td class="reltype">
                                        <select name="reltype[]" id="reltype_{{ $sl }}" class="form-control aiz-selectpicker" data-live-search="true" onchange="load_relvalue(this.value,<?php echo $sl; ?>)">
                                            <option value="">Select Option</option>
                                            @foreach ($rel_types as $rel_type)
                                            <option value="{{ $rel_type->id }}" @selected($rel_type->id == $voucher->relational_type)>{{ $rel_type->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    @if ($voucher->relational_type != null)
                                        <td>
                                            <select name="relvalue[]" id="relvalue_{{ $sl }}" class="form-control aiz-selectpicker" data-live-search="true" required>
                                                @foreach ($voucher->relvalues as $relvalue)
                                                    <option value="{{ $relvalue->id }}" @selected($voucher->relational_id == $relvalue->id)>{{ $relvalue->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    @else
                                        <td>
                                            <select name="relvalue[]" id="relvalue_{{ $sl }}" class="form-control aiz-selectpicker" data-live-search="true" disabled>
                                                <option value="">Select Option</option>
                                            </select>
                                        </td>
                                    @endif
                                    <td>
                                        <input type="text" name="txtComment[]" value="{{ $voucher->ledger_comment }}" class="form-control" id="txtComment_{{ $sl }}">
                                    </td>
                                    <td>
                                        <input type="number" name="txtAmount[]" step=".01" value="{{ $voucher->credit }}" class="form-control total_price text-right" id="txtAmount_{{ $sl }}" onkeyup="calculationCreditv(<?php echo $sl; ?>)" required>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger red text-right" type="button" value="{{ translate('Delete') }}" onclick="deleteRowCreditv(this)"><i class="las la-trash"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>
                                        <input type="button" id="add_more" class="btn btn-info" name="add_more" onClick="addaccountCreditv('creditvoucher');" value="{{ translate('Add More') }}" />
                                    </td>
                                    <td colspan="4" class="text-right">
                                        <label for="reason" class=" col-form-label">{{ translate('Total') }}</label>
                                    </td>
                                    <td class="text-right">
                                        <input type="text" id="grandTotal" class="form-control text-right" name="grand_total" value="{{ number_format($total, 2, '.', '') }}" readonly="readonly" />
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
<script src="{{ static_asset('assets/js/accounts/credit-vouchers.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    function load_subtypeCreditv(id, sl) {
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
</script>
@endsection