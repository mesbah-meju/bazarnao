@if(count($product_ids) > 0)
<table class="table table-bordered aiz-table">
  <thead>
  	<tr>
  		<td width="100%">
          <span>{{translate('Product')}}</span>
  		</td>
      <td data-breakpoints="lg" width="20%">
          <span>{{translate('App Base Price')}}</span>
  		</td>
      <td data-breakpoints="lg" width="20%">
          <span>{{translate('App Max Quantity')}}</span>
  		</td>
      <td data-breakpoints="lg" width="20%">
          <span>{{translate('Web Base Price')}}</span>
  		</td>
      <td data-breakpoints="lg" width="20%">
          <span>{{translate('Web Max Quantity')}}</span>
  		</td>
<!--   		<td data-breakpoints="lg" width="20%">
          <span>{{translate('Discount')}}</span>
  		</td>
      <td data-breakpoints="lg" width="10%">
          <span>{{translate('Discount Type')}}</span>
      </td> -->
  	</tr>
  </thead>
  <tbody>
    @foreach ($product_ids as $key => $id)
        @php
            $product = \App\Models\Product::findOrFail($id);
        @endphp
        <tr>
            <td>
                <div class="from-group row">
                    <div class="col-auto">
                        <img class="size-60px img-fit" src="{{ uploaded_asset($product->thumbnail_img) }}">
                    </div>
                    <div class="col">
                        <span>{{ $product->getTranslation('name') }}</span>
                    </div>
                </div>
            </td>
            <td>
                <span>{{ $product->unit_price }}</span>
            </td>
            <td>
                <div class="form-group row">
                    <div class="col-sm-9">
                        <input type="number" placeholder="{{ translate('Quantity') }}" name="app_quantity[{{ $id }}]" class="form-control" required>
                    </div>
                </div>
            </td>
            <td>
                <span>{{ $product->unit_price }}</span>
            </td>
            <td>
                <div class="form-group row">
                    <div class="col-sm-9">
                        <input type="number" placeholder="{{ translate('Quantity') }}" name="web_quantity[{{ $id }}]" class="form-control" required>
                    </div>
                </div>
            </td>
        </tr>
    @endforeach
</tbody>

</table>
@endif
