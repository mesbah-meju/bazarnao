<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmsTemplate;

class SmsTemplateController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:sms_templates'])->only('index');
    }

    public function index()
    {
        $sms_templates = SmsTemplate::all();
        return view('backend.otp_systems.configurations.sms_templates', compact('sms_templates'));
    }

    public function create()
    {
        abort(404);
    }


    public function store(Request $request)
    {
        abort(404);
    }


    public function show($id)
    {
        abort(404);
    }

    public function edit($id)
    {
        abort(404);
    }


    public function update(Request $request, $id)
    {
        $sms_template               = SmsTemplate::where('id', $id)->first();
        $sms_template->sms_body     = str_replace("\r\n",'',$request->body);
        $sms_template->template_id  = $request->template_id;

        if ($request->status == 1) {
            $sms_template->status = 1;
        }
        else{
            $sms_template->status = 0;
        }

        if($sms_template->save()){
            flash(translate('SMS Template has been updated successfully'))->success();
            return back();
        } else {
            flash(translate('Sorry! Something went wrong.'))->error();
            return back();
        }
    }


    public function destroy($id)
    {
        abort(404);
    }
}
