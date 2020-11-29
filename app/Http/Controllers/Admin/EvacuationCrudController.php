<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EvacuationRequest;
use App\Models\BackpackUser;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Log;

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
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation {show as traitShow; }

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
        CRUD::setShowView('custom.evacuation.show');
        if(backpack_user()->hasAnyRole('resident')){
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
        CRUD::addColumn([
            'name' => 'capacity',
            'type' => 'model_function',
            'function_name' => 'getCapacityCountAttribute',
            'limit' => 1000]);
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
        CRUD::addColumn([
            'name' => 'capacity',
            'type' => 'model_function',
            'function_name' => 'getCapacityCountAttribute',
            'limit' => 1000]);
        CRUD::addField(['name' => 'address', 'type' => 'text']);
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
        CRUD::addField(['name' => 'address', 'type' => 'text']);
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
            'name'        => 'is_avail',
            'label'       => "Is Available?",
            'type'        => 'select2_from_array',
            'options'     => [1 => 'Yes', 0 => 'No'],
            'allows_null' => false,
            'default'     => 1,
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

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $this->crud->hasAccessOrFail('show');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        $setFromDb = $this->crud->get('show.setFromDb');

        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.preview').' '.$this->crud->entity_name;
        $users = BackpackUser::whereNull('evacuation_id')->orderBy('last_name', 'asc')->get();
        $evacuationUsers = $this->crud->getCurrentEntry()->users;
        $this->data['users'] = $users;
        $this->data['evacuationUsers'] = $evacuationUsers;

        // set columns from db
        if ($setFromDb) {
            $this->crud->setFromDb();
        }

        // cycle through columns
        foreach ($this->crud->columns() as $key => $column) {

            // remove any autoset relationship columns
            if (array_key_exists('model', $column) && array_key_exists('autoset', $column) && $column['autoset']) {
                $this->crud->removeColumn($column['key']);
            }

            // remove any autoset table columns
            if ($column['type'] == 'table' && array_key_exists('autoset', $column) && $column['autoset']) {
                $this->crud->removeColumn($column['key']);
            }

            // remove the row_number column, since it doesn't make sense in this context
            if ($column['type'] == 'row_number') {
                $this->crud->removeColumn($column['key']);
            }

            // remove columns that have visibleInShow set as false
            if (isset($column['visibleInShow']) && $column['visibleInShow'] == false) {
                $this->crud->removeColumn($column['key']);
            }

            // remove the character limit on columns that take it into account
            if (in_array($column['type'], ['text', 'email', 'model_function', 'model_function_attribute', 'phone', 'row_number', 'select'])) {
                $this->crud->modifyColumn($column['key'], ['limit' => ($column['limit'] ?? 999)]);
            }
        }

        // remove preview button from stack:line
        $this->crud->removeButton('show');

        // remove bulk actions colums
        $this->crud->removeColumns(['blank_first_column', 'bulk_actions']);

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getShowView(), $this->data);
    }
}
