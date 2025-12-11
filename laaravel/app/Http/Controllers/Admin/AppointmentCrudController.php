<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AppointmentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AppointmentCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Appointment::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/appointment');
        CRUD::setEntityNameStrings('appointment', 'appointments');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('appointee_name')->label('Appointee Name');
        CRUD::column('designation')->label('Designation');
        CRUD::column('phone_number')->label('Phone Number');
        CRUD::column('appointment_date')->label('Date')->type('date');
        CRUD::column('appointment_time')->label('Time')->type('time');
        CRUD::column('place')->label('Place');
        CRUD::column('status')
            ->label('Status')
            ->type('select_from_array')
            ->options([
                0 => 'Pending',
                1 => 'Completed',
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
        CRUD::field('appointee_name')
            ->type('text')
            ->validationRules('required|string|max:255');

        CRUD::field('designation')
            ->type('text')
            ->validationRules('required|string|max:255');

        CRUD::field('phone_number')
            ->type('text')
            ->validationRules('required|string|max:20');

        CRUD::field('appointment_date')
            ->type('date')
            ->validationRules('required|date');

        CRUD::field('appointment_time')
            ->type('time')
            ->validationRules('required');

        CRUD::field('place')
            ->type('text')
            ->validationRules('required|string|max:255');

        CRUD::field('status')
            ->label('Status')
            ->type('select_from_array')
            ->options([
                0 => 'Pending',
                1 => 'Completed',
            ])
            ->default(0) //optional
            ->validationRules('required|integer');
        
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
