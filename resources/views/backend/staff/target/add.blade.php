@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Staff Target Setup')}}</h5>
            </div>

            <form class="form-horizontal" action="{{ route('targets.store') }}" method="POST" enctype="multipart/form-data">
            	@csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{translate('Month')}}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{translate('Month')}}" id="month" name="month" class="form-control monthpicker" required>
                        </div>
                    </div>
                    <table class="table table-stripped table-bordered">
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Target (Amount)</th>
                            <th>Target (New Customer)</th>
                            <th>Recovery Target</th>
                          
                        </tr>
                    @foreach($staffs as $staff)
                        @if($staff->user != null)
                        <tr>
                            <td>{{$staff->user->name}}</td>
                            <td>{{$staff->user->phone}}</td>
                            <td>{{$staff->user->email}}</td>
                            <td>
								@if ($staff->role != null)
									{{ $staff->role->getTranslation('name') }}
								@endif
							</td>
                            <td>
                                <input type="number" class="form-control" placeholder="Enter Target Amount" name="target[{{$staff->user_id}}]">
                            </td>
                            <td>
                                <input type="number" class="form-control" placeholder="Enter Target New Customer" name="new_customer[{{$staff->user_id}}]">
                            </td>
                            <td>
                                <input type="number" class="form-control" placeholder="Enter Recovery Target" name="recovery_target[{{$staff->user_id}}]">
                            </td>

                        </tr>
                        @endif
                    @endforeach
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
