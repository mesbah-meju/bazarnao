<?php

namespace App\Models;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DownloadSupplierLedgerReportModel implements FromCollection, WithMapping, WithHeadings
{
    protected $start_date, $end_date, $warehouse, $supplier_id;

    public function __construct($start_date, $end_date, $warehouse, $supplier_id = null)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->warehouse = $warehouse;
        $this->supplier_id = $supplier_id;
    }

    public function collection()
    {
        $warehouse = $this->warehouse;
        $start_date = date('Y-m-d', strtotime($this->start_date));
        $end_date = date('Y-m-d', strtotime($this->end_date));

        $suppliersQuery = DB::table('suppliers');
        if (!empty($this->supplier_id)) {
            $suppliersQuery->where('supplier_id', $this->supplier_id);
        }

        $suppliers = $suppliersQuery->get();
        $filteredSuppliers = [];

        foreach ($suppliers as $s) {
            // Opening Balance
            if (empty($warehouse)) {
                $opening_sql = "SELECT SUM(sll.debit - sll.credit) AS opening_balance
                                FROM supplier_ledger AS sll
                                WHERE sll.supplier_id = {$s->supplier_id}
                                AND sll.date < '{$start_date}'";
            } else {
                $opening_sql = "SELECT SUM(sll.debit - sll.credit) AS opening_balance
                                FROM supplier_ledger AS sll
                                LEFT JOIN purchases po ON sll.purchase_id = po.id
                                WHERE po.wearhouse_id = {$warehouse}
                                AND sll.supplier_id = {$s->supplier_id}
                                AND sll.date < '{$start_date}'";
            }

            $opening_balance_info = DB::select($opening_sql);
            $s->opening_balance = $opening_balance_info[0]->opening_balance ?? 0;

            // Debit, Credit, Balance
            if (empty($warehouse)) {
                $dbcrb_sql = "SELECT SUM(sll.debit) AS debit, SUM(sll.credit) AS credit, SUM(sll.balance) AS balance
                            FROM supplier_ledger AS sll
                            WHERE sll.supplier_id = {$s->supplier_id}
                            AND sll.date BETWEEN '{$start_date}' AND '{$end_date}'";
            } else {
                $dbcrb_sql = "SELECT SUM(sll.debit) AS debit, SUM(sll.credit) AS credit, SUM(sll.balance) AS balance
                            FROM supplier_ledger AS sll
                            LEFT JOIN purchases po ON sll.purchase_id = po.id
                            WHERE po.wearhouse_id = {$warehouse}
                            AND sll.supplier_id = {$s->supplier_id}
                            AND sll.date BETWEEN '{$start_date}' AND '{$end_date}'";
            }

            $debit_credit_balance_info = DB::select($dbcrb_sql);
            $s->debit = $debit_credit_balance_info[0]->debit ?? 0;
            $s->credit = $debit_credit_balance_info[0]->credit ?? 0;
            $s->balance = $debit_credit_balance_info[0]->balance ?? 0;

            // ❌ Skip if both debit and credit are 0
            if ($s->debit == 0 && $s->credit == 0) {
                continue;
            }

            $filteredSuppliers[] = $s;
        }

        return collect($filteredSuppliers);
    }

    public function headings(): array
    {
        return [
            'Supplier ID',
            'Supplier Name',
            'Opening Balance',
            'Debit',
            'Credit',
            'Balance',
        ];
    }

    public function map($supplier): array
    {
        return [
            $supplier->supplier_id,
            $supplier->name,
            number_format($supplier->opening_balance, 2),
            number_format($supplier->debit, 2),
            number_format($supplier->credit, 2),
            number_format($supplier->opening_balance + $supplier->debit - $supplier->credit, 2),
        ];
    }
}

