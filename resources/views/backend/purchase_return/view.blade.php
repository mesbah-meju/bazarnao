@extends('layout.admin_master')
@section('content') 

<section id="main-content">
    <section class="wrapper">
        <!-- page start-->
        <div class="row">
            <div class="col-lg-12">
                <span class="pull-left" style="margin-left: 10px;margin-bottom: 10px;"><a href="{{URL::to('/')}}/purchase-return"><i class="fa fa-fast-backward"></i> Back</a></span>
                <span class="pull-right" style="margin-right: 10px;margin-bottom: 10px;">
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
                                <td class="pull-right">Return#: {{ $data_row[0]->bill_return_id }}</td>
                            </tr>
                            <tr>
                                <td>Date: {{ Helper::get_formated_date($data_row[0]->date) }}</td>
                                <td class="pull-right">Print Date: {{ date('d/m/Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </header>
                    <div class="panel-body">
                        <div class="form-group">
                            <table id="add_line" class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Title</th>
                                        <th>Qty</th>
                                        <th>Publisher Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($data_item_rows) {

                                        foreach ($data_item_rows as $value) {
                                            ?>
                                            <tr id="item_row_1" class="global_item_row">
                                                <td>{{{$value->code}}}</td>
                                                <td>{{{$value->name}}}</td>
                                                <td>{{{$value->return_qty}}}</td>
                                                <td>{{{$value->pub_price}}}</td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <div class="form-group sub_container">
                                <div class="pull-right text-right fwb">
                                    <table  class="table table-striped table-bordered table-hover">
                                        <tbody>
                                            <tr>
                                                <td>Total Qty:</td>
                                                <td>{{ $data_row[0]->total_qty }}</td>
                                            </tr>
                                            <tr>
                                                <td>Total Value:</td>
                                                <td>{{ $data_row[0]->total_value }}</td>
                                            </tr>
                                            <tr>
                                                <td>Cash Return:</td>
                                                <td>{{ $data_row[0]->cash_return }}</td>
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