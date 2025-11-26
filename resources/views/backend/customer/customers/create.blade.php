@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Add a Customer')}}</h5>
            </div>

            <form class="form-horizontal" action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data">
            	@csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{translate('Name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="email">{{translate('Email')}}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{translate('Email')}}" id="email" name="email" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="phone">{{translate('Phone')}}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{translate('Phone')}}" id="phone" name="phone" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="area_code">{{translate('Assain Executive')}}</label>
                        <div class="col-sm-9">
                            <select class="select2 form-control aiz-selectpicker" id="executive" name="executive" data-toggle="select2" data-placeholder="Choose ..." data-live-search="true" disabled>
                            <!-- <option value="">Choose Executive</option> -->
                            <option value="">meradiacounter@bazarnao.com</option>
                        </select>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="password">{{translate('Password')}}</label>
                        <div class="col-sm-9">
                            <input type="password" placeholder="{{translate('Password')}}" id="password" name="password" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="area_code">{{translate('Area Code')}}</label>
                        <div class="col-sm-9">
                        <select class="select2 form-control aiz-selectpicker" id="area_code" name="area_code" data-toggle="select2" data-placeholder="Choose ..." data-live-search="true" required>
                            <option value="">Choose Area</option>
                            @foreach (\App\Models\Area::get() as $area)
                            <option value="{{ $area->code }}">{{ $area->name }}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="address">{{translate('Address')}}</label>
                        <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Address')}}" id="address" name="address" class="form-control" required>
                        </div>
                    </div>

                    

                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        <a href="{{route('customers.index')}}" class="btn btn-sm btn-danger">
                            <span class="aiz-side-nav-text">{{translate('Go Back')}}</span>
                        </a>
                    </div>


                </div>
            </form>

        </div>
    </div>
</div>

@endsection
