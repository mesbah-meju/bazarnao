@extends('backend.layouts.app')
@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{translate('Upload New Purchase Invoice')}}</h1>
		</div>
		<div class="col-md-6 text-md-right">
			<a href="{{ route('uploaded-invoice.index') }}" class="btn btn-link text-reset">
				<i class="las la-angle-left"></i>
				<span>{{translate('Back to Purchase uploaded Invoice')}}</span>
			</a>
		</div>
	</div>
</div>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{translate('Drag & drop your Purchase Invoice')}}</h5>
    </div>
    <div class="card-body">

    	<div id="aiz-upload-invoice" class="h-420px" style="min-height: 65vh">
    		
    	</div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ static_asset('assets/js/invoicecore.js') }}" ></script>
	<script type="text/javascript">
		$(document).ready(function() {
			AIZ.plugins.aizUppy();
		});
        
	</script>

@endsection
