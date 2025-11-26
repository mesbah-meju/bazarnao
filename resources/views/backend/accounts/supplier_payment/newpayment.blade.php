@php $rand_num = rand(1, 20); @endphp
<div class="row gutters-5" id="pmethod_{{ $rand_num }}">
    <div class="form-group col-md-5">
        <label for="payments" class="col-form-label pb-2">{{ translate('Payment Type') }}</label>
        @php $card_type = 1020101; @endphp
        <select name="multipaytype[]" id="payment_type" class="form-control card_typesl postform resizeselect aiz-selectpicker">
            @foreach ($payment_methods as $key => $value)
            <option value="{{ $key }}" {{ (isset($card_type) && $card_type == $key) ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-5">
        <label for="4digit" class="col-form-label pb-2">{{ translate('Paid Amount') }}</label>
        <input type="text" id="pamount_by_method_{{ $rand_num }}" class="form-control number pay firstpay text-right valid_number" name="pamount_by_method[]" value="" onkeyup="changedueamount()" placeholder="0.00" required />
    </div>
    <div class="form-group col-md-2">
        <label for="payments" class="col-form-label pb-2 text-white">{{ translate('Payment Type') }}</label>
        <button class="btn btn-danger" onclick="removeMethod(this,{{ $rand_num }})"><i class="las la-trash"></i></button>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        "use strict";

        $("select.form-control:not(.dont-select-me)").select2({
            placeholder: "Select option",
            allowClear: true
        });
    });
</script>
