@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{ translate('Loan Information') }}</h5>
</div>

<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Add Loan') }}</h5>
            </div>
            <div class="card-body p-0">
                <form class="p-4" action="{{ route('loans.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="bank_id" class="form-label">Bank Name</label>
                        <select name="bank_id" id="bank_id" class="form-control aiz-selectpicker" data-live-search="true" required>
                            <option value="">Select Bank</option>
                            @foreach ($banks as $key => $bank)
                            <option value="{{ $bank->id }}">{{ $bank->bank_name }}({{ $bank->ac_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="loan_amount" class="form-label">Loan Amount (৳)</label>
                        <input type="number" name="loan_amount" id="loan_amount" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="loan_term" class="form-label">Loan Term (Years)</label>
                        <input type="number" name="loan_term" id="loan_term" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="interest_rate" class="form-label">Interest Rate (%)</label>
                        <input type="number" step="0.01" name="interest_rate" id="interest_rate" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="text" name="start_date" id="start_date" class="form-control datepicker" required>
                    </div>

                    <div class="form-group mb-3 text-right">
                        <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection