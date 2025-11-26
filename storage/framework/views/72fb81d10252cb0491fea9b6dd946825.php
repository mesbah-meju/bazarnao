

<?php $__env->startSection('content'); ?>
<?php if(env('MAIL_USERNAME') == null && env('MAIL_PASSWORD') == null): ?>
    <div class="">
        <div class="alert alert-danger d-flex align-items-center">
            <?php echo e(translate('Please Configure SMTP Setting to work all email sending functionality')); ?>,
            <a class="alert-link ml-2" href="<?php echo e(route('smtp_settings.index')); ?>"><?php echo e(translate('Configure Now')); ?></a>
        </div>
    </div>
<?php endif; ?>
<!-- <?php if(Auth::user()->user_type == 'admin' || in_array('1', json_decode(Auth::user()->staff->role->permissions))): ?>
<?php endif; ?> -->
<div class="row gutters-10">
    <div class="col-lg-6">
        <div class="row gutters-10">
            <div class="col-6">
                <div class="bg-grad-2 text-white rounded-lg mb-4 overflow-hidden">
                    <div class="px-3 pt-3">
                        <div class="opacity-50">
                            <span class="fs-12 d-block"><?php echo e(translate('Total')); ?></span>
                            <?php echo e(translate('Customer')); ?>

                            <?php
                              
                            ?>
                            
                        </div>
                        <div class="h3 fw-700 mb-3"><?php echo e(\App\Models\Customer::all()->count()); ?></div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path fill="rgba(255,255,255,0.3)" fill-opacity="1" d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
                    </svg>
                </div>
            </div>
            <div class="col-6">
                <div class="bg-grad-3 text-white rounded-lg mb-4 overflow-hidden">
                    <div class="px-3 pt-3">
                        <div class="opacity-50">
                            <span class="fs-12 d-block"><?php echo e(translate('Total')); ?></span>
                            <?php echo e(translate('Order')); ?>

                        </div>
                        <div class="h3 fw-700 mb-3"><?php echo e(\App\Models\OrderDetail::where('delivery_status','delivered')->groupBy('order_id')->get()->count()); ?></div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path fill="rgba(255,255,255,0.3)" fill-opacity="1" d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
                    </svg>
                </div>
            </div>
            <div class="col-6">
                <div class="bg-grad-1 text-white rounded-lg mb-4 overflow-hidden">
                    <div class="px-3 pt-3">
                        <div class="opacity-50">
                            <span class="fs-12 d-block"><?php echo e(translate('Total')); ?></span>
                            <?php echo e(translate('Product category')); ?>

                        </div>
                        <div class="h3 fw-700 mb-3"><?php echo e(\App\Models\Category::all()->count()); ?></div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path fill="rgba(255,255,255,0.3)" fill-opacity="1" d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
                    </svg>
                </div>
            </div>
            <div class="col-6">
                <div class="bg-grad-4 text-white rounded-lg mb-4 overflow-hidden">
                    <div class="px-3 pt-3">
                        <div class="opacity-50">
                            <span class="fs-12 d-block"><?php echo e(translate('Total')); ?></span>
                            <?php echo e(translate('Product brand')); ?>

                        </div>
                        <div class="h3 fw-700 mb-3"><?php echo e(\App\Models\Brand::all()->count()); ?></div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path fill="rgba(255,255,255,0.3)" fill-opacity="1" d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="row gutters-10">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0 fs-14"><?php echo e(translate('Products')); ?></h6>
                    </div>
                    <div class="card-body">
                        <canvas id="pie-1" class="w-100" height="305"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0 fs-14"><?php echo e(translate('Warehouse')); ?></h6>
                    </div>
                    <div class="card-body">
                        <canvas id="pie-2" class="w-100" height="305"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- <?php if(Auth::user()->user_type == 'admin' || in_array('1', json_decode(Auth::user()->staff->role->permissions))): ?>
<?php endif; ?> -->

    

