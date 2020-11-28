<?php

namespace App\Http\Controllers\Admin\Custom;

use App\Http\Controllers\Controller;
use App\Models\BackpackUser;
use App\Models\Evacuation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Prologue\Alerts\Facades\Alert;

class CustomEvacuationController extends Controller
{
    public function addUser(Evacuation $evacuation, Request $request){
        $users = BackpackUser::whereIn('id', $request->users)->update(['evacuation_id' => $evacuation->id]);//->get();

        Alert::success('Successfully Added')->flash();
        return redirect()->back();
    }

    public function removeUser(BackpackUser $user){
        Log::info(request());
        Log::info($user);
        $user = $user->update(['evacuation_id' => 0]);
        return response()->json($user);
    }
}
