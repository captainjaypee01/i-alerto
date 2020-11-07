<?php

namespace App\Http\Controllers\Admin\Custom;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\Request;

class CustomAlertController extends Controller
{
    public function response(Request $request, Alert $alert){
        $response = $alert->update(['status' => ($alert->status == 1 ? 0 : 1)]);
        return response()->json($response);
    }
}
