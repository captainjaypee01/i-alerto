@extends(backpack_view('blank'))
@section('content')
<div class="animated fadeIn">
    <div class="row">
        <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-primary">
                <div class="card-body pb-0">
                    
                <div class="text-value">{{ $month_users ?? 0 }}</div>
                <div>Total New Users in {{ Carbon\Carbon::now()->monthName }} {{ Carbon\Carbon::now()->year }}</div>
                </div>
                <div class="chart-wrapper mt-3 mx-3" style="height:70px;"><div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
                </div>
            </div>
        </div>
        <!-- /.col-->
        <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-info">
                <div class="card-body pb-0">
                <button class="btn btn-transparent p-0 float-right" type="button"><i class="icon-location-pin"></i></button>
                <div class="text-value">{{ $total_announcements ?? 0 }}</div>
                <div>Total Announcements in {{ Carbon\Carbon::now()->monthName }} {{ Carbon\Carbon::now()->year }}</div>
                </div>
                <div class="chart-wrapper mt-3 mx-3" style="height:70px;"><div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
                
                </div>
            </div>
        </div>
        <!-- /.col-->
    </div>
    <!-- /.row-->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12">
                <h4 class="card-title mb-0">Announcements</h4>
                <div class="small text-muted">{{ Carbon\Carbon::now()->year }}</div>
                </div>
                <!-- /.col-->
                <div class="col-sm-12 d-none d-md-block">
                    {!! $dashboardChart->container() !!}
                </div>
                <!-- /.col-->
            </div>
            <!-- /.row-->
            <div class="row">
                
            </div>

        </div>

    </div>

    <!-- /.card-->
@endsection

@section('after_styles')
    <link rel="stylesheet" href="https://backstrap.net/vendors/flag-icon-css/css/flag-icon.min.css">
@endsection

@section('after_scripts')
    <script src="https://backstrap.net/vendors/chart.js/js/Chart.min.js"></script>
    <script src="https://backstrap.net/vendors/@coreui/coreui-plugin-chartjs-custom-tooltips/js/custom-tooltips.min.js"></script>
    <script src="https://backstrap.net/js/main.js"></script>
    {!! $dashboardChart->script() !!}
    
@endsection
