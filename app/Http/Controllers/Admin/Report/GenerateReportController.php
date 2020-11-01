<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GenerateReportController extends Controller
{
    public function index(){
        
        Log::info('Visit Generate Report page', ['user' => backpack_user()]);
        return view('custom.report.generate',[
            
        ]);
    }

    public function exportAlert(){

        $alerts = Alert::whereMonth('created_at', request('month'))->whereYear('created_at', request('year'))->orderBy('created_at', 'asc');//->get();
        $type = request('type');
        if(request('type')){        
            if($type != 'all')
                $alerts->where('type', $type);
        }
            
        Log::alert($type);
        

        $alerts = $alerts->get();
        $csv_data = $alerts->reduce(
            function ($data, $collection) {
                
                $data[] = [  
                    $collection->user->name, 
                    $collection->address, 
                    $collection->longitude, 
                    $collection->latitude, 
                    $collection->status_message, 
                    $collection->type,  
                    $collection->created_at,

                ];
                return $data;
            },
            [
                [ 
                    trans('Full name'),
                    trans('Address'),
                    trans('Longitude'),
                    trans('Latitude'),
                    trans('Respond Status'),
                    trans('Type'),
                    trans('Date'),
                ]
            ]
        );
        
        
        $date = Carbon::now()->format('Y-m-d');
        $fileName = Carbon::create()->year(request('year'))->month(request('month'))->format('M Y') . ' Alert List as of ' . $date . '-' . time();
        
        
        Log::info('Generate Export Report', ['user' => backpack_user(), 'filename' => $fileName]);
        // return $response->send();
        return new StreamedResponse(
            function () use ($csv_data) {
                // A resource pointer to the output stream for writing the CSV to
                $handle = fopen('php://output', 'w');
                foreach ($csv_data as $row) {
                    // Loop through the data and write each entry as a new row in the csv
                    fputcsv($handle, $row);
                }

                fclose($handle);
            },
            200,
            [
                'Content-type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename='.$fileName.'.csv',
            ]
        );
    }
}
