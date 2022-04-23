<?php

namespace App\Observers\Admin;

use App\Models\category;

class CategoryObserve
{
    public function created(category $category)
    {
        //
    }

    public function updated(category $category)
    {
        //
    }

    public function deleted(category $category)
    {
        // $category->products()->delete() ;
    }

    public function restored(category $category)
    {
        //
    }

    public function forceDeleted(category $category)
    {
        //
    }
}
