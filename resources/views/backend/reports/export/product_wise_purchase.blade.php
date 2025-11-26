<table class="table aiz-table mb-0" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ translate('Purchase Date') }}</th>
                                    <th>{{ translate('Purchase No') }}</th>
                                    <th>{{ translate('Product Name') }}</th>
                                    <th>{{ translate('Supplier') }}</th>
                                    <th>{{ translate('QTY') }}</th>
                                    <th>{{ translate('Price') }}</th>
                                    <th>{{ translate('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $totalamount = 0;
                                $totalqty = 0;
                                $totalprice = 0;
                                @endphp
                                @foreach ($product_wise_purchase_list as $key => $order)

                                @php
                                $totalamount += $order->qty*$order->price;
                                $totalprice += $order->price;
                                $totalqty += $order->qty;
                                @endphp
                                <tr>
                                    <td>
                                        {{ ($key+1) }}
                                    </td>
                                    <td>
                                        {{ date('d-m-Y',strtotime($order->date)) }}
                                    </td>

                                    <td>
                                        {{ $order->purchase_no }}
                                    </td>

                                    <td>
                                        {{ $order->name }}
                                    </td>
                                    <td>
                                        {{ $order->suppliername }}
                                    </td>

                                    <td style="text-align:right;">
                                        {{$order->qty }}
                                    </td>

                                    <td style="text-align:right;">
                                        {{$order->price }}
                                    </td>
                                    <td style="text-align:right;">
                                        {{$order->qty*$order->price }}
                                    </td>
                                </tr>
                                @endforeach
                                <tr style="font-weight:bold;text-align:right;">
                                    <td colspan="4">Total</td>
                                    <td>{{$totalqty}}</td>
                                    <td>{{$totalprice}}</td>
                                    <td>{{$totalamount}}</td>
                                </tr>
                            </tbody>
                        </table>