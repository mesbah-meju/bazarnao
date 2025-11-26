<?php

use App\Http\Controllers\Accounts\AccCoaController;
use App\Http\Controllers\Accounts\BalanceSheetController;
use App\Http\Controllers\Accounts\BankBookController;
use App\Http\Controllers\Accounts\BankController;
use App\Http\Controllers\Accounts\CashBookController;
use App\Http\Controllers\Accounts\ChartOfAccountController;
use App\Http\Controllers\Accounts\ContraVoucherController;
use App\Http\Controllers\Accounts\CreditVoucherController;
use App\Http\Controllers\Accounts\CustomerReceiveController;
use App\Http\Controllers\Accounts\DayBookController;
use App\Http\Controllers\Accounts\CashTransferController;
use App\Http\Controllers\Accounts\FinancialYearController;
use App\Http\Controllers\Accounts\OpeningBalanceController;
use App\Http\Controllers\Accounts\DebitVoucherController;
use App\Http\Controllers\Accounts\ExpenditureStatementController;
use App\Http\Controllers\Accounts\GeneralLedgerController;
use App\Http\Controllers\Accounts\IncomeStatementController;
use App\Http\Controllers\Accounts\JournalVoucherController;
use App\Http\Controllers\Accounts\PaymentMethodController;
use App\Http\Controllers\Accounts\SubLedgerController;
use App\Http\Controllers\Accounts\TrialBalanceController;
use App\Http\Controllers\Accounts\VoucherController;
use App\Http\Controllers\Accounts\FixedAssetController;
use App\Http\Controllers\Accounts\ProfitLossController;
use App\Http\Controllers\Accounts\SubAccountController;
use App\Http\Controllers\Accounts\BankReconciliationController;
use App\Http\Controllers\Accounts\CashAdjustmentController;
use App\Http\Controllers\Accounts\LoanController;
use App\Http\Controllers\Accounts\PredefinedAccountController;
use App\Http\Controllers\Accounts\ReceiptPaymentController;
use App\Http\Controllers\Accounts\SupplierPaymentController;
use App\Models\Supplier;

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
    Route::resource('/chart-of-accounts', ChartOfAccountController::class)->only('index', 'store');
    Route::controller(ChartOfAccountController::class)->group(function () {
        Route::get('/chart-of-accounts/destroy/{id}', 'destroy')->name('chart-of-accounts.destroy');
        Route::get('/chart-of-accounts/selectedform/{id}',  'selectedform')->name('chart-of-accounts.selectedform');
        Route::get('/chart-of-accounts/newform/{id}',  'newform')->name('chart-of-accounts.newform');
        Route::get('/chart-of-accounts/getsubtype/{id?}',  'getsubtype')->name('chart-of-accounts.getsubtype');
    });

    Route::resource('/accounts', AccCoaController::class)->except('update', 'destroy');
    Route::controller(AccCoaController::class)->group(function () {
        Route::post('/accounts/{headCode}/update', 'update')->name('accounts.update');
        Route::post('/accounts/destroy/{id}', 'destroy')->name('accounts.destroy');

        Route::get('/account/coa_list', 'coa_list')->name('account.coa_list');
        Route::get('/account/coa_print', 'coa_print')->name('account.coa_print');
        Route::get('/account/getLastHeadCode', 'getLastHeadCode')->name('account.getLastHeadCode');
        Route::get('/account/createNewAccount', 'createNewAccount')->name('account.createNewAccount');
        Route::get('/accounts/coa',  'index')->name('accounts.coa');
        Route::get('/accounts/getsubtype', 'getsubtype')->name('accounts.getsubtype');
        Route::get('accounts/{id}',  'show')->name('accounts.show');
        Route::get('/account/head-details',  'getHeadDetails')->name('account.headDetails');
        Route::get('/account/sub-head-details',  'getSubHeadDetails')->name('account.subHeadDetails');
        Route::get('/account/save-head-details', 'saveHeadDetails')->name('account.saveHeadDetails');
    });

    // Predefined Accounts
    Route::get('/predefined-accounts', [PredefinedAccountController::class, 'index'])->name('predefined.accounts.index');
    Route::post('/predefined-accounts/store',  [PredefinedAccountController::class, 'store'])->name('predefined.accounts.store');

    // Cash Transfer
    Route::resource('/cash-transfers', CashTransferController::class)->except('update', 'destroy');
    Route::controller(CashTransferController::class)->group(function () {
        Route::post('/cash-transfers/{id}/update', 'update')->name('cash-transfers.update');
        Route::get('/cash-transfers/destroy/{id}', 'destroy')->name('cash-transfers.destroy');
        Route::get('/cash-transfers/{voucher_no}/approve/{action}', 'approve')->name('cash-transfers.approve');
        Route::get('/cash-transfers/{voucher_no}/reverse', 'reverse')->name('cash-transfers.reverse');
    });

    // Route to Sub Accounts
    Route::resource('/sub-accounts', SubAccountController::class)->except('show', 'update', 'destroy');
    Route::controller(SubAccountController::class)->group(function () {
        Route::post('/sub-accounts/{id}/update', 'update')->name('sub-accounts.update');
        Route::get('/sub-accounts/destroy/{id}', 'destroy')->name('sub-accounts.destroy');
    });

    // Route to FinancialYearController
    Route::resource('/financial-years', FinancialYearController::class)->except('update', 'destroy');
    Route::controller(FinancialYearController::class)->group(function () {
        Route::post('/financial-years/{id}/update', 'update')->name('financial-years.update');
        Route::get('/financial-years/destroy/{id}', 'destroy')->name('financial-years.destroy');

        Route::post('/financial-years/change-status', 'change_status')->name('financial-years.change-status');

        Route::get('/financial-years/closing-years/', 'closing_years')->name('financial-years.closing-years');
        Route::get('/financial-years/closing/{id}', 'closing')->name('financial-years.closing');
        Route::get('/financial-years/reverse/{id}', 'reverse')->name('financial-years.reverse');
    });

    // Route to store new account data
    Route::resource('/opening-balances', OpeningBalanceController::class)->except('update', 'destroy');
    Route::controller(OpeningBalanceController::class)->group(function () {
        Route::post('/opening-balances/{headCode}/update', 'update')->name('opening-balances.update');
        Route::get('/opening-balances/destroy/{id}', 'destroy')->name('opening-balances.destroy');

        Route::get('/opening-balances/subtypecode/{id}', 'subtypecode')->name('opening-balances.subtypecode');
        Route::get('/opening-balances/subtypebyid/{id}', 'subtypebyid')->name('opening-balances.subtypebyid');
        Route::get('/opening-balances/relvaluebyid/{id}', 'relvaluebyid')->name('opening-balances.relvaluebyid');
    });

    // Vouchers Route
    Route::resource('/vouchers', VoucherController::class)->only(['index', 'show']);
    Route::controller(VoucherController::class)->group(function () {
        Route::post('/vouchers/{voucher_no}/approve/{action}', 'approve')->name('vouchers.approve');
        Route::get('/vouchers/{voucher_no}/reverse', 'reverse')->name('vouchers.reverse');
    });

    // Route to store debit voucher
    Route::resource('/debit-vouchers', DebitVoucherController::class)->except('update', 'destroy');
    Route::controller(DebitVoucherController::class)->group(function () {
        Route::post('/debit-vouchers/{id}/update', 'update')->name('debit-vouchers.update');
        Route::get('/debit-vouchers/destroy/{id}', 'destroy')->name('debit-vouchers.destroy');
        Route::get('/debit-vouchers/print/{id}', 'print')->name('debit-vouchers.print');
    });

    // Route to store credit voucher
    Route::resource('/credit-vouchers', CreditVoucherController::class)->except('update', 'destroy');
    Route::controller(CreditVoucherController::class)->group(function () {
        Route::post('/credit-vouchers/{id}/update', 'update')->name('credit-vouchers.update');
        Route::get('/credit-vouchers/destroy/{id}', 'destroy')->name('credit-vouchers.destroy');
        Route::get('/credit-vouchers/print/{id}', 'print')->name('credit-vouchers.print');
    });

    // Route to store journal voucher
    Route::resource('/journal-vouchers', JournalVoucherController::class)->except('update', 'destroy');
    Route::controller(JournalVoucherController::class)->group(function () {
        Route::post('/journal-vouchers/{id}/update', 'update')->name('journal-vouchers.update');
        Route::get('/journal-vouchers/destroy/{id}', 'destroy')->name('journal-vouchers.destroy');
        Route::get('/journal-vouchers/print/{id}', 'print')->name('journal-vouchers.print');
    });

    // Route to store contra voucher
    Route::resource('/contra-vouchers', ContraVoucherController::class)->except('update', 'destroy');
    Route::controller(ContraVoucherController::class)->group(function () {
        Route::post('/contra-vouchers/{id}/update', 'update')->name('contra-vouchers.update');
        Route::get('/contra-vouchers/destroy/{id}', 'destroy')->name('contra-vouchers.destroy');
        Route::get('/contra-vouchers/print/{id}', 'print')->name('contra-vouchers.print');
    });

    Route::resource('/banks', BankController::class)->except('update', 'destroy');
    Route::controller(BankController::class)->group(function () {
        Route::post('/banks/{id}/update', 'update')->name('banks.update');
        Route::get('/banks/destroy/{id}', 'destroy')->name('banks.destroy');
    });

    Route::resource('payment_methods', PaymentMethodController::class);
    Route::controller(PaymentMethodController::class)->group(function () {
        Route::post('/payment_methods/{id}/update', 'update')->name('payment_methods.update');
        Route::get('/payment_methods/destroy/{id}', 'destroy')->name('payment_methods.destroy');
        Route::post('payment_methods/{id}/{name}/restore',  'restore')->name('payment_methods.restore');
    });

    // Route to store supplier payment
    Route::resource('/supplier-payment', SupplierPaymentController::class)->only('index', 'store');
    // Supplier Payment Routes
        Route::controller(SupplierPaymentController::class)->group(function () {
            Route::post('/supplier-payment/due-vouchers', 'due_vouchers')->name('supplier-payment.due-vouchers');
            Route::post('/supplier-payment/due-amount', 'due_amount')->name('supplier-payment.due-amount');
            Route::post('/supplier-receive/purchasewise/{id}', 'purchasewise')->name('supplier-receive.purchasewise');
        });


    Route::post('/accounts/payment/modal', [SupplierPaymentController::class, 'payment_method_modal'])->name('accounts.payment.modal');

    // Route to store customer receive
    Route::resource('/customer-receive', CustomerReceiveController::class)->only('index', 'store');
    Route::controller(CustomerReceiveController::class)->group(function () {
        Route::post('/customer-receive/due-vouchers', 'due_vouchers')->name('customer-receive.due-vouchers');
        Route::post('/customer-receive/due-amount', 'due_amount')->name('customer-receive.due-amount');

        Route::post('/customer-receive/orderwise', 'orderwise')->name('customer-receive.orderwise');
    });

    // Route to store cash adjustment
    Route::resource('/cash-adjustment', CashAdjustmentController::class)->except('update', 'destroy');
    Route::controller(CashAdjustmentController::class)->group(function () {
        Route::get('/cash-adjustment/headcode/{id}', 'customer_due_vouchers')->name('customer-receives.headcode');
    });

    
    // Route to store CashBook
    Route::resource('/cash-book', CashBookController::class)->except('update', 'destroy');
    Route::controller(CashBookController::class)->group(function () {
        Route::any('/cash-book-report', 'report')->name('cash-book-report.index');
    });

    // Route to store BankBook
    Route::resource('/bank-book', BankBookController::class)->except('update', 'destroy');
    Route::controller(BankBookController::class)->group(function () {
        Route::any('/bank-book-report', 'report')->name('bank-book-report.index');
    });

    // Route to store DayBook
    Route::resource('/day-book', DayBookController::class)->except('update', 'destroy');
    Route::controller(DayBookController::class)->group(function () {
        Route::any('/day-book-report', 'report')->name('day-book-report.index');
    });

    // Route to store DayBook
    Route::resource('/general-ledger', GeneralLedgerController::class)->except('update', 'destroy');
    Route::controller(GeneralLedgerController::class)->group(function () {
        Route::any('/general-ledger-report', 'report')->name('general-ledger.report');
        Route::any('/general-ledger-report/print', 'print')->name('general-ledger.print');
    });

    // Route to store DayBook
    Route::resource('/sub-ledger', SubLedgerController::class)->only('index');
    Route::controller(SubLedgerController::class)->group(function () {
        Route::any('/sub-ledger-report', 'report')->name('sub-ledger.report');

        Route::get('/sub-ledger/accounthead/{id}', 'get_account_head')->name('sub-ledger.accounthead');
        Route::get('/sub-ledger/subcode/{id}', 'get_subcode')->name('sub-ledger.subcode');
        Route::get('/sub-ledger/subcode-by-head/{codes}', 'get_subcode_by_accounthead')->name('sub-ledger.subcode-by-head');
    });

    // Route for Fixed Asset
    Route::resource('/fixed-asset', FixedAssetController::class)->except('update', 'destroy');
    Route::controller(FixedAssetController::class)->group(function () {
        Route::any('/fixed-asset-report', 'report')->name('fixed-asset-report.index');
    });

    // Route to store Trial Balance
    Route::resource('/trial-balance', TrialBalanceController::class)->except('update', 'destroy');
    Route::controller(TrialBalanceController::class)->group(function () {
        Route::any('/trial-balance-report', 'report')->name('trial-balance-report.index');
        Route::post('/trial-balance/detail', 'detail')->name('trial-balance.detail');
    });

    // Route to store Income Statement
    Route::resource('/income-statement', IncomeStatementController::class)->except('update', 'destroy');
    Route::controller(IncomeStatementController::class)->group(function () {
        Route::any('/income-statement-report', 'report')->name('income-statement-report.index');
        Route::any('/income-statement-yearly-report', 'yearly_report')->name('income-statement-yearly-report.index');
    });

    // Route to store expenditure_statement
    Route::resource('/expenditure-statement', ExpenditureStatementController::class)->except('update', 'destroy');
    Route::controller(ExpenditureStatementController::class)->group(function () {
        Route::any('/expenditure-statement-report', 'report')->name('expenditure-statement-report.index');
    });

    // Route to store Profit Loss
    Route::resource('/profit-loss', ProfitLossController::class)->only('index');
    Route::controller(ProfitLossController::class)->group(function () {
        Route::any('/profit-loss-report', 'report')->name('profit-loss.report');
    });

    // Route to store Balance Sheet
    Route::resource('/balance-sheet', BalanceSheetController::class)->only('index');

    // Route for Bank Reconciliation
    Route::resource('/bank-reconciliation', BankReconciliationController::class)->only('index');
    Route::controller(BankReconciliationController::class)->group(function () {
        Route::any('/bank-reconciliation/report', 'report')->name('bank-reconciliation.report');
        Route::any('/bank-reconciliation/approve/{voucher_no}', 'approve')->name('bank-reconciliation.approve');
        Route::any('/bank-reconciliation/disapprove/{voucher_no}', 'disapprove')->name('bank-reconciliation.disapprove');
    });

    // Route for Receive Payment
    Route::resource('/receipt-payment', ReceiptPaymentController::class)->only('index');
    Route::controller(ReceiptPaymentController::class)->group(function () {
        Route::any('/receipt-payment-report', 'receipt_payment_report')->name('receipt-payment-report.report');
    });

    // Loan Route
    Route::resource('/loans', LoanController::class)->except('update', 'destroy');
    Route::controller(LoanController::class)->group(function () {
        Route::post('/loans/{id}/update', 'update')->name('loans.update');
        Route::get('/loans/destroy/{id}', 'destroy')->name('loans.destroy');
        Route::get('/loans/installment/{id}', 'installment')->name('loans.installment');
        Route::get('/loans/reschedule/{id}', 'reschedule')->name('loans.reschedule');
        Route::post('/loans/amortization/balance', 'balance')->name('loans.amortization.balance');
        Route::post('/loans/amortization/reschedule/{id}', 'reschedule_update')->name('loans.amortization.reschedule');
    });
});
