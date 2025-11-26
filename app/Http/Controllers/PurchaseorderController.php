<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Support\Facades\Session;

class PurchaseorderController extends Controller
{

    public function index()
    {


        $temp_data = DB::table('purchases as po')
            ->leftjoin('suppliers as su', 'su.supplier_id', '=', 'po.supplier_id')
            ->where('po.status', '=', 1)
            ->where('po.branch', '=', Session::get('branch'))
            ->get(['po.*', 'su.name']);

        if (!$temp_data) {
            $data = null;
        } else {
            $i = 0;
            foreach ($temp_data as $val) {
                $data[$i][0] = $val->id;
                $data[$i][1] = $val->date;
                $data[$i][2] = $val->name;
                $data[$i][3] = $val->total_value;
                $data[$i][4] = $val->generated_po_id;
                $i++;
            }
        }
        $title = 'Purchase Order';
        return view('backend.purchases.index', compact('data', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {


        $data_temp = DB::table('suppliers')
            ->where('status', '=', 1)
            ->where('branch_id', '=', Session::get('branch'))
            ->get();

        if (!$data_temp) {
            $data_temp = null;
        }

        $cur_data = DB::table('oc_currency')
            ->where('status', '=', 1)
            ->where('branch_id', '=', Session::get('branch'))
            ->get();

        $temp_data = DB::table('supplier_group')
            ->where('status', '=', 1)
            ->where('branch_id', '=', Session::get('branch'))
            ->get();

        $acc_asset_ledger = DB::table('acc_ledger')
            ->where('branch_id', Session::get('branch'))
            ->where('acc_ledger_group_id', '=', '1')
            ->where('status', 1)
            ->get();


        return View::make('contents.purchases.add')
            ->with('data_temp', $data_temp)
            ->with('cur_data', $cur_data)
            ->with('temp_data', $temp_data)
            ->with('acc_asset_ledger', $acc_asset_ledger)
            ->with('title', 'Add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {


        $date = Input::get('date');
        $supplier_id = Input::get('supplier_id');

        $payable_to = Input::get('payable_to');
        $total_paid = Input::get('total_paid');
        $payment_method = Input::get('payment_method');


        $bill_id = DB::table('purchases')->insertGetId(
            array(
                'branch' => Session::get('branch'),
                'date' => $date,
                'supplier_id' => $supplier_id,
                'total_value' => $total_paid,
                'other' => Input::get('other'),
                'vat' => Input::get('vat'),
                'created_by' => Session::get('user_id'),
                'createdate' => date("Y-m-d H:i:s"),
            )
        );

        $generated_bill_id = Helper::generate_po_id($bill_id);
        DB::table('purchases')
            ->where('id', $bill_id)
            ->update(
                array(
                    'sl' => $generated_bill_id[0],
                    'generated_po_id' => $generated_bill_id[1],
                )
            );

        //
        $factor = $payable_to / Input::get('payable_to_static');

        for ($i = 0; $i < count(Input::get('qty')); $i++) {

            $code = Input::get('isbn')[$i];
            $currency_id = Input::get('currency_id')[$i];
            $qty = Input::get('qty')[$i];
            $amount = Input::get('amount')[$i] * $factor;
            $cost_price = $amount / $qty;
            $sales_price = Input::get('sales_price')[$i];
            $discount = Input::get('discount')[$i];
            $pub_price = Input::get('pub_price')[$i];
            $cost_price_real = Input::get('cost_price')[$i];

            DB::table('purchase_details')->insertGetId(
                array(
                    'id' => $bill_id,
                    'code' => $code,
                    'currency_id' => $currency_id,
                    'qty' => $qty,
                    'cost_price' => $cost_price,
                    'cost_price_real' => $cost_price_real,
                    'sales_price' => $sales_price,
                    'pub_price' => $pub_price,
                    'discount' => $discount,
                    'amount' => $amount,
                    'createdate' => date("Y-m-d H:i:s"),
                )
            );
        }

        Session::flash('message', 'Data has been added successfully');
        Session::flash('message_type', 'success');
        return Redirect::to('purchaseorder/' . $bill_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {


        $data_row = DB::table('purchases as po')
            ->leftjoin('suppliers as s', 's.supplier_id', '=', 'po.supplier_id')
            ->where('po.status', '=', 1)
            ->where('po.id', '=', $id)
            ->get(['po.*', 's.name', 's.address', 's.mobile']);

        if (!$data_row) {
            return Redirect::to('purchaseorder')
                ->with('message_type', 'danger')
                ->with('message', 'Data not found.');
        }

        $bill_id = $data_row[0]->id;
        $data_item_rows = DB::table('purchase_details as bi')
            ->join('products as op', 'op.id', '=', 'bi.code')
            ->where('bi.status', '=', 1)
            ->where('bi.id', '=', $bill_id)
            ->get(['bi.*', 'op.name']);

        if (!$data_item_rows) {
            $data_item_rows = null;
        }

        $data_temp = DB::table('suppliers')
            ->where('status', '=', 1)
            ->get();

        if (!$data_temp) {
            $data_temp = null;
        }


        $temp_data = DB::table('supplier_group')
            ->where('status', '=', 1)
            ->get();

        $title = 'Edit Purchase Order';

        return View('backend.purchases.edit',compact('temp_data', 'data_temp', 'data_row', 'data_item_rows', 'title'));
    }

    public function update($bill_id)
    {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }
        $user_prev = Session::get('user_prev');
        if (!in_array(555, $user_prev)) {
            return Redirect::to('home')->with('message', 'Access forbidden.');
        }


        $bill_id = Input::get('id');
        $date = Input::get('date');
        $supplier_id = Input::get('supplier_id');

        $payable_to = Input::get('payable_to');
        $total_paid = Input::get('payable_to');

        DB::table('purchases')
            ->where('id', '=', $bill_id)
            ->update(
                array(
                    'total_value' => $total_paid,
                    'other' => Input::get('other'),
                    'vat' => Input::get('vat'),
                    'created_by' => Session::get('user_id'),
                )
            );


        //delete bill item
        DB::table('purchase_details')
            ->where('id', $bill_id)
            ->delete();

        //
        $factor = $payable_to / Input::get('payable_to_static');

        for ($i = 0; $i < count(Input::get('qty')); $i++) {

            $code = Input::get('isbn')[$i];
            $currency_id = Input::get('currency_id')[$i];
            $qty = Input::get('qty')[$i];
            $amount = Input::get('amount')[$i] * $factor;
            $cost_price = $amount / $qty;
            $sales_price = Input::get('sales_price')[$i];
            $discount = Input::get('discount')[$i];
            $pub_price = Input::get('pub_price')[$i];
            $cost_price_real = Input::get('cost_price')[$i];

            DB::table('purchase_details')->insertGetId(
                array(
                    'id' => $bill_id,
                    'code' => $code,
                    'currency_id' => $currency_id,
                    'qty' => $qty,
                    'cost_price' => $cost_price,
                    'cost_price_real' => $cost_price_real,
                    'sales_price' => $sales_price,
                    'pub_price' => $pub_price,
                    'discount' => $discount,
                    'amount' => $amount,
                    'createdate' => date("Y-m-d H:i:s"),
                )
            );
        }



        Session::flash('message', 'Data has been updated successfully');
        Session::flash('message_type', 'success');
        return Redirect::to('purchaseorder/' . $bill_id);
    }

    public function show($id)
    {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }
        $user_prev = Session::get('user_prev');
        if (!in_array(553, $user_prev)) {
            return Redirect::to('home')->with('message', 'Access forbidden.');
        }

        $data_row = DB::table('purchases as po')
            ->leftjoin('suppliers as s', 's.supplier_id', '=', 'po.supplier_id')
            ->where('po.status', '=', 1)
            ->where('po.id', '=', $id)
            ->where('po.branch', '=', Session::get('branch'))
            ->get(['po.*', 's.name', 's.address', 's.mobile']);

        if (!$data_row) {
            return Redirect::to('purchaseorder')
                ->with('message_type', 'danger')
                ->with('message', 'Data not found.');
        }

        $bill_id = $data_row[0]->id;

        $data_item_rows = DB::table('purchase_details as poi')
            ->join('oc_product as op', 'op.isbn', '=', 'poi.code')
            ->join('oc_product_description as opd', 'opd.product_id', '=', 'op.product_id')
            ->join('oc_product_to_store as opts', 'opts.product_id', '=', 'op.product_id')
            ->where('opts.store_id', '=', Session::get('branch'))
            ->where('poi.status', '=', 1)
            ->where('opd.language_id', '=', 1)
            ->where('poi.id', '=', $bill_id)
            ->orderBy('poi.po_item_id', 'desc')
            ->get(['poi.*', 'opd.name']);


        if (!$data_item_rows) {
            $data_item_rows = null;
        }

        return View::make('contents.purchases.view')
            ->with('data_row', $data_row)
            ->with('data_item_rows', $data_item_rows)
            ->with('title', 'View Purchase Order');
    }

    public function mail($id)
    {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }
        $user_prev = Session::get('user_prev');
        if (!in_array(53, $user_prev)) {
            return Redirect::to('home')->with('message', 'Access forbidden.');
        }

        $data_row = DB::table('bill')
            ->leftjoin('suppliers as s', 's.supplier_id', '=', 'bill.supplier_id')
            ->where('bill.status', '=', 1)
            ->where('bill.bill_id', '=', $id)
            ->where('bill.branch', '=', Session::get('branch'))
            ->get(['bill.*', 's.name', 's.email', 's.address', 's.contact']);

        if (!$data_row) {
            return Redirect::to('purchase')
                ->with('message_type', 'danger')
                ->with('message', 'Data not found.');
        }

        $bill_id = $data_row[0]->bill_id;
        $data_item_rows = DB::table('bill_item as bi')
            ->leftjoin('oc_product as op', 'op.isbn', '=', 'bi.code')
            ->leftjoin('oc_product_description as opd', 'opd.product_id', '=', 'op.product_id')
            ->where('bi.status', '=', 1)
            ->where('opd.language_id', '=', 3)
            ->where('bi.bill_id', '=', $bill_id)
            ->get(['bi.*', 'opd.name']);

        if (!$data_item_rows) {
            $data_item_rows = null;
        }

        $data[] = $data_row;
        $data[] = $data_item_rows;

        if ($data_row[0]->email) {
            Helper::send_mail($data_row[0]->email, $data_row[0]->name, 'Consignment :: Baatighar', 'bill', $data, NULL);
        }

        Session::flash('message', 'Email has been added successfully');
        Session::flash('message_type', 'success');
        return Redirect::to('purchase');
    }

    public function pdf($id)
    {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }
        $user_prev = Session::get('user_prev');
        if (!in_array(53, $user_prev)) {
            return Redirect::to('home')->with('message', 'Access forbidden.');
        }

        $data_row = DB::table('bill')
            ->leftjoin('suppliers as s', 's.supplier_id', '=', 'bill.supplier_id')
            ->where('bill.status', '=', 1)
            ->where('bill.bill_id', '=', $id)
            ->where('bill.branch', '=', Session::get('branch'))
            ->get(['bill.*', 's.name']);

        if (!$data_row) {
            return Redirect::to('purchase')
                ->with('message_type', 'danger')
                ->with('message', 'Data not found.');
        }

        $bill_id = $data_row[0]->bill_id;
        $data_item_rows = DB::table('bill_item')
            ->where('status', '=', 1)
            ->where('bill_id', '=', $bill_id)
            ->get();
        if (!$data_item_rows) {
            $data_item_rows = null;
        }

        $view = View::make('contents.purchase.pdf')
            ->with('data_row', $data_row)
            ->with('data_item_rows', $data_item_rows)
            ->with('title', 'Purchase Order#' . $id);

        $contents = $view->render();
        Helper::create_pdf($contents);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }
        $user_prev = Session::get('user_prev');
        if (!in_array(554, $user_prev)) {
            return Redirect::to('home')->with('message', 'Access forbidden.');
        }


        DB::table('purchases')
            ->where('id', $id)
            ->update(
                array(
                    'status' => 0,
                )
            );

        DB::table('purchase_details')
            ->where('id', $id)
            ->update(
                array(
                    'status' => 0,
                )
            );



        Session::flash('message', 'Data has been deleted successfully');
        Session::flash('message_type', 'success');
        return Redirect::to('purchaseorder');
    }

    public function edit_price()
    {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }
        $user_prev = Session::get('user_prev');
        if (!in_array(135, $user_prev)) {
            return Redirect::to('home')->with('message', 'Access forbidden.');
        }


        $temp_data = DB::table('bill_item as bi')
            ->join('oc_product as op', 'op.isbn', '=', 'bi.code')
            ->join('oc_product_description as opd', 'opd.product_id', '=', 'op.product_id')
            ->join('oc_product_to_store as opts', 'opts.product_id', '=', 'op.product_id')
            ->where('opts.store_id', '=', Session::get('branch'))
            ->where('bi.status', '=', 1)
            ->groupBy('bi.code')
            ->where('opd.language_id', '=', 1)
            ->get(['bi.*', 'opd.name']);

        return View::make('contents.purchase.edit-price')
            ->with('temp_data', $temp_data)
            ->with('title', 'Edit Price');
    }

