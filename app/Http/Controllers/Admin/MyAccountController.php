<?php

namespace App\Http\Controllers\Admin;

use Alert;
use App\Http\Requests\AccountInfoRequest;
use App\Models\Employee;
use App\Models\Official;
use App\Models\Resident;
use Backpack\CRUD\app\Http\Requests\ChangePasswordRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MyAccountController extends Controller
{
    protected $data = [];

    public function __construct()
    {
        $this->middleware(backpack_middleware());
    }

    /**
     * Show the user a form to change his personal information & password.
     */
    public function getAccountInfoForm()
    {
        $this->data['title'] = trans('backpack::base.my_account');
        $this->data['user'] = $this->guard()->user();

        return view(backpack_view('my_account'), $this->data);
    }

    /**
     * Save the modified personal information for a user.
     */
    public function postAccountInfoForm(AccountInfoRequest $request)
    {
        Log::info($request);
        $result = $this->guard()->user()->update($request->except(['_token']));

        if ($result) {
            Alert::success(trans('backpack::base.account_updated'))->flash();
            if($this->guard()->user()->hasRole('employee') == true){
                $employee = Employee::where('user_id', $this->guard()->user()->id)->update($request->except(['_token']));
            }
            if($this->guard()->user()->hasRole('official') == true){
                $official = Official::where('user_id', $this->guard()->user()->id)->update($request->except(['_token']));
            }
            if($this->guard()->user()->hasRole('resident') == true){
                $resident = Resident::where('user_id', $this->guard()->user()->id)->update($request->except(['_token']));
            }
        } else {
            Alert::error(trans('backpack::base.error_saving'))->flash();
        }

        return redirect()->back();
    }

    /**
     * Save the new password for a user.
     */
    public function postChangePasswordForm(ChangePasswordRequest $request)
    {
        $user = $this->guard()->user();
        $user->password = Hash::make($request->new_password);

        if ($user->save()) {
            Alert::success(trans('backpack::base.account_updated'))->flash();
        } else {
            Alert::error(trans('backpack::base.error_saving'))->flash();
        }

        return redirect()->back();
    }

    /**
     * Get the guard to be used for account manipulation.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return backpack_auth();
    }
}
