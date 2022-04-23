<?php


use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Config ;


function UploadPhoto ( $photo , $folder )   {
    Image::make($photo)->resize(300, null, function ($constraint) {
        $constraint->aspectRatio();
    })->save( $folder . $photo->hashName());

    return $photo = $photo->hashName() ;
}


function DeletePhoto ($photo)   {
    $photo = strstr($photo , 'assets' ) ;
    $photo = base_path() . '\\' . $photo ;
    unlink($photo) ;
}


function get_default_language ()    {
    return config::get('app.locale') ;
}
