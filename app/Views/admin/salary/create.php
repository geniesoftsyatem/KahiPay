<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<?php
$session = session();
$companyId = $session->get('company_id');
$userType = strtolower($session->get('user_type'));
?>
<div class="page-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12 mt-2">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-rupee-sign text-primary mr-2"></i>
                                <?php echo $pagetitle; ?>
                            </h4>
                            <a href="<?php echo site_url("salary"); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Back to List
                            </a>
                        </div>

                        <?php echo view('admin/_topmessage'); ?>

                        <form class="needs-validation" method="post" action="<?= site_url('salary/store'); ?>" novalidate>
                            <input type="hidden" name="salary_id" value="<?= isset($salary) ? $salary['salary_id'] : ''; ?>">

                            <div class="row">
                                <?php if ($userType === 'admin'): ?>
                                    <div class="col-md-3 mb-3">
                                        <label class="fw-bold">Select Company <span class="text-danger">*</span></label>
                                        <select id="companySelect" name="company_id" class="form-control form-select" required>
                                            <option value="">Select a company</option>
                                            <?php foreach ($companies as $comp): ?>
                                                <option value="<?= $comp['company_id']; ?>" <?= isset($salary) && $salary['company_id'] == $comp['company_id'] ? 'selected' : ''; ?>>
                                                    <?= $comp['company_name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Please select a company.</div>
                                    </div>
                                <?php endif; ?>

                                <div class="<?= ($userType !== 'admin') ? 'col-md-6' : 'col-md-3'; ?> mb-3">
                                    <label class="fw-bold">Select Employee <span class="text-danger">*</span></label>
                                    <select name="employee_id" class="form-control form-select" required>
                                        <option value="">Select an employee</option>
                                        <?php foreach ($employees as $emp): ?>
                                            <option value="<?= $emp['employee_id']; ?>" <?= isset($salary) && $salary['employee_id'] == $emp['employee_id'] ? 'selected' : ''; ?>>
                                                <?= $emp['first_name'] . ' ' . $emp['last_name'] . ' (' . $emp['employee_code'] . ')' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select an employee.</div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold">Month <span class="text-danger">*</span></label>
                                    <select name="month" class="form-control form-select" required>
                                        <option value="">Select Month</option>
                                        <?php
                                        $months = [
                                            '1' => 'January',
                                            '2' => 'February',
                                            '3' => 'March',
                                            '4' => 'April',
                                            '5' => 'May',
                                            '6' => 'June',
                                            '7' => 'July',
                                            '8' => 'August',
                                            '9' => 'September',
                                            '10' => 'October',
                                            '11' => 'November',
                                            '12' => 'December'
                                        ];
                                        foreach ($months as $num => $name): ?>
                                            <option value="<?= $num; ?>" <?= isset($salary) && $salary['month'] == $num ? 'selected' : ''; ?>>
                                                <?= $name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select a month.</div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold">Year <span class="text-danger">*</span></label>
                                    <input type="number" name="year" min="2000" max="2100" class="form-control"
                                        placeholder="Year" value="<?= isset($salary) ? $salary['year'] : date('Y'); ?>" required>
                                    <div class="invalid-feedback">Please enter a valid year.</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold">Basic Salary (₹) <span class="text-danger">*</span></label>
                                    <input type="number" name="basic_salary" min="0" step="0.01" class="form-control"
                                        placeholder="Basic Salary" value="<?= isset($salary) ? $salary['basic_salary'] : ''; ?>" required>
                                    <div class="invalid-feedback">Please enter basic salary.</div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold">Allowances (₹)</label>
                                    <input type="number" name="allowances" min="0" step="0.01" class="form-control"
                                        placeholder="Allowances" value="<?= isset($salary) ? $salary['allowances'] : 0; ?>">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold">Deductions (₹)</label>
                                    <input type="number" name="deductions" min="0" step="0.01" class="form-control"
                                        placeholder="Deductions" value="<?= isset($salary) ? $salary['deductions'] : 0; ?>">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold">Net Salary (₹) <span class="text-danger">*</span></label>
                                    <input type="number" name="net_salary" min="0" step="0.01" class="form-control"
                                        placeholder="Net Salary" value="<?= isset($salary) ? $salary['net_salary'] : ''; ?>" required readonly>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="fw-bold">Remarks</label>
                                    <input type="text" name="remarks" class="form-control" placeholder="Optional notes"
                                        value="<?= isset($salary) ? $salary['remarks'] : ''; ?>">
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-between">
                                <a href="<?php echo site_url("salary"); ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg px-4">
                                    <i class="fas fa-save me-2"></i> <?= isset($salary) ? 'Update' : 'Save'; ?> Salary
                                </button>
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

        calculateNetSalary(); // Initial load

        $('select[name="company_id"]').on('change', function() {
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

        function calculateNetSalary() {
            let basic = parseFloat($('input[name="basic_salary"]').val()) || 0;
            let allowances = parseFloat($('input[name="allowances"]').val()) || 0;
            let deductions = parseFloat($('input[name="deductions"]').val()) || 0;
            let net = basic + allowances - deductions;
            $('input[name="net_salary"]').val(net >= 0 ? net.toFixed(2) : 0);
        }

        $('input[name="basic_salary"], input[name="allowances"], input[name="deductions"]').on('input', calculateNetSalary);

        $('.needs-validation').on('submit', function(event) {
            if (this.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
            }
            $(this).addClass('was-validated');
        });

    });
</script>

<?= $this->endSection() ?>