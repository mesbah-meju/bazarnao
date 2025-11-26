@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 bg-white">
        <table width="100%" class="datatable table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>{{ __('Head Code') }}</th>
                    <th>{{ __('PHead Name') }}</th>
                    <th>{{ __('PHead') }}</th>
                    <th>{{ __('Head Type') }}</th>
                    <th>{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($all_coa_head as $acc)
                    <tr>
                        <td>{{ $acc->head_code }}</td>
                        <td>{{ $acc->head_name }}</td>
                        <td>{{ $acc->pre_head_name }}</td>
                        <td>{{ $acc->head_type }}</td>
                        <td>
                            {{-- <a href="{{ route('accounts.edit_coa', $acc->HeadCode) }}"><i class="fas fa-edit"></i></a> --}}
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>

@endsection
