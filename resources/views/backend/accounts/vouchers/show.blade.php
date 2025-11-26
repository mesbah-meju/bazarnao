@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{ translate('Contra Voucher') }}</h1>
		</div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('debit-vouchers.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Print')}}</span>
            </a>
        </div>
	</div>
</div>

<div class="card">
    <div class="card-body">
        <div class="col-md-12" id="vaucherPrintArea">
            <div class="row pb-3 voucher-center">
                <div class="col-md-3">
                    <img src="{{ asset('public/assets/img/logo.png') }}" alt="Logo" height="40px"><br><br>
                </div>
                <div class="col-md-6 text-center">
                    <h2>Bazarnao</h2>
                    <strong><u class="pt-4">Contra Voucher</u></strong>
                </div>
                <div class="col-md-3">
                    <div class="pull-right" style="margin-right:20px;">
                        <label class="font-weight-600 mb-0">{{ translate('Voucher No') }}</label> :
                        {{ $contra->voucher_no }}<br>
                        <label class="font-weight-600 mb-0">{{ translate('date') }}</label> :
                        {{ date('d/m/Y', strtotime($contra->voucher_date)) }}
                    </div>
                </div>
            </div>

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

                    @if (!empty($contra))
                    @foreach ($contra->vouchers as $voucher)
                    @php
                        $total_debit = $voucher->debit + ($voucher->debit == '0.00' ? $voucher->credit : 0);
                        $total_credit = $voucher->credit + ($voucher->credit == '0.00' ? $voucher->debit : 0);
                    @endphp
                    <tr>
                        <td>
                            <strong style="font-size: 15px;">{{ $voucher->coa->head_name . ($voucher->sub_type != 1 ? '(' . $voucher->subcode->name . ')' : ''); }}</strong><br>
                            <span>{{ $voucher->ledger_comment }}</span>
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
                        <td class="text-left"><strong style="font-size: 15px;">{{ $contra->rev_coa->head_name }}</strong></td>
                        <td class="text-right">{{ ($voucher->debit == '0.00' ? number_format($voucher->credit, 2) : '0.00') }}</td>
                        <td class="text-right">{{ ($voucher->credit == '0.00' ? number_format($voucher->debit, 2) : '0.00') }}</td>
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
                        <th class="" colspan="3"><?php echo translate('remark') ?> : <?php echo $contra->narration; ?></th>
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
        </div>
    </div>
</div>
@endsection