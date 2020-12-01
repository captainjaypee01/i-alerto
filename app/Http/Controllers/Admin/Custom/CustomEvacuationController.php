<?php

namespace App\Http\Controllers\Admin\Custom;

use App\Http\Controllers\Controller;
use App\Models\BackpackUser;
use App\Models\Barangay;
use App\Models\Evacuation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Prologue\Alerts\Facades\Alert;

class CustomEvacuationController extends Controller
{
    public function index(Request $request){

        $form = collect($request->input('form'))->pluck('value', 'name');
        // Log::info($request);
        $barangayArray = [];
        foreach($request->form as $v){
            if($v['name'] == 'barangays[]')
                array_push($barangayArray, $v['value']);
        }

        // if no category has been selected, show no options
        if (! $form['barangays[]']) {
            return [];
        }
        $evacuations = Evacuation::where('is_avail', 1)->whereHas('barangays', function ($query) use ($barangayArray){
            $query->whereIn('barangay_id', $barangayArray);
        });

        return $evacuations->paginate(10);

    }
    public function addUser(Evacuation $evacuation, Request $request){
        request()->validate([
            'users' => 'required'
        ]);
        $users = BackpackUser::whereIn('id', $request->users)->update(['evacuation_id' => $evacuation->id]);//->get();

        Alert::success('Successfully Added')->flash();
        return redirect()->back();
    }

    public function removeUser(BackpackUser $user){
        Log::info(request());
        // Log::info($user);
        // $user = $user->update(['evacuation_id' => 0]);
        $user->evacuation_id = null;
        $res = $user->save();
        Log::info($user);
        return response()->json($res);
    }

    public function userList(Evacuation $evacuation){
        Log::info($evacuation->users);
        $users = $evacuation->users;
        foreach($users as $user){
            $user->full_name = $user->full_name;
            $user->assigned_barangay = $user->assigned_barangay;
            $user->remove_evacuation_user = $user->remove_evacuation_user;
        }
        return response()->json( ['data' => $users] );
        // return $evacuation->users();
    }
}
