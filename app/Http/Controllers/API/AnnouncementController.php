<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\BackpackUser;
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
            'data' => Announcement::with(['evacuations','barangays'])->orderBy('created_at', 'desc')->take(15)->get(),
        ], 200);
    }


    public function store(Request $request)
    {
        //
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'details' => ['required', 'string', 'max:255']
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
            
            $fcm_tokens = [];
            $accounts = BackpackUser::all();
            foreach($accounts as $user)
            {
                if($user->fcm_token != null){
                    $fcm_tokens[] = $user->fcm_token;
                }
            }

            $url = 'https://fcm.googleapis.com/fcm/send';
            $fields = array (
                'registration_ids' => $fcm_tokens,
                'data' => array (
                    "body" => $request->details,
                    "title" => "Announcement",
                    "from_activity" => "announcement_notif",
                )
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
}
