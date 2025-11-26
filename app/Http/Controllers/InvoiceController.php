<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Currency;
use App\Models\Language;
use App\Models\RefundRequest;
use Session;
use PDF; // Import PDF facade
use Config;

class InvoiceController extends Controller
{
    public function invoice_download($id)
    {
        if (Session::has('currency_code')) {
            $currency_code = Session::get('currency_code');
        } else {
            $currency_code = Currency::findOrFail(get_setting('system_default_currency'))->code;
        }
        if (Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1) {
            $direction = 'rtl';
            $text_align = 'right';
            $not_text_align = 'left';
        } else {
            $direction = 'ltr';
            $text_align = 'left';
            $not_text_align = 'right';
        }

        if ($currency_code == 'BDT') {
            $font_family = "'Hind Siliguri','sans-serif'";
        } elseif ($currency_code == 'AMD') {
            $font_family = "'arnamu','sans-serif'";
        } elseif ($currency_code == 'ILS') {
            $font_family = "'Varela Round','sans-serif'";
        } elseif ($currency_code == 'AED' || $currency_code == 'EGP') {
            $font_family = "'XBRiyaz','sans-serif'";
        } else {
            $font_family = "'Roboto','sans-serif'";
        }

        $order = Order::findOrFail($id);

        $user_all_orders_without_recent = Order::where('id', '!=', $id)
            ->where('user_id', $order->user_id)
            ->where('online_order_delivery_status', 'delivered')
            // ->whereNotNull('payment_details')
            ->selectRaw('grand_total, payment_details, id, code, due_amount')
            ->get();

        $user_all_orders_with_recent = Order::where('user_id', $order->user_id)
            ->where('online_order_delivery_status', 'delivered')
            // ->whereNotNull('payment_details')
            ->selectRaw('grand_total, payment_details, id, code, due_amount')
            ->get();


        // Generate and stream the PDF
        $pdf = PDF::loadView('backend.invoices.invoice', [
            'order' => $order,
            'font_family' => $font_family,
            'direction' => $direction,
            'text_align' => $text_align,
            'not_text_align' => $not_text_align,
            'user_all_orders_without_recent' => $user_all_orders_without_recent,
            'user_all_orders_with_recent' => $user_all_orders_with_recent,
        ]);

        return $pdf->stream('order-' . $order->code . '.pdf');
    }
}
