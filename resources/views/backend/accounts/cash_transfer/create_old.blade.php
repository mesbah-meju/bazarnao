


@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Add Cash Transfer')}}</h5>
            </div>
            <?php
                $warehousearray = getWearhouseBuUserId(auth()->user()->id);
                $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
            ?>

            <form class="form-horizontal" action="{{ route('cash-transfers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="txtVNo" class="form-label">{{ translate('Voucher Type') }} <span class="text-danger">*</span></label>
                            <input type="text" name="txtVNo" id="txtVNo" value="Cash Transfer" class="form-control" readonly required/>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="ac" class="form-label">{{ translate('Account Head') }} <span class="text-danger">*</span></label>
                            <select name="cmbCredit" id="cmbCredit" class="form-control aiz-selectpicker" data-live-search="true" required>
                                @foreach ($crcc as $cracc)
                                <option value="{{ $cracc->head_code }}" data-isbank="{{ $cracc->is_bank_nature }}">{{ $cracc->head_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if(Auth::user()->user_type == 'admin')
                        <div class="form-group col-md-6">
                            <label for="from_warehouse" class="form-label">{{translate('From Warehouse')}} <span class="text-danger">*</span></label>
                            <select name="from_warehouse" id="from_warehouse" class="form-control aiz-selectpicker" data-live-search="true" required>
                                <option value="">Select a warehouse</option>
                                @foreach (\App\Models\Warehouse::all() as $key => $warehouse)
                                <option  value="{{ $warehouse->id }}">{{ $warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <div class="form-group col-md-6">
                            <label for="from_warehouse" class="form-label">{{translate('From Warehouse')}} <span class="text-danger">*</span></label>
                            <select name="from_warehouse" id="from_warehouse" class="form-control aiz-selectpicker" data-live-search="true" required>
                                <option value="">Select a warehouse</option>
                                @foreach ($warehouses as $key => $warehouse)
                                <option  value="{{ $warehouse->id }}">{{ $warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="form-group col-md-6">
                            <label for="to_warehouse" class="form-label">{{translate('To Warehouse')}} <span class="text-danger">*</span></label>
                            <select name="to_warehouse" id="to_warehouse" class="form-control aiz-selectpicker" data-live-search="true" required>
                                <option value="">Select a warehouse</option>
                                @foreach (\App\Models\Warehouse::all() as $key => $warehouse)
                                <option  value="{{ $warehouse->id }}">{{ $warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="dtpDate" class="form-label">{{ translate('Date') }} <span class="text-danger">*</span></label>
                            <input type="text" name="dtpDate" id="dtpDate" class="form-control datepicker" value="{{ date('m/d/Y') }}" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="txtAmount" class="form-label">{{ translate('Amount') }} <span class="text-danger">*</span></label>
                            <input type="number" name="txtAmount" id="txtAmount" class="form-control" step=".01" required>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="txtRemarks" class="form-label">{{ translate('Remark') }}</label>
                            <textarea name="txtRemarks" id="txtRemarks" class="form-control"></textarea>
                        </div>
                    </div>

                    <input type="hidden" name="finyear" value="{{ get_financial_year() }}">
                    <div class="form-group form-group-margin row">
                        <div class="col-sm-12 text-right">
                            <input type="submit" id="add_receive" class="btn btn-success btn-large" name="save" value="{{ translate('Save Cash Transfer') }}" tabindex="9" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function () {
        $('#from_warehouse').on('change', function () {
            var from_warehouse_id = $(this).val();
            $('#to_warehouse').find('option:not(:first)').remove();

            @foreach (\App\Models\Warehouse::all() as $warehouse)
                if ({{ $warehouse->id }} != from_warehouse_id) {
                    $('#to_warehouse').append(new Option('{{ $warehouse->name }}', '{{ $warehouse->id }}'));
                }
            @endforeach
            
            AIZ.plugins.bootstrapSelect('refresh');
        });
    });
</script>
@endsection