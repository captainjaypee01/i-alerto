<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\BackpackUser;
use App\Models\Barangay;
use App\Models\Evacuation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'data' => Announcement::with(['evacuations'])->orderBy('created_at', 'desc')->take(15)->get(),
        ], 200);
    }


    public function store(Request $request)
    {
        $request->merge(['evacuations' => json_decode($request->evacuations)]);
        $request->merge(['barangays' => json_decode($request->barangays)]);
        //
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'details' => ['required', 'string', 'max:255'],
            'evacuations' => ['array'],
            'barangays' => ['array']
        ];

        $response = array('success' => false);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
        } else {
            $announcement = Announcement::create([
                'title' => $request->title,
                'details' => $request->details
            ]);

            $response["success"] = true;
            $response['response'] = $announcement;
            
            $this->notify($request,$announcement);
        }
        return response()->json($response, 200);
    }

    public function notify($request,$announcement)
    {
        $barangays = $request->barangays;
        $evacuations = $request->evacuations;
        $fcm_tokens = [];
        $evacuation_center = ["is_evacuation" => false];
        $data = [];
        if($request->has('evacuations') && !empty($evacuations)){
            $evacuation_center["is_evacuation"] = true;
            $evac_ids = Evacuation::whereIn("name",$evacuations)->pluck("id");
            $announcement->evacuations()->attach($evac_ids);
            $evacuation_center["evac_ids"] = $evac_ids;
            if($request->has('barangays') && !empty($barangays)){
                $barangay_ids = Barangay::whereIn("name",$barangays)->pluck("id");
                $accounts = Barangay::with('residents','employees','officials')->whereIn('id',$barangay_ids)->get();
                $announcement->barangays()->attach($barangay_ids);
                foreach($accounts as $users)
                {
                    foreach($users->residents as $user)
                    {
                        if($user->user->fcm_token != null){
                            $fcm_tokens[] = $user->user->fcm_token;
                        }
                    }

                    foreach($users->employees as $user)
                    {
                        if($user->user->fcm_token != null){
                            $fcm_tokens[] = $user->user->fcm_token;
                        }
                    }

                    foreach($users->officials as $user)
                    {
                        if($user->user->fcm_token != null){
                            $fcm_tokens[] = $user->user->fcm_token;
                        }
                    }
                }
            }
            else{
                $accounts = BackpackUser::role(['employee','official','resident','relative'])->get();
                foreach($accounts as $user)
                {
                    if($user->fcm_token != null){
                        $fcm_tokens[] = $user->fcm_token;
                    }
                }
            }
            $data["body"] = $request->details;
            $data["title"] = "Evacuation Center";
            $data["from_activity"] = "announcement_notif";
            $data["evacuation_center"] = $evacuation_center;
        }
        else{
            $accounts = BackpackUser::role(['employee','official','resident','relative'])->get();
            foreach($accounts as $user)
            {
                if($user->fcm_token != null){
                    $fcm_tokens[] = $user->fcm_token;
                }
            }
            $data["body"] = $request->details;
            $data["title"] = "Announcement";
            $data["from_activity"] = "announcement_notif";
            $data["evacuation_center"] = $evacuation_center;
        }
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
}
