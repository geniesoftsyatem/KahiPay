<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .password-requirements .requirement {
        color: #6c757d;
    }

    .password-requirements .requirement.valid {
        color: #198754;
    }

    .password-requirements .requirement.valid i {
        color: #198754;
    }

    .progress-bar {
        transition: width 0.3s ease;
    }

    #passwordMatch {
        font-size: 0.875rem;
    }

    .match {
        color: #198754;
    }

    .mismatch {
        color: #dc3545;
    }

    .input-group-text {
        cursor: pointer;
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
                    <h4 class="mb-sm-0">Password Management</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Change Password</li>
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
                                    <img src="<?= base_url('assets/images/password-security.png') ?>" alt="Password Security" class="img-fluid" style="max-height: 400px;">
                                    <h4 class="mt-3">Secure Your Account</h4>
                                    <p class="text-muted">Create a strong password to protect your account from unauthorized access.</p>
                                </div>
                            </div>

                            <!-- Right Side Form -->
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-light text-primary rounded-circle fs-2">
                                                <i class="fas fa-lock"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="card-title mb-1">Change Password</h4>
                                        <p class="text-muted mb-0">Create a strong, unique password to secure your account.</p>
                                    </div>
                                </div>

                                <?= view('flash_messages') ?>

                                <form class="needs-validation" id="passwordForm" method="post" action="<?= site_url('update-password') ?>" novalidate>
                                    <?= csrf_field() ?>

                                    <!-- Current Password -->
                                    <div class="mb-3">
                                        <label for="currentPassword" class="form-label">Current Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="currentPassword" name="currentPassword" placeholder="Enter current password" required>
                                            <button class="btn btn-outline-secondary password-toggle" type="button" onclick="togglePassword('currentPassword')">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                        </div>
                                        <div class="invalid-feedback">Please enter your current password</div>
                                    </div>

                                    <!-- New Password -->
                                    <div class="mb-3">
                                        <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password" name="password" required placeholder="Enter new password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">
                                            <button class="btn btn-outline-secondary password-toggle" type="button" onclick="togglePassword('password')">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                        </div>
                                        <div class="invalid-feedback">
                                            Password must contain at least 8 characters, including uppercase, lowercase and numbers
                                        </div>
                                        <div class="password-strength mt-2">
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                            </div>
                                            <small class="text-muted">Password strength: <span id="strengthText">Weak</span></small>
                                        </div>
                                        <div class="password-requirements mt-2">
                                            <small class="d-block text-muted">Password requirements:</small>
                                            <ul class="text-muted ps-3 mb-0" style="list-style-type: none;">
                                                <li class="requirement length"><i class="ri-checkbox-blank-circle-fill fs-10 me-2"></i> Minimum 8 characters</li>
                                                <li class="requirement uppercase"><i class="ri-checkbox-blank-circle-fill fs-10 me-2"></i> At least one uppercase letter</li>
                                                <li class="requirement lowercase"><i class="ri-checkbox-blank-circle-fill fs-10 me-2"></i> At least one lowercase letter</li>
                                                <li class="requirement number"><i class="ri-checkbox-blank-circle-fill fs-10 me-2"></i> At least one number</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="mb-4">
                                        <label for="confirmPassword" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control password-toggle" id="confirmPassword" name="confirmPassword" placeholder="Confirm new password" required>
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirmPassword')">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                        </div>
                                        <div class="invalid-feedback">Passwords must match</div>
                                        <div id="passwordMatch" class="mt-1"></div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="<?= base_url('dashboard') ?>" class="btn btn-light">
                                            <i class="ri-arrow-left-line align-bottom me-1"></i> Back
                                        </a>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="ri-lock-line align-bottom me-1"></i> Update Password
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
        // Password toggle functionality
        $('.password-toggle').on('click', function() {
            const button = $(this);
            const input = button.closest('.input-group').find('input');
            const icon = button.find('i');

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            }
        });

        // Password strength checker
        $('#password').on('input', function() {
            const password = $(this).val();
            let strength = 0;

            // Check requirements
            const hasMinLength = password.length >= 8;
            const hasUpperCase = /[A-Z]/.test(password);
            const hasLowerCase = /[a-z]/.test(password);
            const hasNumber = /\d/.test(password);

            // Update requirement indicators
            $('.requirement.length').toggleClass('text-success', hasMinLength);
            $('.requirement.uppercase').toggleClass('text-success', hasUpperCase);
            $('.requirement.lowercase').toggleClass('text-success', hasLowerCase);
            $('.requirement.number').toggleClass('text-success', hasNumber);

            // Calculate strength
            if (hasMinLength) strength += 25;
            if (hasUpperCase) strength += 25;
            if (hasLowerCase) strength += 25;
            if (hasNumber) strength += 25;

            // Update strength meter
            const strengthBar = $('.progress-bar');
            strengthBar.css('width', strength + '%');

            // Update strength text
            const strengthText = $('#strengthText');
            if (strength < 50) {
                strengthBar.removeClass('bg-warning bg-success').addClass('bg-danger');
                strengthText.text('Weak');
            } else if (strength < 75) {
                strengthBar.removeClass('bg-danger bg-success').addClass('bg-warning');
                strengthText.text('Moderate');
            } else {
                strengthBar.removeClass('bg-danger bg-warning').addClass('bg-success');
                strengthText.text('Strong');
            }

            // Check password match
            checkPasswordMatch();
        });

        // Confirm password match checker
        $('#confirmPassword').on('input', checkPasswordMatch);

        function checkPasswordMatch() {
            const password = $('#password').val();
            const confirmPassword = $('#confirmPassword').val();
            const matchElement = $('#passwordMatch');

            if (password && confirmPassword) {
                if (password === confirmPassword) {
                    matchElement.html('<i class="fas fa-check-circle text-success"></i> Passwords match');
                    $('#confirmPassword')[0].setCustomValidity('');
                } else {
                    matchElement.html('<i class="fas fa-times-circle text-danger"></i> Passwords do not match');
                    $('#confirmPassword')[0].setCustomValidity("Passwords don't match");
                }
            } else {
                matchElement.html('');
            }
        }

        // Form validation
        $('#passwordForm').on('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            $(this).addClass('was-validated');
        });
    });
</script>
<?= $this->endSection() ?>