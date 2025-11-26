@extends('backend.layouts.app')

@section('content')
<style> 
      marquee {
        width: 100%;
        padding: 5px 0;
        background-color: lightblue;
      }
    </style>
    
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Message')}}</h5>
            <?php
            echo "<marquee><h1>$message->message</h1></marquee>";
            ?>
            </div>

            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Send Wish Today')}}</h5>
                <a href="{{ URL::route('send_birth_day_wish') }}" onclick="return confirm('Are you sure?')" class="btn btn-primary">Send Wish</a>
            </div>
           
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('birthdaysms_store') }}" method="POST" enctype="multipart/form-data">
                	@csrf
                      
                    <div class="form-group row">
                        <label class="col-sm-2 col-from-label" for="name">{{translate('Update Message content')}}</label>
                        <div class="col-sm-10">
                            <textarea rows="8"  class="form-control aiz-text-editor" data-buttons='[["font", ["bold", "underline", "italic"]],["para", ["ul", "ol"]], ["insert", ["link", "picture"]],["view", ["undo","redo"]]]' name="content" required></textarea>
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                    </div>
                  
                  </form>
              </div>
        </div>
    </div>
</div>

@endsection
