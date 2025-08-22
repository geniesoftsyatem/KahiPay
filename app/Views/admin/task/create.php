<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12 mt-2">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">
                                <i class="fas <?php echo isset($task) ? 'fa-edit' : 'fa-plus-circle'; ?> mr-2 text-primary"></i>
                                <?php echo $pagetitle; ?>
                            </h4>
                            <a href="<?php echo site_url("employee-tasks"); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Back to List
                            </a>
                        </div>

                        <?php echo view('admin/_topmessage'); ?>

                        <form class="needs-validation" method="post" action="<?= site_url('employee-tasks/save'); ?>" novalidate>
                            <input type="hidden" name="task_id" value="<?= isset($task) ? $task['task_id'] : ''; ?>">

                            <div class="row g-3">
                                <!-- Company Dropdown (only for admin) -->
                                <?php if (session()->get('user_type') === 'admin'): ?>
                                    <div class="col-md-6">
                                        <label for="company_id" class="fw-bold">Company <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                                            <select class="form-select" id="company_id" name="company_id" required>
                                                <option value="">Select Company</option>
                                                <?php foreach ($companies as $company): ?>
                                                    <option value="<?= $company['company_id']; ?>"
                                                        <?= (isset($task) && $task['company_id'] == $company['company_id']) ? 'selected' : ''; ?>>
                                                        <?= $company['company_name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="invalid-feedback">Please select a company.</div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Employee Dropdown -->
                                <div class="col-md-6">
                                    <label for="employee_id" class="fw-bold">Employee<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                        <select class="form-select" id="employee_id" name="employee_id" required>
                                            <option value="">Select Employee</option>
                                            <?php foreach ($employees as $employee): ?>
                                                <option value="<?= $employee['employee_id'] ?>"
                                                    <?= (isset($task) && $task['employee_id'] == $employee['employee_id']) ? 'selected' : '' ?>>
                                                    <?= $employee['first_name'] . ' ' . $employee['last_name'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Please select an employee.</div>
                                    </div>
                                </div>

                                <!-- Task Title -->
                                <div class="col-md-6">
                                    <label for="title" class="fw-bold">Task Title <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                        <input type="text" class="form-control" id="title" name="title"
                                            placeholder="Enter task title"
                                            value="<?= isset($task) ? $task['title'] : ''; ?>" required>
                                        <div class="invalid-feedback">Please provide a task title.</div>
                                    </div>
                                </div>

                                <!-- Priority -->
                                <div class="col-md-6">
                                    <label for="priority" class="fw-bold">Priority <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-flag"></i></span>
                                        <select class="form-select" id="priority" name="priority" required>
                                            <option value="">Select Priority</option>
                                            <option value="high" <?= (isset($task) && $task['priority'] == 'high') ? 'selected' : '' ?>>High</option>
                                            <option value="medium" <?= (isset($task) && $task['priority'] == 'medium') ? 'selected' : '' ?>>Medium</option>
                                            <option value="low" <?= (isset($task) && $task['priority'] == 'low') ? 'selected' : '' ?>>Low</option>
                                        </select>
                                        <div class="invalid-feedback">Please select a priority level.</div>
                                    </div>
                                </div>

                                <!-- Due Date -->
                                <div class="col-md-6">
                                    <label for="due_date" class="fw-bold">Due Date <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                        <input type="date" class="form-control" id="due_date" name="due_date"
                                            value="<?= isset($task) ? $task['due_date'] : date('Y-m-d'); ?>"
                                            min="<?= date('Y-m-d') ?>" required>
                                        <div class="invalid-feedback">Please select a due date.</div>
                                    </div>
                                </div>

                                <!-- Status (only when editing task) -->
                                <?php if (isset($task)): ?>
                                    <div class="col-md-6">
                                        <label for="taskStatus" class="fw-bold">Status <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-tasks"></i></span>
                                            <select class="form-control" id="taskStatus" name="status" required>
                                                <option value="pending" <?= $task['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="in_progress" <?= $task['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                                <option value="completed" <?= $task['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Description -->
                                <div class="col-md-12">
                                    <label for="description" class="fw-bold">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="description" name="description"
                                        rows="4" placeholder="Enter task details" required><?= isset($task) ? $task['description'] : ''; ?></textarea>
                                    <div class="invalid-feedback">Please provide a task description.</div>
                                </div>

                                <!-- Notes -->
                                <div class="col-md-12">
                                    <label for="notes" class="fw-bold">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"
                                        placeholder="Additional comments or notes"><?= isset($task) ? $task['notes'] : ''; ?></textarea>
                                </div>

                                <!-- Buttons -->
                                <div class="col-md-12 mt-4 d-flex justify-content-between">
                                    <a href="<?= site_url("employee-tasks"); ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left mr-1"></i> Back to List
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg px-4">
                                        <i class="fas fa-save me-2"></i> <?= isset($task) ? 'Update' : 'Create'; ?> Task
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.needs-validation').on('submit', function(e) {
            var form = $(this)[0];

            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }

            $(this).addClass('was-validated');
        });

        $('#company_id').on('change', function() {
            let companyId = $(this).val();
            let $employeeSelect = $('select[name="employee_id"]');

            $employeeSelect.html('<option value="">Loading...</option>');

            if (companyId) {
                $.ajax({
                    url: "<?= site_url('employees/get-employees-by-company'); ?>/" + companyId,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $employeeSelect.html('<option value="">Select an employee</option>');
                        $.each(data, function(i, emp) {
                            $employeeSelect.append(
                                `<option value="${emp.employee_id}">${emp.first_name} ${emp.last_name} (${emp.employee_code})</option>`
                            );
                        });
                    },
                    error: function() {
                        $employeeSelect.html('<option value="">Error loading employees</option>');
                    }
                });
            } else {
                $employeeSelect.html('<option value="">Select a company first</option>');
            }
        });
    });
</script>

<?= $this->endSection() ?>