@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Staff Target Setup')}}</h5>
            </div>

            <form class="form-horizontal" action="{{ route('targets.update',$target->id) }}" method="POST" enctype="multipart/form-data">
            	<input name="_method" type="hidden" value="PATCH">
            	@csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{translate('Month')}}</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$target->month}}/{{$target->year}}" placeholder="{{translate('Month')}}" id="month" name="month" class="form-control monthpicker" required>
                        </div>
                    </div>
                    <table class="table table-stripped table-bordered">
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Target (Amount)</th>
                            <th>Target (New Customer)</th>
                            <th>Recovery Target</th>
                        </tr>
                        @if($target->user != null)
                        <tr>
                            <td>{{$target->user->name}}</td>
                            <td>{{$target->user->phone}}</td>
                            <td>{{$target->user->email}}</td>
                            <td>
                                <input type="number" required class="form-control" placeholder="Enter Target Amount" value="{{$target->target}}" name="target">
                            </td>
                            <td>
                                <input type="number" class="form-control" placeholder="Enter Target New ustomer" value="{{$target->terget_customer}}" name="terget_customer">
                                
                            </td>
                            <td>
                                <input type="number" class="form-control" placeholder=" Recovery Target" value="{{$target->recovery_target}}" name="recovery_target">
                            </td>
                        </tr>
                        @endif
                    </table>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection
