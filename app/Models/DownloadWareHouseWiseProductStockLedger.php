<?php

namespace App\Models;
use DB;
use App\Models\Order;
use App\Models\User;
use App\Models\Search;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DownloadWareHouseWiseProductStockLedger implements FromCollection, WithMapping, WithHeadings
{
   
    public function __construct($wearhouse_id,$product_id,$category_id,$from_date,$to_date){
        
        $this->wearhouse_id = $wearhouse_id;
        $this->product_id = $product_id;
        $this->category_id = $category_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;

        //dd($from_date);
    }

    public function collection()
    {

       
    $wearhouse = Warehouse::get();
    $sort_by = null;
    $pro_sort_by = null;
    $wearhouse_id = $wearhouse[0]->id;
    $category_id='';

   if(!empty($this->from_date) && !empty($this->to_date)){
    $from_date=date('Y-m-d',strtotime($this->from_date));
    $to_date=date('Y-m-d',strtotime($this->to_date));

    $from_sale_date = date('Y-m-d 00:00:00',strtotime($from_date));
    $to_sale_date= date('Y-m-d 23:59:59',strtotime($to_date));

    $from_string_time=strtotime($from_date);
    $to_string_time=strtotime($to_date);

    $month=date('Y-m',strtotime($from_date));
    $premonth = date('Y-m', strtotime($month." -1 month"));
  }else{
    $from_date=date('Y-m-01');
    $to_date=date('Y-m-t');


    $from_sale_date=date('Y-m-01');
    $to_sale_date=date('Y-m-01');


    $from_string_time=strtotime($from_date);
    $to_string_time=strtotime($to_date);

    $month=date('Y-m',strtotime($from_date));
    $premonth = date('Y-m', strtotime($month." -1 month"));

 }

 $products= Product::leftjoin('categories','products.category_id','=','categories.id');


 if (!empty($this->product_id)) {
    $pro_sort_by = $this->product_id;
    $products->where('products.id','=', $pro_sort_by);        
 }else{
    
        //$products = Product::limit(100);
   
 }


 $products->whereNull('products.parent_id');   



 if(!empty($this->category_id)){
    $category_id=$this->category_id;
    $products->where('category_id','=',$category_id);
 }else{

     if (!empty($this->wearhouse_id)) {
        
     }else{
         
         $products->limit(5);
     }
    
 }



 $products = $products->select('products.*','categories.name as category_name')->get();

 if (!empty($this->wearhouse_id)) {
    $sort_by = $this->wearhouse_id;            
} 



 foreach($products as $key=>$value){
    
    $child_products=array();
    $child_products= Product::where('parent_id','=',$value['id'])->get();
    
   

    $o_purchase_info=array();
    $o_sale_info=array();
    $o_damage_info=array();


    $transfer_receive_info=array();
    $transfer_info=array();

    $purchase_info=array();
    $sale_info=array();
    $damage_info=array();

    $opening_qty=0;
    $opening_amount=0;
    $purchase_qty=0;
    $purchase_amount=0;
    $sale_qty=0;
    $sale_amount=0;

    $child_sales_qty=0;
    $child_sales_amount=0;


    $damage_qty=0;
    $damage_amount=0;

   

    $pre_stock=array();     
    if(!empty($sort_by)){    
        $pre_stock= Product_stock_close::where('month', $premonth)
        ->where('wh_id',$sort_by)
        ->where('product_id',$value['id'])
        ->get(); 

        if(isset($pre_stock[0]->closing_stock_qty) && !empty($pre_stock[0]->closing_stock_qty)){
            $opening_qty=$pre_stock[0]->closing_stock_qty;
            $opening_amount=$pre_stock[0]->closing_stock_amount;
        } 
    }else{

        $op_sql="select sum(closing_stock_qty) as total_closing_stock,sum(closing_stock_amount) as total_closing_amount from product_stock_close where month='$premonth' and product_id=".$value['id'];
        $pre_stock=DB::select($op_sql);
        if(isset($pre_stock[0]->total_closing_stock) && !empty($pre_stock[0]->total_closing_amount)){
            $opening_qty=$pre_stock[0]->total_closing_stock;
            $opening_amount=$pre_stock[0]->total_closing_amount;
        }

    } 

    $products[$key]->opening_stock_qty=$opening_qty;
    $products[$key]->opening_stock_amount=$opening_amount;


    if(!empty($sort_by)){
        $p_sql="select sum(poi.qty) as total_purchase_qty,sum(poi.amount) as total_purchase_amount from purchase_details poi left join purchases po on poi.id=po.id where (po.date>='$from_date' and po.date<='$to_date') and po.status=2 and po.wearhouse_id=$sort_by and poi.product_id=".$value['id'];
    }else{
        $p_sql="select sum(poi.qty) as total_purchase_qty,sum(poi.amount) as total_purchase_amount from purchase_details poi left join purchases po on poi.id=po.id where (po.date>='$from_date' and po.date<='$to_date') and po.status=2 and poi.product_id=".$value['id'];
    }
    $purchase_info=DB::select($p_sql);

    if(!empty($sort_by)){
        //$s_sql="select sum(od.quantity) as total_sale_qty,sum(od.price) as total_sale_amount from order_details od left join orders o on od.order_id=o.id where (o.date>=$from_string_time and o.date<=$to_string_time) and o.warehouse=$sort_by and od.delivery_status IN('delivered','confirmed','on-delivery') and product_id=".$value['id'];
        $s_sql="select sum(od.quantity) as total_sale_qty,sum(od.price) as total_sale_amount from order_details od left join orders o on od.order_id=o.id where (o.created_at>='$from_sale_date' and o.created_at<='$to_sale_date') and o.warehouse=$sort_by and od.delivery_status='delivered' and od.product_id=".$value['id'];
    }else{
        //$s_sql="select sum(od.quantity) as total_sale_qty,sum(od.price) as total_sale_amount from order_details od left join orders o on od.order_id=o.id where (o.date>=$from_string_time and o.date<=$to_string_time) and od.delivery_status IN('delivered','confirmed','on-delivery') and product_id=".$value['id'];
        $s_sql="select sum(od.quantity) as total_sale_qty,sum(od.price) as total_sale_amount from order_details od left join orders o on od.order_id=o.id where (o.created_at>='$from_sale_date' and o.created_at<='$to_sale_date') and od.delivery_status='delivered' and od.product_id=".$value['id'];
    }
    $sale_info=DB::select($s_sql);


    //Child Product Sale info Start

    if(!empty($child_products)){

        foreach($child_products as $chk=>$chval){
            $child_sale_info=array();

            if(!empty($sort_by)){
                //$s_sql="select sum(od.quantity) as total_sale_qty,sum(od.price) as total_sale_amount from order_details od left join orders o on od.order_id=o.id where (o.date>=$from_string_time and o.date<=$to_string_time) and o.warehouse=$sort_by and od.delivery_status IN('delivered','confirmed','on-delivery') and product_id=".$value['id'];
                $child_s_sql="select sum(od.quantity) as total_sale_qty,sum(od.price) as total_sale_amount from order_details od left join orders o on od.order_id=o.id where (o.created_at>='$from_sale_date' and o.created_at<='$to_sale_date') and o.warehouse=$sort_by and od.delivery_status='delivered' and od.product_id=".$chval['id'];
            }else{
                //$s_sql="select sum(od.quantity) as total_sale_qty,sum(od.price) as total_sale_amount from order_details od left join orders o on od.order_id=o.id where (o.date>=$from_string_time and o.date<=$to_string_time) and od.delivery_status IN('delivered','confirmed','on-delivery') and product_id=".$value['id'];
                $child_s_sql="select sum(od.quantity) as total_sale_qty,sum(od.price) as total_sale_amount from order_details od left join orders o on od.order_id=o.id where (o.created_at>='$from_sale_date' and o.created_at<='$to_sale_date') and od.delivery_status='delivered' and od.product_id=".$chval['id'];
            }

            $child_sale_info=DB::select($child_s_sql);

            if(isset($child_sale_info[0]->total_sale_qty) && !empty($child_sale_info[0]->total_sale_qty)){
                $child_sales_qty=$child_sales_qty+($child_sale_info[0]->total_sale_qty*$chval['deduct_qty']);
                $child_sales_amount=$child_sales_amount+$child_sale_info[0]->total_sale_amount;
            }

        }

    }


    // Child Product Sale Info End

    if(!empty($sort_by)){
        $d_sql="select sum(qty) as total_damage_qty,sum(total_amount) as total_damage_amount from damages where (date>='$from_date' and date<='$to_date') and status='Approved' and wearhouse_id=$sort_by and product_id=".$value['id'];
    }else{
        $d_sql="select sum(qty) as total_damage_qty,sum(total_amount) as total_damage_amount from damages where (date>='$from_date' and date<='$to_date') and status='Approved' and product_id=".$value['id'];
    }
    $damage_info=DB::select($d_sql);



    if(!empty($sort_by)){
        $tr_r_sql="select sum(qty) as total_transfer_receive_qty,sum(amount) as total_transfer_receive_amount from transfers where (date>='$from_date' and date<='$to_date') and status='Approved' and to_wearhouse_id=$sort_by and product_id=".$value['id'];
        $transfer_receive_info=DB::select($tr_r_sql);
    }else{
        $tr_r_sql="select sum(qty) as total_transfer_receive_qty,sum(amount) as total_transfer_receive_amount from transfers where (date>='$from_date' and date<='$to_date') and status='Approved' and to_wearhouse_id>0 and product_id=".$value['id'];
        $transfer_receive_info=DB::select($tr_r_sql);
    }
    

    if(!empty($sort_by)){
        $tr_sql="select sum(qty) as total_transfer_qty,sum(amount) as total_transfer_amount from transfers where (date>='$from_date' and date<='$to_date') and status='Approved' and from_wearhouse_id=$sort_by and product_id=".$value['id'];
        $transfer_info=DB::select($tr_sql);
    }else{
        $tr_sql="select sum(qty) as total_transfer_qty,sum(amount) as total_transfer_amount from transfers where (date>='$from_date' and date<='$to_date') and status='Approved' and from_wearhouse_id>0 and product_id=".$value['id'];
        $transfer_info=DB::select($tr_sql);
    }

   
    if(isset($transfer_receive_info[0]->total_transfer_receive_qty) && !empty($transfer_receive_info[0]->total_transfer_receive_qty)){
        $products[$key]->transfer_receive_qty=$transfer_receive_qty=$transfer_receive_info[0]->total_transfer_receive_qty;
        $products[$key]->transfer_receive_amount=$transfer_receive_amount=$transfer_receive_info[0]->total_transfer_receive_amount;
    }else{
        $products[$key]->transfer_receive_qty=$transfer_receive_qty=0;
        $products[$key]->transfer_receive_amount=$transfer_receive_amount=0;
    }


    if(isset($transfer_info[0]->total_transfer_qty) && !empty($transfer_info[0]->total_transfer_qty)){
        $products[$key]->transfer_qty=$transfer_qty=$transfer_info[0]->total_transfer_qty;
        $products[$key]->transfer_amount=$transfer_amount=$transfer_info[0]->total_transfer_amount;
    }else{
        $products[$key]->transfer_qty=$transfer_qty=0;
        $products[$key]->transfer_amount=$transfer_amount=0;
    }



    if(isset($purchase_info[0]->total_purchase_qty) && !empty($purchase_info[0]->total_purchase_qty)){
        $products[$key]->purchase_qty=$purchase_qty=$purchase_info[0]->total_purchase_qty;
        $products[$key]->purchase_amount=$purchase_amount=$purchase_info[0]->total_purchase_amount;
    }else{
        $products[$key]->purchase_qty=$purchase_qty=0;
        $products[$key]->purchase_amount=$purchase_amount=0;
    }

    if(isset($sale_info[0]->total_sale_qty) && !empty($sale_info[0]->total_sale_qty)){
        $products[$key]->sale_qty=$sale_qty=$sale_info[0]->total_sale_qty+$child_sales_qty;
        $products[$key]->sale_amount=$sale_amount=$sale_info[0]->total_sale_amount+$child_sales_amount;
    }else{
        $products[$key]->sale_qty=$sale_qty=0+$child_sales_qty;
        $products[$key]->sale_amount=$sale_amount=0+$child_sales_amount;
    }

    if(isset($damage_info[0]->total_damage_qty) && !empty($damage_info[0]->total_damage_qty)){
        $products[$key]->damage_qty=$damage_qty=$damage_info[0]->total_damage_qty;
        $products[$key]->damage_amount=$damage_amount=$damage_info[0]->total_damage_amount;
    }else{
        $products[$key]->damage_qty=0;
        $products[$key]->damage_amount=0;
    }


    $products[$key]->closing_qty=($opening_qty+$transfer_receive_qty+$purchase_qty)-($sale_qty+$damage_qty+$transfer_qty);
    $products[$key]->closing_amount=($opening_amount+$transfer_receive_amount+$purchase_amount)-($sale_amount+$damage_amount+$transfer_amount);
}
       
            return collect($products);
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Category Name',
            'O.S.Qty',
            'O.S.Amount',
            'R.Qty',
            'R.Amount',
            'P.Qty',
            'P.Amount',
            'Sa.Qty',
            'Sa.Amount',
            'Damage.Qty',
            'Damage.Amount',
            'Trns. Qty',
            'Trns. Amount',
            'C.Qty',
            'C.Amount',
        ];
        
    }


    public function prepareRows($products){

        return $products;
      }

 
 
    public function map($products): array
    {
        $total = 0; 
        $total_opening_stock_qty = 0;
        $total_opening_stock_amount = 0;
        $total_purchase_qty =0;
        $total_purchase_amount = 0;
        $tota_sale_qty =0;
        $tota_sale_amount =0;
        $tota_damage_qty =0;
        $tota_damage_amount =0;
        $total_transfer_receive_qty=0;
        $total_transfer_receive_amount=0;
        $total_transfer_qty=0;
        $total_transfer_amount=0;
        $tota_closing_qty = 0;
        $tota_closing_amount = 0;

        $qty = 0;
        $total = $total+($qty*$products->purchase_price);
        $total_opening_stock_qty += $products->opening_stock_qty;

        if ($products != null){
            return [
                $products->name,
                $products->category_name,
                $total_opening_stock_qty,
                $products->opening_stock_amount,
                $products->transfer_receive_qty,
                $products->transfer_receive_amount,
                $products->purchase_qty,
                $products->purchase_amount,
                $products->sale_qty,
                $products->sale_amount,
                $products->damage_qty,
                $products->damage_amoun,
                $products->transfer_qty,
                $products->transfer_amount,
                $products->closing_qty,
                $products->closing_amount
            ];

        }
       
    }
}
