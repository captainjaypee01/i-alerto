<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'alerts';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    protected $dates = ['responded_at','created_at', 'updated_at'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function getFullNameWithLinkAttribute()
    {
        return '<a href="'. route('user.edit', $this->user) .'" target="_blank">' . $this->user->name . '</a>';
    }

    public function getStatusMessageAttribute()
    {
        $status = $this->status;
        switch($status){
            case 0:
                return 'Not Responded';
            case 1:
                return 'Responded';
            default:
                return null;
        }
    }

    public function getRespondStatusWithButton(){
        $respond = $this->status;
        $btnHtml = '';
        if($respond == 0)
            $btnHtml = '<a href="javascript:void(0)" class="btn btn-success btn-sm btn-respond btn-link text-center" onclick="respondEntry(this)" data-status="'. $this->status .'" data-route="'. backpack_url('alert/' . $this->id) .'" data-button-type="respond"><span class="fa fa-reply"></span></a>';
        else 
            $btnHtml = '<a href="javascript:void(0)" class="btn btn-warning btn-sm btn-respond btn-link text-center" onclick="respondEntry(this)" data-status="'. $this->status .'" data-route="'. backpack_url('alert/' . $this->id) .'" data-button-type="respond"><span class="fa fa-times"></span></a>';
        

        $script = '
        <script>
            if (typeof respondEntry != \'function\') {
                $("[data-button-type=respond]").unbind(\'click\');
        
                function respondEntry(button) {
                // ask for confirmation before deleting an item
                // e.preventDefault();
                var button = $(button);
                var route = button.attr(\'data-route\');
                var status = button.attr(\'data-status\') == 1 ? "Not Responded" : "Responded";
                var row = $("#crudTable a[data-route=\'"+route+"\']").closest(\'tr\');
                
                swal({
                    title: "Warning",
                    text: "Are you sure you want to change the status to \"" + status + "\"?",
                    icon: "warning",
                    buttons: {
                            cancel: {
                            text: "Cancel",
                            value: null,
                            visible: true,
                            className: "bg-secondary",
                            closeModal: true,
                        },
                        success: {
                            text: "Yes",
                            value: true,
                            visible: true,
                            className: "bg-success",
                        }
                    },
                    }).then((value) => {
                        if (value) {
                            $.ajax({
                                url: route + "/response",
                                type: \'PATCH\',
                                success: function(result) {
                                    if (result != 1) {
                                        // Show an error alert
                                        swal({
                                            title: "NOT Updated",
                                            text: "There\'s been an error. Your item might not have been deleted.",
                                            icon: "error",
                                            timer: 2000,
                                            buttons: false,
                                        });
                                    } else {
                                        // Show a success message
                                        swal({
                                            title: "Item Updated",
                                            text: "The item has been updated successfully.",
                                            icon: "success",
                                            timer: 4000,
                                            buttons: false,
                                        });
            
                                        // Hide the modal, if any
                                        $(\'.modal\').modal(\'hide\');

                                        $("#crudTable").DataTable().ajax.reload();
            
                                    }
                                },
                                error: function(result) {
                                    // Show an alert with the result
                                    swal({
                                        title: "NOT updated",
                                        text: "There\'s been an error. Your item might not have been updated.",
                                        icon: "error",
                                        timer: 4000,
                                        buttons: false,
                                    });
                                }
                            });
                        }
                    });
        
                }
            }
        
  
        </script>';
        
        return $btnHtml . '<br>' . $this->status_message .  $script;

    }
    
    public function getDisasterTypeAttribute(){
        if($this->type == 'accident'){
            return '<span class="text-capitalize">'. $this->accident_type . ' ' . $this->type .'</span>';
        }
        
        return '<span class="text-capitalize">'. $this->type .'</span>';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(BackpackUser::class);
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

    public function getMobileRespondedAtAttribute()
    {
        return $this->responded_at != null ? $this->responded_at->format('M d,Y h:i A') : "N/A";
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
