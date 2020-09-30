<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BackpackUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        $response = array('success' => false);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
        } else {
            $user = BackpackUser::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ])->assignRole("user");

            $role = $user->roles;
            $role = $role[0];

            $response["success"] = true;
            $response['response'] = $user;
            $response["role"] = $role->name;
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
        ];

        $response = array('success' => false);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
        } else {
            $user = BackpackUser::find($id);

            $user->fill($request->all())->save();

            $response["success"] = true;
            $response['response'] = $user;
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


    public function change_password(Request $request, $id)
    {

        $response = array('success' => false);
        $user = BackpackUser::find($id)->first();

        if ($user) {
            if (Hash::check($request->old_password, $user->password)) {
                $rules = [
                    'password' => ['required', 'string', 'min:8', 'confirmed']
                ];
                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    $errorString = implode("\n", $validator->messages()->all());
                    $response['response'] =  $errorString;
                } else {
                    $user->password = Hash::make($request->password);
                    $user->save();
                    $response["success"] = true;
                }
            } else {
                $response["response"] = "Old password is incorrect.";
            }
        } else {
            $response["response"] = "These credentials do not match our records.";
        }

        return response()->json($response, 200);
    }

}
