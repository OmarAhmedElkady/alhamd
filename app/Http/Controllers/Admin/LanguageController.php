<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LanguageRequest;
use App\Models\category;
use App\Models\customer;
use App\Models\Language;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LanguageController extends Controller
{

    public function index() {
        // Get all the languages of the site
        $languages = Language::Selection()->paginate(PAGINATE_LANGUAGE) ;
        return view('admin.languages.index')->with('languages' , $languages) ;
    }   // The end of the index function


    public function create()    {
        return view('admin.languages.create') ;
    }   // The end of the Create function

    public function store(LanguageRequest $request)  {
        try {

            $language = Language::create([      // Adding the new language to the database
                'abbr'  =>  $request->abbr ,
                'name'  =>  $request->name ,
            ]) ;

            return response()->json([
                'status'    =>  true ,
            ]) ;

        } catch (\Exception $ex) {
            return response()->json([
                'status'    =>  'false'
            ]) ;
        }   //  End Of Catch
    }   // The end of the Store function


    public function edit($id)  {
        try {

            if(filter_var($id ,FILTER_VALIDATE_INT))    {

                // Get the language you want to modify from the database
                $language = Language::Selection()->find($id) ;

                if($language)   {       //  If this language actually exists?
                    return view('admin.languages.edit')->with('language' , $language) ;

                }   else    {   //  If the language you want to modify is not present in the database
                    return redirect()->route('admin.language.index') ;
                }   //  End Of Else
            }   else    {      //   If the [id] value entered by the user is a non-numeric value
                return redirect()->route('admin.language.index') ;
            }   //  End Of Else


        } catch (\Exception $ex) {
            return redirect()->route('admin.language.index') ;
        }   //  End Of Catch
    }   // The end of the Edit function


    public function update(LanguageRequest $request)    {
        try {

            if (filter_var($request->id , FILTER_VALIDATE_INT)) {

                // Get the language you want to modify from the database
                $language = Language::Selection()->find($request->id) ;
                $oldLanguage = $language->abbr ;

                if($language)   {   //  If this language actually exists?
                    $language->update([     //      Update the language with the new data
                        'name'  =>  $request->name ,
                        'abbr'  =>  $request->abbr ,
                    ]) ;

                    // Language abbreviation update in all Categories
                    category::where('abbr' , $oldLanguage )->update(['abbr' => $request->abbr]) ;

                    // Language abbreviation update in all products
                    Product::where('abbr' , $oldLanguage )->update(['abbr' => $request->abbr]) ;

                    // Update the language abbreviation for clients
                    customer::where('abbr' , $oldLanguage )->update(['abbr' => $request->abbr]) ;

                    return response()->json([
                        'status'    =>  true ,
                    ]) ;
                }else    {  //   If this language actually not exists?
                    return redirect()->route('admin.language.index') ;
                }   //  End Of Else
            }   else    {   //  If [id] is a non-numeric value,
                return redirect()->route('admin.language.index') ;
            }   //  End Of Else

        } catch (\Exception $ex) {
            return redirect()->route('admin.language.index') ;
        }   //  End Of Catch
    }   // The end of the Update function


    public function delete(Request $request)    {
        try {
            if (isset($request->id) && filter_var($request->id , FILTER_VALIDATE_INT )) {
                // Get the language you want to delete from the database
                $language = Language::where('id' , $request->id)->first() ;

                // If the language you want to delete is not the default language for the site
                if ($language->abbr != get_default_language()) {

                    DB::beginTransaction() ;

                        // Delete all sections in the language you want to delete
                        $categories = category::select('id')->where('abbr' , $language->abbr)->get() ;

                        category::destroy($categories->toArray());


                        // Fetch all products in the language you want to delete
                        $products = Product::select('id')->where('abbr' , $language->abbr)->get() ;

                        $products->makeHidden(['pharmacistProfitRatio' , 'profitRateFromTheAudience' , 'totalProfitFromTheAudience' , 'totalProfitFromThePharmacist' , 'ProductPriceAccordingToCustomerType']) ;

                        // Delete all products in the language you want to delete
                        Product::destroy($products->toArray());

                        // Get the [id] of the client you want to delete
                        $customers = customer::select('id')->where('abbr' , $language->abbr)->get() ;

                        customer::destroy($customers->toArray());   // delete customer

                        // delete language
                        $language->delete() ;

                    DB::commit() ;

                    return response()->json([
                        'status'    =>  'true' ,
                        'id'        =>  $request->id ,
                    ]) ;
                }   //  End Of If
            }   //  End Of If
        } catch (\Exception $ex) {
            DB::rollBack() ;
            return response()->json([
                'status'    =>  'false'
            ]) ;
        }   //   End Of Catch
    }   // The end of the Delete function




    public function selectLanguage($locale)    {
        if($locale != 'ar' && $locale != 'en')  {
            $locale = 'ar' ;
        }

        app()->setlocale($locale) ;
        session()->put('locale' , $locale) ;
        return redirect()->back() ;
    }   //  // The end of the Select Language function
}
