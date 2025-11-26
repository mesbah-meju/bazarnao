@extends('backend.layouts.staff')
<style>
    tr,
    th,
    td {
        padding: 3px !important;
    }

    th {
        background: #AE3C86;
        color: #fff;
        font-weight: bold
    }

    li.nav-item {
        width: 90%;
    }

    .navbar-nav {
        width: 100%;
    }
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/2.4.85/css/materialdesignicons.css"
    rel="stylesheet" />

@section('content')
    <div class="row gutters-10">
        <div class="col-lg-12">

            <div id="accordion">
                @include('backend.staff_panel.purchase_executive.purchase_executive_nav')
                <div class="card border-bottom-0">
                    <a class="mt-3" href="{{ route('add_transfer.index') }}"><button class="btn btn-info float-right">Add
                            Transfer</button></a>
                    <div class="row card-body">
                        <form id="culexpo" action="{{ route('transfer_list.index') }}" method="GET">
                            <div class="form-group row">

                                <div class="col-md-3">
                                    <label>Start Date :</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ $start_date }}">

                                </div>
                                <div class="col-md-3">
                                    <label>End Date :</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ $end_date }}">

                                </div>

                                <div class="col-md-3 mt-4">
                                    <button class="btn btn-primary"
                                        onclick="submitForm ('{{ route('transfer_list.index') }}')">{{ translate('Filter') }}</button>
                                </div>
                            </div>
                        </form>
                        <table class="table aiz-table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th data-breakpoints="md">{{ translate('Product') }}</th>
                                    <th data-breakpoints="md">{{ translate('From wearhouse') }}</th>
                                    <th data-breakpoints="md">{{ translate('To wearhouse') }}</th>
                                    <th data-breakpoints="md">{{ translate('Qty') }}</th>
                                    <th data-breakpoints="md">{{ translate('Unit Price') }}</th>
                                    <th data-breakpoints="md">{{ translate('date') }}</th>
                                    <th data-breakpoints="md">{{ translate('Status') }}</th>
                                    <th class="text-right" width="18%">{{ translate('options') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transfer as $key => $order)
                                    <tr>
                                        <td>
                                            {{ $key + 1 }}
                                        </td>
                                        <td>
                                            {{ $order->product->name }}
                                        </td>
                                        <td>
                                            {{ getWearhouseName($order->from_wearhouse_id) }}
                                        </td>
                                        <td>
                                            {{ getWearhouseName($order->to_wearhouse_id) }}
                                        </td>
                                        <td>
                                            {{ $order->qty }}
                                        </td>

                                        <td>
                                            {{ $order->unit_price }}
                                        </td>

                                        <td>
                                            {{ $order->date }}
                                        </td>
                                        <td>
                                            {{ $order->status }}
                                        </td>


                                        <td class="text-right">
                                            @if ($order->status == 'Pending')
                                                <a href="{{ route('transfer.edit', $order->id) }}"
                                                    class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                                    title="{{ translate('Edit') }}">
                                                    <i class="las la-edit"></i>
                                                </a>
                                                <a href="#"
                                                    class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                                    data-href="{{ route('transfer.destroy', $order->id) }}"
                                                    title="{{ translate('Cancel') }}">
                                                    <i class="las la-trash"></i>
                                                </a>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>

                </div>
            </div>
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
