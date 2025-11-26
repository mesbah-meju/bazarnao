@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{ translate('Credit Voucher') }}</h1>
		</div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('credit-vouchers.print', $credit->id) }}" target="_blank" class="btn btn-circle btn-info">
                <span>{{translate('Print')}}</span>
            </a>
        </div>
	</div>
</div>
<div class="card">
    <div class="card-body">
        <div class="col-md-12" id="vaucherPrintArea">
            <table border="0" width="100%">
                <caption class="text-center">
                    <table class="print-font-size" width="100%">
                        <tr>
                            <td align="left" style="border-bottom: 2px #333 solid;" width="33.333%">
                                <img src="{{ asset('public/assets/img/logo.png') }}" class="img-bottom-m print-logo" alt="logo"><br>
                            </td>
                            <td align="center" style="border-bottom: 2px #333 solid;" width="33.333%">
                                <h5><strong>BAZAR NAO LTD.</strong></h5>
                                Sukhnir, Flat: B2, House: 33, Road: 1/A
                                <br>Block: J, Baridhara, Dhaka-1212
                            </td>
                            <td align=" right" style="border-bottom: 2px #333 solid;" width="33.333%">
                                <label class="font-weight-600 mb-0">{{ translate('Voucher No') }}</label> :
                                {{ $credit->voucher_no }}<br>
                                <label class="font-weight-600 mb-0">{{ translate('date') }}</label> :
                                {{ date('d/m/Y', strtotime($credit->voucher_date)) }}
                            </td>
                        </tr>
                    </table>
                </caption>
                <caption class="text-center">
                    <h6><strong><u class="pt-4">Credit Voucher</u></strong></h6>
                </caption>
            </table>
            <table class="table table-bordered table-sm mt-2">
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

                    @if (!empty($credit))
                        @foreach ($credit->vouchers as $voucher)
                        @php
                            $total_debit += $voucher->debit;
                            $total_credit += $voucher->credit;
                        @endphp
                        <tr>
                            <td>
                                <strong style="font-size: 15px;">{{ $voucher->coa->head_name . ($voucher->sub_type != 1 ? '(' . $voucher->subcode->name . ')' : ''); }}</strong><br>
                                @if($voucher->sub_type == 4)
                                    @php
                                        $code = App\Models\Purchase::where('id', $voucher->reference_no)->value('purchase_no');
                                    @endphp
                                    <span>Purchase No: {{ $code }}</span><br>
                                    <span>{{ $voucher->ledger_comment }}</span>
                                @elseif($voucher->sub_type == 3)
                                    @php
                                        $code = App\Models\Order::where('id', $voucher->reference_no)->value('code');
                                    @endphp
                                    <span>Order No: {{ $code }}</span><br>
                                    <span>{{ $voucher->ledger_comment }}</span>
                                @endif
                            </td>
                            <td class="text-right">{{ $voucher->debit }}</td>
                            <td class="text-right">{{ $voucher->credit }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="text-center text-danger">{{ translate('Data is not available') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-left"><strong style="font-size: 15px;">{{ $credit->rev_coa->head_name }}</strong></td>
                        <td class="text-right">{{ number_format($total_credit, 2) }}</td>
                        <td class="text-right">0.00</td>
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
                        <th class="" colspan="3"><?php echo translate('remark') ?> : <?php echo $credit->narration; ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection