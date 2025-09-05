<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<style>
    .search-form {
        display: none;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .search-form.show {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    /* Attendance Calendar Styles */
    .attendance-calendar {
        width: 100%;
        border-collapse: collapse;
    }

    .attendance-calendar th {
        text-align: center;
        padding: 8px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }

    .attendance-calendar td {
        text-align: center;
        padding: 8px;
        border: 1px solid #dee2e6;
        position: relative;
    }

    .day-number {
        font-weight: bold;
    }

    .attendance-status {
        font-size: 10px;
        display: block;
        margin-top: 3px;
    }

    /* Status colors */
    .status-present {
        background-color: #d4edda;
        color: #155724;
    }

    .status-absent {
        background-color: #f8d7da;
        color: #721c24;
    }

    .status-halfday {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-holiday {
        background-color: #e2e3e5;
        color: #383d41;
    }

    .status-pending {
        background-color: #cce5ff;
        color: #004085;
    }

    /* Add these to your existing status classes */
    .status-leave {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .status-wfh {
        background-color: #d6d8f5;
        color: #38419c;
    }

    .status-duty {
        background-color: #f5d6f0;
        color: #8c1a7a;
    }
</style>
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">EMS</a></li>
                        <li class="breadcrumb-item active">Employees</li>
                    </ol>
                    <div class="page-title-right">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <button id="toggleSearchBtn" type="button" class="btn btn-outline-primary waves-effect waves-light">
                                <i class="mdi mdi-magnify me-1"></i> Advanced Search
                            </button>
                            <a href="<?= site_url('employees/create') ?>" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-plus me-1"></i> Add Employee
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="search-form <?= isset($txtsearch) ? 'show' : '' ?>">
            <div class="card customer-card">
                <div class="card-body">
                    <form action="">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="txtsearch" class="form-label">Search Employees</label>
                                <input class="form-control" name="txtsearch" type="text" value="<?= isset($searchArray["txtsearch"]) ? esc($searchArray["txtsearch"]) : ''; ?>" placeholder="Search by name, email, or phone...">
                            </div>
                            <?php if (session('user_type') === 'admin'): ?>
                                <div class="col-md-3">
                                    <label for="company_id" class="form-label">Company</label>
                                    <select class="form-select" id="company_id" name="company_id">
                                        <option value="">Select Company</option>
                                        <?php foreach ($companies as $company): ?>
                                            <option value="<?= esc($company['company_id']) ?>"
                                                <?= (isset($searchArray['company_id']) && $searchArray['company_id'] == $company['company_id']) ? 'selected' : '' ?>>
                                                <?= esc($company['company_name']) ?> (<?= esc($company['company_code']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <div class="col-md-3">
                                <label for="manager" class="form-label">Reporting Manager</label>
                                <select class="form-select" name="manager" id="manager">
                                    <option value="">Select Reporting Manager</option>
                                    <?php foreach ($managers as $manager): ?>
                                        <option value="<?= esc($manager['employee_id']) ?>"
                                            <?= isset($searchArray['manager']) && $searchArray['manager'] == $manager['employee_id'] ? 'selected' : '' ?>>
                                            <?= esc($manager['first_name'] . ' ' . $manager['last_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status" id="imstatus">
                                    <option value="">Select Status</option>
                                    <option value="Active" <?= isset($searchArray['status']) && $searchArray['status'] == 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="Inactive" <?= isset($searchArray['status']) && $searchArray['status'] == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary waves-effect waves-light me-2">
                                    <i class="mdi mdi-filter-outline me-1"></i> Apply Filters
                                </button>
                                <a href="<?= site_url('employees'); ?>" class="btn btn-light waves-effect">
                                    <i class="mdi mdi-autorenew me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <?php echo view('admin/_topmessage'); ?>
                    <div class="card-body">
                        <?php if ($pagination["totalRecords"] > 0) { ?>
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered dt-responsive w-100">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Sl</th>
                                            <th data-sortable="true" class="text-center">Company Name</th>
                                            <th data-sortable="true" class="text-center">Employee Name</th>
                                            <th data-sortable="true" class="text-center">Employee Code</th>
                                            <th data-sortable="true" class="text-center">Employee Email</th>
                                            <th data-sortable="true" class="text-center">Employee Mobile</th>
                                            <th data-sortable="true" class="text-center">Employee Designation</th>
                                            <th data-sortable="true" class="text-center">Employee Manager</th>
                                            <th data-sortable="true" class="text-center">Employee Gender</th>
                                            <th data-sortable="true" class="text-center">Employee Status</th>
                                            <th data-sortable="true" class="text-center">Joined On</th>
                                            <th data-sortable="false" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $item) { ?>
                                            <tr id="employee-row-<?= $item->employee_id ?>">
                                                <td class="text-center" scope="row"><?php echo ++$startLimit; ?></td>
                                                <td class="text-center"><?= $item->company_name ?? 'N/A'; ?></td>
                                                <td class="text-center"><?php echo $item->first_name . ' ' . $item->last_name ?></td>
                                                <td class="text-center"><?php echo $item->employee_code; ?></td>
                                                <td class="text-center"><?php echo $item->email; ?></td>
                                                <td class="text-center"><?php echo $item->phone; ?></td>
                                                <td class="text-center"><?php echo $item->designation; ?></td>
                                                <td class="text-center"><?php echo $item->manager_name ?? "Not Assigned"; ?></td>
                                                <td class="text-center"><?php echo $item->gender; ?></td>
                                                <td class="text-center"><?php echo ucwords($item->status); ?></td>
                                                <td class="text-center"><?php echo date('d M Y', strtotime($item->joining_date)); ?></td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <a href="<?= site_url("employees/edit/" . $item->employee_id) ?>" class="btn btn-primary m-1" data-bs-toggle="tooltip" title="Edit Employee">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="<?= site_url("employees/preview/" . $item->employee_id) ?>" class="btn btn-info m-1" data-bs-toggle="tooltip" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        <?php if (session('user_type') === 'admin'): ?>
                                                            <button type="button" class="btn btn-danger m-1 delete-employee" data-id="<?= $item->employee_id ?>" data-name="<?= $item->first_name . ' ' . $item->last_name ?>" data-bs-toggle="tooltip" title="Delete Employee">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        <?php endif; ?>

                                                        <button type="button" class="btn btn-secondary m-1 view-attendance" data-id="<?= $item->employee_id ?>" data-name="<?= $item->first_name . ' ' . $item->last_name ?>" data-bs-toggle="modal" data-bs-target="#attendanceModal" title="View Attendance" data-bs-toggle="tooltip">
                                                            <i class="fas fa-calendar-alt"></i>
                                                        </button>

                                                        <a href="<?= site_url('employee-attendance/view/' . $item->employee_id) ?>" class="btn btn-warning m-1" title="Attendance Logs" data-bs-toggle="tooltip">
                                                            <i class="fas fa-calendar-check"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if ($pagination['totalRecords']) { ?>
                                <br>
                                <?= view('admin/_paging', ['paginate' => $pagination, 'siteurl' => $action, 'varExtra' => $searchArray]); ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?= view('admin/_noresult'); ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteEmployeeModal" tabindex="-1" aria-labelledby="deleteEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteEmployeeModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                    <h5>Are you sure you want to delete this employee?</h5>
                    <p>This action cannot be undone and all related data will be permanently removed.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="deleteSuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="avatar-lg mx-auto mb-4">
                    <div class="avatar-title bg-light text-success display-4 rounded-circle">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
                <h4 id="successMessage">Employee deleted successfully!</h4>
                <p class="text-muted">The record has been permanently removed from the system.</p>
                <button type="button" class="btn btn-success mt-3" data-bs-dismiss="modal" onclick="setTimeout(function(){ location.reload(); }, 300);">
                    <i class="fas fa-check me-1"></i> Continue
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attendanceModalLabel">Attendance for <span id="employeeName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="monthSelect" class="form-label">Select Month</label>
                        <select class="form-select" id="monthSelect">
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?= $i ?>" <?= $i == date('n') ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="yearSelect" class="form-label">Select Year</label>
                        <select class="form-select" id="yearSelect">
                            <?php for ($i = date('Y') - 2; $i <= date('Y'); $i++): ?>
                                <option value="<?= $i ?>" <?= $i == date('Y') ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div id="calendarContainer">
                    <!-- Calendar will be loaded here via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#datatable').DataTable({
            dom: 'Bfrtip',
            searching: false,
            paging: false,
            info: false,
        });

        $('[data-bs-toggle="tooltip"]').tooltip();

        $('#toggleSearchBtn').click(function() {
            $('.search-form').toggleClass('show');
            localStorage.setItem('isSearchFormVisible', $('.search-form').hasClass('show'));
            localStorage.setItem('searchFormVisible', new Date().getTime());

            if ($('.search-form').hasClass('show')) {
                $(this).html('<i class="mdi mdi-close me-1"></i> Close Search').removeClass('btn-outline-primary').addClass('btn-primary');
            } else {
                $(this).html('<i class="mdi mdi-magnify me-1"></i> Advanced Search').removeClass('btn-primary').addClass('btn-outline-primary');
            }
        });

        const currentTime = new Date().getTime();
        const visibilityDuration = 10 * 60 * 1000;
        const isFormVisible = localStorage.getItem('isSearchFormVisible');
        const formVisibleTimestamp = localStorage.getItem('searchFormVisible');

        if (isFormVisible === 'true' && formVisibleTimestamp && (currentTime - formVisibleTimestamp < visibilityDuration)) {
            $('.search-form').addClass('show');
            $('#toggleSearchBtn').html('<i class="mdi mdi-close me-1"></i> Close Search').removeClass('btn-outline-primary').addClass('btn-primary');
        }

        let currentEmployeeId = null;
        let currentEmployeeName = null;

        // When delete button is clicked
        $('.delete-employee').on('click', function() {
            currentEmployeeId = $(this).data('id');
            currentEmployeeName = $(this).data('name');
            $('#deleteEmployeeModal .modal-body h5').html(
                `Are you sure you want to delete <strong>${currentEmployeeName}</strong>?`
            );
            $('#deleteEmployeeModal').modal('show');
        });

        $('#confirmDelete').on('click', function() {
            $('#deleteEmployeeModal').modal('hide');
            $('#confirmDelete').html('<i class="fas fa-spinner fa-spin"></i> Deleting...').prop('disabled', true);

            $.ajax({
                url: '<?= site_url("employees/delete") ?>',
                type: 'POST',
                data: {
                    employee_id: currentEmployeeId,
                },
                success: function(response) {
                    if (response.success) {
                        $('#employee-row-' + currentEmployeeId).remove();
                        $('#successMessage').html(response.message);
                    } else {
                        $('#successMessage').html(response.message);
                    }
                    $('#deleteSuccessModal').modal('show');
                },
                error: function() {
                    $('#successMessage').html(`An error occurred while deleting ${currentEmployeeName}.`);
                    $('#deleteSuccessModal').modal('show');
                },
                complete: function() {
                    // Reset button state
                    $('#confirmDelete').html('Delete').prop('disabled', false);
                }
            });
        });

        $('#company_id').on('change', function() {
            var companyId = $(this).val();

            if (companyId) {
                $.ajax({
                    url: '<?= site_url("companies/get-managers") ?>',
                    type: 'POST',
                    data: {
                        company_id: companyId
                    },
                    dataType: 'json',
                    success: function(response) {
                        var managerSelect = $('#manager');
                        managerSelect.empty();
                        managerSelect.append('<option value="">Select Reporting Manager</option>');

                        $.each(response, function(index, emp) {
                            managerSelect.append(
                                '<option value="' + emp.employee_id + '">' +
                                emp.first_name + ' ' + emp.last_name +
                                ' (' + emp.employee_code + ')' +
                                '</option>'
                            );
                        });
                    },
                    error: function() {
                        alert('Error fetching managers.');
                    }
                });
            }
        });
    });

    // Attendance Modal Handling
    $(document).on('click', '.view-attendance', function() {
        const employeeId = $(this).data('id');
        const employeeName = $(this).data('name');

        $('#employeeName').text(employeeName);
        $('#attendanceModal').modal('show');

        // Store employee ID in modal for later use
        $('#attendanceModal').data('employeeId', employeeId);

        // Load initial calendar
        loadAttendanceCalendar(employeeId);
    });

    // Handle month/year change
    $('#monthSelect, #yearSelect').change(function() {
        const employeeId = $('#attendanceModal').data('employeeId');
        loadAttendanceCalendar(employeeId);
    });

    function loadAttendanceCalendar(employeeId) {
        const month = $('#monthSelect').val();
        const year = $('#yearSelect').val();

        $.ajax({
            url: '<?= site_url("employees/attendance-calendar") ?>',
            type: 'GET',
            data: {
                employee_id: employeeId,
                month: month,
                year: year
            },
            beforeSend: function() {
                $('#calendarContainer').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
            },
            success: function(response) {
                error_log(response);
                $('#calendarContainer').html(response);
            },
            error: function() {
                $('#calendarContainer').html('<div class="alert alert-danger">Failed to load attendance data</div>');
            }
        });
    }
</script>

<?= $this->endSection() ?>