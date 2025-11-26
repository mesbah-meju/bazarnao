<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccSubcode;
use App\Models\AccSubtype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort_search = null;

        $sub_accounts = AccSubcode::where('status', 1)->orderBy('sub_type_id', 'asc');

        if ($request->search != null) {
            $sort_search = $request->search;
            $sub_accounts = $sub_accounts->where('name', 'like', '%'.$request->search.'%');
        }

        $sub_accounts = $sub_accounts->paginate(30);
        return view('backend.accounts.subaccount.index', compact('sub_accounts', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $subtypes = AccSubtype::all();

        $subtypes = AccSubtype::where('id', 5)->get();
        return view('backend.accounts.subaccount.create', compact('subtypes'));
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sub_type_id'   => 'required|exists:acc_subtypes,id',
            'name'          => 'required|string|max:250',
        ]);

        try {
            $subaccount = new AccSubcode();
            $subaccount->sub_type_id = $validated['sub_type_id'];
            $subaccount->name = $validated['name'];
            $subaccount->status = 1;
            $subaccount->reference_no =  1;
            $subaccount->created_by = Auth::user()->id;
            $subaccount->created_at = date('Y-m-d H:i:s');
            $subaccount->save();

            flash(translate('Sub account has been save successfully'))->success();
            
        } catch (\Exception $e) {
            \Log::error('Error creating sub account: ', ['error' => $e->getMessage()]);
            flash(translate('Failed to create sub account: ' . $e->getMessage()))->error();
            return redirect()->back();
        }

        return redirect()->route('sub-accounts.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id) 
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $subtypes = AccSubtype::all();
        $sub_account = AccSubcode::findOrFail($id);
        return view('backend.accounts.subaccount.edit', compact('subtypes', 'sub_account'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        $validated = $request->validate([
            'sub_type_id'   => 'required|exists:acc_subtypes,id',
            'name'          => 'required|string|max:250',
        ]);

        try {
            $subaccount = AccSubcode::findOrFail($id);
            $subaccount->sub_type_id = $validated['sub_type_id'];
            $subaccount->name = $validated['name'];
            $subaccount->updated_by = Auth::user()->id;
            $subaccount->updated_at = date('Y-m-d H:i:s');
            $subaccount->save();

            flash(translate('Sub account has been update successfully'))->success();
        } catch (\Exception $e) {
            flash(translate('Failed to update sub account: ' . $e->getMessage()))->error();
            return redirect()->back();
        }

        return redirect()->route('sub-accounts.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $subaccount = AccSubcode::findOrFail($id);
            $subaccount->delete();

            flash(translate('Sub account has been delete successfully'))->success();
        } catch (\Exception $e) {
            
            flash(translate('Failed to delete sub account: ' . $e->getMessage()))->error();
            return redirect()->back();
        }

        return redirect()->route('sub-accounts.index');
    }

}
