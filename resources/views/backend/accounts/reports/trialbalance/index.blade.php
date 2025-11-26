@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Trial Balance') }}</h1>
        </div>
    </div>
</div>
<?php
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
?>
<div class="card">
    <form id="sort_debit_vouchers" action="{{ route('trial-balance-report.index') }}" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Trial Balance') }}</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                @if(Auth::user()->user_type == 'admin')   
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label" for="warehouse_id">{{ translate('Warehouse') }} <span class="text-danger">*</span></label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">Select Warehouse</option>
                            @foreach (\App\Models\Warehouse::all() as $key => $warehouse)
                            <option  value="{{ $warehouse->id }}" {{ (isset($warehouse_id) && $warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name}}</option>
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
                            <option  value="{{ $warehouse->id }}" {{ (isset($warehouse_id) && $warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label" for="dtpFromDate">{{ translate('From Date') }}</label>
                        <input type="text" name="dtpFromDate" class="form-control datepicker" value="{{ date('m/1/Y') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="dtpToDate" class="form-label">{{ translate('To Date') }}</label>
                        <input type="text" name="dtpToDate" class="form-control datepicker" value="{{ date('m/t/Y') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="dtpToDate" class="form-label text-white">{{ translate('To Date') }}</label><br>
                        <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                        <button class="btn btn-md btn-info mr-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <div class="card-body printArea">
        <div class="table-responsive">
            <table width="100%">
                <caption class="text-center">
                    <table class="print-font-size" width="100%">
                        <tr>
                            <td align="left" width="33.333%">
                                <img src="{{ asset('public/assets/img/logo.png') }}" class="img-bottom-m print-logo" alt="logo"><br><br>
                            </td>
                            <td align="center" style="border-bottom: 2px #333 solid;" width="33.333%">
                                <strong>Bazar Nao Limited</strong><br>
                                4th Floor, AGM Chandrima, House 12, Road 08, Block J, Baridhara, Dhaka-1212.
                                <br>
                                info@bazarnao.com
                                <br>
                                +880 1969 906 699
                                <br>
                            </td>
                            <td align="right" width="33.333%">
                                <date> {{ translate('Date') }}: {{ date('d-M-Y') }} </date><br>
                            </td>
                        </tr>
                    </table>
                </caption>
                <caption class="text-center">
                    <strong><?php echo translate('Trial Balance')?></strong>
                </caption>
            </table>
            <table class="datatable table table-striped table-hover" cellpadding="6" cellspacing="1">
                <thead>
                    <tr>
                        <th>{{ translate('SL') }}</th>
                        <th>{{ translate('Code') }}</th>
                        <th>{{ translate('Head Name') }}</th>
                        <th>{{ translate('Debit') }}</th>
                        <th>{{ translate('Credit') }}</th>
                    </tr>
                </thead>
               
            </table>
        </div>
    </div>
</div>

@endsection
