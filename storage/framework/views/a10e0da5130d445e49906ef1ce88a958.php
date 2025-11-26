<a href="<?php echo e(route('wishlists.index')); ?>" class="d-flex align-items-center text-reset">
    <span class="flex-grow-1 ml-1">
        <?php if(Auth::check()): ?>
            <span class="badge badge-primary badge-inline badge-pill"><?php echo e(count(Auth::user()->wishlists)); ?></span>
        <?php else: ?>
            <span class="badge badge-primary badge-inline badge-pill">0</span>
        <?php endif; ?>
        
    </span>
    <?php echo e(translate('Wishlist')); ?>  &nbsp;<i class="la la-heart-o la-2x"></i>
</a>
<?php /**PATH D:\xampp\htdocs\bazarnao\resources\views/frontend/partials/wishlist.blade.php ENDPATH**/ ?>