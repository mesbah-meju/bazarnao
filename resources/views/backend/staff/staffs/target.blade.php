@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Staff Target Information')}}</h5>
            </div>

                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{translate('Name')}}</label>
                        <div class="col-sm-9">
                            {{ $staff->user->name }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="email">{{translate('Email')}}</label>
                        <div class="col-sm-9">
                             {{ $staff->user->email }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="mobile">{{translate('Phone')}}</label>
                        <div class="col-sm-9">
                            {{ $staff->user->phone }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{translate('Role')}}</label>
                        <div class="col-sm-9">
                            {{$staff->role->name}}
                        </div>
                    </div>
                    <hr>
                    <table class="table table-bordered" style="width:100%">
                        <tr>
                            <th>Year</th>
                            <th>Month</th>
                            <th>Target</th>
                            <th>Achive</th>
                            <th>Status</th>
                        </tr>
                        @foreach($targets as $target)
                            <tr>
                                <td>{{$target->year}}</td>
                                <td>{{$target->month}}</td>
                                <td>{{$target->target}}</td>
                                <td>{{$target->target}}</td>
                                <td>N/A</td>
                            </tr>

                        @endforeach
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
