<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;
use App\Models\OpeningStock;
use Auth;
use App\Models\ProductsImport;
use App\Models\ProductsExport;
use PDF;
use Excel;

class ProductBulkUploadController extends Controller
{
    public function index()
    {
        if (Auth::user()->user_type == 'seller') {
            return view('frontend.user.seller.product_bulk_upload.index');
        }
        elseif (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return view('backend.product.bulk_upload.index');
        }
    }

    public function export(){
        return Excel::download(new ProductsExport, 'products.xlsx');
    }

    public function pdf_download_category()
    {
        $categories = Category::all();

        return PDF::loadView('backend.downloads.category',[
            'categories' => $categories,
        ], [], [])->download('category.pdf');
    }

    public function pdf_download_brand()
    {
        $brands = Brand::all();

        return PDF::loadView('backend.downloads.brand',[
            'brands' => $brands,
        ], [], [])->download('brands.pdf');
    }

    public function pdf_download_seller()
    {
        $users = User::where('user_type','seller')->get();

        return PDF::loadView('backend.downloads.user',[
            'users' => $users,
        ], [], [])->download('user.pdf');

    }

    public function bulk_upload(Request $request)
    {
        if($request->hasFile('bulk_file')){
            Excel::import(new ProductsImport, request()->file('bulk_file'));
        }
        flash(translate('Products imported successfully'))->success();
        return back();
    }


	function stock_upload(){
        return view('backend.product.bulk_upload.opening_stock_upload');
    }

    function stock_upload_action(Request $request){
        $warehouse_id=2;            
        $product_info=array();
        $startDate = '2024-10-01'; 
        $nextMFdate = date('Y-m-01 00:00:00', strtotime($startDate . ' + 1 month'));
        $nextMLdate = date('Y-m-t 23:59:59', strtotime($startDate . ' + 1 month'));

        OpeningStock::where('wearhouse_id', $warehouse_id)->whereBetween('created_at', array($nextMFdate, $nextMLdate))->delete();

        $upload=$request->file('bulk_file');
        $filePath=$upload->getRealPath();

        $file=fopen($filePath,'r');
        $header=fgetcsv($file);
        
        $escapedHeader=[];
        foreach($header as $key=>$value){
            $l_header=strtolower($value);
            $escapedItem=preg_replace('/[^a-z]/','',$l_header);
            array_push($escapedHeader,$escapedItem);
           
        }
        
        while($columns=fgetcsv($file)){
           
            if($columns[0]==''){
                continue;
            }
            foreach($columns as $key=>&$value){
            }

            $data=array_combine($escapedHeader,$columns);

            foreach($data as $key=>&$value){
                $value=($key=="stock")?(float)$value:$value;   
            }

            $stock=$data['stock'];

            $stock_amount=(float)$data['amount'];

            if( $stock_amount==0){
                continue;
            }

            $product_id=(int)$data['id'];

            $item = new OpeningStock();
            $item->product_id = $product_id;
            $item->wearhouse_id = $warehouse_id;
            $item->qty = $stock;
            if($stock>0){
                $item->price = $stock_amount/$stock;
            }else{
                $item->price = 0;
            }
            $item->amount = $stock_amount;
            $item->created_at = $nextMFdate;
            $item->updated_at = $nextMFdate;
            if($item->save()){
                dd($item,"prince");         
            }  
        }
        flash(translate('Products imported successfully'))->success();
        return back();
    }
}
