<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Order;
use App\Models\Upload;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Models\Cart;
use Hash;
use Illuminate\Support\Facades\File;
use Storage;
use App\Models\Customer;
use App\Models\Area;
use App\Models\Referr_code;
use App\Models\Referr_usage;
use App\Models\Wallet;

class ProfileController extends Controller
{
    public function counters($user_id)
    {
        return response()->json([
            'cart_item_count' => Cart::where('user_id', $user_id)->count(),
            'wishlist_item_count' => Wishlist::where('user_id', $user_id)->count(),
            'order_count' => Order::where('user_id', $user_id)->count(),
        ]);
    }
    public function getAreaCode(){
    return response()->json([
                'area_code' => Area::get()
            ]);
    }
    public function getCreditInfo($user_id)
        {
    $cust = Customer::where('user_id', $user_id)->first();
    $cust->nid_photo = api_asset($cust->nid_photo);
    $cust->utility = api_asset($cust->utility);
    $cust->office_id = api_asset($cust->office_id);

            return response()->json([
                'credit_info' => $cust
            ]);
        }
        public function update(Request $request)
        {
            $user = User::find($request->id);

            $user->name = $request->name;

            if ($request->password != "") {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return response()->json([
                'result' => true,
                'message' => "Profile information updated"
            ]);
        }

        public function update_device_token(Request $request)
        {
            $user = User::find(auth()->user()->id);
            if(!$user){
                return response()->json([
                    'result' => false,
                    'message' => translate("User not found.")
                ]);
            }

            $user->device_token = $request->device_token;


            $user->save();

            return response()->json([
                'result' => true,
                'message' => translate("device token updated")
            ]);
        }
        
        public function updateImage(Request $request)
        {

            $type = array(
                "jpg" => "image",
                "jpeg" => "image",
                "png" => "image",
                "svg" => "image",
                "webp" => "image",
                "gif" => "image",
            );

            try {
                $image = $request->image;
                $request->filename;
                $realImage = base64_decode($image);

                $dir = public_path('uploads/all');
                $full_path = "$dir/$request->filename";

                $file_put = file_put_contents($full_path, $realImage); // int or false

                if ($file_put == false) {
                    return response()->json([
                        'result' => false,
                        'message' => "File uploading error",
                        'path' => ""
                    ]);
                }


                $upload = new Upload;
                $extension = strtolower(File::extension($full_path));
                $size = File::size($full_path);

                if (!isset($type[$extension])) {
                    unlink($full_path);
                    return response()->json([
                        'result' => false,
                        'message' => "Only image can be uploaded",
                        'path' => ""
                    ]);
                }


                $upload->file_original_name = null;
                $arr = explode('.', File::name($full_path));
                for ($i = 0; $i < count($arr) - 1; $i++) {
                    if ($i == 0) {
                        $upload->file_original_name .= $arr[$i];
                    } else {
                        $upload->file_original_name .= "." . $arr[$i];
                    }
                }

                //unlink and upload again with new name
                unlink($full_path);
                $newFileName = rand(10000000000,9999999999).date("YmdHis").".".$extension;
                $newFullPath = "$dir/$newFileName";

                $file_put = file_put_contents($newFullPath, $realImage);

                if ($file_put == false) {
                    return response()->json([
                        'result' => false,
                        'message' => "Uploading error",
                        'path' => ""
                    ]);
                }

                $newPath = "uploads/all/$newFileName";

                if (env('FILESYSTEM_DRIVER') == 's3') {
                    Storage::disk('s3')->put($newPath, file_get_contents(base_path('public/') . $newPath));
                    unlink(base_path('public/') . $newPath);
                }

                $upload->extension = $extension;
                $upload->file_name = $newPath;
                $upload->user_id = $request->id;
                $upload->type = $type[$upload->extension];
                $upload->file_size = $size;
                $upload->save();

                $user  = User::find($request->id);
                $user->avatar_original = $upload->id;
                $user->save();



                return response()->json([
                    'result' => true,
                    'message' => "Image updated",
                    'path' => api_asset($upload->id)
                ]);


            } catch (\Exception $e) {
                return response()->json([
                    'result' => false,
                    'message' => $e->getMessage(),
                    'path' => ""
                ]);
            }

        }

    public function updateCreditForm(Request $request)
        {
            $user = Customer::where(array('user_id'=>$request->id))->first();

    if(!empty($request->document_type))
            $user->document_type = $request->document_type;
    if(!empty($request->nid))
            $user->nid = $request->nid;
    if(!empty($request->ref1_name))
            $user->ref1_name = $request->ref1_name;
    if(!empty($request->ref1_phone))
            $user->ref1_phone = $request->ref1_phone;
    if(!empty($request->ref2_name))
            $user->ref2_name = $request->ref2_name;
    if(!empty($request->ref2_phone))
            $user->ref2_phone = $request->ref2_phone;
    if(!empty($request->dob))
            $user->dob = $request->dob;
    if(!empty($request->office))
            $user->office = $request->office;
    if(!empty($request->office_phone))
            $user->office_phone = $request->office_phone;
    if(!empty($request->designation))
            $user->designation = $request->designation;
    if(!empty($request->salary))
            $user->salary = $request->salary;
    if(!empty($request->ref1_relation))
            $user->ref1_relation = $request->ref1_relation;
    if(!empty($request->ref2_relation))
            $user->ref2_relation = $request->ref2_relation;

            $user->save();
    if(!empty($request->utility))
            $this->uploadImage($request,'utility');
    if(!empty($request->nid_photo))
            $this->uploadImage($request,'nid_photo');
    if(!empty($request->office_id))
            $this->uploadImage($request,'office_id');
            return response()->json([
                'result' => true,
                'message' => "Credit Form information updated"
            ]);
        }

        public function uploadImage($request,$name)
        {

            $type = array(
                "jpg" => "image",
                "jpeg" => "image",
                "png" => "image",
                "svg" => "image",
                "webp" => "image",
                "gif" => "image",
            );

            try {
                $image = $request->$name;
                $realImage = base64_decode($image);

                $dir = public_path('uploads/all');
                $fname = $name."Name";
                $full_path = "$dir/".$request->$fname;

                $file_put = file_put_contents($full_path, $realImage); // int or false

                if ($file_put == false) {
                    return false;
                }


                $upload = new Upload;
                $extension = strtolower(File::extension($full_path));
                $size = File::size($full_path);

                if (!isset($type[$extension])) {
                    unlink($full_path);
                    return false;
                }

                // $arr = explode('.', File::name($full_path));
                $upload->file_original_name = null;
            $arr = explode('.', File::name($full_path));
                for ($i = 0; $i < count($arr) - 1; $i++) {
                    if ($i == 0) {
                        $upload->file_original_name .= $arr[$i];
                    } else {
                        $upload->file_original_name .= "." . $arr[$i];
                    }
                }
                //unlink and upload again with new name
                unlink($full_path);
                $newFileName = rand(10000000000,9999999999).date("YmdHis").".".$extension;
                $newFullPath = "$dir/$newFileName";

                $file_put = file_put_contents($newFullPath, $realImage);

                if ($file_put == false) {
                    return false;
                }

                $newPath = "uploads/all/$newFileName";

                if (env('FILESYSTEM_DRIVER') == 's3') {
                    Storage::disk('s3')->put($newPath, file_get_contents(base_path('public/') . $newPath));
                    unlink(base_path('public/') . $newPath);
                }

                $upload->extension = $extension;
                $upload->file_name = $newPath;
                $upload->user_id = $request->id;
                $upload->type = $type[$upload->extension];
                $upload->file_size = $size;
                $upload->save();

                $user  = Customer::where(array('user_id'=>$request->id))->first();
                $user->$name = $upload->id;
                $user->save();



                return true;


            } catch (\Exception $e) {
                return false;
            }

        }

    public function apply_referr_code(Request $request)
        {

    $status = \App\Models\AffiliateOption::where('type', 'download_app')->first()->status;

    if($status==0){
        return response()->json([
                    'result' => false,
                    'message' => 'We are sorry! Referral campaign is now off.'
                ]);
    }

            $referr = Referr_code::where('code', $request->referr_code)->first();


            if ($referr == null) {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid referral code!'
                ]);
            }


