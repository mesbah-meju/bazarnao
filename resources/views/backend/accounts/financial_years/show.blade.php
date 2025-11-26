<form class="form-horizontal" action="{{ route('school.classes.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-row">
        <div class="form-group col-md-8">
            <label class="from-label fs-13" for="financial_id">{{translate('Campus')}} <span class="text-danger">*</span></label>
            <div class="">
                <select name="campus_id" id="campus_id" class="form-control aiz-selectpicker" onchange="campusWiseShiftForClassCreate(this.value)" required>
                    <option value="">Select a campus</option>
                    @foreach($campuses as $campus)
                    <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    
    <div class="form-group mb-3 text-right">
        <button type="submit" class="btn btn-primary">{{translate('Submit')}}</button>
    </div>
</form>