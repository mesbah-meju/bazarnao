@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
    	<div class="row align-items-center">
    		<div class="col-md-12">
                <div class="card-header d-flex justify-content-between">
    				<div class="mb-0 h6">{{ translate('Edit Bank') }}</div>
    				<a class="btn btm-sm btn-primary p-1" href="{{ route('bank.index') }}" role="button">Bank List</a>

    			</div>
    		</div>
    	</div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h2>{{ __('Edit Bank') }}</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('bank.update', $banks->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="bank_name">{{ __('Bank Name') }}</label>
                                <input type="text" name="bank_name" id="bank_name" value="{{$banks->bank_name}}" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="bank_code">{{ __('Bank Code') }}</label>
                                <input type="text" name="bank_code" id="bank_code"  value="{{$banks->bank_code}}"  class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="account_no">{{ __('Account No') }}</label>
                                <input type="text" name="account_no" id="account_no" value="{{$banks->account_no}}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="account_name">{{ __('Account Name') }}</label>
                                <input type="text" name="account_name" id="account_name" value="{{$banks->account_name}}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="branch">{{ __('Branch') }}</label>
                                <input type="text" name="branch" id="branch" value="{{$banks->branch}}" class="form-control">
                            </div>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                                <a class="btn btn-dark mx-2 btn-sm" href="{{ route('bank.index') }}">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
