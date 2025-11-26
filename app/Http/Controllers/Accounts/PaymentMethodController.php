<?php


namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccCoa;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::withTrashed()->get();
        return view('backend.accounts.payment_methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('backend.accounts.payment_methods.create');
    }

    public function store(Request $request)
    {


        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'details' => 'required|string',
        ]);


        $exists = PaymentMethod::where('name', 'like', '%' . $request->name . '%')->withTrashed()->exists();
        $bank_id = PaymentMethod::max('id') ?? 1;
        $coa = AccCoa::all();
        if ($coa->isNotEmpty()) {
            $headcode = $coa->max('HeadCode') + 1;
        } else {
            $headcode = "1020501";
        }

        if ($exists) {
            flash(translate('Payment method already exists'))->error();
            return redirect()->route('payment_methods.index');
        } else {
            $addmethod = new PaymentMethod;
            $addmethod->name = $request->name;
            $addmethod->type = $request->type;
            $addmethod->details = $request->details;
            $addmethod->save();

            $head = new AccCoa();
            $head->head_code = $headcode;
            $head->head_name = $request->name;
            $head->pre_head_name = 'Payment Method';
            $head->pre_head_code = 10205;
            $head->head_level  = 4;
            $head->is_active = 1;
            $head->is_transaction = 0;
            $head->is_gl = 0;
            $head->is_cash_nature = 0;
            $head->is_bank_nature  = 1;
            $head->head_type = 'A';
            $head->is_budget = 0;
            $head->is_depreciation = 0;
            $head->customer_id = 0;
            $head->supplier_id = 0;
            $head->bank_id = $bank_id;
            $head->service_id = 0;
            $head->depreciation_rate = 0;
            $head->created_by = 0;
            $head->created_at = 0;
            $head->updated_by = 0;
            $head->updated_at = 0;
            $head->is_sub_type = 0;
            $head->sub_type = 1;
            $head->is_stock = 0;
            $head->is_fixed_asset_sch = 0;
            $head->note_no = 0;
            $head->asset_code = 0;
            $head->dep_code = 0;
            $head->save();

            flash(translate('Payment method added successfully'))->success();
            return redirect()->route('payment_methods.index');
        }
    }

    public function edit($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        return view('backend.accounts.payment_methods.create', compact('paymentMethod'));
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'details' => 'required|string',
        ]);

        $paymentMethod = PaymentMethod::findOrFail($id);

        $head = AccCoa::where('bank_id', $id)->first();

        $exists = PaymentMethod::where('name', 'like', '%' . $request->name . '%')
            ->where('id', '!=', $id)
            ->exists();



        if ($exists) {
            flash(translate('Payment method already exists'))->error();
            return redirect()->route('payment_methods.create', compact('paymentMethod'));
        } else {
            $paymentMethod->name = $request->name;
            $paymentMethod->type = $request->type;
            $paymentMethod->details = $request->details;
            $paymentMethod->save();

            $head->HeadName = $request->name;
            $head->save();

            flash(translate('Payment method updated successfully'))->success();
            return redirect()->route('payment_methods.index');
        }
    }

    public function destroy($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $head = AccCoa::where('bank_id', $id)->first() ?? '';

        $paymentMethod->delete();
        $head->delete();
        flash(translate('Payment method Deleted'))->error();
        return redirect()->route('payment_methods.index');
    }
    public function restore(Request $request, $id, $name)
    {
        $coa = AccCoa::all();
        if ($coa->isNotEmpty()) {
            $headcode = $coa->max('HeadCode') + 1;
        } else {
            $headcode = "1020501";
        }

        $paymentMethod = PaymentMethod::withTrashed()->findOrFail($id);
        $paymentMethod->restore();

        $head = new AccCoa();
        $head->HeadCode = $headcode;
        $head->HeadName = $request->name;
        $head->PHeadName = 'Payment Method';
        $head->PHeadCode = 10205;
        $head->HeadLevel = 4;
        $head->IsActive = 1;
        $head->IsTransaction = 0;
        $head->IsGL = 0;
        $head->isCashNature = 0;
        $head->isBankNature = 1;
        $head->HeadType = 'A';
        $head->IsBudget = 0;
        $head->IsDepreciation = 0;
        $head->customer_id = 0;
        $head->supplier_id = 0;
        $head->bank_id = $request->id;
        $head->service_id = 0;
        $head->DepreciationRate = 0;
        $head->CreateBy = 0;
        $head->CreateDate = 0;
        $head->UpdateBy = 0;
        $head->UpdateDate = 0;
        $head->isSubType = 0;
        $head->subType = 1;
        $head->isStock = 0;
        $head->isFixedAssetSch = 0;
        $head->noteNo = 0;
        $head->assetCode = 0;
        $head->depCode = 0;
        $head->save();

        flash(translate('Payment method restored successfully'))->success();
        return redirect()->route('payment_methods.index');
    }
}
