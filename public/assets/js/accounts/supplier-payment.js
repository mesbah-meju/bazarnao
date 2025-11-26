("use strict");
function supplierRcvcalculation(sl) {
    var due_amount = $('#due_'+sl).val();
    var paid = $("#txtAmount_"+sl).val();
    if(parseFloat((due_amount?due_amount:0)) < parseFloat((paid?paid:0))){
        AIZ.plugins.notify('danger', 'Paid Amount Can not be greater than Due Amount');
        $("#txtAmount_"+sl).val('');
        $("#txtAmount_"+sl).focus();
        return false;
    }
   
    var gr_tot = 0;
    $(".total_price").each(function() {
        isNaN(this.value) || 0 == this.value.length || (gr_tot += parseFloat(this.value))
    });

    $("#grandTotal").val(gr_tot.toFixed(2,2));
    var length = $(".number").length;
    $(".number:eq(0)").val(parseFloat(gr_tot.toFixed(2,2)));
}

("use strict");
function changedueamount() {
    var inputval = parseFloat(0);
    var maintotalamount = $("#grandTotal").val();

    $(".number").each(function () {
        var inputdata = parseFloat($(this).val());
        inputval = inputval + inputdata;

        if (parseFloat(maintotalamount) < parseFloat(inputval)) {
            AIZ.plugins.notify("danger", "You Can not Pay More than Total Amount");
            $(this).val(0);
            return false;
        }
    });

    var restamount = parseFloat(maintotalamount) - parseFloat(inputval);
    var changes = restamount.toFixed(3);
    if (changes <= 0) {
        $("#pay-amount").text(0);
    } else {
        $("#pay-amount").text(changes);
    }
}

function removeMethod(rmdiv, sl) {
    var contain_val = $("#pamount_by_method_" + sl).val();
    $(rmdiv).parent().parent().remove();
    var firstval = $(".number:eq(0)").val();
    var effetval = (contain_val ? parseFloat(contain_val) : 0) + (firstval ? parseFloat(firstval) : 0);
    $(".number:eq(0)").val(effetval.toFixed(2, 2));
    changedueamount();
}

$(document).ready(function () {
    "use strict";

    var frm = $("#supplier_paymentform");
    frm.on("submit", function (e) {
        var finyear = $("#finyear").val();

        if (finyear <= 0) {
            AIZ.plugins.notify("danger", "Please Create Financial Year First");
            return false;
        }
        var inputval = parseFloat(0);
        var maintotalamount = $("#grandTotal").val();

        $(".number").each(function () {
            var inputdata = parseFloat($(this).val());
            inputval = inputval + inputdata;
        });

        if (parseFloat(maintotalamount) > parseFloat(inputval)) {
            AIZ.plugins.notify("danger", "You Should Input Equal Total Amount");
            return false;
        }
        e.preventDefault();
        $.ajax({
            url: $(this).attr("action"),
            method: $(this).attr("method"),
            dataType: "json",
            data: frm.serialize(),
            success: function (data) {
                if (data.status == true) {
                    AIZ.plugins.notify("success", data.message);
                    if (confirm("Success! Do You Want To Print?")) {
                        printRawHtmlInvoice(data.details);
                    } else {
                        location.reload();
                    }
                } else {
                    AIZ.plugins.notify("danger", data.exception);
                }
            },
            error: function (xhr) {
                alert("failed!");
            },
        });
    });
});

var focuser = setInterval(() => window.dispatchEvent(new Event("focus")), 200);

function printRawHtmlInvoice(view) {
    printJS({
        printable: view,
        type: "raw-html",
        onPrintDialogClose: printJobCompleteInvoice,
    });
}

function printJobCompleteInvoice() {
    location.reload();
}
