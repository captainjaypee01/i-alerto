<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EmployeeRequest;
use App\Models\BackpackUser;
use App\Models\Employee;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Hash;

/**
 * Class EmployeeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EmployeeCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        $this->crud->setModel('App\Models\Employee');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/employee');
        $this->crud->setEntityNameStrings('employee', 'employees');
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        // $this->crud->setFromDb();
        $this->crud->addColumn([
            'name' => 'full_name',
            'label' => "Full Name",
            'type' => 'text',
        ]);
        $this->crud->addColumn([
            'name' => 'email',
            'label' => "Email",
            'type' => 'text',
        ]);
        $this->crud->addColumn([
            'name' => 'contact_number',
            'label' => "Contact Number",
            'type' => 'text',
        ]);
        $this->crud->addColumn([
            'name' => 'assigned_barangay',
            'label' => "Assigned Barangay",
            'type' => 'text',
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

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(EmployeeRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        // $this->crud->setFromDb();
        // $this->addFields();
        $this->crud->addField(
            [
                'name' => 'first_name',
                'label' => "First Name",
                'type' => 'text',
        ]);

        $this->crud->addField([
                'name' => 'middle_name',
                'label' => "Middle Name",
                'type' => 'text',
        ]);
        $this->crud->addField([
                'name' => 'last_name',
                'label' => "Last Name",
                'type' => 'text',
        ]);

        $this->crud->addField([
            'name' => 'email',
            'label' => "Email",
            'type' => 'email',
        ]);
        $this->crud->addField([
            'name' => 'contact_number',
            'label' => "Contact Number",
            'type' => 'text',
        ]);
        $this->crud->addField([
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

        $this->crud->addField([
            'name' => 'province',
            'label' => "Province",
            'type' => 'text',
        ]);
        $this->crud->addField([
            'name' => 'city',
            'label' => "City",
            'type' => 'text',
        ]);

        $this->crud->addField([
            'name' => 'barangay',
            'label' => "Barangay",
            'type' => 'text',
        ]);
        $this->crud->addField([
            'name' => 'detailed_address',
            'label' => "Detailed Address",
            'type' => 'text',
        ]);
        $this->crud->addField([
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
            ]);//->assignRole('member');
            $user->assignRole('employee');
            Employee::find($this->crud->getCurrentEntry()->id)->update(['user_id' => $user->id]);
        }

        return $result;
    }
}
