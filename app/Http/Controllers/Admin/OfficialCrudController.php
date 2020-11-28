<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OfficialRequest;
use App\Models\BackpackUser;
use App\Models\Official;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Class OfficialCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class OfficialCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
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
        CRUD::setModel(\App\Models\Official::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/official');
        CRUD::setEntityNameStrings('official', 'officials');
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

        CRUD::setColumns([
            [
                'name' => 'full_name',
                'label' => "Full Name",
                'type' => 'text',
            ],
            [
                'name' => 'email',
                'label' => "Email",
                'type' => 'text',
            ],
            [
                'name' => 'contact_number',
                'label' => "Contact Number",
                'type' => 'text',
            ],
            [
                'name' => 'assigned_barangay',
                'label' => "Assigned Barangay",
                'type' => 'text',
            ],
        ]);

    }

    protected function setupShowOperation(){
        $this->crud->setColumns([
            [
                'name' => 'first_name',
                'label' => "First Name",
                'type' => 'text',
            ],
            [
                'name' => 'middle_name',
                'label' => "Middle Name",
                'type' => 'text',
            ],
            [
                'name' => 'last_name',
                'label' => "Last Name",
                'type' => 'text',
            ],
            [
                'name' => 'email',
                'label' => "Email",
                'type' => 'text',
            ],
            [
                'name' => 'contact_number',
                'label' => "Contact Number",
                'type' => 'text',
            ],
            [
                'name' => 'assigned_barangay',
                'label' => "Assigned Barangay",
                'type' => 'text',
            ],
            [
                'name' => 'province',
                'label' => "Province",
                'type' => 'text',
            ],
            [
                'name' => 'city',
                'label' => "City",
                'type' => 'text',
            ],
            [
                'name' => 'barangay',
                'label' => "Barangay",
                'type' => 'text',
            ],
            [
                'name' => 'detailed_address',
                'label' => "Detailed Address",
                'type' => 'text',
            ],
            [
                'name' => 'user_id',
                'label' => "User",
                'type' => 'text',
                'visibleInTable' => false,
                'visibleInShow' => false,
            ],
            [
                'name' => 'barangay_id',
                'label' => "Assigned Barangay",
                'type' => 'text',
                'visibleInTable' => false,
                'visibleInShow' => false,
            ],
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
        CRUD::setValidation(OfficialRequest::class);

        // CRUD::setFromDb(); // fields

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
        CRUD::addField(
            [
                'name' => 'first_name',
                'label' => "First Name",
                'type' => 'text',
        ]);

        CRUD::addField([
                'name' => 'middle_name',
                'label' => "Middle Name",
                'type' => 'text',
        ]);
        CRUD::addField([
                'name' => 'last_name',
                'label' => "Last Name",
                'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'email',
            'label' => "Email",
            'type' => 'email',
        ]);
        CRUD::addField([
            'name' => 'contact_number',
            'label' => "Contact Number",
            'type' => 'text',
        ]);
        CRUD::addField([
            'name'  => 'birthdate',
            'type'  => 'date_picker',
            'label' => 'Birthday',

            // optional:
            'date_picker_options' => [
               'todayBtn' => 'linked',
               'format'   => 'dd-mm-yyyy',
               'language' => 'en'
            ],
        ]);

        CRUD::addField([
            'name' => 'province',
            'label' => "Province",
            'type' => 'text',
        ]);
        CRUD::addField([
            'name' => 'city',
            'label' => "City",
            'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'barangay',
            'label' => "Barangay",
            'type' => 'text',
        ]);
        CRUD::addField([
            'name' => 'detailed_address',
            'label' => "Detailed Address",
            'type' => 'text',
        ]);

        CRUD::addField([
            'label' => "Assigned Barangay",
            'type' => 'select2',
            'name' => 'barangay_id',
            'entity' => 'barangay',
            'attribute' => 'name',
            'model' => "App\Models\Barangay",
            'options'   => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }),
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

    public function store()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->unsetValidation(); // validation has already been run
        $barangayId = $this->crud->getRequest()->barangay_id;
        $result = $this->traitStore();
        if(($result)){
            $user = BackpackUser::create([
                'barangay_id' => intval($barangayId),
                'first_name' => $this->crud->getCurrentEntry()->first_name,
                'middle_name' => $this->crud->getCurrentEntry()->middle_name,
                'last_name' => $this->crud->getCurrentEntry()->last_name,
                'email' => $this->crud->getCurrentEntry()->email,
                'contact_number' => $this->crud->getCurrentEntry()->contact_number,
                'province' => $this->crud->getCurrentEntry()->province,
                'city' => $this->crud->getCurrentEntry()->city,
                'barangay' => $this->crud->getCurrentEntry()->barangay,
                'detailed_address' => $this->crud->getCurrentEntry()->detailed_address,
                'birthdate' => $this->crud->getCurrentEntry()->birthdate,
                'password' => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
            ]);//->assignRole('member');
            $user->assignRole('official');
            Official::find($this->crud->getCurrentEntry()->id)->update(['user_id' => $user->id]);
        }

        return $result;
    }
}
