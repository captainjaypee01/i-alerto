<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
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
            'data' => Announcement::orderBy('created_at', 'desc')->take(15)->get(),
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
        }
        return response()->json($response, 200);
    }
}
