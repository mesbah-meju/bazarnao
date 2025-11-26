<?php
namespace App\Http\Controllers;
class PurchasereturnController extends Controller {

    public function index() {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }

        $user_prev = Session::get('user_prev');
        if (!in_array(55, $user_prev)) {
            return Redirect::to('home')->with('message', 'Access forbidden.');
        }

        $temp_data = DB::table('bill_return as br')
                ->leftjoin('suppliers as su', 'su.supplier_id', '=', 'br.supplier_id')
                ->where('br.status', '=', 1)
                ->where('br.branch', '=', Session::get('branch'))
                ->get(['br.*', 'su.name']);

        if (!$temp_data) {
            $data = null;
        } else {
            $i = 0;
            foreach ($temp_data as $val) {
                $data[$i][0] = $val->bill_return_id;
                $data[$i][1] = $val->date;
                $data[$i][2] = $val->name;
                $data[$i][3] = $val->total_qty;
                $data[$i][4] = $val->total_value;
                $data[$i][5] = $val->cash_return;
                $i++;
            }
        }


        return View::make('contents.purchase_return.index')
                        ->with('data', $data)
                        ->with('title', 'Purchase Return');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }
        $user_prev = Session::get('user_prev');
        if (!in_array(56, $user_prev)) {
            return Redirect::to('/home')->with('message', 'Access forbidden.');
        }

        $data_temp = DB::table('suppliers')
                ->where('status', '=', 1)
                ->where('branch_id', '=', Session::get('branch'))
                ->get();
        
        if (!$data_temp) {
            $data_temp = null;
        }


        return View::make('contents.purchase_return.add')
                        ->with('data_temp', $data_temp)
                        ->with('title', 'Add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }
        $user_prev = Session::get('user_prev');
        if (!in_array(56, $user_prev)) {
            return Redirect::to('home')->with('message', 'Access forbidden.');
        }

        $bill_id = Input::get('bill_id');
        $date = Input::get('date');
        $supplier_id = Input::get('supplier_id');
        $notes = Input::get('notes');

        $total_qty = Input::get('total_qty');
        $total_value = Input::get('total_value');
        $cash_return = Input::get('cash_return');


        $bill_return_id = DB::table('bill_return')->insertGetId(
                array(
                    'branch' => Session::get('branch'),
                    'bill_id' => $bill_id,
                    'date' => $date,
                    'supplier_id' => $supplier_id,
                    'notes' => $notes,
                    'total_qty' => $total_qty,
                    'total_value' => $total_value,
                    'cash_return' => $cash_return,
                    'created_by' => Session::get('user_id'),
                    'createdate' => date("Y-m-d H:i:s"),
                )
        );

        for ($i = 0; $i < count(Input::get('qty')); $i++) {

            $bill_item_id = Input::get('bill_item_id')[$i];
            $qty = Input::get('qty')[$i];

            DB::table('bill_item_return')->insertGetId(
                    array(
                        'bill_return_id' => $bill_return_id,
                        'bill_item_id' => $bill_item_id,
                        'qty' => $qty,
                        'createdate' => date("Y-m-d H:i:s"),
                    )
            );
            
            $code = Helper::get_item_code_by_bill_item_id($bill_item_id);
            $warehouse_id = Helper::get_warehouse_id_bill_code($bill_id, $code);
            if ($warehouse_id) {
                DB::table('warehouse_stock_out')->insertGetId(
                        array(
                            'branch_id' => Session::get('branch'),
                            'ref' => $bill_id,
                            'ref_type' => 'Purchase Return',
                            'warehouse_id' => @$warehouse_id,
                            'code' => $code,
                            'qty' => $qty,
                            'createdate' => date("Y-m-d H:i:s"),
                        )
                );
            }
            
        }
        
        //journal entry
//        $voucherNo = getJournalTransactionId(02);
//        $voucherDate = date("Y-m-d");
//        $client = $supplier_id;
//        $client_type = 'Supplier';
//        $acc_payment_type = 'Cash';
//        
//        $paidLedgerId = 101000000;
//        $accountLedgerId = 100100000;
//        
//        $particularText = 'Purchased Return';
//        $totalAmount = $total_value;
//
//        $result = DB::transaction(function () use ($client_type, $accountLedgerId, $voucherNo, $client, $particularText, $voucherDate, $paidLedgerId, $totalAmount) {
//
//            try {
//
//                $master_data = array(
//                    'branch_id' => Session::get('branch'),
//                    'transaction_id' => $voucherNo,
//                    'transaction_type' => 2,
//                    'createdate' => $voucherDate,
//                    'created_by' => Session::get('user_id'),
//                );
//
//                $acc_journal_master_id = DB::table('acc_journal_master')->insertGetId($master_data);
//
//                    $insetArray = array(
//                        'branch_id' => Session::get('branch'),
//                        'ledger_id' => $accountLedgerId,
//                        'acc_journal_master_id' => $acc_journal_master_id,
//                        'debit' => $totalAmount,
//                        'particulars' => $particularText,
//                        'createdate' => $voucherDate,
//                    );
//                    DB::table('acc_journal_details')->insert($insetArray);
//
//
//                    $insetArray = array(
//                        'acc_journal_master_id' => $acc_journal_master_id,
//                        'branch_id' => Session::get('branch'),
//                        'ledger_id' => $paidLedgerId,
//                        'client' => $client,
//                        'client_type' => $client_type,
//                        'credit' => $totalAmount,
//                        'particulars' => 'Paid to '.get_client_name($client, $client_type),
//                        'createdate' => $voucherDate,
//                    );
//
//                    DB::table('acc_journal_details')->insert($insetArray);
//
//            } catch (\Exception $e) {
//
//                        DB::table('acc_journal_details')
//                                ->where('acc_journal_master_id', $acc_journal_master_id)
//                                ->delete();
//
//                        DB::table('acc_journal_master')
//                                ->where('acc_journal_master_id', $acc_journal_master_id)
//                                ->delete();
//            }
//
//        });
        
        

        Session::flash('message', 'Data has been added successfully');
        Session::flash('message_type', 'success');
        return Redirect::to('purchase-return/'.$bill_return_id);
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
    public function show($id) {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }
        $user_prev = Session::get('user_prev');
        if (!in_array(57, $user_prev)) {
            return Redirect::to('home')->with('message', 'Access forbidden.');
        }

        $data_row = DB::table('bill_return as br')
                ->leftjoin('suppliers as s', 's.supplier_id', '=', 'br.supplier_id')
                ->where('br.status', '=', 1)
                ->where('br.bill_return_id', '=', $id)
                ->where('br.branch', '=', Session::get('branch'))
                ->get(['br.*', 's.name']);

        if (!$data_row) {
            return Redirect::to('purchase-return')
                            ->with('message_type', 'danger')
                            ->with('message', 'Data not found.');
        }

        $bill_return_id = $data_row[0]->bill_return_id;
        $data_item_rows = DB::table('bill_item_return as bir')
                ->leftjoin('bill_item as bi', 'bi.bill_item_id', '=', 'bir.bill_item_id')
                ->leftjoin('oc_product as op', 'op.isbn', '=', 'bi.code')
                ->leftjoin('oc_product_description as opd', 'opd.product_id', '=', 'op.product_id')
                ->join('oc_product_to_store as opts', 'opts.product_id', '=', 'op.product_id')
                ->where('opts.store_id', '=', Session::get('branch'))
                ->where('bir.status', '=', 1)
                ->where('opd.language_id', '=', 1)
                ->where('bir.bill_return_id', '=', $bill_return_id)
                ->get(['bi.*', 'bir.qty as return_qty', 'opd.name']);
        
        if (!$data_item_rows) {
            $data_item_rows = null;
        }

        return View::make('contents.purchase_return.view')
                        ->with('data_row', $data_row)
                        ->with('data_item_rows', $data_item_rows)
                        ->with('title', 'View Purchase Return');
    }
    
    
    public function pdf($id) {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }
        $user_prev = Session::get('user_prev');
        if (!in_array(57, $user_prev)) {
            return Redirect::to('home')->with('message', 'Access forbidden.');
        }

        $data_row = DB::table('bill_return as br')
                ->leftjoin('suppliers as s', 's.supplier_id', '=', 'br.supplier_id')
                ->where('br.status', '=', 1)
                ->where('br.bill_return_id', '=', $id)
                ->where('br.branch', '=', Session::get('branch'))
                ->get(['br.*', 's.name']);

        if (!$data_row) {
            return Redirect::to('purchase-return')
                            ->with('message_type', 'danger')
                            ->with('message', 'Data not found.');
        }

        $bill_return_id = $data_row[0]->bill_return_id;
        $data_item_rows = DB::table('bill_item_return as bir')
                ->leftjoin('bill_item as bi', 'bi.bill_item_id', '=', 'bir.bill_item_id')
                ->where('bir.status', '=', 1)
                ->where('bir.bill_return_id', '=', $bill_return_id)
                ->get(['bi.*', 'bir.qty as return_qty']);
        
        if (!$data_item_rows) {
            $data_item_rows = null;
        }

        $view = View::make('contents.purchase_return.pdf')
                        ->with('data_row', $data_row)
                        ->with('data_item_rows', $data_item_rows)
                        ->with('title', 'View Purchase Return');
        
        $contents = $view->render();
        Helper::create_pdf($contents);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }
        $user_prev = Session::get('user_prev');
        if (!in_array(130, $user_prev)) {
            return Redirect::to('home')->with('message', 'Access forbidden.');
        }


        $name = Input::get('name');
        $point = Input::get('point');
        $note = Input::get('note');
        $option_for = Input::get('option_for');

        DB::table('ev_option')
                ->where('eo_id', $id)
                ->update(
                        array(
                            'name' => $name,
                            'point' => $point,
                            'note' => $note,
                            'option_for' => $option_for,
                        )
        );

        Session::flash('message', 'Data has been updated successfully');
        Session::flash('message_type', 'success');
        return Redirect::to('ev-options');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }
        $user_prev = Session::get('user_prev');
        if (!in_array(58, $user_prev)) {
            return Redirect::to('home')->with('message', 'Access forbidden.');
        }


        DB::table('bill_return')
                ->where('bill_return_id', $id)
                ->update(
                        array(
                            'status' => 0,
                        )
        );

        DB::table('bill_item_return')
                ->where('bill_return_id', $id)
                ->update(
                        array(
                            'status' => 0,
                        )
        );
        
        
        Session::flash('message', 'Data has been deleted successfully');
        Session::flash('message_type', 'success');
        return Redirect::to('purchase-return');
    }

    
    public function export() {
        if (!Session::has('user_id')) {
            return Redirect::to('/')->with('message', 'Please login.');
        }


	$date = date('Y-m-d');
	$filename = 'purchase-return-list-' . $date . '.csv';
	$out = '';
	
	// fiels to export
	$out .='Return#,Date,Supplier,Qty,Total Value,Cash Return';
	$out .="\n";

        $temp_data = DB::table('bill_return as br')
                ->leftjoin('suppliers as su', 'su.supplier_id', '=', 'br.supplier_id')
                ->where('br.status', '=', 1)
                ->where('br.branch', '=', Session::get('branch'))
                ->get(['br.*', 'su.name']);

        if ($temp_data) {
            foreach ($temp_data as $val) {
                $out .= $val->bill_return_id;
                $out .=",";
                $out .= $val->date;
                $out .=",";
                $out .= $val->name;
                $out .=",";
                $out .= $val->total_qty;
                $out .=",";
                $out .= $val->total_value;
                $out .=",";
                $out .= $val->cash_return;
                $out .="\n";
            }
        }

        header("Content-type: text/x-csv");
	header("Content-Disposition: attachment; filename=$filename");
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
	echo $out;
	exit;

    }
    
}
