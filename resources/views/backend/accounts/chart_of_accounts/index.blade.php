@extends('backend.layouts.app')

@section('style')
<style>
    /* Style the overlay to cover the full card */
    #loader-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7); /* Transparent white background */
        z-index: 9999; /* Ensure it appears on top */
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Example loader styling */
    .loader {
        border: 4px solid #f3f3f3; /* Light gray border */
        border-top: 4px solid #3498db; /* Blue color */
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 2s linear infinite;
    }

    /* Animation for loader */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .h-360 {
        height: 360px;
    }
</style>
<link rel="stylesheet" href="{{ static_asset('assets/css/accounts/style-jstree.css') }}" />
<link rel="stylesheet" href="{{ static_asset('assets/css/accounts/style.css') }}" />
@endsection

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{ translate('Chart Of Accounts') }}</h1>
		</div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('contra-vouchers.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Tree View')}}</span>
            </a>
        </div>
	</div>
</div>

<div class="card">
    <form class="" id="sort_contra_vouchers" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Chart Of Accounts') }}</h5>
            </div>
            
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type & Enter') }}">
                </div>
            </div>
        </div>
    </form>
    <div class="card-body">
        <!-- Full Card Loader -->
        <div id="loader-overlay" style="display: none;">
            <div class="loader"></div> <!-- Your loader can go here -->
        </div>
        <div class="row">
            <div class="col-md-6 h-360"> 
                <div id="jstree1" style="display: none;">
                    <ul>
                        @php
                        $visit = array();
                        for ($i = 0; $i < count($coas); $i++)
                        {
                            $visit[$i] = false;
                        }
                        dfs("COA", "0", $coas, $visit, 0);
                        @endphp
                    </ul>
                </div>
            </div> 
            <div class="col-md-6" id="newform"></div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script src="{{ static_asset('assets/js/accounts/jstree.min.js') }}"></script>
