@extends('frontend.layouts.app')

@section('content')
    <section class="pt-5 mb-4">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h2>{{ __('Police Station') }}</h2>
                        </div>
                        <div class="card-body">
                            @if (count($policeStations) > 0)
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('SL') }}</th>
                                            <th>{{ __('Station Name') }}</th>
                                            <th>{{ __('Area Code') }}</th>
                                            <th>{{ __('District') }}</th>
                                            <th>{{ __('Branch') }}</th>
                                            <th>{{ __('Thana') }}</th>
                                            <th>{{ __('Area') }}</th>
                                            <th>{{ __('Phone') }}</th>
                                            <th>{{ __('Alt. Phone') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            {{-- <th>{{ __('Action') }}</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i=1 @endphp
                                        @foreach ($policeStations as $policeStation)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $policeStation->name }}</td>
                                                <td>{{ $policeStation->code }}</td>
                                                <td>{{ $policeStation->district }}</td>
                                                <td>{{ $policeStation->branch }}</td>
                                                <td>{{ $policeStation->thana }}</td>
                                                <td>{{ $policeStation->area }}</td>
                                                <td>{{ $policeStation->phone }}</td>
                                                <td>{{ $policeStation->alt_phone }}</td>
                                                <td>{{ $policeStation->email }}</td>
                                                {{-- <td class="text-center d-flex">
                                                   
                                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('police_station.edit', $policeStation->id) }}" title="{{ __('Edit') }}">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                    <a class="btn btn-soft-danger btn-icon btn-circle btn-sm" href="{{ route('police_station.destroy', $policeStation->id) }}" title="{{ __('Delete') }}">
                                                        <i class="las la-trash"></i>
                                                    </a>
                                                </td> --}}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                               
                            @else
                                <p>{{ __('No Police Station Contact found.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
