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
                    $role = $user->roles;
                    $role = $role[0];
                    $user['name'] = $user->getNameAttribute();
                    $response["success"] = true;
                    $response["response"] = $user;
                    $response["role"] = $role->name;
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
        $user = BackpackUser::find($request->id);
        $user->fcm_token = $request->fcm_token;
        $user->save();
        return response()->json(["success"=>true,"response" => $user->fcm_token], 200);
    }

}
