@extends('backend.layouts.app')

@section('content')
<link rel="stylesheet" href="{{ static_asset('assets/css/style.min.css') }}" />
<style>
    .fyear {
        color: #4a0566!important;
        font-size: 18px;
        font-weight: bold;
        padding-top: 20px;
        padding-left: 30px;
    }
    table.coaTable tr td { text-align: left; }
    table.coaTable tr td:nth-child(1) {
        text-align: left;
        width: 30%;
    }
    table.coaTable tr td:nth-child(2) {
        text-align: left;
        width: 70%;
    }
    table.coaTable tr td:nth-child(2) input,
    table.coaTable tr td:nth-child(2) select {
        min-width: 90%;
        max-width: 100%;
        margin-bottom: 12px;
        padding: 6px 10px;
        border: 1px solid #888;
    }
    table.coaTable tr td:nth-child(2) input[type="checkbox"] {
        min-width: 15px;
        max-width: 15px;
        margin-bottom: 16px;
        padding: 0;
        border: 1px solid #888;
        text-align: left;
        display: inline-block;
    }
    table.coaTable tr td:nth-child(2) input[type="button"],
    table.coaTable tr td:nth-child(2) input[type="submit"] {
        min-width: 100px;
        max-width: 150px;
        margin-bottom: 16px;
        padding: 10px 40px;
        border: 1px solid #37a000;
        background-color: #37a000;
        color: white;
        text-align: center;
        display: inline-block;
    }
    .custom-modal-dialog {
        max-width: 76%;
        min-width: 76%;
    }
    table.general_ledger_report_tble td,
    table.general_ledger_report_tble th {
        padding: 6px 10px;
    }

    .sticky-form {
    position: -webkit-sticky; /* For Safari */
    position: sticky;
    top: 20px; /* Adjust the distance from the top */
    background-color: #f9f9f9;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    max-width: 100%;
    height: 80%;
}

.sticky-form {
    position: sticky;
    top: 20px;
    background-color: #f9f9f9;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 100%;
    margin-bottom: 30px;
    height: 80%;

}

.sticky-form h5 {
    font-size: 18px;
    margin-bottom: 20px;
}

.sticky-form .form-group {
    margin-bottom: 15px;
}

