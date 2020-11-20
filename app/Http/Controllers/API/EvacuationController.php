<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
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
        return response()->json([
            'data' => Evacuation::orderBy('created_at', 'desc')->take(15)->get(),
        ], 200);
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
