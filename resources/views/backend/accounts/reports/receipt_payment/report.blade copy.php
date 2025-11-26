@extends('backend.layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header row gutters-5">
                    <div class="col text-center text-md-left">
                        <h5 class="mb-md-0 h6">{{ translate('Receipt & Payment') }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('receipt-payment-report.report') }}" method="POST" class="form-inline col-md-12">
                        @csrf

                        <div class="form-group col-md-4">
                            <label for="date1">{{ translate('From') }}:</label>
                            <input type="date" id="date1" name="dtpFromDate" class="form-control ml-2 aiz-datepicker" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="date2">{{ translate('To') }}:</label>
                            <input type="date" id="date2" name="dtpToDate" class="form-control ml-2 aiz-datepicker" required>
                        </div>

                        <div class="row col-md-4">

                            <label for="bank ">{{ translate('type') }}:</label>

                            <div class="form-check ml-4">
                                <input class="form-check-input" type="radio" name="reportType" id="reportType1" value="Accrual Basis" checked>
                                <label class="form-check-label" for="reportType">
                                    Accrual Basis
                                </label>
                            </div>
                            <div class="form-check ml-3">
                                <input class="form-check-input" type="radio" name="reportType" id="reportType2" value="Cash Basis">
                                <label class="form-check-label" for="reportType">
                                    Cash Basis
                                </label>
                            </div>

                        </div>
                        <div class="row col-12 d-flex justify-content-end mt-2">
                            <button type="submit" class="btn btn-success ml-2">{{ translate('Find') }}</button>
                        </div>

                    </form>
                </div>

                <div class="card-body mt-4 border border-dark" id="printableArea">
                    <div class="row pb-3">
                        <div style="text-align: center; padding-top: 10px;" class="col-md-3">
                            <img src="{{ asset('public/assets/img/logo.png') }}" alt="Logo" height="40px">
                        </div>
                        <div style="text-align: center; padding-top: 30px;" class="col-md-6">
                            <h4>Receipt & Payment Report</h4>
                        </div>
                        <div style="text-align: center; padding-top: 35px;" class="col-md-3">
                            <label style="font-weight: 600;">{{ translate('Date') }}</label>: {{ date('d/m/Y') }}
                        </div>
                    </div>


                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" width="99%">
                                <thead class="table-bordered">
                                    <tr>
                                        <th width="60%">{{ translate('particulars') }}</th>
                                        <th class="profitamount" width="40%">{{ translate('Balance') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td height="70" colspan="2"><strong>{{ translate('Opening Balance') }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>{{ translate('Cash in hand') }}</td>
                                        <td class="profitamount">{{ $currency . ' ' . number_format($cashOpening, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ translate('Cash bank') }}</td>
                                        <td class="profitamount">{{ $currency . ' ' . number_format($bankOpening, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ translate('Advance') }}</td>
                                        <td class="profitamount">{{ $currency . ' ' . number_format($advOpening, 2) }}</td>
                                    </tr>

                                    <tr>
                                        <td height="70" colspan="2"><strong>{{ translate('Receipt') }}</strong></td>
                                    </tr>

                                    @php $gtotal = 0; @endphp
                                    @if(count($receiptitems) > 0)
                                    @foreach($receiptitems as $receiptitem)
                                    <tr>
                                        <td style="padding-left: 80px;">{{ $receiptitem['headName'] }}</td>
                                        <td></td>
                                    </tr>
                                    @if(count($receiptitem['innerHead']) > 0)
                                    @foreach($receiptitem['innerHead'] as $inner)
                                    <tr>
                                        <td>{{ $inner['headName'] }}</td>
                                        <td class="profitamount">{{ $currency . ' ' . number_format($inner['credit'], 2) }}</td>
                                    </tr>
                                    @php $gtotal += $inner['credit']; @endphp
                                    @endforeach
                                    @endif
                                    @endforeach
                                    <tr>
                                        <td><strong>{{ translate('Total') }}</strong></td>
                                        <td class="profitamount"><strong>{{ $currency . ' ' . number_format($gtotal, 2) }}</strong></td>
                                    </tr>
                                    @endif

                                    <tr>
                                        <td><strong>{{ translate('Grand Total') }}</strong></td>
                                        <td class="profitamount"><strong>{{ $currency . ' ' . number_format(($gtotal + $cashOpening + $bankOpening + $advOpening), 2) }}</strong></td>
                                    </tr>

                                    <tr>
                                        <td height="70" colspan="2"><strong>{{ translate('Payments') }}</strong></td>
                                    </tr>

                                    @php $pgtotal = 0; @endphp
                                    @if(count($paymentitems) > 0)
                                    @foreach($paymentitems as $paymentitem)
                                    <tr>
                                        <td style="padding-left: 80px;">{{ $paymentitem['headName'] }}</td>
                                        <td></td>
                                    </tr>
                                    @if(count($paymentitem['innerHead']) > 0)
                                    @foreach($paymentitem['innerHead'] as $inner)
                                    <tr>
                                        <td>{{ $inner['headName'] }}</td>
                                        <td class="profitamount">{{ $currency . ' ' . number_format($inner['debit'], 2) }}</td>
                                    </tr>
                                    @php $pgtotal += $inner['debit']; @endphp
                                    @endforeach
                                    @endif
                                    @endforeach
                                    <tr>
                                        <td><strong>{{ translate('Total') }}</strong></td>
                                        <td class="profitamount"><strong>{{ $currency . ' ' . number_format($pgtotal, 2) }}</strong></td>
                                    </tr>
                                    @endif

                                    <tr>
                                        <td height="70" colspan="2"><strong>{{ translate('Closing Balance') }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>{{ translate('Cash in hand') }}</td>
                                        <td class="profitamount">{{ $currency . ' ' . number_format($cashClosing, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ translate('Cash bank') }}</td>
                                        <td class="profitamount">{{ $currency . ' ' . number_format($bankClosing, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ translate('Advance') }}</td>
                                        <td class="profitamount">{{ $currency . ' ' . number_format($advClosing, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ translate('Grand Total') }}</strong></td>
                                        <td class="profitamount"><strong>{{ $currency . ' ' . number_format(($pgtotal + $advClosing + $bankClosing + $cashClosing), 2) }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table" width="100%">
                                <tr class="mt-3">
                                    <td style="padding: 3.50rem; vertical-align: top; border-top: none">
                                        <div class="border-top text-center">{{ translate('prepared_by') }}</div>
                                    </td>
                                    <td style="padding: 3.50rem; vertical-align: top;border-top:none">
                                        <div class="border-top text-center">{{ translate('checked_by') }}</div>
                                    </td>
                                    <td style="padding: 3.50rem; vertical-align: top; border-top: none">
                                        <div class="border-top text-center">{{ translate('authorised_by') }}</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="text-center mt-4 mb-4">
    <button class="btn btn-primary" onclick="printReport()">{{ translate('print') }}</button>
</div>
<script>
    function printReport() {
        const printableContent = document.getElementById("printableArea").innerHTML;
        const originalContent = document.body.innerHTML;

        document.body.innerHTML = printableContent;
        window.print();
        document.body.innerHTML = originalContent;
        location.reload();
    }
</script>
@endsection