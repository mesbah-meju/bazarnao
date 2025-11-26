@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Edit Financial Year')}}</h5>
            </div>

            <form class="form-horizontal" action="{{ route('financial-years.update', $financial_year->id) }}" method="POST" enctype="multipart/form-data">
            	@csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="year_name">{{translate('Year Name')}} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" id="year_name" name="year_name" class="form-control" value="{{ $financial_year->year_name }}" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="start_date">{{translate('Start Date')}} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" id="start_date" name="start_date" class="form-control datepicker" value="{{ date('m/d/Y', strtotime($financial_year->start_date)) }}" required>
                        </div>
                    </div>
                
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="end_date">{{translate('End Date')}} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" id="end_date" name="end_date" class="form-control datepicker" value="{{ date('m/d/Y', strtotime($financial_year->end_date)) }}" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="status">{{ translate('Status') }} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select name="status" id="status" class="form-control aiz-selectpicker" required>
                                <option value="0" @selected($financial_year->status == 0)>{{ translate('Inactive') }}</option>
                                <option value="1" @selected($financial_year->status == 1)>{{ translate('Active') }}</option>
                                <option value="2" @selected($financial_year->status == 2)>{{ translate('Close') }}</option>
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
