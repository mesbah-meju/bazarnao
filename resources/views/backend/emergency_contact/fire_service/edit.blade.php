@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
    	<div class="row align-items-center">
    		<div class="col-md-12">
                <div class="card-header d-flex justify-content-between">
    				<div class="mb-0 h6">{{ translate('Edit Fire Service Contact') }}</div>
    				<a class="btn btm-sm btn-primary p-1" href="{{ route('fire_service.index') }}" role="button">Fire Service Contact List</a>

    			</div>
    		</div>
    	</div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h2>{{ __('Edit Fire Service Contact') }}</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('fire_service.update', $fireServices->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="service_name">{{ __('Service Name') }}</label>
                                <input type="text" name="service_name" id="service_name" value="{{$fireServices->service_name}}" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="area_code">{{ __('Area Code') }}</label>
                                <input type="text" name="area_code" id="area_code"  value="{{$fireServices->area_code}}"  class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="provider_name">{{ __('Provider Name') }}</label>
                                <input type="text" name="provider_name" id="provider_name" value="{{$fireServices->provider_name}}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="contact_phone">{{ __('Contact Phone') }}</label>
                                <input type="text" name="contact_phone" id="contact_phone" value="{{$fireServices->contact_phone}}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="branch">{{ __('Contact Email') }}</label>
                                <input type="text" name="contact_email" id="contact_email" value="{{$fireServices->contact_email}}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="duration_hours">{{ __('Duration Hours') }}</label>
                                <input type="text" name="duration_hours" id="duration_hours" value="{{$fireServices->duration_hours}}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="description">{{ __('Description') }}</label>
                                <input type="text" name="description" id="description" value="{{$fireServices->description}}" class="form-control">
                            </div>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                                <a class="btn btn-dark mx-2 btn-sm" href="{{ route('fire_service.index') }}">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
