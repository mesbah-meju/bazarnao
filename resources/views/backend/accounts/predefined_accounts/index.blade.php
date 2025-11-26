@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
   <div class="row align-items-center">
      <div class="col-md-6">
         <h1 class="h3">{{ translate('Predefined Accounts') }}</h1>
      </div>
   </div>
</div>

<div class="card">
   <form class="" id="sort_opening_balances" action="" method="GET">
      <div class="card-header row gutters-5">
         <div class="col text-center text-md-left">
            <h5 class="mb-md-0 h6">{{ translate('Predefined Accounts') }}</h5>
         </div>

         <div class="col-md-2">
            <div class="form-group mb-0">
               <input type="text" class="form-control form-control-sm" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type & Enter') }}">
            </div>
         </div>
      </div>
   </form>
   <div class="card-body">
      <form class="form-horizontal" action="{{ route('predefined.accounts.store') }}" method="POST" enctype="multipart/form-data">
         @csrf

         <?php if ($fieldnames) {
            foreach ($fieldnames as $fields) { ?>
               <?php if ($fields != 'id' && $fields != 'created_at' && $fields != 'updated_at') { ?>
                  <div class="row form-group">
                     <label for="head_code" class="font-weight-600 col-sm-2"> <?php echo $fields; ?><i class="text-danger">*</i></label>

                     <div class="col-sm-3">
                        <select name="{{ $fields }}" id="{{ $fields }}" class="form-control aiz-selectpicker" data-live-search="true">
                           @foreach($allheads as $headCode => $headName)
                           <option value="{{ $headCode }}" {{ $fieldvalues && $fieldvalues->$fields == $headCode ? 'selected' : '' }}>
                              {{ $headName }}
                           </option>
                           @endforeach
                        </select>
                     </div>
                  </div>
         <?php }
            }
         } ?>
         <div class="row form-group">
            <div class="col-sm-5 text-right">
               <button type="submit" class="btn btn-success"><?php echo translate('Submit'); ?></button>
            </div>
            <div class="col-sm-7">
            </div>
         </div>
      </form>
   </div>
</div>

@endsection

@section('modal')
@include('modals.delete_modal')
@endsection

@section('script')
<script type="text/javascript">
   function sort_opening_balances(el) {
      $('#sort_opening_balances').submit();
   }
</script>
@endsection