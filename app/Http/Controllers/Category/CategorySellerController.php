<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Apicontroller;

class CategorySellerController extends Apicontroller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
        $sellers = $category->products()->with('seller')
                    ->get()
                    ->pluck('seller')
                    ->unique()
                    ->values();

        return $this->showAll($sellers);
    }

   
}
