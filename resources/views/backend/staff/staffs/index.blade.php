@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3  bg-white p-2">
    <div class="align-items-center bg-white">
        <form id="filterForm" action="{{ route('staffs.index') }}" method="get">
            <div class="form-group row">

                <div class="col-md-3">
                    <label class="col-form-label">{{translate('Sort by Name')}} :</label>
                    <select id="user_id" class="aiz-selectpicker select2" name="user_id" data-live-search="true">
                        <option value=''>All</option>
                        @foreach (DB::table('staff')->leftJoin('users','staff.user_id','=','users.id')->select('staff.*','users.id as userId','users.name')->get() as $key => $user)
                        <option @if($sort_by == $user->userId) selected @endif value="{{ $user->userId }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="col-form-label">{{translate('Sort by Email')}} :</label>
                    <select id="email" class="aiz-selectpicker select2" name="email" data-live-search="true">
                        <option value=''>All</option>
                        @foreach (DB::table('staff')->leftJoin('users','staff.user_id','=','users.id')->select('staff.*','users.id as userId','users.email')->get() as $key => $user)
                        <option @if($email == $user->email) selected @endif value="{{ $user->email }}">{{ $user->email }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="col-form-label">{{translate('Sort by Role')}} :</label>
                    <select id="role_id" class="aiz-selectpicker select2" name="role_id" data-live-search="true">
                        <option value=''>All</option>
                        @foreach (DB::table('roles')->get() as $key => $role)
                        <option @if($role_id == $role->id) selected @endif value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <br>
                    <button class="btn btn-sm btn-primary" type="submit">{{ translate('Filter') }}</button>
                    <button class="btn btn-sm btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                </div>
            </div>
        </form>
    </div>

    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{translate('All Staffs')}}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('staffs.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add New Staffs')}}</span>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{translate('Staffs')}}</h5>
    </div>
    
    <div class="card-body">
        <div class="printArea">
            <style>
                th{text-align:center;}
            </style>
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th width="10%">#</th>
                        <th>{{translate('Name')}}</th>
                        <th>{{translate('Email')}}</th>
                        <th>{{translate('Phone')}}</th>
                        <th>{{translate('Role')}}</th>
                        <th>{{translate('Ware House')}}</th>
                        <th width="20%">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staffs as $key => $staff)
                        @if($staff->user != null)
                            <tr>
                                <td>{{ ($key+1) + ($staffs->currentPage() - 1)*$staffs->perPage() }}</td>
                                <td>{{$staff->user->name}}</td>
                                <td>{{$staff->user->email}}</td>
                                <td>{{$staff->user->phone}}</td>
                                <td>
                                    @if ($staff->role != null)
                                        {{ $staff->role->getTranslation('name') }}
                                    @endif
                                </td>
                                <td>
                                    @php 
                                        $warehouseIds = getWearhouseBuUserId($staff->user_id);
                                        $warehouseNames = $warehouseIds ? (\App\Models\Warehouse::whereIn('id', $warehouseIds)->pluck('name')) : collect([]);
                                        $data['warehousearray'] = $warehouseNames;
                                    @endphp
                                    <span>{{ $warehouseNames->implode(', ') }}</span>
                                </td>
                                <td class="text-right">
                                    <a href="{{route('staffs.login', encrypt($staff->id))}}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Log in as this Staff') }}">
                                        <i class="las la-sign-in-alt"></i>
                                    </a>
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('staffs.edit', encrypt($staff->id))}}" title="{{ translate('Edit') }}">
                                        <i class="las la-edit"></i>
                                    </a>
                                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('staffs.destroy', $staff->id)}}" title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                    @if($staff->role->role_type == 2)
                                    <a class="btn btn-primary btn-xs" href="{{route('staffs.target', encrypt($staff->id))}}" title="{{ translate('Target') }}">
                                        Target
                                    </a>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $staffs->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    document.getElementById('user_id').addEventListener('change', function() {
        clearOtherFields(['email', 'role_id']);
        document.getElementById('filterForm').submit();
    });

    document.getElementById('email').addEventListener('change', function() {
        clearOtherFields(['user_id', 'role_id']);
         document.getElementById('filterForm').submit();
    });

    document.getElementById('role_id').addEventListener('change', function() {
        clearOtherFields(['user_id', 'email']);
        document.getElementById('filterForm').submit();
    });

    function clearOtherFields(fieldIds) {
        fieldIds.forEach(function(id) {
            document.getElementById(id).value = '';
        });
    }
</script>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
