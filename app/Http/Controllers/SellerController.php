<?php

namespace App\Http\Controllers;

use App\Models\AccCoa;
use App\Models\AccSubcode;
use App\Models\AccSubtype;
use Illuminate\Http\Request;
use App\Models\Seller;
use App\Models\User;
use App\Models\Shop;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Hash;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Support\Facades\Auth;

class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $approved = null;
        $sellers = Seller::orderBy('created_at', 'desc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $user_ids = User::where('user_type', 'seller')->where(function($user) use ($sort_search){
                $user->where('name', 'like', '%'.$sort_search.'%')->orWhere('email', 'like', '%'.$sort_search.'%');
            })->pluck('id')->toArray();
            $sellers = $sellers->where(function($seller) use ($user_ids){
                $seller->whereIn('user_id', $user_ids);
            });
        }
        if ($request->approved_status != null) {
            $approved = $request->approved_status;
            $sellers = $sellers->where('verification_status', $approved);
        }
        $sellers = $sellers->paginate(15);
        return view('backend.sellers.index', compact('sellers', 'sort_search', 'approved'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.sellers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(User::where('email', $request->email)->first() != null){
            flash(translate('Email already exists!'))->error();
            return back();
        }
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->user_type = "seller";
        $user->password = Hash::make($request->password);
        if($user->save()){
            if(get_setting('email_verification') != 1){
                $user->email_verified_at = date('Y-m-d H:m:s');
            }
            else {
                $user->notify(new EmailVerificationNotification());
            }
            $user->save();

            $seller = new Seller;
            $seller->user_id = $user->id;
            if($seller->save()){
                $shop = new Shop;
                $shop->user_id = $user->id;
                $shop->slug = 'demo-shop-'.$user->id;
                $shop->save();

                if($shop->save()){

                    $lastCoa = AccCoa::where('head_code', 'LIKE', '2116%')->orderBy('head_code', 'desc')->first();
                    $headcode = $lastCoa ? $lastCoa->head_code + 1 : 2116000001;

                    $c_acc = $shop->user_id . '-' . $request->name;
                    $createby = Auth::user()->id; 
                    $createdate = now();
        
                    $employee_coa = [
                        'head_code'         => $headcode,
                        'head_name'         => $c_acc,
                        'pre_head_name'        => 'Seller Ledger',
                        'head_level'        => '4',
                        'is_active'         => '1',
                        'is_transaction'    => '1',
                        'is_gl'             => '0',
                        'head_type'         => 'L',
                        'is_budget'         => '0',
                        'is_depreciation'   => '0',
                        'depreciation_rate' => '0',
                        'customer_id'      => $shop->user_id,
                        'created_by'         => $createby,
                        'created_at'       => $createdate,
                    ];

                    AccCoa::create($employee_coa); 

                    $sub_acc = [
                        'sub_type_id'   => 6,
                        'name'        => $request->name,
                        'reference_no' => $shop->id,
                        'status'      => 1,
                        'created_at'=> now(),
                    ];

                    AccSubcode::create($sub_acc); 

                flash(translate('Seller has been inserted successfully'))->success();
                return redirect()->route('sellers.index');
                }
            }
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }


    public function seller_coa()
{
    $sellers = Seller::with('user')->get();

    foreach ($sellers as $seller) {
        if ($seller->user) {
            // Check for existing COA record for the seller's user ID
            if (!AccCoa::where('customer_id', $seller->user_id)->exists()) {
                
                // Determine next head code for COA
                $lastCoa = AccCoa::where('head_code', 'LIKE', '2116%')->orderBy('head_code', 'desc')->first();
                $headcode = $lastCoa ? $lastCoa->head_code + 1 : 2116000001;

                // Create COA record
                $seller_coa = [
                    'head_code'        => $headcode,
                    'head_name'        => "{$seller->user_id}-{$seller->user->name}",
                    'pre_head_name'    => 'Seller Ledger',
                    'head_level'       => '4',
                    'is_active'        => '1',
                    'is_transaction'   => '1',
                    'is_gl'            => '0',
                    'head_type'        => 'L',
                    'is_budget'        => '0',
                    'is_depreciation'  => '0',
                    'depreciation_rate'=> '0',
                    'customer_id'      => $seller->user_id,
                    'created_by'       => Auth::id(),
                    'created_at'       => now(),
                ];
                AccCoa::create($seller_coa);

                // Create Sub Account entry
                $sub_acc = [
                    'sub_type_id'  => 6,
                    'name'         => $seller->user->name,
                    'reference_no' => $seller->id,
                    'status'       => 1,
                    'created_at'   => now(),
                ];
                AccSubcode::create($sub_acc);
            }
        }
    }

    flash(translate('COA for all sellers have been updated successfully'))->success();
     // Prepare data for the view
     $data['title'] = 'List of Sub Accounts';
     $data['subType'] = AccSubtype::all();
 
     $data['subCode'] = AccSubcode::leftJoin('acc_subtypes', 'acc_subcodes.sub_type_id', '=', 'acc_subtypes.id')
         ->where('acc_subcodes.status', 1)
         ->select('acc_subcodes.id', 'acc_subtypes.name as subtypeName', 'acc_subcodes.created_at', 'acc_subcodes.name')
         ->orderBy('acc_subtypes.name')
         ->get();
 
     return view('backend.accounts.subaccount.subaccount', $data);
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
        $seller = Seller::findOrFail(decrypt($id));
        return view('backend.sellers.edit', compact('seller'));
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
        $seller = Seller::findOrFail($id);
        $user = $seller->user;
        $user->name = $request->name;
        $user->email = $request->email;
        if(strlen($request->password) > 0){
            $user->password = Hash::make($request->password);
        }
        if($user->save()){
            if($seller->save()){
                flash(translate('Seller has been updated successfully'))->success();
                return redirect()->route('sellers.index');
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
        $seller = Seller::findOrFail($id);
        Shop::where('user_id', $seller->user_id)->delete();
        Product::where('user_id', $seller->user_id)->delete();
        Order::where('user_id', $seller->user_id)->delete();
        OrderDetail::where('seller_id', $seller->user_id)->delete();
        User::destroy($seller->user->id);
        if(Seller::destroy($id)){
            flash(translate('Seller has been deleted successfully'))->success();
            return redirect()->route('sellers.index');
        }
        else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function show_verification_request($id)
    {
        $seller = Seller::findOrFail($id);
        return view('backend.sellers.verification', compact('seller'));
    }

    public function approve_seller($id)
    {
        $seller = Seller::findOrFail($id);
        $seller->verification_status = 1;
        if($seller->save()){
            flash(translate('Seller has been approved successfully'))->success();
            return redirect()->route('sellers.index');
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }

    public function reject_seller($id)
    {
        $seller = Seller::findOrFail($id);
        $seller->verification_status = 0;
        $seller->verification_info = null;
        if($seller->save()){
            flash(translate('Seller verification request has been rejected successfully'))->success();
            return redirect()->route('sellers.index');
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }


    public function payment_modal(Request $request)
    {
        $seller = Seller::findOrFail($request->id);
        return view('backend.sellers.payment_modal', compact('seller'));
    }

    public function profile_modal(Request $request)
    {
        $seller = Seller::findOrFail($request->id);
        return view('backend.sellers.profile_modal', compact('seller'));
    }

    public function updateApproved(Request $request)
    {
        $seller = Seller::findOrFail($request->id);
        $seller->verification_status = $request->status;
        if($seller->save()){
            return 1;
        }
        return 0;
    }

    public function login($id)
    {
        $seller = Seller::findOrFail(decrypt($id));

        $user  = $seller->user;

        auth()->login($user, true);

        return redirect()->route('dashboard');
    }

    public function ban($id) {
        $seller = Seller::findOrFail($id);

        if($seller->user->banned == 1) {
            $seller->user->banned = 0;
            flash(translate('Seller has been unbanned successfully'))->success();
        } else {
            $seller->user->banned = 1;
            flash(translate('Seller has been banned successfully'))->success();
        }

        $seller->user->save();
        return back();
    }
}
