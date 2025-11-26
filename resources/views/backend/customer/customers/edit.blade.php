@extends('backend.layouts.app')

@section('content')

<div class="col-lg-10 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Customer Information')}}</h5>
        </div>

        <form action="{{ route('customers.update', $customer->id) }}" method="POST">
            <input name="_method" type="hidden" value="PATCH">
        	@csrf
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">{{translate('Name')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" value="{{ $customer->user->name }}" class="form-control" required>
                    </div>
                </div>
            <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">{{translate('Name Type')}}</label>
                    <div class="col-sm-9">
                    <select class="form-control" id="customer_type" name="customer_type" required>
                            <option <?php if($customer->customer_type == 'Normal') echo 'selected';?> value="Normal">Normal</option>
                            <option <?php if($customer->customer_type == 'Premium') echo 'selected';?> value="Premium">Premium</option>
                            <option <?php if($customer->customer_type == 'Corporate') echo 'selected';?> value="Corporate">Corporate</option>
                            <option <?php if($customer->customer_type == 'Employee') echo 'selected';?> value="Employee">Employee</option>
                            <option <?php if($customer->customer_type == 'Retail') echo 'selected';?> value="Retail">Retail</option>
                            <option <?php if($customer->customer_type == 'Website') echo 'selected';?> value="Website">Website</option>
                        </select>
                        
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="email">{{translate('Email')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Email')}}" id="email" name="email" value="{{ $customer->user->email }}" class="form-control" >
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="mobile">{{translate('Phone')}}</label>
                    <div class="col-sm-9">
                        <input type="text" readonly placeholder="{{translate('Phone')}}" id="mobile" name="mobile" value="{{ $customer->user->phone }}" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="area_code">{{translate('Area')}}</label>
                    <div class="col-sm-9">
                        <select class="select2 col-from-label aiz-selectpicker" id="area_code" name="area_code" data-toggle="select2" data-placeholder="Choose ..." data-live-search="true" required>
                            <option value="">Choose Area</option>
                            @foreach (\App\Models\Area::get() as $area)
                            <option value="{{ $area->code }}" @if($customer->area_code == $area->code) selected @endif>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

 @php
$test =  \App\Models\Staff::where('role_id','=','9')->get();
//dd($test);
@endphp 
                
                <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="executive">{{translate('Assain Executive')}}</label>
                        <div class="col-sm-9">
                            <select class="select2 col-from-label aiz-selectpicker" id="executive" name="executive" data-toggle="select2" data-placeholder="Choose ..." data-live-search="true" required>
                            <option value="">Choose Executive</option>
                            @foreach(\App\Models\Staff::where('role_id','=','9')->orWhere('role_id','=','14')->get() as $executive)
                             <option value="{{$executive->user_id}}"  @if($customer->staff_id == $executive->user_id) selected @endif>{{ $executive->user->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="testimonial">{{translate('Testimonial')}}</label>
                    <div class="col-sm-9">
                    <textarea class="form-control" name="testimonial" placeholder="Enter Testimonial"></textarea>
                        <!-- <input type="text" placeholder="{{translate('Testimonial')}}" id="testimonial" name="testimonial" value="{{ $customer->testimonial }}" class="form-control" required> -->
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="password">{{translate('Credit Enabled ?')}}</label>
                    <div class="col-sm-9">
                    <label class="aiz-switch aiz-switch-success mb-0">
                              <input type="checkbox" name="credit_enable" @if($customer->credit_enable==1) {{'checked'}} @endif onchange="changeData(this)" id="credit_enable">
                              <span></span>
                          </label>
                    </div>
                </div>
                <div class="form-group row credit_enable" style="@if($customer->credit_enable==0) {{'display: none;'}} @endif">
                    <label class="col-sm-3 col-from-label" for="mobile">{{translate('Credit Limit')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Credit Limit')}}" id="credit_limit" name="credit_limit" value="{{ $customer->credit_limit }}" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">{{ translate('Office/Workstation Name') }}</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" placeholder="{{ translate('Office/Workstation Name') }}" name="office" value="{{ $customer->office }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">{{ translate('Office/Workstation Phone') }}</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control"  placeholder="{{ translate('Office/Workstation Phone')}}" name="office_phone" value="{{ $customer->office_phone }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">{{ translate('Designation') }}</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control"  placeholder="{{ translate('Designation')}}" name="designation" value="{{ $customer->designation }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">{{ translate('Monthly Income') }}</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control"  placeholder="{{ translate('Monthly Income')}}" name="salary" value="{{ $customer->salary }}">
                                    </div>
                                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="mobile">{{translate('Ducoment Type')}}</label>
                    <div class="col-sm-9">
                    <select class="form-control" name="document_type" id="document_type" onchange="documentchange(this.value)">
                                        
                                        <option <?php if($customer->document_type == 'Nid') echo 'selected';?> value="Nid">Nid</option>
                                        <option <?php if($customer->document_type == 'Birth Certificate') echo 'selected';?> value="Birth Certificate">Birth Certificate</option>
                                        <option <?php if($customer->document_type == 'Passport') echo 'selected';?> value="Passport">Passport</option>
                                        </select>
                    </div>
                </div>
                <div class="form-group row">
                <?php if(!empty($customer->document_type)){?>
                    <label class="col-sm-3 col-from-label" for="mobile" id="nidLabel">{{$customer->document_type}}</label>
                    <?php }else{?>
                        <label class="col-sm-3 col-from-label" for="mobile" id="nidLabel">{{translate('NID')}}</label>
                    <?php }?>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('NID')}}" id="nid" name="nid" value="{{ $customer->nid }}" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                <?php if(!empty($customer->document_type)){?>
                                    <label class="col-md-3 col-form-label"><span id="dodu_photo">{{$customer->document_type}}</span> Photo</label>
                                    <?php }else{?>
                                        <label class="col-md-3 col-form-label"><span id="dodu_photo">NID</span> Photo</label>
                                    <?php }?>
                                    <div class="col-md-9">
                                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                            </div>
                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                            <input type="hidden" name="nid_photo" value="{{ $customer->nid_photo }}" class="selected-files">
                                        </div>
                                        <div class="file-preview box sm">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">{{ translate('Utility Bill Photo') }}</label>
                                    <div class="col-md-9">
                                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                            </div>
                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                            <input type="hidden" name="utility" value="{{ $customer->utility }}" class="selected-files">
                                        </div>
                                        <div class="file-preview box sm">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">{{ translate('Office/Workstation ID') }}</label>
                                    <div class="col-md-9">
                                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                            </div>
                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                            <input type="hidden" name="office_id" value="{{ $customer->office_id }}" class="selected-files">
                                        </div>
                                        <div class="file-preview box sm">
                                        </div>
                                    </div>
                                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="mobile">{{translate('Family Refference Name')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Family Refference Name')}}" id="ref1_name" name="ref1_name" value="{{ $customer->ref1_name }}" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="mobile">{{translate('Family Refference Phone')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Family Refference Phone')}}" id="ref1_phone" name="ref1_phone" value="{{ $customer->ref1_phone }}" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">{{ translate('Family Refference Relation') }}</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" placeholder="{{ translate('Family Refference Relation')}}" name="ref1_relation" value="{{ $customer->ref1_relation }}">
                                    </div>
                                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="mobile">{{translate('Office/Workstation Refference Name')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Office/Workstation Refference Name')}}" id="ref2_name" name="ref2_name" value="{{ $customer->ref2_name }}" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="mobile">{{translate('Office/Workstation Refference Phone')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Office/Workstation Refference Phone')}}" id="ref2_phone" name="ref2_phone" value="{{ $customer->ref2_phone }}" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">{{ translate('Office/Workstation Refference Relation') }}</label>
                                    <div class="col-md-9">
                                        <input type="hidden" name="credit_form" value="1">
                                        <input type="text" class="form-control" placeholder="{{ translate('Office/Workstation Refference Relation')}}" name="ref2_relation" value="{{ $customer->ref2_relation }}">
                                    </div>
                                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
<script type="text/javascript">
function changeData(a){
    if($(a).is(':checked')){
        $('.credit_enable').show()
    }else{
        $('.credit_enable').hide()
    }
}
</script>
<script type="text/javascript">
function documentchange(id){
   $('#nidLabel').text(id);
   $('#dodu_photo').text(id);
        
}
</script>