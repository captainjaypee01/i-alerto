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
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        // $this->crud->setFromDb();
        
        if(backpack_user()->hasRole('user'))
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
                'label' => "Type of Disaster",
                'type' => 'text',
            ],
            [
                'name' => 'longitude',
                'label' => "Longitude",
                'type' => 'text',
                'visibleInTable' => true,
                'visibleInModal' => true,
                'visibleInExport' => false,
                'visibleInShow' => true,
            ],
            [
                'name' => 'latitude',
                'label' => "Latitude",
                'type' => 'text',
                'visibleInTable' => true,
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
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(AlertRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        $this->crud->setFromDb();
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
