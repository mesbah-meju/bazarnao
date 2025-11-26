//Add new option for journal vaucher
("use strict");
function addaccountContrav(divName) {
    var row = $("#contraAccVoucher tbody tr").length;
    var optionval = $("#headoption").val();
    var count = row + 1;
    var limits = 500;
    var tabin = 0;
    if (count == limits)
        alert("You have reached the limit of adding " + count + " inputs");
    else {
        var newdiv = document.createElement("tr");
        var tabin = "cmbCode_" + count;
        var tabindex = count * 2;
        newdiv = document.createElement("tr");
        newdiv.innerHTML =
            "<td> <select name='cmbCode[]' id='cmbCode_" +
            count +
            "' class='form-control aiz-selectpicker'></select></td><td><input type='text' name='txtComment[]' value='' class='form-control'  id='txtComment_" +
            count +
            "' ></td><td><input type='number' name='txtAmount[]' class='form-control total_price text-right' value='0.00' placeholder='0.00' id='txtAmount_" +
            count +
            "' onkeyup='calculationContrav(" +
            count +
            ")'></td><td><input type='number' name='txtAmountcr[]' class='form-control total_price1 text-right' id='txtAmount1_" +
            count +
            "' value='0.00' placeholder='0.00' onkeyup='calculationContrav(" +
            count +
            ")'></td><td><button class='btn btn-danger red' type='button' value='delete' onclick='deleteRowContrav(this)'><i class='las la-trash'></i></button></td>";
        document.getElementById(divName).appendChild(newdiv);
        $("#cmbCode_" + count).html(optionval);
        $("#cmbDebit_" + count).html(optionval);
        $("#subtype_" + count).attr("disabled", "disabled");
        document.getElementById(tabin).focus();
        count++;

        AIZ.plugins.bootstrapSelect('refresh');
    }
}

("use strict");
function calculationContrav(sl) {
    var gr_tot1 = 0;
    var gr_tot = 0;
    $(".total_price").each(function () {
        isNaN(this.value) ||
            0 == this.value.length ||
            (gr_tot += parseFloat(this.value));
    });

    $(".total_price1").each(function () {
        isNaN(this.value) ||
            0 == this.value.length ||
            (gr_tot1 += parseFloat(this.value));
    });
    $("#grandTotal").val(gr_tot.toFixed(2, 2));
    $("#grandTotal1").val(gr_tot1.toFixed(2, 2));
}

("use strict");
function deleteRowContrav(e) {
    var t = $("#contraAccVoucher > tbody > tr").length;
    if (1 == t) alert("There only one row you can't delete.");
    else {
        var a = e.parentNode.parentNode;
        a.parentNode.removeChild(a);
    }
    calculationContrav();
}