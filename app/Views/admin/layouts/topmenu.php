<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="<?php echo site_url('dashboard'); ?>" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="<?= base_url('assets/images/logo.svg'); ?>" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="<?= base_url('assets/images/logo-dark.png'); ?>" alt="" height="17">
                    </span>
                </a>

                <a href="<?php echo site_url('dashboard'); ?>" class="logo logo-light mb-0">
                    <span class="logo-sm">
                        <img src="<?= base_url('assets/images/logo-light.svg'); ?>" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="<?= base_url('assets/images/kahipay_logo.jpg'); ?>" style="margin-top: 10px; width:200px;" alt="" height="70">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>

        </div>

        <div class="d-none d-lg-block" style="text-align: center;">
            <div id="day-yearDisplay" class="text-dark font-size-18"></div>
            <div id="timeDisplay" class="text-dark font-size-18"></div>
        </div>

        <div class="d-flex">

            <div class="dropdown d-inline-block d-lg-none ms-2">
                <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-magnify"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-search-dropdown">

                    <form class="p-3">
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect d-flex align-items-center" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <!-- Dynamic user avatar -->
                    <div class="position-relative">
                        <i class="bx bx-user bx-sm fs-2 rounded-circle header-profile-user"></i>
                        <!-- Online status indicator - dynamic based on status -->
                        <span class="user-status bg-<?= session()->get('status') === 'active' ? 'success' : 'secondary' ?>"></span>
                    </div>
                    <?php
                    // Dynamic user name
                    $fullName = session()->get('name') ?? 'User';
                    $nameParts = explode(' ', trim($fullName));

                    // Example: "John Doe" → "John D."
                    if (count($nameParts) > 1) {
                        $shortName = $nameParts[0] . ' ' . strtoupper(substr($nameParts[1], 0, 1)) . '.';
                    } else {
                        $shortName = $nameParts[0];
                    }
                    ?>
                    <span class="d-none d-lg-inline-block ms-2 fw-medium">
                        <?= $shortName ?>
                    </span>
                </button>

                <div class="dropdown-menu dropdown-menu-end p-0">
                    <!-- Dropdown header with dynamic user info -->
                    <div class="dropdown-header bg-light py-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-2">
                                <i class="bx bx-user-circle bx-md text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0"><?= session()->get('name') ?? 'User' ?></h6>
                                <small class="text-muted text-capitalize">
                                    <?= session()->get('user_type') ?? 'User' ?>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Dropdown items -->
                    <div class="px-2 py-2">
                        <a class="dropdown-item rounded d-flex align-items-center py-2"
                            href="<?= site_url('profile'); ?>">
                            <i class="bx bx-user font-size-16 align-middle me-2"></i>
                            <span key="t-profile">Profile</span>
                            <?php if (session()->get('profile_updated') === false): ?>
                                <span class="badge bg-primary ms-auto">New</span>
                            <?php endif; ?>
                        </a>

                        <a class="dropdown-item rounded d-flex align-items-center py-2"
                            href="<?= site_url('change-password'); ?>">
                            <i class="bx bx-wrench font-size-16 align-middle me-2"></i>
                            <span key="t-settings">Change Password</span>
                        </a>

                        <div class="dropdown-divider my-1"></div>

                        <a class="dropdown-item rounded d-flex align-items-center py-2 text-danger"
                            href="<?= site_url('logout'); ?>">
                            <i class="bx bx-power-off font-size-16 align-middle me-2"></i>
                            <span key="t-logout">Logout</span>
                        </a>
                    </div>

                    <!-- Dynamic last login time -->
                    <div class="dropdown-footer bg-light py-2 px-3 text-center">
                        <small class="text-muted">
                            Last login:
                            <?php if (session()->has('last_login')): ?>
                                <?= date('F j, Y, g:i a', strtotime(session()->get('last_login'))) ?>
                            <?php else: ?>
                                Today, <?= date('g:i a') ?>
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
                    <i class="bx bx-cog bx-spin"></i>
                </button>
            </div>

        </div>
    </div>
</header>