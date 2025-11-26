<?php

namespace App\Http\Controllers;

use App\Models\AccCoa;
use App\Models\AccSubcode;
use App\Models\AccSubtype;
use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Role;
use App\Models\User;
use App\Models\Target;
use App\Models\Warehouse;
use Hash;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_by = null;
        $role_id = null;
        $email = null;

        $staffs = Staff::join('users', 'staff.user_id', '=', 'users.id')
            ->leftJoin('roles', 'staff.role_id', '=', 'roles.id')
            ->select('staff.*', 'users.name as user_name', 'users.id as userId', 'users.email', 'roles.name as role_name', 'roles.id as roleId');

        if (!empty($request->user_id)) {
            $sort_by = $request->user_id;
            $staffs->where('users.id', 'like', '%' . $sort_by . '%');
        }
        if (!empty($request->role_id)) {
            $role_id = $request->role_id;
            $staffs->where('staff.role_id',  $role_id);
        }
        if (!empty($request->email)) {
            $email = $request->email;
            $staffs->where('users.email', 'like', '%' . $email . '%');
        }


        $staffs = $staffs->paginate(10);

        return view('backend.staff.staffs.index', compact('staffs', 'sort_by', 'email', 'role_id'));
    }


    public function login($id)
    {
        $staffs = Staff::findOrFail(decrypt($id));

        $user  = $staffs->user;

        auth()->login($user, true);

        return redirect()->route('admin.dashboard');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        $warehouses = Warehouse::all();
        return view('backend.staff.staffs.create', compact('roles', 'warehouses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (User::where('email', $request->email)->first() == null) {
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->mobile;
            $user->user_type = "staff";
            $user->password = Hash::make($request->password);
            if ($user->save()) {
                $staff = new Staff;
                $staff->user_id = $user->id;
                $staff->role_id = $request->role_id;
                $staff->warehouse_id = serialize($request->warehouse_id);
                if ($staff->save()) {
                    $subcode = [
                        'sub_type_id'   => 2,
                        'name'          => $user->name,
                        'reference_no'  => $staff->id,
                        'status'        => 1,
                        'created_at'    => now(),
                    ];
                    AccSubcode::create($subcode);

                    flash(translate('Staff has been inserted successfully'))->success();
                    return redirect()->route('staffs.index');
                }
            }
        }

        flash(translate('Email already used'))->error();
        return back();
    }


    public function staff_coa(Request $request)
    {
        $staffs = Staff::join('users', 'staff.user_id', '=', 'users.id')
            ->leftJoin('roles', 'staff.role_id', '=', 'roles.id')
            ->select('staff.*', 'users.name as user_name', 'users.id as userId', 'users.email', 'roles.name as role_name', 'roles.id as roleId')
            ->get();

        foreach ($staffs as $staff) {
            if ($staff->user) {
                $check_subcode = AccSubcode::where('sub_type_id', 2)->where('reference_no', $staff->user_id)->first();
                if (!$check_subcode) {
                    $sub_acc = [
                        'sub_type_id'   => 2,
                        'name'          => $staff->user_name,
                        'reference_no'  => $staff->user_id,
                        'status'        => 1,
                        'created_at'    => now(),
                    ];
                    AccSubcode::create($sub_acc);
                }
            }
        }
        return redirect()->route('sub-accounts.index');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $staff = Staff::findOrFail(decrypt($id));

        $warehousearray = getWearhouseBuUserId($staff->user_id);
        $roles = Role::all();
        $warehouses = Warehouse::all();
        return view('backend.staff.staffs.edit', compact('staff', 'roles', 'warehouses', 'warehousearray'));
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
        $staff = Staff::findOrFail($id);
        $user = $staff->user;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->mobile;
        if (strlen($request->password) > 0) {
            $user->password = Hash::make($request->password);
        }
        if ($user->save()) {
            $staff->role_id = $request->role_id;
            $staff->warehouse_id = serialize($request->warehouse_id);
            if ($staff->save()) {
                flash(translate('Staff has been updated successfully'))->success();
                return redirect()->route('staffs.index');
            }
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
        User::destroy(Staff::findOrFail($id)->user->id);
        if (Staff::destroy($id)) {
            flash(translate('Staff has been deleted successfully'))->success();
            return redirect()->route('staffs.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }
    public function target($id)
    {
        $staff = Staff::findOrFail(decrypt($id));
        $roles = Role::all();
        $targets = Target::where('user_id', $staff->user_id)->get();
        return view('backend.staff.staffs.target', compact('staff', 'roles', 'targets'));
    }

    //Target Start
    public function target_list()
    {
        dd('asd');
        $targets = Target::all();
        return view('backend.staff.target.index', compact('targets'));
    }
    public function target_add()
    {
        dd('asda');
        $staffs = Staff::where('role_type', 2)->get();
        return view('backend.staff.target.add', compact('staffs'));
    }
    public function create_target(Request $request)
    {
        $exists = Target::where('year', $request->year)->where('month', $request->month)->count();
        if ($exists > 0) {
            return redirect()->route('target.index')->with('error', 'Target Already Entered for this Month');
        }

        foreach ($request->staffs as $key => $staff) {
            if (!empty($product) && !empty($request->target[$key])) {
                Target::create(
                    array(
                        'year' => $request->year,
                        'month' => $request->month,
                        'user_id' => $staff,
                        'target' => $request->target[$key]
                    )
                );
            }
        }
        return redirect()->route('target.index');
    }
    public function edit_target(Request $request, $year, $month)
    {
        if ($request->method() == 'POST') {
            Target::where('year', $request->year)->where('month', $request->month)->delete();
            foreach ($request->staffs as $key => $staff) {
                if (!empty($staff) && !empty($request->target[$key])) {
                    $item = Target::create(
                        array(
                            'year' => $request->year,
                            'month' => $request->month,
                            'user_id' => $staff,
                            'target' => $request->target[$key]
                        )
                    );
                }
            }
            return redirect()->route('target.index');
        } else {
            $entry = Target::where('year', $year)->where('month', $month)->get();
            $staffs = Staff::where('role_type', 2)->get();
            return view('backend.staff.target.edit', compact('entry', 'staffs'));
        }
    }

    public function delete_target(Request $request, $year, $month)
    {
        Target::where('year', $year)->where('month', $month)->delete();
        return back();
    }
}
