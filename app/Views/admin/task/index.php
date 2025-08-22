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
                        <li class="breadcrumb-item active">Task List</li>
                    </ol>
                    <?php if (isset($taskStatistics)): ?>
                        <div class="task-stats mt-2">
                            <span class="badge badge-success mr-2">Total: <?= $taskStatistics->total_tasks ?></span>
                            <span class="badge badge-primary mr-2">Completed: <?= $taskStatistics->completed_tasks ?></span>
                            <span class="badge badge-warning mr-2">In Progress: <?= $taskStatistics->in_progress_tasks ?></span>
                            <span class="badge badge-danger">Overdue: <?= $taskStatistics->overdue_tasks ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="page-title-right">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <button id="toggleSearchBtn" type="button" class="btn btn-outline-primary waves-effect waves-light">
                                <i class="mdi mdi-magnify me-1"></i> Advanced Search
                            </button>
                            <a class="btn btn-primary waves-effect waves-light" href="<?php echo site_url('employee-tasks/create'); ?>">
                                <i class="mdi mdi-plus me-1"></i> Add Task
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
                                <input class="form-control" name="txtsearch" type="text" value="<?= isset($searchArray["txtsearch"]) ? esc($searchArray["txtsearch"]) : ''; ?>" placeholder="Search by employees name, title, or description...">
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
                                <label>Status</label>
                                <select class="form-select" name="status">
                                    <option value="">Select Status</option>
                                    <option value="pending" <?= isset($searchArray["status"]) && $searchArray["status"] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="in_progress" <?= isset($searchArray["status"]) && $searchArray["status"] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="completed" <?= isset($searchArray["status"]) && $searchArray["status"] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>Priority</label>
                                <select class="form-select" name="priority">
                                    <option value="">Select Priority</option>
                                    <option value="high" <?= isset($searchArray["priority"]) && $searchArray["priority"] == 'high' ? 'selected' : '' ?>>High</option>
                                    <option value="medium" <?= isset($searchArray["priority"]) && $searchArray["priority"] == 'medium' ? 'selected' : '' ?>>Medium</option>
                                    <option value="low" <?= isset($searchArray["priority"]) && $searchArray["priority"] == 'low' ? 'selected' : '' ?>>Low</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>Start Date</label>
                                <input class="form-control" name="startDate" type="date" value="<?php echo isset($searchArray["startDate"]) ? $searchArray["startDate"] : ''; ?>">
                            </div>

                            <div class="col-md-2">
                                <label>End Date</label>
                                <input class="form-control" name="endDate" type="date" value="<?php echo isset($searchArray["endDate"]) ? $searchArray["endDate"] : ''; ?>">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary waves-effect waves-light me-2">
                                    <i class="mdi mdi-filter-outline me-1"></i> Apply Filters
                                </button>
                                <a href="<?= site_url('employee-tasks'); ?>" class="btn btn-light waves-effect">
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
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Company</th>
                                            <th>Task Title</th>
                                            <th>Assigned To</th>
                                            <th>Assigned By</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                            <th>Due Date</th>
                                            <th>Created At</th>
                                            <th data-sortable="false">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $item) {
                                            // Priority classes
                                            $priorityClass = 'secondary';
                                            $priorityText = 'N/A';
                                            if (isset($item->priority)) {
                                                $priority = strtolower($item->priority);
                                                if ($priority == 'high') $priorityClass = 'danger';
                                                elseif ($priority == 'medium') $priorityClass = 'warning';
                                                elseif ($priority == 'low') $priorityClass = 'success';
                                                $priorityText = ucfirst($item->priority);
                                            }

                                            // Status classes
                                            $statusClass = 'secondary';
                                            $statusText = 'N/A';
                                            if (isset($item->status)) {
                                                $status = strtolower($item->status);
                                                if ($status == 'completed') $statusClass = 'success';
                                                elseif ($status == 'in_progress') $statusClass = 'primary';
                                                elseif ($status == 'pending') $statusClass = 'secondary';
                                                $statusText = ucfirst(str_replace('_', ' ', $item->status));
                                            } ?>
                                            <tr>
                                                <td class="text-center"><?= ++$startLimit ?></td>
                                                <td class="text-center"><?= $item->company_name ?? 'N/A'; ?></td>
                                                <td><?= htmlspecialchars($item->title ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($item->employee_name ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($item->assigner_name ?? 'Admin') ?></td>
                                                <td><span class="badge bg-<?= $priorityClass ?>"><?= $priorityText ?></span></td>
                                                <td><span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span></td>
                                                <td><?= !empty($item->due_date) ? date('M d, Y', strtotime($item->due_date)) : 'N/A' ?></td>
                                                <td><?= !empty($item->created_at) ? date('M d, Y', strtotime($item->created_at)) : 'N/A' ?></td>
                                                <td class="text-center">
                                                    <a href="<?= site_url('employee-tasks/preview/' . $item->task_id) ?>" class="btn btn-success" title="View" data-bs-toggle="tooltip">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= site_url('employee-tasks/edit/' . $item->task_id) ?>" class="btn btn-primary" title="Edit" data-bs-toggle="tooltip">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger m-1 delete-task"
                                                        data-id="<?= htmlspecialchars($item->task_id) ?>"
                                                        data-name="<?= htmlspecialchars($item->title) ?>"
                                                        data-bs-toggle="tooltip"
                                                        title="Delete Task">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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
<div class="modal fade" id="deleteTaskModal" tabindex="-1" aria-labelledby="deleteTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteTaskModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                    <h5>Are you sure you want to delete this task?</h5>
                    <p>This action cannot be undone and all related data will be permanently removed.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="deleteSuccessModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle fa-5x text-success"></i>
                </div>
                <h4 id="successMessage">Task deleted successfully!</h4>
                <button type="button" class="btn btn-primary mt-3" data-dismiss="modal" onclick="setTimeout(function(){ location.reload(); }, 300);">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Safe Data Modal -->
<div class="modal fade" id="dataSafeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="fas fa-shield-alt fa-5x text-primary"></i>
                </div>
                <h4>Your data is safe!</h4>
                <p class="text-muted">The task record was not deleted.</p>
                <button type="button" class="btn btn-primary mt-3" data-dismiss="modal" onclick="setTimeout(function(){ location.reload(); }, 300);">Close</button>
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

        let currentTaskId = null;
        let currentEmployeeName = null;

        // When delete button is clicked
        $('.delete-task').on('click', function() {
            currentTaskId = $(this).data('id');
            currentEmployeeName = $(this).data('name');

            // Update modal text with employee name
            $('#deleteTaskModal .modal-body h5').html(
                `Are you sure you want to delete <strong>${currentEmployeeName}</strong>?`
            );

            // Show the confirmation modal
            $('#deleteTaskModal').modal('show');
        });

        // When confirm delete is clicked
        $('#confirmDelete').on('click', function() {
            // Close the confirmation modal
            $('#deleteTaskModal').modal('hide');

            // Show loading state
            $('#confirmDelete').html('<i class="fas fa-spinner fa-spin"></i> Deleting...').prop('disabled', true);

            // AJAX request to delete employee
            $.ajax({
                url: '<?= site_url("employee-tasks/delete") ?>',
                type: 'POST',
                data: {
                    task_id: currentTaskId,
                },
                success: function(response) {
                    if (response.success) {
                        $('#employee-row-' + currentTaskId).remove();
                        $('#successMessage').html(response.message);
                        $('#deleteSuccessModal').modal('show');
                    } else {
                        $('#successMessage').html(response.message);
                        $('#deleteSuccessModal').modal('show');
                    }
                },
                error: function() {
                    $('#successMessage').html(
                        `An error occurred while deleting ${currentEmployeeName}.`
                    );
                    $('#deleteSuccessModal').modal('show');
                },
                complete: function() {
                    // Reset button state
                    $('#confirmDelete').html('Delete').prop('disabled', false);
                }
            });
        });

        // When cancel is clicked in confirmation modal
        $('#deleteTaskModal .btn-secondary').on('click', function() {
            // Show data safe modal
            $('#dataSafeModal').modal('show');
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