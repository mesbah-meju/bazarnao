@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
    	<div class="row align-items-center">
    		<div class="col-md-12">
                <div class="card-header d-flex justify-content-between">
    				<div class="mb-0 h6">{{ translate('All Fire Service Contact List') }}</div>
    				<a class="btn btm-sm btn-primary p-1" href="{{ route('fire_service.create') }}" role="button">Add New Fire Service Contact</a>

    			</div>
    		</div>
    	</div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2>{{ __('Fire Service') }}</h2>
                    </div>
                    <div class="card-body">
                        @if (count($fireServices) > 0)
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('SL') }}</th>
                                        <th>{{ __('Service Name') }}</th>
                                        <th>{{ __('Area Code') }}</th>
                                        <th>{{ __('Provider Name') }}</th>
                                        <th>{{ __('Contact Phone') }}</th>
                                        <th>{{ __('Contact Email') }}</th>
                                        <th>{{ __('Duration Hours') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i=1 @endphp
                                    @foreach ($fireServices as $fireService)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $fireService->service_name }}</td>
                                            <td>{{ $fireService->area_code }}</td>
                                            <td>{{ $fireService->provider_name }}</td>
                                            <td>{{ $fireService->contact_phone }}</td>
                                            <td>{{ $fireService->contact_email }}</td>
                                            <td>{{ $fireService->duration_hours }}</td>
                                            <td>{{ $fireService->description }}</td>
                                            <td class="text-center d-flex">
                                               
                                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('fire_service.edit', $fireService->id) }}" title="{{ __('Edit') }}">
                                                    <i class="las la-edit"></i>
                                                </a>
                                                <a class="btn btn-soft-danger btn-icon btn-circle btn-sm" href="{{ route('fire_service.destroy', $fireService->id) }}" title="{{ __('Delete') }}">
                                                    <i class="las la-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                           
                        @else
                            <p>{{ __('No Fire Service Contact found.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    

@endsection



