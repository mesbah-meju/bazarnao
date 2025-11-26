@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Add Financial Year')}}</h5>
            </div>

            <form class="form-horizontal" action="{{ route('financial-years.store') }}" method="POST" enctype="multipart/form-data">
            	@csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="year_name">{{translate('Year Name')}} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('Year Name')}}" id="year_name" name="year_name" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="start_date">{{translate('Start Date')}} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('Start Date')}}" id="start_date" name="start_date" class="form-control datepicker" required>
                        </div>
                    </div>
                
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="end_date">{{translate('End Date')}} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('End Date')}}" id="end_date" name="end_date" class="form-control datepicker" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="status">{{ translate('Status') }} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select name="status" id="status" class="form-control aiz-selectpicker" required>
                                <option value="1">{{ translate('Active') }}</option>
                                <option value="0">{{ translate('InActive') }}</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group mb-0 text-right">
                        <button type="button" class="btn btn-sm btn-warning">{{translate('Reset')}}</button>
                        <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
