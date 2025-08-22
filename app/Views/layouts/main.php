<!doctype html>
<html lang="en">
<?php
$session = session();
/* 
Include the header view file
This will load the content from header.php and insert it at this point
*/
echo $this->include('layouts/header');
?>

<body>

    <!-- Begin page -->
    <?= view('layouts/topmenu'); ?>
    <?= $this->renderSection('content'); ?>
    <?= $this->include('layouts/footer'); ?>

    <!-- The Modal -->
    <div class="modal login-modal fade" id="myModal" style="background-color: rgba(0, 0, 0, .6)!important;">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border border-0 rounded-0 shadow-lg">

                <!-- Modal Header -->
                <div class="modal-header border border-0 bg-light">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs justify-content-start align-items-center flex-nowrap px-4" role="tablist" style="border: none!important;">
                        <li class="nav-item">
                            <a class="nav-link d-flex active border border-0" data-bs-toggle="tab" href="#login" role="tab">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex border border-0" data-bs-toggle="tab" href="#register" role="tab">Register</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="login" role="tabpanel">
                            <div class="card rounded-bottom border border-0">
                                <div class="card-body px-4 py-4">
                                    <form class="form-horizontal needs-validation" id="loginForm" method="post" autocomplete="off">
                                        <?= csrf_field() ?>
                                        <div id="modalMessages"></div>
                                        <div class="right-menu">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control form-input-bg" id="username" name="username" placeholder="+919894574385" required autocomplete="off" inputmode="text">
                                                <label for="username">Username</label>
                                                <div class="invalid-feedback">Username is required</div>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input type="password" class="form-control form-input-bg" id="password" name="password" placeholder="password" required autocomplete="new-password">
                                                <label for="password">Password</label>
                                                <div class="invalid-feedback">Password is required</div>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <div class="form-check form-check-inline ps-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="contract">
                                                    <label class="custom-control-label text-secondary ms-1" for="contract" style="font-size: 14px; line-height: 13px;">Remember me</label>
                                                </div>
                                            </div>
                                        </div>

                                        <a href="#" class="text-secondary fw-12 text-start" style="font-size: 14px; line-height: 13px;">Lost your password?</a>

                                        <div class="d-flex align-items-stretch button-group mb-2 mt-2">
                                            <button type="submit" class="btn text-light w-100 py-3 bg-dark border border-0 rounded-0" style="background-color: #080808!important;">
                                                Login
                                            </button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="register" role="tabpanel">
                            <div class="card rounded-bottom border border-0">
                                <div class="card-body px-4 py-4">
                                    <form class="form-horizontal needs-validation" id="registerForm" method="post" action="<?= site_url('register') ?>" autocomplete="off">
                                        <?= csrf_field() ?>
                                        <div class="right-menu">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control form-input-bg" id="name" name="name" placeholder="Name" required autocomplete="off">
                                                <label for="name">Name</label>
                                                <div class="invalid-feedback">Name is required</div>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input type="email" class="form-control form-input-bg" id="email" name="email" placeholder="Email" required autocomplete="off">
                                                <label for="email">Email</label>
                                                <div class="invalid-feedback">Email is required</div>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input type="tel" class="form-control form-input-bg" id="phone" name="phone" placeholder="Phone Number" required autocomplete="off">
                                                <label for="phone">Phone Number</label>
                                                <div class="invalid-feedback">Phone number is required</div>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input type="password" class="form-control form-input-bg" id="password" name="password" placeholder="Password" required autocomplete="new-password">
                                                <label for="password">Password</label>
                                                <div class="invalid-feedback">Password is required</div>
                                                <small class="text-danger" id="passwordError"></small>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input type="password" class="form-control form-input-bg" id="confirmPassword" name="repeat_password" placeholder="Repeat Password" required autocomplete="new-password">
                                                <label for="confirmPassword">Confirm Password</label>
                                                <div class="invalid-feedback">Password confirmation is required</div>
                                                <small class="text-danger" id="confirmPasswordError"></small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <div class="form-check form-check-inline ps-2">
                                                <div class="custom-control custom-checkbox">
                                                    <label class="custom-control-label text-secondary ms-1" for="customCheck1" style="font-size: 14px; line-height: 20px;">
                                                        Your personal data will be used to support your experience throughout this website, to manage access to your account, and for other purposes described in our <a href="#" style="color: #080808;">privacy policy.</a>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-stretch button-group mb-2 mt-2">
                                            <button type="submit" class="btn text-light w-100 py-3 bg-dark border border-0 rounded-0">
                                                Register
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
            // Login Form AJAX
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();

                var url = "<?= site_url('login/verify') ?>";
                var formData = new FormData(this);

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        $('#modalMessages').html('');

                        if (response.status === 'error') {
                            let errorMessages = '<ul>';

                            if (response.errors) {
                                for (let field in response.errors) {
                                    if (response.errors.hasOwnProperty(field)) {
                                        errorMessages += `<li>${response.errors[field]}</li>`;
                                    }
                                }
                            } else if (response.message) {
                                errorMessages += `<li>${response.message}</li>`;
                            }

                            errorMessages += '</ul>';

                            $('#modalMessages').html(`
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    ${errorMessages}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `);

                            if (response.user_not_found) {
                                var registerTab = new bootstrap.Tab(document.querySelector('a[href="#register"]'));
                                registerTab.show();

                                $('#registerForm').prepend(`
                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        User not found. Please register below.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                `);
                            }

                        } else if (response.status === 'success') {
                            $('#modalMessages').html(`
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    Login successful! Redirecting...
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `);

                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        }
                    },
                    error: function() {
                        $('#modalMessages').html(`
                            <div class="alert alert-danger">Something went wrong. Please try again.</div>
                        `);
                    }
                });
            });

            $('#registerForm').on('submit', function(e) {
                e.preventDefault();

                // Clear previous error messages
                $('#passwordError').text('');
                $('#confirmPasswordError').text('');

                let password = $('#password').val().trim();
                let confirmPassword = $('#confirmPassword').val().trim();

                if (password !== confirmPassword) {
                    $('#confirmPasswordError').text('Passwords do not match.');
                    return;
                }

                let formData = new FormData(this);

                $.ajax({
                    url: "<?= site_url('register') ?>",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function(response) {
                        $('#registerForm .alert').remove();

                        if (response.status === 'error') {
                            // Show validation errors
                            let errorMessages = '<ul>';
                            for (let key in response.errors) {
                                errorMessages += `<li>${response.errors[key]}</li>`;
                            }
                            errorMessages += '</ul>';

                            $('#registerForm').prepend(`
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    ${errorMessages}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `);
                        } else if (response.status === 'exists') {
                            // Show warning and switch to login tab
                            $('#registerForm').prepend(`
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    ${response.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `);

                            // Clear login form alerts and optionally reset it
                            $('#login .alert').remove();
                            $('#loginForm')[0].reset();
                            $('#login input[name="username"]').val(response.suggest_email);

                            setTimeout(() => {
                                var loginTab = new bootstrap.Tab(document.querySelector('a[href="#login"]'));
                                loginTab.show();
                            }, 1500);
                        } else if (response.status === 'success') {
                            // Show success message and switch to login tab
                            $('#registerForm').prepend(`
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    ${response.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `);

                            // Clear login form alerts and optionally reset it
                            $('#login .alert').remove();
                            $('#loginForm')[0].reset();
                            $('#login input[name="username"]').val(response.suggest_email);

                            setTimeout(() => {
                                var loginTab = new bootstrap.Tab(document.querySelector('a[href="#login"]'));
                                loginTab.show();
                            }, 1500);
                        }
                    },
                    error: function() {
                        $('#registerForm .alert').remove();
                        $('#registerForm').prepend(`
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                Something went wrong. Please try again later.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `);
                    }
                });
            });

            $('body').on('click', '.cart-link', function(e) {
                <?php if (!session()->has('user_id')): ?>
                    e.preventDefault();
                    var loginModal = new bootstrap.Modal(document.getElementById('myModal'));
                    loginModal.show();

                    // Optional: Add a message to the modal
                    $('#modalMessages').html(`
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            Please login to view your cart
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `);
                <?php endif; ?>
            });

        });
    </script>
</body>

</html>