<div class="card">
    <div class="card-header">
        <h6 class="mb-0"><?php echo e(translate('Top 12 Products')); ?></h6>
    </div>
    <div class="card-body">
        <div class="aiz-carousel gutters-10 half-outside-arrow" data-items="6" data-xl-items="5" data-lg-items="4" data-md-items="3" data-sm-items="2" data-arrows='true'>
            <?php $__currentLoopData = filter_products(\App\Models\Product::where('published', 1)->orderBy('num_of_sale', 'desc'))->limit(12)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="carousel-box">
                    <div class="aiz-card-box border border-light rounded shadow-sm hov-shadow-md mb-2 has-transition bg-white">
                        <div class="position-relative">
                            <a href="<?php echo e(route('product', $product->slug)); ?>" class="d-block">
                                <img
                                    class="img-fit lazyload mx-auto h-210px"
                                    src="<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>"
                                    data-src="<?php echo e(uploaded_asset($product->thumbnail_img)); ?>"
                                    alt="<?php echo e($product->getTranslation('name')); ?>"
                                    onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>';"
                                >
                            </a>
                        </div>
                        <div class="p-md-3 p-2 text-left">
                            <div class="fs-15">
                                <?php if(main_home_base_price($product->id) != main_home_discounted_base_price($product->id)): ?>
                                    <del class="fw-600 opacity-50 mr-1"><?php echo e(main_home_base_price($product->id)); ?></del>
                                <?php endif; ?>
                                <span class="fw-700 text-primary"><?php echo e(main_home_discounted_base_price($product->id)); ?></span>
                            </div>
                            <div class="rating rating-sm mt-1">
                                <?php echo e(renderStarRating($product->rating)); ?>

                            </div>
                            <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0">
                                <a href="<?php echo e(route('product', $product->slug)); ?>" class="d-block text-reset"><?php echo e($product->getTranslation('name')); ?></a>
                            </h3>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>

