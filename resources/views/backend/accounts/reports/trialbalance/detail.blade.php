<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div id="printArea">
                <table border="0" width="100%">
                    <caption class="text-center">
                        <table class="print-font-size" width="100%">
                            <tr>
                                <td align="left" style="border-bottom: 2px #333 solid;" width="33.333%">
                                    <img src="{{ asset('public/assets/img/logo.png') }}" class="img-bottom-m print-logo" alt="logo"><br>
                                </td>
                                <td align="center" style="border-bottom: 2px #333 solid;" width="33.333%">
                                    <a class="btn btn-warning btn-sm" href="{{ route('general-ledger.print', ['warehouse_id' => $warehouse_id,'cmbCode' => $cmbCode,'dtpFromDate' => date('m/d/Y', strtotime($dtpFromDate)),'dtpToDate' => date('m/d/Y', strtotime($dtpToDate))]) }}" target="_blank">{{ translate('Print') }}</a>
                                    <h4><strong>BAZAR NAO LTD.</strong></h4>
                                    Sukhnir, Flat: B2, House: 33, Road: 1/A<br>Block: J, Baridhara, Dhaka-1212<br>
                                    info@bazarnao.com<br>
                                    +880 1969 906 699<br>
                                </td>
                                <td align="right" style="border-bottom: 2px #333 solid;" width="33.333%">
                                    <b>
                                        <label class="font-weight-600 mb-0">{{ translate('date') }}</label> : {{ date('d/m/Y') }}
                                    </b>
                                    <br>
                                    <b>
                                        <label class="font-weight-600 mb-0">{{ translate('Opening Balance') }}</label> : {{ number_format($prebalance,2,'.',',');}}
                                    </b>
                                    <br>
                                    @php
                                        $CurBalance = $prebalance;
                                    @endphp
                
                                    @foreach($HeadName2 as $key => $data2)
                                        @php 
                                            if($HeadName->head_type == 'A' || $HeadName->head_type == 'E') {
                                                if($data2->debit > 0) {
                                                    $CurBalance += $data2->debit;
                                                }
                                                if($data2->credit > 0) {
                                                    $CurBalance -= $data2->credit;
                                                }                          
                                            } else {                       
                                                if($data2->debit > 0) {
                                                    $CurBalance -= $data2->debit;
                                                }                          
                                                if($data2->credit > 0) {
                                                    $CurBalance += $data2->credit;
                                                }
                                            }
                                        @endphp
                                    @endforeach
                                    <b>
                                        <label class="font-weight-600 mb-0">{{ translate('Closing Balance') }}</label> : {{number_format($CurBalance,2,'.',',');}}
                                    </b>
                                </td>
                            </tr>
                        </table>
                    </caption>
                    <caption class="text-center">
                        <strong><u class="pt-4">{{ translate('General Ledger of') . ' ' . $ledger->head_name . ' on ' . date('d-m-Y', strtotime($dtpFromDate)) . ' To ' . date('d-m-Y', strtotime($dtpToDate)) }}</u></strong>
                    </caption>
                </table>
                <table width="99%" align="center" class="datatable table table-striped table-bordered table-hover general_ledger_report_tble">
                    <thead>
                        <tr>
                            <td height="25" width="5%"><strong><?php echo translate('SL'); ?></strong></td>
                            <td width="10%"><strong><?php echo translate('Date'); ?></strong></td>
                           

                            <td width="12%"><strong><?php echo translate('Account Head'); ?></strong></td>
                            <td width="12%"><strong><?php echo translate('Party Name'); ?></strong></td>
                            <td width="25%"><strong><?php echo translate('Particulars') ?></strong></td>

                            <td width="8%"><strong><?php echo translate('Voucher Name'); ?></strong></td>
                            <td width="10%"><strong><?php echo translate('Voucher No'); ?></strong></td>

                            <td width="10%" align="right"><strong><?php echo translate('Debit'); ?></strong></td>
                            <td width="10%" align="right"><strong><?php echo translate('Credit'); ?></strong></td>
                            <td width="10%" align="right"><strong><?php echo translate('Balance'); ?></strong></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $TotalCredit = 0;
                            $TotalDebit  = 0;
                            $CurBalance = $prebalance;
                            $openid = 1; 
                        ?>
                        <tr>
                            <td height="25"><?php echo $openid; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($dtpFromDate)); ?></td>

                            <td colspan="5" align="right"> <strong>Opening Balance</strong></td>
                            <td align="right"><?php echo $currency_symbol . ' ' . number_format(0, 2, '.', ','); ?></td>
                            <td align="right"><?php echo $currency_symbol . ' ' . number_format(0, 2, '.', ','); ?></td>
                            <td align="right"><strong><?php echo $currency_symbol . ' ' . number_format($prebalance, 2, '.', ','); ?></strong></td>
                        </tr>
                        <?php
                        foreach ($HeadName2 as $key => $data) { ?>
                            <tr>
                                <td height="25"><?php echo (++$key + $openid); ?></td>
                                <td><?php echo date('d-m-Y', strtotime($data->voucher_date)); ?></td>
                                
                                <td><?php echo $data->rev_coa->head_name; ?></td>
                                <td>
                                    @if($data->relvalue && $data->reltype)
                                        {{ $data->relvalue->name }}({{ $data->reltype->name }})
                                    @else
                                        {{ translate('N/A') }}
                                    @endif
                                </td>
                                <td><?php echo $data->ledger_comment; ?></td>

                                <td>
                                    <?php if ($data->voucher_type == 'DV') {
                                        echo 'Debit';
                                    } else if ($data->voucher_type == 'CV') {
                                        echo 'Credit';
                                    } else if ($data->voucher_type == 'JV') {
                                        echo 'Journal';
                                    } else {
                                        echo 'Contra';
                                    } ?>
                                </td>
                                <td><?php echo $data->voucher_no; ?></td>

                                <td align="right"><?php echo $currency_symbol . ' ' . number_format($data->debit, 2, '.', ','); ?></td>
                                <td align="right"><?php echo $currency_symbol . ' ' . number_format($data->credit, 2, '.', ','); ?></td>

                                <?php
                                $TotalDebit += $data->debit;
                                $TotalCredit += $data->credit;
                                if ($HeadName->head_type == 'A' || $HeadName->head_type == 'E') {
                                    if ($data->debit > 0) {
                                        $CurBalance += $data->debit;
                                    }
                                    if ($data->credit > 0) {
                                        $CurBalance -= $data->credit;
                                    }
                                } else {
                                    if ($data->debit > 0) {
                                        $CurBalance -= $data->debit;
                                    }
                                    if ($data->credit > 0) {
                                        $CurBalance += $data->credit;
                                    };
                                } ?>
                                <td align="right"><?php echo $currency_symbol . ' ' .  number_format($CurBalance, 2, '.', ','); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr class="table_data">
                            <td colspan="7" align="center"><strong><?php echo translate('Total') ?></strong></td>
                            <td align="right"><strong><?php echo  $currency_symbol . ' ' . number_format($TotalDebit, 2, '.', ','); ?></strong></td>
                            <td align="right"><strong><?php echo $currency_symbol . ' ' . number_format($TotalCredit, 2, '.', ','); ?></strong></td>
                            <td align="right"><strong><?php echo $currency_symbol . ' ' . number_format($CurBalance, 2, '.', ','); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>