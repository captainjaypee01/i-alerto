<?php

namespace App\Http\Controllers\Admin\Report;

use App\Charts\ReportChart;
use App\Http\Controllers\Controller;
use App\Models\Alert;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonthlyReportController extends Controller
{
    public function index(){

        $year = request()->has('year')? request()->get('year'):Carbon::now()->year;
        $month = request()->has('month')? request()->get('month'):Carbon::now()->month;

        $fire = Alert::where('type', 'fire')->whereMonth('created_at',$month)->whereYear('created_at',$year)->count();
        $flood = Alert::where('type', 'flood')->whereMonth('created_at',$month)->whereYear('created_at',$year)->count();
        $accident = Alert::where('type', 'accident')->whereMonth('created_at',$month)->whereYear('created_at',$year)->count();
        $crime = Alert::where('type', 'crime')->whereMonth('created_at',$month)->whereYear('created_at',$year)->count();
        $others = Alert::where('type', 'others')->whereMonth('created_at',$month)->whereYear('created_at',$year)->count();

        $monthlyChart = new ReportChart();

        $labelTypes = ['Fire', 'Flood', 'Accident', 'Crime', 'Others'];
        $labelColorTypes = ['red', 'blue', 'green', 'cyan', '#900C3F'];

        $dataset = [$fire, $flood, $accident, $crime, $others];

        $monthlyChart->labels($labelTypes);

        $monthlyChart->dataset('Types', 'bar', $dataset)->color($labelColorTypes)->backgroundColor($labelColorTypes);

        $monthlyChart->options([
            'scales' => [
                'yAxes' => [
                    [
                        'ticks' => [
                            'precision' => 0,
                        ],
                        'scaleLabel' => [
                            'display' => true,
                            'labelString' => '# of Incidents'
                        ]
                    ]
                ]
            ],
        ]);

        $responded = Alert::where('status', 1)->whereMonth('created_at', $month)->whereYear('created_at',$year)->orderBy("created_at", "asc")->get();

        $no_responded = Alert::where('status', 0)->whereMonth('created_at', $month)->whereYear('created_at',$year)->orderBy("created_at", "asc")->get();

        return view('custom.report.monthly', [
            'year' => $year,
            'month' => $month,
            'chart' => $monthlyChart,
            'responded' => $responded,
            'no_responded' => $no_responded,
            'fireChart' => $this->fireChart($month, $year),
            'floodChart' => $this->floodChart($month, $year),
        ]);
    }

    public function fireChart($month, $year){

        $allAlerts = Alert::selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") as entry_date, count(status) as total')->where('type', 'fire')->whereMonth('created_at',$month)->whereYear('created_at',$year)->groupBy('entry_date')->get();
        $fire = Alert::where('type', 'fire')->whereMonth('created_at',$month)->whereYear('created_at',$year)->count();
        $fireChart = new ReportChart();

        $fireData = [];
        $daysThisMonth = [];
        $monthYear = Carbon::createFromDate($year, $month, 1);
        $currentDate = $monthYear->toDateString();

        for ($i = 1; $i <= $monthYear->daysInMonth; $i++) {
            array_push($fireData, ($offset = $allAlerts->firstWhere('entry_date', $currentDate)) ? $offset->total : null);
            array_push($daysThisMonth, $currentDate);
            $currentDate = $i < $monthYear->daysInMonth ? $monthYear->addDay()->toDateString() : $monthYear->toDateString();
        }

        $fireChart->labels($daysThisMonth);
        $fireChart->dataset('All Fire Alerts', 'bar', $fireData)->color('red');
        $fireChart->options([
            'scales' => [
                'yAxes' => [
                    [
                        'ticks' => [
                            'precision' => 0,
                        ],
                        'scaleLabel' => [
                            'display' => true,
                            'labelString' => '# of Fire Alerts this month'
                        ]
                    ]
                ]
            ],
        ]);

        return $fireChart;

    }

    public function floodChart($month, $year){

        $allAlerts = Alert::selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") as entry_date, count(status) as total')->where('type', 'flood')->whereMonth('created_at',$month)->whereYear('created_at',$year)->groupBy('entry_date')->get();
        $fireChart = new ReportChart();

        $fireData = [];
        $daysThisMonth = [];
        $monthYear = Carbon::createFromDate($year, $month, 1);
        $currentDate = $monthYear->toDateString();

        for ($i = 1; $i <= $monthYear->daysInMonth; $i++) {
            array_push($fireData, ($offset = $allAlerts->firstWhere('entry_date', $currentDate)) ? $offset->total : null);
            array_push($daysThisMonth, $currentDate);
            $currentDate = $i < $monthYear->daysInMonth ? $monthYear->addDay()->toDateString() : $monthYear->toDateString();
        }

        $fireChart->labels($daysThisMonth);
        $fireChart->dataset('All Flood Alerts', 'bar', $fireData)->color('blue');
        $fireChart->options([
            'scales' => [
                'yAxes' => [
                    [
                        'ticks' => [
                            'precision' => 0,
                        ],
                        'scaleLabel' => [
                            'display' => true,
                            'labelString' => '# of Fllod Alerts this month'
                        ]
                    ]
                ]
            ],
        ]);

        return $fireChart;

    }

}
