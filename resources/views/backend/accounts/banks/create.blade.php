@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{ translate('Bank Information') }}</h5>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Add Bank') }}</h5>
            </div>
            <div class="card-body p-0">
                <form class="p-4" action="{{ route('banks.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="bank_name" class="form-label">{{ translate('Bank Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="bank_name" id="bank_name" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="ac_name" class="form-label">{{ translate('A/C Name') }}</label>
                        <input type="text" name="ac_name" id="ac_name" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="ac_number" class="form-label">{{ translate('A/C Number') }}</label>
                        <input type="text" name="ac_number" id="ac_number" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="branch" class="form-label">{{ translate('Branch') }}</label>
                        <input type="text" name="branch" id="branch" class="form-control">
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

