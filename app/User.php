<?php

namespace App;

use App\Models\Conversation;
use App\Models\Official;
use Backpack\CRUD\app\Models\Traits\CrudTrait; // <------------------------------- this one
use Spatie\Permission\Traits\HasRoles;// <---------------------- and this one
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    use CrudTrait; // <----- this
    use HasRoles; // <------ and this
    use \Parental\HasChildren;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'evacuation_id', 'first_name', 'middle_name','last_name','email','contact_number','province','city','barangay','detailed_address','health_concern','pwd','senior_citizen','fingerprint','password','birthdate','verification_code'
    ];


    protected $dates = ['birthdate'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthdate' => 'date'
    ];


    public function official(){
        return $this->hasOne(Official::class);
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


    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getNameAttribute()
    {
        $name = $this->first_name." ".$this->middle_name." ".$this->last_name;
        $name = str_replace(" "," ",$name);
        $name = trim($name);
        return $name;
    }


}
