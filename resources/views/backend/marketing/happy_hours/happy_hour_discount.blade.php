@if(count($product_ids) > 0)
    <table class="table table-bordered aiz-table">
        <thead>
            <tr>
                <th width="40%">
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
                <th data-breakpoints="lg" width="10%">
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
                @endphp
                <tr>
                    <!-- Product Details -->
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <img 
                                    class="size-60px img-fit" 
                                    src="{{ uploaded_asset($product->thumbnail_img) }}" 
                                    alt="{{ $product->getTranslation('name') }}" 
                                    title="{{ $product->getTranslation('name') }}">
                            </div>
                            <div>
                                <span>{{ $product->getTranslation('name') }}</span>
                            </div>
                        </div>
                    </td>

                    <!-- App Base Price -->
                    <td>
                        <span>{{ number_format($product->unit_price, 2) }}</span>
                    </td>

                    <!-- App Max Quantity -->
                    <td>
                        <div class="form-group mb-0">
                            <input 
                                type="number" 
                                name="app_quantity[{{ $id }}]" 
                                class="form-control" 
                                placeholder="{{ translate('Quantity') }}" 
                                required>
                        </div>
                    </td>

                    <!-- Web Base Price -->
                    <td>
                        <span>{{ number_format($product->unit_price, 2) }}</span>
                    </td>

                    <!-- Web Max Quantity -->
                    <td>
                        <div class="form-group mb-0">
                            <input 
                                type="number" 
                                name="web_quantity[{{ $id }}]" 
                                class="form-control" 
                                placeholder="{{ translate('Quantity') }}" 
                                required>
                        </div>
                    </td>

                    <!-- Discount -->
                    <td>
                        <div class="form-group mb-0">
                            <input 
                                type="number" 
                                name="discount_{{ $id }}" 
                                value="" 
                                min="0" 
                                step="1" 
                                class="form-control" 
                                required>
                        </div>
                    </td>

                    <!-- Discount Type -->
                    <td>
                        <div class="form-group mb-0">
                            <select class="form-control" name="discount_type_{{ $id }}">
                                <option value="amount">{{ translate('Flat') }}</option>
                                <option value="percent">{{ translate('Percent') }}</option>
                            </select>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