.sticky-form input[type="text"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.checkbox-group .form-check {
    margin-right: 15px;
}

.sticky-form .btn {
    width: 100%;
    padding: 10px;
    font-size: 16px;
}

.sticky-form .form-check-label {
    margin-left: 5px;
}

.form-group.d-flex {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    align-items: center;
}
.bg-tree{
 background-color: #f9f9f9!important;
 padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}


</style>

<div class="row bg-white p-2">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading  bg-soft-info  p-2">
                <h4 class="panel-title">
                    <i class="las la-folder-open aiz-side-nav-icon"></i>  {{ $title }}
                </h4>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div id="jstree1" class="bg-tree">
                            <ul style="font-size: 14px!important; font-weight: bold; color:#ba34eb!important ">
                                @php
                                $visit = array_fill(0, count($userList), false);
                                (new \App\Models\AccCoa)->dfs("COA", "0", $userList, $visit, 0);
                                @endphp
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6" id="newform"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="base_url" value="{{ url('/') }}" name="base_url">

<script>
    var base_url = $('#base_url').val();

    function loadData(headCode) {
    $.ajax({
        url: "{{ route('account.headDetails') }}", 
        method: "GET",
        data: { headCode: headCode },
        success: function(response) {
            let headDetails = response.headDetails; // Access headDetails from response
            let subType = response.subType; // Access subType from response

            let isChecked = headDetails.is_active ? 'checked' : '';
            let isFixedAssetChecked = headDetails.is_fixed_asset_sch ? 'checked' : '';
            let formHtml = `
                <div class="sticky-form  bg-soft-warning">
                    <h5>Details for: ${headDetails.head_name}</h5>
                    <form id="headDetailsForm" method="POST" action="{{ route('accounts.store') }}">
                         @csrf
                        <div class="form-group">
                            <label for="HeadName">Head Name:</label>
                            <input type="text" class="form-control" id="HeadName" name="HeadName" value="${headDetails.head_name}">
                        </div>
                        <div class="form-group">
                            <label for="HeadCode">Head Code:</label>
                            <input type="text" class="form-control" id="HeadCode" name="HeadCode" value="${headDetails.head_code}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="PHeadName">Parent Head Name:</label>
                            <input readonly type="text" class="form-control" id="PHeadName" name="PHeadName" value="${headDetails.pre_head_name}">
                        </div>
                        <div class="form-group">
                            <label for="PHeadCode">Parent Head Code:</label>
                            <input type="text" class="form-control" id="PHeadCode" name="PHeadCode" value="${headDetails.pre_head_code}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="HeadLevel">HeadLevel</label>
                            <input type="text" class="form-control" id="HeadLevel" name="HeadLevel" value="${headDetails.head_level}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="HeadType">HeadType</label>
                            <input type="text" class="form-control" id="HeadType" name="HeadType" value="${headDetails.head_type}" readonly>
                        </div>
            `;

            // Only add fields for HeadLevel 4
            if (headDetails.head_level == 4) {
                formHtml += `
                    <div class="form-group">
                        <label for="noteNo">Note No:</label>
                        <input type="text" class="form-control" id="noteNo" name="noteNo" value="${headDetails.note_no || ''}">
                    </div>
                    <div id="fixedAssetFields" style="display: ${headDetails.is_fixed_asset_sch ? 'block' : 'none'};">
                        <div class="form-group">
                            <label for="assetCode">Fixed Asset Code:</label>
                            <input type="text" class="form-control" id="assetCode" name="assetCode" value="${headDetails.asset_code || ''}">
                        </div>
                        <div class="form-group">
                            <label for="DepreciationRate">Depreciation Rate %:</label>
                            <input type="text" class="form-control" id="DepreciationRate" name="DepreciationRate" value="${headDetails.depreciation_rate || ''}">
                        </div>
                    </div>
                    <div id="subTypeFields" style="display: ${headDetails.is_sub_type ? 'block' : 'none'};">
                        <div class="form-group">
                            <label for="subType">Sub Type:</label>
                            <select class="form-control" id="subType" name="subType">
                                <option value="">Select Sub Type</option>
                                ${subType.map(type => `<option value="${type.id}" ${headDetails.sub_type == type.id ? 'selected' : ''}>${type.sub_type_name}</option>`).join('')}
                            </select>
                        </div>
                    </div>
                `;
            }

            // Common form group for Active checkbox for HeadLevel > 1
            if (headDetails.head_level > 1) {
                formHtml += `
                    <div class="form-group d-flex justify-content-between align-items-center checkbox-group">
                        <div class="form-check">
                            <input checked type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"  ${isChecked}>
                            <label class="form-check-label" for="is_active">Is Active</label>
                        </div>
                    </div>
                `;
            }

            // Add checkboxes for HeadLevel == 4
            if (headDetails.head_level == 4 && (headDetails.head_type != 'E' && headDetails.head_type != 'I'  && headDetails.head_type != 'L')) {
                formHtml += `
                    <div class="form-group d-flex justify-content-between align-items-center checkbox-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="IsGL" name="IsGL" value="1"  ${headDetails.is_gl ? 'checked' : ''}>
                            <label class="form-check-label" for="IsGL">Is GL</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="IsTransaction" name="IsTransaction" value="1"  ${headDetails.is_transaction ? 'checked' : ''}>
                            <label class="form-check-label" for="IsTransaction">Is Transaction</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="isStock" name="isStock" value='1' ${headDetails.is_stock ? 'checked' : ''}>
                            <label class="form-check-label" for="isStock">Is Stock</label>
                        </div>
                    </div>
                    <div class="form-group d-flex justify-content-between align-items-center checkbox-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="isFixedAssetSch" name="isFixedAssetSch" ${isFixedAssetChecked}>
                            <label class="form-check-label" for="isFixedAssetSch">Is Fixed Asset</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="isBankNature" name="isBankNature" value="1"  ${headDetails.is_bank_nature ? 'checked' : ''}>
                            <label class="form-check-label" for="isBankNature">Is Bank Nature</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="isCashNature" name="isCashNature" value="1"  ${headDetails.is_cash_nature ? 'checked' : ''}>
                            <label class="form-check-label" for="isCashNature">Is Cash Nature</label>
                        </div>
                    </div>
                `;
            }

            if (headDetails.head_level == 4) {
                formHtml += `
                    <div class="form-group d-flex justify-content-between align-items-center checkbox-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="isSubType" name="isSubType" ${headDetails.is_sub_type ? 'checked' : ''}>
                            <label class="form-check-label" for="isSubType">Is Sub Type</label>
                        </div>
                    </div>
                `;
            }
            if (headDetails.head_level == 4 && headDetails.head_type == 'L') {
                formHtml += `
                    <div class="form-group d-flex justify-content-between align-items-center checkbox-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="isFixedAssetSch" name="isFixedAssetSch" ${isFixedAssetChecked}>
                            <label class="form-check-label" for="isFixedAssetSch">Is Fixed Asset</label>
                        </div>
                    </div>
                `;
            }

            // Buttons for HeadLevel > 1 and < 4
            if (headDetails.head_level > 1 && headDetails.head_level < 4) {
                formHtml += `
                <div class="form-group d-flex" id="actionButtons">
                    <button type="button" class="btn btn-sm btn-success" id="saveButton" style="display:none;" onclick="saveHeadDetails()">Save</button>
                    <button type="button" class="btn btn-sm btn-dark" id="backButton" style="display:none;" onclick="backToDefault()">Back</button>
                    <button type="button" class="btn btn-sm btn-info" id="newButton">New</button>
                    <button type="button" class="btn btn-sm btn-warning text-white" id="updateButton" onclick="updateHeadDetails()">Update</button>
                    <button type="button" class="btn btn-sm btn-danger" id="deleteButton" onclick="deleteHeadDetails()">Delete</button>
                </div>
            `;
            }

            // Update and Delete buttons for HeadLevel == 4
            if (headDetails.head_level == 4) {
                formHtml += `
                    <div class="form-group d-flex">
                        <button type="button" class="btn btn-sm btn-warning text-white" onclick="updateHeadDetails()">Update</button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteHeadDetails()">Delete</button>
                    </div>
                `;
            }

            formHtml += `</form></div>`;

            // Insert the form HTML into the DOM
            $('#newform').html(formHtml);

            // Event listener for the isFixedAssetSch checkbox
            $('#isFixedAssetSch').change(function() {
                $('#fixedAssetFields').toggle(this.checked);
            });

           $('#isSubType').change(function() {
                $('#subTypeFields').toggle(this.checked);  // Toggle visibility based on checkbox
            });

         
            // Event listener for the new button
            $('#newButton').click(function() {
                // Set the PHeadName input to the current HeadName
                $('#PHeadName').val($('#HeadName').val()); 
                $('#PHeadCode').val($('#HeadCode').val()); 
                $('#HeadType').val($('#HeadType').val()); 
                $('#PHeadName').prop('readonly', true);// Set HeadName to PHeadName
                $('#HeadName').val(''); // Set HeadName to PHeadName
                let currentValue = parseInt($('#HeadLevel').val(), 10) || 0; // Fallback to 0 if the value is not a number
                $('#HeadLevel').val(currentValue + 1);
                // Generate and set a new HeadCode
                $('#HeadCode').val(generateUniqueHeadCode());
                // Show save and back buttons
                $('#saveButton').show();
                $('#backButton').show();
                $('#updateButton').hide();
                $('#newButton').hide();
                $('#deleteButton').hide();
            });

            // $('#updateButton').click(function() {
            // // Call the function to update head details
            // updateHeadDetails();
            // });
        },
        error: function(error,xhr) {
            console.error('Error loading data:', error);
            console.error("Error saving head details:", xhr.responseText);
            $('#newform').html('<p>Error loading data. Please try again later.</p>');
        }
    });
}

function generateUniqueHeadCode() {
    $.ajax({
        url: "{{ route('account.getLastHeadCode') }}", // Replace with your actual endpoint
        method: 'GET',
        success: function(response) {
            let lastHeadCode = response.lastHeadCode; // Get lastHeadCode from response
            // let lastHeadCode = $("#HeadCode").val();
            // Logic to increment the last used HeadCode
            // Assuming HeadCode is a numeric string
            let newHeadCode = parseInt(lastHeadCode, 10) + 1; // Increment by 10101
            $("#HeadCode").val(newHeadCode.toString()); // Update the input field with newHeadCode
        },
        error: function(xhr, status, error) {
            console.error('Error fetching last HeadCode:', error);
            // Handle error, e.g., set a default HeadCode or show a message
        }
    });
}

// Automatically generate HeadCode on page load
$(document).ready(function() {
    generateUniqueHeadCode();
});


function saveHeadDetails() {
    let formData = $('#headDetailsForm').serialize(); // Serialize form data
    $.ajax({
        url: "{{ route('accounts.store') }}",
        method: "POST",
        data: formData,
        success: function(response) {
            alert(response.message); // Show success message
            loadData($('#HeadCode').val()); // Reload data
            location.reload(); // Reload or redirect after insert
        },
        error: function(xhr) {
            console.error(xhr);
            alert("An error occurred while saving data. Please try again.");
        }
    });
}

function backToDefault() {
    // Clear the form fields
    $('#headDetailsForm')[0].reset();
    // Show default buttons
    $('#actionButtons').find('button').show();
    // Hide new buttons
    $('#saveButton').hide();
    $('#backButton').hide();
}

function updateHeadDetails() {
    // Gather the form data
    let formData = {
        HeadName: $('#HeadName').val(),
        HeadCode: $('#HeadCode').val(),
        PHeadName: $('#PHeadName').val(),
        PHeadCode: $('#PHeadCode').val(),
        HeadLevel: $('#HeadLevel').val(),
        HeadType: $('#HeadType').val(),
        noteNo: $('#noteNo').val(),
        assetCode: $('#assetCode').val(),
        DepreciationRate: $('#DepreciationRate').val(),
        subType: $('#subType').val(),
        is_active: $('#is_active').is(':checked') ? 1 : 0,
        IsGL: $('#IsGL').is(':checked') ? 1 : 0,
        IsTransaction: $('#IsTransaction').is(':checked') ? 1 : 0,
        isStock: $('#isStock').is(':checked') ? 1 : 0,
        isFixedAssetSch: $('#isFixedAssetSch').is(':checked') ? 1 : 0,
        isBankNature: $('#isBankNature').is(':checked') ? 1 : 0,
        isCashNature: $('#isCashNature').is(':checked') ? 1 : 0,
        isSubType: $('#isSubType').is(':checked') ? 1 : 0,
    };

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: "POST",
        url: `{{ route('accounts.update', ':HeadCode') }}`.replace(':HeadCode', $('#HeadCode').val()),
        data: formData,
        cache: false,
        success: function(response) {
            alert(response.message); // Show success message
            loadData($('#HeadCode').val()); // Reload data
        },
        error: function(xhr) {
            console.error(xhr);
            alert("An error occurred while saving data. Please try again.");
        }
    });
}

// Call updateHeadDetails on form submission
$(document).on('submit', '#headDetailsForm', function(e) {
    e.preventDefault(); // Prevent the default form submission
    updateHeadDetails(); // Call the update function
});




function deleteHeadDetails() {
    let headCode = $('#HeadCode').val(); // Get the HeadCode from the form or input field

    if (confirm("Are you sure you want to delete this account? This action cannot be undone.")) {
        $.ajax({
            url: `{{ route('accounts.destroy', ':HeadCode') }}`.replace(':HeadCode', $('#HeadCode').val()),

            method: "POST",
            data: {
                _token: "{{ csrf_token() }}" // CSRF token is mandatory for DELETE requests
            },
            success: function(response) {
                alert(response.message); // Show success message
                location.reload(); // Reload or redirect after deletion
            },
            error: function(xhr, status, error) {
                console.error("Error deleting account: ", error);
                alert("Failed to delete account. Please try again.");
            }
        });
    }
}


    
</script>
@endsection

@section('script')
<!-- <script src="{{ static_asset('assets/js/account.js') }}"></script> -->
<script src="{{ static_asset('assets/js/jstree.js') }}"></script>
<script src="{{ static_asset('assets/js/jstree.min.js') }}"></script>
@endsection
