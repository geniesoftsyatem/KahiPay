<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

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
                                <i class="fas <?= isset($company) ? 'fa-building' : 'fa-plus-circle'; ?> text-primary me-2"></i>
                                <?= $pagetitle; ?>
                            </h4>
                            <a href="<?= site_url("companies"); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Back to List
                            </a>
                        </div>

                        <form class="needs-validation" method="post" action="<?= site_url('companies/save'); ?>" enctype="multipart/form-data" novalidate>
                            <?= csrf_field(); ?>
                            <?= view('flash_messages'); ?>
                            <input type="hidden" name="company_id" value="<?= $company['company_id'] ?? ''; ?>">

                            <h5 class="section-title">Company Information</h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="company_name" class="form-label fw-bold">Company Name <span class="text-danger">*</span></label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                                        <input type="text" class="form-control" id="company_name" name="company_name"
                                            placeholder="Enter company name" value="<?= esc($company['company_name'] ?? ''); ?>" required>
                                        <div class="invalid-feedback">Please provide company name.</div>
                                    </div>
                                </div>

                                <?php if (isset($company)): ?>
                                    <div class="col-md-6">
                                        <label for="company_code" class="form-label fw-bold">Company Code <span class="text-danger">*</span></label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text"><i class="fas fa-code"></i></span>
                                            <input type="text" class="form-control" id="company_code" name="company_code"
                                                placeholder="Company code will be auto-generated"
                                                value="<?= esc($company['company_code'] ?? ''); ?>" readonly>
                                            <div class="invalid-feedback">Company code is required.</div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-bold">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Enter email" value="<?= esc($company['email'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-bold">Phone</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                            placeholder="Enter phone number" value="<?= esc($company['phone'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-bold">Password <?= !isset($company) ? '<span class="text-danger">*</span>' : '' ?></label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Enter password" <?= !isset($company) ? 'required' : '' ?>>
                                        <button class="btn btn-outline-secondary password-toggle" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <div class="invalid-feedback">Please provide a password.</div>
                                    </div>
                                    <?php if (isset($company)): ?>
                                        <small class="text-muted">Leave blank to keep current password</small>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-6">
                                    <label for="confirm_password" class="form-label fw-bold">Confirm Password <?= !isset($company) ? '<span class="text-danger">*</span>' : '' ?></label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                            placeholder="Confirm password" <?= !isset($company) ? 'required' : '' ?>>
                                        <button class="btn btn-outline-secondary password-toggle" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <div class="invalid-feedback">Please confirm your password.</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="website" class="form-label fw-bold">Website</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                        <input type="url" class="form-control" id="website" name="website"
                                            placeholder="Enter website URL" value="<?= esc($company['website'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="comStatus" name="status" required>
                                        <option value="Active" <?= (isset($company['status']) && $company['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="Inactive" <?= (isset($company['status']) && $company['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                        <option value="Suspended" <?= (isset($company['status']) && $company['status'] == 'Suspended') ? 'selected' : ''; ?>>Suspended</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a status.</div>
                                </div>
                            </div>

                            <h5 class="section-title">Legal & Address Information</h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="pan" class="form-label fw-bold">PAN Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                        <input type="text" class="form-control" id="pan" name="pan"
                                            placeholder="Enter PAN (e.g., ABCDE1234F)" value="<?= esc($company['pan'] ?? ''); ?>">
                                    </div>
                                    <small class="text-muted">Format: 5 letters + 4 digits + 1 letter</small>
                                </div>

                                <div class="col-md-6">
                                    <label for="gst" class="form-label fw-bold">GST Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-file-invoice"></i></span>
                                        <input type="text" class="form-control" id="gst" name="gst"
                                            placeholder="Enter GST (e.g., 22ABCDE1234F1Z5)" value="<?= esc($company['gst'] ?? ''); ?>">
                                    </div>
                                    <small class="text-muted">Format: 15 characters</small>
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label fw-bold">Address</label>
                                    <textarea class="form-control" id="address" name="address"
                                        placeholder="Enter company address" rows="3"><?= esc($company['address'] ?? ''); ?></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label for="logo" class="form-label fw-bold">Company Logo</label>
                                    <div class="img-preview-container">
                                        <label for="logo" class="file-upload-label">
                                            <i class="fas fa-cloud-upload-alt me-2"></i>Choose Logo
                                        </label>
                                        <input type="file" class="file-upload-input" id="logo" name="logo"
                                            accept="image/*" onchange="previewImage(event, 'logoPreview')">

                                        <?php
                                        $imagePath = base_url('assets/images/default.jpg');
                                        if (!empty($company['logo'])) {
                                            $publicPath = FCPATH . 'uploads/companies/' . $company['logo'];
                                            if (file_exists($publicPath)) {
                                                $imagePath = base_url('uploads/companies/' . $company['logo']);
                                            }
                                        }
                                        ?>

                                        <img id="logoPreview" class="img-preview" src="<?= $imagePath ?>" alt="Logo Preview">

                                        <?php if (!empty($company['logo']) && $imagePath !== base_url('assets/images/default.jpg')): ?>
                                            <div class="text-center">
                                                <a href="<?= $imagePath ?>" class="btn btn-sm btn-outline-primary download-btn"
                                                    download="<?= $company['logo'] ?>">
                                                    <i class="fas fa-download me-1"></i> Download Current Logo
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-between">
                                <a href="<?= site_url("companies"); ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                                </a>
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="fas fa-save me-2"></i> <?= isset($company) ? 'Update Company' : 'Create Company'; ?>
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
                    // Custom password validation
                    const password = document.getElementById('password');
                    const confirmPassword = document.getElementById('confirm_password');

                    if (password && confirmPassword && password.value !== confirmPassword.value) {
                        confirmPassword.setCustomValidity("Passwords do not match");
                        confirmPassword.reportValidity();
                        event.preventDefault();
                        event.stopPropagation();
                    } else {
                        confirmPassword.setCustomValidity("");
                    }

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
        document.getElementById('logo').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'No file chosen';
            document.querySelector('.file-upload-label').innerHTML =
                `<i class="fas fa-cloud-upload-alt me-2"></i>${fileName}`;
        });

        // Show/hide password toggle
        $('.password-toggle').on('click', function() {
            const input = $(this).siblings('input');
            const icon = $(this).find('i');

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Live password matching validation
        $('#confirm_password').on('input', function() {
            const password = $('#password').val();
            const confirmPassword = $(this).val();

            if (password !== confirmPassword) {
                $(this).get(0).setCustomValidity("Passwords do not match");
            } else {
                $(this).get(0).setCustomValidity("");
            }
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