<!-- Desktop View Navbar -->
<?php
$cartData = getCartData();
?>
<nav class="navbar navbar-expand-sm navbar-light bg-light sticky-top desktop-view" style="height:100px;">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex justify-content-start flex-grow-1" style="margin-right: -155px;">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active ps-0" href="<?= site_url('/'); ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('products/rent'); ?>">Rent</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('products/sale'); ?>">Sale</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('contact-us'); ?>">Contact Us</a>
                </li>
            </ul>
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mynavbar">
            <div class="me-auto">
                <a class="navbar-brand fw-bold" href="<?= site_url('/'); ?>">
                    <img src="<?= base_url('public/img/citylight-logo.png'); ?>">
                </a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <!-- Cart -->
                <a class="mkd-header-cart d-flex align-items-center cart-link" href="<?= site_url('cart'); ?>">
                    <i class="fa fa-shopping-cart me-1"></i>
                    <span class="mkd-cart-icon-text pe-1">CART</span>
                    <span class="mkd-cart-info">
                        <span class="mkd-cart-info-total" style="font-size: 12px;">
                            (<span class="cart-count"><?= $cartData['cartCount'] ?></span>)
                        </span>
                    </span>
                </a>

                <!-- Login / Logout -->
                <?php if (session()->get('logged_in')): ?>
                    <a class="mkd-header-cart d-flex align-items-center" href="<?= site_url('logout'); ?>">
                        <i class="fa fa-sign-out-alt me-1"></i>
                        <span class="mkd-cart-icon-text">LOGOUT</span>
                    </a>

                    <!-- Profile Icon -->
                    <a class="mkd-header-cart d-flex align-items-center" href="<?= site_url('user/dashboard'); ?>">
                        <i class="fa fa-user-circle me-1" style="font-size: 24px;"></i>
                    </a>

                <?php else: ?>
                    <a class="mkd-header-cart d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#myModal">
                        <i class="fa fa-sign-in-alt me-1"></i>
                        <span class="mkd-cart-icon-text">LOGIN</span>
                    </a>
                <?php endif; ?>

            </div>
        </div>
    </div>
</nav>

<!-- Mobile View Navbar -->
<nav class="navbar navbar-expand-sm navbar-light bg-light sticky-top mobile-view py-3">
    <div class="container d-flex justify-content-between align-items-center ps-0">
        <div class="me-auto ps-3">
            <a class="navbar-brand fw-bold" href="<?= site_url('/'); ?>">
                <img src="<?= base_url('public/img/citylight-logo.png'); ?>" style="height:55px;">
            </a>
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse bg-dark" id="mynavbar">
            <div class="d-flex justify-content-end flex-column">
                <ul class="navbar-nav d-flex flex-column ps-2">
                    <li class="nav-item py-1 mt-3">
                        <a class="nav-link active" href="<?= site_url('/'); ?>">Home</a>
                    </li>
                    <li class="nav-item py-1">
                        <a class="nav-link" href="<?= site_url('products/rent'); ?>">Rent</a>
                    </li>
                    <li class="nav-item py-1">
                        <a class="nav-link" href="<?= site_url('products/sale'); ?>">Sale</a>
                    </li>
                    <li class="nav-item py-1">
                        <a class="nav-link" href="<?= site_url('contact-us'); ?>">Contact Us</a>
                    </li>
                </ul>
            </div>
            <div class="d-flex justify-content-end flex-column ps-2">
                <!-- Cart -->
                <a class="mkd-header-cart py-1 d-flex align-items-center cart-link" href="<?= site_url('cart'); ?>">
                    <i class="fa fa-shopping-cart text-light me-1"></i>
                    <span class="mkd-cart-icon-text pe-1 text-light">CART</span>
                    <span class="mkd-cart-info">
                        <span class="mkd-cart-info-total text-light" style="font-size: 12px;">
                            (<span class="cart-count"><?= $cartData['cartCount'] ?></span>)
                        </span>
                    </span>
                </a>

                <!-- Login / Logout -->
                <?php if (session()->get('logged_in')): ?>
                    <a class="mkd-header-cart py-1 d-flex align-items-center" href="<?= site_url('logout'); ?>">
                        <i class="fa fa-sign-out-alt text-light me-1"></i>
                        <span class="mkd-cart-icon-text text-light">LOGOUT</span>
                    </a>

                    <!-- Profile Icon -->
                    <a class="mkd-header-cart py-1 mb-3 d-flex align-items-center" href="<?= site_url('user/dashboard'); ?>">
                        <i class="fa fa-user-circle text-light me-1" style="font-size: 24px;"></i>
                    </a>

                <?php else: ?>
                    <a class="mkd-header-cart d-flex align-items-center mb-2" href="#" data-bs-toggle="modal" data-bs-target="#myModal">
                        <i class="fa fa-sign-in-alt text-light me-1"></i>
                        <span class="mkd-cart-icon-text text-light" style="font-size: 12px;">LOGIN</span>
                    </a>
                <?php endif; ?>

            </div>
        </div>
    </div>
</nav>