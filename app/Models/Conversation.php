<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'conversations';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['user_id','alert_id','message'];
    // protected $hidden = [];
    protected $dates = ['created_at', 'updated_at'];
    protected $appends = ['name','role','date'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user(){
        return $this->belongsTo(BackpackUser::class);
    }

    public function alert()
    {
        return $this->belongsTo(Alert::class);
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

    public function getMobileCreatedAtAttribute()
    {
        return $this->created_at->format('M d,Y h:i A');
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | APPENDS
    |--------------------------------------------------------------------------
    */

    public function getNameAttribute()
    {
        return $this->user->getNameAttribute();
    }

    public function getRoleAttribute()
    {
        return $this->user->roles[0]->name;
    }

    public function getDateAttribute()
    {
        return $this->getMobileCreatedAtAttribute();
    }
}
