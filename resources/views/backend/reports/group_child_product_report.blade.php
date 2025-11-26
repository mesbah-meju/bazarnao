@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h6">{{ translate('Child Products Report') }}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h1 class="h6">{{ translate('Child Products Report') }}</h1>
                <div class="d-flex">
                    <button class="btn btn-sm btn-info mr-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                    <a href="{{ route('group_child_product_report.index', array_merge(request()->query(), ['type' => 'excel'])) }}" target="_blank" class="btn btn-sm btn-success" type="button">Excel</a>
                </div>
            </div>
            <div class="card-body ">
                <form action="{{ route('group_child_product_report.index') }}" method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="parent_id">{{ translate('Select Parent Product') }}</label>
                                <select class="form-control aiz-selectpicker" name="parent_id" id="parent_id" data-live-search="true"  onchange="this.form.submit()" >
                                    <option value="">{{ translate('All Parents') }}</option>
                                    @foreach ($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ request()->parent_id == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-sm btn-primary">{{ translate('Filter') }}</button>
                        </div>
                    </div>
                </form>

                

                <div class="table-responsive printArea">
                    <style>
                        th, td {
                            text-align: center;
                            vertical-align: middle;
                        }
                        .parent-row {
                            background-color: #f8f9fa;
                        }
                        .child-row {
                            background-color: #ffffff;
                        }
                        .table-responsive {
                            margin-top: 20px;
                        }
                    </style>
                    <table class="table table-bordered aiz-table mb-0">
                        <thead>
                            <tr>
                                <th>{{ translate('SL') }}</th>
                                <th>{{ translate('Parent Product Name') }}</th>
                                <th>{{ translate('Child Product Name') }}</th>
                                <th>{{ translate('Price') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @foreach ($parents as $parent)
                                @php $childProducts = $children->get($parent->id); @endphp
                                @if ($childProducts && $childProducts->count() > 0)
                                    <tr class="parent-row">
                                        <td>{{ $i++ }}</td>
                                        <td colspan="1"><strong>{{ $parent->name }}</strong></td>
                                        <td></td>
                                        <td colspan="1"><strong>{{ single_price($parent->unit_price) }}</strong></td>
                                    </tr>
                                    @foreach ($childProducts as $child)
                                        <tr class="child-row">
                                            <td></td>
                                            <td></td>
                                            <td>{{ $child->name }}</td>
                                            <td>{{ single_price($child->unit_price) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function printDiv() {
        var printContents = document.querySelector('.printArea').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

    function submitForm(url) {
        $('#culexpo').attr('action', url);
        $('#culexpo').submit();
    }
</script>

@endsection
