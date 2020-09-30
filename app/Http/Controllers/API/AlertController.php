<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $alert_user = [];
        // $alert_users_info = [];

        // $alerts = Alert::where("status",0)->orderBy('created_at','desc')->take(15)->get();
        // foreach ($alerts as $alerts_key => $alerts_value) {
        //     $alert_users_info["alert_id"] = $alerts_value->id;
        //     $alert_users_info["name"] = $alerts_value->user->name;
        //     $alert_users_info["address"] = $alerts_value->address;
        //     $alert_users_info["created_at"] = $alerts_value->getMobileCreatedAtAttribute();
        //     $alert_user[] = $alert_users_info;
        // }
        // return response()->json([
        //     'data' => $alert_user,
        // ], 200);
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
        //
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
