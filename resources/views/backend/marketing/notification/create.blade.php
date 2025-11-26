@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
    	<div class="row align-items-center">
    		<div class="col-md-12">
                <div class="card-header d-flex justify-content-between">
    				<div class="mb-0 h6">{{ translate('Send Notification') }}</div>
    				{{-- <a class="btn btm-sm btn-primary p-1" href="{{ route('notifications.index') }}" role="button">Notifications List</a> --}}

    			</div>
    		</div>
    	</div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h2>{{ __('Send Notification') }}</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('notifications.store') }}" method="POST">
                            @csrf
                            <div class="col-md-12">
                                <div class="form-group" style="width: 100%!important; display: flex; flex-direction: column;">
                                    <label class="col-form-label">{{ translate('Send To') }}:</label>
                                    <select id="user_id" class="aiz-selectpicker select2" name="user_id[]" data-live-search="true" multiple required>
                                        <option value=''>All</option>
                                        @foreach (DB::table('users')->where('users.user_type','customer')->select('users.id as userId', 'users.name')->get() as $key => $user)
                                            <option @php if($sort_by==$user->userId) echo 'selected'; @endphp value="{{ $user->userId }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="title">{{ __('Title') }}</label>
                                <input type="text" name="title" id="title" class="form-control" placeholder="title" required>
                            </div>
                            <div class="form-group">
                                <label for="image_url">{{ __('Image Url') }}</label>
                                <input type="image_url" name="image_url" id="image_url" class="form-control" placeholder="image url">
                            </div>
                            <div class="form-group">
                                <label for="message">{{ __('Message') }}</label>
                                <textarea rows="5" name="message" id="message" class="form-control" placeholder="Write your message here" required></textarea>
                            </div>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary">{{ __('Send') }}</button>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

