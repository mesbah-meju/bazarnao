@extends('layout.admin_master')
@section('content') 

<section id="main-content">
    <section class="wrapper">
        <!-- page start-->
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <?php echo $title ?>
                    </header>
                    <div class="panel-body">
                        <form role="form" method="post" action="{{URL::to('/')}}/purchase-return" enctype="multipart/form-data">

                            <div class="form-group col-lg-4">
                                <label>Purchase Order#<span class="required">*</span></label>
                                <input class="form-control bill_search" name="bill_id" value="" placeholder="Please enter order id e.g. 1, 2, 3" autocomplete="off">
                                <div id='result'></div>
                                <div id='loader' class="none"></div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="none return_order_container">

                                <div class="form-group col-lg-3">
                                    <label>Date<span class="required">*</span></label>
                                    <input class="form-control datepicker1" name="date" value="{{date('Y-m-d')}}">
                                </div>


                                <div class="form-group col-lg-3">
                                    <label>Supplier</label>
                                    <select class="form-control supplier_id" name="supplier_id">
                                        <option value="">Select Supplier</option>
                                        <?php
                                        foreach ($data_temp as $value) {
                                            ?>
                                            <option value="{{{$value->supplier_id}}}">{{{$value->name}}}</option>
                                        <?php } ?>
                                    </select>
                                </div>


                                <div class="clearfix"></div>

                                <div class="form-group col-lg-4">
                                    <label>Remark</label>
                                    <textarea class="form-control" name="notes"></textarea>
                                </div>
                                <div class="clearfix"></div>

                                <div class="form-group col-lg-12">
                                    <table id="add_line" class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Code</th>
                                                <th>Title</th>
                                                <th>Qty</th>
                                                <th>Publisher Price</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody class="bill_item_container">

                                        </tbody>
                                    </table>

                                    <div class="form-group sub_container">
                                        <div class="pull-right text-right fwb">
                                            <div class="pb5">Total Qty: <span class="total_qty">0.00</span>
                                                <input type="hidden" class="total_qty" name="total_qty"></div>

                                            <div class="pb5">Total Value: <span class="total_value">0.00</span>
                                                <input type="hidden" class="total_value" name="total_value" value="0.00"></div>

                                            <div>Cash Return: <input class="form-control medium_text" name="cash_return" value="0.00"></div>
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
                                    <button type="submit" class="btn btn-primary" onclick="return validate_purchase_return_form()">Submit</button>
                                    <a href="{{URL::to('/')}}/purchase-return" class="btn btn-primary">Cancel</a>
                                </div>

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
                    <div class="form-group col-lg-6 pl0">
                        <label>Supplier Name<span class="required">*</span></label>
                        <input class="form-control" name="name" required>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="return insert_supplier()">Submit</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop