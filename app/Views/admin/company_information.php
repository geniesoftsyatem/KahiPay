<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .img-preview {
        max-width: 200px;
        max-height: 150px;
        margin-top: 10px;
        display: none;
        border-radius: 5px;
        object-fit: cover;
        border: 1px solid #ddd;
    }

    .section-title {
        position: relative;
        padding-left: 15px;
        color: #495057;
        font-weight: 600;
    }

    .section-title:before {
        content: "";
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        height: 70%;
        width: 4px;
        background-color: #3b5de7;
        border-radius: 4px;
    }

    .logo-container {
        border: 1px dashed #dee2e6;
        border-radius: 8px;
        padding: 20px;
        background-color: #f8fafc;
        transition: all 0.3s ease;
    }

    .logo-container:hover {
        border-color: #3b5de7;
        background-color: #f1f5ff;
    }

    .logo-upload-label {
        cursor: pointer;
        display: block;
        text-align: center;
    }

    .logo-upload-label:hover .upload-icon {
        transform: translateY(-3px);
    }

    .upload-icon {
        font-size: 2rem;
        color: #3b5de7;
        transition: all 0.3s ease;
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-body {
        padding: 2rem;
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <!-- Start Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Company Information</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Company Settings</li>
                    </ol>
                </div>
            </div>
        </div>
        <!-- End Page Title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <!-- Left Side Image -->
                            <div class="col-md-6 d-none d-md-block">
                                <div class="text-center p-4">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1570/1570887.png"
                                        alt="Company Information"
                                        class="img-fluid"
                                        style="max-height: 400px;">
                                    <h4 class="mt-3">Company Profile</h4>
                                    <p class="text-muted">Update your company details to keep your profile current and professional.</p>
                                </div>
                            </div>

                            <!-- Right Side Form -->
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-light text-primary rounded-circle fs-2">
                                                <i class="fas fa-building"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="card-title mb-1">Company Details</h4>
                                        <p class="text-muted mb-0">Update your company information and branding.</p>
                                    </div>
                                </div>

                                <?= view('flash_messages') ?>

                                <form class="needs-validation" method="post" action="<?= site_url('settings/company/save'); ?>" enctype="multipart/form-data" novalidate>
                                    <?= csrf_field(); ?>

                                    <!-- Company Details -->
                                    <div class="mb-3">
                                        <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                                            <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Enter company name" value="<?= esc($company['company_name'] ?? ''); ?>" required>
                                            <div class="invalid-feedback">Please provide company name.</div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" value="<?= esc($company['email'] ?? ''); ?>">
                                            <div class="invalid-feedback">Please provide a valid email.</div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter phone number" value="<?= esc($company['phone'] ?? ''); ?>">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="website" class="form-label">Website</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                            <input type="text" class="form-control" id="website" name="website" placeholder="Enter website URL" value="<?= esc($company['website'] ?? ''); ?>">
                                        </div>
                                    </div>

                                    <!-- Address Section -->
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control" id="address" name="address" placeholder="Enter company address" rows="2"><?= esc($company['address'] ?? ''); ?></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="city" class="form-label">City</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-city"></i></span>
                                                <input type="text" class="form-control" id="city" name="city" placeholder="Enter city" value="<?= esc($company['city'] ?? ''); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="state" class="form-label">State</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-map-marked-alt"></i></span>
                                                <input type="text" class="form-control" id="state" name="state" placeholder="Enter state" value="<?= esc($company['state'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="country" class="form-label">Country</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-flag"></i></span>
                                                <input type="text" class="form-control" id="country" name="country" placeholder="Enter country" value="<?= esc($company['country'] ?? ''); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="zipcode" class="form-label">Zipcode</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-mail-bulk"></i></span>
                                                <input type="text" class="form-control" id="zipcode" name="zipcode" placeholder="Enter zip/postal code" value="<?= esc($company['pincode'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Logo Upload -->
                                    <div class="mb-4">
                                        <label class="form-label">Company Logo</label>
                                        <div class="logo-container">
                                            <label class="logo-upload-label">
                                                <input type="file" id="logo" name="logo" accept="image/*" onchange="previewImage(event, 'logoPreview')" class="d-none">

                                                <?php
                                                $logoPath = base_url('assets/images/default.jpg');
                                                if (!empty($company['logo'])) {
                                                    $publicPath = FCPATH . 'uploads/company/' . $company['logo'];
                                                    if (file_exists($publicPath)) {
                                                        $logoPath = base_url('uploads/company/' . $company['logo']);
                                                    }
                                                }
                                                ?>

                                                <div class="text-center">
                                                    <i class="fas fa-cloud-upload-alt upload-icon mb-2"></i>
                                                    <h6 class="mb-2">Upload Company Logo</h6>
                                                    <p class="text-muted small mb-3">Recommended size: 300x300px (PNG, JPG)</p>

                                                    <div class="d-flex justify-content-center">
                                                        <img id="logoPreview" class="img-fluid rounded mb-2 mx-auto"
                                                            style="max-height: 150px; display: <?= ($logoPath !== base_url('assets/images/default.jpg')) ? 'block' : 'none' ?>;"
                                                            src="<?= $logoPath ?>" alt="Company Logo">
                                                    </div>

                                                    <?php if (!empty($company['logo']) && $logoPath !== base_url('assets/images/default.jpg')): ?>
                                                        <div class="mt-2">
                                                            <a href="<?= $logoPath ?>" class="btn btn-sm btn-outline-primary" download="<?= $company['logo'] ?>">
                                                                <i class="fas fa-download me-1"></i> Download Logo
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="<?= base_url('dashboard') ?>" class="btn btn-light">
                                            <i class="ri-arrow-left-line align-bottom me-1"></i> Back
                                        </a>
                                        <button type="reset" class="btn btn-light">
                                            <i class="ri-refresh-line align-bottom me-1"></i> Reset
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="ri-save-line align-bottom me-1"></i> Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
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
        const $imgPreview = $('#' + previewId);
        const reader = new FileReader();

        reader.onload = function() {
            $imgPreview.attr('src', reader.result).show();
        };

        if (file) {
            reader.readAsDataURL(file);
        } else {
            $imgPreview.show();
        }
    }

    $(document).ready(function() {
        // Show logo preview if exists
        const $logoPreview = $('#logoPreview');
        if ($logoPreview.attr('src') && $logoPreview.attr('src') !== '<?= base_url('assets/images/default.jpg') ?>') {
            $logoPreview.show();
        }
    });
</script>

<?= $this->endSection() ?>