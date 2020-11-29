<?php

namespace App\Models;

use App\User;
// use Backpack\CRUD\app\Models\Traits\Parental\HasParent;
use Backpack\CRUD\app\Notifications\ResetPasswordNotification as ResetPasswordNotification;
use Illuminate\Notifications\Notifiable;

class BackpackUser extends User
{
    use \Parental\HasParent;
    use Notifiable;

    protected $table = 'users';
    protected $with = ["resident","roles"];
    protected $appends = ["name","role"];

    /**
     * Send the password reset notification.
     *
     * @param string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    public function getAssignedBarangayAttribute(){
        if($this->employee)
            return $this->employee->assign->name;

        if($this->official)
            return $this->official->assign->name;

        if($this->resident)
            return $this->resident->assign->name;

        if($this->assign)
            return $this->assign->name;

        return 'N/A';

    }

    public function getRemoveEvacuationUserAttribute(){

        $btnHtml = '<a href="javascript:void(0)" class="btn btn-warning btn-sm btn-respond text-center" onclick="respondEntry(this)" data-route="'. route('admin.evacuation.user.remove', $this->id) .'" data-button-type="respond">Remove</a>';

        return $btnHtml . '<br>' ;//.  $script;
    }

    public function getFullNameAttribute(){
        $full_name = $this->last_name . ', '. $this->first_name . ' ' . ( ($this->middle_name == '' || $this->middle_name == null ) ? '' : strtoupper($this->middle_name[0]) . '. ');
        return ucwords($full_name);
    }

    public function getRoleAttribute()
    {
        $role = $this->roles;
        $role = $role[0];
        $role_name = $role->name;
        return $role_name;
    }


    public function assign(){
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }

    public function employee(){
        return $this->hasOne(Employee::class);
    }

    public function resident(){
        return $this->hasOne(Resident::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

}
