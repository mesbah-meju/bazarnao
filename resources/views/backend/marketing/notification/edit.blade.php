@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
    	<div class="row align-items-center">
    		<div class="col-md-12">
                <div class="card-header d-flex justify-content-between">
    				<div class="mb-0 h6">{{ translate('Edit Police Station Contact') }}</div>
    				<a class="btn btm-sm btn-primary p-1" href="{{ route('police_station.index') }}" role="button">Police Station Contact List</a>

    			</div>
    		</div>
    	</div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h2>{{ __('Edit Police Station Contact') }}</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('police_station.update', $policeStations->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">{{ __('Station Name') }}</label>
                                <input type="text" name="name" id="name" value="{{$policeStations->name}}" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="code">{{ __('Area Code') }}</label>
                                <input type="text" name="code" id="code"  value="{{$policeStations->code}}"  class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="district">{{ __('District') }}</label>
                                <input type="text" name="district" id="district" value="{{$policeStations->district}}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="branch">{{ __('Branch') }}</label>
                                <input type="text" name="branch" id="branch" value="{{$policeStations->branch}}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="thana">{{ __('Thana') }}</label>
                                <input type="text" name="thana" id="thana" value="{{$policeStations->thana}}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="area">{{ __('Area') }}</label>
                                <input type="text" name="area" id="area" value="{{$policeStations->area}}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="phone">{{ __('Phone') }}</label>
                                <input type="text" name="phone" id="phone" value="{{$policeStations->phone}}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="alt_phone">{{ __('Alt. Phone') }}</label>
                                <input type="text" name="alt_phone" id="alt_phone" value="{{$policeStations->alt_phone}}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="email">{{ __('Email') }}</label>
                                <input type="text" name="email" id="email" value="{{$policeStations->email}}" class="form-control">
                            </div>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                                <a class="btn btn-dark mx-2 btn-sm" href="{{ route('police_station.index') }}">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
