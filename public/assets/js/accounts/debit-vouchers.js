//Add new option for debit vaucher
("use strict");
function addaccountDebit(divName) {
    var row = $("#debitAccVoucher tbody tr").length;
    var optionval = $("#headoption").val();
    var reltypeoptionval = $("#reltypeoption").val();
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
            "<td><select name='cmbCode[]' id='cmbCode_" +
            count +
            "' class='form-control aiz-selectpicker' onchange='load_subtypeDebitv(this.value," +
            count +
            ")'></select></td><td><select name='subtype[]' id='subtype_" +
            count +
            "' class='form-control aiz-selectpicker' data-live-search='true' required><option value=''>Select Option</option></select></td><td><select name='reltype[]' id='reltype_" +
            count +
            "' class='form-control aiz-selectpicker' onchange='load_relvalue(this.value," +
            count +
            ")'></select></td><td><select name='relvalue[]' id='relvalue_" +
            count +
            "' class='form-control aiz-selectpicker' data-live-search='true' required><option value=''>Select Option</option></select></td><td><input type='hidden' name='isSubtype[]' id='isSubtype_" +
            count +
            "' value='1' /><input type='text' name='txtComment[]' value='' class='form-control'  id='txtComment_" +
            count +
            "' ></td><td><input type='number' name='txtAmount[]' class='form-control total_price text-right' id='txtAmount_" +
            count +
            "' onkeyup='calculationDebitv(" +
            count +
            ")'></td><td><button  class='btn btn-danger red' type='button' value='delete' onclick='deleteRowDebitv(this)'><i class='las la-trash'></i></button></td>";
        document.getElementById(divName).appendChild(newdiv);
        $("#cmbCode_" + count).html(optionval);
        $("#subtype_" + count).attr("disabled", "disabled");
        $("#reltype_" + count).html(reltypeoptionval);
        $("#relvalue_" + count).attr("disabled", "disabled");
        document.getElementById(tabin).focus();
        count++;

        AIZ.plugins.bootstrapSelect('refresh');
    }
}

("use strict");
function calculationDebitv(sl) {
    var gr_tot = 0;
    $(".total_price").each(function () {
        isNaN(this.value) ||
            0 == this.value.length ||
            (gr_tot += parseFloat(this.value));
    });

    $("#grandTotal").val(gr_tot.toFixed(2, 2));
}

("use strict");
function deleteRowDebitv(e) {
    var t = $("#debitAccVoucher > tbody > tr").length;
    if (1 == t) alert("There only one row you can't delete.");
    else {
        var a = e.parentNode.parentNode;
        a.parentNode.removeChild(a);
    }
    calculationDebitv();
}

$(document).on('change', '#cmbCredit', function() {
    var isbank = $('#cmbCredit option:selected').data('isbank');
    if(isbank==1){
        $("#isbanknature").show();
    }else{
        $("#chequeNo").val('');
        $("#chequeDate").val('');
        $("#ishonours").prop('checked', false);
        $("#isbanknature").hide();
    }
});