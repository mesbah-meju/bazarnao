<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccPredefineAccount;
use Illuminate\Http\Request;

class PredefinedAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fieldnames = get_predefine_code();
        $fieldvalues = get_predefine_code_values();
        $allheads    = get_coa_heads();

        return view('backend.accounts.predefined_accounts.index', compact('fieldnames', 'fieldvalues', 'allheads'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->isMethod('post')) {
            $fieldnames = get_predefine_code();
            $fieldvalues = get_predefine_code_values();
            
            $rules = [];
            foreach ($fieldnames as $field) {
                if ($field != 'id') {
                    $rules[$field] = 'max:20';
                }
            }

            $validated = $request->validate($rules);

            $definedata = [];
            foreach ($fieldnames as $field) {
                $definedata[$field] = $request->input($field);
            }

            $updated = AccPredefineAccount::where('id', $fieldvalues->id)->update($definedata);

            if ($updated) {
                // add_activity_log($MesTitle, "update", $id, "acc_predefine_account", 2, $definedata);

                flash(translate('Predefined account has updated successfully'))->success();
                return redirect()->route('predefined.accounts.index');
            } else {
                flash(translate('Please try again'))->success();
                return redirect()->route('predefined.accounts.index');
            }
        }
    }
}
