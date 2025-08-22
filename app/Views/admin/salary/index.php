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
</style>
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">EMS</a></li>
                        <li class="breadcrumb-item active">Salary List</li>
                    </ol>
                    <div class="page-title-right">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <button id="toggleSearchBtn" type="button" class="btn btn-outline-primary waves-effect waves-light">
                                <i class="mdi mdi-magnify me-1"></i> Advanced Search
                            </button>
                            <a class="btn btn-primary waves-effect waves-light" href="<?php echo site_url('salary/create'); ?>">
                                <i class="mdi mdi-plus me-1"></i> Add Salary
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
                                <label for="txtsearch" class="form-label">Search</label>
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

                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary waves-effect waves-light me-2">
                                    <i class="mdi mdi-filter-outline me-1"></i> Apply Filters
                                </button>
                                <a href="<?= site_url('salary'); ?>" class="btn btn-light waves-effect">
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
                                            <th data-sortable="true" class="text-center">Employee Company</th>
                                            <th data-sortable="true" class="text-center">Employee Name</th>
                                            <th data-sortable="true" class="text-center">Employee Email</th>
                                            <th data-sortable="true" class="text-center">Month</th>
                                            <th data-sortable="true" class="text-center">Year</th>
                                            <th data-sortable="true" class="text-center">Basic Salary</th>
                                            <th data-sortable="true" class="text-center">Allowances</th>
                                            <th data-sortable="true" class="text-center">Deductions</th>
                                            <th data-sortable="true" class="text-center">Salary Amount</th>
                                            <th data-sortable="false" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $item) { ?>
                                            <tr id="salary-row-<?= $item->salary_id ?>">
                                                <td class="text-center" scope="row"><?php echo ++$startLimit; ?></td>
                                                <td class="text-center"><?= $item->company_name ?? 'N/A'; ?></td>
                                                <td class="text-center"><?php echo $item->first_name . ' ' . $item->last_name; ?></td>
                                                <td class="text-center"><?php echo $item->email; ?></td>
                                                <td class="text-center"><?php echo $months[$item->month]; ?></td>
                                                <td class="text-center"><?php echo $item->year; ?></td>
                                                <td class="text-center"><?php echo $item->basic_salary; ?></td>
                                                <td class="text-center"><?php echo $item->allowances; ?></td>
                                                <td class="text-center"><?php echo $item->deductions; ?></td>
                                                <td class="text-center"><?php echo number_format($item->net_salary, 2); ?></td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <a href="<?= site_url("salary/slip/preview/" . $item->salary_id) ?>" class="btn btn-info m-1" data-bs-toggle="tooltip" title="View Salary Slip" target="_blank">
                                                            <i class="fas fa-file-invoice"></i>
                                                        </a>
                                                        <a href="<?= site_url("salary/slip/download/" . $item->salary_id) ?>" class="btn btn-success m-1" data-bs-toggle="tooltip" title="Download Salary Slip">
                                                            <i class="fas fa-file-download"></i>
                                                        </a>
                                                        <a href="<?= site_url("salary/edit/" . $item->salary_id) ?>" class="btn btn-primary m-1" data-bs-toggle="tooltip" title="Edit Salary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-danger m-1 delete-salary" data-id="<?= $item->salary_id ?>" data-name="<?= $item->first_name . ' ' . $item->last_name ?>" data-bs-toggle="tooltip" title="Delete Salary Record">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
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
<div class="modal fade" id="deleteSalaryModal" tabindex="-1" aria-labelledby="deleteSalaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSalaryModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                <h5>Are you sure you want to delete <strong id="salaryDeleteName"></strong>?</h5>
                <p>This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteSalary">Delete</button>
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
                <h4 id="successMessage">Salary record deleted successfully!</h4>
                <p class="text-muted">The record has been permanently removed.</p>
                <button type="button" class="btn btn-success mt-3" data-bs-dismiss="modal" onclick="setTimeout(function(){ location.reload(); }, 300);">
                    <i class="fas fa-check me-1"></i> Continue
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {

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

        let currentSalaryId = null;
        let currentSalaryName = null;
        $('.delete-salary').on('click', function() {
            currentSalaryId = $(this).data('id');
            currentSalaryName = $(this).data('name');
            $('#salaryDeleteName').text(currentSalaryName);
            $('#deleteSalaryModal').modal('show');
        });

        $('#confirmDeleteSalary').on('click', function() {
            $('#deleteSalaryModal').modal('hide');
            $('#confirmDeleteSalary').html('<i class="fas fa-spinner fa-spin"></i> Deleting...').prop('disabled', true);

            $.ajax({
                url: '<?= site_url("salary/delete") ?>',
                type: 'POST',
                data: {
                    salary_id: currentSalaryId
                },
                success: function(response) {
                    if (response.success) {
                        $('#salary-row-' + currentSalaryId).remove();
                        $('#successMessage').html(response.message);
                    } else {
                        $('#successMessage').html(response.message);
                    }
                    $('#deleteSuccessModal').modal('show');
                },
                error: function() {
                    $('#successMessage').html(`An error occurred while deleting the salary record for ${currentSalaryName}.`);
                    $('#deleteSuccessModal').modal('show');
                },
                complete: function() {
                    $('#confirmDeleteSalary').html('Delete').prop('disabled', false);
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
</script>

<?= $this->endSection() ?>