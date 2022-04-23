<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\customer;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index() {

        $orders = Order::Selection()->with(['customer' => function($q){
            $q->select('translation_of' , 'name')->Abbr() ;
        }])->paginate(PAGINATE_ORDERS) ;

        return view('admin.orders.index')->with('orders' , $orders) ;
    }


    public function search(Request $request)    {

        if($request->has('search') && $request->search != '')  {
            $search = $request->search ;

            // Find the data entered by the user

            $orders = Order::Selection()->whereHas('customer', function ($q) use ($request) {

                return $q->where('name', 'like', '%' . $request->search . '%')->Abbr();

            })->with(['customer'])->paginate(PAGINATE_ORDERS);



            if($orders->count() > 0) {

                return json_encode($orders) ; //  Return data to Ajax using this function

            }   else    {  // If the customer that the user searched for is not found in the database

                return json_encode(['status' => false , 'search' => $search]) ;
            }  //  End Of Else

        }   else    { // If the user enters a null value

            $orders = Order::Selection()->with(['customer' => function($q){
                $q->select('translation_of' , 'name')->Abbr() ;
            }])->paginate(PAGINATE_ORDERS) ;

            return json_encode($orders) ;

        }  // End Of Else
    }



    public function show($id)
    {
        try {
            if(isset($id) && filter_var($id , FILTER_VALIDATE_INT)) {
                $order = Order::Selection()->where('id' , $id)->with(['customer' => function($q){
                    return $q->select('translation_of' , 'name' , 'client_permissions')->Abbr() ;
                }])->with(['product_order' => function($product_order){
                    return $product_order->select('product_id' , 'order_id' , 'quantity')->with(['product' => function($product){
                        return $product->select('translation_of' , 'name' , 'pharmacist_price' , 'selling_price')->withTrashed()->Abbr();
                    }]) ;
                }])->first() ;


                return view('admin.orders._products')->with('order' , $order);
            }

        } catch (\Exception $ex) {
            return '';
        }

    }//end of products


    public function status(Request $request)    {
        try {

            if (isset($request->id) && filter_var($request->id , FILTER_VALIDATE_INT)) {
                $order = Order::select('id')->where('id' , $request->id)->first() ;
                if (isset($order) && $order->count() > 0) {
                    $order->update([
                        'status'    =>  '1'
                    ]) ;

                    return response()->json([
                        'status'    =>  '1' ,
                        'id'        =>  $request->id ,
                    ]) ;
                }
            }
        } catch (\Exception $ex) {
            return response()->json([
                'status'    =>  '0' ,
            ]) ;
        }
    }
}
