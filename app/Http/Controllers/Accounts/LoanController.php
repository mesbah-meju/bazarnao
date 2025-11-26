<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AmortizationSchedule;
use App\Models\Bank;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort_search = null;

        $loans = Loan::query();

        if ($request->search != null) {
            $sort_search = $request->search;
            $loans = $loans->where('bank_name', 'like', '%'.$request->search.'%');
        }

        $loans = $loans->paginate(30);
        return view('backend.accounts.loans.index', compact('loans', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $banks = Bank::where('status', 1)->get();
        return view('backend.accounts.loans.create', compact('banks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'bank_id' => 'required|integer|min:1',
            'loan_amount' => 'required|numeric|min:1',
            'loan_term' => 'required|integer|min:1',
            'interest_rate' => 'required|numeric|min:0',
            'start_date' => 'required|date',
        ]);

        $loan                   = new Loan();
        $loan->bank_id          = $request->bank_id;
        $loan->loan_amount      = $request->loan_amount;
        $loan->loan_term        = $request->loan_term;
        $loan->interest_rate    = $request->interest_rate;
        $loan->start_date       = date('Y-m-d', strtotime($request->start_date));

        if($loan->save()) {
            $this->generate_schedule($loan);

            flash(translate('Loan has been save successfully'))->success();
            return redirect()->route('loans.index'); 
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $loan = Loan::with('schedules')->findOrFail($id);
        return view('backend.accounts.loans.show', compact('loan'));
    }

    /**
     * Display the specified resource.
     */
    public function edit($id)
    {
        $loan = Loan::findOrFail($id);
        $banks = Bank::where('status', 1)->get();
        return view('backend.accounts.loans.edit', compact('loan','banks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'bank_id' => 'required|integer|min:1',
            'loan_amount' => 'required|numeric|min:1',
            'loan_term' => 'required|integer|min:1',
            'interest_rate' => 'required|numeric|min:0',
            'start_date' => 'required|date',
        ]);

        $loan                   = Loan::findOrFail($id);
        $loan->bank_id          = $request->bank_id;
        $loan->loan_amount      = $request->loan_amount;
        $loan->loan_term        = $request->loan_term;
        $loan->interest_rate    = $request->interest_rate;
        $loan->start_date       = date('Y-m-d', strtotime($request->start_date));
        
        if($loan->save()) {
            $loan->schedules()->delete();
            $this->generate_schedule($loan);

            flash(translate('Loan has been update successfully'))->success();
            return redirect()->route('loans.index'); 
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->schedules()->delete();

        if($loan->delete()) {
            flash(translate('Loan has been deleted successfully'))->success();
            return redirect()->route('loans.index'); 
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Generate the specified resource for storage.
     */
    private function generate_schedule(Loan $loan)
    {
        $loanAmount = $loan->loan_amount;
        $loanTerm = $loan->loan_term * 12;
        $interestRate = ($loan->interest_rate / 100) / 12;

        $monthlyPayment = $loanAmount * ($interestRate * pow(1 + $interestRate, $loanTerm)) / (pow(1 + $interestRate, $loanTerm) - 1);
        $balance = $loanAmount;

        for ($month = 1; $month <= $loanTerm; $month++) {
            $interest = $balance * $interestRate;
            $principal = $monthlyPayment - $interest;
            $balance -= $principal;

            $schedule                   = new AmortizationSchedule();
            $schedule->loan_id          = $loan->id;
            $schedule->interest_rate    = $loan->interest_rate;
            $schedule->month            = $month;
            $schedule->payment          = round($monthlyPayment, 2);
            $schedule->principal        = round($principal, 2);
            $schedule->interest         = round($interest, 2);
            $schedule->balance          = max(round($balance, 2), 0);
            $schedule->save();

            if ($balance <= 0) break;
        }
    }

    /**
     * Generate the specified resource for storage.
     */
    public function installment($id)
    {
        $schedule = AmortizationSchedule::findOrFail($id);
        $schedule->status = 'Paid';
        $schedule->save();

        if($schedule->save()) {
            $check = AmortizationSchedule::where('loan_id', $schedule->loan_id)->where('status', '!=', 'Paid')->count();
            if($check == 0) {
                $loan = Loan::findOrFail($schedule->loan_id);
                $loan->status = 'Completed';
                $loan->save();
            } else {
                $loan = Loan::findOrFail($schedule->loan_id);
                $loan->status = 'Processing';
                $loan->save();
            }
            flash(translate('Schedule has been paid successfully'))->success();
            return redirect()->route('loans.show', $schedule->loan_id); 
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Generate the specified resource for storage.
     */
    public function reschedule($id) {
        $loan = Loan::findOrFail($id);
        $banks = Bank::where('status', 1)->get();

        $months = array();
        foreach($loan->schedules as $key => $schedule) {
            $date = date('Y-m-d', strtotime("+$schedule->month month", strtotime($loan->start_date)));
            $months[$schedule->month] = date('F Y', strtotime("-1 month", strtotime($date)));
        }
                    
        return view('backend.accounts.loans.reschedule', compact('loan','banks','months'));
    }

    /**
     * Generate the specified resource for storage.
     */
    public function balance(Request $request) {
        $amortization = AmortizationSchedule::where('loan_id', $request->loan_id)->where('month', $request->month-1)->first();
        // dd($amortization);
        return $amortization->balance ? $amortization->balance : 0;
    }

    /**
     * Generate the specified resource for storage.
     */
    public function reschedule_update(Request $request, $id) {
        $request->validate([
            'reshedule_month'           => 'required|numeric|min:1',
            'reshedule_amount'          => 'required|numeric|min:1',
            'reshedule_interest_rate'   => 'required|numeric|min:1',
        ]);

        $loan = Loan::findOrFail($id);
        $this->generate_reschedule($loan, $request->reshedule_month, $request->reshedule_amount, $request->reshedule_interest_rate);

        flash(translate('Loan has been reschedule successfully'))->success();
        return redirect()->route('loans.show', $loan->id);
    }

    /**
     * Generate the specified resource for storage.
     */
    private function generate_reschedule(Loan $loan, $reshedule_month, $reshedule_amount, $reshedule_interest_rate)
    {
        $loan_term = $loan->schedules()->where('month', '>=', $reshedule_month)->count();

        if ($loan_term <= 0) {
            return; // No remaining schedule to reschedule
        }

        $loanTerm = $loan_term;
        $loanAmount = $reshedule_amount;
        $interestRate = ($reshedule_interest_rate / 100) / 12;

        if ($interestRate == 0) {
            $monthlyPayment = $loanAmount / $loanTerm; // No interest case
        } else {
            $monthlyPayment = $loanAmount * ($interestRate * pow(1 + $interestRate, $loanTerm)) / (pow(1 + $interestRate, $loanTerm) - 1);
        }

        $balance = $loanAmount;

        for ($month = $reshedule_month; $month < $reshedule_month + $loanTerm; $month++) {

            $interest = $balance * $interestRate;
            $principal = $monthlyPayment - $interest;
            $balance -= $principal;

            // Retrieve or create a new schedule entry
            $schedule = AmortizationSchedule::firstOrNew([
                'loan_id' => $loan->id,
                'month' => $month,
            ]);

            $schedule->loan_id = $loan->id;
            $schedule->interest_rate = $reshedule_interest_rate;
            $schedule->month = $month;
            $schedule->payment = round($monthlyPayment, 2);
            $schedule->principal = round($principal, 2);
            $schedule->interest = round($interest, 2);
            $schedule->balance = max(round($balance, 2), 0);
            
            $schedule->save();

            if ($balance <= 0) break;
        }
    }
}
