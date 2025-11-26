<?php

namespace App\Mail;

use App\Order;
use PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceEmailManager extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $array;

    public function __construct($array)
    {
        $this->array = $array;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
     public function build()
     {
         $file = 'public/orders/order-'.$this->array['order']->code.'.pdf';
         if(!file_exists($file))
         $this->invoice_download($this->array['order']->id);
         return $this->view($this->array['view'])
                     ->from($this->array['from'], 'Bazar Nao')
                     ->subject($this->array['subject'])
                     ->attach($file)
                     ->with([
                         'order' => $this->array['order']
                     ]);
     }
     public function invoice_download($id)
    {
        
            $currency_code ='BDT';
       
            $direction = 'ltr';
            $text_align = 'left';
            $not_text_align = 'right';            
        
            $font_family = "'Hind Siliguri','sans-serif'";
       

        $order = Order::findOrFail($id);
        return PDF::loadView('backend.invoices.invoice',[
            'order' => $order,
            'font_family' => $font_family,
            'direction' => $direction,
            'text_align' => $text_align,
            'not_text_align' => $not_text_align
        ], [], [])->save('public/orders/order-'.$order->code.'.pdf');
    }
}
