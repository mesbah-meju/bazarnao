<link
    href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
    rel="stylesheet">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&display=swap"
    rel="stylesheet">
<style>
    *,
    ::after,
    ::before {
        box-sizing: border-box;
    }

    body {
        padding: 0;
        font-family: Lato, "Helvetica Neue", Arial, Helvetica, sans-serif;
    }

    .table2,
    th,
    td {
        border: 1px solid;
    }

    .table2 {
        width: 100%;
        border-collapse: collapse;
    }
</style>
<div style="width: 100%;">
    <div class="panel panel-bd">
        <div id="printableArea">
            <div class="panel-body">
                <div bgcolor='#e4e4e4' text='#ff6633' link='#666666' vlink='#666666' alink='#ff6633' class="phdiv">
                    <div style="width: 33.33333333%;float: right;">
                        <h2 style="margin-top: 0!important;"><?php echo 'Due Invoice'; ?></h2>
                        <div>
                            <abbr class="font-bold">
                                <?php echo translate('Invoice No'); ?>: <span dir="ltr"></span>
                            </abbr>
                            <?php echo $payment_info[0]['code']; ?>
                        </div>
                        <div style="margin-bottom: 15px!important;">
                            <abbr class="font-bold"><?php echo translate('Billing Date'); ?></abbr>
                            <?php echo date('d-M-Y', strtotime($payment_info[0]['created_at'])); ?>
                            <br>


                            <abbr class="font-bold"><?php echo translate('Invoice Time'); ?>:</abbr>
                            <?php echo date('H:i:s', strtotime($payment_info[0]['created_at'])); ?>
                        </div>

                        <span
                            style="color: #45c203;background-color: transparent; border: 2px solid #45c203;margin-right: 15px!important;">
                            <span style="padding: 4px;font-size: 12px;">
                                <?php echo translate('Billing To'); ?>
                            </span>
                        </span>

                        {{-- <address style="margin-top: 10px;" class="">
                            <strong class=""><?php echo $customer_info[0]['customer_name']; ?> </strong><br>
                            <?php echo $customer_info[0]['customer_address']; ?>
                            <br>
                            <?php if ($customer_info[0]['customer_mobile']) { ?>
                            <abbr style="font-weight: bold;"><?php echo translate('Mobile'); ?>: </abbr>
                            <?php echo $customer_info[0]['customer_mobile']; ?>
                            <br>
                            <?php } ?>
                            <?php if ($customer_info[0]['customer_email']) { ?>
                            <abbr style="font-weight: bold;"><?php echo translate('Email'); ?>: </abbr>
                            <?php echo $customer_info[0]['customer_email']; ?>
                            <br>
                            <?php } ?>
                        </address> --}}

                    </div>
                    <div style="width: 33.33333333%;"></div>
                    <div style="text-align: left !important;width: 33.33333333%;margin-bottom: 30px !important;">
                        {{-- <img src="<?php
                        if (isset($setting->invoice_logo)) {
                            echo base_url() . $setting->invoice_logo;
                        }
                        ?>"
                            style="width: 200px;
                                height: auto;
                                max-height: 70px;margin-bottom: 10px;"
                            alt=""> --}}
                        <br>
                        <span
                            style="color: #45c203;background-color: transparent; border: 2px solid #45c203;margin-right: 15px!important;">
                            <span style="padding: 4px;font-size: 12px;">
                                <?php echo translate('Billing From'); ?>
                            </span>
                        </span>
                        <address style="margin-top:10px">
                            <strong style="font-size: 17pt;font-weight: bold;">Bazarnao Ltd</strong><br>
                            <span class="comp-web">Dhaka, Bangladesh</span><br>
                            <abbr style="font-weight: bold;"><?php echo translate('Mobile'); ?>: </abbr>
                            01759724419<br>
                            <abbr><b><?php echo translate('Email'); ?>:</b></abbr>
                            bazarnao@gmail.com<br>
                            <abbr><b><?php echo translate('Website'); ?>:</b></abbr>
                            <span class="comp-web">bazarnao.com</span><br>
                        </address>
                    </div>

                    <div style="margin-bottom: 20px;width: 100%;">
                        <table width="100%" class="table2">
                            <tr>
                                <th><?php echo translate('Voucher No'); ?></th>
                                <th> <?php echo translate('Payment Type'); ?></th>
                                <th> <?php echo translate('Amount'); ?></th>
                                <th><?php echo translate('Remark'); ?></th>
                            </tr>
                            <tr>
                                <td style="text-align: center;"><?php echo $payment_info[0]['voucher_no']; ?></td>
                                <td style="text-align: center;"><?php echo 'Receive'; ?></td>
                                <td style="text-align: center;"><?php echo $payment_info[0]['sumcredit']; ?></td>
                                <td style="text-align: center;"><?php echo $payment_info[0]['ledger_comment']; ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <tr>
                        <td>
                            <?php echo translate('Paid By'); ?>: <?php echo 'Admin'; ?>
                            <div class="">
                                <?php echo translate('Signature'); ?>:
                            </div>
                            Powered By: <a href="javascript:void(0)"><?php echo '4axiz IT Limited'; ?></a>
                        </td>
                    </tr>
                </div>
            </div>
        </div>
    </div>
</div>
