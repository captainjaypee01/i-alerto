<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Evacuation extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'evacuations';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    protected $dates = ['created_at', 'updated_at'];
    protected $with = ['barangays'];
    protected $appends = ['date'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */


    public function getAvailableStatusAttribute(){
        Log::info($this->is_avail);
        $colorBadge = $this->is_avail == 1 ? 'success' : 'danger';
        $text = $this->is_avail == 1 ? 'Available' : 'Not Available';
        return '<span class="badge badge-'. $colorBadge . '">' . $text  . '</span>';
    }

    public function getCapacityCountAttribute(){
        $status = 'success';
        $userCount = count($this->users);
        $unregisteredCount = EvacuationUser::where('evacuation_id', $this->id)->count();
        $totalCount = $userCount + $unregisteredCount;
        if($totalCount >= $this->capacity){
            $status = 'danger';
        }
        $html = '<span class="badge badge-' . $status . '">' . $totalCount . ' / ' . $this->capacity . '</span>';
        return $html;
    }

    public function getMobileCreatedAtAttribute()
    {
        return $this->created_at->format('M d,Y h:i A');
    }

    public function getDateAttribute()
    {
        return $this->getMobileCreatedAtAttribute();
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function barangays()
    {
        return $this->belongsToMany(Barangay::class, 'barangay_evacuation');
    }

    public function users(){
        return $this->hasMany(BackpackUser::class);
    }

    public function unregisterusers(){
        return $this->belongsToMany(BackpackUser::class, 'evacuation_user', 'evacuation_id')->withPivot(['first_name', 'middle_name', 'last_name']);
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
