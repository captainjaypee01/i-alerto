@extends(backpack_view('blank'))

@php
  $defaultBreadcrumbs = [
    trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
    $crud->entity_name_plural => url($crud->route),
    trans('backpack::crud.preview') => false,
  ];

  // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
  $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
	<section class="container-fluid d-print-none">
    	<a href="javascript: window.print();" class="btn float-right"><i class="la la-print"></i></a>
		<h2>
	        <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
	        <small>{!! $crud->getSubheading() ?? mb_ucfirst(trans('backpack::crud.preview')).' '.$crud->entity_name !!}.</small>
	        @if ($crud->hasAccess('list'))
	          <small class=""><a href="{{ url($crud->route) }}" class="font-sm"><i class="la la-angle-double-left"></i> {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
	        @endif
	    </h2>
    </section>
@endsection

@section('content')
<div class="row">
	<div class="{{ $crud->getShowContentClass() }}">

	<!-- Default box -->
	  <div class="">
	  	@if ($crud->model->translationEnabled())
	    <div class="row">
	    	<div class="col-md-12 mb-2">
				<!-- Change translation button group -->
				<div class="btn-group float-right">
				  <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				    {{trans('backpack::crud.language')}}: {{ $crud->model->getAvailableLocales()[request()->input('locale')?request()->input('locale'):App::getLocale()] }} &nbsp; <span class="caret"></span>
				  </button>
				  <ul class="dropdown-menu">
				  	@foreach ($crud->model->getAvailableLocales() as $key => $locale)
					  	<a class="dropdown-item" href="{{ url($crud->route.'/'.$entry->getKey().'/show') }}?locale={{ $key }}">{{ $locale }}</a>
				  	@endforeach
				  </ul>
				</div>
			</div>
	    </div>
	    @else
	    @endif
	    <div class="card no-padding no-border">
			<table class="table table-striped mb-0">
		        <tbody>
		        @foreach ($crud->columns() as $column)
		            <tr>
		                <td>
		                    <strong>{!! $column['label'] !!}:</strong>
		                </td>
                        <td>
							@if (!isset($column['type']))
		                      @include('crud::columns.text')
		                    @else
		                      @if(view()->exists('vendor.backpack.crud.columns.'.$column['type']))
		                        @include('vendor.backpack.crud.columns.'.$column['type'])
		                      @else
		                        @if(view()->exists('crud::columns.'.$column['type']))
		                          @include('crud::columns.'.$column['type'])
		                        @else
		                          @include('crud::columns.text')
		                        @endif
		                      @endif
		                    @endif
                        </td>
		            </tr>
		        @endforeach
				@if ($crud->buttons()->where('stack', 'line')->count())
					<tr>
						<td><strong>{{ trans('backpack::crud.actions') }}</strong></td>
						<td>
							@include('crud::inc.button_stack', ['stack' => 'line'])
						</td>
					</tr>
				@endif
		        </tbody>
			</table>
	    </div><!-- /.box-body -->
	  </div><!-- /.box -->

	</div>
</div>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                Add User
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.evacuation.user.add', $crud->getCurrentEntry()->id) }}" class="my-2 my-lg-0">

                    {!! csrf_field() !!}
                    <div class="row">
                        <div class="col col-md-12 col-12">
                            <div class="form-group">
                                <label>Assigned Users</label>
                                <select
                                    name="users[]"
                                    data-init-function="bpFieldInitSelect2Element"
                                    id="user-select2"
                                    class="form-control w-100 select2_field" multiple>

                                    <option value="">-</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">
                                    <span class="la la-save" role="presentation" aria-hidden="true"></span> &nbsp;
                                    <span data-value="save_and_back">Add and Save</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                List of users
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <table id="crudTable" class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Assigned Barangay</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($evacuationUsers as $user)
                                <tr>
                                    <td>{{ $user->full_name }}</td>
                                    <td>
                                        {{
                                            $user->assigned_barangay
                                        }}
                                    </td>
                                    <td>{!! $user->remove_evacuation_user !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('after_styles')
	<link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/crud.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/show.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/list.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/create.css') }}">
    <link href="{{ asset('packages/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('packages/select2-bootstrap-theme/dist/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('after_scripts')
	<script src="{{ asset('packages/backpack/crud/js/crud.js') }}"></script>
    <script src="{{ asset('packages/backpack/crud/js/show.js') }}"></script>

    <script src="{{ asset('packages/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $("#user-select2").select2();
    </script>
    <script>
        if (typeof respondEntry != 'function') {
            $("[data-button-type=respond]").unbind('click');

            function respondEntry(button) {
            // ask for confirmation before deleting an item
            // e.preventDefault();
            var button = $(button);
            var route = button.attr('data-route');
            var row = $("#crudTable a[data-route='"+route+"']").closest('tr');
            console.log(route);
            swal({
                title: "Warning",
                text: "Are you sure you want to remove the user ?",
                icon: "warning",
                buttons: {
                        cancel: {
                        text: "Cancel",
                        value: null,
                        visible: true,
                        className: "bg-secondary",
                        closeModal: true,
                    },
                    success: {
                        text: "Yes",
                        value: true,
                        visible: true,
                        className: "bg-success",
                    }
                },
                }).then((value) => {
                    if (value) {
                        $.ajax({
                            url: route,
                            type: 'PATCH',
                            success: function(result) {
                                if (result != 1) {
                                    // Show an error alert
                                    swal({
                                        title: "NOT Updated",
                                        text: "There\'s been an error. Your item might not have been deleted.",
                                        icon: "error",
                                        timer: 2000,
                                        buttons: false,
                                    });
                                } else {
                                    // Show a success message
                                    swal({
                                        title: "User Removed",
                                        text: "The list has been updated successfully.",
                                        icon: "success",
                                        timer: 4000,
                                        buttons: false,
                                    });

                                    // Hide the modal, if any
                                    $('.modal').modal('hide');

                                    $("#crudTable").DataTable().ajax.reload();

                                }
                            },
                            error: function(result) {
                                // Show an alert with the result
                                console.log(result);
                                swal({
                                    title: "NOT updated",
                                    text: "There\'s been an error. Your item might not have been updated.",
                                    icon: "error",
                                    timer: 4000,
                                    buttons: false,
                                });
                            }
                        });
                    }
                });

            }
        }


    </script>
@endsection
