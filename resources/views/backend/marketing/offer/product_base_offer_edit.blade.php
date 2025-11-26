<div class="card-header mb-2">
    <h5 class="mb-0 h6">{{translate('Add Your Product Base Offer')}}</h5>
</div>
<div class="form-group row">
    <label class="col-lg-3 control-label" for="offer_title">{{translate('Offer Title')}}</label>
    <div class="col-lg-9">
        <input type="text" placeholder="{{translate('Offer Totle')}}" id="offer_title" name="title" value="{{ $offer->title }}" class="form-control" required>
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
                                <input type="hidden" name="banner" value="{{ $offer->banner }}" class="selected-files">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
<div class="product-choose-list">
    <div class="product-choose">
        <div class="form-group row">
            <label class="col-lg-3 control-label" for="name">{{translate('Product')}}</label>
            <div class="col-lg-9">
                <select name="product_ids[]" class="form-control product_id aiz-selectpicker" data-live-search="true" data-selected-text-format="count" required multiple>
                    @foreach(filter_products(\App\Models\Product::query())->get() as $key => $product)
                        <option value="{{$product->id}}"
                            @foreach (json_decode($offer->details) as $key => $details)
                                @if ($details->product_id == $product->id)
                                    selected
                                @endif
                            @endforeach
                            >{{$product->getTranslation('name')}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
@php
  $start_date = date('m/d/Y', $offer->start_date);
  $end_date = date('m/d/Y', $offer->end_date);
   $odetails = json_decode($offer->details);
@endphp
<div class="form-group row">
    <label class="col-sm-3 control-label" for="start_date">{{translate('Date')}}</label>
    <div class="col-sm-9">
      <input type="text" class="form-control aiz-date-range" value="{{ $start_date .' - '. $end_date }}" name="date_range" placeholder="Select Date">
    </div>
</div>
<div class="form-group row">
   <label class="col-lg-3 col-from-label">{{translate('Minimum Shopping')}}</label>
   <div class="col-lg-9">
      <input type="number" lang="en" min="0" step="0.01" placeholder="{{translate('Minimum Shopping')}}" name="min_buy" value="{{ $odetails[0]->min_buy }}" class="form-control" required>
   </div>
</div>
<div class="form-group row">
   <label class="col-lg-3 col-from-label">{{translate('Maximum Discount Amount')}}</label>
   <div class="col-lg-9">
      <input type="number" lang="en" min="0" step="0.01" placeholder="{{translate('Maximum Discount Amount')}}" name="max_discount" value="{{  $odetails[0]->max_discount }}" class="form-control" required>
   </div>
</div>
<div class="form-group row">
   <label class="col-lg-3 col-from-label">{{translate('Discount type')}}</label>
   <div class="col-lg-9">
      <select class="form-control" name="full_discount">
		<option @if($offer->full_discount==1) {{'selected'}} @endif value="1">Full Discount</option>
		<option @if($offer->full_discount==0) {{'selected'}} @endif value="0">Partial Discount</option>
	  </select>
   </div>
</div>
<div class="form-group row">
   <label class="col-lg-3 col-from-label">{{translate('Maximum Quantity')}}</label>
   <div class="col-lg-9">
      <input type="number" value="{{$offer->max_qty}}" name="max_qty" class="form-control">
   </div>
</div>
<div class="form-group row">
   <label class="col-lg-3 col-from-label">{{translate('Discount per Quantity')}}</label>
   <div class="col-lg-9">
      <input type="number" value="{{$offer->disc_per_qty}}" name="disc_per_qty" class="form-control">
   </div>
</div>
<!-- <div class="form-group row">
   <label class="col-lg-3 col-from-label">{{translate('Discount')}}</label>
   <div class="col-lg-5">
       <input type="number" lang="en" min="0" step="0.01" placeholder="{{translate('Discount')}}" value="{{ $offer->discount }}" name="discount" class="form-control" required>

   </div>
   <div class="col-lg-4">
       <select class="form-control aiz-selectpicker" name="discount_type">
           <option value="amount" @if ($offer->discount_type == 'amount') selected  @endif>{{translate('Amount')}}</option>
           <option value="percent" @if ($offer->discount_type == 'percent') selected  @endif>{{translate('Percent')}}</option>
       </select>
   </div>
</div> -->

<script type="text/javascript">

    $(document).ready(function(){
        $('.aiz-date-range').daterangepicker();
        AIZ.plugins.bootstrapSelect('refresh');
		$('[data-toggle="aizuploader"]').each(function () {
				
                var $this = $(this);
                var files = $this.find(".selected-files").val();

                $.post(
                    AIZ.data.appUrl + "/aiz-uploader/get_file_by_ids",
                    { _token: AIZ.data.csrf, ids: files },
                    function (data) {

                        $this.next(".file-preview").html(null);

                        if (data.length > 0) {
                            $this.find(".file-amount").html(
                                AIZ.uploader.updateFileHtml(data)
                            );
                            for (
                                var i = 0;
                                i < data.length;
                                i++
                            ) {
                                var thumb = "";
                                if (data[i].type === "image") {
                                    thumb =
                                        '<img src="' +
                                        AIZ.data.fileBaseUrl +
                                        data[i].file_name +
                                        '" class="img-fit">';
                                } else {
                                    thumb = '<i class="la la-file-text"></i>';
                                }
                                var html =
                                    '<div class="d-flex justify-content-between align-items-center mt-2 file-preview-item" data-id="' +
                                    data[i].id +
                                    '" title="' +
                                    data[i].file_original_name +
                                    "." +
                                    data[i].extension +
                                    '">' +
                                    '<div class="align-items-center align-self-stretch d-flex justify-content-center thumb">' +
                                    thumb +
                                    "</div>" +
                                    '<div class="col body">' +
                                    '<h6 class="d-flex">' +
                                    '<span class="text-truncate title">' +
                                    data[i].file_original_name +
                                    "</span>" +
                                    '<span class="ext">.' +
                                    data[i].extension +
                                    "</span>" +
                                    "</h6>" +
                                    "<p>" +
                                    AIZ.extra.bytesToSize(
                                        data[i].file_size
                                    ) +
                                    "</p>" +
                                    "</div>" +
                                    '<div class="remove">' +
                                    '<button class="btn btn-sm btn-link remove-attachment" type="button">' +
                                    '<i class="la la-close"></i>' +
                                    "</button>" +
                                    "</div>" +
                                    "</div>";

                                $this.next(".file-preview").append(html);
                            }
                        } else {
                            $this.find(".file-amount").html("Choose File");
                        }
                });
            });
    });

</script>
