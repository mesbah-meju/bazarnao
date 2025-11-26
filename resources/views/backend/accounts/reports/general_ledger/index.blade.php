@extends('backend.layouts.app')

@section('content')

<div class="card">
    <form id="sort_debit_vouchers" action="{{ route('general-ledger.report') }}" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('General Ledger') }}</h5>
            </div>
        </div>
        <?php
                $warehousearray = getWearhouseBuUserId(auth()->user()->id);
                $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
        ?>
        <div class="card-body">
            <div class="row">
                @if(Auth::user()->user_type == 'admin')   
                <div class="col-md-3">
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
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <div class="form-group row">
                        <label for="cmbCode" class="form-label">{{ translate('Transaction Head') }} <span class="text-danger">*</span></label>
                        <select name="cmbCode" class="form-control aiz-selectpicker" data-live-search="true" id="cmbCode" required>
                            <option value="">{{ translate('Select Option') }}</option>
                            @foreach($general_ledger as $ledger)
                            <option value="{{ $ledger->head_code }}">{{ $ledger->head_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label" for="dtpFromDate">{{ translate('From Date') }}</label>
                        <input type="text" name="dtpFromDate" class="form-control datepicker" value="{{ date('m/1/Y') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label" for="dtpToDate">{{ translate('To Date') }}</label>
                        <input type="text" name="dtpToDate" class="form-control datepicker" value="{{ date('m/d/Y') }}">
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
            <table border="0" width="100%">
                <caption class="text-center">
                    <table class="print-font-size" width="100%">
                        <tr>
                            <td align="left" style="border-bottom: 2px #333 solid;" width="33.333%">
                                <img src="{{ asset('public/assets/img/logo.png') }}" class="img-bottom-m print-logo" alt="logo"><br>
                            </td>
                            <td align="center" style="border-bottom: 2px #333 solid;" width="33.333%">
                                <h4><strong>BAZAR NAO LTD.</strong></h4>
                                Sukhnir, Flat: B2, House: 33, Road: 1/A<br>Block: J, Baridhara, Dhaka-1212<br>
                                info@bazarnao.com<br>
                                +880 1969 906 699<br>
                            </td>
                            <td align="right" style="border-bottom: 2px #333 solid;" width="33.333%">
                                <b>
                                    <label class="font-weight-600 mb-0">{{ translate('date') }}</label> : {{ date('d/m/Y') }}
                                </b>
                                <br>
                                <b>
                                    <label class="font-weight-600 mb-0">{{ translate('Opening Balance') }}</label> : 0.00
                                </b>
                                <br>
                                <b>
                                    <label class="font-weight-600 mb-0">{{ translate('Closing Balance') }}</label> : 0.00
                                </b>
                            </td>
                        </tr>
                    </table>
                </caption>
                <caption class="text-center">
                    <strong><u class="pt-4">{{ translate('General Ledger') }}</u></strong>
                </caption>
            </table>
        </div>
        <div class="table-responsive">
            <table class="datatable table table-striped table-hover" cellpadding="6" cellspacing="1">
                <thead>
                    <tr>
                        <th>{{ translate('SL') }}</th>
                        <th>{{ translate('Date') }}</th>
                        <th>{{ translate('Account Head') }}</th>
                        <th>{{ translate('Party Name') }}</th>
                        <th>{{ translate('Particulars') }}</th>
                        <th>{{ translate('Voucher Name') }}</th>
                        <th>{{ translate('Voucher No') }}</th>
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
