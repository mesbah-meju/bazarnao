@extends('layout.admin_master')
@section('content') 

<section id="main-content">
    <section class="wrapper">
        <!-- page start-->
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <form role="form" method="post" action="{{URL::to('/')}}/purchase" enctype="multipart/form-data">
                        <header class="panel-heading">
                            <?php echo $title ?>
                            <div class="pull-right">
                                <button type="submit" class="btn btn-primary" onclick="return validate_purchase_form()">Submit</button>
                                <a href="{{URL::to('/')}}/purchase" class="btn btn-default">Cancel</a>
                            </div>
                        </header>
                        <div class="panel-body">



                            <div class="form-group col-lg-3">
                                <label>Date<span class="required">*</span></label>
                                <input class="form-control datepicker1" name="date" value="{{date('Y-m-d')}}">
                            </div>


                            <div class="form-group col-lg-3">
                                <label>Supplier<span class="required">*</span></label>
                                <select class="form-control supplier_id" name="supplier_id" required>
                                    <option value="">Select Supplier</option>
                                    <?php
                                    if ($data_temp) {
                                        foreach ($data_temp as $value) {
                                            ?>
                                            <option value="{{{$value->supplier_id}}}">{{{$value->name}}}</option>
                                        <?php }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group col-lg-3">
                                <button type="button" class="btn btn-success mt23" onclick="return add_supplier()"><b>+</b> New Supplier</button>
                            </div>

                            <div class="clearfix"></div>

                            <div class="form-group col-lg-12">

                                <a href="javascript:;" onclick="add_line_purchase()" class="btn mb5 btn-xs btn-success">
                                    <i class="fa fa-plus fa-fw"></i> Add Item</a>

                                <div class="clearfix"></div>

                                <table id="add_line" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Title</th>
                                            <th>Qty</th>
                                            <th>Unit Cost Price</th>
                                            <th>Discount(%)</th>
                                            <th>Amount</th>
                                            <th>Unit Sales Price</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody class="purchase_item_container">
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="5" style="text-align: right;">Sub-Total : </td>
                                        <td id="sub_total"></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    </tfoot>
                                </table>

                                <div class="form-group sub_container">

                                    <div class="pull-right text-right fwb">

                                        <table class="invoice_calculation">
                                            <tr>
                                                <td>
                                                    Pay Mode:
                                                    <div>
                                                        <select class="form-control small_select" name="paidLedgerId">
                                                            <?php
                                                            if ($acc_asset_ledger) {
                                                                foreach ($acc_asset_ledger as $value) {
                                                                    ?>
                                                                    <option value="{{$value->ledger_id}}">{{$value->ledger_name}}[{{$value->ledger_id}}]</option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    Other:
                                                    <div>
                                                        <input type="text" class="form-control purchase_other medium_text text-right" name="other" value="0">
                                                    </div>
                                                </td>
                                                <td>
                                                    Vat(%):
                                                    <div>
                                                        <input type="text" class="form-control purchase_vat medium_text text-right" name="vat" value="0">
                                                    </div>
                                                </td>
                                                <td>Payable To:
                                                    <div>
                                                        <span class="payable_to">0.00</span>
                                                        <input type="hidden" class="payable_to" name="payable_to">
                                                        <input type="hidden" class="payable_to_static" name="payable_to_static">
                                                    </div>
                                                </td>
                                                <td>Total Paid:
                                                    <div>
                                                        <input class="form-control medium_text text-right total_paid" name="total_paid"></div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>

                            </div>
                            <input type="hidden" id="last_max" value="1">
                            <input type="hidden" id="removed" value="0">
                            <input type="hidden" name="sub_total_input" id="sub_total_input"value="">
                            <div class="clearfix"></div>

                            <div class="clearfix"></div>
                            <br/>


                            <div class="form-group col-lg-5">
                                <button type="submit" class="btn btn-primary" onclick="return validate_purchase_form()">Submit</button>
                                <a href="{{URL::to('/')}}/purchase" class="btn btn-default">Cancel</a>
                            </div>
                    </form>

            </div>
    </section>
</div>

</div>

</section>
</section>
<!--main content end-->
<div class="modal fade add_supplier_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form role="form" method="post" action="{{URL::to('/')}}/add-supplier" enctype="multipart/form-data">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title mmt" id="myModaltitle">Add Supplier</h4>
                </div>
                <div class="modal-body mmb">
                    <div class="form-group col-lg-4">
                        <label>Supplier Name<span class="required">*</span></label>
                        <input class="form-control" name="name" required>
                    </div>
                    <div class="form-group col-lg-4">
                        <label>Contact Person</label>
                        <input class="form-control" name="contact_person">
                    </div>

                    <div class="form-group col-lg-4">
                        <label>Address</label>
                        <input class="form-control" name="address">
                    </div>

                    <div class="form-group col-lg-4">
                        <label>Phone</label>
                        <input class="form-control" name="phone">
                    </div>

                    <div class="form-group col-lg-4">
                        <label>Mobile<span class="required">*</span></label>
                        <input class="form-control" name="mobile">
                    </div>

                    <div class="form-group col-lg-4">
                        <label>Email</label>
                        <input class="form-control" name="email" type="email">
                    </div>

                    <div class="form-group col-lg-4">
                        <label>Group</label>
                        <div class="input-group">
                            <select class="form-control" name="supplier_group_id">
                                <option value="">Select Group</option>
                                <?php
                                foreach ($temp_data as $value) {
                                    ?>
                                    <option value="{{{$value->supplier_group_id}}}">{{{$value->name}}}</option>
<?php } ?>
                            </select>
                            <span class="input-group-addon"><a href="javascript:create_supplier_group()">Add New</a></span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="return insert_supplier()">Add Supplier</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade add_purchase_item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form role="form" method="post" action="#" id="reset_form" enctype="multipart/form-data">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title mmt" id="myModaltitle">Add Purchase Item</h4>
                </div>
                <div class="modal-body mmb">

                    <div class="form-group col-lg-3 pl0">
                        <label>Code<span class="required">*</span></label>
                        <input class="form-control reset" name="add_code" required onkeyup="get_suggested_item()" placeholder="Item code/name">
                        <div id="result"></div>
                        <div id="loader" class="none"></div>
                    </div>

                    <div class="form-group col-lg-3 pl0">
                        <label>Title</label>
                        <input class="form-control reset" name="add_title">
                    </div>

                    <div class="form-group col-lg-2 pl0">
                        <label>Model</label>
                        <input class="form-control reset" name="add_model">
                    </div>

                    <div class="form-group col-lg-2 pl0">
                        <label>Qty<span class="required">*</span></label>
                        <input class="form-control reset" name="add_copies" required>
                    </div>

                    <div class="form-group col-lg-2 pl0">
                        <label>Unit Price<span class="required">*</span></label>
                        <input class="form-control reset" name="add_unit_price" required onkeyup="calculate_sales_price_purchase()">
                    </div>

                    <div class="form-group col-lg-2 pl0">
                        <label>Discount(%)</label>
                        <input class="form-control reset" name="add_discount" required onkeyup="calculate_sales_price_purchase()" value="0">
                    </div>

                    <div class="form-group col-lg-2 pl0">
                        <label>Currency<span class="required">*</span></label>
                        <select required name="currency" class="form-control" onchange="change_currency()">
                            <?php
                            if ($cur_data) {
                                foreach ($cur_data as $value) {
                                    ?>
                                    <option value="{{{$value->currency_id}}}" data-value="{{{$value->in_taka}}}" <?php if ($value->currency_id == 4) echo 'selected'; ?>>{{{$value->title}}}</option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group col-lg-2 pl0">
                        <label>Unit Price(Tk)<span class="required">*</span></label>
                        <input class="form-control reset" name="add_cost_price" readonly="">
                    </div>

                    <div class="form-group col-lg-2 pl0">
                        <label>Amount<span class="required">*</span></label>
                        <input class="form-control reset" name="amount" required readonly="">
                    </div>
                    <div class="form-group col-lg-2 pl0">
                        <label>Rate</label>
                        <input class="form-control reset" name="profit_rate" required value="1" onkeyup="calculate_sales_price_purchase()">
                    </div>
                    <div class="form-group col-lg-2 pl0">
                        <label>Unit Sales Price<span class="required">*</span></label>
                        <input class="form-control reset" name="add_sales_price" required>
                    </div>
                    <div class="clearfix"></div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="add_item_to_purchase()">Ok</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade add_supplier_group" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" >
        <div class="modal-content" style="height: 330px">
            <form role="form" method="post" action="#" enctype="multipart/form-data">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title mmt" id="myModaltitle">Add Group</h4>
                </div>
                <div class="modal-body mmb">
                    <div class="form-group col-lg-6 pl0">
                        <label>Group Name<span class="required">*</span></label>
                        <input class="form-control" name="group_name" required>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="return insert_supplier_group()">Add Group</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop