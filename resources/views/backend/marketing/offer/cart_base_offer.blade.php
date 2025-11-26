<div class="card-header mb-2">
    <h3 class="h6">{{translate('Add Your Cart Base offer')}}</h3>
</div>
<div class="form-group row">
    <label class="col-lg-3 col-from-label" for="title">{{translate('Offer Title')}}</label>
    <div class="col-lg-9">
        <input type="text" placeholder="{{translate('Offer Title')}}" id="title" name="title" class="form-control" required>
    </div>
</div>
<div class="form-group row">
    <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Banner')}} <small>(1920x500)</small></label>
    <div class="col-md-9">
        <div class="input-group" data-toggle="aizuploader" data-type="image">
            <div class="input-group-prepend">
                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
            </div>
            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
            <input type="hidden" name="banner" class="selected-files">
        </div>
        <div class="file-preview box sm">
        </div>
        <span class="small text-muted">{{ translate('This image is shown as cover banner in flash deal details page.') }}</span>
    </div>
</div>
<div class="product-choose-list">
    <div class="product-choose">
        <div class="form-group row">
            <label class="col-lg-3 col-from-label" for="name">{{translate('Excluded Product')}}</label>
            <div class="col-lg-9">
                <select name="product_ids[]" class="form-control product_id aiz-selectpicker" data-live-search="true" data-selected-text-format="count" required multiple>
                    @foreach(filter_products(\App\Models\Product::query())->get() as $product)
                        <option value="{{$product->id}}">{{ $product->getTranslation('name') }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
<div class="form-group row">
    <label class="col-lg-3 col-from-label">{{translate('Minimum Shopping')}}</label>
    <div class="col-lg-9">
        <input type="number" lang="en" min="0" step="0.01" placeholder="{{translate('Minimum Shopping')}}" name="min_buy" class="form-control" required>
    </div>
</div>
<div class="form-group row">
    <label class="col-lg-3 col-from-label">{{translate('Discount')}}</label>
    <div class="col-lg-7">
        <input type="number" lang="en" min="0" step="0.01" placeholder="{{translate('Discount')}}" name="discount" class="form-control" required>
    </div>
    <div class="col-lg-2">
        <select class="form-control aiz-selectpicker" name="discount_type">
            <option value="amount">{{translate('Amount')}}</option>
            <option value="percent">{{translate('Percent')}}</option>
        </select>
    </div>
</div>
<div class="form-group row">
    <label class="col-lg-3 col-from-label">{{translate('Maximum Discount Amount')}}</label>
    <div class="col-lg-9">
        <input type="number" lang="en" min="0" step="0.01" placeholder="{{translate('Maximum Discount Amount')}}" name="max_discount" class="form-control" required>
    </div>
</div>
<div class="form-group row">
    <label class="col-sm-3 control-label" for="start_date">{{translate('Date')}}</label>
    <div class="col-sm-9">
        <input type="text" class="form-control aiz-date-range" name="date_range" placeholder="Select Date">
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.aiz-selectpicker').selectpicker();
        $('.aiz-date-range').daterangepicker();
    });
</script>