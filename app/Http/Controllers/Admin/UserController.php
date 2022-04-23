<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Image ;

class UserController extends Controller
{
    public function index(Request $request) {

        if($request->has('search')) {
            if($request->search != '' )  {
                $search = $request->search ;

                // Find the data entered by the user
                $users = User::Selection()->where('name' , 'like' , '%' . $search . '%')->with(['roles' => function($q){
                    $q->select('name') ;
                }])->paginate(PAGINATE_USER) ;

                if($users->count() > 0) {   //  If this user already exists

                    return json_encode($users) ; // Return data to Ajax using this function

                }   else    {
                    // Return the state with false, which means that there is no data
                    return json_encode(['status' => false , 'search' => $search]) ;
                }   //  End Of Else
            }   else    { // If the user enters a null value
                // Fetch all data
                $users = User::Selection()->with(['roles' => function($q){
                    $q->select('name') ;
                }])->paginate(PAGINATE_USER) ;

                return json_encode($users) ;
            }   //  End Of Else

        }   else    { //    If the user enters the page through a linke and not through the search field

            // Bring all users
            $users = User::Selection()->paginate(PAGINATE_USER) ;
            return view('admin.users.index')->with('users' , $users) ;
        }   //  End Of Else
    }    // The end of the index function



    public function create()    {
        return view('admin.users.create') ;
    }    // The end of the Create function



    public function store(UserRequest $request) {
        try {

            /**
             * UploadPhoto :- You upload the image and retrieve the name of the image
             *                This function was written in the Helpers folder
             */
            $photo = UploadPhoto($request->photo , PAGINATE_PHOTO_USER) ;

            $password = Hash::make($request->password) ;    // Password encryption

            DB::beginTransaction() ;
                // Adding a new admin to the database

                $user = User::create([
                    'name'      =>  $request->name ,
                    'email'     =>  $request->email ,
                    'photo'     =>  $photo ,
                    'password'  =>  $password ,
                ]) ;


                // Giving admin permission to the new user
                $user->attachRole("admin");

                // This variable stores all user permissions
                $permission = collect([]) ;

                if ($request->has('users')) {
                    $permission = $permission->merge($request->users) ;
                }

                if ($request->has('categories')) {
                    $permission = $permission->merge($request->categories) ;
                }

                if ($request->has('products')) {
                    $permission = $permission->merge($request->products) ;
                }

                if ($request->has('customers')) {
                    $permission = $permission->merge($request->customers) ;
                }

                if ($request->has('orders')) {
                    $permission = $permission->merge($request->orders) ;
                }

                // If permissions have been added to the new admin
                if ($permission->count() > 0) {
                    $user->syncPermissions($permission);
                }

            DB::commit() ;

            return response()->json([
                'status'    =>  true ,
            ]) ;

        } catch (\Exception $ex) {
            DB::rollBack() ;
            return response()->json([
                'status'    =>  'fail'
            ]) ;
        }   //  End Of Catch
    }  // The end of the Store function



    public function edit($id)   {
        try {
            if(! filter_var($id , FILTER_VALIDATE_INT))   {
                return redirect()->route('admin.users.index') ;
            }

            $user = User::Selection()->with(['roles' => function($q){
                $q->select('name') ;
            }])->where('id' , $id)->first() ;

            // If the person who is modifying the data of the other person is suber admin or has modification powers
            if( ($user && $user->count() > 0) && (($user['roles'][0]->name == 'admin' && Auth::user()->hasPermission('users-update')) || Auth::id() == $user->id )) {
                return view('admin.users.edit')->with('user' , $user) ;
            }   else    {
                return redirect()->route('admin.users.index') ;
            }   //  End Of Else

        } catch (\Exception $ex) {
            return redirect()->route('admin.users.index') ;
        }   //  End Of Catch
    }   // The end of the Edit function



    public function update(UserRequest $request , $id) {
        try {

            if(! filter_var($id , FILTER_VALIDATE_INT))   {
                return redirect()->route('admin.users.index') ;
            }

            // Fetch the data of the person to be modified
            $user = User::Selection()->where('id' , $id)->first() ;

            // If the person who is modifying the data of the other person is suber admin or has modification powers
            if( $user && (($user->hasRole('admin') && Auth::user()->hasPermission('users-update')) || Auth::id() == $user->id ) )  {

                $newData = [] ;     //  This is the variable in which we store the new person's data

                // This is the variable in which we store the new person's data
                if($request->has('photo'))  {
                    $newData['photo']   =   UploadPhoto($request->photo , PAGINATE_PHOTO_USER) ;
                }

                // If the user has updated the password
                if($request->has('password') && ! empty($request->password))   {
                    $newData['password']    =   HASH::make($request->password) ;
                }

                $newData['name'] = $request->name ;
                $newData['email']   = $request->email ;

                $user->update($newData) ;       // Update admin data

                $permission = collect([]) ;     //  This variable holds all user permissions

                if ($request->has('users')) {
                    $permission = $permission->merge($request->users) ;
                }

                if ($request->has('categories')) {
                    $permission = $permission->merge($request->categories) ;
                }

                if ($request->has('products')) {
                    $permission = $permission->merge($request->products) ;
                }

                if ($request->has('customers')) {
                    $permission = $permission->merge($request->customers) ;
                }

                if ($request->has('orders')) {
                    $permission = $permission->merge($request->orders) ;
                }

                if ($permission->count() > 0) {     //  If the user has permissions
                    $user->syncPermissions($permission);
                }

                return response()->json([
                    'status'    =>  true ,
                ]) ;
            }   else    {   // If the person modifying the other person's data is not an admin or does not have editing powers
                return response()->json([
                    'status'    =>  'fail' ,
                ]) ;
            }   //  End Of Else


        } catch (\Exception $ex) {
            return response()->json([
                'status'    =>  'fail' ,
            ]) ;
        }   //  End Of Catch
    }   // The end of the Update function


    public function delete(Request $request)    {
        try {
            $id = $request->id ;

            if(! filter_var($id , FILTER_VALIDATE_INT))   {
                return response()->json([
                    'status'    =>  false ,
                ]) ;
            }   //  End Of If

            // Fetch the data of the person we want to delete
            $user = User::select('id' , 'name' , 'photo')->where('id' , $id)->first() ;

            // If the person who deletes someone else is an administrator or has the rights to delete
            if( (Auth::user()->hasRole('super_admin') || ! $user->hasRole('super_admin')) && Auth::id() != $user->id && Auth::user()->hasPermission('users-delete'))    {
                /**This function deletes photos
                  *It was written in the General.php file located in the Helpers folder */

                DeletePhoto($user->photo) ;     //  delete picture

                $user->detachPermissions($user->permissions) ;   // Delete user permissions
                $user->delete() ;

                return response()->json([
                    'status'    =>  'true' ,
                    'id'        =>  $id ,
                ]) ;
            }   else    {
                return response()->json([
                    'status'    =>  false ,
                ]) ;
            }   //  End Of Else


        } catch (\Exception $ex) {
            return response()->json([
                'status'    =>  false ,
            ]) ;
        }   //  End Of Catch
    }   // The end of the Delete function

}
