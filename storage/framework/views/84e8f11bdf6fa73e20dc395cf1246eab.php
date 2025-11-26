<a href="javascript:void(0)" class="d-flex align-items-center text-reset h-100" data-toggle="dropdown" data-display="static">
    <i class="la la-shopping-cart la-2x opacity-80"></i>
    <span class="flex-grow-1 ml-1">
        <?php
            $cart = Session::get('cart', []); // Fetch cart from session
        ?>
        <span class="badge badge-primary badge-inline badge-pill"><?php echo e(count($cart)); ?></span>
    </span>
</a>
<div class="dropdown-menu dropdown-menu-right dropdown-menu-lg p-0 stop-propagation">
    <?php if(count($cart) > 0): ?>
        <div class="p-3 fs-15 fw-600 p-3 border-bottom">
            <?php echo e(translate('Cart Items')); ?>

        </div>
        <ul class="h-250px overflow-auto c-scrollbar-light list-group list-group-flush">
            <?php $total = 0; ?>
            <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $cartItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $product = App\Models\Product::find($cartItem['id']);
                    $total = $total + $cartItem['price']*$cartItem['quantity'];
                    if($product->is_group_product) {
                        $discount_price = App\Models\Group_product::where('group_product_id',$cartItem['id'])->sum('discount_amount');
                    } else {
                        $discount_price = App\Models\Product::where('id',$cartItem['id'])->sum('discount');
                    }
                ?>
                <?php if($product): ?>
                    <li class="list-group-item">
                        <span class="d-flex align-items-center">
                            <a href="<?php echo e(route('product', $product->slug)); ?>" class="text-reset d-flex align-items-center flex-grow-1">
                                <img
                                    src="<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>"
                                    data-src="<?php echo e(uploaded_asset($product->thumbnail_img)); ?>"
                                    class="img-fit lazyload size-60px rounded"
                                    alt="<?php echo e($product->getTranslation('name')); ?>"
                                    onerror="this.src='<?php echo e(static_asset('assets/img/default.jpg')); ?>';"  <!-- Fallback image -->
                                >
                                <span class="minw-0 pl-2 flex-grow-1">
                                    <span class="fw-600 mb-1 text-truncate-2"><?php echo e($product->getTranslation('name')); ?></span>
                                    <span><?php echo e($cartItem['quantity']); ?>x</span>
                                    <?php if($discount_price): ?>
                                        <del class="text-muted"><?php echo e(single_price($cartItem['price'] + $discount_price)); ?></del>
                                    <?php endif; ?>
                                    <span class="text-primary"><?php echo e(single_price($cartItem['price'])); ?></span>
                                </span>
                            </a>
                            <span>
                                <button onclick="removeFromCart(<?php echo e($key); ?>, <?php echo e($product->id); ?>)" class="btn btn-sm btn-icon stop-propagation">
                                    <i class="la la-close"></i>
                                </button>
                            </span>
                        </span>
                    </li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <div class="px-3 py-2 fs-15 border-top d-flex justify-content-between">
            <span class="opacity-60"><?php echo e(translate('Subtotal')); ?></span>
            <span class="fw-600"><?php echo e(single_price($total)); ?></span>
        </div>
        <div class="px-3 py-2 text-center border-top">
            <ul class="list-inline mb-0">
                <?php if(Auth::check()): ?>
                    <li class="list-inline-item">
                        <a href="<?php echo e(route('checkout.shipping_info')); ?>" class="btn btn-primary btn-sm">
                            <?php echo e(translate('Checkout')); ?>

                        </a>
                    </li>
                <?php else: ?>
                    <li class="list-inline-item">
                        <button class="btn btn-primary fw-600" onclick="showCheckoutModal()">
                            Continue to Shipping
                        </button>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    <?php else: ?>
        <div class="text-center p-3">
            <i class="las la-frown la-3x opacity-60 mb-3"></i>
            <h3 class="h6 fw-700"><?php echo e(translate('Your Cart is empty')); ?></h3>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH D:\xampp\htdocs\bazarnao\resources\views/frontend/partials/cart.blade.php ENDPATH**/ ?>