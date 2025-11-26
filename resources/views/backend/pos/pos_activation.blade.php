@extends('backend.layouts.app')

@section('content')

<h4 class="text-center text-muted">{{translate('POS Printer')}}</h4>
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Thermal Printer Size') }}</h5>
            </div>
            <div class="card-body text-center">
                <form class="form-horizontal" action="{{ route('business_settings.update') }}" method="POST">
                    @csrf
                    <div class="form-group row">
                        <input type="hidden" name="types[]" value="print_width">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="print_width" placeholder="{{ translate('Print width in mm') }}" 
                            value="{{ get_setting('print_width') }}">
                            <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2">{{ translate('mm') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
        function updateSettings(el, type){
            if($(el).is(':checked')){
                var value = 1;
            }
            else{
                var value = 0;
            }
            $.post('{{ route('business_settings.update.activation') }}', {_token:'{{ csrf_token() }}', type:type, value:value}, function(data){
                if(data == '1'){
                    AIZ.plugins.notify('success', '{{ translate('Settings updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
    </script>
@endsection
