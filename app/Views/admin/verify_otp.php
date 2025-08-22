<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>OTP Verification | Kahipay - Secure Access</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Secure OTP verification page for Kahipay">

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

        /* OTP Specific Styles */
        .otp-container {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }

        .otp-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .otp-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(84, 29, 108, 0.15);
            outline: none;
        }

        .otp-input.filled {
            border-color: var(--primary-color);
        }

        .otp-message {
            text-align: center;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .otp-timer {
            text-align: center;
            margin-top: 15px;
            font-size: 0.9rem;
            color: var(--secondary-color);
        }

        .otp-resend {
            text-align: center;
            margin-top: 15px;
        }

        .otp-resend a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .otp-resend a:hover {
            text-decoration: underline;
        }

        .otp-resend.disabled {
            color: var(--secondary-color);
        }

        .otp-resend.disabled a {
            color: var(--secondary-color);
            pointer-events: none;
        }

        @media (max-width: 575.98px) {
            .auth-header {
                padding: 1.5rem;
            }

            .auth-logo {
                margin-top: -30px;
            }

            .otp-input {
                width: 40px;
                height: 50px;
                font-size: 20px;
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
                            <h4 class="mb-1">OTP Verification</h4>
                            <p class="mb-0">Enter the code sent to your email</p>
                        </div>

                        <div class="card-body p-4">
                            <!-- Flash Messages -->
                            <?= view('flash_messages'); ?>

                            <form class="form-horizontal needs-validation" method="post" action="<?= site_url('verify-otp') ?>" novalidate>
                                <?= csrf_field() ?>

                                <div class="otp-message">
                                    <p>We've sent a 6-digit verification code to<br>
                                        <strong>
                                            <?= isset($email) ? esc($email) : (session()->get('otp_email') ?? 'your registered email') ?>
                                        </strong>
                                    </p>
                                </div>

                                <div class="otp-container">
                                    <input type="text" class="form-control otp-input" name="otp1" id="otp1" maxlength="1" required autofocus>
                                    <input type="text" class="form-control otp-input" name="otp2" id="otp2" maxlength="1" required>
                                    <input type="text" class="form-control otp-input" name="otp3" id="otp3" maxlength="1" required>
                                    <input type="text" class="form-control otp-input" name="otp4" id="otp4" maxlength="1" required>
                                    <input type="text" class="form-control otp-input" name="otp5" id="otp5" maxlength="1" required>
                                    <input type="text" class="form-control otp-input" name="otp6" id="otp6" maxlength="1" required>
                                </div>

                                <input type="hidden" name="otp_code" id="otp_code">
                                <input type="hidden" id="hidden-email" value="<?= $email ?>">
                                <input type="hidden" name="otp_user" id="otp_user" value="<?= $email ?>">

                                <div class="otp-timer">
                                    <span id="countdown">05:00</span> remaining
                                </div>

                                <div class="otp-resend disabled" id="resend-container">
                                    Didn't receive code? <a href="<?= site_url('resend-otp') ?>" id="resend-link">Resend</a>
                                </div>

                                <div class="mt-3 d-grid">
                                    <button class="btn btn-primary" type="submit">
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        Verify & Continue
                                    </button>
                                </div>

                                <div class="mt-3 text-center">
                                    <a href="<?= site_url('login') ?>" class="text-muted">
                                        <i class="mdi mdi-arrow-left me-1"></i> Back to Login
                                    </a>
                                </div>

                                <?php if (isset($validation)): ?>
                                    <div class="alert alert-danger mt-3">
                                        <?= $validation->listErrors() ?>
                                    </div>
                                <?php endif; ?>
                            </form>

                            <div class="auth-footer mt-4 text-center">
                                <p class="mb-0">&copy; <?= date('Y') ?> Kahipay. All rights reserved.</p>
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
            // OTP Input Handling
            const otpInputs = $('.otp-input');

            otpInputs.on('input', function() {
                const currentInput = $(this);
                const currentValue = currentInput.val();

                // Auto-focus to next input
                if (currentValue.length === 1) {
                    const currentIndex = otpInputs.index(currentInput);
                    if (currentIndex < otpInputs.length - 1) {
                        otpInputs.eq(currentIndex + 1).focus();
                    }
                    currentInput.addClass('filled');
                } else {
                    currentInput.removeClass('filled');
                }

                // Combine all OTP digits into the hidden field
                updateFullOTP();
            });

            // Handle backspace
            otpInputs.on('keydown', function(e) {
                if (e.key === 'Backspace' || e.key === 'Delete') {
                    const currentInput = $(this);
                    const currentIndex = otpInputs.index(currentInput);

                    if (currentInput.val() === '' && currentIndex > 0) {
                        otpInputs.eq(currentIndex - 1).focus();
                    }
                    currentInput.removeClass('filled');
                }
            });

            function updateFullOTP() {
                let fullOTP = '';
                otpInputs.each(function() {
                    fullOTP += $(this).val();
                });
                $('#otp_code').val(fullOTP);
            }

            // Countdown Timer
            let timeLeft = 300; // 5 minutes
            const countdownElement = $('#countdown');
            const resendContainer = $('#resend-container');
            const resendLink = $('#resend-link');

            function startCountdown() {
                timeLeft = 300; // reset timer
                resendContainer.addClass('disabled');
                resendLink.css('pointer-events', 'none').css('color', 'gray');

                const countdown = setInterval(function() {
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;

                    countdownElement.text(
                        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
                    );

                    if (timeLeft <= 0) {
                        clearInterval(countdown);
                        countdownElement.text('00:00');
                        resendContainer.removeClass('disabled');
                        resendLink.css('pointer-events', 'auto').css('color', '#007bff');
                    } else {
                        timeLeft--;
                    }
                }, 1000);
            }

            startCountdown(); // Start on page load

            // AJAX Resend OTP
            resendLink.on('click', function(e) {
                e.preventDefault();

                const resendUrl = $(this).attr('href');
                const userEmail = $('#hidden-email').val();

                resendLink.text('Sending...');
                resendLink.css('pointer-events', 'none');

                $.ajax({
                    url: resendUrl,
                    method: 'GET',
                    data: {
                        email: userEmail
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('.otp-message').html('<p class="text-success">A new OTP has been sent to your email.</p>');
                            startCountdown();
                        } else {
                            $('.otp-message').html('<p class="text-danger">' + response.message + '</p>');
                        }
                        resendLink.text('Resend');
                        resendLink.css('pointer-events', 'auto');
                    },
                    error: function() {
                        $('.otp-message').html('<p class="text-danger">Failed to resend OTP. Please try again.</p>');
                        resendLink.text('Resend');
                        resendLink.css('pointer-events', 'auto');
                    }
                });
            });

            // Form validation and spinner handling
            $('.needs-validation').on('submit', function(event) {
                const form = this;
                updateFullOTP();

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