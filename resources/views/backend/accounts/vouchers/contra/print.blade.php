<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Contra Voucher</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page {
            size: auto;
            margin: 10mm;
        }

        @media print {
            body {
                margin: 0 !important;
                padding: 20px !important;
                font-family: 'Arial', sans-serif !important;
                color: #000000 !important;
            }

            caption {
                color: #000000 !important;
            }

            .no-print {
                display: none !important;
            }

            .container {
                max-width: 100% !important;
                padding-right: 15px;
                padding-left: 15px;
            }

            .company-logo {
                height: 100px !important;
                margin-bottom: 20px !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin: 15px 0 !important;
            }

            th, td {
                padding: 8px !important;
                font-size: 14px !important;
            }
        }
    </style>
</head>

<body class="container" onload="initPrint()">
    <table border="0" width="100%">
        <caption class="text-center">
            <table class="print-font-size" width="100%">
                <tr>
                    <td align="left" style="border-bottom: 2px #333 solid;" width="33.333%">
                        <img src="{{ asset('public/assets/img/logo.png') }}" class="img-bottom-m print-logo"
                            alt="logo"><br>
                    </td>
                    <td align="center" style="border-bottom: 2px #333 solid;" width="33.333%">
                        <h5><strong>BAZAR NAO LTD.</strong></h5>
                        Sukhnir, Flat: B2, House: 33, Road: 1/A
                        <br>Block: J, Baridhara, Dhaka-1212
                    </td>
                    <td align=" right" style="border-bottom: 2px #333 solid;" width="33.333%">
                        <label class="font-weight-600 mb-0">{{ translate('Voucher No') }}</label> :
                        {{ $contra->voucher_no }}<br>
                        <label class="font-weight-600 mb-0">{{ translate('date') }}</label> :
                        {{ date('d/m/Y', strtotime($contra->voucher_date)) }}
                    </td>
                </tr>
            </table>
        </caption>
        <caption class="text-center">
            <h6><strong><u class="pt-4">Contra Voucher</u></strong></h6>
        </caption>
    </table>
    <table border="1" width="100%">
        <thead>
            <tr>
                <th class="text-center">{{ translate('Particulars') }}</th>
                <th class="text-center">{{ translate('Debit') }}</th>
                <th class="text-center">{{ translate('Credit') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_debit = 0;
                $total_credit = 0;
            @endphp

            @if (!empty($contra))
                @foreach ($contra->vouchers as $voucher)
                    @php
                        $total_debit = $voucher->debit + ($voucher->debit == '0.00' ? $voucher->credit : 0);
                        $total_credit = $voucher->credit + ($voucher->credit == '0.00' ? $voucher->debit : 0);
                    @endphp
                    <tr>
                        <td>
                            <strong
                                style="font-size: 15px;">{{ $voucher->coa->head_name . ($voucher->sub_type != 1 ? '(' . $voucher->subcode->name . ')' : '') }}</strong><br>
                            <span>{{ $voucher->ledger_comment }}</span>
                        </td>
                        <td class="text-right">{{ $voucher->debit }}</td>
                        <td class="text-right">{{ $voucher->credit }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" class="text-center text-danger">
                        {{ translate('Data is not available') }}
                    </td>
                </tr>
            @endif
            <tr>
                <td class="text-left"><strong style="font-size: 15px;">{{ $contra->rev_coa->head_name }}</strong></td>
                <td class="text-right">
                    {{ $voucher->debit == '0.00' ? number_format($voucher->credit, 2) : '0.00' }}</td>
                <td class="text-right">
                    {{ $voucher->credit == '0.00' ? number_format($voucher->debit, 2) : '0.00' }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th class="text-right"><?php echo translate('total'); ?></th>
                <th class="text-right"><?php echo number_format($total_credit, 2); ?></th>
                <th class="text-right"><?php echo number_format($total_credit, 2); ?></th>
            </tr>
            <tr>
            </tr>
            <tr>
                <th class="" colspan="3"><?php echo translate('remark'); ?> : <?php echo $contra->narration; ?></th>
            </tr>
        </tfoot>
    </table>
    <div class="form-group row mt-5">
        <label for="name" class="col-lg-3 col-md-3 col-sm-3 col-form-label text-center">
            <div><b>{{ translate('Prepared By') }}: {{ $contra->user->name }}</b></div>
        </label>
        <label for="name" class="col-lg-3 col-md-3 col-sm-3 col-form-label text-center">
            <div><b>{{ translate('Checked By') }}</b></div>
        </label>
        <label for="name" class="col-lg-3 col-md-3 col-sm-3 col-form-label text-center">
            <div><b>{{ translate('Authorised By') }}</b></div>
        </label>
        <label for="name" class="col-lg-3 col-md-3 col-sm-3 col-form-label text-center">
            <div><b>{{ translate('Pay By') }}</b></div>
        </label>
    </div>
    <script>
        function initPrint() {
            window.print();
            window.onafterprint = function() {
                window.close();
            };
        }
    </script>
</body>

</html>
