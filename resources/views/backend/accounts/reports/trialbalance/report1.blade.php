@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Trial Balance') }}</h1>
        </div>
    </div>
</div>

<div class="card">
    <form id="sort_debit_vouchers" action="{{ route('trial-balance-report.index') }}" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Trial Balance') }}</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="dtpFromDate" class="form-label">{{ translate('From Date') }}</label>
                        <input type="text" name="dtpFromDate" class="form-control datepicker" value="{{ date('m/d/Y', strtotime($dtpFromDate)) }}">
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="dtpToDate" class="form-label">{{ translate('To Date') }}</label>
                        <input type="text" name="dtpToDate" class="form-control datepicker" value="{{ date('m/d/Y', strtotime($dtpToDate)) }}">
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="dtpToDate" class="form-label text-white">{{ translate('To Date') }}</label><br>
                        <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                        <button class="btn btn-md btn-info mr-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body printArea">
        <div class="table-responsive">
            <table width="100%">
                <caption class="text-center">
                    <table class="print-font-size" width="100%">
                        <tr>
                            <td align="left" width="33.333%">
                                <img src="{{ asset('public/assets/img/logo.png') }}" class="img-bottom-m print-logo" alt="logo"><br><br>
                            </td>
                            <td align="center" style="border-bottom: 2px #333 solid;" width="33.333%">
                                <strong>Bazar Nao Limited</strong><br>
                                4th Floor, AGM Chandrima, House 12, Road 08, Block J, Baridhara, Dhaka-1212.
                                <br>
                                info@bazarnao.com
                                <br>
                                +880 1969 906 699
                                <br>
                            </td>
                            <td align="right" width="33.333%">
                                <date> {{ translate('Date') }}: {{ date('d-M-Y') }} </date><br>
                            </td>
                        </tr>
                    </table>
                </caption>
                <caption class="text-center">
                    <strong><?php echo translate('Trial Balance')?>
                        <?php echo translate('Start Date');?> <?php echo $dtpFromDate; ?>
                        <?php echo translate('End Date');?> <?php echo $dtpToDate;?>
                    </strong>
                </caption>
            </table>
            <table width="99%" align="center" class="datatable table table-striped table-bordered table-hover general_ledger_report_tble" title="TriaBalanceReport<?php echo $dtpFromDate; ?><?php echo translate('to_date');?><?php echo $dtpToDate;?>">
                <thead>
                    <tr>
                        <th>Code </th>
                        <th>Account Name </th>
                        <th>Opening Balance <br/> Debit </th>
                        <th>Opening Balance <br/> Credit</th>
                        <th>Transational Balance <br/> Debit </th>
                        <th>Transational Balance <br/> Credit</th>
                        <th>Closing Balance <br/> Debit </th>
                        <th>Closing Balance <br/> Credit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($results)> 0) {
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
                            
                            $resultDebit = $result[0]->debit ?? '0';
                            $resultCredit = $result[0]->credit ?? '0';

                            if($result['head_type'] == 'A' || $result['head_type'] == 'E') { 
                                if($openings[$result['head_code']] !=0) {
                                    $totalOpenDebit += $openings[$result['head_code']];
                                    $copenDebit     += $openings[$result['head_code']];                                       
                                } 
                                $totalbalanceDebit   +=  $copenDebit + ($resultDebit - $resultCredit);
                            } else { 
                                if($openings[$result['head_code']] !=0) {
                                    $totalOpenCredit += $openings[$result['head_code']];
                                    $copenCredit     += $openings[$result['head_code']];
                                } 
                                $totalbalanceCredit  +=  $copenCredit + ($resultCredit - $resultDebit);  
                            }
                                                            
                            $totalCurentDebit   += $resultDebit; 
                            $totalCurentCredit  += $resultCredit;  
                                                        
                            $totalCloseDebit   += $totalbalanceDebit;
                            $totalCloseCredit  += $totalbalanceCredit; 
                        ?>
                        <tr>
                            <td>
                                <a href="javascript:" onClick=" return showTranDetail('<?php echo $result['head_code'];?>','<?php echo $dtpFromDate; ?>','<?php echo $dtpToDate;?>');"><?php echo $result['head_code'];?></a>
                            </td>
                            <td> <?php echo $result['head_name'];?></td>
                            <td> <?php if($result['head_type'] == 'A' || $result['head_type'] == 'E') { echo $currency. ' '. number_format($openings[$result['head_code']],2,'.',',');} else { echo $currency. ' '. '0.00';}?>
                            </td>
                            <td><?php if($result['head_type'] == 'L' || $result['head_type'] == 'I') { echo $currency. ' '. number_format($openings[$result['head_code']],2,'.',',');} else { echo $currency. ' '. '0.00';}?>
                            </td>
                            <td><?php echo $currency. ' '. $resultDebit ;?> </td>
                            <td><?php echo $currency. ' '. $resultCredit ;?> </td>
                            <td><?php echo $currency. ' '. number_format($totalbalanceDebit,2,'.',',');?> </td>
                            <td><?php echo $currency. ' '. number_format($totalbalanceCredit,2,'.',',');?> </td>
                        </tr>
                    <?php } $ix++; }  ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" align="right"> <strong><?php echo translate('Total')?> </strong></th>
                        <th><strong><?php echo $currency. ' '. number_format($totalOpenDebit,2,'.',',');?>
                            </strong></th>
                        <th><strong><?php echo $currency. ' '. number_format($totalOpenCredit,2,'.',',');?>
                            </strong></th>
                        <th><strong><?php echo $currency. ' '. number_format($totalCurentDebit,2,'.',',');?>
                            </strong></th>
                        <th><strong><?php echo $currency. ' '. number_format($totalCurentCredit,2,'.',',');?>
                            </strong></th>
                        <th><strong><?php echo $currency. ' '. number_format($totalCloseDebit,2,'.',',');?>
                            </strong></th>
                        <th><strong><?php echo $currency. ' '. number_format($totalCloseCredit,2,'.',',');?>
                            </strong></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div id="all_transation_view"></div>
    </div>
</div>
@endsection

@section('script')
<script>
    function showTranDetail(coaid, sdate, edate) {
        $.post('{{ route("trial-balance.detail") }}', {
            _token      : AIZ.data.csrf, 
            coaid       : coaid, 
            sdate       : sdate,
            edate       : edate
        }, function(data){
            $('#all_transation_view').html(data);
        });
    }
</script>
@endsection