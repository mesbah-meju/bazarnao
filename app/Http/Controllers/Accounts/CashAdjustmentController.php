<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccCoa;
use App\Models\AccTransaction;
use App\Models\FinancialYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CashAdjustmentController extends Controller
{
    public function index()
    {
        $transaction = AccTransaction::where('voucher_no', 'like', 'CHV-%')->order_by('ID', 'desc')->first();

        if (!empty($transaction->voucher_no)) {
            $vn = substr($transaction->voucher_no, 4) + 1;
            $voucher_no = 'CHV-' . $vn;
        } else {
            $voucher_no = 'CHV-1';
        }

        return view('backend.accounts.cash_adjustment.index', compact('transaction', 'voucher_no'));
    }

    public function create()
    {
        $transaction = AccTransaction::where('voucher_no', 'like', 'CHV-%')->orderBy('ID', 'desc')->first();

        if (!empty($transaction->voucher_no)) {
            $vn = substr($transaction->voucher_no, 4) + 1;
            $voucher_no = 'CHV-' . $vn;
        } else {
            $voucher_no = 'CHV-1';
        }

        return view('backend.accounts.cash_adjustment.create', compact('transaction', 'voucher_no'));
    }

    public function store(Request $request)
    {
        $this->form_validation->set_rules('txtAmount', display('amount'), 'required|max_length[100]');
        $this->form_validation->set_rules('txtRemarks', display('remarks'), 'max_length[200]');
        $this->form_validation->set_rules('type', display('adjustment_type'), 'required|max_length[20]');

        if ($this->form_validation->run()) {
        if ($this->accounts_model->insert_cashadjustment()) {
            $this->session->set_flashdata('message', display('save_successfully'));
            redirect('cash_adjustment');
        } else {
            $this->session->set_flashdata('exception',  display('please_try_again'));
        }
        redirect("cash_adjustment");
        } else {
        $data['title']      = display('cash_adjustment');
        $data['voucher_no'] = $this->accounts_model->Cashvoucher();
        $data['module']     = "account";
        $data['page']       = "cash_adjustment";
        echo Modules::run('template/layout', $data);
        }

        return view('backend.accounts.cash_adjustment.index', compact('transaction', 'voucher_no'));




        $finyear = $request->finyear;
        if ($finyear <= 0) {
            flash(translate('Please Create Financial Year First'))->warning();
            return redirect()->route('debit-vouchers.index');
        } else {
            $validator = Validator::make($request->all(), [
                'txtAmount' => 'required|max:100',
                'txtRemarks' => 'max:200',
                'type' => 'required|max:20',
            ]);

            if ($validator->fails()) {
                flash(translate('Please try again'))->error();
                return redirect()->route('debit-vouchers.index');
            } else {
                $financialyears = FinancialYear::where('status', 1)->first();
                $date = date('Y-m-d', strtotime($request->dtpDate));
                $startfdate = $financialyears->start_date;
                $crdate = date("Y-m-d");
                if ($startfdate > $date) {
                    flash(translate('Please try again'))->error();
                    return redirect()->route('debit-vouchers.index');
                } else if ($date > $crdate || $date > $financialyears->end_date) {
                    flash(translate('Please try again'))->error();
                    return redirect()->route('debit-vouchers.index');
                } else {
                    $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'DV', 'voucher_no');
                    $voucher_no = "DV-" . ($maxid + 1);

                    $fyear = get_financial_year();

                    $rev_coa_id = $request->cmbCredit;
                    $voucher_date = date('Y-m-d', strtotime($request->dtpDate));
                    $cheque_no = $request->chequeNo;
                    $cheque_date = $request->chequeDate ? date('Y-m-d', strtotime($request->chequeDate)) : null;
                    $is_honours = $request->ishonours;
                    $narration = addslashes(trim($request->txtRemarks));

                    $is_subtypes = $request->isSubtype;
                    $subtypes = $request->subtype;
                    $coa_ids = $request->cmbCode;
                    $comments = $request->txtComment;
                    $debits = $request->txtAmount;
                    $warehouse = $request->warehouse;

                    $created_by = Auth::user()->id;
                    $created_at = date('Y-m-d H:i:s');

                    $route_name = Route::currentRouteName();

                    for ($i = 0; $i < count($coa_ids); $i++) {
                        $coa_id = $coa_ids[$i];
                        
                        $debit_amnt = $debits[$i];
                        $is_subtype = $is_subtypes[$i];
                        $comment = $comments[$i];

                        if ($is_subtype != 1) {
                            $subcode = $subtypes[$i];
                            $refno = get_referance_no($subcode);
                        } else {
                            $subcode = null;
                            $refno = null;
                        }

                        if (isset($is_honours)) {
                            $is_honour = 1;
                        } else {
                            $is_honour = 0;
                        }

                        $debit = new AccVoucher();
                        $debit->fyear = $fyear;
                        $debit->voucher_no = $voucher_no;
                        $debit->voucher_type = 'DV';
                        $debit->reference_no = $refno;
                        $debit->voucher_date = $voucher_date;
                        $debit->coa_id = $coa_id;
                        $debit->narration = $narration;
                        $debit->cheque_no = $cheque_no;
                        $debit->cheque_date = $cheque_date;
                        $debit->is_honour = $is_honour;
                        $debit->ledger_comment = $comment;
                        $debit->debit = $debit_amnt;
                        $debit->credit = 0.00;
                        $debit->rev_code = $rev_coa_id;
                        $debit->sub_type = $is_subtype;
                        $debit->sub_code = $subcode;
                        $debit->warehouse_id = $warehouse;
                        $debit->is_approved = 0;
                        $debit->created_by = $created_by;
                        $debit->created_at = $created_at;
                        $debit->status = 0;
                        $debit->save();

                        add_activity_log("debit_voucher", "create", $debit->id, "acc_vouchers", $route_name, 1, $debit);
                    }

                    flash(translate('Debit voucher has saved Successfully'))->success();
                    return redirect()->route('debit-vouchers.index');
                }
            }
        } 



        $voucher_no = $this->input->post('txtVNo', true);
        $Vtype = "AD";
        $amount = $this->input->post('txtAmount', true);
        $type = $this->input->post('type', true);
        if ($type == 1) {
            $debit = $amount;
            $credit = 0;
        }
        if ($type == 2) {
            $debit = 0;
            $credit = $amount;
        }
        $VDate = $this->input->post('dtpDate', true);
        $Narration = $this->input->post('txtRemarks', true);
        $IsPosted = 1;
        $IsAppove = 1;
        $CreateBy = $this->session->userdata('user_id');
        $createdate = date('Y-m-d H:i:s');
        $fyear = $this->db->select("yearName")
            ->from('financial_year')
            ->where('status', 1)
            ->limit(1)
            ->get()
            ->row();

        $cc = array(
            'VNo'            =>  $voucher_no,
            'fyear'          =>  $fyear->yearName,
            'Vtype'          =>  $Vtype,
            'VDate'          =>  $VDate,
            'COAID'          =>  1020101,
            'Narration'      =>  'Cash Adjustment ',
            'Debit'          =>  $debit,
            'Credit'         =>  $credit,
            'IsPosted'       =>  1,
            'CreateBy'       =>  $CreateBy,
            'CreateDate'     =>  $createdate,
            'IsAppove'       =>  1
        );

        $this->db->insert('acc_transaction', $cc);

        return true;
    }

    public function report(Request $request)
    {
        $cashbook = AccCoa::where('is_cash_nature', 1)->orderBy('head_name', 'asc')->get();

        $cmbCode = $request->cmbCode;  
        $dtpFromDate = date('Y-m-d', strtotime($request->dtpFromDate));
        $dtpToDate = date('Y-m-d', strtotime($request->dtpToDate));

        $HeadName = general_led_report_head_name($cmbCode);       
        $pre_balance = get_opening_balance($cmbCode,$dtpFromDate,$dtpToDate);
        $HeadName2 = get_general_ledger_report($cmbCode,$dtpFromDate,$dtpToDate,1,0);

        $data['dtpFromDate'] = $dtpFromDate;
        $data['dtpToDate'] = $dtpToDate;
        $data['cmbCode'] = $cmbCode;
        $data['HeadName'] = $HeadName;
        $data['ledger'] = $HeadName;
        $data['HeadName2'] = $HeadName2;
        $data['prebalance'] =  $pre_balance;
        // $data['setting'] = $this->accounts_model->setting(); 
        $data['cashbook']  = $cashbook;

        return view('backend.accounts.reports.cashbook.report', $data);
    }
}
