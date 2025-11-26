<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccCoa;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Supplier_ledger;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class SupplierPaymentController extends Controller
{
    public function index(Request $request)
    {
        $suppliers          = get_supplier();
        $voucher_no         = supplier_payment();
        $payment_methods    = payment_methods();

        return view('backend.accounts.supplier_payment.index', compact('suppliers', 'voucher_no', 'payment_methods'));
    }

    public function purchasewise(Request $request, $id)
    {
        $purchase_id = $id;
        $purchase = DB::table('purchases')
            ->leftJoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.supplier_id')
            ->where('purchases.id', $id)
            ->select('purchases.*', 'suppliers.name as supplier_name')
            ->first();


        if (!$purchase) {
            return redirect()->back()->with('error', 'Purchase not found.');
        }

        $suppliers = Supplier::where('supplier_id', $purchase->supplier_id)->where('status', 1)->get();
        $supplier_id = $purchase->supplier_id;

        $voucher_no         = supplier_payment();
        $payment_methods    = payment_methods();

        return view('backend.accounts.supplier_payment.index2', compact(
            'suppliers',
            'voucher_no',
            'payment_methods',
            'purchase',
            'supplier_id',
            'purchase_id'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'txtCode'     => 'nullable|max:100',
            'dueAmount'   => 'required',
            'voucher_no'  => 'required',
            'txtAmount'   => 'required|max:30',
        ]);

        $purchase_date = insert_supplier_payment($request);
        if ($purchase_date) {

            $supplier_ledger = new Supplier_ledger();
            $supplier_ledger->supplier_id = $request->supplier_id;
            $supplier_ledger->purchase_id = $request->voucher_no;
            $supplier_ledger->descriptions = 'Purchase Order';
            $supplier_ledger->type = 'Payment';
            $supplier_ledger->debit = 0;
            $supplier_ledger->credit = $request->txtAmount;
            $supplier_ledger->date = date('Y-m-d', strtotime($request->dtpDate));
            $supplier_ledger->save();

            $info['supplier_info']  = get_supplier_info($request->supplier_id);
            $info['payment_info']   = get_supplier_payment_info($request->voucher_no, $purchase_date);
            // $info['company_info']   = get_company_info();
            $info['message']        = translate('Save Successfully');
            $info['details']        = view('backend.accounts.supplier_payment.receipt', $info)->render();
            $info['status']         = true;
            return response()->json($info);
        } else {
            $info['exception']      = translate('Please Try Again');
            $info['status']         = false;
            return response()->json($info);
        }
    }

    public function due_vouchers(Request $request)
    {
        if ($request->has('purchaseData') && !empty($request->purchaseData)) {
            $supplier_id = $request->supplier_id;
            $coa = AccCoa::where('supplier_id', $supplier_id)->first();
            $code = $coa ? $coa->head_code : '';

            $selected_voucher_id = null;

            if ($request->has('purchaseData') && !empty($request->purchaseData)) {
                $selected_voucher_id = $request->purchaseData; // capture purchase id to preselect
            }

            $vouchers = Purchase::where('supplier_id', $supplier_id)
                ->where('due_amount', '>', 0)
                ->orderBy('approved_date', 'asc')
                ->get();

            $html = '';
            if ($vouchers->isEmpty()) {
                $html .= "No Chalan Found!";
            } else {
                $html .= '<select name="voucher_no" id="voucher_no_1" class="voucher_no form-control aiz-selectpicker" data-live-search="true">';
                $html .= '<option value="">' . 'Select Voucher' . '</option>';

                foreach ($vouchers as $voucher) {
                    $selected = ($selected_voucher_id == $voucher->id) ? 'selected' : '';
                    $html .= '<option value="' . $voucher->id . '" ' . $selected . '>' . $voucher->purchase_no . '</option>';
                }

                $html .= '</select>';
            }

            echo json_encode(['headcode' => $code, 'vouchers' => $html]);
        } else {
            $supplier_id = $request->supplier_id;
            $coa = AccCoa::where('supplier_id', $supplier_id)->first();
            $code = $coa ? $coa->head_code : '';
            $vouchers = Purchase::where('supplier_id', $supplier_id)
                ->where('due_amount', '>', 0)
                ->orderBy('approved_date', 'asc')
                ->get();
            $html = '';
            if ($vouchers->isEmpty()) {
                $html .= "No Chalan Found!";
            } else {
                $html .= '<select name="voucher_no" id="voucher_no_1" class="voucher_no form-control aiz-selectpicker">';
                $html .= '<option>' . 'Select Voucher' . '</option>';
                foreach ($vouchers as $voucher) {
                    $html .= '<option value="' . $voucher->id . '">' . $voucher->purchase_no . '</option>';
                }
                $html .= '</select>';
            }
            echo json_encode(['headcode' => $code, 'vouchers' => $html]);
        }
    }

    public function due_amount(Request $request)
    {
        $purchase_id = $request->purchase_id;
        $purchase = Purchase::where('id', $purchase_id)->first();

        echo ($purchase->due_amount ? $purchase->due_amount : 0);
    }

    public function payment_method_modal()
    {

        $data['payment_methods'] = payment_methods();
        return view('backend.accounts.supplier_payment.newpayment', $data);
    }
}
