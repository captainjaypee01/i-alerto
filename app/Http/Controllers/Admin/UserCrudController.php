<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\UserStoreCrudRequest as StoreRequest;
use App\Http\Requests\UserUpdateCrudRequest as UpdateRequest;
use App\Models\Employee;
use App\Models\Official;
use App\Models\Resident;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup()
    {
        $this->crud->setModel(config('backpack.permissionmanager.models.user'));
        $this->crud->setEntityNameStrings(trans('backpack::permissionmanager.user'), trans('backpack::permissionmanager.users'));
        $this->crud->setRoute(backpack_url('user'));
    }

    public function setupListOperation()
    {
        $this->crud->addColumns([
            [
                'name'  => 'name',
                'label' => trans('backpack::permissionmanager.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],
            [ // n-n relationship (with pivot table)
                'label'     => trans('backpack::permissionmanager.roles'), // Table column heading
                'type'      => 'select_multiple',
                'name'      => 'roles', // the method that defines the relationship in your Model
                'entity'    => 'roles', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model'     => config('permission.models.role'), // foreign key model
            ],
            [ // n-n relationship (with pivot table)
                'label'     => trans('backpack::permissionmanager.extra_permissions'), // Table column heading
                'type'      => 'select_multiple',
                'name'      => 'permissions', // the method that defines the relationship in your Model
                'entity'    => 'permissions', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model'     => config('permission.models.permission'), // foreign key model
            ],
        ]);

        // Role Filter
        $this->crud->addFilter(
            [
                'name'  => 'role',
                'type'  => 'dropdown',
                'label' => trans('backpack::permissionmanager.role'),
            ],
            config('permission.models.role')::all()->pluck('name', 'id')->toArray(),
            function ($value) { // if the filter is active
                $this->crud->addClause('whereHas', 'roles', function ($query) use ($value) {
                    $query->where('role_id', '=', $value);
                });
            }
        );

        // Extra Permission Filter
        $this->crud->addFilter(
            [
                'name'  => 'permissions',
                'type'  => 'select2',
                'label' => trans('backpack::permissionmanager.extra_permissions'),
            ],
            config('permission.models.permission')::all()->pluck('name', 'id')->toArray(),
            function ($value) { // if the filter is active
                $this->crud->addClause('whereHas', 'permissions', function ($query) use ($value) {
                    $query->where('permission_id', '=', $value);
                });
            }
        );
    }

    public function setupCreateOperation()
    {
        $this->addUserFields();
        $this->crud->setValidation(StoreRequest::class);
    }

    public function setupUpdateOperation()
    {
        $this->addUserFields();
        $this->crud->setValidation(UpdateRequest::class);
    }

    /**
     * Store a newly created resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->setRequest($this->handlePasswordInput($this->crud->getRequest()));
        $this->crud->unsetValidation(); // validation has already been run

        $result = $this->traitStore();

        if(($result) && $this->crud->getCurrentEntry()->hasRole('resident')==true){
            $resident = Resident::create([
                'user_id' => $this->crud->getCurrentEntry()->id,
                'barangay_id' => $this->crud->getCurrentEntry()->barangay_id,
                'first_name' => $this->crud->getCurrentEntry()->first_name,
                'middle_name' => $this->crud->getCurrentEntry()->middle_name,
                'last_name' => $this->crud->getCurrentEntry()->last_name,
                'email' => $this->crud->getCurrentEntry()->email,
                'contact_number' => $this->crud->getCurrentEntry()->contact_number,
                'birthdate' => $this->crud->getCurrentEntry()->birthdate,
                'province' => $this->crud->getCurrentEntry()->province,
                'city' => $this->crud->getCurrentEntry()->city,
                'barangay' => $this->crud->getCurrentEntry()->barangay,
                'detailed_address' => $this->crud->getCurrentEntry()->detailed_address,
                'health_concern' => $this->crud->getCurrentEntry()->health_concern,
                'pwd' => $this->crud->getCurrentEntry()->pwd,
                'senior_citizen' => $this->crud->getCurrentEntry()->senior_citizen,
            ]);
        }

        if(($result) && $this->crud->getCurrentEntry()->hasRole('employee')==true){
            $employee = Employee::create([
                'user_id' => $this->crud->getCurrentEntry()->id,
                'barangay_id' => $this->crud->getCurrentEntry()->barangay_id,
                'first_name' => $this->crud->getCurrentEntry()->first_name,
                'middle_name' => $this->crud->getCurrentEntry()->middle_name,
                'last_name' => $this->crud->getCurrentEntry()->last_name,
                'email' => $this->crud->getCurrentEntry()->email,
                'contact_number' => $this->crud->getCurrentEntry()->contact_number,
                'birthdate' => $this->crud->getCurrentEntry()->birthdate,
                'province' => $this->crud->getCurrentEntry()->province,
                'city' => $this->crud->getCurrentEntry()->city,
                'barangay' => $this->crud->getCurrentEntry()->barangay,
                'detailed_address' => $this->crud->getCurrentEntry()->detailed_address,
            ]);
        }

        if(($result) && $this->crud->getCurrentEntry()->hasRole('official')==true){
            $official = Official::create([
                'user_id' => $this->crud->getCurrentEntry()->id,
                'barangay_id' => $this->crud->getCurrentEntry()->barangay_id,
                'first_name' => $this->crud->getCurrentEntry()->first_name,
                'middle_name' => $this->crud->getCurrentEntry()->middle_name,
                'last_name' => $this->crud->getCurrentEntry()->last_name,
                'email' => $this->crud->getCurrentEntry()->email,
                'contact_number' => $this->crud->getCurrentEntry()->contact_number,
                'birthdate' => $this->crud->getCurrentEntry()->birthdate,
                'province' => $this->crud->getCurrentEntry()->province,
                'city' => $this->crud->getCurrentEntry()->city,
                'barangay' => $this->crud->getCurrentEntry()->barangay,
                'detailed_address' => $this->crud->getCurrentEntry()->detailed_address,
            ]);
        }
        return $result;
    }

    /**
     * Update the specified resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->setRequest($this->handlePasswordInput($this->crud->getRequest()));
        $this->crud->unsetValidation(); // validation has already been run

        $result = $this->traitUpdate();

        if(($result) && $this->crud->getCurrentEntry()->hasRole('resident')==true){
            $resident = Resident::where('user_id', $this->crud->getCurrentEntry()->id)->update([
                'user_id' => $this->crud->getCurrentEntry()->id,
                'barangay_id' => $this->crud->getCurrentEntry()->barangay_id,
                'first_name' => $this->crud->getCurrentEntry()->first_name,
                'middle_name' => $this->crud->getCurrentEntry()->middle_name,
                'last_name' => $this->crud->getCurrentEntry()->last_name,
                'email' => $this->crud->getCurrentEntry()->email,
                'contact_number' => $this->crud->getCurrentEntry()->contact_number,
                'birthdate' => $this->crud->getCurrentEntry()->birthdate,
                'province' => $this->crud->getCurrentEntry()->province,
                'city' => $this->crud->getCurrentEntry()->city,
                'barangay' => $this->crud->getCurrentEntry()->barangay,
                'detailed_address' => $this->crud->getCurrentEntry()->detailed_address,
                'health_concern' => $this->crud->getCurrentEntry()->health_concern,
                'pwd' => $this->crud->getCurrentEntry()->pwd,
                'senior_citizen' => $this->crud->getCurrentEntry()->senior_citizen,
            ]);
        }

        if(($result) && $this->crud->getCurrentEntry()->hasRole('employee')==true){
            $employee = Employee::where('user_id', $this->crud->getCurrentEntry()->id)->update([
                'user_id' => $this->crud->getCurrentEntry()->id,
                'barangay_id' => $this->crud->getCurrentEntry()->barangay_id,
                'first_name' => $this->crud->getCurrentEntry()->first_name,
                'middle_name' => $this->crud->getCurrentEntry()->middle_name,
                'last_name' => $this->crud->getCurrentEntry()->last_name,
                'email' => $this->crud->getCurrentEntry()->email,
                'contact_number' => $this->crud->getCurrentEntry()->contact_number,
                'birthdate' => $this->crud->getCurrentEntry()->birthdate,
                'province' => $this->crud->getCurrentEntry()->province,
                'city' => $this->crud->getCurrentEntry()->city,
                'barangay' => $this->crud->getCurrentEntry()->barangay,
                'detailed_address' => $this->crud->getCurrentEntry()->detailed_address,
            ]);
        }

        if(($result) && $this->crud->getCurrentEntry()->hasRole('official')==true){
            $official = Official::where('user_id', $this->crud->getCurrentEntry()->id)->update([
                'user_id' => $this->crud->getCurrentEntry()->id,
                'barangay_id' => $this->crud->getCurrentEntry()->barangay_id,
                'first_name' => $this->crud->getCurrentEntry()->first_name,
                'middle_name' => $this->crud->getCurrentEntry()->middle_name,
                'last_name' => $this->crud->getCurrentEntry()->last_name,
                'email' => $this->crud->getCurrentEntry()->email,
                'contact_number' => $this->crud->getCurrentEntry()->contact_number,
                'birthdate' => $this->crud->getCurrentEntry()->birthdate,
                'province' => $this->crud->getCurrentEntry()->province,
                'city' => $this->crud->getCurrentEntry()->city,
                'barangay' => $this->crud->getCurrentEntry()->barangay,
                'detailed_address' => $this->crud->getCurrentEntry()->detailed_address,
            ]);
        }

        return $result;
    }

    /**
     * Handle password input fields.
     */
    protected function handlePasswordInput($request)
    {
        // Remove fields not present on the user.
        $request->request->remove('password_confirmation');
        $request->request->remove('roles_show');
        $request->request->remove('permissions_show');

        // Encrypt password if specified.
        if ($request->input('password')) {
            $request->request->set('password', Hash::make($request->input('password')));
        } else {
            $request->request->remove('password');
        }

        return $request;
    }

    protected function addUserFields()
    {
        $this->crud->addFields([
            [
                'name'  => 'first_name',
                'label' => 'First Name',
                'type'  => 'text',
            ],
            [
                'name'  => 'middle_name',
                'label' => 'Middle Name',
                'type'  => 'text',
            ],
            [
                'name'  => 'last_name',
                'label' => 'Last Name',
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],
            [
                'name'  => 'contact_number',
                'label' => 'Contact Number',
                'type'  => 'text',
            ],
            [
                'name'  => 'birthdate',
                'type'  => 'date_picker',
                'label' => 'Birthday',

                // optional:
                'date_picker_options' => [
                'todayBtn' => 'linked',
                'format'   => 'dd-mm-yyyy',
                'language' => 'en'
                ],
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
                'name' => 'health_concern',
                'label' => "Health Concern",
                'type' => 'textarea',
            ],
            [
                'name'        => 'pwd', // the name of the db column
                'label'       => 'Are you a PWD?', // the input label
                'type'        => 'radio',
                'options'     => [
                    // the key will be stored in the db, the value will be shown as label;
                    0 => "No",
                    1 => "Yes"
                ],
                'inline' => true,
            ],
            [
                'name'        => 'senior_citizen', // the name of the db column
                'label'       => 'Are you a Senior Citizen?', // the input label
                'type'        => 'radio',
                'options'     => [
                    // the key will be stored in the db, the value will be shown as label;
                    0 => "No",
                    1 => "Yes"
                ],
                'inline' => true,
            ],
            [
                'label' => "Assigned Barangay",
                'type' => 'select2',
                'name' => 'barangay_id',
                'entity' => 'assign',
                'attribute' => 'name',
                'model' => "App\Models\Barangay",
                'options'   => (function ($query) {
                    return $query->orderBy('name', 'ASC')->get();
                }),
            ],
            [
                'name'  => 'password',
                'label' => trans('backpack::permissionmanager.password'),
                'type'  => 'password',
            ],
            [
                'name'  => 'password_confirmation',
                'label' => trans('backpack::permissionmanager.password_confirmation'),
                'type'  => 'password',
            ],
            [
                // two interconnected entities
                'label'             => trans('backpack::permissionmanager.user_role_permission'),
                'field_unique_name' => 'user_role_permission',
                'type'              => 'checklist_dependency',
                'name'              => ['roles', 'permissions'],
                'subfields'         => [
                    'primary' => [
                        'label'            => trans('backpack::permissionmanager.roles'),
                        'name'             => 'roles', // the method that defines the relationship in your Model
                        'entity'           => 'roles', // the method that defines the relationship in your Model
                        'entity_secondary' => 'permissions', // the method that defines the relationship in your Model
                        'attribute'        => 'name', // foreign key attribute that is shown to user
                        'model'            => config('permission.models.role'), // foreign key model
                        'pivot'            => true, // on create&update, do you need to add/delete pivot table entries?]
                        'number_columns'   => 3, //can be 1,2,3,4,6
                    ],
                    'secondary' => [
                        'label'          => ucfirst(trans('backpack::permissionmanager.permission_singular')),
                        'name'           => 'permissions', // the method that defines the relationship in your Model
                        'entity'         => 'permissions', // the method that defines the relationship in your Model
                        'entity_primary' => 'roles', // the method that defines the relationship in your Model
                        'attribute'      => 'name', // foreign key attribute that is shown to user
                        'model'          => config('permission.models.permission'), // foreign key model
                        'pivot'          => true, // on create&update, do you need to add/delete pivot table entries?]
                        'number_columns' => 3, //can be 1,2,3,4,6
                    ],
                ],
            ],
        ]);
    }
}
