@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
    	<div class="row align-items-center">
    		<div class="col-md-12">
                <div class="card-header d-flex justify-content-between">
    				<div class="mb-0 h6">{{ translate('All Banks') }}</div>
    				<a class="btn btm-sm btn-primary p-1" href="{{ route('bank.create') }}" role="button">Add New Bank</a>

    			</div>
    		</div>
    	</div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2>{{ __('Banks') }}</h2>
                    </div>
                    <div class="card-body">
                        @if (count($banks) > 0)
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('SL') }}</th>
                                        <th>{{ __('Bank Name') }}</th>
                                        <th>{{ __('Bank Code') }}</th>
                                        <th>{{ __('Account No') }}</th>
                                        <th>{{ __('Account Name') }}</th>
                                        <th>{{ __('Branch') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i=1 @endphp
                                    @foreach ($banks as $bank)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $bank->bank_name }}</td>
                                            <td>{{ $bank->bank_code }}</td>
                                            <td>{{ $bank->account_no }}</td>
                                            <td>{{ $bank->account_name }}</td>
                                            <td>{{ $bank->branch }}</td>
                                            <td class="text-right">
                                                {{-- <a class="btn btn-soft-info btn-icon btn-circle btn-sm" href="{{route('bank.show', encrypt($bank->id))}}" title="{{ translate('Show') }}">
                                                    <i class="las la-language"></i>
                                                </a> --}}
                                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('bank.edit', $bank->id) }}" title="{{ __('Edit') }}">
                                                    <i class="las la-edit"></i>
                                                </a>
                                                <a class="btn btn-soft-danger btn-icon btn-circle btn-sm" href="{{ route('bank.destroy', $bank->id) }}" title="{{ __('Delete') }}">
                                                    <i class="las la-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                           
                        @else
                            <p>{{ __('No banks found.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    

@endsection



