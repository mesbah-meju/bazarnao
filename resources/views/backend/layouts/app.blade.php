<!doctype html>
@if(App\Models\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
<html dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@else
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endif

<head>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="app-url" content="{{ getBaseURL() }}">
	<meta name="file-base-url" content="{{ getFileBaseURL() }}">

	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!-- Favicon -->
	<link rel="icon" href="{{ uploaded_asset(get_setting('site_icon')) }}">
	<title>{{ get_setting('website_name').' | '.get_setting('site_motto') }}</title>

	<!-- google font -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">

	<!-- aiz core css -->
	<link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">
	@if(App\Models\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
	<link rel="stylesheet" href="{{ static_asset('assets/css/bootstrap-rtl.min.css') }}">
	@endif
	<link rel="stylesheet" href="{{ static_asset('assets/css/aiz-core.css') }}">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />
	
	@yield('style')

	<script>
		var AIZ = AIZ || {};
	</script>

</head>

<body class="">

	<div class="aiz-main-wrapper">
		@include('backend.inc.admin_sidenav')
		<div class="aiz-content-wrapper">
			@include('backend.inc.admin_nav')
			<div class="aiz-main-content">
				<div class="px-15px px-lg-25px">
					@yield('content')
				</div>
				<div class="bg-white text-center py-3 px-15px px-lg-25px mt-auto">
					<p class="mb-0">&copy; {{ get_setting('site_name') }} || Design & Developed by <a href="http://fouraxiz.com" target="_blank">4axiz IT Ltd</a></p>
				</div>
			</div><!-- .aiz-main-content -->
		</div><!-- .aiz-content-wrapper -->
	</div><!-- .aiz-main-wrapper -->

	@yield('modal')


	<script src="{{ static_asset('assets/js/vendors.js') }}"></script>
	<script src="{{ static_asset('assets/js/aiz-core.js') }}"></script>
	<script src="{{ static_asset('assets/js/kinzi.print.min.js') }}"></script>
	<script src="{{ static_asset('assets/js/myscript.js') }}"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
	@yield('script')

	@foreach (session('flash_notification', collect())->toArray() as $message)
	<script type="text/javascript">
		AIZ.plugins.notify('{{ $message["level"] }}', '{{ $message["message"] }}');
	</script>
	@endforeach

	<script type="text/javascript">
		if ($('#lang-change').length > 0) {
			$('#lang-change .dropdown-menu a').each(function() {
				$(this).on('click', function(e) {
					e.preventDefault();
					var $this = $(this);
					var locale = $this.data('flag');
					$.post('{{ route("language.change") }}', {
						_token: '{{ csrf_token() }}',
						locale: locale
					},
					function(data) {
						location.reload();
					});
				});
			});
		}
		function menuSearch() {
			var filter, item;
			filter = $("#menu-search").val().toUpperCase();
			items = $("#main-menu").find("a");
			items = items.filter(function(i, item) {
				if ($(item).find(".aiz-side-nav-text")[0].innerText.toUpperCase().indexOf(filter) > -1 && $(item).attr('href') !== '#') {
					return item;
				}
			});

			if (filter !== '') {
				$("#main-menu").addClass('d-none');
				$("#search-menu").html('')
				if (items.length > 0) {
					for (i = 0; i < items.length; i++) {
						const text = $(items[i]).find(".aiz-side-nav-text")[0].innerText;
						const link = $(items[i]).attr('href');
						$("#search-menu").append(`<li class="aiz-side-nav-item"><a href="${link}" class="aiz-side-nav-link"><i class="las la-ellipsis-h aiz-side-nav-icon"></i><span>${text}</span></a></li`);
					}
				} else {
					$("#search-menu").html(`<li class="aiz-side-nav-item"><span	class="text-center text-muted d-block">{{ translate('Nothing Found') }}</span></li>`);
				}
			} else {
				$("#main-menu").removeClass('d-none');
				$("#search-menu").html('')
			}
		}
		function printDiv() {
			$('.printArea').kinziPrint({
				importCSS: true,
				header: $('#header').html(),
				footer: $('#footer').html(),
				debug: false
			});
		}
		$('.datepicker').datetimepicker({
			"allowInputToggle": true,
			"showClose": true,
			"showClear": true,
			"showTodayButton": true,
			"format": "MM/DD/YYYY",
		});
		$('.yearpicker').datetimepicker({
			"allowInputToggle": true,
			"showClose": true,
			"showClear": true,
			"format": "YYYY",
		});
		$('.monthpicker').datetimepicker({
			"allowInputToggle": true,
			"showClose": true,
			"showClear": true,
			"format": "MM/YYYY",
		});
	</script>

</body>

</html>