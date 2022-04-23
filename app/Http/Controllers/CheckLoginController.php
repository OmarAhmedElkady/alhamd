<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckLoginController extends Controller
{

    public function CheckLogin()    {
        if (Auth::id()) {
            // $user       = [ 'users-create' , 'users-read' , 'users-update' , 'users-delete'] ;
            // $customers       = [ 'customers-create' , 'customers-read' , 'customers-update' , 'customers-delete'] ;
            // $categories = [ 'categories-create' , 'categories-read' , 'categories-update' , 'categories-delete'] ;
            // $products   = [ 'products-create' , 'products-read' , 'products-update' , 'products-delete'] ;
            // Auth::user()->attachRole('super_admin');
            // Auth::user()->attachPermissions($user);
            // Auth::user()->attachPermissions($customers);
            // Auth::user()->attachPermissions($categories);
            // Auth::user()->attachPermissions($products);



            if(Auth::user()->hasRole('super_admin'))  {
                return redirect()->route('admin.Dashboard.index') ;
            }   else    {

                if(Auth::user()->hasPermission('customers-read'))  {

                    return redirect()->route('admin.customer.index') ;

                }   elseif (Auth::user()->hasPermission('products-read')) {

                    return redirect()->route('admin.product.index') ;

                }   elseif (Auth::user()->hasPermission('categories-read')) {

                    return redirect()->route('admin.category.index') ;

                }   elseif (Auth::user()->hasPermission('orders-read')) {

                    return redirect()->route('admin.all_order.index') ;

                }   else    {

                    Auth::logout();
                    return redirect()->route('login') ;
                }
            }

        }   else    {
            return redirect()->route('login') ;
        }
    }

    public function logOut()    {
        Auth::logout();
        return redirect()->route('login') ;
    }
}
