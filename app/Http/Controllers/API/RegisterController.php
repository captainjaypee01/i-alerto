<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerification;
use App\Models\BackpackUser;
use App\Models\Barangay;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $relative = [];
        $data = [];
        $data["first_name"] = "Bryan";
        $data["middle_name"] = "middlename";
        $data["last_name"] = "lastname";
        $data["birthdate"] = "906912000000";
        $relative[] = $data;

        $data["first_name"] = "josh";
        $data["middle_name"] = "middlename2";
        $data["last_name"] = "lastname2";
        $data["birthdate"] = "906912000000";
        $relative[] = $data;

        return response()->json($relative, 200);
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'contact_number' => ['required', 'string','digits:11', 'unique:users'],
        ];

        $response = array('success' => false);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
        } else {
            if ($request->has("left_index_fingerprint") && $request->has("right_index_fingerprint") && $request->has('selfie_image')) {
                $name_edited = strtolower($request->first_name."".$request->middle_name."".$request->last_name);
                $folder = str_replace(' ', '', strtolower($name_edited))."-".$request->birthdate;
                $left_index_fingerprint_image = "left_index_finger.jpg";
                $uploaded1 = $request->left_index_fingerprint->move(public_path("/fingerprint/$folder"), $left_index_fingerprint_image);

                $right_index_fingerprint_image = "right_index_finger.jpg";
                $uploaded2 = $request->right_index_fingerprint->move(public_path("/fingerprint/$folder"), $right_index_fingerprint_image);

                $selfi_image = "selfie_image.jpg";
                $uploaded3 = $request->selfie_image->move(public_path("/fingerprint/$folder"), $selfi_image);
                if($uploaded1 && $uploaded2 && $uploaded3){
                    $verification_code = Str::random(10);
                    $user = $this->store_user($request,$verification_code,$folder);
                    $resident = $this->store_resident($user,$request,$folder);

                    $role = $user->roles;
                    $role = $role[0];
                    
                    unset($user['roles']);

                    $res = [
                        "user" => $user,
                        "resident" => $resident
                    ];
                    $declarations = json_decode($request->declarations);
                    if(!empty($relatives) || $declarations != null){
                        $relative = $this->store_relative($resident,$request,$declarations);
                        $res["relative"] = $relative;
                    }
                    

                    Mail::to($user->email)->send(new EmailVerification($user));

                    $response["success"] = true;
                    $response['response'] = $res;
                    $response["role"] = $role->name;
                }
            }
            else{
                $response["response"] = "No Fingerprint or Selfie Image.";
            }
        }
        return response()->json($response, 200);
    }

    public function store_user($request,$verification_code,$folder)
    {
        $user = BackpackUser::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'birthdate' => Carbon::createFromTimestampMs($request->birthdate)->format('Y-m-d'),
            'province' => $request->province,
            'city' => $request->city,
            'barangay' => $request->barangay,
            'detailed_address' => $request->detailed_address,
            'health_concern' => $request->health_concern,
            'pwd' => $request->pwd,
            'senior_citizen' => $request->senior,
            'fingerprint' => $folder,
            'password' => Hash::make($request->password),
            'verification_code' => $verification_code
        ])->assignRole("resident");
        return $user;
    }

    public function store_resident($user,$request,$folder)
    {
        $barangay = Barangay::where("name",$request->barangay)->first();
        $resident = $user->resident()->create([
            'barangay_id' => $barangay->id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'province' => $request->province,
            'city' => $request->city,
            'barangay' => $request->barangay,
            'detailed_address' => $request->detailed_address,
            'birthdate' => Carbon::createFromTimestampMs($request->birthdate)->format('Y-m-d'),
            'health_concern' => $request->health_concern,
            'pwd' => $request->pwd,
            'senior_citizen' => $request->senior,
            'fingerprint' => $folder,
        ]);
        return $resident;
    }

    public function store_relative($resident,$request,$declarations)
    {   
        $barangay = Barangay::where("name",$request->barangay)->first();
        $relatives = [];
        foreach($declarations as $declaration)
        {
            $details = [];
            $details["user_id"] = $resident->user_id;
            $details["resident_id"] = $resident->id;
            $details["barangay_id"] = $barangay->id;
            $details["first_name"] = $declaration->firstname;
            $details["middle_name"] = $declaration->middlename;
            $details["last_name"] = $declaration->lastname;
            $details["relationship"] = $declaration->relationship;
            $details["birthdate"] = Carbon::createFromTimestampMs($declaration->birthdate)->format('Y-m-d');
            $details["province"] = $request->province;
            $details["city"] = $request->city;
            $details["barangay"] = $request->barangay;
            $details["detailed_address"] = $request->detailed_address;
            $relatives[] = $details;
        }

        $barangay = Barangay::where("name",$request->barangay)->first();
        $relative = $resident->relatives()->createMany($relatives);
        return $relative;
    }

    public function check_first(Request $request)
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'contact_number' => ['required', 'string','digits:11', 'unique:users'],
            'pwd' => ['boolean'],
            'senior' => ['boolean'],
            'birthdate' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        $response = array('success' => false);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
        } else {
            $response["success"] = true;
            $response['response'] = $request->all();
            // $response["role"] = $role->name;
        }
        return response()->json($response, 200);
    }

    public function verify_address(Request $request)
    {
        $rules = [
            'province' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:255'],
            'detailed_address' => ['required', 'string', 'max:255'],
        ];

        $response = array('success' => false);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
        } else {
            $response["success"] = true;
            $response['response'] = $request->all();
            // $response["role"] = $role->name;
        }
        return response()->json($response, 200);
    }

    public function resend_code(Request $request)
    {
        $rules = [
            'email' => ['required', 'string', 'max:255','email'],
        ];

        $response = array('success' => false);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
        } else {
            $response['success'] = true;
            $user = BackpackUser::where("email",$request->email)->first();
            if($user){
                Mail::to($user->email)->send(new EmailVerification($user,"resend"));
            }
            
        }

        return response()->json($response, 200);
    }


    public function verify_account(Request $request)
    {
        $rules = [
            'email' => ['required', 'string', 'email', 'max:255'],
            'verification_code' => ['required', 'string',Rule::exists('users')->where(function($query) use ($request){
                $query->where("email",$request->email);
            })],
        ];

        $response = array('success'=>false);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
        }else{
            $user = BackpackUser::with('roles')->where('email',$request->email)->first();
            if (is_null($user->email_verified_at) && strcmp($user->verification_code, $request->verification_code) === 0) {
                $user->email_verified_at = now();
                $user->save();
                $role = $user->roles;
                $role = $role[0];
                $response["success"] = true;
                $response["role"] = $role->name;
                $response['response'] = $user;
            }
            else if(is_null($user->email_verified_at) && strcmp($user->verification_code, $request->verification_code) !== 0){
                $error = [];
                $error["verification_code"] = ["The selected verification code is invalid."];
                $response['response'] = $error;
            }
            else{
                $error = [];
                $error["error"] = "Your account is already verified.";
                $response['response'] = $error;
            }
            
        }
        return response()->json($response, 200);
    }


    // public function receive_declaration(Request $request)
    // {
    //     $declaration = json_decode($request->declarations);
    //     return response()->json(["success"=>true,"response" => $declaration], 200);
    // }

    public function barangay()
    {
        $barangays = Barangay::all();
        $data = [
            'success' => true,
            'barangays' => $barangays
        ];
        return response()->json($data, 200);
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
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
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
