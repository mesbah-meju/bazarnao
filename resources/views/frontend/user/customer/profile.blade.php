@extends('frontend.layouts.app')

@section('content')

    <section class="py-5">
        <div class="container">
            <div class="d-flex align-items-start">
                @include('frontend.inc.user_side_nav')
                <div class="aiz-user-panel">
                    <div class="aiz-titlebar mt-2 mb-4">
                      <div class="row align-items-center">
                        <div class="col-md-6">
                            <h1 class="h3">{{ translate('Manage Profile') }}</h1>
                        </div>
                      </div>
                    </div>
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Basic Info</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Credit Form</a>
  </li>

</ul>
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

                    <!-- Basic Info-->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Basic Info')}}</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Your Name') }}</label>
                                    <div class="col-md-10">
                                    <input type="hidden" name="credit_form" value="0">
                                        <input type="text" class="form-control" placeholder="{{ translate('Your Name') }}" name="name" value="{{ Auth::user()->name }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Your Phone') }}</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" placeholder="{{ translate('Your Phone')}}" name="phone" value="{{ Auth::user()->phone }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Birth Date') }}</label>
                                    <div class="col-md-10">
                                        <input type="date" class="form-control" placeholder="{{ translate('Birth Date')}}" name="dob" value="{{ Auth::user()->customer->dob }}">
                                    </div>
                                </div>
                                
                                
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Your Profile Photo') }}</label>
                                    <div class="col-md-10">
                                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                            </div>
                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                            <input type="hidden" name="photo" value="{{ Auth::user()->avatar_original }}" class="selected-files">
                                        </div>
                                        <div class="file-preview box sm">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Your Password') }}</label>
                                    <div class="col-md-10">
                                        <input type="password" class="form-control" placeholder="{{ translate('New Password') }}" name="new_password">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Confirm Password') }}</label>
                                    <div class="col-md-10">
                                        <input type="password" class="form-control" placeholder="{{ translate('Confirm Password') }}" name="confirm_password">
                                    </div>
                                </div>

                                <div class="form-group mb-0 text-right">
                                    <button type="submit" class="btn btn-primary">{{translate('Update Profile')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Address')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row gutters-10">
                                @foreach (Auth::user()->addresses as $key => $address)
                                    <div class="col-lg-6">
                                        <div class="border p-3 pr-5 rounded mb-3 position-relative">
                                            <div>
                                                <span class="w-50 fw-600">{{ translate('Address') }}:</span>
                                                <span class="ml-2">{{ $address->address }}</span>
                                            </div>
                                            <div>
                                                <span class="w-50 fw-600">{{ translate('Postal Code') }}:</span>
                                                <span class="ml-2">{{ $address->postal_code }}</span>
                                            </div>
                                            <div>
                                                <span class="w-50 fw-600">{{ translate('City') }}:</span>
                                                <span class="ml-2">{{ $address->city }}</span>
                                            </div>
                                            <div>
                                                <span class="w-50 fw-600">{{ translate('Country') }}:</span>
                                                <span class="ml-2">{{ $address->country }}</span>
                                            </div>
                                            <div>
                                                <span class="w-50 fw-600">{{ translate('Phone') }}:</span>
                                                <span class="ml-2">{{ $address->phone }}</span>
                                            </div>
                                            @if ($address->set_default)
                                                <div class="position-absolute right-0 bottom-0 pr-2 pb-3">
                                                    <span class="badge badge-inline badge-primary">{{ translate('Default') }}</span>
                                                </div>
                                            @endif
                                            <div class="dropdown position-absolute right-0 top-0">
                                                <button class="btn bg-gray px-2" type="button" data-toggle="dropdown">
                                                    <i class="la la-ellipsis-v"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                    @if (!$address->set_default)
                                                        <a class="dropdown-item" href="{{ route('addresses.set_default', $address->id) }}">{{ translate('Make This Default') }}</a>
                                                    @endif
                                                    <a class="dropdown-item" href="{{ route('addresses.destroy', $address->id) }}">{{ translate('Delete') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-lg-6 mx-auto" onclick="add_new_address()">
                                    <div class="border p-3 rounded mb-3 c-pointer text-center bg-light">
                                        <i class="la la-plus la-2x"></i>
                                        <div class="alpha-7">{{ translate('Add New Address') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Change -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Change your email')}}</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('user.change.email') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>{{ translate('Your Email') }}</label>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="input-group mb-3">
                                          <input type="email" class="form-control" placeholder="{{ translate('Your Email')}}" name="email" value="{{ Auth::user()->email }}" />
                                          <div class="input-group-append">
                                             <button type="button" class="btn btn-outline-secondary new-email-verification">
                                                 <span class="d-none loading">
                                                     <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                     {{ translate('Sending Email...') }}
                                                 </span>
                                                 <span class="default">{{ translate('Verify') }}</span>
                                             </button>
                                          </div>
                                        </div>
                                        <div class="form-group mb-0 text-right">
                                            <button type="submit" class="btn btn-primary">{{translate('Update Email')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

                <!-- Basic Info-->
                <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Credit Form')}}</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Office/Workstation Name') }}</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" required placeholder="{{ translate('Office/Workstation Name') }}" name="office" value="{{ Auth::user()->customer->office }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Office/Workstation Phone') }}</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control"  required placeholder="{{ translate('Office/Workstation Phone')}}" name="office_phone" value="{{ Auth::user()->customer->office_phone }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Designation') }}</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control"  required placeholder="{{ translate('Designation')}}" name="designation" value="{{ Auth::user()->customer->designation }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Monthly Income') }}</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control"  required placeholder="{{ translate('Monthly Income')}}" name="salary" value="{{ Auth::user()->customer->salary }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Document Type') }}</label>
                                    <div class="col-md-10">
                                        <select required class="form-control" name="document_type" id="document_type">
                                        <option value="">Select Document Type</option>
                                        <option <?php if(Auth::user()->customer->document_type == 'Nid') echo 'selected';?> value="Nid">Nid</option>
                                        <option <?php if(Auth::user()->customer->document_type == 'Birth Certificate') echo 'selected';?> value="Birth Certificate">Birth Certificate</option>
                                        <option <?php if(Auth::user()->customer->document_type == 'Passport') echo 'selected';?> value="Passport">Passport</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="nid_section" style="<?php if(Auth::user()->customer->document_type == 'Nid' || empty(Auth::user()->customer->document_type) ) echo 'display:block'; else echo 'display:none';?>">
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Your NID') }}</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control"   placeholder="{{ translate('Your NID')}}" name="nid" id="nid" value="{{ Auth::user()->customer->nid }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('NID Photo') }}</label>
                                    <div class="col-md-10">
                                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                            </div>
                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                            <input type="hidden" name="nid_photo" required value="{{ Auth::user()->customer->nid_photo }}" class="selected-files">
                                        </div>
                                        <div class="file-preview box sm">
                                        </div>
                                    </div>
                                </div>

                                </div>
                                <div id="bc_section" style="<?php if(Auth::user()->customer->document_type == 'Birth Certificate') echo 'display:block'; else echo 'display:none';?>">
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Your Birth Certificate ') }}</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control"  placeholder="{{ translate('Your Birth Certificate')}}" name="b_cert" id="b_cert" value="{{ Auth::user()->customer->nid }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Birth Certificate Photo') }}</label>
                                    <div class="col-md-10">
                                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                            </div>
                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                            <input type="hidden" name="nid_photo1" required value="{{ Auth::user()->customer->nid_photo }}" class="selected-files">
                                        </div>
                                        <div class="file-preview box sm">
                                        </div>
                                    </div>
                                </div>

                                </div>
                                <div id="passport_section" style="<?php if(Auth::user()->customer->document_type == 'Passport') echo 'display:block'; else echo 'display:none';?>">
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Your Passport') }}</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" placeholder="{{ translate('Your Passport')}}"  name="passport" id="passport" value="{{ Auth::user()->customer->nid }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Passport Photo') }}</label>
                                    <div class="col-md-10">
                                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                            </div>
                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                            <input type="hidden" name="nid_photo2" value="{{ Auth::user()->customer->nid_photo }}" required class="selected-files">
                                        </div>
                                        <div class="file-preview box sm">
                                        </div>
                                    </div>
                                </div>

                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Utility Bill Photo') }}</label>
                                    <div class="col-md-10">
                                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                            </div>
                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                            <input type="hidden" name="utility" value="{{ Auth::user()->customer->utility }}" class="selected-files">
                                        </div>
                                        <div class="file-preview box sm">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Office/Workstation ID') }}</label>
                                    <div class="col-md-10">
                                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                            </div>
                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                            <input type="hidden" name="office_id" value="{{ Auth::user()->customer->office_id }}" class="selected-files">
                                        </div>
                                        <div class="file-preview box sm">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Family Refference Name') }}</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" required placeholder="{{ translate('Family Refference Name')}}" name="ref1_name" value="{{ Auth::user()->customer->ref1_name }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Family Refference Phone') }}</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" required placeholder="{{ translate('Family Refference Phone')}}" name="ref1_phone" value="{{ Auth::user()->customer->ref1_phone }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Family Refference Relation') }}</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" required placeholder="{{ translate('Family Refference Relation')}}" name="ref1_relation" value="{{ Auth::user()->customer->ref1_relation }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Office/Workstation Refference Name') }}</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" placeholder="{{ translate('Office/Workstation Refference Name')}}" name="ref2_name" value="{{ Auth::user()->customer->ref2_name }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Office/Workstation Refference Phone') }}</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" required placeholder="{{ translate('Office/Workstation Refference Phone')}}" name="ref2_phone" value="{{ Auth::user()->customer->ref2_phone }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">{{ translate('Office/Workstation Refference Relation') }}</label>
                                    <div class="col-md-10">
                                        <input type="hidden" name="credit_form" value="1">
                                        <input type="text" class="form-control" required placeholder="{{ translate('Office/Workstation Refference Relation')}}" name="ref2_relation" value="{{ Auth::user()->customer->ref2_relation }}">
                                    </div>
                                </div>
                                

                                <div class="form-group mb-0 text-right">
                                    <button type="submit" class="btn btn-primary">{{translate('Update Profile')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                </div>
</div>

                </div>
            </div>
        </div>
    </section>

@endsection

@section('modal')
    <div class="modal fade" id="new-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('New Address') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form-default" role="form" action="{{ route('addresses.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="p-3">
                            <div class="row">
                                <label class="col-md-2 col-form-label">{{ translate('Address') }}</label>
                                <div class="col-md-10">
                                    <textarea class="form-control  mb-3" placeholder="{{ translate('Your Address') }}" rows="1" name="address" required></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-2 col-form-label">{{ translate('Country') }}</label>
                                <div class="col-md-10">
                                    <div class="mb-3">
                                        <select class="form-control aiz-selectpicker" data-live-search="true" data-placeholder="{{translate('Select your country')}}" name="country" required>
                                            @foreach (\App\Models\Country::where('status', 1)->get() as $key => $country)
                                                <option value="{{ $country->name }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @if (\App\Models\BusinessSetting::where('type', 'shipping_type')->first()->value == 'area_wise_shipping')
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>{{ translate('City')}}</label>
                                    </div>
                                    <div class="col-md-10">
                                        <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="city" required>
                                            @foreach (\App\Models\City::get() as $key => $city)
                                                <option value="{{ $city->name }}">{{ $city->getTranslation('name') }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>{{ translate('City')}}</label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control mb-3" placeholder="{{ translate('Your City')}}" name="city" value="" required>
                                    </div>
                                </div>
                            @endif
                            <div class="row">
                                <label class="col-md-2 col-form-label">{{ translate('Postal code') }}</label>
                                <div class="col-md-10">
                                    <input type="text" class="form-control mb-3" placeholder="{{ translate('Your Postal Code')}}" name="postal_code" value="" required>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-2 col-form-label">{{ translate('Phone') }}</label>
                                <div class="col-md-10">
                                    <input type="text" class="form-control mb-3" placeholder="{{ translate('Your phone number')}}" name="phone" value="" required>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@section('script')
<script type="text/javascript">
$('#list_menu').click();
    function add_new_address(){
        $('#new-address-modal').modal('show');
    }

    $('.new-email-verification').on('click', function() {
        $(this).find('.loading').removeClass('d-none');
        $(this).find('.default').addClass('d-none');
        var email = $("input[name=email]").val();

        $.post('{{ route('user.new.verify') }}', {_token:'{{ csrf_token() }}', email: email}, function(data){
            data = JSON.parse(data);
            $('.default').removeClass('d-none');
            $('.loading').addClass('d-none');
            if(data.status == 2)
                AIZ.plugins.notify('warning', data.message);
            else if(data.status == 1)
                AIZ.plugins.notify('success', data.message);
            else
                AIZ.plugins.notify('danger', data.message);
        });
    });

    $('#document_type').change(function(){
        var type = $(this).val();
        if(type == 'Nid'){
      $('#nid_section').show()
      $('#bc_section').hide()
        $("#nid").prop('required',true);
        $("#b_cert").prop('required',false);
         $("#passport").prop('required',false);
      $('#passport_section').hide()
        }else if(type == 'Birth Certificate'){
            $('#nid_section').hide()
      $('#bc_section').show()
      $('#passport_section').hide()
        $("#b_cert").prop('required',true);
       $("#nid").prop('required',false);
        $("#passport").prop('required',false);
        }else if(type == 'Passport'){
            $('#nid_section').hide()
      $('#bc_section').hide()
      $('#passport_section').show()
        $("#passport").prop('required',true);
        $("#nid").prop('required',false);
        $("#b_cert").prop('required',false);
        }
    })
</script>
@endsection
