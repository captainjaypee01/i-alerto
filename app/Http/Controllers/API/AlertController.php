<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Alert;
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

        $alerts = Alert::where("status",0)->orderBy('created_at','desc')->take(15)->get();
        foreach ($alerts as $alerts_key => $alerts_value) {
            $alert_users_info["alert_id"] = $alerts_value->id;
            $alert_users_info["name"] = $alerts_value->getNameAttribute();
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
        $alerts = Alert::orderBy('created_at','desc')->take(15)->get();
        foreach ($alerts as $alerts_key => $alerts_value) {
            $alert_users_info["id"] = $alerts_value->id;
            $alert_users_info["alert_user_name"] = $alerts_value->user->getNameAttribute();
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
        
        $response = [];

        if (!$alert) {
            $response["success"] = false;
        }
        else{
            $response["success"] = true;
            $response["response"] = $alert;



            $url ="https://fcm.googleapis.com/fcm/send";
            $fields=array(
                "to"=>"/topics/alert",
                "data" => array(
                    "body" => $request->address,
                    "title" => "Alert",
                    "from_activity" => "alert_notif",
                ),
            );

            $headers=array(
                'Authorization: key=AAAAvF1qE-A:APA91bHFsBPdURKVGuqE3IZB7Ztw5REJaRZQl7mpb1lrDuUM0YyYnWHEiZeJpgzKBT0YM4NoAzaznKQE5RnlsB9HdmrjasLRj0HvqGpqwknSOS7eRIg67PyLAbWTAO3RAAeeaTPob2EM',
                'Content-Type:application/json'
            );

            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_POST,true);
            curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($fields));
            $result=curl_exec($ch);
            // echo $result;
            curl_close($ch);
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
            'status' => ['required']
        ];

        $response = array('success' => false);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
        } else {
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