<script src="{{ static_asset('assets/js/accounts/account.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    ("use strict");
    function loadData(elm, id) {
        var areaval = $("#" + id).attr("aria-level");
        $.ajax({
            url: "{{ route('chart-of-accounts.selectedform', '') }}/" + id,
            type: "GET",
            dataType: "json",
            success: function (data) {
                $("#newform").html(data);
                $("#btnSave").hide();
                $("#cnodeelem").val(elm);
                $("#clevel").val(areaval);
                $("#btnUndo").hide();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert("Error get data from ajax");
            },
        });
    }

    /*chart of account subtype*/
    ("use strict");
    function isSubType_change(stype) {
        if ($("#" + stype).is(":checked")) {
            $.ajax({
                url: "{{ route('chart-of-accounts.getsubtype') }}",
                type: "GET",
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    if (data == "") {
                        $("#subtypeContent").html("");
                        $("#subtypeContent").hide();
                    } else {
                        $("#subtypeContent").html(data);
                        $("#subtypeContent").show();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert("Error get data from ajax");
                },
            });
        } else {
            $("#subtypeContent").html("");
            $("#subtypeContent").hide();
        }
    }

    ("use strict");
    function newdata(id) {
        var areaid = $("#clevel").val();
        areaid++;
        $.ajax({
            url: "{{ route('chart-of-accounts.newform', '') }}/" + id,
            type: "GET",
            dataType: "json",
            success: function (data) {
                console.log(data.rowdata);
                var htmlcontent = "";
                var headlabel = data.headlabel;
                $("#txtHeadCode").val(data.headcode);
                document.getElementById("txtHeadName").value = "";
                $("#txtPHead").val(data.rowdata.head_name);
                $("#txtHeadLevel").val(headlabel);
                $("#txtPHeadCode").val(data.rowdata.head_code);
                $("#clevel").val(areaid);
                $("#btnSave").prop("disabled", false);
                $("#btnSave").show();
                $("#btnUpdate").hide();
                $("#btnUndo").removeAttr("onclick");
                $("#btnUndo").attr(
                    "onclick",
                    "loadData('" +
                        data.rowdata.head_code +
                        "_anchor','" +
                        data.rowdata.head_code +
                        "')"
                );
                $("#btnUndo").show();
                if (headlabel == 3) {
                    htmlcontent +=
                        '<input type="checkbox" value="1" name="IsActive" checked="checked" id="IsActive" size="28"/><label for="IsActive">&nbsp;Is Active</label> &nbsp;&nbsp; ';

                    if (
                        data.rowdata.head_type == "A" ||
                        data.rowdata.head_type == "L"
                    ) {
                        if (data.rowdata.head_type == "A") {
                            htmlcontent +=
                                '<input type="checkbox" name="isStock" value="1" id=isStock" size="28"  onchange="isStock_change()"/><label for=isStock">&nbsp;Is Stock</label> &nbsp;&nbsp; ';
                        }
                        htmlcontent +=
                            '<input type="checkbox" name="isFixedAssetSch" value="1" id="isFixedAssetSch" size="28"  onchange="isFixedAssetSch_change(\'isFixedAssetSch\',\'' +
                            data.rowdata.head_type +
                            '\')"/><label for="isFixedAssetSch">&nbsp;Is Fixed Asset </label> &nbsp;&nbsp; ';
                    }
                    $("#btnNew").show();
                } else if (headlabel == 4) {
                    htmlcontent +=
                        '<input type="checkbox" value="1" name="IsActive" id="IsActive" checked="checked" size="28"/><label for="IsActive">&nbsp;Is Active</label> &nbsp;&nbsp; ';
                    if (
                        data.rowdata.head_type == "A" ||
                        data.rowdata.head_type == "L"
                    ) {
                        if (data.rowdata.head_type == "A") {
                            htmlcontent +=
                                '<input type="checkbox" name="isStock" value="1" id=isStock" size="28"  onchange="isStock_change()"/><label for=isStock">&nbsp;Is Stock</label> &nbsp;&nbsp; ';
                            htmlcontent +=
                                '<br/><input type="checkbox" name="isCashNature" value="1" id="isCashNature" size="28"  onchange="isCashNature_change()"/><label for="isCashNature">&nbsp;Is Cash Nature</label> &nbsp;&nbsp; ';
                            htmlcontent +=
                                '<input type="checkbox" name="isBankNature" value="1" id="isBankNature" size="28"  onchange="isBankNature_change()"/><label for="isBankNature">&nbsp;Is Bank Nature</label> &nbsp;&nbsp; ';
                        }
                        htmlcontent +=
                            '<br/><input type="checkbox" name="isFixedAssetSch" value="1" id="isFixedAssetSch" size="28"  onchange="isFixedAssetSch_change(\'isFixedAssetSch\',\'' +
                            data.rowdata.head_type +
                            '\')"/><label for="isFixedAssetSch">&nbsp;Is Fixed Asset </label> &nbsp;&nbsp; ';
                    }
                    htmlcontent +=
                        '<input type="checkbox" name="isSubType" value="1" id="isSubType" size="28"  onchange="isSubType_change(\'isSubType\')"/><label for="isSubType">&nbsp;Is Sub Type</label> &nbsp;&nbsp; ';
                    $("#btnNew").hide();
                }

                $("#innerCheck").html(htmlcontent);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert("Error get data from ajax");
            },
        });
    }

    ("use strict");
    function delDataAcc(id) {
        var confm = confirm(
            "Are you sure you want to delete this account? If you delete this account all transation with this account will be deleted!"
        );
        if (confm) {
            $.ajax({
                url: "{{ route('chart-of-accounts.destroy', '') }}/" + id,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if (data.status == "success") {
                        $("#" + id).remove();
                        $("#newform").html("");
                        AIZ.plugins.notify('success', 'Account has been delete successfully!');
                    } else {
                        AIZ.plugins.notify('warning', 'You can not delete this account because some transation being occure related this account!');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    AIZ.plugins.notify('error', 'Error get data from ajax');
                },
            });
        } else {
            return false;
        }
    }

    ("use strict");
    function validate(lid) {
        var nameVal = $("#txtHeadName").val();
        if (nameVal == "" || nameVal == null) {
            $("#" + lid).html("Please Enter Head Name");
            $("#" + lid).css("color", "red");
            $("#" + lid).show();
            return false;
        } else {
            var nhtm = "";
            var elid = $("#cnodeelem").val();
            var areaid = $("#clevel").val();
            var formData = $("#coaform").serialize();
            var base_url = $("#base_url").val();
            var hid = $("#txtHeadCode").val();
            var phname = $("#txtPHead").val();
            var hname = $("#txtHeadName").val();
            var pid = $("#txtPHeadCode").val();
            $.ajax({
                url: "{{ route('chart-of-accounts.store') }}",
                type: "POST",
                dataType: "json",
                data: formData,
                success: function (data) {
                    var content = data.info;
                    if (data.type == "new") {
                        if (
                            $("#" + pid)
                                .find("ul")
                                .children().length > 0
                        ) {
                            nhtm +=
                                '<li role="treeitem" aria-selected="true" aria-level="' +
                                areaid +
                                '" aria-labelledby="10101_anchor" id="' +
                                hid +
                                '" class="jstree-node  jstree-leaf jstree-last">';
                            nhtm +=
                                '<i class="jstree-icon jstree-ocl" role="presentation"></i>';
                            nhtm +=
                                '<a class="jstree-anchor jstree-clicked" href="javascript:" tabindex="-1" onclick="loadData( this.id,' +
                                hid +
                                ')" id="' +
                                hid +
                                '_anchor" style="touch-action: none;">';
                            nhtm +=
                                '<i class="jstree-icon jstree-themeicon las la-folder jstree-themeicon-custom" role="presentation"></i>' +
                                hname +
                                "</a></li>";

                            $("#" + pid)
                                .find("ul")
                                .find("li")
                                .last()
                                .removeClass("jstree-last");
                            $("#" + pid)
                                .find("ul")
                                .append(nhtm);
                        } else {
                            nhtm +=
                                '<li role="treeitem" aria-selected="true" aria-level="' +
                                areaid +
                                '" aria-labelledby="10101_anchor" id="' +
                                hid +
                                '" class="jstree-node  jstree-leaf jstree-last">';
                            nhtm +=
                                '<i class="jstree-icon jstree-ocl" role="presentation"></i>';
                            nhtm +=
                                '<a class="jstree-anchor jstree-clicked" href="javascript:" tabindex="-1" onclick="loadData( this.id,' +
                                hid +
                                ')" id="' +
                                hid +
                                '_anchor" style="touch-action: none;">';
                            nhtm +=
                                '<i class="jstree-icon jstree-themeicon las la-folder jstree-themeicon-custom" role="presentation"></i>' +
                                hname +
                                "</a></li>";
                            $(nhtm).appendTo("#" + pid);
                        }
                        $("#cnodeelem").val(hid + "_anchor");
                        $("#clevel").val(areaid);
                    } else {
                        //  nhtm +=  '<a class="jstree-anchor" href="javascript:" tabindex="-1" onclick="loadData( this.id,'+hid+')" id="'+elid+'"><i class="jstree-icon jstree-themeicon las la-folder jstree-themeicon-custom" role="presentation"></i>'+hname+'</a>';
                        nhtm +=
                            '<i class="jstree-icon jstree-themeicon las la-folder jstree-themeicon-custom" role="presentation"></i>' +
                            hname;

                        $("#" + elid).html(nhtm);
                        $("#" + elid).removeAttr("onclick");
                        $("#" + elid).attr(
                            "onclick",
                            "loadData(this.id,'" + hid + "')"
                        );
                    }
                    $("#btnSave").hide();
                    $("#btnUpdate").show();
                    $("#btnDelete").show();
                    $("#btnNew").removeAttr("onclick");
                    $("#btnDelete").removeAttr("onclick");
                    $("#btnNew").attr("onclick", "newdata(" + hid + ")");
                    $("#btnDelete").attr("onclick", "delDataAcc(" + hid + ")");
                    AIZ.plugins.notify('success', data.message);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    AIZ.plugins.notify('error', 'Please try again!');
                },
            });
            $("#" + lid).html("");
            $("#" + lid).hide();
            return false;
        }
    }

    $(document).ready(function() {
        // Show the loader while content is loading
        $('#loader-overlay').show();

        setTimeout(function() {
            $('#loader-overlay').hide();
            $('#jstree1').closest('.col-md-6').removeClass('h-360');
            $('#jstree1').show();
            
        }, 2000);
    });
</script>

@endsection