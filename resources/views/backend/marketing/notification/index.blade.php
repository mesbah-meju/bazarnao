@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
    	<div class="row align-items-center">
    		<div class="col-md-12">
                <div class="card-header d-flex justify-content-between">
    				<div class="mb-0 h6">{{ translate('All Notification List') }}</div>
    				<a class="btn btm-sm btn-primary p-1" href="{{ route('notifications.create') }}" role="button">Send Notification</a>

    			</div>
    		</div>
    	</div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2>{{ __('Notifications') }}</h2>
                    </div>
                    <div class="card-body">
                        @if (count($notifications) > 0)
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('SL') }}</th>
                                        <th>{{ __('Send To') }}</th>
                                        {{-- <th>{{ __('Phone Number') }}</th> --}}
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Message') }}</th>
                                        <th>{{ __('Sender') }}</th>
                                        <th>{{ __('Send at') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i=1 @endphp
                                    @foreach ($notifications as $notification)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $notification->recipient_name }}</td>
                                            {{-- <td>{{ $notification->code }}</td> --}}
                                            <td>{{ $notification->recipient_email }}</td>
                                            <td>{{ $notification->title }}</td>
                                            <td>{{ $notification->message }}</td>
                                            <td>{{ $notification->sender_name }}</td>
                                            <td>{{ $notification->created_at }}</td>
                                            
                                            <td class="text-center d-flex">
                                               
                                                {{-- <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('police_station.edit', $notification->id) }}" title="{{ __('Edit') }}">
                                                    <i class="las la-edit"></i>
                                                </a> --}}
                                                <a class="btn btn-soft-danger btn-icon btn-circle btn-sm" href="{{ route('notifications.destroy', $notification->id) }}" title="{{ __('Delete') }}">
                                                    <i class="las la-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                           
                        @else
                            <p>{{ __('No Notification found.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    

@endsection



