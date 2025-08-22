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
        margin-bottom: 20px;
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
            <div class="col-lg-12 col-xl-12 mt-4">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0 text-dark">
                                <i class="fas <?= isset($user) ? 'fa-user-edit' : 'fa-user-plus'; ?> text-primary me-2"></i>
                                <?= $pagetitle; ?>
                            </h4>
                            <a href="<?php echo site_url("users"); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Back to List
                            </a>
                        </div>

                        <form class="needs-validation" method="post" action="<?= site_url('users/save'); ?>" enctype="multipart/form-data" novalidate>
                            <?= csrf_field(); ?>
                            <?= view('flash_messages'); ?>
                            <input type="hidden" name="id" value="<?= $user['user_id'] ?? ''; ?>">

                            <h5 class="section-title">User Information</h5>

                            <div class="row g-3">
                                <?php if (isset($user['user_id'])): ?>
                                    <div class="col-md-6">
                                        <label for="username" class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" class="form-control" readonly id="username" name="username"
                                                placeholder="Enter username" value="<?= esc($user['username'] ?? ''); ?>" required>
                                            <div class="invalid-feedback">Please provide a username.</div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Enter full name" value="<?= esc($user['name'] ?? ''); ?>" required>
                                        <div class="invalid-feedback">Please provide full name.</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Enter email" value="<?= esc($user['email'] ?? ''); ?>" required>
                                        <div class="invalid-feedback">Please provide a valid email.</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-bold">Phone <span class="text-danger">*</span></label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                            placeholder="Enter phone number" value="<?= esc($user['phone'] ?? ''); ?>"
                                            required pattern="[0-9]{10}">
                                        <div class="invalid-feedback">Please provide a valid 10-digit phone number.</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="alt_mobile_number" class="form-label fw-bold">Alternate Mobile</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                        <input type="tel" class="form-control" id="alt_mobile_number" name="alt_mobile_number"
                                            placeholder="Enter alternate mobile number" value="<?= esc($user['alt_mobile_number'] ?? ''); ?>"
                                            pattern="[0-9]{10}">
                                        <div class="invalid-feedback">Please provide a valid 10-digit alternate mobile number.</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="gender" class="form-label fw-bold">Gender</label>
                                    <select class="form-select" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male" <?= (isset($user['gender']) && strtolower($user['gender']) === 'male') ? 'selected' : ''; ?>>Male</option>
                                        <option value="female" <?= (isset($user['gender']) && strtolower($user['gender']) === 'female') ? 'selected' : ''; ?>>Female</option>
                                        <option value="other" <?= (isset($user['gender']) && strtolower($user['gender']) === 'other') ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="userStatus" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="userStatus" name="status" required>
                                        <option value="1" <?= (isset($user['status']) && $user['status'] == 1) ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?= (isset($user['status']) && $user['status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a status.</div>
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
                                        if (!empty($user['profile_image'])) {
                                            $publicPath = FCPATH . 'uploads/users/' . $user['profile_image'];
                                            if (file_exists($publicPath)) {
                                                $imagePath = base_url('uploads/users/' . $user['profile_image']);
                                            }
                                        }
                                        ?>

                                        <img id="profilePreview" class="img-preview" src="<?= $imagePath ?>" alt="Profile Preview">

                                        <?php if (!empty($user['profile_image']) && $imagePath !== base_url('assets/images/default.jpg')): ?>
                                            <div class="text-center">
                                                <a href="<?= $imagePath ?>" class="btn btn-sm btn-outline-primary download-btn" download="<?= $user['profile_image'] ?>">
                                                    <i class="fas fa-download me-1"></i> Download Current Image
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="notes" class="form-label fw-bold">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes"
                                        placeholder="Enter any notes about this user" rows="3"><?= esc($user['notes'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-between">
                                <a href="<?php echo site_url("users"); ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                                </a>
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="fas fa-save me-2"></i> <?= isset($user) ? 'Update User' : 'Create User'; ?>
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

    // Show file name when selected
    document.getElementById('profile_image').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'No file chosen';
        document.querySelector('.file-upload-label').innerHTML =
            `<i class="fas fa-cloud-upload-alt me-2"></i>${fileName}`;
    });
</script>

<?= $this->endSection() ?>