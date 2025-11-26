@extends('backend.layouts.app')

@section('content')

    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Offer Information Adding')}}</h5>
            </div>
            <div class="card-body">
              <form class="form-horizontal" action="{{ route('offer.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group row">
                    <label class="col-lg-3 col-from-label" for="name">{{translate('Offer Type')}}</label>
                    <div class="col-lg-9">
                        <select name="offer_type" id="offer_type" class="form-control aiz-selectpicker" onchange="offer_form()" required>
                            <option value="">{{translate('Select One') }}</option>
                            <option value="product_base">{{translate('For Products')}}</option>
                            <option value="cart_base">{{translate('For Total Orders')}}</option>
                        </select>
                    </div>
                </div>

                <div id="offer_form">

                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                </div>
              </from>
            </div>
        </div>
    </div>

@endsection
@section('script')

<script type="text/javascript">

    function offer_form(){
        var offer_type = $('#offer_type').val();
		$.post('{{ route('offer.get_offer_form') }}',{_token:'{{ csrf_token() }}', offer_type:offer_type}, function(data){
            $('#offer_form').html(data);
		});
    }

</script>

@endsection
