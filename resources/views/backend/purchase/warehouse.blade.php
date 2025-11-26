@extends('layout.admin_master')
@section('content') 

<section id="main-content">
    <section class="wrapper">
        <!-- page start-->
        <div class="row">
            <div class="col-lg-12">
                      
                <span class="pull-right" style="margin-right: 10px;"><a href="{{URL::to('/')}}/purchase"><i class="fa fa-fast-backward"></i> Back</a></span>

        
                <section class="panel">
                    <header class="panel-heading">
                        <?php echo $title ?>
                    </header>
                    <div class="panel-body">
                        {{ Form::open(array('route' => array('purchase.update', $data_row[0]->bill_id), 'method' => 'PUT')) }}

                            <input type="hidden" value="<?php echo $data_row[0]->bill_id; ?>" name="id">
                            
                            <div class="clearfix"></div>

                            <div class="form-group col-lg-12">


                                <div class="clearfix"></div>

                                <table id="add_line" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Title</th>
                                            <th>Qty</th>
                                            <th>Set To Warehouse</th>
                                        </tr>
                                    </thead>
                                    <tbody class="purchase_item_container">
                                        <?php
                                        $total_qty = array();
                                        if ($data_item_rows) {
                                            $i = 0;
                                            foreach ($data_item_rows as $value) {
                                                $i++;
                                                ?>
                                                <tr id="item_row_{{$i}}" class="global_item_row">
                  
                                                    <td>
                                                        <input class="form-control" id="isbn_{{$i}}" name="isbn[]" value='{{{$value->code}}}' disabled="">
                        
                                                        </td>         
                                                    <td><input class="form-control" id="title_{{$i}}" name="title[]" value='{{{$value->name}}}' disabled=""></td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input class="form-control numbersOnly qty_{{$i}}" id="qty_{{$i}}" name="qty[]" value='{{$value->qty}}' disabled="">
                                                        </div>
                                                    </td>         

                                                    <td>
                                                        <?php
                                                        if ($warehouse) {
                                                            foreach ($warehouse as $wvalue) {
                                                                ?>
                                                                <div class="form-group col-lg-6">
                                                                    <label>{{{$wvalue->name}}}</label>
                                                                    <div class="input-group">
                                                                    <input class="form-control warehouse_qty_{{$i}} wh_qty_{{$i}}_{{$wvalue->warehouse_id}}"  value="{{Helper::get_warehouse_assigned_qty($value->bill_id, $wvalue->warehouse_id, $value->code)}}">
                                                                    <span class="input-group-addon pointer" id="basic-addon1" onclick="assign_to_warehouse({{$value->bill_id}}, {{$wvalue->warehouse_id}}, '{{$value->code}}', {{$i}})">Set</span>
                                                                    </div>
                                                                    </div>

                                                            <?php }
                                                        } ?>

                                                    </td>         
     

                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>



                            </div>

                        </form>

                    </div>
                </section>
            </div>

        </div>

    </section>
</section>

@stop