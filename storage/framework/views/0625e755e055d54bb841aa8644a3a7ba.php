<a href="javascript:" class="d-flex align-items-center text-reset dropdown-toggle"  data-toggle="dropdown">
<?php if(auth()->guard()->check()): ?>
<?php echo e(translate(Auth::user()->name)); ?> <i class="la la-user la-2x"></i>
    <?php else: ?>
    Sign in <i class="la la-user la-2x"></i>
    <?php endif; ?>
</a>
<div class="dropdown-menu">
    
    <?php if(auth()->guard()->check()): ?>
                    <?php if(isAdmin()): ?>
                    <a class="dropdown-item" href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(translate('My Panel')); ?></a>
                    
                    <?php else: ?>
                    <a class="dropdown-item" href="<?php echo e(route('dashboard')); ?>"><?php echo e(translate('My Panel')); ?></a>
                    <a class="dropdown-item" href="<?php echo e(route('referral_link.index')); ?>"><?php echo e(translate('Referral Link')); ?></a>
                    <?php endif; ?>
                    <a class="dropdown-item" href="<?php echo e(route('logout')); ?>"><?php echo e(translate('Logout')); ?></a>
                    
                    <?php else: ?>
                    <a class="dropdown-item" href="<?php echo e(route('user.login')); ?>"><?php echo e(translate('Login')); ?></a>
                    <a class="dropdown-item" href="<?php echo e(route('user.registration')); ?>"><?php echo e(translate('Registration')); ?></a>
                    <?php endif; ?>
    
  </div><?php /**PATH D:\xampp\htdocs\bazarnao\resources\views/frontend/partials/login.blade.php ENDPATH**/ ?>