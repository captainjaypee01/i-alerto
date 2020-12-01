<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class EvacuationUser extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'evacuation_user';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = ['created_at', 'updated_at'];
    // protected $with = ['barangays'];
    // protected $appends = ['date'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function getRemoveEvacuationUserAttribute(){

        $btnHtml = '<a href="javascript:void(0)" class="btn btn-warning btn-sm btn-respond text-center" onclick="respondEntry(this)" data-route="'. route('admin.evacuation.user.remove.unregister', $this->id) .'" data-button-type="respond">Remove</a>';

        return $btnHtml . '<br>' ;//.  $script;
    }

    public function getFullNameAttribute(){
        $full_name = $this->last_name . ', '. $this->first_name . ' ' . ( ($this->middle_name == '' || $this->middle_name == null ) ? '' : strtoupper($this->middle_name[0]) . '. ');
        return ucwords($full_name);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

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
