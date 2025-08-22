<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-body {
        padding: 2rem;
    }

    .avatar-upload {
        position: relative;
        max-width: 200px;
        margin: 0 auto;
    }

    .avatar-upload .avatar-preview {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        border: 5px solid #f8f9fa;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .avatar-upload .btn-upload {
        position: absolute;
        bottom: 10px;
        right: 10px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #405189;
        color: white;
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .input-group-text {
        cursor: pointer;
    }
</style>
<div class="page-content">
    <div class="container-fluid">
        <!-- Start Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Profile Management</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Edit Profile</li>
                    </ol>
                </div>
            </div>
        </div>
        <!-- End Page Title -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (session()->has('errors')): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach (session('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="row align-items-center">
                            <!-- Left Side Image/Profile Picture -->
                            <div class="col-md-5 d-none d-md-block">
                                <div class="text-center p-4">
                                    <div class="avatar-upload">
                                        <div class="avatar-preview" id="avatarPreview"
                                            style="background-image: url('<?= !empty($user['profile_image'])
                                                                                ? base_url('uploads/users/' . esc($user['profile_image']))
                                                                                : base_url('assets/images/default.jpg') ?>');">
                                        </div>
                                        <button type="button" class="btn-upload">
                                            <i class="fas fa-camera"></i>
                                        </button>
                                    </div>
                                    <h4 class="mt-3">Profile Information</h4>
                                    <p class="text-muted">Update your personal information and profile picture.</p>
                                </div>
                            </div>

                            <!-- Right Side Form -->
                            <div class="col-md-7">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-light text-primary rounded-circle fs-2">
                                                <i class="fas fa-user-edit"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="card-title mb-1">Edit Profile</h4>
                                        <p class="text-muted mb-0">Update your personal information below.</p>
                                    </div>
                                </div>

                                <form class="needs-validation" id="profileForm" method="post" action="<?= site_url('update-profile') ?>" novalidate enctype="multipart/form-data">
                                    <?= csrf_field() ?>

                                    <input type="file" id="profileImage" name="profile_image" accept="image/*" style="display: none;">

                                    <!-- Name -->
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" value="<?= esc($user['name'] ?? '') ?>" required>
                                        <div class="invalid-feedback">Please enter your name</div>
                                    </div>

                                    <!-- Email -->
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" value="<?= esc($user['email'] ?? '') ?>" required readonly>
                                        <div class="invalid-feedback">Please enter a valid email address</div>
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" value="<?= esc($user['phone'] ?? '') ?>">
                                        <div class="invalid-feedback">Please enter a valid phone number</div>
                                    </div>

                                    <!-- Bio -->
                                    <div class="mb-4">
                                        <label for="notes" class="form-label">Bio</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Tell us about yourself"><?= esc($user['notes'] ?? '') ?></textarea>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="<?= base_url('dashboard') ?>" class="btn btn-light">
                                            <i class="ri-arrow-left-line align-bottom me-1"></i> Back
                                        </a>
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
    $(document).ready(function() {
        // Initialize elements
        const profileImageInput = $("#profileImage");
        const avatarPreview = $("#avatarPreview");
        const uploadButton = $(".btn-upload");

        // Handle profile image selection
        profileImageInput.on("change", function() {
            const file = this.files[0];
            const maxSize = 2 * 1024 * 1024; // 2MB
            const validTypes = ["image/jpeg", "image/png", "image/gif"];

            if (file) {
                // Validate file size
                if (file.size > maxSize) {
                    alert("File size should not exceed 2MB");
                    $(this).val(""); // Clear the file input
                    return false;
                }

                // Validate file type
                if (!validTypes.includes(file.type)) {
                    alert("Only JPEG, PNG, and GIF images are allowed");
                    $(this).val(""); // Clear the file input
                    return false;
                }

                // Preview image
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.css("background-image", "url(" + e.target.result + ")");
                };
                reader.readAsDataURL(file);
            }
            return true;
        });

        uploadButton.off("click").on("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            profileImageInput.trigger("click");
        });

        // Show loading state on form submission
        $("#profileForm").on("submit", function() {
            const submitBtn = $("#submitBtn");
            submitBtn.prop("disabled", true);
            submitBtn.html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
            );
        });
    });
</script>
<?= $this->endSection() ?>