@extends('backend.layouts.app')

@section('content')
    @php
        $refund_request_addon = App\Models\Addon::where('unique_identifier', 'refund_request')->first();
        $user_name = auth()->user()->name;
    @endphp

    <?php
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
    ?>
    <div class="card">
        <form class="" action="" method="GET">
            <div class="card-header row gutters-5">
                <div class="col-lg-2 ml-auto">
                    <select class="form-control aiz-selectpicker" name="customer_type" id="customer_type">
                        <option value="">{{ translate('Filter by Customer Type') }}</option>
                        <option value="Normal" @if ($customer_type == 'Normal') selected @endif>{{ translate('Normal') }}
                        </option>
                        <option value="Premium" @if ($customer_type == 'Premium') selected @endif>{{ translate('Premium') }}
                        </option>
                        <option value="Corporate" @if ($customer_type == 'Corporate') selected @endif>
                            {{ translate('Corporate') }}</option>
                        <option value="Employee" @if ($customer_type == 'Employee') selected @endif>
                            {{ translate('Employee') }}</option>
                        <option value="Retail" @if ($customer_type == 'Retail') selected @endif>{{ translate('Retail') }}
                        </option>
                        <option value="Website" @if ($customer_type == 'Website') selected @endif>{{ translate('Website') }}
                        </option>
                    </select>
                </div>
                <div class="col-lg-2 ml-auto">
                    <select class="form-control aiz-selectpicker" name="order_from[]" id="order_from" multiple>
                        <option value="">{{ translate('Filter by Order From') }}</option>
                        <option value="Web" @if (in_array('Web', (array) $order_from)) selected @endif>{{ translate('Web') }}
                        </option>
                        <option value="App" @if (in_array('App', (array) $order_from)) selected @endif>{{ translate('App') }}
                        </option>
                        <option value="POS" @if (in_array('POS', (array) $order_from)) selected @endif>{{ translate('POS') }}
                        </option>
                        <option value="IOS" @if (in_array('IOS', (array) $order_from)) selected @endif>{{ translate('IOS') }}
                        </option>
                    </select>
                </div>
                <div class="col-lg-2 ml-auto">
                    <select class="form-control aiz-selectpicker" name="delivery_status[]" id="delivery_status" multiple>
                        <option value="">{{ translate('Filter by Delivery Status') }}</option>
                        <option value="pending" @if (in_array('pending', (array) $delivery_status)) selected @endif>
                            {{ translate('Pending') }}</option>
                        <option value="confirmed" @if (in_array('confirmed', (array) $delivery_status)) selected @endif>
                            {{ translate('Confirmed') }}</option>
                        <option value="on_delivery" @if (in_array('on_delivery', (array) $delivery_status)) selected @endif>
                            {{ translate('On delivery') }}</option>
                        <option value="delivered" @if (in_array('delivered', (array) $delivery_status)) selected @endif>
                            {{ translate('Delivered') }}</option>
                        <option value="Cancel" @if (in_array('Cancel', (array) $delivery_status)) selected @endif>
                            {{ translate('Canceled') }}</option>
                    </select>
                </div>


                <div class="col-lg-2 ml-auto">
                    <select class="form-control aiz-selectpicker" name="payment_status" id="payment_status">
                        <option value="">{{ translate('Filter by Payment Status') }}</option>
                        <option value="paid" @if ($payment_status == 'paid') selected @endif>{{ translate('Paid') }}
                        </option>
                        <option value="unpaid" @if ($payment_status == 'unpaid') selected @endif>
                            {{ translate('Un-Paid') }}</option>
                    </select>
                </div>
                @if(Auth::user()->user_type == 'admin' || $user_name == 'Account Department')   
                <div class="col-md-2 ml-auto">
                    <select id="warehouse" class="form-control aiz-selectpicker" name="warehouse">
                        <option value=''>Filter by Warehouse</option>
                        @foreach (\App\Models\Warehouse::all() as $key => $warehous)
                            <option @php if($warehouse ==$warehous->id) echo 'selected'; @endphp
                                value="{{ $warehous->id }}">{{ $warehous->name }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <div class="col-md-2 ml-auto">
                    <select id="warehouse" class="form-control aiz-selectpicker" name="warehouse">
                        <option value=''>Filter by Warehouse</option>
                        @foreach ($warehouses as $key => $warehous)
                            <option @php if($warehouse ==$warehous->id) echo 'selected'; @endphp
                                value="{{ $warehous->id }}">{{ $warehous->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-lg-2">
                    <div class="form-group mb-0">
                        <input type="text" class="aiz-date-range form-control" value="{{ $date }}"
                            name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y"
                            data-separator=" to " data-advanced-range="true" autocomplete="off">
                    </div>
                </div>

                <div class="col-lg-2">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control" id="search"
                            name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset
                            placeholder="{{ translate('Type Order code & hit Enter') }}">
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                    </div>
                </div>
            </div>
        </form>
        <div class="card-header row gutters-5">
            <div class="col text-center">
                <h5 class="mb-md-0 h6">{{ translate('All Orders') }}</h5>
            </div>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Date') }}</th>
                        <th>{{ translate('Order Code') }}</th>
                        <th data-breakpoints="md">{{ translate('Customer ID') }}</th>
                        <th data-breakpoints="md">{{ translate('Customer') }}</th>
                        <th data-breakpoints="md">{{ translate('Order From') }}</th>
                        <th data-breakpoints="md">{{ translate('Amount') }}</th>
                        <th data-breakpoints="md">{{ translate('Delivery Status') }}</th>
                        <th data-breakpoints="md">{{ translate('Status') }}</th>
                        <th data-breakpoints="lg">warehouse</th>
                        <th data-breakpoints="lg">Status Change Date</th>
                        @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                            <!-- <th>{{ translate('Refund') }}</th> -->
                        @endif
                        <th class="text-right" width="18%">{{ translate('options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $key => $order)
                        @php
                            $status = $order->delivery_status;

                        @endphp

                        @php
                            if (!empty(App\Models\Customer::where('user_id', $order->user_id)->first())) {
                                $customer_id = \App\Models\Customer::where('user_id', $order->user_id)->first()
                                    ->customer_id;
                            } else {
                                $customer_id = '';
                            }
                        @endphp
                        <tr>

                            <td>{{ $key + 1 + ($orders->currentPage() - 1) * $orders->perPage() }}</td>
                            <td>
                                {{ date('d-m-Y', $order->date) }}
                            </td>
                            <td>
                                <a href="{{ route('all_orders.show', encrypt($order->id)) }}" target="_blank"
                                    title="{{ translate('View') }}">{{ $order->code }}</a>
                            </td>
                            <td>
                                @if ($order->user != null)
                                    <a href="{{ route('customer_ledger.index') }}?user_id={{ $order->user_id }}"
                                        target="_blank" title="{{ translate('View') }}">{{ $customer_id }} </a>
                                @else
                                    {{ $order->guest_id }}
                                @endif
                            </td>
                            <td>
                                @if ($order->user != null)
                                    {{ $order->user->name }}
                                @else
                                    Guest
                                @endif
                            </td>
                            <td>

                                {{ $order->order_from }}

                            </td>
                            <td>
                                {{ single_price($order->grand_total) }}
                            </td>
                            <td>
                                {{ translate($status) }}

                            </td>
                            <td>
                                @if ($order->payment_status == 'paid')
                                    <span class="badge badge-inline badge-success">{{ translate('Paid') }}</span>
                                @else
                                    <span class="badge badge-inline badge-danger">{{ translate('Unpaid') }}</span>
                                @endif
                            </td>
                            <td>
                                @if (!empty($order->warehouse))
                                    {{ getWearhouseName($order->warehouse) }}
                                @endif
                            </td>
                            <td>
                                <span style="font-size:11px;color:green">
                                    Confirm Date : @if (!empty($order->confirm_date))
                                        {{ date('d/m/Y h:i:s A', strtotime($order->confirm_date)) }}
                                    @endif
                                    </br>
                                    Confirmed By: @if (!empty($order->confirmed_by))
                                        {{ getUserNameByuserID($order->confirmed_by) }}
                                    @endif 
                                    <br>
                                    On Delivery Date : @if (!empty($order->on_delivery_date))
                                        {{ date('d/m/Y h:i:s A', strtotime($order->on_delivery_date)) }}
                                    @endif 
                                    <br>
                                    On delivery By: @if (!empty($order->on_delivery_by))
                                        {{ getUserNameByuserID($order->on_delivery_by) }}
                                    @endif 
                                    <br>
                                    Delivered Date : @if (!empty($order->delivered_date))
                                        {{ date('d/m/Y h:i:s A', strtotime($order->delivered_date)) }}
                                    @endif 
                                    <br>
                                    Delivered By: @if (!empty($order->delivered_by))
                                        {{ getUserNameByuserID($order->delivered_by) }}
                                    @endif 
                                    <br>
                                    Cancel Date : @if (!empty($order->cancel_date))
                                        {{ date('d/m/Y h:i:s A', strtotime($order->cancel_date)) }}
                                    @endif
                                    <br>
                                    Cancel Reason: @if (!empty($order->reason_of_cancel))
                                        {{ $order->reason_of_cancel }}
                                    @endif 
                                    <br>
                                    Canceled By: @if (!empty($order->canceled_by))
                                        {{ getUserNameByuserID($order->canceled_by) }}
                                    @endif
                                </span>
                            </td>
                            @if ($refund_request_addon != null && $refund_request_addon->activated == 1)
                                <!-- <td>
                                        @if (count($order->refund_requests) > 0)
    {{ count($order->refund_requests) }} {{ translate('Refund') }}
@else
    {{ translate('No Refund') }}
    @endif
                                    </td> -->
                            @endif
                            @if ($user_name == 'Delivery Department' || $user_name == 'Operational Department')
                                <td class="text-right">
                                    <!-- <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Edit') }}">
                                           <i class="las la-edit"></i>
                                       </a> -->
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                        href="{{ route('all_orders.show', encrypt($order->id)) }}"
                                        title="{{ translate('View') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                        href="{{ route('invoice.download', $order->id) }}"
                                        title="{{ translate('Download Invoice') }}">
                                        <i class="las la-download"></i>
                                    </a>

                                </td>
                            @else
                                <td class="text-right">
                                    @if ($status == 'pending' || $status == 'confirmed' || $status == 'on_delivery')
                                        <a href="{{ route('orders.edit', $order->id) }}"
                                            class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                            title="{{ translate('Edit') }}">
                                            <i class="las la-edit"></i>
                                        </a>
                                    @endif
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                        href="{{ route('all_orders.show', encrypt($order->id)) }}"
                                        title="{{ translate('View') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                        href="{{ route('invoice.download', $order->id) }}"
                                        title="{{ translate('Download Invoice') }}">
                                        <i class="las la-download"></i>
                                    </a>
                                    @if ($status == 'confirmed' && ($order->order_from == 'Web' || $order->order_from == 'App'))
                                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                            href="{{ route('scan-online-order') }}" title="{{ translate('Scan') }}">
                                            <i class="las la-barcode"></i>
                                        </a>
                                        <a href="#"
                                            class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                            data-href="{{ route('orders.destroy', $order->id) }}"
                                            title="{{ translate('Delete') }}">
                                            <i class="las la-trash"></i>
                                        </a>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $orders->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript"></script>
@endsection
