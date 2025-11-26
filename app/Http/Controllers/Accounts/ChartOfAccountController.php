<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\AccCoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class ChartOfAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $coas = AccCoa::where('is_active', 1)->orderBy('head_name', 'asc')->get();

        return view('backend.accounts.chart_of_accounts.index', compact('coas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'HeadName' => 'required|string|max:255',
        ]);
        
        if ($validator->fails()) {
            flash(translate('Please Enter Head Name.'))->error();
            return redirect()->route('chart-of-accounts.index');
        } else {
            $HeadCode    = $request->txtHeadCode;
            $PHeadCode   = $request->txtPHeadCode;
            $HeadName    = $request->txtHeadName;
            $PHeadName   = $request->txtPHead;
            $HeadLevel   = $request->txtHeadLevel;
            $txtHeadType = $request->txtHeadType;
            $isact       = $request->IsActive;
            $IsActive    = (!empty($isact) ? $isact : 0);
            $stock       = $request->isStock;
            $isStock     = (!empty($stock) ? $stock : 0);
            $cashnature  = $request->isCashNature;
            $isCashNature     = (!empty($cashnature) ? $cashnature : 0);
            $banknature  = $request->isBankNature;
            $isBankNature     = (!empty($banknature) ? $banknature : 0);
            $fixedassets  = $request->isFixedAssetSch;
            $isFixedAssetSch     = (!empty($fixedassets) ? $fixedassets : 0);
            $isstype     = $request->isSubType;
            $isSubType   = (!empty($isstype) ? $isstype : 0);
            $createdBy    = Auth::user()->id;
            $createdAt = date('Y-m-d H:i:s');
            if ($isFixedAssetSch == 1) {
                if ($txtHeadType == 'A') {
                    $assetCode   = $request->assetCode;
                    $DepreciationRate   = $request->DepreciationRate;
                    $depCode   = null;
                } else {
                    $depCode   = $request->depCode;
                    $DepreciationRate = 0;
                    $assetCode   = null;
                }
            } else {
                $assetCode   = null;
                $depCode   = null;
                $DepreciationRate = 0;
            }
            if ($isSubType == 1) {
                $subtype   = $request->subtype;
            } else {
                $subtype   = 1;
            }
            $noteNo   = (!empty($request->noteNo) ? $request->noteNo : null);

            $route_name = Route::currentRouteName();

            $upinfo = AccCoa::where('head_code', $HeadCode)->first();
            if (empty($upinfo)) {
                $coa = new AccCoa();
                $coa->head_code = $HeadCode;
                $coa->pre_head_code = $PHeadCode;
                $coa->head_name = $HeadName;
                $coa->pre_head_name = $PHeadName;
                $coa->head_level = $HeadLevel;
                $coa->is_active = $IsActive;
                $coa->is_stock = $isStock;
                $coa->is_sub_type = $isSubType;
                $coa->depreciation_rate = $DepreciationRate;
                $coa->head_type = $txtHeadType;
                $coa->is_budget = 0;
                $coa->is_cash_nature = $isCashNature;
                $coa->is_bank_nature = $isBankNature;
                $coa->is_fixed_asset_sch = $isFixedAssetSch;
                $coa->asset_code = $assetCode;
                $coa->dep_code = $depCode;
                $coa->sub_type = $subtype;
                $coa->note_no = $noteNo;
                $coa->created_by = $createdBy; 
                $coa->created_at = $createdAt;
                // $coa->is_transaction = $request->input('IsTransaction') ?? 0;
                // $coa->is_gl = $request->input('IsGL') ?? 0;

                if ($coa->save()) {
                    add_activity_log("coa_account", "create", $coa->id, "acc_vouchers", $route_name, 1, $coa);
                    echo json_encode(array('info' => $coa, 'message' => 'Account has been save successfully', 'type' => 'new'));
                }
            } else {
                $coa = AccCoa::findOrFail($upinfo->id);
                $coa->head_code = $HeadCode;
                $coa->pre_head_code = $PHeadCode;
                $coa->head_name = $HeadName;
                $coa->pre_head_name = $PHeadName;
                $coa->head_level = $HeadLevel;
                $coa->is_active = $IsActive;
                $coa->is_stock = $isStock;
                $coa->is_sub_type = $isSubType;
                $coa->depreciation_rate = $DepreciationRate;
                $coa->head_type = $txtHeadType;
                $coa->is_budget = 0;
                $coa->is_cash_nature = $isCashNature;
                $coa->is_bank_nature = $isBankNature;
                $coa->is_fixed_asset_sch = $isFixedAssetSch;
                $coa->asset_code = $assetCode;
                $coa->dep_code = $depCode;
                $coa->sub_type = $subtype;
                $coa->note_no = $noteNo;
                $coa->created_by = $createdBy; 
                $coa->created_at = $createdAt;
                // $coa->is_transaction = $request->input('IsTransaction') ?? 0;
                // $coa->is_gl = $request->input('IsGL') ?? 0;

                if ($coa->save()) {
                    add_activity_log("coa_account", "update", $coa->id, "acc_vouchers", $route_name, 2, $coa);
                    echo json_encode(array('message' => 'Account has been update successfully', 'type' => 'update'));
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $isDel = true;
        $checkTransation = checkIsTransationAccount($id);
        $checkSub = checkChildAccount($id);
        if ($checkSub) {
            foreach ($checkSub as $sub) {
                if ($sub->head_level == 4) {
                    $chtran = checkIsTransationAccount($sub->head_code);
                    if (!$chtran) {
                        $isDel = false;
                    }
                } else {
                    $checkSub = checkChildAccount($sub->head_code);
                    if ($checkSub) {
                        foreach ($checkSub as $sub) {
                            if ($sub->head_level == 4) {
                                $chtran = checkIsTransationAccount($sub->head_code);
                                if (!$chtran) {
                                    $isDel = false;
                                }
                            }
                        }
                    }
                }
            }
        }
        if (!$checkTransation) {
            $isDel = false;
        }
        if ($isDel) {
            $deldata = AccCoa::where('head_code', $id)->delete();
            $deldatap = AccCoa::where('pre_head_code', $id)->delete();
        } else {
            $deldata  = false;
            $deldatap  = false;
        }

        if ($deldata || $deldatap) {
            $info['status'] =  'success';
        } else {
            $info['status'] =  'faild';
        }
        echo json_encode($info);
    }

    public function selectedform($id)
    {
        $role_reult = AccCoa::where('head_code', $id)->first();

        $html = "";
        if ($role_reult) {
            $html .= "
           <form name=\"coaform\" id=\"coaform\" action=\"#\" method=\"post\" enctype=\"multipart/form-data\" onSubmit=\"return validate('nameLabel');\">
            <input type=\"hidden\" name=\"txtPHeadCode\" id=\"txtPHeadCode\"  value=\"" . $role_reult->pre_head_code . "\"/>
            <input type=\"hidden\" name=\"cnodeelem\" id=\"cnodeelem\"  value=\"\"/>
            <input type=\"hidden\" name=\"clevel\" id=\"clevel\"  value=\"\"/>                
            <table class=\"coaTable\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">
            <input type=\"hidden\" name=\"_token\" id=\"CSRF_TOKEN\" value=\"" . csrf_token() . "\">
            <input type=\"hidden\" name=\"txtHeadLevel\" id=\"txtHeadLevel\" class=\"form_input\"  value=\"" . $role_reult->head_level . "\"/>
            <input type=\"hidden\" name=\"txtHeadType\" id=\"txtHeadType\" class=\"form_input\"  value=\"" . $role_reult->head_type . "\"/>    
            <tr>
              <td>Head Code</td>
              <td><input type=\"text\" name=\"txtHeadCode\" id=\"txtHeadCode\" class=\"form_input\"  value=\"" . $role_reult->head_code . "\" readonly=\"readonly\"/></td>
            </tr>
            <tr>
              <td>Head Name</td>
              <td><input type=\"text\" name=\"txtHeadName\" id=\"txtHeadName\" class=\"form_input\" value=\"" . $role_reult->head_name . "\"  onkeyUp=\"checkNameField('txtHeadName','nameLabel')\"/>
              <input type=\"hidden\" name=\"HeadName\" id=\"HeadName\" class=\"form_input\" value=\"" . $role_reult->head_name . "\"/><label id=\"nameLabel\" class=\"errore\"></label>
              </td>
            </tr>
            <tr>
            <td>Parent Head</td>
            <td><input type=\"text\" name=\"txtPHead\" id=\"txtPHead\" class=\"form_input\" readonly=\"readonly\" value=\"" . $role_reult->pre_head_name . "\"/></td>
          </tr>";
            if ($role_reult->head_level > 3) {
                $html .= "<tr>
              <td>Note No</td>
              <td><input type=\"text\" name=\"noteNo\" id=\"noteNo\" class=\"form_input\"  value=\"" . $role_reult->note_no . "\"/></td>
             </tr>";
            }

            $html .= "<tr>
           <td>&nbsp;</td>
           <td id=\"innerCheck\">";
            $html .= "<input type=\"checkbox\" value=\"1\" name=\"IsActive\" id=\"IsActive\" size=\"28\"";
            if ($role_reult->is_active == 1) {
                $html .= "checked";
            }
            $html .= "/><label for=\"IsActive\">&nbsp;Is Active</label> &nbsp;&nbsp; ";

            if ($role_reult->head_level > 3 && ($role_reult->head_type == "A" || $role_reult->head_type == "L")) {
                $html .= "<input type=\"checkbox\" name=\"isFixedAssetSch\" value=\"1\" id=\"isFixedAssetSch\" size=\"28\"  onchange=\"isFixedAssetSch_change('isFixedAssetSch','" . $role_reult->head_type . "')\"";
                if ($role_reult->is_fixed_asset_sch == 1) {
                    $html .= "checked";
                }
                $html .= "/><label for=\"isFixedAssetSch\">&nbsp;Is Fixed Asset</label> &nbsp;&nbsp; ";
            }
            if ($role_reult->head_level > 3) {
                if ($role_reult->head_type == "A") {
                    $html .= "<input type=\"checkbox\" name=\"isStock\" value=\"1\" id=\isStock\" size=\"28\"  onchange=\"isStock_change()\"";
                    if ($role_reult->is_stock == 1) {
                        $html .= "checked";
                    }
                    $html .= "/><label for=\isStock\">&nbsp;Is Stock</label> &nbsp;&nbsp; ";
                    $html .= "<br/><input type=\"checkbox\" name=\"isCashNature\" value=\"1\" id=\"isCashNature\" size=\"28\"  onchange=\"isCashNature_change()\"";
                    if ($role_reult->is_cash_nature == 1) {
                        $html .= "checked";
                    }
                    $html .= "/><label for=\"isCashNature\">&nbsp;Is Cash Nature</label> &nbsp;&nbsp; ";

                    $html .= "<input type=\"checkbox\" name=\"isBankNature\" value=\"1\" id=\"isBankNature\" size=\"28\"  onchange=\"isBankNature_change()\"";
                    if ($role_reult->is_bank_nature == 1) {
                        $html .= "checked";
                    }
                    $html .= "/><label for=\"isBankNature\">&nbsp;Is Bank Nature</label> &nbsp;&nbsp; ";
                }
                $html .= "<input type=\"checkbox\" name=\"isSubType\" value=\"1\" id=\"isSubType\" size=\"28\"  onchange=\"isSubType_change('isSubType')\"";
                if ($role_reult->is_sub_type == 1) {
                    $html .= "checked";
                }
                $html .= "/><label for=\"isSubType\">&nbsp;Is Sub Type</label> &nbsp;&nbsp; ";
            }
            $html .= "</tr>";
            if ($role_reult->is_fixed_asset_sch == 1) {
                if ($role_reult->head_level > 3 && $role_reult->head_type == "A") {
                    $html .= "<tr id=\"fixedassetCode\">";
                    $html .= "<td>Fixed Asset Code</td><td><input type=\"text\" name=\"assetCode\" id=\"assetCode\" class=\"form_input\" value=\"" . $role_reult->asset_code . "\"/></td>";
                    $html .= "</tr>";
                    $html .= "<tr id=\"fixedassetRate\">";
                    $html .= "<td>Depreciation Rate % </td><td><input type=\"text\" name=\"DepreciationRate\" id=\"DepreciationRate\" class=\"form_input\" value=\"" . $role_reult->depreciation_rate . "\"/></td>";
                    $html .= "</tr>";
                } else if ($role_reult->head_level > 3 &&  $role_reult->head_type == "L") {
                    $html .= "<tr id=\"depreciationCode\"> <td>Depraciation Code</td><td><input type=\"text\" name=\"depCode\" id=\"depCode\" class=\"form_input\" value=\"" . $role_reult->dep_code . "\"/></td></tr>";
                }
            } else {
                $html .= "<tr id=\"fixedassetCode\"> </tr>";
                $html .= "<tr id=\"depreciationCode\"> </tr>";
            }
            if ($role_reult->is_sub_type == 1) {
                $html .= "<tr id=\"subtypeContent\">";
                $subdata = getsubTypeData();
                if ($subdata) {
                    $html .= "<td>Subtype</td>
                    <td><select  name=\"subtype\" id=\"subtype\" style=\"width: 90%;\" class=\"form-control\" >";
                    foreach ($subdata as $sub) {
                        $scheck = $sub->id == $role_reult->sub_type ? 'selected' : '';
                        $html .= "<option value=\"" . $sub->id . "\" " . $scheck . " >" . $sub->name . "</option>";
                    }
                    $html .= "</select><br/></td>";
                }
                $html .= "</tr>";
            } else {
                $html .= "<tr id=\"subtypeContent\"> </tr>";
            }
            $html .= "<tr> <td>&nbsp;</td><td>";


            if ($role_reult->head_level >= 2 &&  $role_reult->head_level <= 3) {
                $html .= "<input type=\"button\" name=\"btnNew\" id=\"btnNew\" class=\"btn btn-success\"  value=\"New\" onClick=\"newdata(" . $role_reult->head_code . ")\" />
                <input type=\"submit\" name=\"btnSave\" id=\"btnSave\" class=\"btn btn-success\"  value=\"Save\" disabled=\"disabled\"/>&nbsp;&nbsp;";
            }

            if ($role_reult->head_level >= 2 &&  $role_reult->head_level <= 4) {
                $html .= " <input type=\"submit\" name=\"btnUpdate\" id=\"btnUpdate\" value=\"Update\" class=\"btn btn-success\" /> &nbsp;&nbsp;";
            }

            if ($role_reult->head_level >= 2 &&  $role_reult->head_level <= 4) {
                $html .= "<input type=\"button\" name=\"btnDelete\" id=\"btnDelete\" class=\"btn btn-success\"  value=\"Delete\" onClick=\"delDataAcc(" . $role_reult->head_code . ")\" /> ";
            }

            if ($role_reult->head_level >= 2) {
                $html .= "&nbsp;&nbsp; <input type=\"button\" name=\"btnUndo\" id=\"btnUndo\" class=\"btn btn-success\"  value=\"Undo\" onClick=\"loadData('" . $role_reult->head_code . "_anchor','" . $role_reult->head_code . "')\" />";
            }

            $html .= "</td></tr></table></form>";
        }
        echo json_encode($html);
    }

    public function newform($id)
    {
        $newdata = AccCoa::where('head_code', $id)->first();
        $maxHeadCode = AccCoa::where('pre_head_code', $newdata->head_code)->max('head_code');

        $nid = $maxHeadCode;

        if ($nid > 0) {
            $HeadCode = $nid + 1;
        } else {
            $n = $nid + 1;
            if ($n / 10 < 1) {
                $HeadCode = $id . "0" . $n;
            } else {
                $HeadCode = $id . $n;
            }
        }

        $info['headcode']  =  $HeadCode;
        $info['rowdata']   =  $newdata;
        $info['headlabel'] =  $newdata->head_level + 1;
        echo json_encode($info);
    }

    public function getsubtype($id = null)
    {
        $subdata = getsubTypeData($id);
        $html = "";
        if ($subdata) {
            $html .= "<td>Subtype</td>
                <td><select  name=\"subtype\" id=\"subtype\" style=\"width: 90%;\" class=\" form-control\" >";
            foreach ($subdata as $sub) {
                $html .= "<option value=\"" . $sub->id . "\">" . $sub->name . "</option>";
            }
            $html .= "</select><br/></td>";
        }
        echo json_encode($html);
    }
}
