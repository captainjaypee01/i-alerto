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

    public function barangay(){
        return $this->hasOne(Barangay::class);
    }
    
    public function employee(){
        return $this->hasOne(Employee::class);
    }
    
    public function resident(){
        return $this->hasOne(Resident::class);
    }
    
}
