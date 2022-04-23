<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\category;
use App\Models\Language;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(Request $request) {

        if($request->has('search')) {

            if($request->search != '' )  {
                $search = $request->search ;

                // Find the data entered by the user
                $categories = category::Selection()->LanguageSelect()->where('name' , 'like' , '%' . $search . '%')->with(['products' => function($q){
                    return $q->select('id' , 'category_id')->Abbr() ;
                }])->orderBy('name')->paginate(PAGINATE_CATEGORIES) ;

                if($categories->count() > 0) {

                    return json_encode($categories) ; // Return data to Ajax using this function

                }   else    {  // If the product that the user searched for is not found in the database
                    return json_encode(['status' => false , 'search' => $search]) ;
                }
            }   else    { // If the value entered by the user in the search field is null
                //  Fetch all sections according to the language of the site
                $categories = category::Selection()->LanguageSelect()->with(['products' => function($q){
                    return $q->select('id' , 'category_id')->Abbr() ;
                }])->orderBy('name')->paginate(PAGINATE_CATEGORIES) ;

                return json_encode($categories) ;

            }   //  End Of Else

        }   else    { //  If the user enters the page through the linke and not the search field

            // $categories = category::Selection()->LanguageSelect()->orderBy('name')->paginate(PAGINATE_CATEGORIES) ;

            // Fetch all sections according to the language of the site
            $categories = category::Selection()->LanguageSelect()->with(['products' => function($q){
                return $q->select('id' , 'category_id')->Abbr() ;
            }])->orderBy('name')->paginate(PAGINATE_CATEGORIES) ;

            return view('admin.categories.index')->with('categories' , $categories) ;
        }   //  End Of Else

    }       // The end of the index function


    public function create()    {
        // Get all the languages of the site
        $languages = Language::Selection()->get() ;
        return view('admin.categories.create')->with('languages' , $languages) ;
    }   // The end of the Create function



    public function store(CategoryRequest $request) {
        try {
            $category = collect( $request->category ) ;

            // Get shortcut of all languages
            $languages = Language::select('abbr')->get() ;

            // This is the variable in which we save the new section in all its languages
            $productInAllLanguages = [] ;

            // If the number of names of the new section is equal to the number of languages on the site
            if ( $languages->count() == $category->count() ) {

                /* Now we want to fetch the last [id] in the database
                *  To be able to specify the value of the [translation_of] field
                */
                $firstCategory = category::withTrashed()->first() ;
                if ($firstCategory) {
                    $last_id = category::orderBy('id', 'DESC')->withTrashed()->first()->id;
                }   else    {
                    $last_id = 0 ;
                }
                ++$last_id ;

                foreach ($languages as $key => $lang) {
                    if ($lang->abbr == $category[$key] ['abbr']) {
                        $productInAllLanguages [] = [
                            'abbr'              =>  $category[$key]['abbr'] ,
                            'translation_of'    =>  $last_id ,
                            'name'              =>  $category[$key]['name'] ,
                        ] ;
                    }   else    {
                        return response()->json([
                            'status'    =>  'false' ,
                        ]) ;
                    }   //  End Of Else
                }     // End Of Foreach
            }   else    {   //  If the user does not enter the new section in all its languages
                return response()->json([
                    'status'    =>  'false' ,
                ]) ;
            }   //  End Of Else

            // Add the new product in all its languages into the database
            category::insert($productInAllLanguages) ;

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

            if(isset($id) && filter_var($id , FILTER_VALIDATE_INT)) {

                // Get the partition to be modified from the database
                $categories = category::Selection()->where('translation_of' , $id)->get() ;

                // Get shortcut of all languages
                $languages = Language::select('abbr' , 'name')->get() ;


                // If the section already exists
                if ($categories->count() > 0 && $languages->count() > 0) {
                    return view('admin.categories.edit')->with('categories' , $categories)->with('id' , $id)->with('languages' , $languages) ;

                } else {
                    return redirect()->route('admin.category.index') ;
                }   // End Of Else


            }   else    {   // If the user enters the value [ id ] is given a non-numeric value
                return redirect()->route('admin.category.index') ;
            }   // End Of Else

        } catch (\Exception $ex) {
            return redirect()->route('admin.category.index') ;
        }   // End Of Catch
    }   // The end of the Edit function


    public function update(CategoryRequest $request)    {

        try {
            if (isset($request->translation_of) && filter_var($request->translation_of , FILTER_VALIDATE_INT)) {

                $requestCategory = collect($request->category);

                $languages = Language::select('abbr')->get() ;  // Get shortcut of all languages

                // If the number of names of the new section is equal to the number of languages of the site
                if($languages->count() == $requestCategory->count() )    {

                    // If the number of names of the new section is equal to the number of languages of the site
                    $newData = [] ;

                    foreach ($languages as $key => $value) {
                        $newData[] = [
                            'name'  =>  $requestCategory[$key]['name'] ,
                            'translation_of'    =>  $request->translation_of ,
                            'abbr'  =>  $value->abbr
                        ] ;
                    }

                    DB::beginTransaction() ;
                        // Delete the old section
                        // category::where('translation_of' , $request->translation_of)->delete() ;

                        category::where('translation_of' , $request->translation_of)->forceDelete() ;
                        category::insert($newData) ;    // Add a new section
                    DB::commit() ;

                    return response()->json([
                        'status'    =>  true ,
                    ]) ;

                }   else    {       //  If the user does not enter the new section in all its languages
                    return response()->json([
                        'status'    =>  'false' ,
                    ]) ;
                }       //  End Of Else
            }   else    {   //  If the user gives the value [ id ] a non-numeric value
                return response()->json([
                    'status'    =>  'false' ,
                ]) ;
            }   //  End Of Else
        } catch (\Exception $ex) {
            DB::rollBack() ;
            return response()->json([
                'status'    =>  'false' ,
            ]) ;
        }   //  End Of Catch
    }   // The end of the Update function



    public function delete(Request $request )    {

        try {
            if ( isset($request->translation_of) && filter_var($request->translation_of , FILTER_VALIDATE_INT) ) {

                // Get the partition from the database
                $categories = category::select('id' , 'abbr' , 'translation_of' )->where('translation_of' , $request->translation_of)->get() ;

                if ($categories->count() > 0) { // If the section already exists

                    DB::beginTransaction() ;

                        $categories[0]->products()->delete() ;  // Delete all products in the section

                        $categories = collect($categories) ;

                        // Filter the section in all its languages so that we can retrieve the section according to the language of the site
                        $categoryByLanguage = $categories->filter(function($value , $key){
                            return $value['abbr'] == get_default_language() ;
                        }) ;
                        $key = collect($categoryByLanguage)->keys()->all() ;

                        /**
                         * In order to be able to delete a partition, only this array must have the value [ id ]
                        *  So we delete all the other elements
                        */
                        $categories->each(function ($item, $key) {
                            unset($item['abbr']) ;
                            unset($item['translation_of']) ;
                            return $item['id'] ;
                        });


                        // Delete the section in all its languages
                        category::destroy($categories->toArray());

                    DB::commit() ;

                    return response()->json([
                        'status'    =>  'true' ,
                        'id'        =>  $categoryByLanguage[$key[0]]['id'] ,
                    ]) ;
                }   //  End Of If
            }   //  End Of If
        } catch (\Exception $ex) {
            DB::rollBack() ;
            return response()->json([
                'status'    =>  'false' ,
            ]) ;
        }   // End Of Catch
    }   // The end of the Edit function
}
