@extends('layout.admin_master')
@section('content') 
<?php
$user_prev = Session::get('user_prev');
?>
<!--main content start-->
<section id="main-content">
    <section class="wrapper site-min-height">
        <!-- page start-->
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <?php echo $title ?>
                            <a class="btn btn-default pull-right ml10" href="{{URL::to('/')}}/purchase-return/export"><i class="fa fa-file-o"></i> Export</a>
                        <?php if (in_array(56, $user_prev)) { ?>
                            <a class="btn btn-success pull-right" href="{{URL::to('/')}}/purchase-return/create"><b>+</b> Add New</a>
                        <?php } ?>
                        <div class="clearfix"></div>
                    </header>

                    <div class="panel-body">
                        @if (Session::has('message'))
                        <div class="alert alert-{{ Session::get('message_type') }}">{{ Session::get('message') }}</div>
                        @endif

                        <div class="adv-table">
                            <table  class="display table table-bordered table-striped" id="example1">
                                <thead>
                                    <tr>
                                        <th>Return#</th>
                                        <th>Date</th>
                                        <th>Supplier</th>
                                        <th>Qty</th>
                                        <th>Total Value</th>
                                        <th>Cash Return</th>

                                        <?php if ((in_array(57, $user_prev)) or ( in_array(58, $user_prev))) { ?>
                                            <th class="text-center">Action</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php for ($i = 0; $i < count($data); $i++) { ?>
                                        <tr>
                                            <td>{{{$data[$i][0]}}}</td>
                                            <td>{{{Helper::get_formated_date($data[$i][1])}}}</td>

                                            <td>{{{$data[$i][2]}}}</td>
                                            <td>{{{$data[$i][3]}}}</td>
                                            <td>{{{$data[$i][4]}}}</td>
                                            <td>{{{$data[$i][5]}}}</td>

                                            <?php if ((in_array(57, $user_prev)) or ( in_array(58, $user_prev))) { ?>
                                                <td class="center">
                                                    <div class="btn-group">
                                                        <button class="btn btn-default btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                                                            Action
                                                            <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu pull-right" role="menu">
                                                            <?php if (in_array(57, $user_prev)) { ?>
                                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="{{URL::to('/')}}/purchase-return/<?php echo $data[$i][0]; ?>">View</a></li>
                                                            <?php } ?>
                                                            <?php if (in_array(57, $user_prev)) { ?>
<!--                                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="{{URL::to('/')}}/purchase-return/pdf/<?php echo $data[$i][0]; ?>" target="_blank">Create PDF</a></li>-->
                                                            <?php } ?>

                                                            <?php if (in_array(58, $user_prev)) { ?>
                                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:delete_confirm('{{URL::to('/')}}/purchase-return/<?php echo $data[$i][0]; ?>', 'Purchase Return')">Delete</a></li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>   

                                                </td>
                                            <?php } ?>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <!-- page end-->
    </section>
</section>
<!--main content end-->
@stop