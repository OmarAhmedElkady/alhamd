<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\category;
use App\Models\Language;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductController extends Controller
{
    public function index(Request $request) {

        // Bring all products by language
        $products = Product::Selection()->Abbr()->with(['category' =>  function($q){
            return $q->select('translation_of' , 'name')->LanguageSelect() ;
        }])->orderBy('name')->paginate(PAGINATE_PRODUCTS) ;

        return view('admin.products.index')->with('products' , $products) ;
    }  // The end of the index function




    public function search(Request $request)    {

        // If a person searches within a section
        if ($request->has('category_id') && filter_var($request->category_id , FILTER_VALIDATE_INT)) {

            if($request->has('search') && $request->search != '')  {
                $search = $request->search ;

                // Find the data entered by the user

                // Products that the user is searching for are prohibited within the section
                $products = Product::Selection()->Abbr()->where('name' , 'like' , '%' . $search . '%')->where('category_id' , $request->category_id)->with(['category' =>  function($q){
                    return $q->select('translation_of' , 'name')->LanguageSelect() ;
                }])->orderBy('name')->paginate(PAGINATE_PRODUCTS) ;

                if($products->count() > 0) {

                    return json_encode($products) ; //  Return data to Ajax using this function

                }   else    {  // If the product that the user searched for is not found in the database

                    return json_encode(['status' => false , 'search' => $search]) ;
                }  //  End Of Else

            }   else    { // If the user enters a null value

                // Bring all the products inside the section
                $products = Product::Selection()->Abbr()->where('category_id' , $request->category_id)->with(['category' =>  function($q){
                    return $q->select('translation_of' , 'name')->LanguageSelect() ;
                }])->orderBy('name')->paginate(PAGINATE_PRODUCTS) ;

                return json_encode($products) ;
            }  // End Of Else

        } else {    //  If a person searches inside all products

            if($request->has('search') && $request->search != '')  {
                $search = $request->search ;

                // Find the data entered by the user

                // Bring the products the user is looking for
                $products = Product::Selection()->Abbr()->where('name' , 'like' , '%' . $search . '%')->with(['category' =>  function($q){
                    return $q->select('translation_of' , 'name')->LanguageSelect() ;
                }])->orderBy('name')->paginate(PAGINATE_PRODUCTS) ;

                if($products->count() > 0) {

                    return json_encode($products) ; //  Return data to Ajax using this function

                }   else    {  // If the product that the user searched for is not found in the database

                    return json_encode(['status' => false , 'search' => $search]) ;
                }  //  End Of Else

            }   else    { // If the user enters a null value

                // Bring all products by language
                $products = Product::Selection()->Abbr()->with(['category' =>  function($q){
                    return $q->select('translation_of' , 'name')->LanguageSelect() ;
                }])->orderBy('name')->paginate(PAGINATE_PRODUCTS) ;

                return json_encode($products) ;
            }  // End Of Else
        }
    }    // The end of the Search function



    public function create()    {
        // Bring all sections
        $categories = category::select('translation_of' , 'name')->LanguageSelect()->get() ;
        $languages = Language::Selection()->get() ;     // fetch all language

        return view('admin.products.create')->with('categories' , $categories)->with('languages' , $languages) ;
    }   // The end of the Create function



    public function store(ProductRequest $request) {
        try {

            // If the user selects a section
            if ($request->has('category_id') && filter_var($request->category_id , FILTER_VALIDATE_INT)) {
                $category = category::select('id')->where('translation_of' , $request->category_id) ;  // Get this section from the database
            }

            // If the partition chosen by the user already exists in the database
            if(isset($category) && $category->count() > 0)  {

                // Fetch the name of the new product in all its languages and save it in the variable
                $product = collect( $request->product ) ;

                // Get the abbreviation of all languages in the database
                $languages = Language::select('abbr')->get() ;

                $productInAllLanguages = [] ; // This is the variable in which we store the product in all its languages

                // If the number of product names is the same as the number of languages
                if ( $languages->count() == $product->count() ) {

                    /* Now we want to fetch the last [id] in the database
                     * To be able to specify the value of the [translation_of] field
                    */
                    $firstProduct = Product::withTrashed()->first() ;
                    if ($firstProduct) {
                        $last_id = Product::orderBy('id', 'DESC')->withTrashed()->first()->id;
                    }   else    {
                        $last_id = 0 ;
                    }
                    ++$last_id ;

                    /**
                     * This function uploads the image and fetches the name of the image
                     * This function was written in the Helpers folder
                     */
                    $image = UploadPhoto($request->image,PAGINATE_IMAGE_PRODUCT) ;

                    foreach ($languages as $key => $lang) {
                        $productInAllLanguages [] = [
                            'abbr'              =>  $lang['abbr'] ,
                            'translation_of'    =>  $last_id ,
                            'category_id'       =>   $request->category_id,
                            'name'              =>  $product[$key]['name'] ,
                            'image'             =>  $image ,
                            'purchasing_price'  =>  $request->purchasing_price ,
                            'pharmacist_price'  =>  $request->pharmacist_price ,
                            'selling_price'     =>  $request->selling_price ,
                            'store'             =>  $request->store ,
                            'created_at'        =>  now() ,
                        ] ;
                    }  //  End Of Foreach

                }   else    {  // If the user does not enter the name of the product in all its languages
                    return response()->json([
                        'status'    =>  'false' ,
                    ]) ;
                }   // End Of Else

                Product::insert($productInAllLanguages) ;   // Add the product to the database

                return response()->json([
                    'status'    =>  true ,
                ]) ;
            }   //  End Of If
        } catch (\Exception $ex) {
            return response()->json([
                'status'    =>  'false' ,
            ]) ;
        }   //  End Of Catch
    }    // The end of the Store function


    public function show(Request $request , $category_id ) {


        if (isset($category_id) && filter_var($category_id , FILTER_VALIDATE_INT)) {

            $products = Product::Selection()->Abbr()->where('category_id' , $category_id)->with(['category' =>  function($q){
                return $q->select('id' , 'translation_of' , 'name')->LanguageSelect() ;
            }])->orderBy('name')->paginate(PAGINATE_PRODUCTS) ;

            if ($products && $products->count() > 0) {
                return view('admin.products.show')->with('products' , $products)->with('category_id' , $category_id) ;
            } else {
                return redirect()->route('admin.product.index') ;
            }
        }

    }  // The end of the index function



    public function edit($id)   {
        try {

            if(isset($id) && filter_var($id , FILTER_VALIDATE_INT)) {

                // Get the product from the database
                $product = Product::Selection()->where('translation_of' , $id)->get() ;

                // Fetch all sections according to the language of the site
                $categories = category::select('translation_of' , 'name')->LanguageSelect()->get() ;

                $languages = Language::select('abbr' , 'name')->get() ;  // Get shortcut of all languages


                if ($product->count() > 0 && $languages->count() > 0) {
                    return view('admin.products.edit')->with('product' , $product)->with('categories' , $categories)->with('id' , $id)->with('languages' , $languages) ;

                } else {
                    return redirect()->route('admin.product.index') ;
                }


            }   else    {   // If the user enters the top [ id ] with a value other than the number
                return redirect()->route('admin.product.index') ;
            }   // End Of Else

        } catch (\Exception $ex) {
            return redirect()->route('admin.product.index') ;
        }   //  End Of Catch
    }    // The end of the Edit function


    public function update(ProductRequest $request)    {

        try {
            if (isset($request->translation_of) && filter_var($request->translation_of , FILTER_VALIDATE_INT)) {

                $product = collect( $request->product ) ;  // Get the product name in all languages

                $languages = Language::select('abbr')->get() ;  //  Get all languages
                $productInAllLanguages = [] ;

                if ( $languages->count() == $product->count() ) {

                    // Get the old photo
                    $oldData = Product::select('image')->where('translation_of' , $request->translation_of)->first() ;

                    // If the user updates the image
                    if ($request->has('image')) {
                        DeletePhoto($oldData['image']) ;    // delete old photo
                        $image = UploadPhoto($request->image,PAGINATE_IMAGE_PRODUCT) ; // Add a new photo

                    }   else    {  // If the user does not update the image

                        $arr = explode('\\' , $oldData['image'] ) ;
                        $image = $arr[count($arr) - 1] ;   // Get the name of the old photo
                    }

                    foreach ($languages as $key => $lang) {

                        // Save the product in all its languages in this array
                        $productInAllLanguages [] = [
                            'abbr'              =>  $lang['abbr'] ,
                            'translation_of'    =>  $request->translation_of ,
                            'category_id'       =>   $request->category_id,
                            'name'              =>  $product[$key]['name'] ,
                            'image'             =>  $image ,
                            'purchasing_price'  =>  $request->purchasing_price ,
                            'pharmacist_price'  =>  $request->pharmacist_price ,
                            'selling_price'     =>  $request->selling_price ,
                            'store'             =>  $request->store ,
                            'created_at'        =>  now() ,
                        ] ;
                    }
                }   else    {
                    return response()->json([
                        'status'    =>  'false' ,
                    ]) ;
                }

                DB::beginTransaction() ;
                    // Delete the old product
                    // $oldData = Product::where('translation_of' , $request->translation_of)->delete() ;
                    Product::where('translation_of' , $request->translation_of)->forceDelete() ;

                    // Add the product again with its new data
                    Product::insert($productInAllLanguages) ;
                DB::commit() ;

                return response()->json([
                    'status'    =>  true ,
                ]) ;


            }   else    {
                return response()->json([
                    'status'    =>  'false' ,
                ]) ;
            }
        } catch (\Exception $ex) {
            DB::rollBack() ;
            return $ex ;
            return response()->json([
                'status'    =>  'false' ,
            ]) ;
        }   // End Catch
    }   // Edn Update



    public function delete(Request $request )    {

        try {
            if ( isset($request->translation_of) && filter_var($request->translation_of , FILTER_VALIDATE_INT) ) {

                // Bring the product you want to delete in all its languages
                $products = Product::select('id' , 'abbr' , 'image')->where('translation_of' , $request->translation_of)->get() ;

                // If this product actually exists
                if ($products->count() > 0) {

                    // Hide the columns inside the appends of the model because they come automatically when fetching any data from the product
                    $products->makeHidden(['pharmacistProfitRatio' , 'profitRateFromTheAudience' , 'totalProfitFromTheAudience' , 'totalProfitFromThePharmacist' , 'ProductPriceAccordingToCustomerType']) ;
                    $products = collect($products) ;

                    // We filter the product and return the product according to the language of the site
                    $ProductByLanguage = $products->filter(function($value , $key){
                        return $value['abbr'] == get_default_language() ;
                    }) ;
                    // We are fetching the product id so that we can delete this product
                    $key = collect($ProductByLanguage)->keys()->all() ;

                    // DeletePhoto($products[0]['image']) ;    // Delete the product image
                    // Delete the language abbreviation column and the image column in order to get the product id only so that we can delete the product by id only
                    $products->each(function ($item, $key) {
                        unset($item['abbr']) ;
                        unset($item['image']) ;

                        return $item['id'] ;
                    });

                    Product::destroy($products->toArray());   // delete product

                    return response()->json([
                        'status'    =>  'true' ,
                        'id'        =>  $ProductByLanguage[$key[0]]['id'] ,
                    ]) ;
                }
            }
        } catch (\Exception $ex) {
            return $ex ;
            return response()->json([
                'status'    =>  'false' ,
            ]) ;
        }   // End Catch
    }   // The end of the delete function
}
