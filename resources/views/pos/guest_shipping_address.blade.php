<div class="form-group">
    <div class="row">
        <label class="col-sm-2 control-label" for="name">{{translate('Name')}}</label>
        <div class="col-sm-10">
            <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" required>
        </div>
    </div>
</div>

<div class="form-group">
    <div class=" row">
        <label class="col-sm-2 control-label" for="address">{{translate('Address')}}</label>
        <div class="col-sm-10">
            <textarea placeholder="{{translate('Address')}}" id="address" name="address" class="form-control" required></textarea>
        </div>
    </div>
</div>

<div class="form-group">
    <div class=" row">
        <label class="col-sm-2 control-label" for="phone">{{translate('Phone')}}</label>
        <div class="col-sm-10">
            <input type="number" min="0" placeholder="{{translate('Phone')}}" id="phone" name="phone" class="form-control" required>
        </div>
    </div>
</div>