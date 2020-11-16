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
            $btnHtml = '<a href="javascript:void(0)" class="btn btn-warning btn-sm btn-respond text-center" onclick="respondEntry(this)" data-status="'. $this->status .'" data-route="'. backpack_url('alert/' . $this->id) .'" data-button-type="respond">'. $this->status_message . '</a>';
        else
            $btnHtml = '<a href="javascript:void(0)" class="btn btn-success btn-sm btn-respond text-center" onclick="respondEntry(this)" data-status="'. $this->status .'" data-route="'. backpack_url('alert/' . $this->id) .'" data-button-type="respond">'. $this->status_message .'</a>';


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
                                    console.log(result);
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

        return $btnHtml . '<br>' .  $script;

    }

    public function showMap(){
        $btnHtml = '<a href="javascript:void(0)" class="btn btn-info btn-sm btn-map text-center" data-toggle="modal" data-target="#mapModal" onclick="mapEntry(this)" data-lat="'. $this->latitude .'" data-lng="'. $this->longitude .'" data-route="'. backpack_url('alert/' . $this->id) .'" data-button-type="map">Show Map</a>';

        $script = '
            <script>

            </script>';
        $script .=  '<script>
            if (typeof mapEntry != \'function\') {
                $("[data-button-type=map]").unbind(\'click\');

                function mapEntry(button) {
                    // ask for confirmation before deleting an item
                    // e.preventDefault();
                    var button = $(button);
                    var route = button.attr(\'data-route\');
                    const lat = button.attr(\'data-lat\');
                    const lng = button.attr(\'data-lng\');
                    var row = $("#crudTable a[data-route=\'"+route+"\']").closest(\'tr\');

                    mapboxgl.accessToken = "pk.eyJ1IjoiYnJ5YW5iZXJuYXJkbzI4IiwiYSI6ImNrMTJnODZoajAxN3Izb202YzdnbXdiM2kifQ.rBaatfV0jYq0tQIB-qHwmA";

                    var map = new mapboxgl.Map({
                        container: "map",
                        style: "mapbox://styles/mapbox/streets-v11",
                        center: [lng, lat], // starting position [lng, lat]
                        zoom: 9 // starting zoom
                    });
                    var marker = new mapboxgl.Marker()
                            .setLngLat([lng, lat])
                            .addTo(map);


                }
            }


        </script>';
        return $btnHtml . $script;
    }

    public function getDisasterTypeAttribute(){
        if($this->type == 'accident'){
            return '<span class="text-capitalize">'. $this->accident_type . ' ' . $this->type .'</span>';
        }

        return '<span class="text-capitalize">'. $this->type .'</span>';
    }

    public function getCreatedDateFormatAttribute(){
        return $this->created_at->format('F, d Y');
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

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
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

    public function getNameAttribute()
    {
        $name = $this->user->first_name." ".$this->user->middle_name." ".$this->user->last_name;
        $name = str_replace(" "," ",$name);
        $name = trim($name);
        return $name;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
