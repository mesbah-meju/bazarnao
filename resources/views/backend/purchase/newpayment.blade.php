<div class="row no-gutters">
    <div class="form-group col-md-6">
        <label for="payments" class="col-form-label pb-2"><?php echo translate('Payment Type'); ?></label>
        <?php
        $card_type = 111000001;
        ?>
        <select name="multipaytype[]" class="form-control card_typesl postform aiz-selectpicker" id="payment_type" onchange="check_creditsale()">
            @foreach ($payment_types as $key => $value)
            <option value="{{ $key }}" @selected($card_type == $key)>{{ $value }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-6">
        <label for="4digit" class="col-form-label pb-2"><?php echo translate('Paid Amount'); ?></label>
        <input type="number" id="pamount_by_method" class="form-control number pay" name="pamount_by_method[]" value="" onkeyup="changedueamount()" placeholder="0" />
    </div>
</div>