            if (!empty($referr->used_by)) {
                return response()->json([
                    'result' => false,
                    'message' => 'Already used this code!'
                ]);
            }

            $used_by = Referr_code::where('used_by', $request->user_id)->first();
            if ($used_by!=null) {
                return response()->json([
                    'result' => false,
                    'message' => 'Already used another code once !'
                ]);
            }
            

            $used_by = Referr_usage::where('user_id', $request->user_id)->first();
            if ($used_by!=null) {
                return response()->json([
                    'result' => false,
                    'message' => 'Already used another code once !'
                ]);
            }
    $user = User::findOrFail($request->user_id);
    if (empty($user->phone)) {
                return response()->json([
                    'result' => false,
                    'message' => 'Please update your phone from profile page first.'
                ]);
            }


            $referr->used_by = $request->user_id;
            $referr->save();
            $usage = new Referr_usage();
            $usage->code_id = $referr->id;
            $usage->user_id = $request->user_id;
            $usage->save();
            $amount = \App\Models\AffiliateOption::where('type', 'download_app')->first()->percentage;
            $us = User::findOrFail($request->user_id);
            $wallet = new Wallet();
            $wallet->user_id = $referr->user_id;
            $wallet->payment_method = 'Referral Rewards for new account registration-'.$us->customer->customer_id;
            $wallet->amount = $amount;
            $wallet->payment_details = json_encode(array('code'=>$request->referr_code,'user_id'=>$request->user_id));
            $wallet->save();
            $user = User::findOrFail($referr->user_id);
            $user->balance = $user->balance + $amount;
            $user->save();
            $discount1 = json_decode(\App\Models\AffiliateOption::where('type', 'download_app')->first()->details)->discount1;
            if($discount1>0){
            $wallet = new Wallet();
            $wallet->user_id = $request->user_id;
            $wallet->payment_method = 'Referral Rewards for account opening';
            $wallet->amount = $discount1;
            $wallet->payment_details = json_encode(array('code'=>$request->referr_code,'user_id'=>$referr->user_id));
            $wallet->save();
            $user = User::findOrFail($request->user_id);
            $user->balance = $user->balance + $discount1;
            $user->save();
            }
            
            return response()->json([
                    'result' => true,
                    'message' => 'Referral Code Applied'
                ]);

        }

}
