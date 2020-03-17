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
                $role = $user->roles;
                $role = $role[0];
                $response["success"] = true;
                $response["response"] = $user;
                $response["role"] = $role->name;
            } else {
                $response["response"] = "These credentials do not match our records.";
            }
        } else {
            $response["response"] = "These credentials do not match our records.";
        }

        return response()->json($response, 200);
    }
}
