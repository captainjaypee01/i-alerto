<?php

namespace App\Http\Controllers\Admin\Report;

use App\Charts\ReportChart;
use App\Http\Controllers\Controller;
use App\Models\Alert;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WeeklyAlertReportController extends Controller
{
    
    public function index(){
        // if(!(request()->has('year') && request()->has('week')))
        //     Log::info('Visit Weekly Report page', ['user' => backpack_user()]);

        $year = request()->has('year')? request()->get('year'):Carbon::now()->year;
        $week = request()->has('week')? request()->get('week'):Carbon::now()->weekOfYear;

        // $types = array('fire', 'flood', 'accident', 'medical', 'earthquakes', 'typhoon','others');
        $fire = Alert::where('type', 'fire')->selectRaw('*, YEARWEEK(created_at, 1) as week_number, user_id')
            ->groupBy(DB::raw('week_number, user_id'))->havingRaw('week_number=\'' . $year . ($week < 10 ? '0' . $week : $week) . '\'')->get();
        $flood = Alert::where('type', 'flood')->selectRaw('*, YEARWEEK(created_at, 1) as week_number, user_id')
            ->groupBy(DB::raw('week_number, user_id'))->havingRaw('week_number=\'' . $year . ($week < 10 ? '0' . $week : $week) . '\'')->get();
        $accident = Alert::where('type', 'accident')->selectRaw('*, YEARWEEK(created_at, 1) as week_number, user_id')
            ->groupBy(DB::raw('week_number, user_id'))->havingRaw('week_number=\'' . $year . ($week < 10 ? '0' . $week : $week) . '\'')->get();
        $medical = Alert::where('type', 'medical')->selectRaw('*, YEARWEEK(created_at, 1) as week_number, user_id')
            ->groupBy(DB::raw('week_number, user_id'))->havingRaw('week_number=\'' . $year . ($week < 10 ? '0' . $week : $week) . '\'')->get();
        $earthquake = Alert::where('type', 'earthquakes')->selectRaw('*, YEARWEEK(created_at, 1) as week_number, user_id')
            ->groupBy(DB::raw('week_number, user_id'))->havingRaw('week_number=\'' . $year . ($week < 10 ? '0' . $week : $week) . '\'')->get();
        $typhoon = Alert::where('type', 'typhoon')->selectRaw('*, YEARWEEK(created_at, 1) as week_number, user_id')
            ->groupBy(DB::raw('week_number, user_id'))->havingRaw('week_number=\'' . $year . ($week < 10 ? '0' . $week : $week) . '\'')->get();
        $others = Alert::where('type', 'others')->selectRaw('*, YEARWEEK(created_at, 1) as week_number, user_id')
            ->groupBy(DB::raw('week_number, user_id'))->havingRaw('week_number=\'' . $year . ($week < 10 ? '0' . $week : $week) . '\'')->get();


        $responded = Alert::where('status', 1)->selectRaw('*, YEARWEEK(created_at, 1) as week_number, user_id')
                        ->groupBy(DB::raw('week_number, user_id'))->havingRaw('week_number=\'' . $year . ($week < 10 ? '0' . $week : $week) . '\'')->get();
                        
        $no_responded = Alert::where('status', 0)->selectRaw('*, YEARWEEK(created_at, 1) as week_number, user_id')
                        ->groupBy(DB::raw('week_number, user_id'))->havingRaw('week_number=\'' . $year . ($week < 10 ? '0' . $week : $week) . '\'')->get();
        
        $datasetResponded = [];
        $datasetNotResponded = [];
        $fireData = [];
        $floodData = [];
        $accidentData = [];
        $medicalData = [];
        $earthquakeData = [];
        $typhoonData = [];
        $othersData = [];
        $labels = [];
        for ($i=($week-4); $i<=$week; $i++) {
            $datasetResponded[] = count(Alert::where('status', 1)->selectRaw('YEARWEEK(created_at, 1) as week_number')
                            ->groupBy(DB::raw('week_number'))->havingRaw('week_number=\'' . $year . ($i < 10 ? '0' . $i : $i) . '\'')->get());
                            
            $datasetNotResponded[] = count(Alert::where('status', 0)->selectRaw('YEARWEEK(created_at, 1) as week_number')
                ->groupBy(DB::raw('week_number'))->havingRaw('week_number=\'' . $year . ($i < 10 ? '0' . $i : $i) . '\'')->get());

            $fireData[] = count(Alert::where('type', 'fire')->selectRaw('YEARWEEK(created_at, 1) as week_number, user_id')
                ->groupBy(DB::raw('week_number, user_id'))->havingRaw('week_number=\'' . $year . ($i < 10 ? '0' . $i : $i) . '\'')->get());
            $floodData[] = count(Alert::where('type', 'flood')->selectRaw('YEARWEEK(created_at, 1) as week_number, user_id')
                ->groupBy(DB::raw('week_number, user_id'))->havingRaw('week_number=\'' . $year . ($i < 10 ? '0' . $i : $i) . '\'')->get());
            $accidentData[] = count(Alert::where('type', 'accident')->selectRaw('YEARWEEK(created_at, 1) as week_number, user_id')
                ->groupBy(DB::raw('week_number, user_id'))->havingRaw('week_number=\'' . $year . ($i < 10 ? '0' . $i : $i) . '\'')->get());
            $medicalData[] = count(Alert::where('type', 'medical')->selectRaw('YEARWEEK(created_at, 1) as week_number, user_id')
                ->groupBy(DB::raw('week_number, user_id'))->havingRaw('week_number=\'' . $year . ($i < 10 ? '0' . $i : $i) . '\'')->get());
            $earthquakeData[] = count(Alert::where('type', 'earthquakes')->selectRaw('YEARWEEK(created_at, 1) as week_number, user_id')
                ->groupBy(DB::raw('week_number, user_id'))->havingRaw('week_number=\'' . $year . ($i < 10 ? '0' . $i : $i) . '\'')->get());
            $typhoonData[] = count(Alert::where('type', 'typhoon')->selectRaw('YEARWEEK(created_at, 1) as week_number, user_id')
                ->groupBy(DB::raw('week_number, user_id'))->havingRaw('week_number=\'' . $year . ($i < 10 ? '0' . $i : $i) . '\'')->get());
            $othersData[] = count(Alert::where('type', 'others')->selectRaw('YEARWEEK(created_at, 1) as week_number, user_id')
                ->groupBy(DB::raw('week_number, user_id'))->havingRaw('week_number=\'' . $year . ($i < 10 ? '0' . $i : $i) . '\'')->get());
                
            $date = new Carbon();
            $date->setISODate($year, $i);
            $labels[] = 'Week # '. $i . ($i==Carbon::now()->weekOfYear?' (Current)':'');
        } 
        $chart = new ReportChart();
        
        $chart->labels($labels);
        $chart->dataset('Fire', 'line', $floodData)->color('#CD201F');
        $chart->dataset('Flood', 'line', $floodData)->color('#63C2DE');
        $chart->dataset('Accident', 'line', $accidentData)->color('#f7941d');
        $chart->dataset('Medical', 'line', $accidentData)->color('#33a853');
        $chart->dataset('Earthquake', 'line', $accidentData)->color('#c36d15');
        $chart->dataset('Typhoon', 'line', $accidentData)->color('#545d57');
        $chart->dataset('Others', 'line', $othersData)->color('#000');

        // $chart->dataset('Responded', 'bar', $datasetResponded)->color('#fff');
        // $chart->dataset('No Responded.', 'bar', $datasetNotResponded)->color('#000');

        $chart->options([
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

        return view('custom.report.weekly', [
            'year' => $year,
            'week' => $week < 10?'0'.$week:$week,
            'chart' => $chart,
            'responded' => $responded,
            'no_responded' => $no_responded,
        ]);
    }
}
