@extends('frontend.layouts.app')

@section('content')
    <section class="pt-5 mb-4">
        <div class="container">
            <h2 class="mb-4">Emergency Contact</h2>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <a href="{{ route('contact.fire_services') }}"><h4 class="card-title">Fire Service</h4></a>
                            {{-- <a href="#" class="btn btn-primary">Contact</a> --}}
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                           <a href="{{ route('contact.police_stations') }}"> <h4 class="card-title">Police Station</h4></a>
                            {{-- <a href="#" class="btn btn-primary">Contact</a> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
