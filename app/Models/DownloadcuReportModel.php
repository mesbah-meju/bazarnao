<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Search;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DownloadcuReportModel implements FromCollection, WithMapping, WithHeadings
{
    public function __construct($start_date, $end_date,$user_id,$warehouse)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->user_id = $user_id;
        $this->warehouse = $warehouse;
    }

    public function collection()
    {


        $warehouse = $this->warehouse;
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $sort_user_id = $this->user_id;

        if (empty($this->start_date))

            $this->start_date = $start_date;

        if (empty($this->end_date))
            $this->end_date = $end_date;
        $cust = array();
        $orders = array();

            $sql = "SELECT
            u.name,
            c.user_id,
            c.customer_id as customer_no,
            sum(cl.debit) as debit,
            sum(cl.credit) as credit,
            sum(cl.balance) as balance,
            (select sum(cll.debit-cll.credit) from customer_ledger as cll 
            where c.user_id=cll.customer_id and cll.date < '".$this->start_date."') as opening_balance
            FROM
            customers c
            LEFT JOIN customer_ledger cl ON c.user_id = cl.customer_id
            LEFT JOIN users u ON c.user_id = u.id
            LEFT JOIN orders on orders.id = cl.order_id";
            $sql.=" where 1=1 ";

            if (!empty($this->start_date) && !empty($this->end_date)) {
                $start_date = date('Y-m-d', strtotime($this->start_date));
                $end_date = date('Y-m-d', strtotime($this->end_date));
                $sql .= "	and (cl.date between '" . $start_date . "' and '" . $end_date . "' or cl.date is null) ";
            }
        
            if(!empty($sort_user_id)){
                $sql .= " AND c.staff_id = $sort_user_id";
            }

            if(!empty($warehouse)){

            $sql .= " AND orders.warehouse = $warehouse";
            }

            $sql.="	and (debit>0 or credit>0) 
            GROUP BY c.customer_id
            order by u.name asc";
            $customers = DB::select($sql);

        return collect($customers);
    }

    public function headings(): array
    {
        return [
            'Customer ID',
            'Customer Name',
            'Opening Balance',
            'Debit',
            'Credit',
            'Balance',
        ];
    }



    public function map($customers): array
    {



        return [
            $customers->customer_no,
            $customers->name,
            $customers->opening_balance,
            $customers->debit,
            $customers->credit,
            $customers->opening_balance+ $customers->debit - $customers->credit,
        ];
    }
}
