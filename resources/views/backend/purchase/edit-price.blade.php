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

                        <div class="form-group col-lg-12">

                            <div class="clearfix"></div>

                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Title</th>
                                        <th>Unit Sales Price(Tk)</th>
          
                                    </tr>
                                </thead>
                                <tbody class="purchase_item_container">
                                    <?php
                                    if ($temp_data) {
                                        foreach ($temp_data as $value) {
                                            ?>
                                            <tr>
                                                <td>{{{$value->code}}}</td>         
                                                <td>{{{$value->name}}}</td>        
                                                <td><input data-code="{{{$value->code}}}" value='{{{$value->sales_price}}}' class="update_sales_price"><span class="pull-right none text-success msg_{{{$value->code}}}">Updated</span></td>         
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>


                        </div>

                        <div class="clearfix"></div>
                        <br/>

                    </div>
                </section>
            </div>

        </div>

    </section>
</section>
@stop