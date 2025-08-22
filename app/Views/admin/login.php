<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Login | Kahipay - Access Your Account</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Secure login page for Kahipay administration">

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/images/apple-touch-icon.png'); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/images/favicon-32x32.png'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/images/favicon-16x16.png'); ?>">
    <link rel="manifest" href="<?= base_url('assets/images/site.webmanifest'); ?>">

    <!-- Fonts and Icons -->
    <link href="https://cdn.materialdesignicons.com/5.9.55/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" id="bootstrap-style" rel="stylesheet">
    <link href="<?= base_url('assets/css/icons.min.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.min.css'); ?>" id="app-style" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #541D6C;
            --primary-light: #F0E6F5;
            --secondary-color: #6c757d;
            --accent-color: #7C4DFF;
            --error-color: #dc3545;
            --success-color: #28a745;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            background-image: url('<?= base_url('assets/images/auth-bg-pattern.png'); ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #495057;
        }

        .auth-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .auth-card:hover {
            transform: translateY(-5px);
        }

        .auth-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #7C4DFF 100%);
            color: white;
            padding: 2rem;
        }

        .auth-logo {
            background: white;
            border-radius: 8px;
            padding: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-top: -40px;
            margin-bottom: 1rem;
            display: inline-block;
        }

        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(84, 29, 108, 0.15);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 12px;
            border-radius: 8px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: #3a1352;
            border-color: #3a1352;
            transform: translateY(-2px);
        }

        .password-toggle {
            cursor: pointer;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .auth-footer {
            text-align: center;
            margin-top: 2rem;
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .error-message {
            color: var(--error-color);
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        .success-message {
            color: var(--success-color);
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #e0e0e0;
        }

        .divider-text {
            padding: 0 1rem;
            color: var(--secondary-color);
            font-size: 0.85rem;
        }

        @media (max-width: 575.98px) {
            .auth-header {
                padding: 1.5rem;
            }

            .auth-logo {
                margin-top: -30px;
            }
        }
    </style>
</head>

<body>
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card auth-card">
                        <div class="auth-header text-center">
                            <h4 class="mb-1">Welcome to Kahipay</h4>
                            <p class="mb-0">Please sign in to your account</p>
                        </div>

                        <div class="card-body p-4">
                            <!-- Flash Messages -->
                            <?= view('flash_messages'); ?>

                            <form class="form-horizontal needs-validation" method="post" action="<?= site_url('verify-login') ?>" novalidate>
                                <?= csrf_field() ?>

                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="email" class="form-control" name="username" id="username"
                                        placeholder="Enter your email" required>
                                    <div class="invalid-feedback">
                                        Please enter a valid username.
                                    </div>
                                    <?php if (session()->getFlashdata('error')): ?>
                                        <div class="error-message">
                                            <?= session()->getFlashdata('error') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="input-group auth-pass-inputgroup">
                                        <input type="password" class="form-control" name="password" id="password"
                                            placeholder="Enter your password" required>
                                        <button class="btn btn-outline-secondary password-toggle" type="button"
                                            id="password-toggle">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </button>
                                        <div class="invalid-feedback">
                                            Please enter your password.
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" name="remember" type="checkbox" id="remember-check">
                                        <label class="form-check-label" for="remember-check">Remember me</label>
                                    </div>
                                    <div>
                                        <a href="<?= site_url('forgot-password') ?>" class="text-muted small">
                                            <i class="mdi mdi-lock me-1"></i> Forgot password?
                                        </a>
                                    </div>
                                </div>

                                <div class="mt-3 d-grid">
                                    <button class="btn btn-primary" type="submit">
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        Sign In
                                    </button>
                                </div>

                                <?php if (isset($validation)): ?>
                                    <div class="alert alert-danger mt-3">
                                        <?= $validation->listErrors() ?>
                                    </div>
                                <?php endif; ?>
                            </form>

                            <!-- Social Login (optional) -->
                            <!--
                            <div class="divider">
                                <span class="divider-text">OR</span>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary" type="button">
                                    <i class="mdi mdi-google me-2"></i> Sign in with Google
                                </button>
                                <button class="btn btn-outline-primary" type="button">
                                    <i class="mdi mdi-microsoft me-2"></i> Sign in with Microsoft
                                </button>
                            </div>
                            -->

                            <div class="auth-footer mt-4 text-center">
                                <p class="mb-0">Don't have an account? <a href="#" class="text-primary">Sign up</a></p>
                                <p class="mb-0 mt-2">&copy; <?= date('Y') ?> Kahipay. All rights reserved.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="<?= base_url('assets/libs/jquery/jquery.min.js'); ?>"></script>
    <script src="<?= base_url('assets/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?= base_url('assets/libs/simplebar/simplebar.min.js'); ?>"></script>
    <script src="<?= base_url('assets/libs/node-waves/waves.min.js'); ?>"></script>

    <script>
        $(document).ready(function() {
            // Password toggle
            $('#password-toggle').on('click', function() {
                const $passwordInput = $('#password');
                const $icon = $(this).find('i');

                if ($passwordInput.attr('type') === 'password') {
                    $passwordInput.attr('type', 'text');
                    $icon.removeClass('mdi-eye-outline').addClass('mdi-eye');
                } else {
                    $passwordInput.attr('type', 'password');
                    $icon.removeClass('mdi-eye').addClass('mdi-eye-outline');
                }
            });

            // Form validation and spinner handling
            $('.needs-validation').on('submit', function(event) {
                const form = this;

                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    const $submitButton = $(form).find('button[type="submit"]');
                    const $spinner = $submitButton.find('.spinner-border');
                    $submitButton.prop('disabled', true);
                    $spinner.removeClass('d-none');
                }

                $(form).addClass('was-validated');
            });

            // Fade-in animation for form elements
            $('.form-control, .btn').css({
                opacity: 0,
                transform: 'translateY(20px)',
                transition: 'opacity 0.5s ease, transform 0.5s ease'
            }).each(function(index, element) {
                setTimeout(function() {
                    $(element).css({
                        opacity: 1,
                        transform: 'translateY(0)'
                    });
                }, 100 + (index * 100));
            });
        });
    </script>
</body>

</html>