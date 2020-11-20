<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EvacuationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class EvacuationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EvacuationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Evacuation::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/evacuation');
        CRUD::setEntityNameStrings('evacuation', 'evacuations');
        if(backpack_user()->hasAnyRole('employee|resident')){
            $this->crud->denyAccess(['create', 'update', 'delete']);
        }
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // CRUD::setFromDb(); // columns

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
        CRUD::addColumn(['name' => 'name', 'type' => 'text']);
        CRUD::addColumn(['name' => 'capacity', 'type' => 'text']);
        CRUD::addColumn(['name' => 'barangays', 'type' => 'relationship']);
        CRUD::addColumn([
            'name' => 'is_avail',
            'type' => 'model_function',
            'label' => 'Availability',
            'function_name' => 'getAvailableStatusAttribute',
            'limit' => 1000,
        ]);
    }

    protected function setupShowOperation()
    {
        // CRUD::setFromDb(); // columns

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
        CRUD::addColumn(['name' => 'name', 'type' => 'text']);
        CRUD::addColumn(['name' => 'capacity', 'type' => 'text']);
        CRUD::addColumn(['name' => 'barangays', 'type' => 'relationship']);
        CRUD::addColumn([
            'name' => 'is_avail',
            'type' => 'model_function',
            'label' => 'Availability',
            'function_name' => 'getAvailableStatusAttribute',
        ]);
        CRUD::addColumn([
            'name' => 'status',
            'type' => 'text',
            'visibleInTable' => false,
            'visibleInModal' => true,
            'visibleInExport' => false,
            'visibleInShow' => false,
        ]);
    }


    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(EvacuationRequest::class);

        // CRUD::setFromDb(); // fields

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
        CRUD::addField(['name' => 'name', 'type' => 'text']);
        CRUD::addField(['name' => 'capacity', 'type' => 'number']);
        CRUD::addField([
            'label' => "Barangay",
            'type' => 'select2_multiple',
            'name' => 'barangays',
            'entity' => 'barangays',
            'attribute' => 'name',
            'model' => "App\Models\Barangay",
            'pivot' => true,
            'select_all' => true,
            'options'   => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }),
        ]);
        CRUD::addField([
            'name'        => 'is_available',
            'label'       => "Is Available?",
            'type'        => 'select2_from_array',
            'options'     => [0 => 'No', 1 => 'yes'],
            'allows_null' => false,
            'default'     => 0,
        ]);
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
