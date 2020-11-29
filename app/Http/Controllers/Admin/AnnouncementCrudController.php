<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AnnouncementRequest;
use App\Models\BackpackUser;
use App\Models\Barangay;
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
        if(backpack_user()->hasRole('resident')){
            $this->crud->denyAccess(['create','update','delete']);
        }
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        // $this->crud->setFromDb();
        $this->crud->addColumn([
            'name' => 'title',
            'label' => 'Title',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'evacuations',
            'label' => 'List of Evacuation Centers',
            'type' => 'relationship',
        ]);
        $this->crud->addColumn([
            'name' => 'barangays',
            'label' => 'List of Barangay',
            'type' => 'relationship',
        ]);
        $this->crud->addColumn([
            'name' => 'mobile_created_at',
            'label' => 'Date Posted',
            'type' => 'datetime',
        ]);

        Log::info('Visit Announcement List page', ['user' => backpack_user()]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(AnnouncementRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        $this->crud->setFromDb();
        $this->crud->addField([
            'label' => "Set Available Evacuation Centers for Barangays (optional)",
            'type' => 'select2_multiple',
            'name' => 'evacuations',
            'entity' => 'evacuations',
            'attribute' => 'name',
            'model' => "App\Models\Evacuation",
            'pivot' => true,
            'select_all' => true,
            'options'   => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }),
        ]);
        $this->crud->addField([
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
        // $this->crud->setFromDb();
        $this->crud->addColumn([
            'name' => 'title',
            'label' => 'Title',
            'type' => 'text',
        ]);
        $this->crud->addColumn([
            'name' => 'details',
            'label' => 'Details',
            'type' => 'textarea',
        ]);

        $this->crud->addColumn(
        [
            'name' => 'evacuations',
            'label' => 'List of Evacuation Centers',
            'type' => 'relationship',
        ]);
        $this->crud->addColumn(
        [
            'name' => 'barangays',
            'label' => 'List of Barangay',
            'type' => 'relationship',
        ]);
        $this->crud->addColumn(
        [
            'name' => 'mobile_created_at',
            'label' => 'Date Posted',
            'type' => 'datetime',
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

        $this->notify($request);

        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        Log::info("Announcement created | ", ['user' => backpack_user(), 'announcement' => $item]);
        return $this->crud->performSaveAction($item->getKey());
    }

    public function notify($request)
    {
        $fcm_tokens = [];
        $evacuation_center = ["is_evacuation" => false];
        $data = [];
        if($request->has('evacuations')){
            $evacuation_center["is_evacuation"] = true;
            $evacuation_center["evac_ids"] = $request->evacuations;
            if($request->has('barangays')){
                $accounts = Barangay::with('residents','employees','officials')->whereIn('id',$request->barangays)->get();
                foreach($accounts as $users)
                {
                    foreach($users->residents as $user)
                    {
                        if($user->user->fcm_token != null){
                            $fcm_tokens[] = $user->user->fcm_token;
                        }
                    }

                    foreach($users->employees as $user)
                    {
                        if($user->user->fcm_token != null){
                            $fcm_tokens[] = $user->user->fcm_token;
                        }
                    }

                    foreach($users->officials as $user)
                    {
                        if($user->user->fcm_token != null){
                            $fcm_tokens[] = $user->user->fcm_token;
                        }
                    }
                }
            }
            else{
                $accounts = BackpackUser::role(['employee','official','resident','relative'])->get();
                foreach($accounts as $user)
                {
                    if($user->fcm_token != null){
                        $fcm_tokens[] = $user->fcm_token;
                    }
                }
            }
            $data["body"] = $request->details;
            $data["title"] = "Evacuation Center";
            $data["from_activity"] = "announcement_notif";
            $data["evacuation_center"] = $evacuation_center;
        }
        else{
            $accounts = BackpackUser::role(['employee','official','resident','relative'])->get();
            foreach($accounts as $user)
            {
                if($user->fcm_token != null){
                    $fcm_tokens[] = $user->fcm_token;
                }
            }
            $data["body"] = $request->details;
            $data["title"] = "Announcement";
            $data["from_activity"] = "announcement_notif";
            $data["evacuation_center"] = $evacuation_center;
        }
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array (
            'registration_ids' => $fcm_tokens,
            'data' => $data,
        );
        $fields = json_encode ($fields);

        $headers = array (
            'Authorization: key=' . "AAAAvF1qE-A:APA91bHFsBPdURKVGuqE3IZB7Ztw5REJaRZQl7mpb1lrDuUM0YyYnWHEiZeJpgzKBT0YM4NoAzaznKQE5RnlsB9HdmrjasLRj0HvqGpqwknSOS7eRIg67PyLAbWTAO3RAAeeaTPob2EM",
            'Content-Type: application/json'
        );

        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

        $result = curl_exec ( $ch );
        // echo $result;
        curl_close ( $ch );
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
