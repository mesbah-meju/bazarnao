@extends('backend.layouts.app')

@section('content')

    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0 h6">{{translate('Offer Information Update')}}</h3>
            </div>
            <form action="{{ route('offer.update', $offer->id) }}" method="POST">
                <input name="_method" type="hidden" value="PATCH">
            	@csrf
                <div class="card-body">
                    <input type="hidden" name="id" value="{{ $offer->id }}" id="id">
                    <div class="form-group row">
                        <label class="col-lg-3 col-from-label" for="name">{{translate('Offer Type')}}</label>
                        <div class="col-lg-9">
                            <select name="offer_type" id="offer_type" class="form-control aiz-selectpicker" onchange="offer_form()" required>
                                @if ($offer->type == "product_base"))
                                    <option value="product_base" selected>{{translate('For Products')}}</option>
                                @elseif ($offer->type == "cart_base")
                                    <option value="cart_base">{{translate('For Total Orders')}}</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div id="offer_form">

                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                    </div>
            </form>

        </div>
    </div>


@endsection
@section('script')

<script type="text/javascript">

    function offer_form(){
        var offer_type = $('#offer_type').val();
        var id = $('#id').val();
		$.post('{{ route('offer.get_offer_form_edit') }}',{_token:'{{ csrf_token() }}', offer_type:offer_type, id:id}, function(data){
            $('#offer_form').html(data);
		});
    }

    $(document).ready(function(){
        offer_form();
    });


</script>

@endsection
