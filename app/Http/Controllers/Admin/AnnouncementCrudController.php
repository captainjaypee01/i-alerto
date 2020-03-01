<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AnnouncementRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Log;

/**
 * Class AnnouncementCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AnnouncementCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    
    public function setup()
    {
        $this->crud->setModel('App\Models\Announcement');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/announcement');
        $this->crud->setEntityNameStrings('announcement', 'announcements');
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        $this->crud->setFromDb();
            $this->crud->addColumn(
            [
                'name' => 'created_at',
                'label' => 'Date Posted',
                'type' => 'date',
            ]);
            
        Log::info('Visit Announcement List page', ['user' => backpack_user()]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(AnnouncementRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        $this->crud->setFromDb();
        Log::info('Visit Announcement Create page', ['user' => backpack_user()]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        Log::info('Visit Announcement Update page', [
                        'user' => backpack_user(), 
                        'announcement' => $this->crud->getCurrentEntry()
                        ]);
    }
    
    protected function setupShowOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        $this->crud->setFromDb();
            $this->crud->addColumn(
            [
                'name' => 'created_at',
                'label' => 'Date Posted',
                'type' => 'date',
            ]);
            
        Log::info('Visit Announcement Show page', [
                'user' => backpack_user(), 
                'announcement' => $this->crud->getCurrentEntry()
            ]);
    }

    /**
     * Show the form for creating inserting a new row.
     *
     * @return Response
     */
    public function store(){
        
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        // insert item in the db
        $item = $this->crud->create($this->crud->getStrippedSaveRequest());
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();
        
        Log::info("Announcement created | ", ['user' => backpack_user(), 'announcement' => $item]);
        return $this->crud->performSaveAction($item->getKey());
    }

    
    /**
     * Update the specified resource in the database.
     *
     * @return Response
     */
    public function update()
    {
        $this->crud->hasAccessOrFail('update');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        // update the row in the db
        $item = $this->crud->update($request->get($this->crud->model->getKeyName()),
                            $this->crud->getStrippedSaveRequest());
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        Log::info("Announcement updated | ", ['user' => backpack_user(), 'announcement' => $item]);
        return $this->crud->performSaveAction($item->getKey());
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return string
     */
    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        Log::info("Announcement created | ", ['user' => backpack_user(), 'announcement' => $id]);
        return $this->crud->delete($id);
    }
}
