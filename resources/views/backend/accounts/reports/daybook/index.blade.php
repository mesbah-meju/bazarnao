@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Day Book') }}</h1>
        </div>
    </div>
</div>

<?php
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
?>

<div class="card">
    <form id="sort_daybook" action="{{ route('day-book-report.index') }}" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Day Book') }}</h5>
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
                        <label for="head_code" class="form-label">{{ translate('Account Head') }}</label>
                        <select name="head_code" id="head_code" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">Select Account Head</option>
                            @foreach($coas as $coa)
                            <option value="{{ $coa->head_code }}" @isset($head_code) @if($coa->head_code==$head_code ) selected @endif @endisset>{{ $coa->head_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="party_id" class="form-label">{{ translate('Party Name') }}</label>
                        <select name="party_id" id="party_id" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">Select Party Name</option>
                            @foreach($parties as $party)
                            <option value="{{ $party->id }}" @isset($party_id) @if($party->id==$party_id ) selected @endif @endisset>
                                {{ $party->name }} @if($party->sub_type_id != null) ({{ $party->subtype->name }}) @endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="dtpFromDate" class="form-label">{{ translate('From Date') }}</label>
                        <input type="text" name="dtpFromDate" class="form-control datepicker" value="{{ date('m/d/Y') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="dtpToDate" class="form-label">{{ translate('To Date') }}</label>
                        <input type="text" name="dtpToDate" class="form-control datepicker" value="{{ date('m/d/Y') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="button" class="form-label text-white">{{ translate('To Date') }}</label>
                        <div class="text-left">
                            <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <div class="card-body printArea">
        <div class="row pb-3 voucher-center">
            <div class="col-md-3">
                <img src="{{ asset('public/assets/img/logo.png') }}" alt="Logo" height="40px"><br><br>
            </div>
            <div class="col-md-6 text-center">
                <h2>Bazarnao</h2>
                <strong><u class="pt-4">{{ translate('Day Book Voucher') }}</u></strong>
            </div>
            <div class="col-md-3">
                <div class="pull-right" style="margin-right:20px;">
                    <b>
                        <label class="font-weight-600 mb-0">{{ translate('date') }}</label> : {{ date('d/m/Y') }}
                    </b>
                    <br>
                </div>
            </div>
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
                        <th>{{ translate('Voucher') }}</th>
                        <th>{{ translate('Debit') }}</th>
                        <th>{{ translate('Credit') }}</th>
                        <th>{{ translate('Rev. Head') }}</th>
                        <th>{{ translate('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
