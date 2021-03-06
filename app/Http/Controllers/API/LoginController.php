<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BackpackUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login_submit(Request $request)
    {
        $user = BackpackUser::with('roles')->where('email', '=', $request->email)->first();


        $response = ["success" => false];

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                if (!is_null($user->email_verified_at)) {
                    
                    $response["success"] = true;
                    $role_name = $user->role;
                    $resident = $user->resident;
                    $resident["role"] = $role_name;
                    $response["response"] = $role_name == "resident" ? $user->resident : $user; 

                    
                }
                else{
                    $response["response"] = "Your account is not yet verified.";
                }
            }
            else{
                $response["response"] = "These credentials do not match our records.";
            }
        }
        else{
            $response["response"] = "These credentials do not match our records.";
        }

        return response()->json($response, 200);
    }

    public function update_token(Request $request){
        BackpackUser::where('fcm_token',$request->fcm_token)->update(["fcm_token" => null]);
        $user = BackpackUser::find($request->id);
        $user->fcm_token = $request->fcm_token;
        $user->save();
        return response()->json(["success"=>true,"response" => $user->fcm_token], 200);
    }

    public function remove_token(Request $request)
    {
        $user = BackpackUser::find($request->id);
        $user->fcm_token = NULL;
        $user->save();
        return response()->json(["success"=>true,"response" => $user], 200);
    }

}
