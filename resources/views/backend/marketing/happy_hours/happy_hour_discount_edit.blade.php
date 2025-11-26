@if(count($product_ids) > 0)
<table class="table table-bordered aiz-table">
    <thead>
        <tr>
            <th width="35%">
                <span>{{ translate('Product') }}</span>
            </th>
            <th data-breakpoints="lg" width="15%">
                <span>{{ translate('App Base Price') }}</span>
            </th>
            <th data-breakpoints="lg" width="15%">
                <span>{{ translate('App Max Quantity') }}</span>
            </th>
            <th data-breakpoints="lg" width="15%">
                <span>{{ translate('Web Base Price') }}</span>
            </th>
            <th data-breakpoints="lg" width="15%">
                <span>{{ translate('Web Max Quantity') }}</span>
            </th>
            <th data-breakpoints="lg" width="15%">
                <span>{{ translate('Discount') }}</span>
            </th>
            <th data-breakpoints="lg" width="10%">
                <span>{{ translate('Discount Type') }}</span>
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($product_ids as $key => $id)
        @php
        $product = \App\Models\Product::findOrFail($id);
        $happy_hour_product = \App\Models\HappyHourProduct::where('happy_hour_id', $happy_hour_id)->where('product_id', $product->id)->first();
        @endphp
        <tr>
            <td>
                <div class="form-group row">
                    <div class="col-auto">
                        <img src="{{ uploaded_asset($product->thumbnail_img)}}" class="size-60px img-fit">
                    </div>
                    <div class="col">
                        <span>{{ $product->getTranslation('name')  }}</span>
                    </div>
                </div>
            </td>
            <td>
                <span>{{ $product->unit_price }}</span>
            </td>
            <td>
                <div class="form-group row">
                    <div class="col-sm-9">
                        <input type="number" placeholder="{{ translate('Quantity') }}" id="app_quantity"
                            value="{{ optional($happy_hour_product)->app_quantity ?? 0 }}"
                            name="app_quantity[{{ $id }}]" class="form-control" required>
                    </div>
                </div>
            </td>

            <td>
                <span>{{ $product->unit_price }}</span>
            </td>
            <td>
                <div class="form-group row">
                    <div class="col-sm-9">
                        <input type="number" placeholder="{{ translate('Quantity') }}" id="web_quantity"
                            value="{{ $happy_hour_product ? ($happy_hour_product->web_quantity ?? 0) : 0 }}"
                            name="web_quantity[{{ $id }}]" class="form-control" required>
                    </div>
                </div>
            </td>

            
                @if ($happy_hour_product != null)
                <td>
                    <input type="number" lang="en" name="discount_{{ $id }}" value="{{ $happy_hour_product->discount }}" min="0" step="1" class="form-control" required>
                </td>
                <td>
                    <select class="form-control" name="discount_type_{{ $id }}">
                        <option value="amount" <?php if ($happy_hour_product->discount_type == 'amount') echo "selected"; ?> >{{ translate('Flat') }}</option>
                        <option value="percent" <?php if ($happy_hour_product->discount_type == 'percent') echo "selected"; ?> >{{ translate('Percent') }}</option>
                    </select>
                </td>
                @else
                <td>
                    <input type="number" lang="en" name="discount_{{ $id }}" value="{{ $product->discount }}" min="0" step="1" class="form-control" required>
                </td>
                <td>
                    <select class="form-control" name="discount_type_{{ $id }}">
                        <option value="amount" <?php if ($product->discount_type == 'amount') echo "selected"; ?> >{{ translate('Flat') }}</option>
                        <option value="percent" <?php if ($product->discount_type == 'percent') echo "selected"; ?> >{{ translate('Percent') }}</option>
                    </select>
                </td>
                @endif

        </tr>
        @endforeach
    </tbody>
</table>
@endif