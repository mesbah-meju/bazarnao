@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Cash Adjustment')}}</h5>
            </div>

            <form class="form-horizontal" action="{{ route('debit-vouchers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label for="vo_no" class="col-sm-2 col-form-label"><?php echo translate('Voucher No') ?></label>
                        <div class="col-sm-4">
                            <input type="text" name="txtVNo" id="txtVNo" value="<?php echo $voucher_no; ?>" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="date" class="col-sm-2 col-form-label"><?php echo translate('Date') ?></label>
                        <div class="col-sm-4">
                            <input type="text" name="dtpDate" id="dtpDate" class="form-control datepicker" value="<?php echo date('m/d/Y'); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="type" class="col-sm-2 col-form-label"><?php echo translate('Adjustment Type') ?> *</label>
                        <div class="col-sm-4">
                            <select name="type" class="form-control">
                                <option value=""><?php echo translate('Adjustment Type') ?></option>
                                <option value="1"><?php echo translate('Debit') ?></option>
                                <option value="2"><?php echo translate('Credit') ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="txtRemarks" class="col-sm-2 col-form-label"><?php echo translate('Remark') ?></label>
                        <div class="col-sm-4">
                            <textarea name="txtRemarks" id="txtRemarks" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="debtAccVoucher">
                            <thead>
                                <tr>
                                    <th class="text-center"><?php echo translate('Code') ?> *</th>
                                    <th class="text-center"><?php echo translate('Amount') ?> *</th>
                                </tr>
                            </thead>
                            <tbody id="debitvoucher">
                                <tr>
                                    <td><input type="text" name="txtCode" value="1020101" class="form-control " id="txtCode" readonly=""></td>
                                    <td><input type="number" name="txtAmount" value="" class="form-control total_price text-right" id="txtAmount_1" required></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" name="finyear" value="{{ get_financial_year() }}">
                    <div class="form-group form-group-margin row">
                        <div class="col-sm-12 text-right">
                            <input type="submit" id="add_receive" class="btn btn-success btn-large" name="save" value="<?php echo translate('Save') ?>" tabindex="9" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection