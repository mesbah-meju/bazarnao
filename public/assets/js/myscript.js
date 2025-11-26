function get_site_url() {
    var base_url = location.href.split('/');
    base_url = base_url[0] + '/' + base_url[1] + '/' + base_url[2] + '/' + base_url[3];
    return base_url;
}

jQuery('.numbersOnly').keyup(function () {
    this.value = this.value.replace(/[^0-9\.]/g, '');
});

function open_cat() {
    $(".options").css({"opacity": "1", "visibility": "visible", "z-index": "9999"});
}

//image upload
$(document).ready(function (e) {

// Function to preview image after validation
    $(function () {
        $("#file").change(function () {
            $("#message").empty(); // To remove the previous error message
            var file = this.files[0];
            var imagefile = file.type;
            var match = ["image/jpeg", "image/png", "image/jpg"];
            if (!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2])))
            {
                $('#previewing').attr('src', 'noimage.png');
                $("#message").html("<p id='error'>Please Select A valid Image File</p>" + "<h4>Note</h4>" + "<span id='error_message'>Only jpeg, jpg and png Images type allowed</span>");
                return false;
            } else
            {
                var reader = new FileReader();
                reader.onload = imageIsLoaded;
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    function imageIsLoaded(e) {
        $("#file").css("color", "green");
        $('#image_preview').css("display", "block");
        $('#previewing').attr('src', e.target.result);
        $('#previewing').attr('width', '200px');
        $('#previewing').attr('height', '200px');
    }
    ;
});
function printDiv(elem)
{
    data = $('#' + elem).html();
    Popup(data);
}

function Popup(data)
{
    var site_url = get_site_url();
    var mywindow = window.open('', 'mydiv', 'height=650,width=1024');
    mywindow.document.open();
    mywindow.document.onreadystatechange = function () {
        if (this.readyState === 'complete') {
            this.onreadystatechange = function () {};
            mywindow.focus();
            mywindow.print();
            mywindow.close();
        }
    }

    mywindow.document.write('<html><head><title>Receipt</title>');
    mywindow.document.write('<link href="' + site_url + '/public/css/bootstrap.min.css" rel="stylesheet">');
    mywindow.document.write('<link href="' + site_url + '/public/css/bootstrap-reset.css" rel="stylesheet">');
    mywindow.document.write('<link href="' + site_url + '/public/css/style.css" rel="stylesheet">');
    mywindow.document.write('<link href="' + site_url + '/public/css/custom.css" rel="stylesheet">');
    mywindow.document.write('</head><body >');
    mywindow.document.write(data);
    mywindow.document.write('</body></html>');

    mywindow.document.close(); // necessary for IE >= 10
    return true;
}
//function printDiv(contents) {
//    var site_url = get_site_url();
//    var html = "<html><body style='margin:20px'>";
//    html += '<link href="'+site_url+'/public/css/bootstrap.min.css" rel="stylesheet">';
//    html += '<link href="'+site_url+'/public/css/bootstrap-reset.css" rel="stylesheet">';
//    html += '<link href="'+site_url+'/public/css/style.css" rel="stylesheet">';
//    html += '<link href="'+site_url+'/public/css/custom.css" rel="stylesheet">';
//
//    html += document.getElementById(contents).innerHTML;
//
//    html += "</body></html>";
//    var printWin = window.open('', '', '');
//    printWin.document.write(html);
//    printWin.document.close();
//    printWin.focus();
//    printWin.print();
//    printWin.close();
//}

function add_line_invoice() {

    var html = '';
    var last_max_val = $('#last_max').val();
    var removed = $('#removed').val();
    last_max_val = (parseInt(last_max_val) + parseInt(removed));
    last_max_val = last_max_val + 1;

    html += '<tr id="item_row_' + last_max_val + '" class="global_item_row">';
    html += '<td><input class="form-control" id="isbn_' + last_max_val + '" name="isbn[]" onkeyup="get_suggested_item(\'invoice\', ' + last_max_val + ')" autocomplete="off"><div class="result_' + last_max_val + ' auto_result" ></div></td>';
    html += '<td><input class="form-control" id="title_' + last_max_val + '" name="title[]"></td>';
    html += '<td>';
    html += '<select class="form-control" id="warehouse_id_' + last_max_val + '" name="warehouse_id[]">';
    html += '<option value="">Select Warehouse</option>';
    html += '</select>';
    html += '</td>';
    html += '<td><input class="form-control numbersOnly qty" id="qty_' + last_max_val + '" name="qty[]" value="0" onkeyup="calculate_sub_total_invoice(' + last_max_val + ')"></td>';
    html += '<td><input class="form-control" id="price_' + last_max_val + '" name="price[]" onkeyup="calculate_sub_total_invoice()"></td>';
    html += '<td><input class="form-control discountField" id="discount_' + last_max_val + '" name="discount[]" value="0" onkeyup="calculate_sub_total_invoice()"></td>';
    html += '<td>';
    html += '<a class="text-danger" href="javascript:remove_line_invoice(' + last_max_val + ')">';
    html += '<i class="fa fa-close fa-fw"></i>';
    html += '</a>';
    html += '</td>';
    html += '</tr>';


    $('#add_line tr:last').after(html);
    $('#last_max').val(last_max_val);
    $('#isbn_' + last_max_val + '').focus();
}

function add_line_purchase() {
    $('.add_purchase_item').modal('show');
}

function add_delivery() {
    $('.delivery_charge').toggle();
}
function add_currier() {
    $('.currier_charge').toggle();
}
function add_wrapping() {
    $('.wrapping_charge').toggle();
}
function add_vat() {
    $('.vat_charge').toggle();
}
function add_service() {
    $('.service_charge').toggle();
}

function add_item_to_purchase() {
    error = '';
    var add_code = $('[name=add_code]').val();
    if (add_code.trim() == '') {
        error += '<li>Please enter book code.</li>';
        $('[name=add_code]').addClass('danger_field');
    } else {
        $('[name=add_code]').removeClass('danger_field');
    }

    var add_copies = $('[name=add_copies]').val();
    if (add_copies.trim() == '') {
        error += '<li>Please enter purchase qty.</li>';
        $('[name=add_copies]').addClass('danger_field');
    } else {
        $('[name=add_copies]').removeClass('danger_field');
    }

    var add_cost_price = $('[name=add_cost_price]').val();
    if (add_cost_price.trim() == '') {
        error += '<li>Please enter unit cost price.</li>';
        $('[name=add_cost_price]').addClass('danger_field');
    } else {
        $('[name=add_cost_price]').removeClass('danger_field');
    }

    if (error != '') {
        $('.alert_title').html('Please fix following error(s)');
        $('.alert_body').html(error);
        $('#general_alert').modal('show');
        return false;
    }

    var add_title = $('[name=add_title]').val();
    var discount = $('[name=add_discount]').val();
    var add_cost_price = $('[name=add_cost_price]').val();
    var amount = $('[name=amount]').val();
    var add_sales_price = $('[name=add_sales_price]').val();
    var add_unit_price = $('[name=add_unit_price]').val();


    var html = '';
    var last_max_val = $('#last_max').val();
    var removed = $('#removed').val();
    last_max_val = (parseInt(last_max_val) + parseInt(removed));
    last_max_val = last_max_val + 1;

    html += '<tr id="item_row_' + last_max_val + '" class="global_item_row">';
    html += '<td><input class="form-control" id="isbn_' + last_max_val + '" name="isbn[]" value="' + add_code + '"><input type="hidden" name="currency_id[]" value="4"><input type="hidden" name="pub_price[]" value=' + add_unit_price + '></td>';
    html += '<td><input class="form-control" id="title_' + last_max_val + '" name="title[]" value="' + add_title + '"></td>';
    html += '<td><input class="form-control numbersOnly qty" onchange="change_value(' + last_max_val + ')" id="qty_' + last_max_val + '" name="qty[]" value=' + add_copies + '></td>';

    html += '<td><input class="form-control" onchange="change_value(' + last_max_val + ')" id="cost_price_' + last_max_val + '" name="cost_price[]" value=' + add_cost_price + '></td>';
    html += '<td><input class="form-control" id="discount_' + last_max_val + '" name="discount[]" value=' + discount + '></td>';
    html += '<td><input class="form-control" id="amount_' + last_max_val + '" name="amount[]" value=' + amount + '></td>';
    html += '<td><input class="form-control" id="sales_price_' + last_max_val + '" name="sales_price[]" value=' + add_sales_price + '></td>';


    html += '<td>';
    html += '<a class="text-danger" href="javascript:remove_line_purchase(' + last_max_val + ')">';
    html += '<i class="fa fa-close fa-fw"></i>';
    html += '</a>';
    html += '</td>';
    html += '</tr>';


    $('.purchase_item_container').prepend(html);
    $('#last_max').val(last_max_val);

    $('[name=add_code]').val('');
    $('[name=add_title]').val('');
    $('[name=add_model]').val('');
    $('[name=add_copies]').val('');
    $('[name=add_unit_price]').val('');
    $('[name=add_discount]').val(0);
    $('[name=add_cost_price]').val('');
    $('[name=amount]').val('');
    $('[name=profit_rate]').val(1);
    $('[name=add_sales_price]').val('');
    //$('#reset_form')[0].reset();

    calculate_sub_total_purchase();
}

function add_line_quotation() {

    var html = '';
    var last_max_val = $('#last_max').val();
    var removed = $('#removed').val();
    last_max_val = (parseInt(last_max_val) + parseInt(removed));
    last_max_val = last_max_val + 1;

    html += '<tr id="item_row_' + last_max_val + '" class="global_item_row">';
    html += '<td><input class="form-control" id="code_' + last_max_val + '" name="code[]" onkeyup="get_item_info_quotation(' + last_max_val + ')"></td>';
    html += '<td><input class="form-control" id="title_' + last_max_val + '" name="title[]"></td>';
    html += '<td><input class="form-control numbersOnly qty" id="qty_' + last_max_val + '" name="qty[]" value="1" onkeyup="calculate_sub_total_quotation()"></td>';

    html += '<td><input class="form-control" id="quote_price_' + last_max_val + '" name="quote_price[]" onkeyup="calculate_sub_total_quotation()"></td>';

    html += '<td>';
    html += '<a class="text-danger" href="javascript:remove_line_invoice(' + last_max_val + ')">';
    html += '<i class="fa fa-close fa-fw"></i>';
    html += '</a>';
    html += '</td>';
    html += '</tr>';


    $('#add_line tr:last').after(html);
    $('#last_max').val(last_max_val);
}

function add_line_damage_replace() {

    var html = '';
    var last_max_val = $('#last_max').val();
    var removed = $('#removed').val();
    last_max_val = (parseInt(last_max_val) + parseInt(removed));
    last_max_val = last_max_val + 1;

    html += '<tr id="item_row_' + last_max_val + '" class="global_item_row">';
    html += '<td><input class="form-control" id="code_' + last_max_val + '" name="code[]" onkeyup="get_item_damaged_qty(' + last_max_val + ')"></td>';
    html += '<td><input class="form-control" id="title_' + last_max_val + '" name="title[]"></td>';
    html += '<td><input class="form-control numbersOnly dqty" id="dqty_' + last_max_val + '" name="dqty[]" value="0"></td>';
    html += '<td><input class="form-control numbersOnly rqty" id="rqty_' + last_max_val + '" name="rqty[]" value="0" onkeyup="calculate_replacement_qty()"></td>';

    html += '<td><input class="form-control" id="cost_price_' + last_max_val + '" name="cost_price[]" onkeyup="calculate_sub_total_quotation()"></td>';
    html += '<td>';
    html += '<a class="text-danger" href="javascript:remove_line_invoice(' + last_max_val + ')">';
    html += '<i class="fa fa-close fa-fw"></i>';
    html += '</a>';
    html += '</td>';
    html += '</tr>';


    $('#add_line tr:last').after(html);
    $('#last_max').val(last_max_val);
}

function remove_line_invoice(which_item) {

    var removed = $('#removed').val();
    $('#removed').val(parseInt(removed) + 1);

    $('#item_row_' + which_item).remove();
    var last_max = $('#last_max').val();
    $('#last_max').val((last_max - 1));
    calculate_sub_total_invoice();
}

function remove_line_purchase(which_item, bill_item_id) {

    var removed = $('#removed').val();
    $('#removed').val(parseInt(removed) + 1);

    $('#item_row_' + which_item).remove();
    var last_max = $('#last_max').val();
    $('#last_max').val((last_max - 1));
    calculate_sub_total_purchase();

    bill_item_id = bill_item_id || "";
    if (bill_item_id != '') {
        site_url = get_site_url();
        $.ajax({
            type: "POST",
            url: site_url + '/remove_bill_row',
            data: "bill_item_id=" + bill_item_id,
            success: function (msg) {
            }
        });
    }

}

function remove_shelf_ref_list(which_item) {

    $('.row_' + which_item).remove();
}


function calculate_sub_total_invoice(row) {

    //check stock
    var stock = $('#warehouse_id_' + row).find(':selected').data('stock');
    var input_val = $('#qty_' + row).val();
    if (input_val > stock) {
        $('.alert_title').html('Please fix following error(s)');
        $('.alert_body').html('Your stock will exceed. Please enter maximum ' + stock);
        $('#general_alert').modal('show');
        $('#qty_' + row).val(stock);
    }

    total_value = 0;
    total_discount = 0;
    total_qty = 0;
    inv_discount_amount = 0;
    delivery_charge = 0;
    vat_charge = 0;

    $('.qty').each(function () {
        qty = $(this).val();
        var id = $(this).attr('id');
        id_array = id.split('_');
        which = id_array[1];

        var item_price = $('#price_' + which).val();
        var item_price = (item_price * qty);
        var discount = $('#discount_' + which).val();
        var discounted_amount = ((discount * item_price) / 100);

        total_value = total_value + item_price;
        total_discount = total_discount + discounted_amount;
        total_qty = total_qty + parseInt(qty);

    });

    var inv_discount = $('#inv_discount').val();
    var inv_discount_amount = ((inv_discount * total_value) / 100);

    total_discount = total_discount + inv_discount_amount;

    receivable = total_value - total_discount;

    //currier_charge
    currier_charge = $('.currier_charge').val();
    currier_charge = parseFloat(currier_charge);

    //delivery
    delivery_charge = $('.delivery_charge').val();
    delivery_charge = parseFloat(delivery_charge);

    //delivery
    wrapping_charge = $('.wrapping_charge').val();
    wrapping_charge = parseFloat(wrapping_charge);

    //delivery
    service_charge = $('.service_charge').val();
    service_charge = parseFloat(service_charge);

    //vat
    vat_charge = $('.vat_charge').val();
    vat_charge = parseFloat(vat_charge);
    if (vat_charge > 0) {
        vat_charge = (receivable * vat_charge) / 100;
    }

    all_charge = currier_charge + delivery_charge + wrapping_charge + service_charge + vat_charge;

    receivable = receivable + all_charge;

    $('#total_value').html(total_value);
    $('#total_qty').html(total_qty);
    $('#receivable').html(receivable);
    $('#net_receivable').html(receivable.toFixed(2));

    $('#total_value_input').val(total_value);
    $('#total_discount_input').val(total_discount);
    $('#total_qty_input').val(total_qty);
//    $('#receivable_input').val(receivable);
    $('#net_receivable_input').val(receivable);
    $('.received').val(receivable.toFixed(2));


}

function calculate_sub_total_invoice_return() {

    total_value = 0;
    total_discount = 0;
    total_qty = 0;

    $('.qty').each(function () {
        qty = $(this).val();
        var id = $(this).attr('id');
        id_array = id.split('_');
        which = id_array[1];

        var item_price = $('#price_' + which).val();
        var item_price = (item_price * qty);
        var discount = $('#discount_' + which).val();
        var discounted_amount = ((discount * item_price) / 100);

        total_value = total_value + item_price;
        total_discount = total_discount + discounted_amount;
        total_qty = total_qty + parseInt(qty);

    });

    receivable = total_value - total_discount;

    $('.total_value').html(total_value);
    $('.total_value').val(total_value);

    $('.total_qty').html(total_qty);
    $('.total_qty').val(total_qty);
}

function calculate_grand_total_invoice() {

    var total_discount_input = $('#total_discount_input').val();
    var total_value_input = $('#total_value_input').val();
    var received = total_value_input - total_discount_input;

    $('.received').val(received.toFixed(2));
    $('#net_receivable').html(received.toFixed(2));
    $('#net_receivable_input').val(received.toFixed(2));

}

function change_currency() {

    var currency_value = $('[name=currency]').find(':selected').data('value');
    currency_value = parseFloat(currency_value);

    var add_copies = $('[name=add_copies]').val();
    var add_unit_price = $('[name=add_unit_price]').val();
    var profit_rate = $('[name=profit_rate]').val();
    add_sales_price = profit_rate * add_unit_price;

    add_unit_price = add_unit_price * currency_value;
    amount = add_copies * add_unit_price;

    var add_discount = $('[name=add_discount]').val();


    amount = amount - ((amount * add_discount) / 100);

    $('[name=amount]').val(amount.toFixed(2));
    $('[name=add_cost_price]').val((amount / add_copies).toFixed(2));

    //var add_cost_price = $('[name=add_cost_price]').val();

    $('[name=add_sales_price]').val(add_sales_price.toFixed(2));

}

function calculate_sales_price_purchase() {
    var add_copies = $('[name=add_copies]').val();
    var add_unit_price = $('[name=add_unit_price]').val();
    amount = add_copies * add_unit_price;
    var add_discount = $('[name=add_discount]').val();


    amount = amount - ((amount * add_discount) / 100);

    $('[name=amount]').val(amount.toFixed(2));
    $('[name=add_cost_price]').val(add_unit_price);
    var profit_rate = $('[name=profit_rate]').val();
    add_sales_price = profit_rate * add_unit_price;
    //var add_cost_price = $('[name=add_cost_price]').val();


    $('[name=add_sales_price]').val(add_sales_price.toFixed(2));

    change_currency();
}

function add_supplier() {
    $('.add_supplier_modal').modal('show');
}
function insert_supplier() {

    site_url = get_site_url();
    var name = $('[name=name]').val();
    var contact_person = $('[name=contact_person]').val();
    var address = $('[name=address]').val();
    var phone = $('[name=phone]').val();
    var mobile = $('[name=mobile]').val();
    var email = $('[name=email]').val();
    var supplier_group_id = $('[name=supplier_group_id]').val();

    $.ajax({
        type: "POST",
        url: site_url + '/add-supplier',
        data: "name=" + name + "&contact_person=" + contact_person + "&address=" + address + "&mobile=" + mobile + "&email=" + email + "&supplier_group_id=" + supplier_group_id + "&phone=" + phone,
        success: function (msg) {
            $('.supplier_id').html(msg);
            $('.add_supplier_modal').modal('hide');
        }
    });
}

function create_supplier_group() {
    $('.add_supplier_group').modal('show');
}
function insert_supplier_group() {

    site_url = get_site_url();
    var group_name = $('[name=group_name]').val();

    $.ajax({
        type: "POST",
        url: site_url + '/add-supplier-group',
        data: "group_name=" + group_name,
        success: function (msg) {
            $('[name=supplier_group_id]').html(msg);
            $('.add_supplier_group').modal('hide');
        }
    });
}

function get_item_title(code) {

    $('[name=add_title]').val('');
    site_url = get_site_url();
    $('[name=add_code]').val(code);

    $.ajax({
        type: "POST",
        url: site_url + '/get_item_title',
        data: "code=" + code,
        success: function (msg) {
            msg = msg.split('@');
            $('[name=add_title]').val(msg[0]);
            $('[name=add_model]').val(msg[1]);
        }
    });
}


function get_searched_books() {

    site_url = get_site_url();
    $('.book_list').hide();
    var book_keyword = $('[name=book_keyword]').val();
    var category_id = $('[name=category_id]').val();


    $.ajax({
        type: "POST",
        url: site_url + '/get_searched_books',
        data: "book_keyword=" + book_keyword + "&category_id=" + category_id,
        success: function (msg) {
            if (msg != '') {
                $('.book_list').show();
                $('.book_list').html(msg);
            }
        }
    });
}

function get_customer_balance() {

    site_url = get_site_url();
    var customer_id = $('[name=customer_id]').val();

    $.ajax({
        type: "POST",
        url: site_url + '/get_customer_balance',
        data: "customer_id=" + customer_id,
        success: function (msg) {
            $('.balance').html('Balance: ' + msg);
        }
    });
}


function calculate_sub_total_purchase() {

    total_value = 0;
    total_qty = 0;

    $('.qty').each(function () {
        qty = $(this).val();
        var id = $(this).attr('id');
        id_array = id.split('_');
        which = id_array[1];
        var item_price = $('#amount_' + which).val();
        total_value = total_value + parseFloat(item_price);
    });
    total_value = parseFloat(total_value);
    $('.payable_to').html(total_value);
    $('.payable_to').val(total_value);
    $('.payable_to_static').val(total_value);
    $('.total_paid').val(total_value);

    $('#sub_total').html(total_value);

}

function change_value(id) {
    var total_value = 0;
    $('.qty').each(function () {
        qty = $(this).val();
        var id = $(this).attr('id');
        id_array = id.split('_');
        which = id_array[1];
        var item_price = $('#cost_price_' + which).val();
        var total = (item_price * qty);
        $('#amount_' + which).val(total);
        total_value = total_value + total;
    });
    total_value = parseFloat(total_value);
    $('.payable_to').html(total_value);
    $('.payable_to').val(total_value);
    $('.payable_to_static').val(total_value);
    $('.total_paid').val(total_value);

    $('#sub_total').html(total_value);
}

function calculate_note_row(which) {

    var qty = $('.note' + which).val();
    row_amount = parseInt(which) * qty;

    $('.note_row_amount' + which).html(row_amount.toFixed(2));
    calculate_note_amount();

}

function calculate_note_amount() {

    total_value = 0;

    $('.row_amount').each(function () {
        val = $(this).html();
        total_value = total_value + parseFloat(val);

    });

    $('.total_note_amount').html(total_value.toFixed(2));

}

function calculate_sub_total_purchase_return() {

    total_value = 0;
    total_qty = 0;

    $('.qty').each(function () {
        qty = $(this).val();
        var id = $(this).attr('id');
        id_array = id.split('_');
        which = id_array[1];

        var item_price = $('#publisher_price_' + which).val();
        var item_price = (item_price * qty);

        total_value = total_value + item_price;
        total_qty = total_qty + parseInt(qty);

    });

    $('.total_qty').html(total_qty);
    $('.total_qty').val(total_qty);

    $('.total_value').html(total_value.toFixed(2));
    $('.total_value').val(total_value);

}

function calculate_sub_total_quotation() {

    total_value = 0;
    total_qty = 0;
    total_discount = 0;

    $('.qty').each(function () {
        qty = $(this).val();
        var id = $(this).attr('id');
        id_array = id.split('_');
        which = id_array[1];

        code = $('#code_' + which).val();
        if (code != '') {

            var item_price = $('#quote_price_' + which).val();
            var item_price = (item_price * qty);

            total_value = total_value + item_price;
            total_qty = total_qty + parseInt(qty);
        }

    });

    var discount = $('.discount').val();
    var total_discount = ((discount * total_value) / 100);


    $('.total_qty').html(total_qty);
    $('.total_qty').val(total_qty);

    total_value = total_value - total_discount;

    $('.total_value').html(total_value.toFixed(2));
    $('.total_value').val(total_value);

}



$(".bill_search").keyup(function () {
    $('#result').hide();
    site_url = get_site_url();
    var bill_id = $('[name=bill_id]').val();

    $.ajax({
        type: "POST",
        url: site_url + '/get_bill_list',
        data: "bill_id=" + bill_id,
        success: function (msg) {
            if (msg == '') {
                $('.return_order_container').hide();
            } else {
                $('#result').show();
                $('#result').html(msg);
            }
        }
    });
});

//$(".product_search").keyup(function () {
//    $('#result').hide();
//    site_url = get_site_url();
//    var keyword = $('[name=keyword]').val();
//
//    $.ajax({
//        type: "POST",
//        url: site_url + '/get_product_list',
//        data: "bill_id=" + bill_id,
//        success: function (msg) {
//            if (msg == '') {
//                $('.return_order_container').hide();
//            } else {
//                $('#result').show();
//                $('#result').html(msg);
//            }
//        }
//    });
//});


function get_bill_details(bill_id) {
    $('.return_order_container').hide();
    site_url = get_site_url();
    $.ajax({
        type: "POST",
        url: site_url + '/get_bill_details',
        data: "bill_id=" + bill_id,
        success: function (msg) {
            if (msg != '') {
                msg = msg.split('@');
                $('.return_order_container').show();
                $('.supplier_id').html(msg[0]);
                $('.bill_item_container').html(msg[1]);
            }
        }
    });
}


$(".order_search").keyup(function () {
    $('#result').hide();
    site_url = get_site_url();
    var order_id = $('[name=order_id]').val();

    $.ajax({
        type: "POST",
        url: site_url + '/get_order_list',
        data: "order_id=" + order_id,
        success: function (msg) {
            if (msg == '') {
                $('.return_order_container').hide();
            } else {
                $('#result').show();
                $('#result').html(msg);
            }
        }
    });
});

function get_order_details(order_id) {
    $('.return_order_container').hide();
    site_url = get_site_url();

    $.ajax({
        type: "POST",
        url: site_url + '/get_order_details',
        data: "order_id=" + order_id,
        success: function (msg) {
            if (msg != '') {
                msg = msg.split('@');
                $('.return_order_container').show();
                $('.customer_id').html(msg[0]);
                $('.bill_item_container').html(msg[1]);
            }
        }
    });
}

$(".auto_keyword").keyup(function () {
    site_url = get_site_url();
    var book_keyword = $('.auto_keyword').val();

    $.ajax({
        type: "POST",
        url: site_url + '/get_book_list',
        data: "book_keyword=" + book_keyword,
        success: function (msg) {
            if (msg != '') {
                $('#result').show();
                $('#result').html(msg);
            }
        }
    });
});



function product_selected(product_id, code, title, author) {

    $('.auto_keyword').val(title + ', ' + author);
    $('.product_id').val(product_id);
    $('.code').val(code);
    $('.title').val(title);

}

function stock_at_a_glance() {

    site_url = get_site_url();

    $.ajax({
        type: "POST",
        url: site_url + '/stock-at-a-glance',
        //data: "name=" + name,
        success: function (msg) {
            $('#myModaltitle').html('Stock at a glance');
            $('#modal_contents').html(msg);
            $('#myModal').modal('show');
        }
    });
}

$(document).on("click", function (e) {
    var $clicked = $(e.target);
    if (!$clicked.hasClass("auto_keyword")) {
        $("#result").fadeOut();
    }
});

function create_manufacturer() {
    $('.add_manufacturer_modal').modal('show');
}

function insert_manufacturer() {

    site_url = get_site_url();
    var pub_name = $('[name=pub_name]').val();

    $.ajax({
        type: "POST",
        url: site_url + '/add-manufacturer',
        data: "name=" + pub_name,
        success: function (msg) {
            $('[name=manufacturer_id]').html(msg);
            $('.add_manufacturer_modal').modal('hide');
        }
    });
}

function create_customer() {
    $('.add_customer_modal').modal('show');
}

function insert_customer() {

    site_url = get_site_url();
    var customer_group_id = $('[name=customer_group_id]').val();
    var firstname = $('[name=firstname]').val();
    var lastname = $('[name=lastname]').val();
    var mobile = $('[name=mobile]').val();
    var telephone = $('[name=telephone]').val();
    var address_1 = $('[name=address_1]').val();

    $.ajax({
        type: "POST",
        url: site_url + '/add_customer',
        data: "customer_group_id=" + customer_group_id + "&firstname=" + firstname + "&lastname=" + lastname
                + "&mobile=" + mobile + "&telephone=" + telephone + "&address_1=" + address_1,
        success: function (msg) {
            if (msg == '01') {
                $('.add_customer_msg').show();
                $('.add_customer_msg').html('Customer already exist with same mobile.');
            } else {
                $('.select2-offscreen').html(msg);
                $('.add_customer_modal').modal('hide');
            }
        }
    });
}

function create_brand() {
    $('.add_brand_modal').modal('show');
}

function insert_brand() {

    site_url = get_site_url();
    var brand_name = $('[name=brand_name]').val();

    $.ajax({
        type: "POST",
        url: site_url + '/add-brand',
        data: "name=" + brand_name,
        success: function (msg) {
            if (msg == 'exist') {
                $('.brand_msg').html('Already exist').show;
            } else {
                $('#brand_id').prepend($('<option>', {value: msg, text: brand_name}));
                $('.add_brand_modal').modal('hide');
            }
        }
    });
}

function show_modal(table) {
    $('[name=crud_data_source]').val(table);
    $('.crud_modal').modal('show');
}

function crud_insert() {

    site_url = get_site_url();
    var table = $('[name=crud_data_source]').val();
    var name = $('[name=crud_name]').val();

    $.ajax({
        type: "POST",
        url: site_url + '/add-crud',
        data: "name=" + name + "&table=" + table,
        success: function (msg) {
            if (msg == 'exist') {
                $('.crud_msg').html('Already exist').show;
            } else {
                $('#' + table + '_id').prepend($('<option>', {value: msg, text: name}));
                $('.crud_modal').modal('hide');
            }
        }
    });
}

function create_subject() {
    $('.add_subject_modal').modal('show');
}

function insert_subject() {

    site_url = get_site_url();
    var sub_name = $('[name=sub_name]').val();
    var parent_id = $('[name=parent_id]').val();

    $.ajax({
        type: "POST",
        url: site_url + '/add-subject',
        data: "name=" + sub_name + "&parent_id=" + parent_id,
        success: function (msg) {
            $('#category_id').html(msg);
            $('.add_subject_modal').modal('hide');
        }
    });
}

function transaction_type_selected() {
    var type = $('[name=type]').val();
    $('.optional_key').hide();

    if (type == 'Invoice') {
        $('.customer').show();
    }
    if (type == 'Purchase') {
        $('.supplier').show();
    }
}

function payment_type_selected() {
    var type = $('[name=user_type]').val();
    $('.optional_key').hide();
    if (type == 'Customer') {
        $('.customer').show();
    }
    if (type == 'Supplier') {
        $('.supplier').show();
    }
}


function get_transaction_references() {
    $('.transaction_references').html('');
    site_url = get_site_url();
    var type = $('[name=type]').val();
    var date_from = $('[name=date_from]').val();
    var date_to = $('[name=date_to]').val();
    var customer_id = $('[name=customer_id]').val();
    var supplier_id = $('[name=supplier_id]').val();

    if (type == 'Invoice') {
        customer_id = customer_id;
    } else {
        customer_id = supplier_id;
    }

    $.ajax({
        type: "POST",
        url: site_url + '/get_transactions',
        data: "type=" + type + "&date_from=" + date_from + "&date_to=" + date_to + "&customer_id=" + customer_id,
        success: function (msg) {
            $('.transaction_references').html(msg);
        }
    });
}

function view_transaction_details(id, type) {
    site_url = get_site_url();
    if (type == 'Invoice') {
        title = 'Invoice';
    }
    if (type == 'Purchase') {
        title = 'Purchase Order';
    }

    $.ajax({
        type: "POST",
        url: site_url + '/get_transactions_details',
        data: "type=" + type + "&id=" + id,
        success: function (msg) {

            $('#transaction_modal_title').html(title);
            $('#modal_contents').html(msg);
            $('#transaction_modal').modal('show');
        }
    });

}

function print_barcode() {

    site_url = get_site_url();
    var error = '';

    var isbn = $('[name=isbn]').val();
    var qty = $('[name=qty]').val();

    if (isbn.trim() == '') {
        error += '<li>Please enter code.</li>';
        $('[name=isbn]').addClass('danger_field');
    } else {
        $('[name=isbn]').removeClass('danger_field');
    }

    if (qty.trim() == '') {
        error += '<li>Please enter qty.</li>';
        $('[name=qty]').addClass('danger_field');
    } else {
        $('[name=qty]').removeClass('danger_field');
    }

    if (error != '') {
        $('.alert_title').html('Please fix following error(s)');
        $('.alert_body').html(error);
        $('#general_alert').modal('show');
        return false;
    }




    $.ajax({
        type: "POST",
        url: site_url + '/print_barcode',
        data: "isbn=" + isbn + "&qty=" + qty,
        success: function (msg) {

            $('.barcode_container').append(msg);
            //alert(msg);
        }
    });

}


function get_customer_quotations() {

    site_url = get_site_url();
    var customer_id = $('[name=customer_id]').val();
    $.ajax({
        type: "POST",
        url: site_url + '/get_customer_quotations',
        data: "customer_id=" + customer_id,
        success: function (msg) {
            if (msg == '') {
                $('.ajaxs_data').html('<table  class="display table table-bordered table-striped" id="example"><tbody><tr><td colspan="3">No data found.</td></tr></tbody></table>');
            } else {
                $('.ajaxs_data').html(msg);
            }
        }
    });

}


function view_quotations_details(id) {
    site_url = get_site_url();

    $.ajax({
        type: "POST",
        url: site_url + '/view_quotations_details',
        data: "id=" + id,
        success: function (msg) {
            $('#transaction_modal_title').html('Print Quotation');
            $('#modal_contents').html(msg);
            $('#transaction_modal').modal('show');
        }
    });

}


function add_book_to_damage_list() {
    $('.add_book_to_damage_list').modal('show');
}


function get_item_info_damage_entry(type) {
    site_url = get_site_url();
    var supplier_id = $('[name=supplier_id]').val();
    var code = $('[name=add_code]').val();

    $.ajax({
        type: "POST",
        url: site_url + '/get_item_info_damage_item',
        data: "code=" + code + "&supplier_id=" + supplier_id + "&type=" + type,
        success: function (msg) {
            msg = msg.split('@');
            $('.add_title').val(msg[0]);
            $('.add_batch').html(msg[1]);
        }
    });

}

function get_item_price_damage_stock() {
    site_url = get_site_url();
    var bill_id = $('[name=add_batch]').val();
    var add_code = $('[name=add_code]').val();

    $.ajax({
        type: "POST",
        url: site_url + '/get_item_price_damage_stock',
        data: "bill_id=" + bill_id + "&code=" + add_code,
        success: function (msg) {
            msg = msg.split('@');

            $('.add_current_stock').val(msg[0]);
            $('.add_pub_price').val(msg[1]);
            $('.add_unit_price').val(msg[2]);
            $('.add_cost_price').val(msg[3]);
        }
    });

}

function get_item_info_quotation(which) {
    site_url = get_site_url();
    var code = $('#code_' + which).val();

    $.ajax({
        type: "POST",
        url: site_url + '/get_item_info_quotation',
        data: "code=" + code,
        success: function (msg) {
            msg = msg.split('@');
            $('#title_' + which).val(msg[0]);
            $('#quote_price_' + which).val(msg[2]);
            $('#pub_price_' + which).val(msg[3]);
            calculate_sub_total_quotation();
            add_line_quotation();
        }
    });

}

function calculate_item_damage_price() {
    var add_unit_price = $('[name=add_unit_price]').val();
    var add_copies = $('[name=add_copies]').val();
    $('.add_total_price').val(add_unit_price * add_copies);

}

function insert_into_damage_list() {
    var add_code = $('[name=add_code]').val();
    var add_title = $('[name=add_title]').val();
    var add_copies = $('[name=add_copies]').val();
    var add_cost_price = $('[name=add_cost_price]').val();
    var warehouse_id = $('[name=warehouse_id]').val();
    var bill_id = $('[name=add_batch]').val();

    var cost_value = add_cost_price * add_copies;
    $('.add_book_to_damage_list').modal('hide');
    $('.damage_item_list').append('<tr class="damage_row_' + add_code + '"><td>' + add_code + '<input type="hidden" name="code[]" value="' + add_code + '"></td><td>' + add_title + '<input type="hidden" name="title[]" value="' + add_title + '"></td><td class="damage_copies">' + add_copies + '<input type="hidden" name="qty[]" value="' + add_copies + '"></td><td class="damage_cost_value">' + cost_value + '<input type="hidden" name="cost_price[]" value="' + cost_value + '"><input type="hidden" name="warehouse_id[]" value="' + warehouse_id + '"><input type="hidden" name="bill_id[]" value="' + bill_id + '"></td><td><a class="text-danger" href="javascript:remove_damage_item(' + add_code + ')"><i class="fa fa-close fa-fw"></i></a></td></tr>');
    calculate_total_damage_value();

}

function remove_damage_item(which) {
    $('.damage_row_' + which).remove();
    calculate_total_damage_value();
}

function calculate_total_damage_value() {

    var total_qty = 0;
    var total_value = 0;

    $('.damage_copies').each(function () {
        qty = $(this).html();
        qty = parseInt(qty);

        total_qty = total_qty + parseInt(qty);

    });

    $('.damage_cost_value').each(function () {
        value = $(this).html();
        value = parseFloat(value);

        total_value = total_value + parseInt(value);

    });

    $('[name=total_damage_qty]').val(total_qty);
    $('[name=total_damage_value]').val(total_value);

}


function get_item_damaged_qty(which) {
    site_url = get_site_url();
    var code = $('#code_' + which).val();
    var supplier_id = $('[name=supplier_id]').val();

    $.ajax({
        type: "POST",
        url: site_url + '/get_item_damaged_qty',
        data: "code=" + code + "&supplier_id=" + supplier_id,
        success: function (msg) {
            msg = msg.split('@');
            $('#title_' + which).val(msg[0]);
            $('#dqty_' + which).val(msg[1]);
            $('#cost_price_' + which).val(msg[2]);
            calculate_replacement_qty();
        }
    });

}

function calculate_replacement_qty() {

    var total_qty = 0;
    var total_value = 0;
    var total_rqty = 0;

    $('.dqty').each(function () {
        dqty = $(this).val();
        var id = $(this).attr('id');
        id_array = id.split('_');
        which = id_array[1];

        var rqty = $('#rqty_' + which).val();
        var cost_price = $('#cost_price_' + which).val();

        total_value = total_value + parseFloat(cost_price);
        total_qty = total_qty + parseInt(dqty);
        total_rqty = total_rqty + parseInt(rqty);

    });


    $('[name=total_qty]').val(total_qty);
    $('[name=total_value]').val(total_value);
    $('[name=total_return]').val(total_qty - total_rqty);

}


$(function () {
    $(".search_cus_sup").keyup(function () {

        site_url = get_site_url();

        var searchid = $(this).val();
        var dataString = 'search=' + searchid;
        if (searchid != '')
        {
            $.ajax({
                type: "POST",
                url: site_url + "/get_customer_supplier",
                data: dataString,
                cache: false,
                success: function (html) {

                    if (html == '') {
                        $('#loader').html('There is no data found.');
                    } else {
                        $("#result").html(html).show();
                    }

                }
            });


        }
        return false;
    });


    $(document).on("click", function (e) {
        var $clicked = $(e.target);
        if (!$clicked.hasClass("search")) {
            $("#result").fadeOut();
        }
    });
    $('#budget_id').click(function () {
        $("#result").fadeIn();
    });
});

function cus_sup_search_row_selected(id, user_type) {
    $(".search_cus_sup").val('');
    var html = $(".row_" + user_type + '_' + id).html();
    $(".search_cus_sup").val(html);
    $(".user_type").val(user_type);
    $(".user_id").val(id);
}

function display(block, field_name) {
    $(".toggle_display_" + field_name).hide();
    $("." + block + "_" + field_name + "").show();
}


function check_book() {
    site_url = get_site_url();
    $('.alert-info').hide();

    var isbn = $('[name=isbn]').val();
    var name = $('[name=name]').val();
    if ((isbn.trim() == '') || (name.trim() == '')) {
        return false;
    }

    $.ajax({
        type: "POST",
        url: site_url + '/check_book',
        data: "isbn=" + isbn + "&name=" + name,
        success: function (msg) {
            if (msg != '') {
                $('.alert-info').show();
                $('.alert-info').html(msg);
            }
        }
    });

}

function create_isbn() {
    site_url = get_site_url();

    var auto_isbn_gen = $('[name=auto_isbn_gen]').val();
    if (auto_isbn_gen != '') {
        $('[name=isbn]').val(auto_isbn_gen);
        return false;
    }

    $.ajax({
        type: "POST",
        url: site_url + '/create_isbn',
        //data: "isbn=" + isbn+"&name=" + name,
        success: function (msg) {
            $('[name=auto_isbn_gen]').val(msg);
            $('[name=isbn]').val(msg);
        }
    });

}

function check_code() {
    site_url = get_site_url();

    var isbn = $('[name=isbn]').val();

    $.ajax({
        type: "POST",
        url: site_url + '/check_code',
        data: "isbn=" + isbn,
        success: function (msg) {
            if (msg == 'exist') {
                $('.alert-info').html('Product already exist with same code.').show();
            } else {
                $('.alert-info').hide();
            }
        }
    });

}

function get_item_info_invoice(which, code) {
    site_url = get_site_url();

    $('#isbn_' + which).val(code);

    $.ajax({
        type: "POST",
        url: site_url + '/get_item_info_invoice',
        data: "isbn=" + code,
        success: function (msg) {
            msg = msg.split('@');
            $('#title_' + which).val(msg[0]);
            //$('#batch_' + which).html(msg[1]);
            $('#price_' + which).val(msg[2]);
            $('#qty_' + which).val(1);
            $('#qty_' + which).attr("data-stock", msg[3]);
            //calculate_sub_total_invoice();
            //add_line_invoice()
            //get_item_price_invoice(which);
            get_warehouse(code, which);
        }
    });

}

function get_warehouse(code, which) {
    site_url = get_site_url();
    blank = 0;

    $.ajax({
        type: "POST",
        url: site_url + '/get_warehouse',
        data: "code=" + code,
        success: function (msg) {
            $('#warehouse_id_' + which).html(msg);
            calculate_sub_total_invoice();

            $('.qty').each(function () {
                qty = $(this).val();
                if (qty == 0) {
                    blank++;
                }
            });
            if (blank == 0) {
                add_line_invoice();
            }
        }
    });

}

//function get_item_price_invoice(which) {
//    site_url = get_site_url();
//    //var bill_id = $('#batch_'+which).val();
//    var code = $('#isbn_'+which).val();
//
//    $.ajax({
//        type: "POST",
//        url: site_url + '/get_item_price_damage_stock',
//        data: "code=" + code,
//        success: function (msg) {
//            msg = msg.split('@');
//            $('#price_'+which).val(msg[2]);
//            get_warehouse(bill_id, code, which);
//        }
//    });
//    
//}

function customer_report_type() {

    var type = $('[name=type]').val();
    if (type == 'ledger') {
        $('.ledger').show();
        $('.register').hide();
    } else {
        $('.register').show();
        $('.ledger').hide();
    }

}


//*********************************validation********************************************

function validate_branch_form() {

    var error = '';

    var title = $('[name=title]').val();
    if (title.trim() == '') {
        error += '<li>Branch title required.</li>';
        $('[name=title]').addClass('danger_field');
    } else {
        $('[name=title]').removeClass('danger_field');
    }


    var address = $('[name=address]').val();
    if (address.trim() == '') {
        error += '<li>Branch address required.</li>';
        $('[name=address]').addClass('danger_field');
    } else {
        $('[name=address]').removeClass('danger_field');
    }

    var contact_person = $('[name=contact_person]').val();
    if (contact_person.trim() == '') {
        error += '<li>Contact person required.</li>';
        $('[name=contact_person]').addClass('danger_field');
    } else {
        $('[name=contact_person]').removeClass('danger_field');
    }

    if (error != '') {
        $('.alert_title').html('Please fix following error(s)');
        $('.alert_body').html(error);
        $('#general_alert').modal('show');
        return false;
    }
}

function validate_purchase_form() {

    var error = '';

    var date = $('[name=date]').val();
    if (date.trim() == '') {
        error += '<li>Please select date.</li>';
        $('[name=date]').addClass('danger_field');
    } else {
        $('[name=date]').removeClass('danger_field');
    }

    var supplier_id = $('[name=supplier_id]').val();
    if (supplier_id.trim() == '') {
        error += '<li>Please select supplier.</li>';
        $('[name=supplier_id]').addClass('danger_field');
    } else {
        $('[name=supplier_id]').removeClass('danger_field');
    }

    var qty = $('.qty').length;
    if (qty == 0) {
        error += '<li>Please enter at least one purchase item.</li>';
    }

    var total_paid = $('[name=total_paid]').val();
    if (total_paid.trim() == '') {
        error += '<li>Please enter total paid amount.</li>';
        $('[name=total_paid]').addClass('danger_field');
    } else {
        $('[name=total_paid]').removeClass('danger_field');
    }

    if (error != '') {
        $('.alert_title').html('Please fix following error(s)');
        $('.alert_body').html(error);
        $('#general_alert').modal('show');
        return false;
    }
}

function validate_purchase_return_form() {

    var error = '';

    var bill_id = $('[name=bill_id]').val();
    if (bill_id.trim() == '') {
        error += '<li>Please enter purchase order ID.</li>';
        $('[name=bill_id]').addClass('danger_field');
    } else {
        $('[name=bill_id]').removeClass('danger_field');
    }

    var total_value = $('[name=total_value]').val();
    var cash_return = $('[name=cash_return]').val();

    if (parseFloat(total_value) < parseFloat(cash_return)) {
        error += '<li>Cash return can not be more than total value.</li>';
        $('[name=cash_return]').addClass('danger_field');
    } else {
        $('[name=cash_return]').removeClass('danger_field');
    }

    if (error != '') {
        $('.alert_title').html('Please fix following error(s)');
        $('.alert_body').html(error);
        $('#general_alert').modal('show');
        return false;
    }
}

function validate_invoice_return_form() {

    var error = '';

    var order_id = $('[name=order_id]').val();
    if (order_id.trim() == '') {
        error += '<li>Please enter order ID.</li>';
        $('[name=order_id]').addClass('danger_field');
    } else {
        $('[name=order_id]').removeClass('danger_field');
    }

    var total_value = $('[name=total_value]').val();
    var cash_return = $('[name=cash_return]').val();

    if (parseFloat(total_value) < parseFloat(cash_return)) {
        error += '<li>Cash return can not be more than total value.</li>';
        $('[name=cash_return]').addClass('danger_field');
    } else {
        $('[name=cash_return]').removeClass('danger_field');
    }

    if (error != '') {
        $('.alert_title').html('Please fix following error(s)');
        $('.alert_body').html(error);
        $('#general_alert').modal('show');
        return false;
    }
}

function validate_publisher_form() {

    var error = '';

    var name = $('[name=name]').val();
    if (name.trim() == '') {
        error += '<li>Please enter order ID.</li>';
        $('[name=name]').addClass('danger_field');
    } else {
        $('[name=name]').removeClass('danger_field');
    }

    if (error != '') {
        $('.alert_title').html('Please fix following error(s)');
        $('.alert_body').html(error);
        $('#general_alert').modal('show');
        return false;
    }
}


function validate_quotation_form() {

    var error = '';

    var date = $('[name=date]').val();
    if (date.trim() == '') {
        error += '<li>Please select date.</li>';
        $('[name=date]').addClass('danger_field');
    } else {
        $('[name=date]').removeClass('danger_field');
    }

    var customer_id = $('[name=customer_id]').val();
    if (customer_id.trim() == '') {
        error += '<li>Please select customer.</li>';
        $('[name=customer_id]').addClass('danger_field');
    } else {
        $('[name=customer_id]').removeClass('danger_field');
    }

    var qty = $('.qty').length;
    if (qty == 0) {
        error += '<li>Please enter at least one purchase item.</li>';
    }


    if (error != '') {
        $('.alert_title').html('Please fix following error(s)');
        $('.alert_body').html(error);
        $('#general_alert').modal('show');
        return false;
    }
}

function validate_book_info_form() {

    var error = '';

    var code = $('[name=code]').val();
    if (code.trim() == '') {
        error += '<li>Please enter SL.</li>';
        $('[name=code]').addClass('danger_field');
    } else {
        $('[name=code]').removeClass('danger_field');
    }


    if (error != '') {
        $('.alert_title').html('Please fix following error(s)');
        $('.alert_body').html(error);
        $('#general_alert').modal('show');
        return false;
    }
}

function validate_authors_form() {

    var error = '';

    var name = $('[name=name]').val();
    if (name.trim() == '') {
        error += '<li>Please enter name.</li>';
        $('[name=name]').addClass('danger_field');
    } else {
        $('[name=name]').removeClass('danger_field');
    }

    if (error != '') {
        $('.alert_title').html('Please fix following error(s)');
        $('.alert_body').html(error);
        $('#general_alert').modal('show');
        return false;
    }

}

function validate_invoice_form() {

    var error = '';

    var customer_id = $('#customer_id').val();
    var type_customer = $('.type_customer').val();
    if ((customer_id.trim() == '') && (type_customer.trim() == '')) {
        error += '<li>Please select customer or type customer name.</li>';
    }

    var total_qty_input = $('[name=total_qty_input]').val();
    if (total_qty_input.trim() == '') {
        error += '<li>Please select at least one item.</li>';
    }

    if (error != '') {
        $('.alert_title').html('Please fix following error(s)');
        $('.alert_body').html(error);
        $('#general_alert').modal('show');
        return false;
    }

}

function validate_customer_payment_form() {

    var error = '';

    var user_type = $('[name=user_type]').val();
    if (user_type == 'Customer') {
        var customer_id = $('[name=customer_id]').val();
        if (customer_id == '') {
            $('.alert_title').html('Please fix following error(s)');
            $('.alert_body').html('Please select customer.');
            $('#general_alert').modal('show');
            return false;
        }
    } else {
        var supplier_id = $('[name=supplier_id]').val();
        if (supplier_id == '') {
            $('.alert_title').html('Please fix following error(s)');
            $('.alert_body').html('Please select supplier.');
            $('#general_alert').modal('show');
            return false;
        }
    }


    check = 0;
    if (($('.rcv_check').prop('checked')) && ($('.pay_check').prop('checked'))) {
        $('.alert_title').html('Please fix following error(s)');
        $('.alert_body').html('You can not receive and pay at a same time.');
        $('#general_alert').modal('show');
        return false;
    }
    if ($('.rcv_check').prop('checked')) {
        check = 1;
    }
    if ($('.pay_check').prop('checked')) {
        check = 1;
    }

    if (check == 0) {
        $('.alert_title').html('Please fix following error(s)');
        $('.alert_body').html('Please select payment is received or pay.');
        $('#general_alert').modal('show');
        return false;
    }
}

function validate_add_to_list_form() {

    var error = '';

    var auto_keyword = $('[name=auto_keyword]').val();
    if (auto_keyword.trim() == '') {
        error += '<li>Please book enter code.</li>';
        $('[name=auto_keyword]').addClass('danger_field');
    } else {
        $('[name=auto_keyword]').removeClass('danger_field');
    }

    var shelf_ref = $('[name=shelf_ref]').val();
    if (shelf_ref.trim() == '') {
        error += '<li>Please book self ref.</li>';
        $('[name=shelf_ref]').addClass('danger_field');
    } else {
        $('[name=shelf_ref]').removeClass('danger_field');
    }


    if (error != '') {
        $('.alert_title').html('Please fix following error(s)');
        $('.alert_body').html(error);
        $('#general_alert').modal('show');
        return false;
    }

    var product_id = $('.product_id').val();
    var code = $('.code').val();
    var title = $('.title').val();

    html = '';

    html += '<tr class="row_' + product_id + '"><td>' + code + '</td><td>' + title + '</td><td>' + shelf_ref + '</td>';
    html += '<td><a class="text-danger" href="javascript:remove_shelf_ref_list(' + product_id + ')">';
    html += '<i class="fa fa-close fa-fw"></i>';
    html += '</a>';
    html += '</td>';
    html += '<input type="hidden" name="product_id[]" value="' + product_id + '">';
    html += '<input type="hidden" name="shelf_ref[]" value="' + shelf_ref + '">';
    html += '</tr>';

    $('.shelf_ref_list').append(html);
    $('[name=auto_keyword]').val('');

}


function jquery_form_submit(form_id) {
    $("#" + form_id).submit();
}

//function assign_warehouse(bill_id, code, qty) {
//   $(".wh_bill_id").val(bill_id);
//   $(".wh_code").val(code);
//   $(".warehouse_total_max_qty").val(qty);
//   $("#add_warehouse").modal('show');
//}

function assign_to_warehouse(bill_id, warehouse_id, code, sl) {
    site_url = get_site_url();

    var warehouse_total_max_qty = $('.qty_' + sl).val();

    var qty = $('.wh_qty_' + sl + '_' + warehouse_id).val();

    qty_sum = 0;
    $('.warehouse_qty_' + sl).each(function () {
        row_qty = $(this).val();
        qty_sum = qty_sum + parseFloat(row_qty);
    });

    if (qty_sum > warehouse_total_max_qty) {
        $('.alert_title').html('Warning');
        $('.alert_body').html('<li>You can not assign qty to warehouse more than purchase</li>');
        $("#general_alert").modal('show');
        return false;
    }

    $.ajax({
        type: "POST",
        url: site_url + '/assign_to_warehouse',
        data: "bill_id=" + bill_id + "&code=" + code + "&qty=" + qty + "&warehouse_id=" + warehouse_id,
        success: function (msg) {
            $('.wh_qty_' + sl + '_' + warehouse_id).addClass('assigned');
            $('#myModaltitle').html('Action Completed');
            $('#modal_contents').html('Assigned successfully.');
            $("#myModal").modal('show');
        }
    });

}
$('html').click(function () {
    $(".ledgerListRight").css({"display": "none"});
});
$('html').click(function () {
    $(".auto_result").css({"display": "none"});
});

function get_child(parent_id, table, child_level) {
    site_url = get_site_url();

    var parent_id_val = $('#' + parent_id + '').val();

    $.ajax({
        type: "POST",
        url: site_url + '/get_child',
        data: "parent_id=" + parent_id + "&table=" + table + "&parent_id_val=" + parent_id_val,
        success: function (msg) {

            $('#' + table + '_container').show();
            $('#' + table + '_lebel').html(child_level);
            $('#' + table + '_id').html(msg);

        }
    });

}
function get_suggested_item(page, sl) {
    sl = sl || "";
    page = page || "";

    $('[name=add_title]').val('');
    site_url = get_site_url();
    if (page == 'invoice') {
        var code = $('#isbn_' + sl).val();
    } else {
        var code = $('[name=add_code]').val();
    }

    $.ajax({
        type: "POST",
        url: site_url + '/get_suggested_item',
        data: "code=" + code + "&sl=" + sl + "&page=" + page,
        success: function (msg) {
            if (page == 'invoice') {
                $('.result_' + sl).html(msg).show();
            } else {
                $('#result').html(msg).show();
            }

        }
    });
}
$(".purchase_vat").keyup(function () {
    var purchase_vat = $(this).val();
    var payable_to = parseFloat($('.payable_to_static').val());
    payable_to = payable_to + ((payable_to * purchase_vat) / 100);

    var purchase_other = parseFloat($('.purchase_other').val());
    payable_to = purchase_other + payable_to;

    $('.payable_to').html(payable_to);
    $('.payable_to').val(payable_to);
    $('.total_paid').val(payable_to);
});

$(".purchase_other").keyup(function () {
    var purchase_other = parseFloat($(this).val());
    var purchase_vat = parseFloat($('.purchase_vat').val());
    var payable_to = parseFloat($('.payable_to_static').val());
    payable_to = payable_to + ((payable_to * purchase_vat) / 100);
    if ($('#every_item').is(':checked') == true) {
        var total_qty = 0;
        $('.qty').each(function () {
            total_qty = total_qty + Number($(this).val());
        })
        purchase_other = purchase_other * total_qty;
    }
    payable_to = purchase_other + payable_to;

    $('.payable_to').html(payable_to);
    $('.payable_to').val(payable_to);
    $('.total_paid').val(payable_to);
});
$('#every_item').click(function () {
    var purchase_other = parseFloat($(".purchase_other").val());
    var purchase_vat = parseFloat($('.purchase_vat').val());
    var payable_to = parseFloat($('.payable_to_static').val());
    payable_to = payable_to + ((payable_to * purchase_vat) / 100);
    if ($(this).is(':checked') == true) {
        var total_qty = 0;
        $('.qty').each(function () {
            total_qty = total_qty + Number($(this).val());
        })
        purchase_other = purchase_other * total_qty;
    }
    payable_to = purchase_other + payable_to;

    $('.payable_to').html(payable_to);
    $('.payable_to').val(payable_to);
    $('.total_paid').val(payable_to);
})
$(".update_sales_price").keyup(function () {
    var price = parseFloat($(this).val());
    var code = $(this).data('code');
    site_url = get_site_url();
    $.ajax({
        type: "POST",
        url: site_url + '/update_sales_price',
        data: "code=" + code + "&price=" + price,
        success: function (msg) {
            $('.msg_' + code).show();
        }
    });
});
