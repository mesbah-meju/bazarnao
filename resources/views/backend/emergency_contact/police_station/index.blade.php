@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
    	<div class="row align-items-center">
    		<div class="col-md-12">
                <div class="card-header d-flex justify-content-between">
    				<div class="mb-0 h6">{{ translate('All Police Station Contact List') }}</div>
    				<a class="btn btm-sm btn-primary p-1" href="{{ route('police_station.create') }}" role="button">Add New Police Station Contact</a>

    			</div>
    		</div>
    	</div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2>{{ __('Police Station') }}</h2>
                    </div>
                    <div class="card-body">
                        @if (count($policeStations) > 0)
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('SL') }}</th>
                                        <th>{{ __('Station Name') }}</th>
                                        <th>{{ __('Area Code') }}</th>
                                        <th>{{ __('District') }}</th>
                                        <th>{{ __('Branch') }}</th>
                                        <th>{{ __('Thana') }}</th>
                                        <th>{{ __('Area') }}</th>
                                        <th>{{ __('Phone') }}</th>
                                        <th>{{ __('Alt. Phone') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i=1 @endphp
                                    @foreach ($policeStations as $policeStation)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $policeStation->name }}</td>
                                            <td>{{ $policeStation->code }}</td>
                                            <td>{{ $policeStation->district }}</td>
                                            <td>{{ $policeStation->branch }}</td>
                                            <td>{{ $policeStation->thana }}</td>
                                            <td>{{ $policeStation->area }}</td>
                                            <td>{{ $policeStation->phone }}</td>
                                            <td>{{ $policeStation->alt_phone }}</td>
                                            <td>{{ $policeStation->email }}</td>
                                            <td class="text-center d-flex">
                                               
                                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('police_station.edit', $policeStation->id) }}" title="{{ __('Edit') }}">
                                                    <i class="las la-edit"></i>
                                                </a>
                                                <a class="btn btn-soft-danger btn-icon btn-circle btn-sm" href="{{ route('police_station.destroy', $policeStation->id) }}" title="{{ __('Delete') }}">
                                                    <i class="las la-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                           
                        @else
                            <p>{{ __('No Police Station Contact found.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    

@endsection



