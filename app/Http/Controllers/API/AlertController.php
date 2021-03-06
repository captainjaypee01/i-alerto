<?php

namespace App\Http\Controllers\API;

use App\Events\ChatAlert;
use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\BackpackUser;
use App\Models\Conversation;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlertController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $alert_user = [];
        $alert_users_info = [];

        $alerts = Alert::where("status",0)->has('user')->orderBy('created_at','desc')->take(15)->get();
        foreach ($alerts as $alerts_key => $alerts_value) {
            $alert_users_info["alert_id"] = $alerts_value->id;
            $alert_users_info["name"] = $alerts_value->getNameAttribute();
            $alert_users_info["contact_number"] = $alerts_value->user->contact_number;
            $alert_users_info["latitude"] = $alerts_value->latitude;
            $alert_users_info["longitude"] = $alerts_value->longitude;
            $alert_users_info["address"] = $alerts_value->address;
            $alert_users_info["type"] = $alerts_value->type;
            $alert_users_info['status'] = $alerts_value->status;
            $alert_users_info["created_at"] = $alerts_value->getMobileCreatedAtAttribute();
            $alert_user[] = $alert_users_info;
        }

        return response()->json([
            'data' => $alert_user,
        ], 200);
    
    }

    public function my_alerts($id)
    {
        $alert_user = [];
        $alert_users_info = [];
        
        $alerts = Alert::where("user_id",$id)->has('user')->orderBy('created_at','desc')->take(15)->get();
        foreach ($alerts as $alerts_key => $alerts_value) {
            $alert_users_info["alert_id"] = $alerts_value->id;
            $alert_users_info["name"] = $alerts_value->getNameAttribute();
            $alert_users_info["contact_number"] = $alerts_value->user->contact_number;
            $alert_users_info["latitude"] = $alerts_value->latitude;
            $alert_users_info["longitude"] = $alerts_value->longitude;
            $alert_users_info["address"] = $alerts_value->address;
            $alert_users_info["type"] = $alerts_value->type;
            $alert_users_info['status'] = $alerts_value->status;
            $alert_users_info["created_at"] = $alerts_value->getMobileCreatedAtAttribute();
            $alert_user[] = $alert_users_info;
        }

        return response()->json([
            'data' => $alert_user,
        ], 200);
    }

    public function history()
    {
        $alerts = Alert::orderBy('created_at','desc')->has('user')->take(15)->get();
        foreach ($alerts as $alerts_key => $alerts_value) {
            $alert_users_info["id"] = $alerts_value->id;
            $alert_users_info["alert_user_name"] = $alerts_value->getNameAttribute();
            $alert_users_info["latitude"] = $alerts_value->latitude;
            $alert_users_info["longitude"] = $alerts_value->longitude;
            $alert_users_info["address"] = $alerts_value->address;
            $alert_users_info['type'] = $alerts_value->type;
            $alert_users_info['status'] = $alerts_value->status;
            $alert_users_info["created_at"] = $alerts_value->getMobileCreatedAtAttribute();
            $alert_users_info["responded_at"] = $alerts_value->getMobileRespondedAtAttribute();
            $alert_user[] = $alert_users_info;
        }

    
        
        return response()->json([
            'data' => $alert_user,
        ], 200);
    }


    public function chat(Request $request)
    {
        $has_image = intval($request->has_image) == 1 ? 1 : 0;
        $user_id = $request->user_id;
        $alert_id = $request->alert_id;
        $response = [];
        $req_message = empty($request->message) ? NULL : $request->message;
        $message = [];
        if($req_message != null){
            $msg = [
                'user_id' => $user_id,
                'alert_id' => $alert_id,
                'message' => $req_message,
            ];
            $message = Conversation::create($msg);
        }
        // $response["message"] = $message;
        if($has_image){
            $image_name = $request->user_id."-".$request->alert_id."-".now()->timestamp.".jpg";
            $request->disaster_image->move(public_path("/chat/images"), $image_name);
            $img = [
                'user_id' => $user_id,
                'alert_id' => $alert_id,
                'image' => $image_name,
                'has_image' => $has_image
            ];
            $message = Conversation::create($img);
            // $response["image"] = $image;
        }
        unset($message['user']);
        event(new ChatAlert($message,$request->alert_id));
        // return response()->json($message,200);
    }



    public function conversations()
    {
        $alert_id = request()->segment(6);
        $conv = Conversation::where("alert_id",$alert_id)->get();
        return response()->json($conv, 200);
    }

    public function conversation_status()
    {
        $alert_id = request()->segment(6);
        $user_id = request()->segment(7);
        $role = request()->segment(8);
        // $conv = Conversation::where('alert_id',$alert_id)->first();
        $alert = Alert::find($alert_id);
        $response = ["is_empty"=>false,"has_chat" => false];
        if($role == "resident"){
            $uid = $alert->user_id;
            if($uid == $user_id){
                $response["has_chat"] = true;
            }
        }
        else if ($role == "official" || $role == "employee"){
            $with_chat = $alert->conversations->where("alert_id",$alert_id)->first();
            if($with_chat){
                $response["role"] = $with_chat->user->role;
                $has_chat = $alert->conversations->where("user_id",$user_id)->first();
                if($has_chat){
                    $response["role"] = $has_chat->user->role;
                    $response["has_chat"] = true;
                }
                else{
                    $response["has_chat"] = false;
                }
                
            }
            else{
                $response["is_empty"] = true;
            }
        }
        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $alert = Alert::create([
                'user_id' => $request->user_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'address' => $request->address,
                'type' => $request->type,
                'status' => 0
            ]);

        $alert["alert_id"] = $alert->id;
        
        $response = [];

        if (!$alert) {
            $response["success"] = false;
        }
        else{
            $response["success"] = true;
            $response["response"] = $alert;
            

            $accounts = BackpackUser::role(['employee','official'])->get();
            $fcm_tokens = [];
            foreach($accounts as $user)
            {
                if($user->fcm_token != null){
                    $fcm_tokens[] = $user->fcm_token;
                }
            }
            
            $data["body"] = $request->address;
            $data["title"] = "Alert";
            $data["from_activity"] = "alert_notif";
            $url = 'https://fcm.googleapis.com/fcm/send';
            $fields = array (
                'registration_ids' => $fcm_tokens,
                'data' => $data,
            );
            $fields = json_encode ($fields);

            $headers = array (
                'Authorization: key=' . "AAAAvF1qE-A:APA91bHFsBPdURKVGuqE3IZB7Ztw5REJaRZQl7mpb1lrDuUM0YyYnWHEiZeJpgzKBT0YM4NoAzaznKQE5RnlsB9HdmrjasLRj0HvqGpqwknSOS7eRIg67PyLAbWTAO3RAAeeaTPob2EM",
                'Content-Type: application/json'
            );

            $ch = curl_init ();
            curl_setopt ( $ch, CURLOPT_URL, $url );
            curl_setopt ( $ch, CURLOPT_POST, true );
            curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

            $result = curl_exec ( $ch );
            // echo $result;
            curl_close ( $ch );
        }

        return response()->json($response, 200);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'status' => ['required'],
            'auto_reply' => ['required'],
            'user_id' => ['required']
        ];

        $response = array('success' => false);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
        } else {
            $req_message = empty($request->auto_reply) ? NULL : $request->auto_reply;
            $message = [];
            if($req_message != null){
                $msg = [
                    'user_id' => $request->user_id,
                    'alert_id' => $id,
                    'message' => $req_message,
                ];
                $message = Conversation::create($msg);
            }
            unset($message['user']);
            event(new ChatAlert($message,$id));

            $alert = Alert::with('user')->get()->find($id);
            // $alert->fill($request->all())->save();
            $alert->update(["status" => $request->status,"responded_at" => Carbon::now()]);
            $response["success"] = true;
            $response['response'] = $alert;
        }
        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
