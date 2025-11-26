

<?php $__env->startSection('content'); ?>

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3"><?php echo e(translate('Income Statement')); ?></h1>
        </div>
    </div>
</div>

<div class="card">
    <form id="sort_debit_vouchers" action="<?php echo e(route('income-statement-report.index')); ?>" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6"><?php echo e(translate('Income Statement')); ?></h5>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="warehouse_id"><?php echo e(translate('Warehouse')); ?> <span class="text-danger">*</span></label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">All Warehouse</option>
                            <?php $__currentLoopData = \App\Models\Warehouse::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option  value="<?php echo e($warehouse->id); ?>" <?php echo e((isset($warehouse_id) && $warehouse_id == $warehouse->id) ? 'selected' : ''); ?>><?php echo e($warehouse->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="fyear"><?php echo e(translate('Year')); ?></label>
                        <select name="fyear" id="fyear" class="form-control aiz-selectpicker" data-live-search="true">
                            <?php $__currentLoopData = $fyears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($fy->id); ?>" <?php echo e((isset($fyear) && $fyear == $fy->id) ? 'selected' : ''); ?>><?php echo e($fy->year_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="dtpToDate" class="form-label text-white"><?php echo e(translate('To Date')); ?></label><br>
                        <button type="submit" class="btn btn-success"><?php echo e(translate('Filter')); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <div class="card-body printArea">
        <div class="row pb-3 voucher-center">
            <div class="col-md-3">
                <img src="<?php echo e(asset('public/assets/img/logo.png')); ?>" alt="Logo" height="40px"><br><br>
            </div>
            <div class="col-md-6 text-center">
                <h2>Bazarnao</h2>
                <strong><u class="pt-4"><?php echo e(translate('Income Statement Voucher')); ?></u></strong>
            </div>
            <div class="col-md-3">
                <div class="pull-right" style="margin-right:20px;">
                    <b>
                        <label class="font-weight-600 mb-0"><?php echo e(translate('date')); ?></label> : <?php echo e(date('d/m/Y')); ?>

                    </b>
                    <br>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="datatable table table-striped table-hover" cellpadding="6" cellspacing="1">
                <thead class="table-bordered">
                    <tr>
                        
                    </tr>
                </thead>
               
            </table>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\bazarnao\resources\views/backend/accounts/reports/income_statement/index.blade.php ENDPATH**/ ?>