    public function update_sales_price()
    {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }

        $code = Input::get('code');
        $price = Input::get('price');

        DB::table('bill_item')
            ->where('code', $code)
            ->update(
                array(
                    'sales_price' => $price,
                )
            );
    }

    public function remove_bill_row()
    {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }

        $bill_item_id = Input::get('bill_item_id');

        DB::table('bill_item')
            ->where('bill_item_id', $bill_item_id)
            ->update(
                array(
                    'status' => 0,
                )
            );
    }

    public function export()
    {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }
        $user_prev = Session::get('user_prev');
        if (!in_array(551, $user_prev)) {
            return Redirect::to('home')->with('message', 'Access forbidden.');
        }

        $date = date('Y-m-d');
        $filename = 'purchase-order-list-' . $date . '.csv';
        $out = '';

        // fiels to export
        $out .= 'Purchase Order#,Date,Supplier,Total Amount';
        $out .= "\n";

        $temp_data = DB::table('purchases as po')
            ->leftjoin('suppliers as su', 'su.supplier_id', '=', 'po.supplier_id')
            ->where('po.status', '=', 1)
            ->where('po.branch', '=', Session::get('branch'))
            ->get(['po.*', 'su.name']);

        if ($temp_data) {
            foreach ($temp_data as $val) {
                $out .= $val->generated_po_id;
                $out .= ",";
                $out .= $val->date;
                $out .= ",";
                $out .= $val->name;
                $out .= ",";
                $out .= $val->total_value;
                $out .= ",";
            }
        }

        header("Content-type: text/x-csv");
        header("Content-Disposition: attachment; filename=$filename");
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo $out;
        exit;
    }

    public function get_bill_list()
    {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }


        $ret = '';
        $bill_id = Input::get('bill_id');


        $temp_data = DB::table('bill')
            ->leftjoin('suppliers as su', 'su.supplier_id', '=', 'bill.supplier_id')
            ->where('bill.status', '=', 1)
            ->where('bill.sl', 'like', $bill_id)
            ->where('bill.branch', '=', Session::get('branch'))
            ->get(['bill.*', 'su.name', 'su.supplier_id']);

        if ($temp_data) {
            foreach ($temp_data as $value) {
                $ret .= '<div onclick="get_bill_details(' . $value->bill_id . ')" class="show">' . $value->generated_bill_id . '</div>';
            }
        }


        echo $ret;
    }

    public function get_bill_details()
    {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }


        $ret = '';
        $bill_id = Input::get('bill_id');


        $temp_data = DB::table('bill')
            ->leftjoin('suppliers as su', 'su.supplier_id', '=', 'bill.supplier_id')
            ->where('bill.status', '=', 1)
            ->where('bill.bill_id', '=', $bill_id)
            ->where('bill.branch', '=', Session::get('branch'))
            ->get(['bill.*', 'su.name', 'su.supplier_id']);

        if ($temp_data) {
            $ret .= '<option value="' . $temp_data[0]->supplier_id . '">' . $temp_data[0]->name . '</option>';
            $ret .= '@';

            $bill_item = DB::table('bill_item as bi')
                ->leftjoin('oc_product as op', 'op.isbn', '=', 'bi.code')
                ->leftjoin('oc_product_description as opd', 'opd.product_id', '=', 'op.product_id')
                ->where('opd.language_id', '=', 1)
                ->where('bi.bill_id', '=', $bill_id)
                ->where('bi.status', '=', 1)
                ->get(['bi.*', 'opd.name']);

            if ($bill_item) {
                $i = 0;
                foreach ($bill_item as $value) {
                    $i++;
                    $ret .= '<tr id="item_row_' . $i . '" class="global_item_row"><input type="hidden" name="bill_item_id[]" value="' . $value->bill_item_id . '">';
                    $ret .= '<td><input class="form-control" id="isbn_' . $i . '" name="isbn[]" value="' . $value->code . '" disabled></td>';
                    $ret .= '<td><input class="form-control" id="title_' . $i . '" name="title[]" value="' . $value->name . '" disabled></td>';
                    $ret .= '<td><input class="form-control numbersOnly qty" id="qty_' . $i . '" name="qty[]" value="' . $value->qty . '" onkeyup="calculate_sub_total_purchase_return()"></td>';
                    $ret .= '<td><input class="form-control" id="publisher_price_' . $i . '" name="publisher_price[]" value="' . $value->pub_price . '" disabled></td>';
                    $ret .= '<td>';
                    $ret .= '<a class="text-danger" href="javascript:remove_line_invoice(' . $i . ')">';
                    $ret .= '<i class="fa fa-close fa-fw"></i>';
                    $ret .= '</a>';
                    $ret .= '</td>';
                    $ret .= '</tr>';
                }
            }
        }


        echo $ret;
    }

    public function excel($id)
    {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }
        $user_prev = Session::get('user_prev');
        if (!in_array(132, $user_prev)) {
            return Redirect::to('home')->with('message', 'Access forbidden.');
        }

        $date = date('Y_m_d');
        $filename = 'baatighar_consignment_' . $date . '.csv';
        $out = '';


        // fiels to export
        $out .= 'Code,Title,Qty,Cost Price,Sales Price,Publisher Price';
        $out .= "\n";

        $data_item_rows = DB::table('bill_item as bi')
            ->leftjoin('oc_product as op', 'op.isbn', '=', 'bi.code')
            ->leftjoin('oc_product_description as opd', 'opd.product_id', '=', 'op.product_id')
            ->where('bi.status', '=', 1)
            ->where('opd.language_id', '=', 1)
            ->where('bi.bill_id', '=', $id)
            ->get(['bi.*', 'opd.name']);

        $total_discount = 0;
        $total_amount = 0;
        $unit_price = array();
        if ($data_item_rows) {
            foreach ($data_item_rows as $value) {
                $out .= $value->code . ',';
                $out .= $value->name . ',';
                $out .= $value->qty . ',';
                $out .= $value->cost_price . ',';
                $out .= $value->sales_price . ',';
                $out .= $value->pub_price . ',';
                $out .= "\n";
            }
        }

        header("Content-type: text/x-csv");
        header("Content-Disposition: attachment; filename=$filename");
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo $out;
        exit;
    }
}
