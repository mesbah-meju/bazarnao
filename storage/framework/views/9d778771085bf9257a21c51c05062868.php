<!doctype html>
<?php if(App\Models\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1): ?>
<html dir="rtl" lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<?php else: ?>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<?php endif; ?>

<head>
	<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
	<meta name="app-url" content="<?php echo e(getBaseURL()); ?>">
	<meta name="file-base-url" content="<?php echo e(getFileBaseURL()); ?>">

	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!-- Favicon -->
	<link rel="icon" href="<?php echo e(uploaded_asset(get_setting('site_icon'))); ?>">
	<title><?php echo e(get_setting('website_name').' | '.get_setting('site_motto')); ?></title>

	<!-- google font -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">

	<!-- aiz core css -->
	<link rel="stylesheet" href="<?php echo e(static_asset('assets/css/vendors.css')); ?>">
	<?php if(App\Models\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1): ?>
	<link rel="stylesheet" href="<?php echo e(static_asset('assets/css/bootstrap-rtl.min.css')); ?>">
	<?php endif; ?>
	<link rel="stylesheet" href="<?php echo e(static_asset('assets/css/aiz-core.css')); ?>">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />
	
	<?php echo $__env->yieldContent('style'); ?>

	<script>
		var AIZ = AIZ || {};
	</script>

</head>

<body class="">

	<div class="aiz-main-wrapper">
		<?php echo $__env->make('backend.inc.admin_sidenav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		<div class="aiz-content-wrapper">
			<?php echo $__env->make('backend.inc.admin_nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			<div class="aiz-main-content">
				<div class="px-15px px-lg-25px">
					<?php echo $__env->yieldContent('content'); ?>
				</div>
				<div class="bg-white text-center py-3 px-15px px-lg-25px mt-auto">
					<p class="mb-0">&copy; <?php echo e(get_setting('site_name')); ?> || Design & Developed by <a href="http://fouraxiz.com" target="_blank">4axiz IT Ltd</a></p>
				</div>
			</div><!-- .aiz-main-content -->
		</div><!-- .aiz-content-wrapper -->
	</div><!-- .aiz-main-wrapper -->

	<?php echo $__env->yieldContent('modal'); ?>


	<script src="<?php echo e(static_asset('assets/js/vendors.js')); ?>"></script>
	<script src="<?php echo e(static_asset('assets/js/aiz-core.js')); ?>"></script>
	<script src="<?php echo e(static_asset('assets/js/kinzi.print.min.js')); ?>"></script>
	<script src="<?php echo e(static_asset('assets/js/myscript.js')); ?>"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
	<?php echo $__env->yieldContent('script'); ?>

	<?php $__currentLoopData = session('flash_notification', collect())->toArray(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	<script type="text/javascript">
		AIZ.plugins.notify('<?php echo e($message["level"]); ?>', '<?php echo e($message["message"]); ?>');
	</script>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

	<script type="text/javascript">
		if ($('#lang-change').length > 0) {
			$('#lang-change .dropdown-menu a').each(function() {
				$(this).on('click', function(e) {
					e.preventDefault();
					var $this = $(this);
					var locale = $this.data('flag');
					$.post('<?php echo e(route("language.change")); ?>', {
						_token: '<?php echo e(csrf_token()); ?>',
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
					$("#search-menu").html(`<li class="aiz-side-nav-item"><span	class="text-center text-muted d-block"><?php echo e(translate('Nothing Found')); ?></span></li>`);
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

</html><?php /**PATH D:\xampp\htdocs\bazarnao\resources\views/backend/layouts/app.blade.php ENDPATH**/ ?>