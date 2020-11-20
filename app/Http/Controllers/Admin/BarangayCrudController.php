<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BarangayRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class BarangayCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BarangayCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        $this->crud->setModel('App\Models\Barangay');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/barangay');
        $this->crud->setEntityNameStrings('barangay', 'barangays');

        $this->crud->denyAccess(['create', 'update', 'delete']);
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        // $this->crud->setFromDb();

        CRUD::addColumn(['name' => 'name', 'type' => 'text']);
        CRUD::addColumn(['name' => 'evacuations', 'type' => 'relationship']);
    }

    protected function setupShowOperation(){

        CRUD::addColumn(['name' => 'name', 'type' => 'text']);
        CRUD::addColumn(['name' => 'evacuations', 'type' => 'relationship']);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(BarangayRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        // $this->crud->setFromDb();

    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
