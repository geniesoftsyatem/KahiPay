<div class="page-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12 mt-2">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">
                                <i class="fas <?php echo isset($employee) ? 'fa-user-edit' : 'fa-user-plus'; ?> mr-2 text-primary"></i>
                                <?php echo $pagetitle; ?>
                            </h4>
                            <a href="<?php echo site_url("employees"); ?>" class="btn btn-outline-secondary" style="opacity: 1 !important;">
                                <i class="fas fa-arrow-left mr-1"></i> Back to List
                            </a>
                        </div>

                        <?php echo view('admin/_topmessage'); ?>

                        <form method="post" action="<?= site_url('attendance/mark'); ?>" class="mb-4">
                            <div class="row">

                                <!-- Employee -->
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold">Select Employee <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <select name="employee_id" class="form-control" required>
                                            <option value="">-- Select Employee --</option>
                                            <?php foreach ($employees as $emp): ?>
                                                <option value="<?= $emp['employee_id']; ?>">
                                                    <?= $emp['first_name'] . ' ' . $emp['last_name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Attendance Date -->
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold">Date <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        <input type="date" name="attendance_date" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                                    </div>
                                </div>

                                <!-- Attendance Status -->
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold">Status <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                        <select name="attendanceStatus" class="form-control" required>
                                            <option value="Present">Present</option>
                                            <option value="Half Day">Half Day</option>
                                            <option value="Absent">Absent</option>
                                            <option value="Casual Leave">Casual Leave</option>
                                            <option value="Sick Leave">Sick Leave</option>
                                            <option value="Work From Home">Work From Home</option>
                                            <option value="On Duty / Official Visit">On Duty / Official Visit</option>
                                            <option value="Paid Leave">Paid Leave</option>
                                            <option value="Unpaid Leave">Unpaid Leave</option>
                                            <option value="Compensatory Off">Compensatory Off</option>
                                            <option value="Holiday / Weekend">Holiday / Weekend</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- In-Time -->
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold">In Time</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                        <input type="datetime-local" name="in_time" class="form-control">
                                    </div>
                                </div>

                                <!-- Out-Time -->
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold">Out Time</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                        <input type="datetime-local" name="out_time" class="form-control">
                                    </div>
                                </div>

                                <!-- Total Work Hours -->
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold">Total Work Hours (HH.MM)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-hourglass-half"></i></span>
                                        <input type="text" name="total_work_hours" class="form-control" placeholder="Eg: 8.50">
                                    </div>
                                </div>

                                <!-- Remarks -->
                                <div class="col-md-12 mb-3">
                                    <label class="fw-bold">Remarks</label>
                                    <textarea name="remarks" class="form-control" rows="3" placeholder="Optional remarks..."></textarea>
                                </div>

                            </div>

                            <div class="mt-3">
                                <button class="btn btn-primary">
                                    <i class="fas fa-check me-2"></i> Mark Attendance
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
    // Form validation script (same as before)
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>