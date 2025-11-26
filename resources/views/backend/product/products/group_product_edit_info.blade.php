@if(count($product_ids) > 0)
<div class="mt-4">
    <table class="table table-bordered aiz-table" style="font-size: 8px;">
        <thead class="">
            <tr>
                <th width="15%">
                    <span>{{ translate('Product') }}</span>
                </th>
                <th width="4%">
                    <span>{{ translate('MRP') }}</span>
                </th>
                <th width="7%">
                    <span>{{ translate('Web Qty') }}</span>
                </th>
                <th width="10%">
                    <span>{{ translate('Web Total Price') }}</span>
                </th>
                <th width="7%">
                    <span>{{ translate('Web Discount') }}</span>
                </th>
                <th width="7%">
                    <span>{{ translate('Web Discount Type') }}</span>
                </th>
                <th width="10%">
                    <span>{{ translate('Web Price After Discount') }}</span>
                </th>
                <th width="7%">
                    <span>{{ translate('App Qty') }}</span>
                </th>
                <th width="10%">
                    <span>{{ translate('App Total Price') }}</span>
                </th>
                <th width="7%">
                    <span>{{ translate('App Discount') }}</span>
                </th>
                <th width="7%">
                    <span>{{ translate('App Discount Type') }}</span>
                </th>
                <th width="10%">
                    <span>{{ translate('App Price After Discount') }}</span>
                </th>
            </tr>
        </thead>

        <tbody>
            @foreach ($product_ids as $key => $id)
                @php
                    $product = \App\Models\Product::findOrFail($id);
                    $group_product = \App\Models\Group_product::where('product_id', $id)->where('group_product_id', $group_product_id)->first();
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <img class="img-thumbnail" src="{{ uploaded_asset($product->thumbnail_img) }}" alt="{{ $product->getTranslation('name') }}" style="width: 60px; height: 60px;">
                            </div>
                            <div>
                                <span>{{ $product->getTranslation('name') }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span>{{ number_format($product->unit_price, 2) }}</span>
                    </td>
                    <td>
                        <input type="number" placeholder="{{ translate('Web Quantity') }}" value="{{ $group_product->qty ?? 1 }}" name="web_quantity[{{ $id }}]" class="form-control quantity web-quantity small-font" data-id="{{ $id }}" data-price="{{ $product->unit_price }}" required onchange="updateBasePrice(this, 'web')">
                    </td>
                    <td>
                        <input type="number" placeholder="{{ translate('Web Total Price') }}" name="web_base_price[{{ $id }}]" value="{{ $group_product->qty * $product->unit_price }}" class="form-control base-price web-base-price small-font" data-id="{{ $id }}" readonly>
                    </td>
                    <td>
                        <input type="number" placeholder="{{ translate('Web Discount Price') }}" value="{{ $group_product->discount_amount ?? 0.00 }}" name="web_discount_price[{{ $id }}]" class="form-control discount-price web-discount-price small-font" data-id="{{ $id }}" required onchange="updateDiscountAmount(this, 'web')">
                    </td>
                    <td>
                        <select name="web_discount_type[{{ $id }}]" class="form-control small-font" data-id="{{ $id }}" onchange="updateDiscountFields(this, 'web')">
                            <option value="flat" {{ ($group_product->discount_type == 'flat') ? 'selected' : '' }}>{{ translate('Flat') }}</option>
                            <option value="percentage" {{ ($group_product->discount_type == 'percentage') ? 'selected' : '' }}>{{ translate('Percentage') }}</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" placeholder="{{ translate('Web Price After Discount') }}" value="{{ $group_product->price ?? 0.00 }}" name="web_price[{{ $id }}]" class="form-control total-price web-total-price small-font" data-id="{{ $id }}" readonly>
                    </td>
                    <td>
                        <input type="number" placeholder="{{ translate('App Quantity') }}" value="{{ $group_product->app_qty ?? 1 }}" name="app_quantity[{{ $id }}]" class="form-control quantity app-quantity small-font" data-id="{{ $id }}" data-price="{{ $product->unit_price }}" required onchange="updateBasePrice(this, 'app')">
                    </td>
                    <td>
                        <input type="number" placeholder="{{ translate('App Total Price') }}" name="app_base_price[{{ $id }}]" value="{{ $group_product->app_qty * $product->unit_price }}" class="form-control base-price app-base-price small-font" data-id="{{ $id }}" readonly>
                    </td>
                    <td>
                        <input type="number" placeholder="{{ translate('App Discount Price') }}" value="{{ $group_product->app_discount_amount ?? 0.00 }}" name="app_discount_price[{{ $id }}]" class="form-control discount-price app-discount-price small-font" data-id="{{ $id }}" required onchange="updateDiscountAmount(this, 'app')">
                    </td>
                    <td>
                        <select name="app_discount_type[{{ $id }}]" class="form-control small-font" data-id="{{ $id }}" onchange="updateDiscountFields(this, 'app')">
                            <option value="flat" {{ ($group_product->app_discount_type == 'flat') ? 'selected' : '' }}>{{ translate('Flat') }}</option>
                            <option value="percentage" {{ ($group_product->app_discount_type == 'percentage') ? 'selected' : '' }}>{{ translate('Percentage') }}</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" placeholder="{{ translate('App Price After Discount') }}" value="{{ $group_product->app_price ?? 0.00 }}" name="app_price[{{ $id }}]" class="form-control total-price app-total-price small-font" data-id="{{ $id }}" readonly>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<style>
    .small-font {
        font-size: 10px; /* Adjust as needed */
    }

    .table td, .table th {
        vertical-align: middle; /* Ensures vertical alignment */
    }

    .table th {
        text-align: center; /* Center-aligns table headers */
    }
</style>

<script>
    function updateBasePrice(element, type) {
        const id = element.dataset.id;
        const price = parseFloat(element.dataset.price);
        const quantity = parseFloat(element.value);
        const basePriceInput = document.querySelector(`input.${type}-base-price[data-id='${id}']`);
        const discountPriceInput = document.querySelector(`input.${type}-discount-price[data-id='${id}']`);
        const discountTypeSelect = document.querySelector(`select[name='${type}_discount_type[${id}]']`);

        if (!isNaN(price) && !isNaN(quantity)) {
            const totalBasePrice = price * quantity;
            basePriceInput.value = totalBasePrice.toFixed(2);

            const discountPrice = parseFloat(discountPriceInput.value) || 0;
            updateTotalPrice(discountTypeSelect, discountPrice, totalBasePrice, quantity, id, type);
        }
    }

    function updateTotalPrice(discountTypeSelect, discountPrice, totalBasePrice, quantity, id, type) {
        const totalPriceInput = document.querySelector(`input.${type}-total-price[data-id='${id}']`);
        let discountedPrice;

        if (discountTypeSelect.value === 'flat') {
            discountedPrice = totalBasePrice - discountPrice;
        } else if (discountTypeSelect.value === 'percentage') {
            discountedPrice = totalBasePrice - ((discountPrice / 100) * totalBasePrice);
        }

        totalPriceInput.value = discountedPrice.toFixed(2);
    }

    function updateDiscountFields(select, type) {
        const id = select.dataset.id;
        const discountPriceInput = document.querySelector(`input.${type}-discount-price[data-id='${id}']`);
        const quantityInput = document.querySelector(`input.${type}-quantity[data-id='${id}']`);
        const totalBasePrice = parseFloat(document.querySelector(`input.${type}-base-price[data-id='${id}']`).value);
        const discountPrice = parseFloat(discountPriceInput.value) || 0;

        updateBasePrice(quantityInput, type);
        updateTotalPrice(select, discountPrice, totalBasePrice, parseFloat(quantityInput.value), id, type);
    }

    function updateDiscountAmount(input, type) {
        const id = input.dataset.id;
        const discountTypeSelect = document.querySelector(`select[name='${type}_discount_type[${id}]']`);
        const quantityInput = document.querySelector(`input.${type}-quantity[data-id='${id}']`);
        const totalBasePrice = parseFloat(document.querySelector(`input.${type}-base-price[data-id='${id}']`).value);
        const discountPrice = parseFloat(input.value) || 0;

        updateBasePrice(quantityInput, type);
        updateTotalPrice(discountTypeSelect, discountPrice, totalBasePrice, parseFloat(quantityInput.value), id, type);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const webQuantities = document.querySelectorAll('.web-quantity');
        const appQuantities = document.querySelectorAll('.app-quantity');

        webQuantities.forEach(input => {
            updateBasePrice(input, 'web');
        });

        appQuantities.forEach(input => {
            updateBasePrice(input, 'app');
        });
    });
</script>
