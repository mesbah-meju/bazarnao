<?php

namespace App\Models;

use App\Models\Customer;
use App\Models\Area;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomersExport implements FromCollection, WithMapping, WithHeadings
{
    protected $start_date;
    protected $end_date;

    public function __construct($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function collection()
    {
        $start_date = $this->start_date ? date('Y-m-d', strtotime($this->start_date)) : date('Y-m-01');
        $end_date = $this->end_date ? date('Y-m-d 23:59:59', strtotime($this->end_date)) : date('Y-m-t 23:59:59');

        $total_customers = Customer::orderBy('created_at', 'desc');

        if ($this->start_date && $this->end_date) {
            $total_customers->whereBetween('customers.created_at', [$start_date, $end_date]);
        }

        return $total_customers->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Customer Type',
            'Customer Id',
            "Staff Name",
            'Total Order',
            'Email',
			'Phone',
            'Area',
            'Created',
            'balance',
            'Credit Limit',
        ];
    }

    /**
    * @var Product $product
    */

    public function map($customer): array
    {
        if ($customer->user != null) {
                            // $customer->staff_id->staff->user_id->user->name,
                $area_code = Area::where('areas.code','=',$customer->area_code)->value('areas.name');
                $Seller_name = Staff::join('users','users.id','=','staff.user_id')
                                ->where('staff.user_id','=',$customer->staff_id)
                                ->value('users.name');
            return [
                $customer->user->name ?? '', 
                $customer->customer_type ?? '', 
                $customer->customer_id ?? '',
                $Seller_name ?? '', 
                count($customer->orders()) ?? 0,
                $customer->user->email ?? '', 
                $customer->user->phone ?? '', 
                $area_code ?? '', 
                date('d-m-Y h:i:A', strtotime($customer->user->created_at)) ?? '', 
                single_price($customer->user->balance) ?? '', 
                single_price($customer->credit_limit) ?? '', 
            ];
        } else {
            return [];
        }
    }
    
}
