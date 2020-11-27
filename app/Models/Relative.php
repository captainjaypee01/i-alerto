<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Relative extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'relatives';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $appends = ["name"];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function getFullNameAttribute(){
        $full_name = $this->first_name . ' ' . ( ($this->middle_name == '' || $this->middle_name == null ) ? '' : strtoupper($this->middle_name[0])) .
        $this->last_name;
        return ucwords($full_name);
    }

    public function getNameAttribute()
    {
        $full_name = $this->first_name . ' ' . ( ($this->middle_name == '' || $this->middle_name == null ) ? '' : strtoupper($this->middle_name[0])) .
        ' '.$this->last_name;
        return ucwords($full_name);
    }

    public function getAssignedBarangayAttribute(){

        return $this->assign->name;
    }

    

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user(){
        return $this->belongsTo(BackpackUser::class);
    }

    public function resident(){
        return $this->belongsTo(Resident::class,"resident_id");
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
