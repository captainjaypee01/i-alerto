<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BackpackUser;
use Carbon\Carbon;
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
            'name' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'contact_number' => ['required', 'string','digits:11', 'unique:users'],
        ];

        $response = array('success' => false);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
        } else {
            $folder = str_replace(' ', '', strtolower($request->name))."-".$request->date_of_birth;

            $left_index_fingerprint_image = "left_index_finger.jpg";
            $uploaded1 = $request->left_index_fingerprint->move(public_path("/fingerprint/$folder"), $left_index_fingerprint_image);

            $right_index_fingerprint_image = "right_index_finger.jpg";
            $uploaded2 = $request->right_index_fingerprint->move(public_path("/fingerprint/$folder"), $right_index_fingerprint_image);
        
            if($uploaded1 && $uploaded2){
                $user = BackpackUser::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'contact_number' => $request->contact_number,
                    'address' => $request->address,
                    'date_of_birth' => Carbon::createFromTimestampMs($request->date_of_birth)->format('Y-m-d'),
                    'health_concern' => $request->health_concern,
                    'pwd' => $request->pwd,
                    'senior_citizen' => $request->senior,
                    'fingerprint' => $folder,
                    'password' => Hash::make($request->password),
                ])->assignRole("user");

                $role = $user->roles;
                $role = $role[0];

                $response["success"] = true;
                $response['response'] = $user;
                $response["role"] = $role->name;
            }
        }
        return response()->json($response, 200);
    }

    public function check_first(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'contact_number' => ['required', 'string','digits:11', 'unique:users'],
            'address' => ['required', 'string','max:255'],
            'pwd' => ['boolean'],
            'senior' => ['boolean'],
            'date_of_birth' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        $response = array('success' => false);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
        } else {
            // $user = BackpackUser::create([
            //     'name' => $request->name,
            //     'email' => $request->email,
            //     'password' => Hash::make($request->password),
            // ])->assignRole("user");

            // $role = $user->roles;
            // $role = $role[0];

            $response["success"] = true;
            $response['response'] = $request->all();
            // $response["role"] = $role->name;
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
