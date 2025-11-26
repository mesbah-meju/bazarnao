@extends('layout.admin_master')
@section('content') 

<section id="main-content">
    <section class="wrapper">
        <!-- page start-->
        <div class="row">
            <div class="col-lg-12">
                <span class="pull-left" style="margin-left: 10px;margin-bottom: 10px;"><a href="{{URL::to('/')}}/purchase"><i class="fa fa-fast-backward"></i> Back</a></span>
                <span class="pull-right" style="margin-right: 10px;margin-bottom: 10px;">
                    <a href="{{URL::to('/')}}/purchase/create" class="mr20"><i class="fa fa-plus"></i> New Purchase</a>
                    <a href="#" onclick="printDiv('print_contents')"><i class="fa fa-print"></i> Print</a>
                </span>
            </div>
            <div class="clearfix"></div>
            <div class="col-lg-12" id="print_contents">
                <section class="panel">
                    <header class="panel-heading">
                        <img alt="avatar" src="{{URL::to('/')}}/public/upload/{{Helper::company('logo')}}" class="print_logo">
                        <h4 class="text-center fwb">{{Helper::company('name')}}</h4>
                        <h6 class="text-center">{{Helper::company('address')}} Phone: {{Helper::company('phone')}}</h6>

                        <table style="width: 100%; font-size: 13px;">
                            <tr>
                                <td>Supplier: {{{$data_row[0]->name}}}</td>
                                <td class="pull-right">{{ Helper::get_generated_bill_id($data_row[0]->bill_id) }}</td>
                            </tr>
                            <tr>
                                <td>{{{$data_row[0]->address}}}, {{{$data_row[0]->mobile}}}</td>
                                <td class="pull-right">Date: {{ date('d/m/Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <table id="add_line" class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Code</th>
                                        <th>Title</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">MRP</th>
                                        <th class="text-center">MRP Total</th>
                                        <th class="text-center">Discount</th>
                                        <th class="text-center">Discounted MRP</th>
                                        <th class="text-right">Unit Cost Price(Tk)</th>
                                        <th class="text-right">Amount(Tk)</th>
                                        <th class="text-right">Unit Sales Price(Tk)</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $total_qty = array();
                                    $total_mrp = array();
                                    $total_mrp_sum = array();
                                    $total_mrp_discounted = array();
                                    $amount = array();
                                    if ($data_item_rows) {

                                        foreach ($data_item_rows as $value) {
                                            ?>
                                            <tr id="item_row_1" class="global_item_row">
                                                <td>{{{$i++}}}</td>
                                                <td>{{{$value->code}}}</td>
                                                <td>{{{$value->name}}}</td>
                                                <td class="text-center">{{{$total_qty[] = $value->qty}}}</td>
                                                <td class="text-center">{{{$total_mrp[] = $value->pub_price}}}</td>
                                                <td class="text-center">{{{$total_mrp_sum[] = $value->qty*$value->pub_price}}}</td>
                                                <td class="text-center">{{{$value->discount}}}</td>
                                                <td class="text-center">{{{$total_mrp_discounted[] = ($value->qty*$value->pub_price) - (($value->qty*$value->pub_price)*$value->discount/100) }}}</td>
                                                <td class="text-right">{{{$value->cost_price}}}</td>
                                                <td class="text-right">{{{$amount[] = $value->amount}}}</td>
                                                <td class="text-right">{{{$value->sales_price}}}</td>
                                                
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td class="text-right fwb" colspan="3">Total:</td>
                                        <td class="fwb text-center">{{ array_sum($total_qty) }}</td>
                                        <td class="fwb text-center"></td>
                                        <td class="fwb text-center">{{ array_sum($total_mrp_sum) }}</td>
                                        <td class="fwb text-center"></td>
                                        <td class="fwb text-center">{{ $total_mrp_with_discount = array_sum($total_mrp_discounted) }}</td>
                                        <td></td>
                                        <td class="text-right">{{ array_sum($amount) }}</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="form-group sub_container">
                                <div class="pull-right text-right fwb">
                                    <table  class="table table-striped table-bordered table-hover">
                                        <tbody>
                                            <?php
                                            if($data_row[0]->other){ 
                                                ?>
                                            <tr>
                                                <td>Other(Tk):</td>
                                                <td class="text-right">{{ $data_row[0]->other }}</td>
                                            </tr>
                                            <?php } ?>
                                            <?php
                                            if($data_row[0]->vat){ 
                                                ?>
                                            <tr>
                                                <td>Vat({{ $data_row[0]->vat }}%):</td>
                                                <td class="text-right">{{ ($data_row[0]->vat*$total_mrp_with_discount)/100 }}</td>
                                            </tr>
                                            <?php } ?>
                                            <tr>
                                                <td>Total Amount(Tk):</td>
                                                <td class="text-right">{{ $data_row[0]->payable_to }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="clearfix"></div>
                            </div>

                        </div>

                    </div>
                </section>
            </div>

        </div>

    </section>
</section>
<!--main content end-->
@stop