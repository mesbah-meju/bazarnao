@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Sub Ledger') }}</h1>
        </div>
    </div>
</div>

<?php
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
?>

<div class="card">
    <form id="sort_debit_vouchers" action="{{ route('sub-ledger.report') }}" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Sub Ledger') }}</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                @if(Auth::user()->user_type == 'admin')   
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="warehouse_id">{{ translate('Warehouse') }} <span class="text-danger">*</span></label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">Select Warehouse</option>
                            @foreach (\App\Models\Warehouse::all() as $key => $warehouse)
                            <option  value="{{ $warehouse->id }}">{{ $warehouse->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @else
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="warehouse_id">{{ translate('Warehouse') }} <span class="text-danger">*</span></label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">Select Warehouse</option>
                            @foreach ($warehouses as $key => $warehouse)
                            <option  value="{{ $warehouse->id }}">{{ $warehouse->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="dtpFromDate">{{ translate('From Date') }}</label>
                        <input type="text" name="dtpFromDate" class="form-control datepicker" value="{{ date('m/1/Y') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="dtpToDate">{{ translate('To Date') }}</label>
                        <input type="text" name="dtpToDate" class="form-control datepicker" value="{{ date('m/d/Y') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label"  for="subtype">{{ translate('Sub Type') }}</label>
                        <select name="subtype" id="subtype" class="form-control aiz-selectpicker" onchange="showAccountSubhead(this.value);" required>
                            <option value="">{{ translate('Select Option') }}</option>
                            @foreach($subtypes as $subtype)
                            <option value="{{ $subtype->id }}">{{ $subtype->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="accounthead">{{ translate('Account Head') }}</label>
                        <select name="accounthead[]" id="accounthead" class="form-control aiz-selectpicker" multiple data-live-search="true" onchange="showTransationSubheadFromHeads();">
                            <option value="">{{ translate('Select Option') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="subcode">{{ translate('Transaction Head') }}</label>
                        <select name="subcode" id="subcode" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">{{ translate('Select Option') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="text-left">
                            <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body printArea">
        <div class="row pb-3 align-items-center">
            <div class="col-md-3">
                <img src="{{ asset('public/assets/img/logo.png') }}" alt="Logo" height="40px"><br><br>
            </div>
            <div class="col-md-6 text-center">
                <h4><strong class="">Bazar Nao Limited</strong><br></h4>
                4th Floor, AGM Chandrima, House 12, Road 08, Block J, Baridhara, Dhaka-1212.<br>
                info@bazarnao.com<br>
                +880 1969 906 699<br>
            </div>
            <div class="col-md-3 text-right">
                <div class="pull-right">
                    <b>
                        <label class="font-weight-600 mb-0">{{ translate('date') }}</label> : {{ date('d/m/Y') }}
                    </b>
                    <br>
                    <b>
                        <label class="font-weight-600 mb-0">{{ translate('Opening Balance') }}</label> : {{ number_format(0,2,'.',',') }}
                    </b>
                    <br>
                    <b>
                        <label class="font-weight-600 mb-0">{{ translate('Closing Balance') }}</label> : {{ number_format(0,2,'.',',') }}
                    </b>
                </div>
            </div>
        </div>

        <div class="row pb-3 voucher-center align-items-center">
            <div class="col-md-12 text-center">
                <strong><u class="pt-4">{{ translate('Sub Ledger Report') }}</u></strong>
            </div>
        </div>

        <div class="table-responsive">
            <table class="datatable table table-striped table-hover" cellpadding="6" cellspacing="1">
                <thead>
                    <tr>
                        <th>{{ translate('SL') }}</th>
                        <th>{{ translate('Date') }}</th>
                        <th>{{ translate('Voucher No') }}</th>
                        <th>{{ translate('Voucher Type') }}</th>
                        <th>{{ translate('Head Name') }}</th>
                        <th>{{ translate('Ledger Comment') }}</th>
                        <th>{{ translate('Debit') }}</th>
                        <th>{{ translate('Credit') }}</th>
                        <th>{{ translate('Balance') }}</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="text-right"><strong>{{ translate('Total') }}</strong></td>
                        <td class="text-right"><strong>{{ number_format(0, 2, '.', ',') }}</strong></td>
                        <td class="text-right"><strong>{{ number_format(0, 2, '.', ',') }}</strong></td>
                        <td class="text-right"><strong>{{ number_format(0, 2, '.', ',') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection

@section('script')

<script type="text/javascript">
    function makeAccountHeadReadonly() {
        var $form = $('#sort_debit_vouchers');
        // Remove any prior hidden mirrors
        $form.find('input.accounthead-hidden').remove();
        // Mirror selected values to hidden inputs so they submit even if the select is disabled
        var selectedHeads = $('#accounthead').val() || [];
        selectedHeads.forEach(function(v){
            $('<input/>', { type: 'hidden', name: 'accounthead[]', value: v, class: 'accounthead-hidden' }).appendTo($form);
        });
        // Disable UI interaction and refresh picker
        $('#accounthead').prop('disabled', true).selectpicker('refresh');
        // Also make the bootstrap-select UI non-interactive as a safeguard
        $('#accounthead').closest('.bootstrap-select').css('pointer-events', 'none').find('button').addClass('disabled');
    }
    function showTransationSubheadFromHeads() {
        $('#subcode').html('');
        var selectedHeads = $('#accounthead').val() || [];
        var codes = selectedHeads.join(',');
        if (!codes) { return; }
        $.ajax({
            url: "{{ route('sub-ledger.subcode-by-head', '') }}/" + codes,
            type: "GET",
            dataType: "json",
            success: function(data) {
                if (data != '') {
                    $('#subcode').html(data).selectpicker('refresh');
                }
            },
            error: function() {
                alert('Error fetching transaction heads');
            }
        });
    }

    function showAccountSubhead(id) {
        $('#accounthead').html('');
        $.ajax({
            url: "{{ route('sub-ledger.accounthead', '') }}/" + id,
            type: "GET",
            dataType: "json",
            success: function(data) {
                if (data != '') {
                    $('#accounthead').html(data);
                    $('#accounthead option').prop('selected', true);
                    $('#accounthead').selectpicker('refresh');
                    showTransationSubheadFromHeads();
                    makeAccountHeadReadonly();
                }
            },
            error: function() {
                alert('Error fetching account heads');
            }
        });
    }

    // If the page loads with account heads already present/selected, make it readonly
    $(document).ready(function(){
        if ($('#accounthead option').length > 0) {
            // If nothing is selected for some reason, select all to reflect default behavior
            if (!(($('#accounthead').val() || []).length)) {
                $('#accounthead option').prop('selected', true);
                $('#accounthead').selectpicker('refresh');
            }
            makeAccountHeadReadonly();
        }
    });
</script>

@endsection