<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Target;
use Hash;
use Auth;

class TargetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $targets = Target::orderby('id', 'desc')->paginate(10);
    //     return view('backend.staff.target.index', compact('targets'));
    // }

    public function index(Request $request)
    {
        $dateString = $request->month;
        $parts = explode('/', $dateString);
        $month = ltrim($parts[0], '0');
    
        $user_id = $request->user_id;
        $user_role_ids = $request->user_role_id;
        $targets = Target::query();
    
        if (!empty($user_id)) {
            $targets->where('user_id', $user_id);
        }
    
        if(!empty($user_role_ids) && is_array($user_role_ids)){
            $targets->join('staff', 'staff.user_id', '=', 'targets.user_id')
                    ->leftJoin('roles', 'staff.role_id', '=', 'roles.id')
                    ->whereIn('staff.role_id', $user_role_ids); 
        }
    
        if (!empty($month)) {
            $targets->where('targets.month', $month);
        }
    
        $targets = $targets->orderBy('targets.id', 'desc')->paginate(20);
        return view('backend.staff.target.index', compact('targets', 'user_id', 'month', 'user_role_ids'));
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $staffs = Staff::Join('roles', 'staff.role_id', '=', 'roles.id')->where('roles.role_type', '2')->get();
        return view('backend.staff.target.add', compact('staffs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        //dd($request->new_customer);
        if (empty($request->month)) {
            flash(translate('Please Enter Month'))->error();
            return back();
        }
        $month = explode('/', $request->month)[0];
        $year = explode('/', $request->month)[1];

        foreach ($request->target as $staff_id => $tgt) {
            $exists = Target::where('year', $year)->where('month', $month)->where('user_id', $staff_id)->first();
            if (!$exists) {
                $target = new Target();
                $target->user_id = $staff_id;
                $target->year = $year;
                $target->month = $month;
                $target->target = $tgt;
                $target->terget_customer = $request->new_customer[$staff_id];
                $target->recovery_target = $request->recovery_target[$staff_id];
        
                $target->save();
            }
        }
        flash(translate('Target Saved successfully'))->success();
        return redirect()->route('targets.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $target = Target::findOrFail(decrypt($id));
        $staffs = Staff::Join('roles', 'staff.role_id', '=', 'roles.id')->where('roles.role_type', '2')->get();
        return view('backend.staff.target.edit', compact('staffs', 'target'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (empty($request->month)) {
            flash(translate('Please Enter Month'))->error();
            return back();
        }
        $month = explode('/', $request->month)[0];
        $year = explode('/', $request->month)[1];
        $staff = Target::findOrFail($id);
        $staff->target = $request->target;
        $staff->terget_customer = $request->terget_customer;
        $staff->recovery_target = $request->recovery_target;
        $staff->month = $month;
        $staff->year = $year;

        if ($staff->save()) {
            flash(translate('Target has been updated successfully'))->success();
            return redirect()->route('targets.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Target::destroy($id)) {
            flash(translate('Target has been deleted successfully'))->success();
            return redirect()->route('targets.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }
}
