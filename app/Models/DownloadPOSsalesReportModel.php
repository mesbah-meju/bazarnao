<?php

namespace App\Models;
use DB;
use App\Models\Order;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Search;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DownloadPOSsalesReportModel implements FromCollection, WithMapping, WithHeadings
{
    public function __construct($start_date,$end_date,$search,$date,$user_id,$warehouse){        
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->date = $date;
        $this->search = $search;
        $this->user_id = $user_id;
        $this->warehouse = $warehouse;
    }

    public function collection()
    {
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $sort_search =  $this->search;
        $sort_user_id = $this->user_id;
        $wearhouse = $this->warehouse;

        $orders = Order::select('orders.*')
            ->where('orders.order_from','POS')
            ->orderBy('orders.date', 'ASC');

   
        if(!empty($sort_search)){
            $orders = $orders->where('orders.code', 'like', '%'.$sort_search.'%');
        }

        if (!empty($this->start_date) && !empty($this->end_date)) {
            $start_date = date('Y-m-d 00:00:00',strtotime($this->start_date));
            $end_date = date('Y-m-d 23:59:59',strtotime($this->end_date));
        }

        if(!empty($sort_user_id)){
            $orders = $orders->select('orders.*', 'customers.staff_id')
                ->join('customers', 'customers.user_id', '=', 'orders.user_id')
                ->where('customers.staff_id', $sort_user_id)
                ->whereBetween('orders.created_at', [$start_date, $end_date]);eBetween('orders.created_at', [$start_date, $end_date]);            
        }else {
            $orders = $orders->whereBetween('orders.created_at', [$start_date, $end_date]);
        }
        
        if(!empty($wearhouse)){
            $orders = $orders->whereBetween('orders.created_at', [$start_date, $end_date])
                    ->where('orders.warehouse', $wearhouse);
        }
        else{
            $orders = $orders->whereBetween('orders.created_at', [$start_date, $end_date]);     
        }

        $orders = $orders->get();
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));
        return collect($orders);
    }

    public function headings(): array
    {
        return [
            'Date',
            'Order Code',
            'Warehouse',
            'Customer ID',
            'Customer Name',
            'Customer Type',
            'Phone',
            'Area',
            'Amount',
            'Paid',
            'Due',
        ];
    }

 
 
    public function map($orders): array
    {
        
        $delivery_status = $orders->orderDetails->first();
        $error = 0;
        if(empty($delivery_status)){
        $error = 1;
        return [];
        }else{
        if($delivery_status->delivery_status=='cancel' || $delivery_status->delivery_status=='pending'){
        $error = 1;
        return [];
        }
        }

        $total = 0;
        $totalpaid = 0;
        $totaldue = 0;
        $i = 0;
        $executive = '';

        if(!empty(\App\Customer::where('user_id', $orders->user_id)->first())){
            $customer_id = \App\Customer::where('user_id', $orders->user_id)->first();
            $payment_details = json_decode($orders->payment_details);
    
        }else{
            $customer_id = '';
            $payment_details = json_decode($orders->payment_details);
           }
    
           if(!empty($customer_id)){
            $customer_type = $customer_id->customer_type;

             if(!empty($customer_id->staff_id)){
                $executive = User::where('id',$customer_id->staff_id)
                ->select('name')->first();
                // dd($executive->name);
               }else{
                $executive = 'No Define';
               }

           }else{
            $customer_type = "Guest";
           }


        if(!empty($payment_details) && !empty($payment_details->status) && ($payment_details->status=='VALID')){
        $totalpaid+=$payment_details->amount;
        $paid =$payment_details->amount;
        $totaldue+=($orders->grand_total-$paid);
        $due = $orders->grand_total-$paid;
        }else{
        $totaldue+=$orders->grand_total;
        $due = $orders->grand_total;
        $paid = 0;
        }
        $total+=$orders->grand_total;
        
        if( $error == 0)
        $i++;
        $warehouse = Warehouse::where('id',$orders->warehouse)->first();
        

        if ($orders->user != null){

            return [
                date('d-m-Y',$orders->date),
                $orders->code,
                $warehouse->name,
                $customer_id->customer_id,
                $orders->user->name,
                $customer_type,
                $orders->user->phone,
                get_customer_area_name($orders->user->id)[0],
                $orders->grand_total,
                $paid ,
                $due,
            ];

        }else{

            return [
            
                date('d-m-Y',$orders->date),
                $orders->code,
                $warehouse->name,
                $orders->guest_id,
                json_decode($orders->shipping_address)->name,
                $customer_type,
                json_decode($orders->shipping_address)->phone,  
                json_decode($orders->shipping_address)->area, 
                $orders->grand_total,
                $paid,
                $due,
            ];

        }       
    }
}
