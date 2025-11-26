@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{ translate('Loan & Amortizations') }}</h1>
		</div>
	</div>
</div>

<div class="card">
    <form class="" id="sort_loans" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Loan Details') }}</h5>
            </div>
            <div class="col text-center text-md-right">
                <a class="btn btn-soft-primary btn-sm text-dark" href="javascript:void(0);" onclick="return showReSchedule('{{ $loan->id }}');" title="{{ translate('Re-Schedule') }}">
                    <i class="las la-redo-alt"></i> {{ translate('Re-Schedule') }}
                </a>
            </div>
        </div>
    </form>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <tbody>
                <tr>
                    <td><strong>Borrower Name:</strong></td>
                    <td>
                        @if($loan->bank)
                            {{ $loan->bank->bank_name }}
                        @else
                            {{ translate('N/A') }}
                        @endif
                    </td>
                    <td><strong>Loan Amount:</strong></td>
                    <td>{{ number_format($loan->loan_amount, 2) }}</td>
                    <td><strong>Loan Term:</strong></td>
                    <td>{{ $loan->loan_term }} years</td>
                </tr>
                <tr>
                    <td><strong>Interest Rate:</strong></td>
                    <td>{{ $loan->interest_rate }}%</td>
                    <td><strong>Start Date:</strong></td>
                    <td>{{ $loan->start_date }}</td>
                    <td><strong>Status:</strong></td>
                    <td>{{ $loan->status }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <form class="" id="sort_loans" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Amortization Schedules') }}</h5>
            </div>
            
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type & Enter') }}">
                </div>
            </div>
        </div>
    </form>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('Month') }}</th>
                    <th>{{ translate('Interest Rate') }}</th>
                    <th>{{ translate('Payment') }}</th>
                    <th>{{ translate('Principal') }}</th>
                    <th>{{ translate('Interest') }}</th>
                    <th>{{ translate('Balance') }}</th>
                    <th>{{ translate('Status') }}</th>
                    <th class="text-right" width="35px">{{ translate('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $first_unpaid = $loan->schedules->firstWhere('status', 'Unpaid');
                @endphp
                @foreach($loan->schedules as $key => $schedule)
                    @php
                        $date = date('Y-m-d', strtotime("+$schedule->month month", strtotime($loan->start_date)));
                    @endphp
                    <tr>
                        <td>{{ ($key+1) }}</td>
                        <td>{{ date('F Y', strtotime("-1 month", strtotime($date))) }}</td>
                        <td>{{ number_format($schedule->interest_rate, 2) }}%</td>
                        <td>{{ number_format($schedule->payment, 2) }}</td>
                        <td>{{ number_format($schedule->principal, 2) }}</td>
                        <td>{{ number_format($schedule->interest, 2) }}</td>
                        <td>{{ number_format($schedule->balance, 2) }}</td>
                        <td>
                            @if ($schedule->status == 'Unpaid')
                                <span class="badge badge-warning w-auto">{{ $schedule->status }}</span>
                            @else
                                <span class="badge badge-success w-auto">{{ $schedule->status }}</span>
                            @endif
                        </td>
                        <td class="text-right">
                            @if ($schedule->status == 'Unpaid' && $schedule->id == optional($first_unpaid)->id)
                            <a class="btn btn-soft-warning btn-sm text-dark" href="{{ route('loans.installment', $schedule->id) }}" title="{{ translate('Unpaid') }}">
                                <i class="las la-plus"></i>
                            </a>
                            @elseif ($schedule->status == 'Unpaid')
                            <a class="btn btn-soft-warning btn-sm disabled text-dark" href="javascript:void(0)" title="{{ translate('Unpaid') }}">
                                <i class="las la-plus"></i>
                            </a>
                            @else
                            <a class="btn btn-soft-success btn-sm disabled text-dark" href="javascript:void(0)" title="{{ translate('Paid') }}">
                                <i class="las la-check"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('modal')
    @include('modals.common_modal')
@endsection

@section('script')
<script type="text/javascript">
    function sort_loans(el){
        $('#sort_loans').submit();
    }

    function showReSchedule(loan_id) {
        $('#common-modal .modal-title').html('');
        $('#common-modal .modal-body').html('');

        var name = 'Re-Schedule';
        $.ajax({
            type: "GET",
            url: "{{ route('loans.reschedule', ':id') }}".replace(':id', loan_id),
            data: {},
            success: function(data) {
                $('#common-modal .modal-title').html(name);
                $('#common-modal .modal-body').html(data);
                $('#common-modal').modal('show');
            }
        });
    }
</script>
@endsection

