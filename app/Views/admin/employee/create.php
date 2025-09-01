<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<?php

$session = session();
$companyId = $session->get('company_id');
$userType = strtolower($session->get('user_type'));
?>

<style>
    .img-preview-container {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 15px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }

    .img-preview-container:hover {
        border-color: #adb5bd;
    }

    .img-preview {
        max-width: 100%;
        max-height: 200px;
        border-radius: 6px;
        object-fit: cover;
        display: block;
        margin: 0 auto 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .section-title {
        position: relative;
        padding-bottom: 10px;
        margin: 25px 0 20px;
        color: #495057;
    }

    .section-title:after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 3px;
        background: #4e73df;
        border-radius: 3px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }

    .input-group-text {
        background-color: #f8f9fa;
        min-width: 40px;
        justify-content: center;
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: none;
    }

    .card-body {
        padding: 2rem;
    }

    .btn-outline-secondary {
        border-color: #d1d3e2;
    }

    .btn-outline-secondary:hover {
        background-color: #f8f9fa;
    }

    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
        padding: 0.5rem 1.75rem;
    }

    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2653d4;
    }

    .file-upload-label {
        display: block;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 400;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        cursor: pointer;
        text-align: center;
        margin-bottom: 10px;
    }

    .file-upload-label:hover {
        background-color: #f8f9fa;
    }

    .file-upload-input {
        display: none;
    }

    .download-btn {
        margin-top: 10px;
    }

    .form-check-input {
        width: 3em;
        height: 1.5em;
        margin-right: 10px;
        cursor: pointer;
    }

    .form-switch .form-check-input {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%280, 0, 0, 0.25%29'/%3e%3c/svg%3e");
        background-position: left center;
        border-radius: 2em;
        transition: background-position .15s ease-in-out;
    }

    .form-switch .form-check-input:checked {
        background-color: #4e73df;
        border-color: #4e73df;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
        background-position: right center;
    }

    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem;
        }
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-12 mt-4">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0 text-dark">
                                <i class="fas <?= isset($employee) ? 'fa-user-edit' : 'fa-user-plus'; ?> text-primary me-2"></i>
                                <?= $pagetitle; ?>
                            </h4>
                            <a href="<?= site_url("employees"); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Back to List
                            </a>
                        </div>

                        <form class="needs-validation" method="post" action="<?= site_url('employees/save'); ?>" enctype="multipart/form-data" novalidate>
                            <?= csrf_field(); ?>
                            <?= view('flash_messages'); ?>
                            <input type="hidden" name="employee_id" value="<?= $employee['employee_id'] ?? ''; ?>">

                            <h5 class="section-title">Basic Information</h5>

                            <div class="row g-3">
                                <?php if ($userType === 'company'): ?>
                                    <input type="hidden" name="company_id" value="<?= esc($companyId) ?>">
                                <?php else: ?>
                                    <div class="col-md-6">
                                        <label for="company_id" class="form-label fw-bold">Company <span class="text-danger">*</span></label>
                                        <select class="form-select" id="company_id" name="company_id" required>
                                            <option value="">Select Company</option>
                                            <?php foreach ($companies as $company): ?>
                                                <option value="<?= $company['company_id'] ?>"
                                                    <?= (isset($employee['company_id']) && $employee['company_id'] == $company['company_id']) ? 'selected' : '' ?>>
                                                    <?= esc($company['company_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Please select a company.</div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($employee['employee_id'])): ?>
                                    <div class="col-md-6">
                                        <label for="employee_code" class="form-label fw-bold">Employee Code</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                                            <input type="text" class="form-control" id="employee_code" name="employee_code"
                                                placeholder="Employee code" value="<?= esc($employee['employee_code'] ?? ''); ?>" readonly>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="col-md-6">
                                    <label for="first_name" class="form-label fw-bold">First Name <span class="text-danger">*</span></label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" id="first_name" name="first_name"
                                            placeholder="Enter first name" value="<?= esc($employee['first_name'] ?? ''); ?>" required>
                                        <div class="invalid-feedback">Please provide first name.</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="last_name" class="form-label fw-bold">Last Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" id="last_name" name="last_name"
                                            placeholder="Enter last name" value="<?= esc($employee['last_name'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Enter email" value="<?= esc($employee['email'] ?? ''); ?>" required>
                                        <div class="invalid-feedback">Please provide a valid email.</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-bold">Phone <span class="text-danger">*</span></label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                            placeholder="Enter phone number" value="<?= esc($employee['phone'] ?? ''); ?>" required>
                                        <div class="invalid-feedback">Please provide a valid phone number.</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="designation" class="form-label fw-bold">Designation</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                        <input type="text" class="form-control" id="designation" name="designation"
                                            placeholder="Enter designation" value="<?= esc($employee['designation'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="department" class="form-label fw-bold">Department</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                                        <input type="text" class="form-control" id="department" name="department"
                                            placeholder="Enter department" value="<?= esc($employee['department'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>

                            <h5 class="section-title">Additional Information</h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="reporting_manager_id" class="form-label fw-bold">Reporting Manager</label>
                                    <select class="form-select" id="reporting_manager_id" name="reporting_manager_id">
                                        <option value="">Select Manager</option>
                                        <?php if (!empty($managers)): ?>
                                            <?php foreach ($managers as $manager): ?>
                                                <option value="<?= $manager['employee_id'] ?>"
                                                    <?= (isset($reporting_manager_id) && $reporting_manager_id == $manager['employee_id']) ? 'selected' : '' ?>>
                                                    <?= esc($manager['first_name'] . ' ' . $manager['last_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="empstatus" name="status" required>
                                        <option value="Active" <?= (isset($employee['status']) && $employee['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="Inactive" <?= (isset($employee['status']) && $employee['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                        <option value="Suspended" <?= (isset($employee['status']) && $employee['status'] == 'Suspended') ? 'selected' : ''; ?>>Suspended</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a status.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="dob" class="form-label fw-bold">Date of Birth</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        <input type="date" class="form-control" id="dob" name="dob"
                                            value="<?= isset($employee) ? $employee['dob'] : ''; ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="joining_date" class="form-label fw-bold">Joining Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        <input type="date" class="form-control" id="joining_date" name="joining_date"
                                            value="<?= isset($employee) ? $employee['joining_date'] : ''; ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="gender" class="form-label fw-bold">Gender</label>
                                    <select class="form-select" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="Male" <?= (isset($employee['gender']) && $employee['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?= (isset($employee['gender']) && $employee['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                        <option value="Other" <?= (isset($employee['gender']) && $employee['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="profile_image" class="form-label fw-bold">Profile Image</label>
                                    <div class="img-preview-container">
                                        <label for="profile_image" class="file-upload-label">
                                            <i class="fas fa-cloud-upload-alt me-2"></i>Choose Image
                                        </label>
                                        <input type="file" class="file-upload-input" id="profile_image" name="profile_image"
                                            accept="image/*" onchange="previewImage(event, 'profilePreview')">

                                        <?php
                                        $imagePath = base_url('assets/images/default.jpg');
                                        if (!empty($employee['profile_image'])) {
                                            $publicPath = FCPATH . 'uploads/employees/' . $employee['profile_image'];
                                            if (file_exists($publicPath)) {
                                                $imagePath = base_url('uploads/employees/' . $employee['profile_image']);
                                            }
                                        }
                                        ?>

                                        <img id="profilePreview" class="img-preview" src="<?= $imagePath ?>" alt="Profile Preview">

                                        <?php if (!empty($employee['profile_image']) && $imagePath !== base_url('assets/images/default.jpg')): ?>
                                            <div class="text-center">
                                                <a href="<?= $imagePath ?>" class="btn btn-sm btn-outline-primary download-btn"
                                                    download="<?= $employee['profile_image'] ?>">
                                                    <i class="fas fa-download me-1"></i> Download Current Image
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label fw-bold">Address</label>
                                    <textarea class="form-control" id="address" name="address"
                                        placeholder="Enter employee address" rows="3"><?= esc($employee['address'] ?? ''); ?></textarea>
                                </div>

                                <div class="col-12 mt-3">
                                    <div class="form-check form-switch">
                                        <!-- Hidden input to ensure geo_tracking is sent even when unchecked -->
                                        <input type="hidden" name="geo_tracking" value="0">
                                        <input class="form-check-input" type="checkbox" id="geo_tracking" name="geo_tracking" value="1" 
                                            <?= (!isset($employee['geo_tracking']) || $employee['geo_tracking'] == 1) ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold" for="geo_tracking">
                                            Enable Location Tracking
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-between">
                                <a href="<?= site_url("employees"); ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                                </a>
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="fas fa-save me-2"></i> <?= isset($employee) ? 'Update Employee' : 'Create Employee'; ?>
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
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            Array.prototype.forEach.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();

    $(document).ready(function() {
        // Show file name when selected
        document.getElementById('profile_image').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'No file chosen';
            document.querySelector('.file-upload-label').innerHTML =
                `<i class="fas fa-cloud-upload-alt me-2"></i>${fileName}`;
        });
    });

    function previewImage(event, previewId) {
        const file = event.target.files[0];
        const output = document.getElementById(previewId);
        const downloadBtn = document.querySelector('.download-btn');

        if (file) {
            output.src = URL.createObjectURL(file);
            output.style.display = 'block';

            // Hide download button when new image is selected
            if (downloadBtn) {
                downloadBtn.style.display = 'none';
            }
        }
    }
</script>

<?= $this->endSection() ?>