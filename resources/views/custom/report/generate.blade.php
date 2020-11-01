@extends(backpack_view('blank'))

@section('header')
	<section class="container-fluid">
	  <h2>
            <span class="text-capitalize">Reports</span>
	  </h2>
    </section>
    <hr>
@endsection

@section('content')
<div class="animated fadeIn">

    <div class="row">
        <div class="col col-md-12 col-sm-12">
            
            <form action="{{ route('admin.report.export.alert') }}" class="form-inline my-2 my-lg-0">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-capitalize text-title">
                                Alert Reports
                        </h3>
                    </div>
                    <div class="card-body">
                        
                            <div class="row">
                                <div class="col col-md-12 col-sm-12">    
                                    <div class="form-group mr-2">
                                        <select name="type" class="w-100 form-control" >
                                            <option value selected>Select Alert Type</option> 
                                            <option value="all">All Types</option>
                                            <option value="fire">Fire</option>
                                            <option value="accident">Accident</option>
                                            <option value="flood">Flood</option>
                                            <option value="medical">Medical</option>
                                            <option value="earthquake">Earthquake</option>
                                            <option value="typhoon">Typhoon</option>
                                            <option value="others">Others</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-md-6 col-sm-12 mt-2">
                                        <div class="form-group mr-2">
                                            <select name="year" class="form-control" id="year" required>
                                                <option value="" selected>Select Year</option>
                                                <option value="2019">2019</option>
                                                <option value="2020">2020</option>
                                            </select>
                                        </div>
                                </div>
                                <div class="col col-md-6 col-sm-12 mt-2">
                                        <div class="form-group mr-2">
                                            <select name="month" class="form-control" id="month"  required>
                                                <option value="" selected>Select Month</option>
                                                <option value="1">January</option>
                                                <option value="2">February</option>
                                                <option value="3">March</option>
                                                <option value="4">April</option>
                                                <option value="5">May</option>
                                                <option value="6">June</option>
                                                <option value="7">July</option>
                                                <option value="8">August</option>
                                                <option value="9">September</option>
                                                <option value="10">October</option>
                                                <option value="11">November</option>
                                                <option value="12">December</option>
                                            </select>
                                        </div>
                                        <input type="hidden" name="export" value="1">
                                    
                                </div>
                                    
                            </div>
                            
                    </div>
                    <div class="card-footer">
                        
                        <div class="form-group float-right">
                            <button type="submit" class="btn btn-success">Export</button>
                        </div>
                    </div>
                </div>
            
            </form>
        </div>
    </div>
    
    <div class="row">
        <div class="col"></div>
    </div>
</div>
@endsection

@section('after_styles')
  <!-- DATA TABLES -->
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">

  <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/crud.css') }}">
  <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/form.css') }}">
  <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/list.css') }}">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
      
  <!-- CRUD LIST CONTENT - crud_list_styles stack -->
  @stack('crud_list_styles')
@endsection

@section('after_scripts')
 
    <script src="{{ asset('packages/backpack/crud/js/crud.js') }}"></script>
    <script src="{{ asset('packages/backpack/crud/js/form.js') }}"></script>
    <script src="{{ asset('packages/backpack/crud/js/list.js') }}"></script>

    <!-- CRUD LIST CONTENT - crud_list_scripts stack -->
    @stack('crud_list_scripts')
    
@endsection

