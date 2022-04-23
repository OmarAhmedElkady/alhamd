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

        $sales_data = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_price) as sum')
        )->groupBy('month')->get();

        return view('admin.dashboard.index' , compact('categories_count', 'products_count', 'customers_count', 'users_count' , 'sales_data')) ;
    }
}
