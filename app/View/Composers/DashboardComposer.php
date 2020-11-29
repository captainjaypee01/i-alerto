<?php

namespace App\View\Composers;

use App\Charts\DashboardChart;
use App\Models\Announcement;
use App\Models\BackpackUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DashboardComposer
{

    public function __construct()
    {
        // Dependencies automatically resolved by service container...
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // $total_alerts = Alert::whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->count();
        Log::info('test');
        $month_announcements = Announcement::whereYear('created_at', Carbon::now()->year)->count();
        $month_users = BackpackUser::whereYear('created_at', Carbon::now()->year)->count();
        $dashboardChart = new DashboardChart();
        $users = BackpackUser::selectRaw('year(created_at) year, month(created_at) month, count(*) total')
                        ->whereYear('created_at', Carbon::now()->year)
                        ->groupBy('year', 'month')
                        ->orderBy('month', 'asc')
                        ->get(); 
        $announcements = Announcement::selectRaw('year(created_at) year, month(created_at) month, count(*) total')
                        ->whereYear('created_at', Carbon::now()->year)
                        ->groupBy('year', 'month')
                        ->orderBy('month', 'asc')
                        ->get();
        //whereYear('created_at', Carbon::now()->year)->groupBy( DB::raw('Month("created_at")') )->get();//->pluck('count'); 
        // Log::info($announcements);

        $announcement_data = [];
        $user_data = [];
        $monthlabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
        $monthColorlabels = ['red', 'green', 'blue', 'yellow', 'orange', 'cyan', 'magenta', 'black', 'violet', '#900C3F', '#D1FF33', '#33C6FF'];
        $dashboard_labels = [];
        $dashboard_colors = [];
        $monthYear = Carbon::createFromDate(Carbon::now()->year, Carbon::now()->month, 1);

        for($i = 1;$i <= $monthYear->month;$i++){
            array_push($announcement_data, ($offset = $announcements->firstWhere('month', $i)) ? $offset->total : null );
            array_push($user_data, ($offset = $users->firstWhere('month', $i)) ? $offset->total : null );
            array_push($dashboard_labels, $monthlabels[$i-1]);
        }

        $dashboardChart->labels($dashboard_labels);
        $dashboardChart->dataset('Announcement', 'bar', $announcement_data)->color('#900C3F')->backgroundColor('#900C3F'); 
        $dashboardChart->dataset('Users', 'bar', $user_data)->color('#33C6FF')->backgroundColor('#33C6FF');
        // $alertChart = new DashboardChart();
        // $alert_responded = Alert::selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") as entry_date, count(status) as total')->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->where('status', 1)->groupBy('entry_date')->get();
        // $alert_no_responded = Alert::selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") as entry_date, count(status) as total')->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->where('status', 0)->groupBy('entry_date')->get();

        // $alerts = Alert::selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") as entry_date, count(status) as total')->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->groupBy('entry_date')->get();
        
        // $monthYear = Carbon::createFromDate(Carbon::now()->year, Carbon::now()->month, 1);
        // $daysThisMonth = []; // set temp vars
        // $responded_data = [];
        // $no_responded_data = [];
        // $time = [];
        // $currentDate = $monthYear->toDateString();
        
        // for ($i = 1; $i <= $monthYear->daysInMonth; $i++) {
        //     array_push($responded_data, ($offset = $alert_responded->firstWhere('entry_date', $currentDate)) ? $offset->total : null);
        //     array_push($no_responded_data, ($offset = $alert_no_responded->firstWhere('entry_date', $currentDate)) ? $offset->total : null);
        //     array_push($time, ($offset = $alerts->firstWhere('entry_date', $currentDate)) ? $offset->total : null);
        //     array_push($daysThisMonth, $currentDate);
        //     $currentDate = $i < $monthYear->daysInMonth ? $monthYear->addDay()->toDateString() : $monthYear->toDateString();
        // };

        // $alertChart->labels($daysThisMonth);
        // $alertChart->dataset('Responded', 'bar', $responded_data)->color('#63C2DE')->backgroundColor('#63C2DE');
        // $alertChart->dataset('No Responded', 'bar', $no_responded_data)->color('#CD201F')->backgroundColor('#CD201F');

        // $view->with('total_alerts', $total_alerts);
        $view->with('dashboardChart', $dashboardChart);
        $view->with('month_announcements', $month_announcements);
        $view->with('month_users', $month_users);
        // $view->with('alertChart', $alertChart);
    }
}
