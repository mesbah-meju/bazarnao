@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Bank Reconciliation') }}</h1>
        </div>
    </div>
</div>
            <?php
                $warehousearray = getWearhouseBuUserId(auth()->user()->id);
                $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
            ?>

<div class="card">
    <form id="sort_bank_reconciliation" action="{{ route('bank-reconciliation.index') }}" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Bank Reconciliation') }}</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                @if(Auth::user()->user_type == 'admin')   
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label" for="warehouse_id">{{ translate('Warehouse') }} <span class="text-danger">*</span></label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="" selected>Select Warehouse</option>
                            @foreach (\App\Models\Warehouse::all() as $key => $warehouse)
                            <option value="{{ $warehouse->id }}" {{ (isset($warehouse_id) && $warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @else
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label" for="warehouse_id">{{ translate('Warehouse') }} <span class="text-danger">*</span></label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="" selected>Select Warehouse</option>
                            @foreach ($warehouses as $key => $warehouse)
                            <option value="{{ $warehouse->id }}" {{ (isset($warehouse_id) && $warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="bankCode" class="form-label">{{ translate('Bank Name') }}</label>
                        <select name="bankCode" id="bankCode" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">Select Bank</option>
                            @foreach($banks as $bank)
                            <option value="{{ $bank->head_code }}" {{ (isset($bankCode) && $bankCode == $bank->head_code) ? 'selected' : '' }}>
                                {{ $bank->head_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="dtpFromDate" class="form-label">{{ translate('From Date') }}</label>
                        <input type="text" name="dtpFromDate" id="dtpFromDate" class="datepicker form-control" value="{{ date('m/d/Y', strtotime('first day of this month')) }}" placeholder="{{ translate('From Date') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="dtpToDate" class="form-label">{{ translate('To Date') }}</label>
                        <input type="text" name="dtpToDate" id="dtpToDate" class="datepicker form-control" value="{{ date('m/d/Y', strtotime('last day of this month')) }}" placeholder="{{ translate('To Date') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="text-left">
                            <button type="submit" class="btn btn-success">{{ translate('Filter') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body printArea">
        <div class="table-responsive">
            <?php if ($vouchers) { ?>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th><?php echo translate('SL No') ?></th>
                            <th><?php echo translate('Voucher No') ?></th>
                            <th><?php echo translate('Voucher Type') ?></th>
                            <th><?php echo translate('Particulars') ?></th>
                            <th><?php echo translate('Chaque No') ?></th>
                            <th><?php echo translate('Chaque Date') ?></th>
                            <th><?php echo translate('Remark') ?></th>
                            <th><?php echo translate('Amount') ?></th>
                            <th width="180px" style="text-align: right;"><?php echo translate('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sl = 1;
                        $sum = 0;
                        foreach ($vouchers as $appr) {
                            $sum += $appr->debit;
                                if ($appr->is_honour == 1) { ?>
                                    <tr id="t-<?php echo $sl; ?>" style="background-color:#151B8D !important; color: #fff !important;"> 
                                <?php } else { ?>
                                    <tr id="t-<?php echo $sl; ?>"> 
                                <?php } ?>
                                    <td><input type="checkbox" class="approvalCheckbox" data-property="t-<?php echo $sl; ?>" value="<?php echo $appr->voucher_no; ?>" name="vapprove[]" <?php if ($appr->is_honour == 1) { ?> checked="checked" <?php } ?> /> &nbsp; <?php echo $sl; ?></td>
                                    <td><?php echo $appr->voucher_no; ?></td>
                                    <td><?php echo $appr->voucher_type; ?></td>
                                    <td><?php echo $appr->account_name; ?></td>
                                    <td><?php echo $appr->cheque_no; ?></td>
                                    <td><?php echo $appr->cheque_date; ?></td>
                                    <td><?php echo $appr->narration; ?></td>
                                    <td><?php echo number_format($appr->debit, 2); ?></td>
                                    <td style="text-align: right;">
                                        @if($appr->is_honour == 0)
                                        <a class="btn btn-success btn-sm" href="{{ route('bank-reconciliation.approve', $appr->voucher_no) }}" title="{{ translate('Reconciliation') }}">{{ translate('Reconciliation') }}</a>
                                        @else
                                        <a class="btn btn-danger btn-sm" href="{{ route('bank-reconciliation.disapprove', $appr->voucher_no) }}" title="{{ translate('Un-Reconciliation') }}">{{ translate('Un-Reconciliation') }}</a>
                                        @endif
                                    </td>
                                </tr>
                            <?php $sl++;
                        } ?>
                            <tr>
                                <td style="border-bottom: 1px solid #ddd;" colspan="7" align="right"> <strong><?php echo translate('total'); ?> </strong></td>
                                <td style="border-bottom: 1px solid #ddd;"><strong><?php echo number_format($sum, 2); ?></strong></td>
                                <td style="border-bottom: 1px solid #ddd;"><strong></td>
                            </tr>
                    </tbody>
                </table>
            <?php } else { ?>
                <h5> <?php echo translate('No Result Found'); ?> </h5>
            <?php } ?>
        </div>
    </div>
</div>

@endsection