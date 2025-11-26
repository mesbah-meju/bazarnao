

<?php $__env->startSection('content'); ?>

<div class="h-100 bg-cover bg-center py-5 d-flex align-items-center" style="background-image: url(<?php echo e(uploaded_asset(get_setting('admin_login_background'))); ?>)">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-xl-4 mx-auto">
                <div class="card text-left">
                    <div class="card-body">
                        <div class="mb-5 text-center">
                            <img src="<?php echo e(uploaded_asset(get_setting('system_logo_black'))); ?>" class="mw-100 mb-4" height="40">
                            <h1 class="h3 text-primary mb-0"><?php echo e(translate('Welcome to')); ?> <br><?php echo e(env('APP_NAME')); ?></h1>
                            <p><?php echo e(translate('Login to your account.')); ?></p>
                        </div>
                        <form class="pad-hor" method="POST" role="form" action="<?php echo e(route('login')); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <input id="email" type="email" class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" name="email" value="<?php echo e(old('email')); ?>" required autofocus placeholder="<?php echo e(translate('Email')); ?>">
                                <?php if($errors->has('email')): ?>
                                    <span class="invalid-feedback" role="alert">
                                        <strong><?php echo e($errors->first('email')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <input id="password" type="password" class="form-control<?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" name="password" required placeholder="<?php echo e(translate('Password')); ?>">
                                <?php if($errors->has('password')): ?>
                                    <span class="invalid-feedback" role="alert">
                                        <strong><?php echo e($errors->first('password')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <div class="text-left">
                                        <label class="aiz-checkbox">
                                            <input type="checkbox" name="remember" id="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>
                                            <span><?php echo e(translate('Remember Me')); ?></span>
                                            <span class="aiz-square-check"></span>
                                        </label>
                                    </div>
                                </div>
                                <?php if(env('MAIL_USERNAME') != null && env('MAIL_PASSWORD') != null): ?>
                                    <div class="col-sm-6">
                                        <div class="text-right">
                                            <a href="<?php echo e(route('password.request')); ?>" class="text-reset fs-14"><?php echo e(translate('Forgot password ?')); ?></a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                <?php echo e(translate('Login')); ?>

                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script type="text/javascript">
        function autoFill(){
            $('#email').val('admin@example.com');
            $('#password').val('123456');
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.blank', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\bazarnao\resources\views/auth/login.blade.php ENDPATH**/ ?>