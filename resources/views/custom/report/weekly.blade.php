@extends(backpack_view('blank'))

@php
    $date = new Carbon\Carbon();
    $date->setISODate($year, $week);
    $i=0;
    $j=0;
@endphp

@php
    function seconds_to_mmss($seconds, $target = 300) {
        $t = round($seconds);

        if($t >= $target){
            return sprintf('%02d:%02d', ($t/60%60), $t%60) . '&nbsp; <i class="fa fa-check fa-sm" style="color:green"></i>';
        }
        else{
            return sprintf('%02d:%02d', ($t/60%60), $t%60);
        }
    }
@endphp


@section('header')
	<section class="container-fluid">
	  <h2>
            <span class="text-capitalize">Alert Reports (Weekly)</span>
	  </h2>
    </section>
    <hr>
@endsection

@section('content')
<div class="animated fadeIn">

    <div class="row">
        <form action="" method="get">
            <div class="form-inline">
                <label for="week">Select Week: &nbsp;</label>
                <input type="text" name="week" id="week_select" class="form-control">
            </div>
        </form>
    </div>
    <div class="row">
        {!! $chart->container() !!}
    </div>
    <hr>
    <div class="row">
        <div class="col col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Responded Alerts</h4>
                    <div class="small text-muted"><strong>Week # {{ $week }}</strong> From: {{ $date->startOfWeek()->format('Y-m-d') }} To: {{ $date->endOfWeek()->format('Y-m-d') }} | <strong>Total: </strong>{{ $responded->count() }}</div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Address</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($responded as $respond)
                            <tr>
                                <td>{{ $respond->id }}</td>
                                <td>{{ $respond->type }}</td>
                                <td>{{ $respond->address }} </td> 
                                <td>{{ $respond->latitude }} </td> 
                                <td>{{ $respond->longitude }} </td> 
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">No Responded Alerts</h4>
                    <div class="small text-muted"><strong>Week # {{ $week }}</strong> From: {{ $date->startOfWeek()->format('Y-m-d') }} To: {{ $date->endOfWeek()->format('Y-m-d') }} | <strong>Total: </strong>{{ $no_responded->count() }}</div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Address</th> 
                                <th>Latitude</th>
                                <th>Longitude</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($no_responded as $respond)
                            <tr>
                                <td>{{ $respond->id }}</td>
                                <td>{{ $respond->type }}</td>
                                <td>{{ $respond->address }} </td> 
                                <td>{{ $respond->latitude }} </td> 
                                <td>{{ $respond->longitude }} </td> 
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
</div>
<div class="container-fluid">
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

  <link rel="stylesheet" href="{{ asset('packages/bootstrap-daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />

  <!-- CRUD LIST CONTENT - crud_list_styles stack -->
  @stack('crud_list_styles')
@endsection

@section('after_scripts')

    <script src="{{ asset('packages/backpack/crud/js/crud.js') }}"></script>
    <script src="{{ asset('packages/backpack/crud/js/form.js') }}"></script>
    <script src="{{ asset('packages/backpack/crud/js/list.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('packages/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('packages/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="https://backstrap.net/vendors/chart.js/js/Chart.min.js"></script>
    <!-- CRUD LIST CONTENT - crud_list_scripts stack -->
    @stack('crud_list_scripts')

    <script>
        $('#week_select').daterangepicker({
            "singleDatePicker": true,
            "minYear": null,
            "showWeekNumbers": true,
            "alwaysShowCalendars": true,
            "startDate": moment().week({{ $week }}).startOf('iweek').format('MM-DD-YYYY'),
            "maxDate": moment()
        }, function(start, end, label) {
            var weeknumber = start.week();
            var year = start.format("YYYY");
            window.location = '{{ route("admin.report.weekly") }}?week=' + weeknumber + '&year=' + year;
        });
    </script>
    {!! $chart->script() !!}
@endsection

