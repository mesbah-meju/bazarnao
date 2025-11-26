<?php

namespace App\Models;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DownloadProductWiseSalesReportModel implements FromCollection, WithMapping, WithHeadings
{
    public function __construct($warehouse_id, $start_date, $end_date, $search, $category_id, $product_id)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->search = $search;
        $this->category_id = $category_id;
        $this->product_id = $product_id;
        $this->warehouse_id = $warehouse_id;
    }

    public function collection()
    {
        // Convert warehouse into an array if it's not already
        $warehouse = $this->warehouse_id ? (array)$this->warehouse_id : [1, 2, 3]; // Default warehouses if none is selected

        // Build the base query
        $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
            ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->where('num_of_sale', '>', 0)
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'products.purchase_price',
                'categories.name as category_name',
                DB::raw('sum(order_details.price) AS price'),
                DB::raw('sum(order_details.quantity) AS quantity'),
                DB::raw('count(product_id) AS num_of_sale')
            )
            ->groupBy('products.id')
            ->orderBy('num_of_sale', 'desc');

        // Apply category filter if provided
        if (!empty($this->category_id)) {
            $products = $products->where('category_id', $this->category_id);
        }

        // Apply product filter if provided
        if (!empty($this->product_id)) {
            $products = $products->where('products.id', $this->product_id);
        }

        // Apply warehouse filter
        if (!empty($this->warehouse_id)) {
            $products = $products->whereIn('orders.warehouse', $warehouse);
        }

        // Apply date range filter
        if (!empty($this->start_date) && !empty($this->end_date)) {
            $start_date = date('Y-m-d 00:00:00', strtotime($this->start_date));
            $end_date = date('Y-m-d 23:59:59', strtotime($this->end_date));
            $products = $products->whereBetween('orders.delivered_date', [$start_date, $end_date]);
        }

        // Final retrieval of filtered products
        $products = $products->get();

        // Get related stock data
        $openingStocks = OpeningStock::whereIn('wearhouse_id', $warehouse)
            ->whereBetween('created_at', [$start_date, $end_date])
            ->get()->groupBy('product_id');

        $purchases = PurchaseDetail::whereHas('purchase', function($query) use ($start_date, $end_date) {
                $query->where('status', 2)->whereBetween('date', [$start_date, $end_date]);
            })
            ->whereIn('wearhouse_id', $warehouse)
            ->orderBy('created_at', 'asc')
            ->get()->groupBy('product_id');

        $transfersIn = Transfer::where('status', 'Approved')
            ->whereBetween('date', [$start_date, $end_date])
            ->whereIn('to_wearhouse_id', $warehouse)
            ->get()->groupBy('product_id');

        $transfersOut = Transfer::where('status', 'Approved')
            ->whereBetween('date', [$start_date, $end_date])
            ->whereIn('from_wearhouse_id', $warehouse)
            ->get()->groupBy('product_id');

        $damages = Damage::where('status', 'Approved')
            ->whereBetween('date', [$start_date, $end_date])
            ->whereIn('wearhouse_id', $warehouse)
            ->get()->groupBy('product_id');

        $refunds = RefundRequest::where('refund_status', 5)
            ->leftJoin('orders', 'orders.id', '=', 'refund_requests.order_id')
            ->leftJoin('order_details', 'order_details.id', '=', 'refund_requests.order_detail_id')
            ->whereBetween('refund_requests.created_at', [$start_date, $end_date])
            ->whereIn('orders.warehouse', $warehouse)
            ->get()->groupBy('order_details.product_id');

        // Attach stock calculations to the products collection
        foreach ($products as $product) {
            $product_id = $product->product_id;

            // Calculate opening stock
            $openingStockQty = $openingStockAmount = 0;
            if (isset($openingStocks[$product_id])) {
                foreach ($openingStocks[$product_id] as $openStock) {
                    $openingStockQty += $openStock->qty;
                    $openingStockAmount += $openStock->qty * $openStock->price;
                }
            }
            $product->opening_stock_qty = $openingStockQty;
            $product->opening_stock_amount = $openingStockAmount;

            // FIFO Calculation for Purchases
            $purchaseQty = $purchaseAmount = 0;
            $remainingQtyToAllocate = $product->quantity;
            if (isset($purchases[$product_id])) {
                foreach ($purchases[$product_id] as $purchase) {
                    if ($remainingQtyToAllocate <= 0) break;

                    $allocateQty = min($purchase->qty, $remainingQtyToAllocate);
                    $purchaseAmount += $allocateQty * $purchase->price;
                    $purchaseQty += $allocateQty;
                    $remainingQtyToAllocate -= $allocateQty;
                }
            }
            $product->purchase_qty = $purchaseQty;
            $product->purchase_amount = $purchaseAmount;

            // Calculate transfer in
            $transferInQty = $transferInAmount = 0;
            if (isset($transfersIn[$product_id])) {
                foreach ($transfersIn[$product_id] as $transferIn) {
                    $transferInQty += $transferIn->qty;
                    $transferInAmount += $transferIn->qty * $transferIn->price;
                }
            }
            $product->transfer_in_qty = $transferInQty;
            $product->transfer_in_amount = $transferInAmount;

            // Calculate transfer out
            $transferOutQty = $transferOutAmount = 0;
            if (isset($transfersOut[$product_id])) {
                foreach ($transfersOut[$product_id] as $transferOut) {
                    $transferOutQty += $transferOut->qty;
                    $transferOutAmount += $transferOut->qty * $transferOut->price;
                }
            }
            $product->transfer_out_qty = $transferOutQty;
            $product->transfer_out_amount = $transferOutAmount;

            // Calculate damages
            $damageQty = $damageAmount = 0;
            if (isset($damages[$product_id])) {
                foreach ($damages[$product_id] as $damage) {
                    $damageQty += $damage->qty;
                    $damageAmount += $damage->total_amount;
                }
            }
            $product->damage_qty = $damageQty;
            $product->damage_amount = $damageAmount;

            // Calculate refunds
            $refundQty = $refundAmount = 0;
            if (isset($refunds[$product_id])) {
                foreach ($refunds[$product_id] as $refund) {
                    $refundQty += $refund->return_qty;
                    $refundAmount += $refund->return_amount;
                }
            }
            $product->refund_qty = $refundQty;
            $product->refund_amount = $refundAmount;

            // Calculate closing stock
            $closingStockQty = $openingStockQty + $purchaseQty + $transferInQty - $transferOutQty - $product->quantity - $damageQty + $refundQty;
            if ($openingStockQty > 0) {
                $closingStockAmount = $closingStockQty * ($openingStockAmount / $openingStockQty);
            } else {
                $closingStockAmount = 0;
            }
            $product->closing_stock_qty = $closingStockQty;
            $product->closing_stock_amount = $closingStockAmount;

            // Calculate profit or loss
            $product->profit_loss = $product->price - ($openingStockAmount + $purchaseAmount + $transferInAmount - $damageAmount - $refundAmount - $transferOutAmount - $closingStockAmount);
        }

        return collect($products);
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Category',
            'QTY',
            'Unit Price',
            'Total Amount',
            'Num of Sales',
            'Profit',
        ];
    }

    public function map($product): array
    {
        return [
            $product->product_name,
            $product->category_name,
            $product->quantity,
            $product->price / $product->quantity,
            $product->price,
            $product->num_of_sale,
            $product->profit_loss,
        ];
    }
}
