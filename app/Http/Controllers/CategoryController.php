<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\CategoryTranslation;
use App\Utility\CategoryUtility;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search =null;
        $categories = Category::orderBy('name', 'asc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $categories = $categories->where('name', 'like', '%'.$sort_search.'%');
        }
        $categories = $categories->paginate(15);
        return view('backend.product.categories.index', compact('categories', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::where('parent_id', 0)
            ->with('childrenCategories')
            ->get();

        return view('backend.product.categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $category = new Category;
        $category->name = $request->name;
        $category->banner = $request->banner;
        $category->icon = $request->icon;
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;

        if ($request->parent_id != "0") {
            $category->parent_id = $request->parent_id;

            $parent = Category::find($request->parent_id);
            $category->level = $parent->level + 1 ;
        }

        if ($request->slug != null) {
            $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        }
        else {
            $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)).'-'.Str::random(5);
        }
        if ($request->commision_rate != null) {
            $category->commision_rate = $request->commision_rate;
        }

        $category->save();

        $category_translation = CategoryTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'category_id' => $category->id]);
        $category_translation->name = $request->name;
        $category_translation->save();

        flash(translate('Category has been inserted successfully'))->success();
        return redirect()->route('categories.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $lang = $request->lang;
        $category = Category::findOrFail($id);
        $categories = Category::where('parent_id', 0)
            ->with('childrenCategories')
            ->whereNotIn('id', CategoryUtility::children_ids($category->id, true))->where('id', '!=' , $category->id)
            ->orderBy('name','asc')
            ->get();

        return view('backend.product.categories.edit', compact('category', 'categories', 'lang'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        if($request->lang == env("DEFAULT_LANGUAGE")){
            $category->name = $request->name;
        }
        
        $category->banner = $request->banner;
        $category->icon = $request->icon;
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;

        $previous_level = $category->level;

        if ($request->parent_id != "0") {
            $category->parent_id = $request->parent_id;

            $parent = Category::find($request->parent_id);
            $category->level = $parent->level + 1 ;
        }
        else{
            $category->parent_id = 0;
            $category->level = 0;
        }

        if($category->level > $previous_level){
            CategoryUtility::move_level_down($category->id);
        }
        elseif ($category->level < $previous_level) {
            CategoryUtility::move_level_up($category->id);
        }

        if ($request->slug != null) {
            $category->slug = strtolower($request->slug);
        }
        else {
            $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)).'-'.Str::random(5);
        }


        if ($request->commision_rate != null) {
            $category->commision_rate = $request->commision_rate;
        }

        $category->save();

        $category_translation = CategoryTranslation::firstOrNew(['lang' => $request->lang, 'category_id' => $category->id]);
        $category_translation->name = $request->name;
        $category_translation->save();

        flash(translate('Category has been updated successfully'))->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Category Translations Delete
        foreach ($category->category_translations as $key => $category_translation) {
            $category_translation->delete();
        }

        foreach (Product::where('category_id', $category->id)->get() as $product) {
            $product->category_id = null;
            $product->save();
        }

        CategoryUtility::delete_category($id);

        flash(translate('Category has been deleted successfully'))->success();
        return redirect()->route('categories.index');
    }

    public function featured_category_wise(Request $request){
        $category = Category::findOrFail($request->id);
        $category->category_wise = $request->status;
        if($category->save()){
            return 1;
        }
        return 0;
    }

    public function updateFeatured(Request $request)
    {
        $category = Category::findOrFail($request->id);
        $category->featured = $request->status;
        if($category->save()){
            return 1;
        }
        return 0;
    }

    public function menu_update(Request $request)
    {
        $str1 = '';
        $str1 .= '<ul class="list-unstyled categories no-scrollbar py-2 mb-0 text-left" style="margin-top:60px">';
        $str1 .= '<a href="{{ URL::to(\'offers\') }}" style="font-size: 15px;color: #AE3C86;font-weight: bold;"><span class="cat-name">{{ translate(\'Offers\') }}</span><span class="offerCount">{{ offerCount() }}</span></a>';
        $str1 .= '<div class="aiz-side-nav-wrap">';
        $str1 .= '<h4 class="h5 fs-16 mb-1 fw-600 ml-3">All Category</h4>';
        $str1 .= '<ul class="aiz-side-nav-list" id="main-menu" data-toggle="aiz-side-menu">';
        $str1 .= '<ul class="list-unstyled categories no-scrollbar py-2 mb-0 text-left">';
        foreach (Category::where('level', 0)->where('parent_id', 0)->orderBy('sl')->get()->take(30) as $key => $category) {
            if (count(CategoryUtility::get_immediate_children_ids($category->id)) > 0) {
                $str1 .= '<button onclick="getSubCategory(' . $category->id . ')" class="dropdown-btn">';
                $str1 .= '<img class="cat-image lazyload" src="' . static_asset('assets/img/placeholder.jpg') . '" data-src="' . uploaded_asset($category->icon) . '" width="20" alt="' . $category->getTranslation('name') . '" onerror="this.onerror=null;this.src=\''.static_asset('assets/img/placeholder.jpg').'\';">';
                $str1 .= '<span class="cat-name" id="cat_name_' . $category->id . '">' . $category->getTranslation('name') . '</span> <i class="las la-chevron-right pull-right"></i></button>';
                $str1 .= ' <div class="dropdown-container"><span class="maincategory_' . $category->id . '" style="display:none">';
                $str1 .= '<img class="cat-image lazyload" src="' . static_asset('assets/img/placeholder.jpg') . '" data-src="' . uploaded_asset($category->icon) . '" width="20" alt="' . $category->getTranslation('name') . '" onerror="this.onerror=null;this.src=\''.static_asset('assets/img/placeholder.jpg').'\';"></span>';
                $sub_cats = CategoryUtility::get_immediate_children($category->id, false, true);
                foreach (CategoryUtility::get_immediate_children_ids($category->id) as $key => $first_level_id) {
                    if (count(CategoryUtility::get_immediate_children_ids($first_level_id)) > 0) {
                        $str1 .= '<span class="subcategory_' . $category->id . '" style="display:none"><button onclick="getSubCategory(' . $first_level_id . ')" style="display:block; " class="button" >';
                        $str1 .= '<img class="cat-image lazyload" src="' . static_asset('assets/img/placeholder.jpg') . '" data-src="' . uploaded_asset($category->icon) . '" width="20" alt="' . $category->getTranslation('name') . '" onerror="this.onerror=null;this.src=\''.static_asset('assets/img/placeholder.jpg').'\';">';
                        $str1 .= '<span class="cat-name">' . Category::find($first_level_id)->getTranslation('name') . '</span> </button></span>';
                    } else {
                        $str1 .= '<span class="subcategory_' . $category->id . '" style="display:none"><a href="' . route('products.category', Category::find($first_level_id)->slug) . '">';
                        $str1 .= '<img class="cat-image lazyload" src="' . static_asset('assets/img/placeholder.jpg') . '" data-src="' . uploaded_asset($category->icon) . '" width="20" alt="' . $category->getTranslation('name') . '" onerror="this.onerror=null;this.src=\''.static_asset('assets/img/placeholder.jpg').'\';">';
                        $str1 .= ' <span class="cat-name">' . Category::find($first_level_id)->getTranslation('name') . '</span></a></span>';
                    }
                    if (count(CategoryUtility::get_immediate_children_ids($first_level_id)) > 0) {
                        $str1 .= '<button onclick="getSubCategory(' . $first_level_id . ')" class="dropdown-btn">';
                        $str1 .= '<img class="cat-image lazyload" src="' . static_asset('assets/img/placeholder.jpg') . '" data-src="' . uploaded_asset($category->icon) . '" width="20" alt="' . $category->getTranslation('name') . '" onerror="this.onerror=null;this.src=\''.static_asset('assets/img/placeholder.jpg').'\';">';
                        $str1 .= '<span class="cat-name" id="cat_name_' . $first_level_id . '">' . Category::find($first_level_id)->getTranslation('name') . '</span> <i class="las la-chevron-right pull-right"></i></button>';
                        foreach (CategoryUtility::get_immediate_children_ids($first_level_id) as $key => $second_level_id) {
                            $str1 .= '<span class="subcategory_' . $second_level_id . '" style="display:none"><a href="' . route('products.category', Category::find($second_level_id)->slug) . '">';
                            $str1 .= '<img class="cat-image lazyload" src="' . static_asset('assets/img/placeholder.jpg') . '" data-src="' . uploaded_asset($category->icon) . '" width="20" alt="' . $category->getTranslation('name') . '" onerror="this.onerror=null;this.src=\''.static_asset('assets/img/placeholder.jpg').'\';">';
                            $str1 .= ' <span class="cat-name">' . Category::find($second_level_id)->getTranslation('name') . '</span></a></span>';
                        }
                    }
                }
            }
        }
        $str1 .= ' </ul>';
        $str1 .= ' </ul>';
        $str1 .= '</div>';
        $str1 .= '</div>';
        $str1 .= '<div class="fixed-bottom d-xl-none bg-white border-top d-flex justify-content-between px-2" style="box-shadow: 0 -5px 10px rgb(0 0 0 / 10%);">';
        $str1 .= '<a class="btn btn-sm p-2 d-flex align-items-center" href="javascript:void(0)"><i class="las la-sign-out-alt fs-18 mr-2"></i><span></span>';
        $str1 .= '</a>';
        $str1 .= '<button class="btn btn-sm p-2 " data-toggle="class-toggle" data-backdrop="static"data-target=".aiz-mobile-cat-side-nav" data-same=".mobile-side-nav-thumb"><i class="las la-times la-2x"></i>';
        $str1 .= '</button>';
        $str1 .= '</div>';
        $str1 .= '</div>';
        file_put_contents('category_menu_static.php', $str1 . PHP_EOL);
        file_put_contents('category_mobile_menu_static.php', $str1 . PHP_EOL);
        flash(translate('Menu Updated successfully'))->success();
        return back();
    }

}
