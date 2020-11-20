<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AlertRequest;
use App\Models\BackpackUser;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AlertCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AlertCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        $this->crud->setModel('App\Models\Alert');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/alert');
        $this->crud->setEntityNameStrings('alert', 'alerts');
        $this->crud->setShowView('custom.alert.show');
        $this->crud->setListView('custom.alert.list');

        $this->crud->orderBy('created_at','desc');
        if(backpack_user()->hasRole('resident') && backpack_user()->resident !== null){
            $this->crud->addClause("whereUserId", backpack_user()->id);
            $this->crud->denyAccess(['create', 'update', 'delete']);
        }

    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        // $this->crud->setFromDb();

        if(backpack_user()->hasRole('resident'))
            $this->crud->denyAccess(['update', 'delete']);

        $this->crud->setColumns([
            [
                'name' => "user_id",
                'label' => "User",
                'type' => "model_function",
                'function_name' => 'getFullNameWithLinkAttribute',
                'limit' => 100,
            ],
            [
                'name' => 'address',
                'label' => "Address",
                'type' => 'text'
            ],
            [
                'name' => 'type',
                'label' => "Type of Alert",
                'type' => 'model_function',
                'limit' => 150,
                'function_name' => 'getDisasterTypeAttribute',
            ],
            [
                'name' => 'longitude',
                'label' => "Longitude",
                'type' => 'text',
                'visibleInTable' => false,
                'visibleInModal' => true,
                'visibleInExport' => false,
                'visibleInShow' => true,
            ],
            [
                'name' => 'latitude',
                'label' => "Latitude",
                'type' => 'text',
                'visibleInTable' => false,
                'visibleInModal' => true,
                'visibleInExport' => false,
                'visibleInShow' => true,
            ],
            [
                'name' => "status",
                'label' => "Respond Status",
                'type' => "model_function",
                'function_name' => 'getRespondStatusWithButton',
                'limit' => 10000,
            ],
            [
                'name' => "map",
                'label' => "Show Map",
                'type' => "model_function",
                'function_name' => 'showMap',
                'limit' => 10000,
            ],
            [
                'name' => 'created_at',
                'label' => 'Date Alerted',
                'type' => 'datetime_picker',
                'format' => 'D MMM Y, hh:mm A',
            ],
        ]);

        $this->crud->orderBy('created_at', 'desc');


        if(backpack_user()->hasRole('administrator') || backpack_user()->hasRole('employee')) {
            $this->crud->addFilter([
                'name' => 'user_id',
                'type' => 'select2',
                'label' => 'Users',
            ], function(){
                return BackpackUser::all()->pluck('name', 'id')->toArray();
            }, function($value){
                $this->crud->addClause('where', 'user_id', $value);
            });
        }

        $this->crud->addFilter(
            [
                'type' => 'date_range',
                'name' => 'from_to',
                'label' => 'Date range',
            ],
            false,
            function ($value) {
                    $dates = json_decode($value);
                    $this->crud->addClause('where', 'created_at', '>=', $dates->from);
                    $this->crud->addClause('where', 'created_at', '<=', $dates->to . ' 23:59:59');
            }
        );

        $this->crud->addFilter([
            'name' => 'status',
            'type' => 'select2',
            'label' => 'Response Status',
        ], function(){
            return ['0' => 'Not Responded', '1' => 'Responded'];
        }, function($value){
            $this->crud->addClause('where', 'status', $value);
        });

        $this->crud->addFilter([
            'name' => 'type',
            'type' => 'select2',
            'label' => 'Alert Type',
        ], function (){
            return ['fire' => 'Fire','flood' => "Flood",'accident' => 'Accident','crime' => "Crime", 'others' => "Others"];
        }, function($value){
            $this->crud->addClause('where', 'type', $value);
        });
    }

    protected function setupShowOperation(){
        $this->crud->setColumns([
            [
                'name' => "user_id",
                'label' => "User",
                'type' => "model_function",
                'function_name' => 'getFullNameWithLinkAttribute',
                'limit' => 100,
                'visibleInTable' => false,
                'visibleInModal' => true,
                'visibleInExport' => false,
                'visibleInShow' => true,
            ],
            [
                'name' => 'address',
                'label' => "Address",
                'type' => 'text'
            ],
            [
                'name' => 'type',
                'label' => "Type of Alert",
                'type' => 'model_function',
                'limit' => 150,
                'function_name' => 'getDisasterTypeAttribute',
            ],
            [
                'name' => "status",
                'label' => "Respond Status",
                'type' => "model_function",
                'function_name' => 'getStatusMessageAttribute',
                'limit' => 10000,
                'visibleInTable' => false,
                'visibleInModal' => true,
                'visibleInExport' => false,
                'visibleInShow' => true,
            ],
            [
                'name' => 'responded_at',
                'label' => 'Time of Responded ',
                'type' => 'datetime_picker',
                'format' => 'D MMM Y, hh:mm A',
            ],
            [
                'name' => 'created_at',
                'label' => 'Date Alerted',
                'type' => 'datetime_picker',
                'format' => 'D MMM Y, hh:mm A',
            ],
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(AlertRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        // $this->crud->setFromDb();

        if (backpack_user()->hasAnyRole('administrator|official|employee')) {
            $this->crud->addField([
                'label' => "User",
                'type' => 'select2',
                'name' => 'user_id',
                'entity' => 'user',
                'attribute' => 'full_name',
                'model' => "App\Models\BackpackUser",
                'options'   => (function ($query) {
                    return $query->orderBy('last_name', 'ASC')->get();
                }),
            ]);
        }
        else{
            $this->crud->addField([
                'name' => 'user_id',
                'type' => 'hidden',
                'value' => backpack_user()->id,
            ]);
        }
        $this->crud->addField([
            'name' => 'type',
            'type' => 'select2_from_array',
            'label' => 'Alert Type',
            'options' => ['fire' => 'Fire','flood' => "Flood",'accident' => 'Accident','crime' => "Crime", 'others' => "Others"],
            'allows_null' => null,
            'default' => 'fire',
        ]);
        $this->crud->addField([
            'name' => 'accident_type',
            'type' => 'select2_from_array',
            'label' => 'Accident Type (optional)',
            'options' => ['car' => 'Car','road' => "Road",'fatal' => 'Fatal', 'others' => "Others"],
            'allows_null' => true,
            'default' => '',
        ]);
        $this->crud->addField([
            'name' => 'address',
            'type' => 'text',
            'label' => "Address",
        ]);

        $this->crud->addField([
            'name' => 'latitude',
            'type' => 'text',
            'label' => "Latitude",
        ]);

        $this->crud->addField([
            'name' => 'longitude',
            'type' => 'text',
            'label' => "Longitude",
        ]);

        $this->crud->addField([
            'name'        => 'status',
            'label'       => "Respond Status?",
            'type'        => 'select2_from_array',
            'options'     => [0 => 'Not Responded', 1 => 'Responded'],
            'allows_null' => false,
            'default'     => 0,
        ]);

        $this->crud->addField([
            'name' => 'responded_at',
            'label' => 'Time of Responded',
            'type' => 'datetime_picker',
                // optional:
            'datetime_picker_options' => [
                'format' => 'DD/MM/YYYY h:m A',
                'language' => 'en'
            ],
            'allows_null' => true,

        ]);

        $this->crud->addField([
            'name' => 'created_at',
            'label' => 'Date of Alert',
            'type' => 'datetime_picker',
                // optional:
            'datetime_picker_options' => [
                'format' => 'DD/MM/YYYY h:m A',
                'language' => 'en'
            ],
            'allows_null' => true,

        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
