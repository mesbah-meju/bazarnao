<?php

namespace App\Http\Controllers;

use App\Models\AccCoa;
use App\Models\AccSubtype;
use App\Models\AccSubcode;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use App\Models\Order;
use App\Models\Wallet;
use Hash;
// use Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // $customers = Customer::where('area_code','07')->get();
        // $codestart = 7000000;
        // foreach($customers as $key => $customer){
        //     $codestart += 1;
        //     $customer->customer_id = '0'.$codestart;
        //     $customer->save();
        // }

        $sort_search = null;


        $customers = Customer::orderBy('created_at', 'desc');
        $customers = $customers->join('areas', 'areas.code', '=', 'customers.area_code')
            ->leftJoin('orders', function ($join) {
                $join->on('customers.user_id', '=', 'orders.user_id')
                    ->whereNull('orders.canceled_by')
                    ->whereNotNull('orders.delivered_by');
            });

        if ($request->has('search')) {
            $sort_search = $request->search;
            $user_ids = User::where('user_type', 'customer')->join('customers', 'users.id', '=', 'customers.user_id')->where(function ($user) use ($sort_search) {
                $user->where('name', 'like', '%' . $sort_search . '%')->orWhere('email', 'like', '%' . $sort_search . '%')->orWhere('customer_id', 'like', '%' . $sort_search . '%')->orWhere('phone', 'like', '%' . $sort_search . '%')->orWhere('customer_type', 'like', '%' . $sort_search . '%');
            })->pluck('users.id')->toArray();

            $customers = $customers->where(function ($customer) use ($user_ids) {
                $customer->whereIn('customers.user_id', $user_ids);
            });
        }

        if (!empty($request->customer_type)) {
            $customers = $customers->where('customers.customer_type', $request->customer_type);
        }

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
            $customers->whereBetween('customers.created_at', [$start_date, $end_date]);
        }

        $start_date = !empty($start_date) ? date('Y-m-d', strtotime($start_date)) : '';
        $end_date = !empty($end_date) ? date('Y-m-d', strtotime($end_date)) : '';
        $customers = $customers->select('customers.*', 'areas.name as areacode', \DB::raw('SUM(orders.grand_total) as total_sales'))
            ->groupBy('customers.id', 'areas.name');
        $customers = $customers->paginate(15);
        return view('backend.customer.customers.index', compact('customers', 'sort_search', 'start_date', 'end_date',));
    }

    public function customers_coa(Request $request)
    {
        $customers = Customer::get();
        foreach ($customers as $customer) {
            if ($customer->user) {
                $check_subcode = AccSubcode::where('sub_type_id', 3)->where('reference_no', $customer->user_id)->first();
                if (!$check_subcode) {
                    $sub_acc = [
                        'sub_type_id'   => 3,
                        'name'          => $customer->user->name,
                        'reference_no'   => $customer->user_id,
                        'status'        => 1,
                        'created_at'    => now(),
                    ];
                    AccSubcode::create($sub_acc);
                }
            } else {
                $customer->delete();
            }
        }
        return redirect()->route('sub-accounts.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.customer.customers.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */



    public function store(Request $request)
    {
        if (empty($request->email)) {
            if (User::where('phone', $request->phone)->first() == null) {
                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->user_type = "customer";
                $user->password = Hash::make($request->password);
                $user->address = $request->address;
                $exists = Customer::where('area_code', $request->area_code)->orderBy('created_at', 'desc')->first();

                if ($exists) {
                    $increment = (int)$exists->customer_id + 1;
                    $customer_id = $increment;
                    if (strlen((string)$customer_id) < 8) {
                        $customer_id = '0' . $customer_id;
                    }
                } else {
                    $customer_id = (string)$request->area_code . '000001';
                    if (strlen((string)$customer_id) < 8) {
                        $customer_id = '0' . $customer_id;
                    }
                }

                if ($user->save()) {
                    $customer = new Customer();
                    $customer->user_id = $user->id;
                    $customer->created_from = 'Web';
                    $customer->area_code = $request->area_code;
                    $customer->customer_id = $customer_id;
                    $customer->staff_id = 163684;

                    if ($customer->save()) {
                        $subcode = [
                            'sub_type_id'   => 3,
                            'name'        => $request->name,
                            'reference_no' => $customer->id,
                            'status'      => 1,
                            'created_at' => now(),
                        ];
                        AccSubcode::create($subcode);

                        flash(translate('Customer has been inserted successfully'))->success();
                        return redirect()->route('customers.index');
                    }
                }
            } else {
                flash(__('This Phone Number already Exists.'))->error();
                return back();
            }
        } else {
            if (User::where('email', $request->email)->first() == null && User::where('phone', $request->phone)->first() == null) {
                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->user_type = "customer";
                $user->password = Hash::make($request->password);
                $user->address = $request->address;
                $exists = Customer::where('area_code', $request->area_code)->orderBy('created_at', 'desc')->first();

                if ($exists) {
                    $increment = (int)$exists->customer_id + 1;
                    $customer_id = $increment;
                    if (strlen((string)$customer_id) < 8) {
                        $customer_id = '0' . $customer_id;
                    }
                } else {
                    $customer_id = (string)$request->area_code . '000001';
                    if (strlen((string)$customer_id) < 8) {
                        $customer_id = '0' . $customer_id;
                    }
                }

                if ($user->save()) {
                    $customer = new Customer();
                    $customer->created_from = 'Web';
                    $customer->user_id = $user->id;
                    $customer->area_code = $request->area_code;
                    $customer->customer_id = $customer_id;
                    $customer->staff_id = 163684;

                    if ($customer->save()) {
                        $subcode = [
                            'sub_type_id'   => 3,
                            'name'          => $user->name,
                            'reference_no'  => $customer->id,
                            'status'        => 1,
                            'created_at'    => now(),
                        ];
                        AccSubcode::create($subcode);

                        flash(translate('Customer has been inserted successfully'))->success();
                        return redirect()->route('customers.index');
                    }
                }
            } else if (User::where('email', $request->email)->first() != null && User::where('phone', $request->phone)->first() == null) {
                flash(__('This Email already Exists.'))->error();
                return back();
            } else if (User::where('email', $request->email)->first() == null && User::where('phone', $request->phone)->first() != null) {
                flash(__('This Phone Number already Exists.'))->error();
                return back();
            } else {
                flash(__('This Email & Phone Number already Exists.'))->error();
                return back();
            }
        }
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
        //
        $customer = Customer::findOrFail($id);
        return view('backend.customer.customers.edit', compact('customer'));
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
        $staff = Customer::findOrFail($id);

        if (strtolower($request->credit_enable) == 'on') {
            $staff->credit_enable = 1;
        }

        $staff->credit_limit = $request->credit_limit;
        $staff->office = $request->office;
        $staff->office_phone = $request->office_phone;
        $staff->designation = $request->designation;
        $staff->salary = $request->salary;
        $staff->nid = $request->nid;
        $staff->document_type = $request->document_type;
        $staff->nid_photo = $request->nid_photo;
        $staff->ref1_name = $request->ref1_name;
        $staff->ref1_phone = $request->ref1_phone;
        $staff->ref1_relation = $request->ref1_relation;
        $staff->ref2_name = $request->ref2_name;
        $staff->ref2_phone = $request->ref2_phone;
        $staff->ref2_relation = $request->ref2_relation;
        $staff->testimonial = $request->testimonial;
        $staff->utility = $request->utility;
        $staff->office_id = $request->office_id;
        $staff->customer_type = $request->customer_type;
        $staff->staff_id = $request->executive;
        $staff->area_code = $request->area_code;

        $user = $staff->user;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->mobile;

        $postpon = $request->mobile;
        $userpho = $user->phone;

        if ($postpon != $userpho) {
            if (User::where('phone', $request->mobile)->first() != null) {
                flash(translate('Phone already exists.'));
                return back();
            }
        }

        if ($user->save()) {
            if ($staff->save()) {
                flash(translate('Customer has been updated successfully'))->success();
                return redirect()->route('customers.index');
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
        Order::where('user_id', Customer::findOrFail($id)->user->id)->delete();
        User::destroy(Customer::findOrFail($id)->user->id);
        if (Customer::destroy($id)) {
            flash(translate('Customer has been deleted successfully'))->success();
            return redirect()->route('customers.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    public function login($id)
    {
        $customer = Customer::findOrFail(decrypt($id));
        $user  = $customer->user;
        Auth::login($user, true);

        session(['admin_login' => true]);
        return redirect()->route('dashboard');
    }

    public function ban($id)
    {
        $customer = Customer::findOrFail($id);

        if ($customer->user->banned == 1) {
            $customer->user->banned = 0;
            flash(translate('Customer UnBanned Successfully'))->success();
        } else {
            $customer->user->banned = 1;
            flash(translate('Customer Banned Successfully'))->success();
        }

        $customer->user->save();
        return back();
    }

    function wallet_refund(Request $request)
    {
        $amt = $request->amount;
        User::where('id', $request->user_id)->decrement('balance', $amt);

        $wallet = new Wallet();
        $wallet->user_id = $request->user_id;
        $wallet->payment_method = 'Wallet amount refund for cash payment';
        $wallet->amount = -1 * $amt;
        $wallet->payment_details = json_encode(array('payment_method' => 'Cash', 'payment_date' => date('Y-m-d H:i:s')));
        $wallet->save();
        return back();
    }

    function creadit_due(Request $request)
    {
        $amt = $request->due_amount;
        User::where('id', $request->due_user_id)->increment('balance', $amt);

        $wallet = new Wallet();
        $wallet->user_id = $request->due_user_id;
        $wallet->payment_method = 'creadit Wallet amount for hand payment';
        $wallet->amount = $amt;
        $wallet->payment_details = json_encode(array('payment_method' => 'Cash', 'payment_date' => date('Y-m-d H:i:s')));
        $wallet->save();

        $cust_ledger = array();
        $cust_ledger['customer_id'] = $request->due_user_id;
        $cust_ledger['order_id'] = 'Payment';
        $cust_ledger['descriptions'] = 'Wallet Payment';
        $cust_ledger['type'] = 'Payment';
        $cust_ledger['debit'] = 0;
        $cust_ledger['credit'] = $request->due_amount;
        $cust_ledger['date'] = date('Y-m-d H:i:s');
        save_customer_ledger($cust_ledger);

        return back();
    }
}
