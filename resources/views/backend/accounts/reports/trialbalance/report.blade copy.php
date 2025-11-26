@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Trial Balance') }}</h1>
        </div>
    </div>
</div>
<?php
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
?>
<div class="card">
    <form id="sort_debit_vouchers" action="{{ route('trial-balance-report.index') }}" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Trial Balance') }}</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                @if(Auth::user()->user_type == 'admin')   
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label" for="warehouse_id">{{ translate('Warehouse') }} <span class="text-danger">*</span></label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">Select Warehouse</option>
                            @foreach (\App\Models\Warehouse::all() as $key => $warehouse)
                            <option  value="{{ $warehouse->id }}" {{ (isset($warehouse_id) && $warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @else
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label" for="warehouse_id">{{ translate('Warehouse') }} <span class="text-danger">*</span></label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">Select Warehouse</option>
                            @foreach ($warehouses as $key => $warehouse)
                            <option  value="{{ $warehouse->id }}" {{ (isset($warehouse_id) && $warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="dtpFromDate" class="form-label">{{ translate('From Date') }}</label>
                        <input type="text" name="dtpFromDate" class="form-control datepicker" value="{{ date('m/d/Y', strtotime($dtpFromDate)) }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="dtpToDate" class="form-label">{{ translate('To Date') }}</label>
                        <input type="text" name="dtpToDate" class="form-control datepicker" value="{{ date('m/d/Y', strtotime($dtpToDate)) }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="dtpToDate" class="form-label text-white">{{ translate('Button') }}</label><br>
                        <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                        <button class="btn btn-md btn-info mr-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body printArea">
        <div class="table-responsive">
            <table border="0" width="100%">
                <caption class="text-center">
                    <table class="print-font-size" width="100%">
                        <tr>
                            <td align="left" style="border-bottom: 2px #333 solid;" width="33.333%">
                                <img src="{{ asset('public/assets/img/logo.png') }}" class="img-bottom-m print-logo" alt="logo"><br>
                            </td>
                            <td align="center" style="border-bottom: 2px #333 solid;" width="33.333%">
                                <h4><strong>BAZAR NAO LTD.</strong></h4>
                                Sukhnir, Flat: B2, House: 33, Road: 1/A<br>Block: J, Baridhara, Dhaka-1212<br>
                                info@bazarnao.com<br>
                                +880 1969 906 699<br>
                                @if(!empty($warehouse_id))
                                    <strong>{{ \App\Models\Warehouse::find($warehouse_id)->name ?? '' }}</strong><br>
                                @else
                                    <strong>All Warehouse</strong><br>
                                @endif
                            </td>
                            <td align="right" style="border-bottom: 2px #333 solid;" width="33.333%">
                                <date> {{ translate('Date') }}: {{ date('d-M-Y') }} </date><br>
                            </td>
                        </tr>
                    </table>
                </caption>
                <caption class="text-center">
                    <strong><u class="pt-4">{{ translate('Trial Balance on') . ' '. date('d-m-Y', strtotime($dtpFromDate)) . ' To ' . date('d-m-Y', strtotime($dtpToDate)) }}</u></strong>
                </caption>
            </table>
            <table width="99%" align="center" class="datatable table table-striped table-bordered table-hover general_ledger_report_tble" title="TriaBalanceReport<?php echo $dtpFromDate; ?><?php echo translate('to_date');?><?php echo $dtpToDate;?>">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Code </th>
                        <th>Account Name</th>
                        <th>Debit </th>
                        <th>Credit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($results)> 0) {
                        $i= 1;
                        $ix= 0;
                        $totalOpenDebit=0;
                        $totalOpenCredit=0;
                        $totalCurentDebit=0;
                        $totalCurentCredit=0;
                        $totalCloseDebit=0;
                        $totalCloseCredit=0;
                        $totalbalanceDebit=0;
                        $totalbalanceCredit=0;
                        
                        foreach ($results as $key => $result)  {  
                            $totalbalanceDebit=0;
                            $totalbalanceCredit=0;

                            $copenDebit=0;
                            $copenCredit=0;
                            
                            $resultDebit = isset($result[0]) && isset($result[0]->debit) ? (float)$result[0]->debit : 0.0;
                            $resultCredit = isset($result[0]) && isset($result[0]->credit) ? (float)$result[0]->credit : 0.0;

                            $openingForHead = isset($openings[$result['head_code']]) ? (float)$openings[$result['head_code']] : 0.0;

                            if($result['head_type'] == 'A' || $result['head_type'] == 'E') { 
                                if($openingForHead != 0) {
                                    $totalOpenDebit += $openingForHead;
                                    $copenDebit     += $openingForHead;                                       
                                } 
                                $totalbalanceDebit   +=  $copenDebit + ($resultDebit - $resultCredit);
                            } else { 
                                if($openingForHead != 0) {
                                    $totalOpenCredit += $openingForHead;
                                    $copenCredit     += $openingForHead;
                                } 
                                $totalbalanceCredit  +=  $copenCredit + ($resultCredit - $resultDebit);  
                            }
                                                            
                            $totalCurentDebit   += $resultDebit; 
                            $totalCurentCredit  += $resultCredit;  

                            // Show only if there is a non-zero balance
                            if ($totalbalanceDebit != 0 || $totalbalanceCredit != 0) {
                                $totalCloseDebit   += $totalbalanceDebit;
                                $totalCloseCredit  += $totalbalanceCredit; 
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><a href="javascript:" onClick=" return showTranDetail('<?php echo $result['head_code'];?>', '<?php echo $warehouse_id; ?>', '<?php echo $dtpFromDate; ?>','<?php echo $dtpToDate;?>');"><?php echo $result['head_code'];?></a></td>
                            <td><?php echo $result['head_name'];?></td>
                            <td><?php echo $currency. ' '. number_format($totalbalanceDebit,2,'.',',');?> </td>
                            <td><?php echo $currency. ' '. number_format($totalbalanceCredit,2,'.',',');?> </td>
                        </tr>
                        <?php } // endif non-zero balance ?>
                    <?php } $ix++; }  ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-center"> <strong><?php echo translate('Total')?> </strong></th>
                        <th><strong><?php echo $currency. ' '. number_format($totalCloseDebit,2,'.',',');?></strong></th>
                        <th><strong><?php echo $currency. ' '. number_format($totalCloseCredit,2,'.',',');?></strong></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@section('modal')
    @include('modals.common_modal')
@endsection

@section('script')
<script>
    function showTranDetail(coaid, warehouse_id, sdate, edate) {
        $('#common-modal .modal-title').html('');
        $('#common-modal .modal-body').html('');

        var title = 'General Ledger';

        $.post('{{ route("trial-balance.detail") }}', {
            _token      : AIZ.data.csrf, 
            coaid       : coaid,
            warehouse_id: warehouse_id,
            sdate       : sdate,
            edate       : edate
        }, function(data){
            $('#common-modal .modal-title').html(title);
            $('#common-modal .modal-body').html(data);
            $('#common-modal .modal-dialog').removeClass('modal-lg');
            $('#common-modal .modal-dialog').addClass('modal-xl');
            $('#common-modal').modal('show');
        });
    }
</script>
@endsection