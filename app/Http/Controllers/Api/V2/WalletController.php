<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\WalletCollection;
use App\Models\User;
use App\Models\Customer;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function balance($id)
    {
        $user = User::find($id);
        $latest = Wallet::where('user_id', $id)->latest()->first();
        $credit_limit = Customer::where('user_id', $id)->get();
        return response()->json([
            'balance' => format_price($user->balance),
            'credit_limit' => (count($credit_limit)>0) ? format_price($credit_limit[0]->credit_limit) : format_price(0),
            'last_recharged' => $latest == null ? "Not Available" : Carbon::createFromTimestamp(strtotime($latest->created_at))->diffForHumans(),
        ]);
    }

    public function walletRechargeHistory($id)
    {
        return new WalletCollection(Wallet::where('user_id', $id)->latest()->paginate(10));
    }

    public function processPayment(Request $request)
    {
        $order = new OrderController;
        $user = User::find($request->user_id);
        $credit_limit = Customer::where('user_id', $request->user_id)->get();
        $cr = (count($credit_limit)>0) ? $credit_limit[0]->credit_limit : 0;
        if (($user->balance+$cr) >= $request->amount) {
            $user->balance -= $request->amount;
            $user->save();
            return $order->store($request,true,$request->amount);
        } else {
			$bal = $user->balance;
            $user->balance = 0;
            $user->save();
            return $order->store($request,false,$bal);
        }
    }
}
