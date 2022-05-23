<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\category;
use App\Models\customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index() {

        $categories_count = category::LanguageSelect()->count() ;
        $products_count = Product::Abbr()->count() ;
        $customers_count = customer::Abbr()->count() ;
        $users_count = User::count() ;

        //  Calculates the total sales for the month
        $sales_data = Order::select(
            DB::raw('YEAR(created_at) as year'),    // bring the year
            DB::raw('MONTH(created_at) as month'),  // bring month
            DB::raw('SUM(total_price) as sum')            // Total sales for the month
        )->groupBy('month')->get();

        $collection  = collect($sales_data) ;

        $year = $collection ->pluck('year'); //  Return the years only

        $month = $collection ->pluck('month');  // Return only months

        $sum = $collection ->pluck('sum');      //  Returns the total sales for the month

        // Combine month and year
        $yearAndMOnth = $collection->map(function ($item, $key) {
            return $item->year . '-' . $item->month ;
        });

        $chartjs = app()->chartjs
                ->name('lineChartTest')
                ->type('line')
                ->size(['width' => 400, 'height' => 130])
                ->labels($yearAndMOnth->toArray())
                ->datasets([
                    [
                        "label" => __('dashboard.total'),
                        'backgroundColor' => "rgba(38, 185, 154, 0.31)",
                        'borderColor' => "rgba(38, 185, 154, 0.7)",
                        "pointBorderColor" => "rgba(38, 185, 154, 0.7)",
                        "pointBackgroundColor" => "rgba(38, 185, 154, 0.7)",
                        "pointHoverBackgroundColor" => "#fff",
                        "pointHoverBorderColor" => "rgba(220,220,220,1)",
                        'data' => $sum,
                    ],
                ])
                ->options([]);


        return view('admin.dashboard.index' , compact('categories_count', 'products_count', 'customers_count', 'users_count' , 'chartjs')) ;
    }
}
