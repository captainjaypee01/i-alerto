<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Resident extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'residents';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $with = ["relatives"];
    protected $appends = ["name"];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(){
        $full_name = $this->last_name . ', '. $this->first_name . ' ' . ( ($this->middle_name == '' || $this->middle_name == null ) ? '' : strtoupper($this->middle_name[0]) . '. ');
        return ucwords($full_name);
    }

    public function getNameAttribute()
    {
        $full_name = $this->first_name . ' ' . ( ($this->middle_name == '' || $this->middle_name == null ) ? ' ' : strtoupper($this->middle_name[0])) .
        ' '.$this->last_name;
        return ucwords($full_name);
    }


    public function getAssignedBarangayAttribute(){

        return $this->assign ? $this->assign->name : 'N/A';
    }

    public function getPwdStatusAttribute(){
        return $this->pwd == 1 ? 'Yes' : 'No';
    }

    public function getSeniorCitizenStatusAttribute(){
        return $this->senior_citizen == 1 ? 'Yes' : 'No';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user(){
        return $this->belongsTo(BackpackUser::class);
    }

    public function relatives(){
        return $this->hasMany(Relative::class);
    }

    public function assign(){
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
