<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccCoa;
use App\Models\AccSubtype;
use App\Models\AccSubcode;
use App\Models\AccPredefineAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Exports\CoaPrintExport;
use Maatwebsite\Excel\Facades\Excel;


class AccCoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'List of Accounts';
        $data['subType'] = AccSubtype::all();
        $data['userList'] = AccCoa::distinct()
            ->where('is_active', 1)
            ->orderBy('head_name')
            ->get();
        $data['csrf_token'] = csrf_token();

        return view('backend.accounts.treeview', $data);
    }

    public function coa_list()
    {
        $all_coa_head = AccCoa::where('is_active', 1)
            ->orderBy('head_code', 'asc')
            ->get();

        return view('backend.accounts.list', compact('all_coa_head'));
    }

    public function coa_print(Request $request)
    {
        $type = $request->type;

        $coaData = AccCoa::where('is_active', 1)
            ->orderBy('head_code')
            ->get();

        $maxLevel = AccCoa::where('is_active', 1)
            ->max('head_level');

        $data['coaData'] = $coaData;
        $data['maxLevel'] = $maxLevel;

        // Handle Excel export
        if ($type == 'excel') {
            return Excel::download(new CoaPrintExport($data), 'ChartOfAccounts_' . date('Y-m-d') . '.xlsx');
        }

        return view('backend.accounts.coa_print', $data);
    }



    public function getHeadDetails(Request $request)
    {
        $headCode = $request->input('headCode');
        $headDetails = AccCoa::getHeadDetails($headCode);
        $subType = AccSubtype::all();

        // Return both headDetails and subType in a single JSON response
        return response()->json([
            'headDetails' => $headDetails,
            'subType' => $subType,
        ]);
    }

    public function getLastHeadCode()
    {
        $lastHeadCode = AccCoa::orderBy('head_code', 'desc')->value('head_code'); // Adjust according to your database structure
        return response()->json(['lastHeadCode' => $lastHeadCode]);
    }



    public function store(Request $request)
    {
        // Validate incoming request
        // $validated = $request->validate([
        //     'head_code' => 'required|unique:acc_coas,head_code',
        //     'head_name' => 'required|string|max:255',
        // ]);

        // Handle conditional fields
        $isFixedAssetSch = $request->input('isFixedAssetSch');
        $DepreciationRate = $request->input('DepreciationRate', 0);
        $assetCode = $isFixedAssetSch == 1 ? $request->input('assetCode') : null;
        $depCode = $request->input('depCode');
        $HeadLevel = $request->input('HeadLevel');
        // $HeadLevel = $headLevel + 1;

        $isSubType = $request->input('isSubType');
        $subtype = $isSubType ? $request->input('subType') : 1;

        // Create a new account in the database
        $account = new AccCoa();
        $account->head_code = $request->input('HeadCode');
        $account->head_name = $request->input('HeadName');
        $account->pre_head_name = $request->input('PHeadName');
        $account->head_type = $request->input('HeadType');
        $account->pre_head_code = $request->input('PHeadCode');
        $account->head_level = $HeadLevel;
        $account->is_active = $request->input('is_active', 1);
        $account->is_stock = $request->input('isStock') ?? 0;
        $account->is_transaction = $request->input('IsTransaction') ?? 0;
        $account->is_gl = $request->input('IsGL') ?? 0;
        $account->is_cash_nature = $request->input('isCashNature') ?? 0;
        $account->is_bank_nature = $request->input('isBankNature') ?? 0;
        $account->is_sub_type = $isSubType ? $isSubType : 0;
        $account->depreciation_rate = $DepreciationRate ?? 0;
        $account->is_fixed_asset_sch = $isFixedAssetSch ?? 0;
        $account->asset_code = $assetCode ?? '';
        $account->dep_code = $depCode ?? '';
        $account->sub_type = $subtype;
        $account->note_no = $request->input('noteNo') ?? '';
        $account->created_by = Auth::user()->id;
        $account->created_at = now();
        $account->updated_by = Auth::user()->id;
        $account->updated_at = now();

        $account->save();

        return response()->json(['message' => 'Account created successfully.']);
    }

    public function update(Request $request, $id)
    {
        // dd($request->all);
        // Validate the request data
        // $validated = $request->validate([
        //     'head_name' => 'required|string|max:255',
        //     'head_code' => 'required|string|max:50',
        // ]);

        // Gather input data
        $isFixedAssetSch = $request->input('isFixedAssetSch', 0);
        $DepreciationRate = $request->input('DepreciationRate', 0);
        $assetCode = $isFixedAssetSch ? $request->input('assetCode') : null;
        $depCode = $request->input('depCode');
        $HeadLevel = $request->input('HeadLevel');
        $isSubType = $request->input('isSubType', 0);
        $subtype = $isSubType ? $request->input('subType') : 1;

        // Find the AccCoa record by id
        $accCoa = AccCoa::where('head_code', $id)->first();

        // If record found, update it
        if ($accCoa) {
            $accCoa->head_code = $request->input('HeadCode');
            $accCoa->head_name = $request->input('HeadName');
            $accCoa->pre_head_name = $request->input('PHeadName');
            $accCoa->head_type = $request->input('HeadType');
            $accCoa->pre_head_code = $request->input('PHeadCode');
            $accCoa->head_level = $HeadLevel;
            $accCoa->is_active = $request->input('is_active', 1);
            $accCoa->is_stock = $request->input('isStock') ?? 0;
            $accCoa->is_transaction = $request->input('IsTransaction') ?? 0;
            $accCoa->is_gl = $request->input('IsGL') ?? 0;
            $accCoa->is_cash_nature = $request->input('isCashNature') ?? 0;
            $accCoa->is_bank_nature = $request->input('isBankNature') ?? 0;
            $accCoa->is_sub_type = $isSubType ? $isSubType : 0;
            $accCoa->depreciation_rate = $DepreciationRate ?? 0;
            $accCoa->is_fixed_asset_sch = $isFixedAssetSch ?? 0;
            $accCoa->asset_code = $assetCode ?? '';
            $accCoa->dep_code = $depCode ?? '';
            $accCoa->sub_type = $subtype;
            $accCoa->note_no = $request->input('noteNo') ?? '';
            $accCoa->updated_by = Auth::user()->id;
            $accCoa->updated_at = now();
            $accCoa->save();

            return response()->json(['message' => 'Account updated successfully'], 200);
        }

        return response()->json(['message' => 'Account not found'], 404);
    }

    public function destroy($id)
    {
        // Find the account by HeadCode (or ID if you use it as the identifier)
        $accCoa = AccCoa::where('head_code', $id)->first();

        if ($accCoa) {
            // Delete the record
            $accCoa->delete();

            return response()->json(['message' => 'Account deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Account not found'], 404);
        }
    }

    public function subAccount()
    {
        $data['title'] = 'List of Sub Accounts';
        $data['subType'] = AccSubtype::all();

        $data['subCode'] = AccSubcode::leftJoin('acc_subtypes', 'acc_subcodes.sub_type_id', '=', 'acc_subtypes.id')
            ->where('acc_subcodes.status', 1)
            ->select('acc_subcodes.id', 'acc_subtypes.name as subtypeName', 'acc_subcodes.created_at', 'acc_subcodes.name')
            ->orderBy('acc_subtypes.name')
            ->get();

        return view('backend.accounts.subaccount.subaccount', $data);
    }

    public function create_subAccount()
    {
        $data['title'] = 'Create Sub Accounts';
        $data['subType'] = AccSubtype::all();


        return view('backend.accounts.subaccount.create_subaccount', $data);
    }

    public function store_subaccount(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'sub_type_id' => 'required|exists:acc_subtypes,id',
            'name' => 'required|string|max:250',
        ]);

        try {
            // Create new subaccount
            $subaccount = new AccSubcode();

            $subaccount->sub_type_id = $validatedData['subTypeId'];
            $subaccount->name = $validatedData['name'];
            $subaccount->status = 1;


            $subaccount->reference_no =  1;


            $subaccount->created_by = Auth::user()->id;
            $subaccount->created_at = now();
            $subaccount->updated_by = Auth::user()->id;
            $subaccount->updated_at = now();


            $subaccount->save();

            // Flash success message
            session()->flash('success', 'Sub Account created successfully!');
        } catch (\Exception $e) {

            \Log::error('Error creating sub account: ', ['error' => $e->getMessage()]);
            session()->flash('error', 'Failed to create Sub Account: ' . $e->getMessage());
            return redirect()->back();
        }

        return redirect()->route('account.subaccount');
    }

    public function edit_subaccount($id)
    {
        $data['title'] = 'Edit Sub Account';
        $data['subAccount'] = AccSubcode::findOrFail($id);  // Get the specific subaccount to edit
        $data['subType'] = AccSubtype::all();  // Fetch all subtypes for the dropdown

        return view('backend.accounts.subaccount.edit_subaccount', $data);
    }

    public function update_subaccount(Request $request, $id)
    {
        // Validate the request
        $validatedData = $request->validate([
            'sub_type_id' => 'required|exists:acc_subtypes,id',
            'name' => 'required|string|max:250',
        ]);

        try {
            // Find the subaccount by ID
            $subaccount = AccSubcode::findOrFail($id);

            // Update fields
            $subaccount->sub_type_id = $validatedData['subTypeId'];
            $subaccount->name = $validatedData['name'];
            $subaccount->updated_by = Auth::user()->id;
            $subaccount->updated_at = now();

            // Save the changes
            $subaccount->save();

            session()->flash('success', 'Sub Account updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update Sub Account: ' . $e->getMessage());
        }

        return redirect()->route('account.subaccount');
    }

    public function delete_subaccount($id)
    {
        try {
            $subaccount = AccSubcode::findOrFail($id);
            $subaccount->delete();

            session()->flash('success', 'Sub Account deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete Sub Account: ' . $e->getMessage());
        }

        return redirect()->route('account.subaccount');
    }


    public function predefined_accounts()
    {
        $data['title'] = 'Predefined Accounts';
        $data['subType'] = AccSubtype::all();


        return view('backend.accounts.predefined_accounts.predefined_accounts', $data);
    }

    public function predefinedAccounts(Request $request)
    {
        $moduleTitle = 'Accounts';
        $title = 'Accounts';
        $fieldNames = AccPredefineAccount::get_predefine_code();
        $fieldValues = AccPredefineAccount::get_predefine_code_values();
        $allHeads = AccCoa::get_coa_heads();
        $requestData = request()->all();
        

        if ($request->isMethod('post')) {
            
            $definedData = [];
            foreach ($fieldNames as $field) {
                if ($field != 'id') {
                    $definedData[$field] = $request->input($field);
                }
            }

            $predefinedAccount = AccPredefineAccount::find($fieldValues->id);
            if ($predefinedAccount->update($definedData)) {
                // add_activity_log('PredefineCode', "update", $fieldValues->id, "acc_predefine_account", 2, $definedData);

                Session::flash('message', 'Updated successfully');
                return redirect()->route('predefined.accounts');
            } else {
                Session::flash('exception', 'Please try again');
                return redirect()->route('predefined.accounts');
            }
        } else {
            // return view('backend.accounts.predefined_accounts.index', ['title' => 'Predefined Accounts', 'moduleTitle' => 'Accounts', 'allHeads' => AccCoa::get_coa_heads(), 'requestData' => request()->all()]);
            return view('backend.accounts.predefined_accounts.index', compact('fieldNames', 'fieldValues', 'moduleTitle', 'title', 'allHeads','requestData'));
        }
    }
}
