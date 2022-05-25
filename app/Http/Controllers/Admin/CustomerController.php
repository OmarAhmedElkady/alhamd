<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerRequest;
use App\Models\customer;
use App\Models\Language;
use App\Models\Order;
use App\Models\Product_order;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request) {

        // Bring all customers according to the language of the site
        $customers = customer::Selection()->Abbr()->orderBy('client_permissions')->paginate(PAGINATE_CUSTOMERS) ;

        return view('admin.customers.index')->with('customers' , $customers) ;
    }  // The end of the index function




    public function search(Request $request)    {

        if($request->has('search') && $request->search != '')  {
            $search = $request->search ;

            // Find the data entered by the user

            // Bring the customer the user is looking for
            $customers = customer::Selection()->Abbr()->where('name' , 'like' , '%' . $search . '%')->orderBy('client_permissions')->paginate(PAGINATE_CUSTOMERS) ;

            if($customers->count() > 0) {

                return json_encode($customers) ; //  Return data to Ajax using this function

            }   else    {  // If the customer that the user searched for is not found in the database

                return json_encode(['status' => false , 'search' => $search]) ;
            }  //  End Of Else

        }   else    { // If the user enters a null value

            // Bring all customers by language
            $customers = customer::Selection()->Abbr()->orderBy('client_permissions')->paginate(PAGINATE_CUSTOMERS) ;

            return json_encode($customers) ;
        }  // End Of Else
    }    // The end of the Search function



    public function create()    {

        $languages = Language::Selection()->get() ;     // fetch all language

        return view('admin.customers.create')->with('languages' , $languages) ;
    }   // The end of the Create function



    public function store(CustomerRequest $request) {
        try {

            $customerData = [] ;  //  This is the variable in which we store customer data in all languages

            // Get the abbreviation of all languages in the database
            $languages = Language::select('abbr')->get() ;

            // This array stores the names of the new client
            $customerNames = collect( $request->customer ) ;

            // If the number of customer names is the same as the number of languages
            if ( $languages->count() == $customerNames->count() ) {

                $phone = null ;
                if ($request->has('phone')) {
                    $phone =  $request->phone ;
                }

                $title = null ;
                if ($request->has('title')) {
                    $title =  $request->title ;
                }


                /* Now we want to fetch the last [id] in the database
                    * To be able to specify the value of the [translation_of] field
                */
                $firstcustomer = customer::get()->first() ;
                if ($firstcustomer) {
                    $last_id = customer::orderBy('id', 'DESC')->first()->id;
                }   else    {
                    $last_id = 0 ;
                }
                ++$last_id ;


                foreach ($languages as $key => $lang) {

                    $customerData [] = [
                        'abbr'              =>  $lang['abbr'] ,
                        'translation_of'    =>  $last_id ,
                        'client_permissions'=>  $request->client_permissions ,
                        'name'              =>  $customerNames[$key]['name'] ,
                        'phone'             =>  $phone ,
                        'title'             =>  $title ,
                        'created_at'        =>  now() ,
                    ] ;
                }  //  End Of Foreach

            }   else    {  // If the user does not enter the name of the product in all its languages
                return response()->json([
                    'status'    =>  'false' ,
                ]) ;
            }   // End Of Else


            customer::insert($customerData) ;  //  Add customer data to the database


            return response()->json([
                'status'    =>  true ,
            ]) ;

        } catch (\Exception $ex) {
            return response()->json([
                'status'    =>  'false' ,
            ]) ;
        }   //  End Of Catch
    }    // The end of the Store function




    public function edit($id)   {
        try {

            // If there really is [id] and also its type is a number
            if(isset($id) && filter_var($id , FILTER_VALIDATE_INT)) {

                // Fetch all customer data
                $customer = customer::Selection()->where('translation_of' , $id)->get() ;

                $languages = Language::select('abbr' , 'name')->get() ;  // Get shortcut of all languages


                if ($customer->count() > 0 && $languages->count() > 0) {
                    return view('admin.customers.edit')->with('customer' , $customer)->with('languages' , $languages) ;

                } else {
                    return redirect()->route('admin.customer.index') ;
                }  //   End Of Else


            }   else    {   // If the user enters the top [ id ] with a value other than the number
                return redirect()->route('admin.customer.index') ;
            }   // End Of Else

        } catch (\Exception $ex) {
            return redirect()->route('admin.customer.index') ;
        }   //  End Of Catch
    }    // The end of the Edit function


    public function update(customerRequest $request)    {

        try {
            if (isset($request->translation_of) && filter_var($request->translation_of , FILTER_VALIDATE_INT)) {

                // Get the [id] of the client from the database
                $oldCustomerData = customer::select('id')->where('translation_of' , $request->translation_of)->first() ;

                // If the customer you want to modify data already exists in the database
                if (isset($oldCustomerData) && $oldCustomerData->count() > 0 ) {

                    $customer = collect( $request->customer ) ;  // This array stores the names of the new clients

                    $languages = Language::select('abbr')->get() ;  //  Get all languages

                    $newCustomerData = [] ;  //  This is the array in which we store all the new customer data

                    if ( $languages->count() == $customer->count() ) {

                        foreach ($languages as $key => $lang) {

                            // Save the product in all its languages in this array
                            $newCustomerData [] = [
                                'abbr'              =>  $lang['abbr'] ,
                                'translation_of'    =>  $request->translation_of ,
                                'client_permissions'=>  $request->client_permissions ,
                                'name'              =>  $customer[$key]['name'] ,
                                'phone'             =>  $request->phone ,
                                'title'             =>  $request->title ,
                                'updated_at'        =>  now() ,
                            ] ;
                        }   //  End Of Foreach
                    }   else    {
                        return response()->json([
                            'status'    =>  'false' ,
                        ]) ;
                    }   //  End Of Else

                    DB::beginTransaction() ;
                        // Delete old customer data
                        customer::where('translation_of' , $request->translation_of)->delete() ;
                        // Adding new customer data
                        customer::insert($newCustomerData) ;
                    DB::commit() ;

                    return response()->json([
                        'status'    =>  true ,
                    ]) ;

                } else {    //  If the customer whose data you want to modify does not exist at all
                    return response()->json([
                        'status'    =>  'false' ,
                    ]) ;
                }   //  End Of Else

            }   else    {
                return response()->json([
                    'status'    =>  'false' ,
                ]) ;
            }   //  End Of Else

        } catch (\Exception $ex) {
            DB::rollBack() ;
            return response()->json([
                'status'    =>  'false' ,
            ]) ;
        }   // End Catch
    }   // The end of the Update function



    public function delete(Request $request )    {

        try {
            if ( isset($request->translation_of) && filter_var($request->translation_of , FILTER_VALIDATE_INT) ) {

                // Get the [id] of the client you want to delete
                $customers = customer::select('id')->where('translation_of' , $request->translation_of)->get() ;

                // If the client you want to delete already exists in the database
                if ($customers->count() > 0) {

                    DB::beginTransaction() ;
                        $orders = Order::select('id')->where('client_id' , $request->translation_of)->get() ;

                        foreach($orders as $order) {
                            $product_order = Product_order::select('id')->where('order_id' , $order->id)->get() ;
                            Product_order::destroy($product_order->toArray()) ;
                        }

                        Order::destroy($orders->toArray()) ;

                        customer::destroy($customers->toArray());   // delete customer

                    DB::commit() ;

                    return response()->json([
                        'status'    =>  'true' ,
                        'translation_of'        =>  $request->translation_of ,
                    ]) ;
                }   //  End If
            }   //  End If
        } catch (\Exception $ex) {
            DB::rollBack() ;
            return response()->json([
                'status'    =>  'false' ,
            ]) ;
        }   // End Catch
    }   // The end of the delete function

}
