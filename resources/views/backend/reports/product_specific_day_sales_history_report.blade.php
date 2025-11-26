@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class=" align-items-center">
            <h1 class="h3">{{ translate('Product Wise Daily Sales History Report') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-sm btn-info mx-2" onclick="printDiv()"
                        type="button">{{ translate('Print') }}</button>
                    
                    <div class="printArea">
                        <style>
                            th {
                                text-align: center;
                            }
                        </style>
                        <h3 style="text-align:center;">{{ translate('Product Wise Daily Sales History Report') }} <span  style="font-size: 16px">({{$start_date}} to {{$end_date}})</span></h3>
                        <table class="table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width:5%">SL</th>
                                    <th style="width:20%">{{ translate('Product Name') }}</th>
                                    <th style="width:10%">{{ translate('Category') }}</th>
                                    <th style="width:10%">{{ translate('Delivery Date') }}</th>
                                    <th style="width:10%">{{ translate('Month-year') }}</th>
                                    <th style="width:10%">{{ translate('Order Code') }}</th>
                                    <th style="width:5%">{{ translate('Num of Sales') }}</th>
                                    <th style="width:10%">{{ translate('Amount') }}</th>
                                    <th style="width:10%">{{ translate('Average Amount') }}</th>

                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total = 0;
                                    $qty = 1;
                                    $order_qty = 1;
                                    $total_profit = 0;
                                    $total_qty = 0;
                                @endphp
                                @foreach ($products as $key => $product)
                                    @php
                                        $total_qty = $total_qty + $product->order_qty;
                                        $total = $total + $product->price;
                                        $total_profit =
                                            $total_profit +
                                            ($product->price - $product->purchase_price * $product->quantity);
                                    @endphp

                                    <?php if (!empty($product->quantity)) {
                                        $qty = $product->quantity;
                                    } else {
                                        $qty = 1;
                                    }
                                    ?>
                                    <?php if (!empty($product->order_qty)) {
                                        $order_qty = $product->order_qty;
                                    } else {
                                        $order_qty = 1;
                                    }
                                    ?>
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $product->getTranslation('product_name') }}</td>
                                        <td>{{ $product->getTranslation('category_name') }}</td>
                                        <td>{{ $product->delivered_date ? date('Y-m-d', strtotime($product->delivered_date)) : 'undefined' }}
                                        </td>
                                        <td>{{ $product->delivered_date ? date('F Y', strtotime($product->delivered_date)) : 'undefined' }}
                                        </td>
                                        <td style="text-align:center;">
                                            <a href="{{ route('all_orders.show', encrypt($product->orderId)) }}"
                                                target="_blank" title="{{ translate('View') }}">{{ $product->code }}</a>
                                        </td>

                                       
                                        <td style="text-align:center;">{{ $product->order_qty }}</td>
                                        <td style="text-align:right;">{{ single_price($product->sale_price) }}</td>
                                        <td style="text-align:right;">
                                            {{ single_price($product->getTranslation('sale_price') / $product->order_qty) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td style="text-align:right;" colspan="6"><b>Total</b></td>
                                    <td style="text-align:center;" colspan="1"><b>{{ $total_qty }}</b></td>
                                    <td style="text-align:right;"><b>{{ single_price($total) }}</b></td>
                                  
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function submitForm(url) {
            $('#prowasales').attr('action', url);
            $('#prowasales').submit();
        }
    </script>

@endsection
