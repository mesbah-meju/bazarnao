<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="app-url" content="<?php echo e(env('APP_URL')); ?>">

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favicon -->
  	<title><?php echo e(config('app.name', 'eCommerce')); ?></title>

    <!-- google font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">

    <!-- aiz core css -->
    <link rel="stylesheet" href="<?php echo e(static_asset('assets/css/vendors.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(static_asset('assets/css/aiz-core.css')); ?>">

    <script>
        var AIZ = AIZ || {};
    </script>
</head>
<body>
    <div class="aiz-main-wrapper d-flex">

        <div class="flex-grow-1">
            <?php echo $__env->yieldContent('content'); ?>
        </div>

    </div><!-- .aiz-main-wrapper -->
    <script src="<?php echo e(static_asset('assets/js/vendors.js')); ?>" ></script>
    <script src="<?php echo e(static_asset('assets/js/aiz-core.js')); ?>" ></script>

    <?php echo $__env->yieldContent('script'); ?>

    <script type="text/javascript">
    <?php $__currentLoopData = session('flash_notification', collect())->toArray(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        AIZ.plugins.notify('<?php echo e($message['level']); ?>', '<?php echo e($message['message']); ?>');
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </script>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\bazarnao\resources\views/backend/layouts/blank.blade.php ENDPATH**/ ?>