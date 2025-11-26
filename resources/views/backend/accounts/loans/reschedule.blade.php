
<form action="{{ route('loans.amortization.reschedule', $loan->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="form-group col-md-4 mb-3">
            <label for="bank_id" class="form-label">Bank Name</label>
            <select name="bank_id" id="bank_id" class="form-control aiz-selectpicker" data-live-search="true" disabled>
                <option value="">Select Bank</option>
                @foreach ($banks as $key => $bank)
                    <option value="{{ $bank->id }}" @selected($loan->bank_id == $bank->id)> {{ $bank->bank_name }}({{ $bank->ac_number }})</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-4 mb-3">
            <label for="loan_amount" class="form-label">Loan Amount (৳)</label>
            <input type="number" name="loan_amount" id="loan_amount" class="form-control" value="{{ $loan->loan_amount }}" readonly>
        </div>

        <div class="form-group col-md-4 mb-3">
            <label for="loan_term" class="form-label">Loan Term (Years)</label>
            <input type="number" name="loan_term" id="loan_term" class="form-control" value="{{ $loan->loan_term }}" readonly>
        </div>

        <div class="form-group col-md-4 mb-3">
            <label for="interest_rate" class="form-label">Interest Rate (%)</label>
            <input type="number" step="0.01" name="interest_rate" id="interest_rate" class="form-control" value="{{ $loan->interest_rate }}" readonly>
        </div>

        <div class="form-group col-md-4 mb-3">
            <label for="reshedule_month" class="form-label">Re-Schedule Month</label>
            <select name="reshedule_month" id="reshedule_month" class="form-control aiz-selectpicker" data-live-search="true" required onchange="get_reschedule_balance(this.value)">
                <option value="">Select Month</option>
                @foreach ($months as $key => $month)
                    <option value="{{ $key }}">{{ $month }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-4 mb-3">
            <label for="reshedule_amount" class="form-label">Re-Schedule Amount</label>
            <input type="number" name="reshedule_amount" id="reshedule_amount" class="form-control" value="" required readonly>
        </div>

        <div class="form-group col-md-4 mb-3">
            <label for="reshedule_interest_rate" class="form-label">Re-Schedule Interest Rate (%)</label>
            <input type="number" step="0.01" name="reshedule_interest_rate" id="reshedule_interest_rate" class="form-control" value="" required>
        </div>
    </div>
    <div class="form-group mb-3 text-right">
        <button type="submit" class="btn btn-primary">{{ translate('Submit') }}</button>
    </div>
</form>

<script>
    function get_reschedule_balance(month) {
        $.post('{{ route("loans.amortization.balance") }}', {
            _token  : AIZ.data.csrf, 
            loan_id : {{ $loan->id }},
            month   : month
        }, function(data){
            alert(data);
            $('#reshedule_amount').val(data);
        });
    }
</script>
