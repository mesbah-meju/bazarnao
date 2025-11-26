@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Add Opening Balance')}}</h5>
            </div>
            <?php
                $warehousearray = getWearhouseBuUserId(auth()->user()->id);
                $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
            ?>

            <form class="form-horizontal" action="{{ route('opening-balances.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    @if(Auth::user()->user_type == 'admin')
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="warehouse_id">{{translate('Warehouse')}} <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <select name="warehouse_id" id="warehouse_id" class="form-control aiz-selectpicker" data-live-search="true" required>
                                <option value="">Select a warehouse</option>
                                @foreach (\App\Models\Warehouse::all() as $key => $warehouse)
                                <option  value="{{ $warehouse->id }}">{{ $warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @else
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="warehouse_id">{{translate('Warehouse')}} <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <select name="warehouse_id" id="warehouse_id" class="form-control aiz-selectpicker" data-live-search="true" required>
                                <option value="">Select a warehouse</option>
                                @foreach ($warehouses as $key => $warehouse)
                                <option  value="{{ $warehouse->id }}">{{ $warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="ac">{{translate('Financial Year')}} <span class="text-danger">*</span></label>
                        <div class="col-md-4">
                            <select name="fyear" id="fyear" class="form-control aiz-selectpicker">
                                @foreach ($oldyears as $oldyear)
                                <option value="{{ $oldyear->id }}">{{ $oldyear->year_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="date" class="col-sm-2 col-form-label">{{translate('Opening Date')}} <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <input type="text" name="dtpDate" id="dtpDate" class="form-control datepicker" value="<?php echo date("m/d/Y", strtotime('first day of January ' . date('Y'))); ?>">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="debtAccVoucher">
                            <thead>
                                <tr>
                                    <th width="25%" class="text-center">{{translate('Account Name')}}<span class="text-danger">*</span></th>
                                    <th width="25%" class="text-center">{{translate('Sub Type')}}<span class="text-danger">*</span></th>
                                    <th width="20%" class="text-center">{{translate('Debit')}}</th>
                                    <th width="20%" class="text-center">{{translate('Credit')}}</th>
                                    <th width="10%" class="text-center">{{translate('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody id="debitvoucher">
                                <tr>
                                    <td>
                                        <select name="cmbCode[]" required id="cmbCode_1" class="form-control" onchange="load_subtypeOpen(this.value,1)">
                                            <option value="">Select Option</option>
                                            @foreach ($acc as $acc1)
                                            <option value="{{ $acc1->head_code}}">{{ $acc1->head_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="subtype[]" id="subtype_1" required class="form-control">
                                            <option value="">Select Option</option>
                                        </select>
                                    </td>
                    <td>
                        <input type="number" name="txtDebit[]" value="" class="form-control total_dprice text-right" id="txtDebit_1" onkeyup="calculationDebtOpen(1)">
                    </td>
                    <td>
                        <input type="number" name="txtCredit[]" value="" class="form-control total_cprice text-right" id="txtCredit_1" onkeyup="calculationCreditOpen(1)">
                        <input type="hidden" name="isSubtype[]" id="isSubtype_1" value="1" />
                    </td>
                                    <td>
                                        <button class="btn btn-danger red text-right" type="button" value="{{ translate('Delete')}}" onclick="deleteRowDebtOpen(this)"><i class="las la-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>
                                        <input type="button" id="add_more" class="btn btn-info" name="add_more" onClick="addaccountOpen('debitvoucher');" value="{{ translate('Add More') }}" />
                                    </td>
                                    <td colspan="1" class="text-right"><label for="reason" class="  col-form-label">{{translate('Total')}}</label>
                                    </td>

                                    <td class="text-right">
                                        <input type="text" id="grandTotald" class="form-control text-right " name="grand_totald" value="" readonly="readonly" />
                                    </td>
                                    <td class="text-right">
                                        <input type="text" id="grandTotalc" class="form-control text-right " name="grand_totalc" value="" readonly="readonly" />
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="form-group form-group-margin row">
                        <div class="col-sm-12 text-right">
                            <input type="submit" id="add_receive" class="btn btn-success btn-large" name="save" value="{{ translate('Save') }}" tabindex="9" />
                            <input type="hidden" name="" id="base_url" value="{{ url('/') }}">
                            <input type="hidden" name="" id="headoption" value="<option value=''>Select Option</option>@foreach ($acc as $acc2)<option value='{{ $acc2->head_code }}'>{{ $acc2->head_name }} </option>@endforeach">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ static_asset('assets/js/accounts/opening-balances.js') }}"></script>
<script type="text/javascript">
    function load_subtypeOpen(id, sl) {
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
                setReadonlyByHead(id, sl);
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

    function setReadonlyByHead(headCode, sl) {
        $.ajax({
            url: "{{ route('account.headDetails') }}",
            type: "GET",
            dataType: "json",
            data: { headCode: headCode },
            success: function(resp) {
                var details = resp && resp.headDetails ? resp.headDetails : null;
                var debitEl = $('#txtDebit_' + sl);
                var creditEl = $('#txtCredit_' + sl);
                if (!details) {
                    debitEl.prop('readonly', false).val('');
                    creditEl.prop('readonly', false).val('');
                    return;
                }

                var headType = details.head_type;
                if (headType === 'A' || headType === 'E') {
                    debitEl.prop('readonly', false).val('');
                    creditEl.prop('readonly', true).val('0');
                } else if (headType === 'L' || headType === 'I') {
                    creditEl.prop('readonly', false).val('');
                    debitEl.prop('readonly', true).val('0');
                } else {
                    debitEl.prop('readonly', false).val('');
                    creditEl.prop('readonly', false).val('');
                }
            },
            error: function() {
                var debitEl = $('#txtDebit_' + sl);
                var creditEl = $('#txtCredit_' + sl);
                debitEl.prop('readonly', false).val('');
                creditEl.prop('readonly', false).val('');
            }
        });
    }
</script>
@endsection