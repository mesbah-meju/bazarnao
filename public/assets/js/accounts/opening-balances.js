("use strict");
function addaccountOpen(divName) {
    var row = $("#debtAccVoucher tbody tr").length;
    var optionval = $("#headoption").val();
    var count = row + 1;
    var limits = 500;
    var tabin = 0;
    if (count == limits) {
        alert("You have reached the limit of adding " + count + " inputs");
    } else {
        var newdiv = document.createElement("tr");
        var tabin = "cmbCode_" + count;
        var tabindex = count * 2;
        newdiv = document.createElement("tr");

        newdiv.innerHTML =
            "<td> <select name='cmbCode[]' id='cmbCode_" +
            count +
            "' class='form-control aiz-selectpicker' onchange='load_subtypeOpen(this.value," +
            count +
            ")'></select></td><td><select name='subtype[]' id='subtype_" +
            count +
            "' class='form-control aiz-selectpicker' ><option value=''>Select Option</option></select></td><td><input type='number' name='txtDebit[]' class='form-control total_dprice text-right' id='txtDebit_" +
            count +
            "' onkeyup='calculationDebtOpen(" +
            count +
            ")'></td><td><input type='number' name='txtCredit[]' class='form-control total_cprice text-right' id='txtCredit_" +
            count +
            "' onkeyup='calculationCreditOpen(" +
            count +
            ")'><input type='hidden' name='isSubtype[]' id='isSubtype_" +
            count +
            "'  value='1'/></td><td><button  class='btn btn-danger red' type='button' value='delete' onclick='deleteRowDebtOpen(this)'><i class='las la-trash'></i></button></td>";
        document.getElementById(divName).appendChild(newdiv);
        $("#cmbCode_" + count).html(optionval);
        $("#subtype_" + count).attr("disabled", "disabled");
        document.getElementById(tabin).focus();
        count++;

        AIZ.plugins.bootstrapSelect('refresh');
    }
}

("use strict");
function calculationDebtOpen(sl) {
    var gr_tot = 0;
    $(".total_dprice").each(function () {
        isNaN(this.value) ||
            0 == this.value.length ||
            (gr_tot += parseFloat(this.value));
    });

    $("#grandTotald").val(gr_tot.toFixed(2, 2));
}
("use strict");
function calculationCreditOpen(sl) {
    var gr_tot = 0;
    $(".total_cprice").each(function () {
        isNaN(this.value) ||
            0 == this.value.length ||
            (gr_tot += parseFloat(this.value));
    });

    $("#grandTotalc").val(gr_tot.toFixed(2, 2));
}

("use strict");
function deleteRowDebtOpen(e) {
    var t = $("#debtAccVoucher > tbody > tr").length;
    if (1 == t) alert("There only one row you can't delete.");
    else {
        var a = e.parentNode.parentNode;
        a.parentNode.removeChild(a);
    }
    calculationDebtOpen();
    calculationCreditOpen();
}