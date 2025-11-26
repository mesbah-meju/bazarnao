@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Add Journal Voucher')}}</h5>
            </div>

            <?php
                $warehousearray = getWearhouseBuUserId(auth()->user()->id);
                $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
            ?>

            <form class="form-horizontal" action="{{ route('journal-vouchers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label for="txtVNo" class="col-sm-2 col-form-label">{{ translate('Voucher Type') }} <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <input type="text" name="txtVNo" id="txtVNo" value="Journal" class="form-control" required readonly/>
                        </div>
                    </div>
                    @if(Auth::user()->user_type == 'admin')
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">{{translate('Warehouse')}} <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <select id="warehouse" class="form-control aiz-selectpicker" name="warehouse" data-live-search="true" required>
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
                            <select id="warehouse" class="form-control aiz-selectpicker" name="warehouse" data-live-search="true" required>
                                <option value="">Select a warehouse</option>
                                @foreach ($warehouses as $key => $warehouse)
                                <option  value="{{ $warehouse->id }}">{{ $warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif

                    <div class="form-group row">
                        <label for="dtpDate" class="col-sm-2 col-form-label">{{ translate('Date') }} <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <input type="text" name="dtpDate" id="dtpDate" class="form-control datepicker" value="<?php echo  date('m/d/Y')?>" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="txtRemarks" class="col-sm-2 col-form-label">{{ translate('Remark') }}</label>
                        <div class="col-sm-4">
                            <textarea name="txtRemarks" id="txtRemarks" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="">
                        <table class="table table-bordered table-hover" id="journalAccVoucher">
                            <thead>
                                <tr>
                                    <th width="12%" class="text-center">{{ translate('Account Name') }} <span class="text-danger">*</span></th>
                                    <th width="12%" class="text-center">{{ translate('Sub Type') }} <span class="text-danger">*</span></th>
                                    <th width="12%" class="text-center">{{ translate('Relational Type') }}</th>
                                    <th width="12%" class="text-center">{{ translate('Relational Value') }}</th>
                                    <th width="15%" class="text-center">{{ translate('Ledger Comment') }}</th>
                                    <th width="10%" class="text-center">{{ translate('Debit') }}</th>
                                    <th width="10%" class="text-center">{{ translate('Credit') }}</th>
                                    <th width="12%" class="text-center">{{ translate('Reverse Account Head') }} <span class="text-danger">*</span></th>
                                    <th width="5%" class="text-center">{{ translate('Action') }}</th>
                                </tr>
                            </thead>

                            <tbody id="journalvoucher">
                                <tr>
                                    <td class="expenseincometd">
                                        <select name="cmbCode[]" id="cmbCode_1" required class="form-control aiz-selectpicker" data-live-search="true" onchange="load_subtypeJournalv(this.value,1)">
                                            <option value="">Select Option</option>
                                            @foreach ($acc as $acc1)
                                            <option value="{{ $acc1->head_code }}">{{ $acc1->head_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="subtype[]" id="subtype_1" class="form-control aiz-selectpicker" data-live-search="true" required>
                                            <option value="">Select Option</option>
                                        </select>
                                    </td>
                                    <td class="reltype">
                                        <select name="reltype[]" id="reltype_1" class="form-control aiz-selectpicker" data-live-search="true" onchange="load_relvalue(this.value,1)">
                                            <option value="">Select Option</option>
                                            @foreach ($rel_types as $rel_type)
                                            <option value="{{ $rel_type->id }}">{{ $rel_type->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="relvalue[]" id="relvalue_1" class="form-control aiz-selectpicker" data-live-search="true" disabled>
                                            <option value="">Select Option</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="hidden" name="isSubtype[]" id="isSubtype_1" value="1" />
                                        <input type="text" name="txtComment[]" value="" class="form-control" id="txtComment_1">
                                    </td>
                                    <td>
                                        <input type="number" name="txtAmount[]" value="" step=".01" placeholder="0.00" class="form-control total_price text-right" id="txtAmount_1" onkeyup="calculationJournalv(1)">
                                    </td>
                                    <td>
                                        <input type="number" name="txtAmountcr[]" value="" step=".01" placeholder="0.00" class="form-control total_price1 text-right" id="txtAmount1_1" onkeyup="calculationJournalv(1)">
                                    </td>
                                    <td>
                                        <select name="cmbDebit[]" id="cmbDebit_1" class="form-control aiz-selectpicker" data-live-search="true" required>
                                            <option value="">Please Option</option>
                                            @foreach ($acc as $cracc)
                                            <option value="{{ $cracc->head_code }}">{{ $cracc->head_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger red" type="button" value="{{ translate('Delete') }}" onclick="deleteRowJournalv(this)"><i class="las la-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>
                                        <input type="button" id="add_more" class="btn btn-info" name="add_more" onClick="addaccountJournalv('journalvoucher');" value="{{ translate('Add More') }}" />
                                    </td>
                                    <td colspan="4" class="text-right">
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
<script src="{{ static_asset('assets/js/accounts/journal-vouchers.js') }}" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Add Swal CDN -->

<script type="text/javascript">
    function load_subtypeJournalv(id, sl) {
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

    // New Save button override with Swal
    $(document).ready(function() {
        $('#add_receive').click(function(e) {
            e.preventDefault(); // Stop normal form submit

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to save this journal voucher!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).closest('form').submit(); // Submit the form if confirmed
                }
            });
        });
    });
</script>
@endsection
