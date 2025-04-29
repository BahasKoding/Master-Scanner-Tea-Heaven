@extends('layouts.main')

@section('title', 'Route Plan')
@section('breadcrumb-item', $item)
@section('breadcrumb-item-active', $itemActive)

@section('css')
<!-- [Page specific CSS] start -->
<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">
<!-- [Page specific CSS] end -->
@endsection

@section('content')
<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <h5>Route Plan</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Left Table (Employees) -->
                <div class="col-md-4">
                    <h6>Employees</h6>
                    <div class="table-responsive">
                        <table id="employees-table" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Code</th>
                                    <th>Employee Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesPersons as $index => $salesPerson)
                                <tr>
                                    <td>
                                        <input type="radio" name="salesPerson" id="salesPerson{{ $salesPerson->id }}" 
                                               value="{{ $salesPerson->id }}" 
                                               {{ $index === 0 ? 'checked' : '' }}
                                               onchange="console.log('Radio changed:', this.value);">
                                    </td>
                                    <td>{{ $salesPerson->code }}</td>
                                    <td>{{ $salesPerson->name }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Right Table (Route Plan) -->
                <div class="col-md-8">
                    <h6>Route Plan Details</h6>
                    <button type="button" class="btn btn-primary mb-3" id="addRoutePlanBtn">
                        Add New Route Plan
                    </button>
                    <div class="table-responsive">
                        <table id="route-plan-table" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Mon</th>
                                    <th>Tue</th>
                                    <th>Wed</th>
                                    <th>Thu</th>
                                    <th>Fri</th>
                                    <th>Sat</th>
                                    <th>W1</th>
                                    <th>W2</th>
                                    <th>W3</th>
                                    <th>W4</th>
                                    <th>Week</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Route plan data will be loaded here dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Route Plan Modal -->
<div class="modal fade" id="addRoutePlanModal" tabindex="-1" aria-labelledby="addRoutePlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRoutePlanModalLabel">Add New Route Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addRoutePlanForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="add_sales_person_id" name="sales_person_id">
                    
                    <div class="mb-3">
                        <label for="add_description" class="form-label">Description</label>
                        <select class="form-control" id="add_description" name="description[]" multiple>
                            <option value="Jakarta">Jakarta</option>
                            <option value="Surabaya">Surabaya</option>
                            <option value="Bandung">Bandung</option>
                            <option value="Medan">Medan</option>
                            <option value="Semarang">Semarang</option>
                            <option value="Makassar">Makassar</option>
                            <option value="Palembang">Palembang</option>
                            <option value="Tangerang">Tangerang</option>
                            <option value="Depok">Depok</option>
                            <option value="Bekasi">Bekasi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Day Of Visits</label>
                        <select class="form-control" id="add_days" name="days[]" multiple>
                            <option value="mon">Monday</option>
                            <option value="tue">Tuesday</option>
                            <option value="wed">Wednesday</option>
                            <option value="thu">Thursday</option>
                            <option value="fri">Friday</option>
                            <option value="sat">Saturday</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Weeks</label>
                        <select class="form-control" id="add_weeks" name="weeks[]" multiple>
                            <option value="w1">Week 1</option>
                            <option value="w2">Week 2</option>
                            <option value="w3">Week 3</option>
                            <option value="w4">Week 4</option>
                        </select>
                    </div>
                    
                    <!-- Hidden inputs -->
                    <input type="hidden" name="add_mon" value="false">
                    <input type="hidden" name="add_tue" value="false">
                    <input type="hidden" name="add_wed" value="false">
                    <input type="hidden" name="add_thu" value="false">
                    <input type="hidden" name="add_fri" value="false">
                    <input type="hidden" name="add_sat" value="false">
                    <input type="hidden" name="add_w1" value="false">
                    <input type="hidden" name="add_w2" value="false">
                    <input type="hidden" name="add_w3" value="false">
                    <input type="hidden" name="add_w4" value="false">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="saveRoutePlanBtn" class="btn btn-primary">Save Route Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Route Plan Modal -->
<div class="modal fade" id="editRoutePlanModal" tabindex="-1" aria-labelledby="editRoutePlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoutePlanModalLabel">Edit Route Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRoutePlanForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_route_plan_id" name="id">
                    <input type="hidden" id="edit_sales_person_id" name="sales_person_id">
                    
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <select class="form-control" id="edit_description" name="description[]" multiple>
                            <option value="Jakarta">Jakarta</option>
                            <option value="Surabaya">Surabaya</option>
                            <option value="Bandung">Bandung</option>
                            <option value="Medan">Medan</option>
                            <option value="Semarang">Semarang</option>
                            <option value="Makassar">Makassar</option>
                            <option value="Palembang">Palembang</option>
                            <option value="Tangerang">Tangerang</option>
                            <option value="Depok">Depok</option>
                            <option value="Bekasi">Bekasi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Day Of Visits</label>
                        <select class="form-control" id="edit_days" name="days[]" multiple>
                            <option value="mon">Monday</option>
                            <option value="tue">Tuesday</option>
                            <option value="wed">Wednesday</option>
                            <option value="thu">Thursday</option>
                            <option value="fri">Friday</option>
                            <option value="sat">Saturday</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Weeks</label>
                        <select class="form-control" id="edit_weeks" name="weeks[]" multiple>
                            <option value="w1">Week 1</option>
                            <option value="w2">Week 2</option>
                            <option value="w3">Week 3</option>
                            <option value="w4">Week 4</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="updateRoutePlanBtn" class="btn btn-primary">Update Route Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection


@section('scripts')
@parent
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ URL::asset('build/js/plugins/choices.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    var routePlanTable = $('#route-plan-table').DataTable({
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "columnDefs": [
            {
                "targets": 0,
                "orderable": false,
                "width": "30px"
            }
        ]
    });

    // Load initial route plan data for the first selected sales person
    var initialSalesPersonId = $('input[name="salesPerson"]:checked').val();
    $('#add_sales_person_id').val(initialSalesPersonId);
    console.log("Initial sales person ID:", initialSalesPersonId);
    loadRoutePlanData(initialSalesPersonId);

    // Handle sales person selection
    $(document).on('change', 'input[name="salesPerson"]', function() {
        var selectedId = $(this).val();
        console.log("Selected sales person ID:", selectedId);
        $('#add_sales_person_id').val(selectedId);
        loadRoutePlanData(selectedId);
    });

    // Add New Route Plan button
    $('#addRoutePlanBtn').on('click', function() {
        var selectedSalesPersonId = $('#add_sales_person_id').val() || $('input[name="salesPerson"]:checked').val();
        console.log("Selected sales person ID when adding new route plan:", selectedSalesPersonId);
        
        if (!selectedSalesPersonId) {
            // If still no selection, select the first one
            selectedSalesPersonId = $('input[name="salesPerson"]:first').val();
            $('input[name="salesPerson"]:first').prop('checked', true).trigger('change');
        }
        
        if (!selectedSalesPersonId) {
            toast('Error', 'Please select a sales person first', 'error');
            return;
        }
        
        $('#add_sales_person_id').val(selectedSalesPersonId);
        $('#addRoutePlanModal').modal('show');
    });

    // Initialize choices when modal is shown
    $('#addRoutePlanModal').on('shown.bs.modal', function () {
        console.log("Add Route Plan modal shown");
        setTimeout(function() {
            initializeAddChoices();
        }, 100);
    });

    // Add Route Plan
    $('#saveRoutePlanBtn').on('click', function() {
        var formData = new FormData($('#addRoutePlanForm')[0]);
        var selectedSalesPersonId = $('#add_sales_person_id').val();
        formData.set('sales_person_id', selectedSalesPersonId);
        
        $.ajax({
            url: '{{ route("route-plan.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#addRoutePlanModal').modal('hide');
                    toast('Success', response.message, 'success');
                    $('#addRoutePlanForm')[0].reset();
                    initializeAddChoices();
                    
                    // Refresh the table for the current sales person
                    loadRoutePlanData($('input[name="salesPerson"]:checked').val());
                } else {
                    toast('Error', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred';
                toast('Error', errorMessage, 'error');
            }
        });
    });

   // Edit Route Plan
    $(document).on('click', '.edit-routeplan', function() {
        var routePlanId = $(this).data('id');
        console.log("Editing route plan with ID:", routePlanId);
        if (!routePlanId) {
            console.error("Route plan ID is undefined");
            toast('Error', 'Invalid route plan ID', 'error');
            return;
        }
        
        // Reset form fields
        $('#editRoutePlanForm')[0].reset();
        
        // Destroy existing Choices instances
        if (editDescriptionChoice) editDescriptionChoice.destroy();
        if (editDaysChoice) editDaysChoice.destroy();
        if (editWeeksChoice) editWeeksChoice.destroy();
        
        $.ajax({
            url: '/route-plan/' + routePlanId + '/edit',
            type: 'GET',
            success: function(response) {
                console.log("Edit response:", response);
                if (!response || !response.id) {
                    console.error("Invalid response from server");
                    toast('Error', 'Invalid response from server', 'error');
                    return;
                }
                $('#edit_route_plan_id').val(response.id);
                $('#edit_sales_person_id').val(response.sales_person_id);
                
                // Initialize Choices and set values
                editDescriptionChoice = initializeEditChoice('#edit_description');
                editDescriptionChoice.setChoiceByValue(response.description.split(', '));
                
                editDaysChoice = initializeEditChoice('#edit_days');
                editDaysChoice.setChoiceByValue(getDaysArray(response));
                
                editWeeksChoice = initializeEditChoice('#edit_weeks');
                editWeeksChoice.setChoiceByValue(getWeeksArray(response));
                
                $('#editRoutePlanModal').modal('show');
            },
            error: function(xhr) {
                console.log("Edit error:", xhr);
                var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred';
                toast('Error', errorMessage, 'error');
            }
        });
    });
    // Update Route Plan
   // Update Route Plan
    $('#updateRoutePlanBtn').on('click', function() {
        var formData = new FormData($('#editRoutePlanForm')[0]);
        var routePlanId = $('#edit_route_plan_id').val();
        var salesPersonId = $('#edit_sales_person_id').val();
        console.log("Updating route plan with ID:", routePlanId);
        console.log("Sales Person ID:", salesPersonId);
        if (!routePlanId || !salesPersonId) {
            console.error("Route plan ID or Sales Person ID is undefined");
            toast('Error', 'Invalid route plan or sales person ID', 'error');
            return;
        }
        
        formData.append('sales_person_id', salesPersonId);
        
        $.ajax({
            url: '/route-plan/' + routePlanId,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log("Update response:", response);
                if (response.status === 'success') {
                    $('#editRoutePlanModal').modal('hide');
                    toast('Success', response.message, 'success');
                    
                    // Refresh the table for the current sales person
                    loadRoutePlanData($('input[name="salesPerson"]:checked').val());
                } else {
                    toast('Error', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.log("Update error:", xhr);
                var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred';
                toast('Error', errorMessage, 'error');
            }
        });
    });
    // Delete Route Plan
    $(document).on('click', '.delete-routeplan', function() {
        var routePlanId = $(this).data('id');
        if (confirm('Are you sure you want to delete this route plan?')) {
            $.ajax({
                url: '/route-plan/' + routePlanId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'success') {
                        toast('Success', response.message, 'success');
                        // Refresh the table for the current sales person
                        loadRoutePlanData($('input[name="salesPerson"]:checked').val());
                    } else {
                        toast('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred';
                    toast('Error', errorMessage, 'error');
                }
            });
        }
    });

    function loadRoutePlanData(salesPersonId) {
        $.ajax({
            url: '/route-plan/data',
            type: 'GET',
            data: { sales_person_id: salesPersonId },
            success: function(response) {
                routePlanTable.clear().draw();
                response.data.forEach(function(routePlan) {
                    addNewRow(routePlan);
                });
            },
            error: function(xhr) {
                var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred';
                toast('Error', errorMessage, 'error');
            }
        });
    }

    function addNewRow(data) {
        var newRow = [
            '<button class="btn btn-sm btn-outline-primary toggle-details" data-id="' + data.id + '">+</button>',
            'R' + String(data.id).padStart(3, '0'),
            data.description,
            data.mon ? '✓' : '',
            data.tue ? '✓' : '',
            data.wed ? '✓' : '',
            data.thu ? '✓' : '',
            data.fri ? '✓' : '',
            data.sat ? '✓' : '',
            data.w1 ? '✓' : '',
            data.w2 ? '✓' : '',
            data.w3 ? '✓' : '',
            data.w4 ? '✓' : '',
            data.week,
            '<button class="btn btn-sm btn-primary edit-routeplan" data-id="' + data.id + '">Edit</button> ' +
            '<button class="btn btn-sm btn-danger delete-routeplan" data-id="' + data.id + '">Delete</button>'
        ];
        
        var row = routePlanTable.row.add(newRow).draw(false).node();
        $(row).attr('data-id', data.id);
    }

    function formatDetails(data) {
        return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px; width: 100%;" class="table table-bordered">' +
            '<thead>' +
                '<tr>' +
                    '<th>Customer</th>' +
                    '<th>Customer Name</th>' +
                    '<th>Address</th>' +
                    '<th>Latitude</th>' +
                    '<th>Longitude</th>' +
                    '<th>Visit Date</th>' +
                    '<th>Status</th>' +
                    '<th>Description</th>' +
                '</tr>' +
            '</thead>' +
            '<tbody>' +
                // Placeholder for future data rows
                '<tr><td colspan="8">Data will be populated here</td></tr>' +
            '</tbody>' +
        '</table>';
    }

    function getDaysString(data) {
        var days = [];
        if (data.mon) days.push('Monday');
        if (data.tue) days.push('Tuesday');
        if (data.wed) days.push('Wednesday');
        if (data.thu) days.push('Thursday');
        if (data.fri) days.push('Friday');
        if (data.sat) days.push('Saturday');
        return days.join(', ');
    }

    function getWeeksString(data) {
        var weeks = [];
        if (data.w1) weeks.push('Week 1');
        if (data.w2) weeks.push('Week 2');
        if (data.w3) weeks.push('Week 3');
        if (data.w4) weeks.push('Week 4');
        return weeks.join(', ');
    }

    $(document).on('click', '.toggle-details', function() {
        var tr = $(this).closest('tr');
        var row = routePlanTable.row(tr);
        var id = $(this).data('id');

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
            $(this).text('+');
        } else {
            // Open this row
            $.ajax({
                url: '/route-plan/' + id + '/edit',
                type: 'GET',
                success: function(data) {
                    row.child(formatDetails(data)).show();
                    tr.addClass('shown');
                    tr.next().find('td').addClass('p-3');
                    tr.find('.toggle-details').text('-');
                },
                error: function(xhr) {
                    var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred';
                    toast('Error', errorMessage, 'error');
                }
            });
        }
    });

    function getDaysArray(data) {
        var days = [];
        if (data.mon) days.push('mon');
        if (data.tue) days.push('tue');
        if (data.wed) days.push('wed');
        if (data.thu) days.push('thu');
        if (data.fri) days.push('fri');
        if (data.sat) days.push('sat');
        return days;
    }

    function getWeeksArray(data) {
        var weeks = [];
        if (data.w1) weeks.push('w1');
        if (data.w2) weeks.push('w2');
        if (data.w3) weeks.push('w3');
        if (data.w4) weeks.push('w4');
        return weeks;
    }

    function initializeAddChoices() {
        console.log("Initializing add choices");
        new Choices('#add_description', {
            removeItemButton: true,
        });

        new Choices('#add_days', {
            removeItemButton: true,
        });

        new Choices('#add_weeks', {
            removeItemButton: true,
        });
        console.log("Add choices initialized");
    }

    // Declare variables to hold Choices instances
    var editDescriptionChoice, editDaysChoice, editWeeksChoice;

    function initializeEditChoice(selector) {
        return new Choices(selector, {
            removeItemButton: true,
        });
    }

    // Initialize choices for add form
    initializeAddChoices();

    // Handle click on employee table row
    $('#employees-table tbody').on('click', 'tr', function() {
        var radio = $(this).find('input[type="radio"]');
        radio.prop('checked', true).trigger('change');
    });

    // Function to reset sales person selection if needed
    function resetSalesPersonSelection() {
        $('input[name="salesPerson"]:first').prop('checked', true).trigger('change');
    }

    // Debug: Log current selection every 5 seconds
    setInterval(function() {
        var checkedRadio = $('input[name="salesPerson"]:checked');
        console.log("Currently checked radio:", checkedRadio.length ? checkedRadio.val() : "None");
        console.log("Current add_sales_person_id value:", $('#add_sales_person_id').val());
    }, 5000);
});

function toast(heading, text, icon) {
    $.toast({
        heading: heading,
        text: text,
        icon: icon,
        position: 'top-right',
        loader: true,
        loaderBg: '#9EC600'
    });
}
</script>
@endsection