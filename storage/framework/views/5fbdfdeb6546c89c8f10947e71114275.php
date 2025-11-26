

<?php $__env->startSection('content'); ?>

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3"><?php echo e(translate('Income Statement')); ?></h1>
        </div>
    </div>
</div>

<div class="card">
    <form id="sort_debit_vouchers" action="<?php echo e(route('income-statement-yearly-report.index')); ?>" method="GET">
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
                            <?php $__currentLoopData = $fyears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fyear): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($fyear->id); ?>" <?php echo e((isset($curentYear) && $curentYear->id == $fyear->id) ? 'selected' : ''); ?>><?php echo e($fyear->year_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="dtpToDate" class="form-label text-white"><?php echo e(translate('To Date')); ?></label><br>
                        <button type="submit" class="btn btn-success"><?php echo e(translate('Filter')); ?></button>
                        <button class="btn btn-warning mr-2" onclick="printDiv()" type="button"><?php echo e(translate('Print')); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body printArea">
        <div class="row pb-3 align-items-center">
            <div class="col-md-3">
                <img src="<?php echo e(asset('public/assets/img/logo.png')); ?>" alt="Logo" height="40px"><br><br>
            </div>
            <div class="col-md-6 text-center">
                <h4><strong class="">Bazar Nao Limited</strong><br></h4>
                4th Floor, AGM Chandrima, House 12, Road 08, Block J, Baridhara, Dhaka-1212.<br>
                info@bazarnao.com<br>
                +880 1969 906 699<br>
            </div>
            <div class="col-md-3 text-right">
                <div class="pull-right">
                    <b>
                        <label class="font-weight-600 mb-0"><?php echo e(translate('date')); ?></label> : <?php echo e(date('d/m/Y')); ?>

                    </b>
                </div>
            </div>
        </div>

        <div class="row pb-3 voucher-center align-items-center">
            <div class="col-md-12 text-center">
                <strong><u class="pt-4"><?php echo e(translate('Income Statement for ' . $curentYear->year_name)); ?></u></strong>
            </div>
        </div>

        <div class="row">
            <div class="table-responsive">
            <table width="99%" align="left" class="datatableReport table table-striped table-bordered table-hover general_ledger_report_tble print-font-size">
                <thead>
                    <tr>
                        <th width="16%" bgcolor="#E7E0EE" align="left"><?php echo e(translate('Particulars')); ?></th>
                        <?php
                            $time = strtotime($curentYear->start_date);
                            $startmonth = date('n',  strtotime($curentYear->start_date));
                        ?>
                        
                        <?php $__currentLoopData = $financial_years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $financial_year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th width="7%" bgcolor="#E7E0EE" class="profitamount text-right"><?php echo e($financial_year->year_name); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($incomes) > 0): ?>
                        <?php $__currentLoopData = $incomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $income): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td align="left"><?php echo e($income['head']); ?></td>
                                <td align="right" colspan="12"></td>
                            </tr>
                            <?php if(count($income['nextlevel']) > 0): ?>
                                <?php $__currentLoopData = $income['nextlevel']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td align="left" style="padding-left: 80px;"><?php echo e($value['headName']); ?></td>
                                        <td align="right" colspan="12" class="profitamount"></td>
                                    </tr>
                                    <?php if(count($value['innerHead']) > 0): ?>
                                        <?php $__currentLoopData = $value['innerHead']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($startmonth == 1): ?>
                                                <?php
                                                    $yearly_incomes = $inner['amount1'] + $inner['amount2'] + $inner['amount3'] + $inner['amount4'] + $inner['amount5'] + $inner['amount6'] + $inner['amount7'] + $inner['amount8'] + $inner['amount9'] + $inner['amount10'] + $inner['amount11'] + $inner['amount12'];
                                                ?>
                                                <tr>
                                                    <td align="left" style="padding-left: 160px;"><?php echo e($inner['headName']); ?></td>
                                                    <td align="right" class="profitamount"><?php echo e(number_format($yearly_incomes, 2)); ?></td>
                                                    <td align="right" class="profitamount"><?php echo e(number_format(0, 2)); ?></td>
                                                    <td align="right" class="profitamount"><?php echo e(number_format(0, 2)); ?></td>
                                                </tr>
                                            <?php else: ?>
                                                <?php
                                                    $yearly_incomes = $inner['amount7'] + $inner['amount8'] + $inner['amount9'] + $inner['amount10'] + $inner['amount11'] + $inner['amount12'] + $inner['amount1'] + $inner['amount2'] + $inner['amount3'] + $inner['amount4'] + $inner['amount5'] + $inner['amount6'];
                                                ?>
                                                <tr>
                                                    <td align="left" style="padding-left: 160px;"><?php echo e($inner['headName']); ?></td>
                                                    <td align="right" class="profitamount"><?php echo e(number_format($yearly_incomes, 2)); ?></td>
                                                    <td align="right" class="profitamount"><?php echo e(number_format(0, 2)); ?></td>
                                                    <td align="right" class="profitamount"><?php echo e(number_format(0, 2)); ?></td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($startmonth == 1): ?>
                        <?php
                            $total_yearly_incomes = $incomes[0]['gtotal1'] + $incomes[0]['gtotal2'] + $incomes[0]['gtotal3'] + $incomes[0]['gtotal4'] + $incomes[0]['gtotal5'] + $incomes[0]['gtotal6'] + $incomes[0]['gtotal7'] + $incomes[0]['gtotal8'] + $incomes[0]['gtotal9'] + $incomes[0]['gtotal10'] + $incomes[0]['gtotal11'] + $incomes[0]['gtotal12'];
                        ?>
                            <tr>
                                <td align="right"><strong><?php echo e(translate('Total Income')); ?></strong></td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format($total_yearly_incomes, 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                            </tr>
                        <?php else: ?>
                        <?php
                            $total_yearly_incomes = $incomes[0]['gtotal7'] + $incomes[0]['gtotal8'] + $incomes[0]['gtotal9'] + $incomes[0]['gtotal10'] + $incomes[0]['gtotal11'] + $incomes[0]['gtotal12'] + $incomes[0]['gtotal1'] + $incomes[0]['gtotal2'] + $incomes[0]['gtotal3'] + $incomes[0]['gtotal4'] + $incomes[0]['gtotal5'] + $incomes[0]['gtotal6'];
                        ?>
                            <tr>
                                <td align="right"><strong><?php echo e(translate('Total Income')); ?></strong></td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format($total_yearly_incomes, 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr bgcolor="#E7E0EE">
                            <td colspan="13"> &nbsp;</td>
                        </tr>
                    <?php endif; ?>

                    <?php if(count($costofgoodsolds) > 0): ?>
                        <?php $__currentLoopData = $costofgoodsolds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $costofgoodsold): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td align="left" style="padding-left: 80px;"><?php echo e($costofgoodsold['headName']); ?></td>
                                <td align="right" colspan="12"></td>
                            </tr>

                            <?php if(count($costofgoodsold['innerHead']) > 0): ?>
                                <?php $__currentLoopData = $costofgoodsold['innerHead']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($startmonth == 1): ?>
                                    <?php
                                        $yearly_cost_of_goods_sold = $inner['amount1'] + $inner['amount2'] + $inner['amount3'] + $inner['amount4'] + $inner['amount5'] + $inner['amount6'] + $inner['amount7'] + $inner['amount8'] + $inner['amount9'] + $inner['amount10'] + $inner['amount11'] + $inner['amount12'];
                                    ?>
                                        <tr>
                                            <td align="left" style="padding-left: 160px;"><?php echo e($inner['headName']); ?></td>
                                            <td align="right" class="profitamount"><?php echo e(number_format($yearly_cost_of_goods_sold, 2)); ?></td>
                                            <td align="right" class="profitamount"><?php echo e(number_format(0, 2)); ?></td>
                                            <td align="right" class="profitamount"><?php echo e(number_format(0, 2)); ?></td>
                                        </tr>
                                    <?php else: ?>
                                    <?php
                                        $yearly_cost_of_goods_sold = $inner['amount7'] + $inner['amount8'] + $inner['amount9'] + $inner['amount10'] + $inner['amount11'] + $inner['amount12'] + $inner['amount1'] + $inner['amount2'] + $inner['amount3'] + $inner['amount4'] + $inner['amount5'] + $inner['amount6'];
                                    ?>
                                        <tr>
                                            <td align="left" style="padding-left: 160px;"><?php echo e($inner['headName']); ?></td>
                                            <td align="right" class="profitamount"><?php echo e(number_format($yearly_cost_of_goods_sold, 2)); ?></td>
                                            <td align="right" class="profitamount"><?php echo e(number_format(0, 2)); ?></td>
                                            <td align="right" class="profitamount"><?php echo e(number_format(0, 2)); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($startmonth == 1): ?>
                        <?php
                            $total_yearly_cost_of_goods_sold = $costofgoodsolds[0]['subtota1'] + $costofgoodsolds[0]['subtota2'] + $costofgoodsolds[0]['subtota3'] + $costofgoodsolds[0]['subtota4'] + $costofgoodsolds[0]['subtota5'] + $costofgoodsolds[0]['subtota6'] + $costofgoodsolds[0]['subtota7'] + $costofgoodsolds[0]['subtota8'] + $costofgoodsolds[0]['subtota9'] + $costofgoodsolds[0]['subtota10'] + $costofgoodsolds[0]['subtota11'] + $costofgoodsolds[0]['subtota12'];
                        ?>
                            <tr>
                                <td align="right"><strong><?php echo e(translate('Total Cost of Goods Sold')); ?></strong></td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format($total_yearly_cost_of_goods_sold, 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                            </tr>
                        <?php else: ?>
                        <?php
                            $total_yearly_cost_of_goods_sold = $costofgoodsolds[0]['subtota7'] + $costofgoodsolds[0]['subtota8'] + $costofgoodsolds[0]['subtota9'] + $costofgoodsolds[0]['subtota10'] + $costofgoodsolds[0]['subtota11'] + $costofgoodsolds[0]['subtota12'] + $costofgoodsolds[0]['subtota1'] + $costofgoodsolds[0]['subtota2'] + $costofgoodsolds[0]['subtota3'] + $costofgoodsolds[0]['subtota4'] + $costofgoodsolds[0]['subtota5'] + $costofgoodsolds[0]['subtota6'];
                        ?>
                            <tr>
                                <td align="right"><strong><?php echo e(translate('Total Cost of Goods Sold')); ?></strong></td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format($total_yearly_cost_of_goods_sold, 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if($startmonth == 1): ?>
                        <?php
                            $yearly_gross_profit = $total_yearly_incomes - $total_yearly_cost_of_goods_sold;
                        ?>
                            <tr bgcolor="#E7E0EE">
                                <td align="right"><strong><?php echo e(translate('Gross Profit')); ?></strong></td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format($yearly_gross_profit, 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                            </tr> 
                        <?php else: ?>
                        <?php
                            $yearly_gross_profit = $total_yearly_incomes - $total_yearly_cost_of_goods_sold;
                        ?>
                            <tr bgcolor="#E7E0EE">
                                <td align="right"><strong><?php echo e(translate('Gross Profit')); ?></strong></td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format($yearly_gross_profit, 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if(count($expenses) > 0): ?>
                        <?php $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td align="left"><?php echo e($expense['head']); ?></td>
                                <td align="right" colspan="12"></td>
                            </tr>
                            <?php if(count($expense['nextlevel']) > 0): ?>
                                <?php $__currentLoopData = $expense['nextlevel']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td align="left" style="padding-left: 80px;"><?php echo e($value['headName']); ?></td>
                                        <td align="right" colspan="12" class="profitamount"></td>
                                    </tr>
                                    <?php if(count($value['innerHead']) > 0): ?>
                                        <?php $__currentLoopData = $value['innerHead']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($startmonth == 1): ?>
                                            <?php
                                                $yearly_expenses = $inner['amount1'] + $inner['amount2'] + $inner['amount3'] + $inner['amount4'] + $inner['amount5'] + $inner['amount6'] + $inner['amount7'] + $inner['amount8'] + $inner['amount9'] + $inner['amount10'] + $inner['amount11'] + $inner['amount12'];
                                            ?>
                                                <tr>
                                                    <td align="left" style="padding-left: 160px;"><?php echo e($inner['headName']); ?></td>
                                                    <td align="right" class="profitamount"><?php echo e(number_format($yearly_expenses, 2)); ?></td>
                                                    <td align="right" class="profitamount"><?php echo e(number_format(0, 2)); ?></td>
                                                    <td align="right" class="profitamount"><?php echo e(number_format(0, 2)); ?></td>
                                                </tr>
                                            <?php else: ?>
                                            <?php
                                                $yearly_expenses = $inner['amount7'] + $inner['amount8'] + $inner['amount9'] + $inner['amount10'] + $inner['amount11'] + $inner['amount12'] + $inner['amount1'] + $inner['amount2'] + $inner['amount3'] + $inner['amount4'] + $inner['amount5'] + $inner['amount6'];
                                            ?>
                                                <tr>
                                                    <td align="left" style="padding-left: 160px;"><?php echo e($inner['headName']); ?></td>
                                                    <td align="right" class="profitamount"><?php echo e(number_format($yearly_expenses, 2)); ?></td>
                                                    <td align="right" class="profitamount"><?php echo e(number_format(0, 2)); ?></td>
                                                    <td align="right" class="profitamount"><?php echo e(number_format(0, 2)); ?></td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($startmonth == 1): ?>
                        <?php
                            $total_yearly_expenses = $expenses[0]['gtotal1'] + $expenses[0]['gtotal2'] + $expenses[0]['gtotal3'] + $expenses[0]['gtotal4'] + $expenses[0]['gtotal5'] + $expenses[0]['gtotal6'] + $expenses[0]['gtotal7'] + $expenses[0]['gtotal8'] + $expenses[0]['gtotal9'] + $expenses[0]['gtotal10'] + $expenses[0]['gtotal11'] + $expenses[0]['gtotal12'];
                        ?>
                            <tr>
                                <td align="right"><strong><?php echo e(translate('Total Expense')); ?></strong></td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format($total_yearly_expenses, 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                            </tr>
                        <?php else: ?>
                        <?php
                            $total_yearly_expenses = $expenses[0]['gtotal7'] + $expenses[0]['gtotal8'] + $expenses[0]['gtotal9'] + $expenses[0]['gtotal10'] + $expenses[0]['gtotal11'] + $expenses[0]['gtotal12'] + $expenses[0]['gtotal1'] + $expenses[0]['gtotal2'] + $expenses[0]['gtotal3'] + $expenses[0]['gtotal4'] + $expenses[0]['gtotal5'] + $expenses[0]['gtotal6'];
                        ?>
                            <tr>
                                <td align="right"><strong><?php echo e(translate('Total Expense')); ?></strong></td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format($total_yearly_expenses, 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if($startmonth == 1): ?>
                        <?php
                            $total_yearly_net_profit = $yearly_gross_profit - $total_yearly_expenses;
                        ?>
                            <tr bgcolor="#E7E0EE">
                                <td align="right"><strong><?php echo e(translate('Net Amount')); ?></strong></td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(($total_yearly_net_profit), 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                            </tr> 
                        <?php else: ?>
                        <?php
                            $total_yearly_net_profit = $yearly_gross_profit - $total_yearly_expenses;
                        ?>
                            <tr bgcolor="#E7E0EE">
                                <td align="right"><strong><?php echo e(translate('Net Amount')); ?></strong></td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(($total_yearly_net_profit), 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo e(number_format(0, 2)); ?></strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <table border="0" width="100%" style="padding-top: 100px;">
                <tr>
                    <td align="left" class="noborder">
                        <div class="border-top"><?php echo e(translate('Prepared By')); ?></div>
                    </td>
                    <td align="center" class="noborder">
                        <div class="border-top"><?php echo e(translate('Checked By')); ?></div>
                    </td>
                    <td align="right" class="noborder">
                        <div class="border-top"><?php echo e(translate('Authorised By')); ?></div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\bazarnao\resources\views/backend/accounts/reports/income_statement/yearly_report.blade.php ENDPATH**/ ?>