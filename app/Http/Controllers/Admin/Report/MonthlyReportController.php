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
        $medical = Alert::where('type', 'medical')->whereMonth('created_at',$month)->whereYear('created_at',$year)->count(); 
        $earthquake = Alert::where('type', 'earthquake')->whereMonth('created_at',$month)->whereYear('created_at',$year)->count(); 
        $typhoon = Alert::where('type', 'typhoon')->whereMonth('created_at',$month)->whereYear('created_at',$year)->count(); 
        $others = Alert::where('type', 'others')->whereMonth('created_at',$month)->whereYear('created_at',$year)->count();

        $monthlyChart = new ReportChart();
        
        $labelTypes = ['Fire', 'Flood', 'Accident', 'Medical', 'Earthquake', 'Typhoon', 'Others'];
        $labelColorTypes = ['red', 'blue', 'green', 'cyan', '#900C3F', '#D1FF33', '#33C6FF'];

        $dataset = [$fire, $flood, $accident, $medical, $earthquake, $typhoon, $others];
        
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

        $responded = Alert::where('status', 1)->whereMonth('created_at', $month)->whereYear('created_at',$year)->get();
                        
        $no_responded = Alert::where('status', 0)->whereMonth('created_at', $month)->whereYear('created_at',$year)->get();
        
        return view('custom.report.monthly', [
            'year' => $year,
            'month' => $month,
            'chart' => $monthlyChart,
            'responded' => $responded,
            'no_responded' => $no_responded,
        ]);
    }
}
