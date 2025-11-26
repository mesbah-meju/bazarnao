<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\CategoryCollection;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Product;
use App\Http\Resources\V2\ProductMiniCollection;


class CategoryController extends Controller
{

    public function index($parent_id = 0)
    {
        if(request()->has('parent_id') && is_numeric (request()->get('parent_id'))){
          $parent_id = request()->get('parent_id');
        }
        return new CategoryCollection(Category::where('parent_id', $parent_id)->get());
    }

    public function featured()
    {
        return new CategoryCollection(Category::where('featured', 1)->get());
    }

    public function home()
    {
        $homepageCategories = BusinessSetting::where('type', 'home_categories')->first();
        $homepageCategories = json_decode($homepageCategories->value);
        return new CategoryCollection(Category::whereIn('id', $homepageCategories)->get());
    }

    public function top()
    {
        $homepageCategories = BusinessSetting::where('type', 'home_categories')->first();
        $homepageCategories = json_decode($homepageCategories->value);
        return new CategoryCollection(Category::whereIn('id', $homepageCategories)->limit(30)->get());
    }

    public function categories_top()
    {
        $top_categories = Category::where('categories.category_wise', 1)
                        ->select('categories.id','categories.name')->get();
    
        foreach ($top_categories as $category) {
            $products = new ProductMiniCollection(Product::where('category_id',$category->id)->limit(15)->latest()->get());
           
            $category->products = $products;
        }
        return $top_categories;
    }
}
