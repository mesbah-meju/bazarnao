<?php
namespace App\Models;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DownloadDailyActivitiesReportModel implements FromCollection, WithMapping, WithHeadings
{
    public function __construct($from_date=null, $to_date=null,$sort_search=null,$user_name=null,$order_status=null)
    {
        $this->from_date =  $from_date;
        $this->to_date =  $to_date;
        $this->sort_search = $sort_search;
        $this->user_name = $user_name;  
        $this->order_status = $order_status;  
        //dd($wearehouse_id);
    }

    public function collection()
    {
        $from_date = date('Y-m-01');
        $to_date = date('Y-m-t');
        $sort_search = null;
        $user_name = null;
        $order_status = null;
        
        if (!empty($this->from_date) && !empty($this->to_date)) {
            $from_date = date('Y-m-d 00:00:00', strtotime($this->from_date));
            $to_date = date('Y-m-d 23:59:59', strtotime($this->to_date));
        }

        if (!empty($sort_search)) {
            $order_status_logs = OrderStatusLog::whereBetween('order_status_logs.created_at', [$from_date, $to_date])
                ->where('order_id', $sort_search);
        } else {
            $order_status_logs = OrderStatusLog::whereBetween('order_status_logs.created_at', [$from_date, $to_date]);
        }
    
        $order_status_logs = DB::table('order_status_logs')
            ->join('users', 'order_status_logs.user_id', '=', 'users.id')
            ->select('order_status_logs.*', 'users.id as user_id', 'users.name as user_name');
    
        if (!empty($user_name)) {
            $order_status_logs->where('users.id', $user_name);
        }
    
        if (!empty($order_status)) {
            $order_status_logs->where('order_status_logs.order_status', $order_status);
        }
    
        $order_status_logs->whereBetween('order_status_logs.created_at', [$from_date, $to_date]);
    
        $order_status_logs = $order_status_logs->get();

        $from_date = date('Y-m-d', strtotime($from_date));
        $to_date = date('Y-m-d', strtotime($to_date));
    
        return collect($order_status_logs);
    }
    public function headings(): array
    {
        return [
            'User Name',
            'Date',
            'Order Id',
            'Status',
            'Remarks',

        ];
    }

    public function map($order_status_logs): array
    {
        return [
            $order_status_logs->user_name,
            $order_status_logs->created_at,
            $order_status_logs->order_code,
            $order_status_logs->order_status,
            $order_status_logs->remarks,

        ];
    }
}