<div class="col-8">
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0"><?php echo e(translate('Top 20 Products (Stock Quantity & Value)')); ?></h6>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Product Name</th>
                    <th scope="col" class="text-center">Stock Quantity</th>
                    <th scope="col" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                $total = 0;
                ?>
                <?php

                  

                    $Parentproducts = \App\Models\Product::where('products.parent_id', '=', null)
                    ->leftJoin('product_stocks', 'products.id', '=', 'product_stocks.product_id')
                    ->groupBy('products.id')
                    ->select(
                        'products.id','products.purchase_price',
                        DB::raw('SUM(product_stocks.qty) as total_qty'),
                    )
                    ->orderByRaw('(products.purchase_price * SUM(product_stocks.qty)) DESC')
                    ->limit(20)
                    ->get();

                    $top20Products = $Parentproducts;
                ?>
                    <?php
                    $total = 0; 
                    ?>

                    <?php $__empty_1 = true; $__currentLoopData = $top20Products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php          
                        $name = \App\Models\Product::where('id', '=', $product->id)->value('name');       
                        $amount = $product->purchase_price * $product->total_qty;
                        $total += $amount;
                        ?>

                        <tr>
                            <th scope="row"><?php echo e($key + 1); ?></th>
                            <td class="text-left"><?php echo e($name); ?></td>
                            <td class="text-center"><?php echo e($product->total_qty); ?></td>
                            <td class="text-right">
                                <?php echo e(single_price($amount, 2)); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="text-center">No products found</td>
                        </tr>
                    <?php endif; ?>

                    <?php if($total > 0): ?>
                    <tr style="font-weight:bold;">
                        <td colspan="3" style="text-align:right;">Total</td>
                        <td style="text-align:right;"><?php echo e(single_price($total, 2)); ?></td>
                    </tr>
                    <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<script type="text/javascript">
    AIZ.plugins.chart('#pie-1',{
        type: 'doughnut',
        data: {
            labels: [
                '<?php echo e(translate('Total published products')); ?>',
                '<?php echo e(translate('Total admin products')); ?>'
            ],
            datasets: [
                {
                    data: [
                        <?php echo e(\App\Models\Product::where('published', 1)->get()->count()); ?>,
                        <?php echo e(\App\Models\Product::where('published', 1)->where('added_by', 'seller')->get()->count()); ?>,
                        <?php echo e(\App\Models\Product::where('published', 1)->where('added_by', 'admin')->get()->count()); ?>

                    ],
                    backgroundColor: [
                        "#fd3995",
                        "#34bfa3",
                        "#5d78ff",
                        '#fdcb6e',
                        '#d35400',
                        '#8e44ad',
                        '#006442',
                        '#4D8FAC',
                        '#CA6924',
                        '#C91F37'
                    ]
                }
            ]
        },
        options: {
            cutoutPercentage: 70,
            legend: {
                labels: {
                    fontFamily: 'Poppins',
                    boxWidth: 10,
                    usePointStyle: true
                },
                onClick: function () {
                    return '';
                },
                position: 'bottom'
            }
        }
    });

    AIZ.plugins.chart('#pie-2',{
        type: 'doughnut',
        data: {
            labels: [
                '<?php echo e(translate('Total warehouse')); ?>',
                '<?php echo e(translate('Total approved warehouse')); ?>',
                '<?php echo e(translate('Total pending warehouse')); ?>'
            ],
            datasets: [
                {
                    data: [
                        <?php echo e(\App\Models\Seller::where('verification_status', 1)->get()->count()); ?>,
                        <?php echo e(\App\Models\Seller::where('verification_status', 0)->count()); ?>

                    ],
                    backgroundColor: [
                        "#fd3995",
                        "#34bfa3",
                        "#5d78ff",
                        '#fdcb6e',
                        '#d35400',
                        '#8e44ad',
                        '#006442',
                        '#4D8FAC',
                        '#CA6924',
                        '#C91F37'
                    ]
                }
            ]
        },
        options: {
            cutoutPercentage: 70,
            legend: {
                labels: {
                    fontFamily: 'Montserrat',
                    boxWidth: 10,
                    usePointStyle: true
                },
                onClick: function () {
                    return '';
                },
                position: 'bottom'
            }
        }
    });
    var sfs = {
            labels: [
                <?php $__currentLoopData = \App\Models\Category::where('level', 0)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                '<?php echo e($category->getTranslation('name')); ?>',
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ],
            datasets: [
                <?php $__currentLoopData = \App\Models\Category::where('level', 0)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo e(\App\Models\Product::where('category_id', $category->id)->sum('num_of_sale')); ?>,
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ]
    }
    AIZ.plugins.chart('#graph-1',{
        type: 'bar',
        data: {
            labels: [
                <?php $__currentLoopData = \App\Models\Category::where('level', 0)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                '<?php echo e($category->getTranslation('name')); ?>',
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ],
            datasets: [{
                label: '<?php echo e(translate('Number of sale')); ?>',
                data: [
                    <?php $__currentLoopData = \App\Models\Category::where('level', 0)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $category_ids = \App\Utility\CategoryUtility::children_ids($category->id);
                            $category_ids[] = $category->id;
                        ?>
                    <?php echo e(\App\Models\Product::whereIn('category_id', $category_ids)->sum('num_of_sale')); ?>,
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                backgroundColor: [
                    <?php $__currentLoopData = \App\Models\Category::where('level', 0)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        'rgba(55, 125, 255, 0.4)',
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                borderColor: [
                    <?php $__currentLoopData = \App\Models\Category::where('level', 0)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        'rgba(55, 125, 255, 1)',
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    gridLines: {
                        color: '#f2f3f8',
                        zeroLineColor: '#f2f3f8'
                    },
                    ticks: {
                        fontColor: "#8b8b8b",
                        fontFamily: 'Poppins',
                        fontSize: 10,
                        beginAtZero: true
                    }
                }],
                xAxes: [{
                    gridLines: {
                        color: '#f2f3f8'
                    },
                    ticks: {
                        fontColor: "#8b8b8b",
                        fontFamily: 'Poppins',
                        fontSize: 10
                    }
                }]
            },
            legend:{
                labels: {
                    fontFamily: 'Poppins',
                    boxWidth: 10,
                    usePointStyle: true
                },
                onClick: function () {
                    return '';
                },
            }
        }
    });
    AIZ.plugins.chart('#graph-2',{
        type: 'bar',
        data: {
            labels: [
                <?php $__currentLoopData = \App\Models\Category::where('level', 0)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                '<?php echo e($category->getTranslation('name')); ?>',
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ],
            datasets: [{
                label: '<?php echo e(translate('Number of Stock')); ?>',
                data: [
                    <?php $__currentLoopData = \App\Models\Category::where('level', 0)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $category_ids = \App\Utility\CategoryUtility::children_ids($category->id);
                            $category_ids[] = $category->id;
                            
                            $products = \App\Models\Product::whereIn('category_id', $category_ids)->get();
                            $qty = 0;
                            foreach ($products as $key => $product) {
                                if ($product->variant_product) {
                                    foreach ($product->stocks as $key => $stock) {
                                        $qty += $stock->qty;
                                    }
                                }
                                else {
                                    $qty = $product->current_stock;
                                }
                            }
                        ?>
                        <?php echo e($qty); ?>,
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                backgroundColor: [
                    <?php $__currentLoopData = \App\Models\Category::where('level', 0)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        'rgba(253, 57, 149, 0.4)',
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                borderColor: [
                    <?php $__currentLoopData = \App\Models\Category::where('level', 0)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        'rgba(253, 57, 149, 1)',
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    gridLines: {
                        color: '#f2f3f8',
                        zeroLineColor: '#f2f3f8'
                    },
                    ticks: {
                        fontColor: "#8b8b8b",
                        fontFamily: 'Poppins',
                        fontSize: 10,
                        beginAtZero: true
                    }
                }],
                xAxes: [{
                    gridLines: {
                        color: '#f2f3f8'
                    },
                    ticks: {
                        fontColor: "#8b8b8b",
                        fontFamily: 'Poppins',
                        fontSize: 10
                    }
                }]
            },
            legend:{
                labels: {
                    fontFamily: 'Poppins',
                    boxWidth: 10,
                    usePointStyle: true
                },
                onClick: function () {
                    return '';
                },
            }
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\bazarnao\resources\views/backend/dashboard.blade.php ENDPATH**/ ?>