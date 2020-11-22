<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Barangay;
use App\Models\Evacuation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EvacuationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $evacuation = Announcement::with('evacuations')->whereHas('evacuations')->get();
        // foreach( as )
        // {
        
        // }
        // return response()->json($evacuation, 200);
        // $barangay = Barangay::whereIn("name",["Bagong Ilog","Bambang"])->pluck("id");
        // echo json_encode($barangay);
        $json = [
            'data' => Evacuation::where("is_avail",1)->orderBy('created_at', 'desc')->take(15)->get()
        ];
        return response()->json($json, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->merge(['barangay' => json_decode($request->barangay)]);
        // $is_avail = $request->is_avail == "1" ? 1 : 0 ;
        // $request->merge(['is_avail' => $is_avail]);
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'numeric','min:1'],
            'address' => ['required', 'string', 'max:255'],
            'barangay' => ['required','array'],
            'is_avail' => ['required','boolean'],
        ];

        $response = array('success' => false);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
        } else {
            $evac = Evacuation::create([
                "name" => $request->name,
                "capacity" => $request->capacity,
                "address" => $request->address,
                "barangay" => $request->barangay,
                "is_avail" => $request->is_avail
            ]);
            $barangay = Barangay::whereIn("name",$request->barangay)->pluck("id");
            $evac->barangays()->attach($barangay);
            $response["success"] = true;
            $response["response"] = $request->all();
        }
        return response()->json($response, 200);
    }


    public function update_capacity(Request $request)
    {
        $rules = [
            'capacity' => ['required', 'numeric', 'min:1']
        ];

        $response = array('success' => false);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
        } else {
            $evacuation = Evacuation::find($request->id);
            $evacuation->capacity = $request->capacity;
            $evacuation->save();

            $response["success"] = true;
            $response['response'] = $evacuation;
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
