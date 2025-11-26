<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
{
    $notifications = Notification::leftJoin('users as recipients', 'recipients.id', '=', 'notifications.user_id')
                                 ->leftJoin('users as senders', 'senders.id', '=', 'notifications.sender_id')
                                 ->select('notifications.*', 'recipients.name as recipient_name','recipients.email as recipient_email', 'senders.name as sender_name')
                                 ->get();

    return view('backend.marketing.notification.index', compact('notifications'));
}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $sort_by= null;
        return view('backend.marketing.notification.create',compact('sort_by'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $userIds = $request->user_id;
        $senderId = auth()->id(); 
        
        if(empty($request->user_id[0])){
            $tokens = User::whereNotNull('device_token')->groupBy('device_token')
            ->select('id','device_token');
        }else{
            $tokens = User::whereNotNull('device_token')->groupBy('device_token')
            ->select('id','device_token')->whereIn('id',$userIds);
        }

        $alltokens = $tokens->get()->pluck('device_token')->toarray();
       

        $url = 'https://fcm.googleapis.com/fcm/send';
        $api_key = env('FCMKEY');
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key=' . $api_key
        );

        if($request->image_link == !null){
            $notificationArray = array(
                'title' => $request->title,
                'body' => $request->message,
                'image' => $request->image_link,
                'sound' => 'default',
                'badge' => '1'
              );
         }else{
            $notificationArray = array(
                'title' => $request->title,
                'body' => $request->message,
                'sound' => 'default',
                'badge' => '1'
              );
            
         }
         $regIdChunk = array_chunk($alltokens,1000);
         foreach($regIdChunk as $RegId){
            $fields = array(
                'registration_ids' => $RegId,
                'notification' => $notificationArray,
                'priority' => 'high'
               );

               $dataString = json_encode($fields);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$dataString );
            $result = curl_exec($ch);
          if ($result === FALSE){
              die('FCM Send Error: ' . curl_error($ch));
              Log::debug(curl_error($ch));
          }else{
            foreach ($userIds as $userId){
                $notification = new Notification;
                $notification->user_id = $userId;
                $notification->sender_id = $senderId;
                $notification->title = $request->title;
                $notification->message = $request->message;
                $notification->save();
            }
          }
          curl_close($ch);
          
         }

         if($result){
            flash('Notification(s) has been sent successfully')->success();
            return redirect()->route('notifications.index');
        }else{
            flash($ch)->warning();
            return back();
        }
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $notifications = Notification::findOrFail($id);
        return view('backend.marketing.notification.edit', compact('notifications'));
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
        $userIds = $request->user_id;
        $senderId = auth()->id(); 
        $notification = Notification::findOrFail($id);

        // $notification->user_id = $userId;
            $notification->sender_id = $senderId;
            $notification->title = $request->title;
            $notification->image_url = $request->image_url;
            $notification->message = $request->message;
    
        $notification->save();


        flash(translate('police Station Contact has been updated successfully'))->success();
        return redirect()->route('notification.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        flash(translate('Notification has been deleted successfully'))->success();
        return redirect()->route('notification.create');
    }

}
