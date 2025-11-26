@extends('backend.layouts.staff')
<style>
    tr,
    th,
    td {
        padding: 3px !important;
    }

    th {
        background: #AE3C86;
        color: #fff;
        font-weight: bold
    }

    li.nav-item {
        width: 100%;
    }

    .navbar-nav {
        width: 100%;
    }
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/2.4.85/css/materialdesignicons.css" rel="stylesheet" />

@section('content')
<div class="row gutters-10">
    <div class="col-lg-12">

        <div id="accordion">
        @include('backend.staff_panel.purchase_manager.purchase_manager_nav')
            <div class="card border-bottom-0">
                <div class="card-body">
                    <table class="table aiz-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th data-breakpoints="md">{{ translate('Supplier Name') }}</th>
                                <th data-breakpoints="md">{{ translate('Comment') }}</th>
                                <th data-breakpoints="md">{{ translate('Address') }}</th>
                                <th data-breakpoints="md">{{ translate('Contact Person') }}</th>
                                <th data-breakpoints="md">{{ translate('Phone') }}</th>
                            
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($suppliers_list as $key => $supplier)
                            <tr>
                                <td>
                                    {{ ($key+1) }}
                                </td>

                                <td>
                                    {{ $supplier->name }}
                                </td>
                                <td>
                                    {{ !empty($supplier->comment)?$supplier->comment: 'N/A' }}
                                </td>
                                <td>
                                    {{  $supplier->address }}
                                </td>
                                <td>
                                    {{  $supplier->contact_person }}
                                </td>

                                <td>
                                    {{  $supplier->phone }}
                                </td>
                              
                              
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>


</div>
@endsection
@section('script')
<script>
 function submitForm(url){
    $('#culexpo').attr('action',url);
    $('#culexpo').submit();
 }
</script>
@endsection