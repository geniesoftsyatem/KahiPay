<!-- ========== Left Sidebar Start ========== -->
<?php

$uri = service('uri');
$uriSegment1 = $uri->getSegment(1); // "companies"
$uriSegment2 = $uri->getSegment(2); // "edit", "create", etc.

$moduleAccess = [];
$userType = strtolower(session()->get('user_type'));
$emsModules = ['companies', 'employees', 'employee-locations', 'employee-tasks', 'salary', 'request-letters'];

// Grant all access if the user is admin
if ($userType === 'admin') {
    $moduleAccess = [
        'applications',
        'users',
        'feedback',
        'wallet',
        'transaction',
        'notification',
        'settings'
    ];
    // Add all EMS modules for admin
    $moduleAccess = array_merge($moduleAccess, $emsModules);
} elseif ($userType === 'company') {
    // Remove companies module for company users
    $emsModules = array_diff($emsModules, ['companies']);
    $moduleAccess = $emsModules;
}
?>

<style>
    .vertical-menu .metismenu a:hover,
    .vertical-menu .metismenu a.active {
        color: #fff;
        background-color: #292942;
    }
</style>

<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">

                <!-- Dashboard -->
                <li>
                    <a href="<?= site_url('dashboard'); ?>" class="<?= $uriSegment2 == 'dashboard' ? 'active' : '' ?>">
                        <i class="bx bx-home-circle"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Applications -->
                <?php if (in_array('applications', $moduleAccess)): ?>
                    <li class="menu-title">Applications</li>
                <?php endif; ?>

                <!-- Users -->
                <?php if (in_array('users', $moduleAccess)): ?>
                    <li>
                        <a href="<?= site_url('users'); ?>" class="<?= $uriSegment1 == 'users' ? 'active' : '' ?>">
                            <i class="bx bx-user"></i>
                            <span>Users</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Wallet -->
                <?php if (in_array('wallet', $moduleAccess)): ?>
                    <li>
                        <a href="<?= site_url('wallets'); ?>" class="<?= $uriSegment1 == 'wallet' ? 'active' : '' ?>">
                            <i class="bx bx-wallet"></i>
                            <span>Wallet</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Transaction -->
                <?php if (in_array('transaction', $moduleAccess)): ?>
                    <li>
                        <a href="<?= site_url('wallet-transactions'); ?>" class="<?= $uriSegment1 == 'wallet-transactions' ? 'active' : '' ?>">
                            <i class="bx bx-transfer"></i>
                            <span>Transaction</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Notification -->
                <?php if (in_array('notification', $moduleAccess)): ?>
                    <li>
                        <a href="<?= site_url('notifications'); ?>" class="<?= $uriSegment1 == 'notifications' ? 'active' : '' ?>">
                            <i class="bx bx-bell"></i>
                            <span>Notification</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Feedback -->
                <?php if (in_array('feedback', $moduleAccess)): ?>
                    <li>
                        <a href="<?= site_url('feedback'); ?>" class="<?= $uriSegment1 == 'feedback' ? 'active' : '' ?>">
                            <i class="bx bx-comment"></i>
                            <span>Customer Feedback</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Employees Management -->
                <?php
                $isEMSActive = in_array($uriSegment1, $emsModules);
                ?>
                <li class="menu-title">Employees Management</li>
                <li class="<?= $isEMSActive ? 'mm-active' : '' ?>">
                    <a href="javascript:void(0);" class="has-arrow <?= $isEMSActive ? 'active' : '' ?>">
                        <i class="bx bx-group"></i>
                        <span>EMS</span>
                    </a>
                    <ul class="sub-menu <?= $isEMSActive ? 'mm-show' : '' ?>" aria-expanded="<?= $isEMSActive ? 'true' : 'false' ?>">

                        <?php if (in_array('companies', $moduleAccess) && $userType !== 'company'): ?>
                            <li>
                                <a href="<?= site_url('companies'); ?>" class="<?= $uriSegment1 == 'companies' ? 'active' : '' ?>">
                                    <i class="bx bx-buildings"></i> Companies
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (in_array('employees', $moduleAccess)): ?>
                            <li>
                                <a href="<?= site_url('employees'); ?>" class="<?= $uriSegment1 == 'employees' ? 'active' : '' ?>">
                                    <i class="bx bx-id-card"></i> Employees
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (in_array('employee-locations', $moduleAccess)): ?>
                            <li>
                                <a href="<?= site_url('employee-locations'); ?>" class="<?= $uriSegment1 == 'employee-locations' ? 'active' : '' ?>">
                                    <i class="bx bx-map-pin"></i> Track Location
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (in_array('employee-tasks', $moduleAccess)): ?>
                            <li>
                                <a href="<?= site_url('employee-tasks'); ?>" class="<?= $uriSegment1 == 'employee-tasks' ? 'active' : '' ?>">
                                    <i class="bx bx-task"></i> Assign Task
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (in_array('salary', $moduleAccess)): ?>
                            <li>
                                <a href="<?= site_url('salary'); ?>" class="<?= $uriSegment1 == 'salary' ? 'active' : '' ?>">
                                    <i class="bx bx-money"></i> Salaries Info
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (in_array('request-letters', $moduleAccess)): ?>
                            <li>
                                <a href="<?= site_url('request-letters'); ?>" class="<?= $uriSegment1 == 'request-letters' ? 'active' : '' ?>">
                                    <i class="bx bx-envelope"></i> Request Letter
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>

                <!-- Settings -->
                <?php if (in_array('settings', $moduleAccess)): ?>
                    <li class="menu-title">Settings</li>
                    <?php $isSettingsActive = $uriSegment1 === 'settings'; ?>
                    <li class="<?= $isSettingsActive ? 'mm-active' : '' ?>">
                        <a href="javascript:void(0);" class="has-arrow <?= $isSettingsActive ? 'active' : '' ?>">
                            <i class="bx bx-cog"></i>
                            <span>Settings</span>
                        </a>
                        <ul class="sub-menu <?= $isSettingsActive ? 'mm-show' : '' ?>" aria-expanded="<?= $isSettingsActive ? 'true' : 'false' ?>">
                            <li>
                                <a href="<?= site_url('settings/company-info'); ?>" class="<?= $uriSegment2 == 'company-info' ? 'active' : '' ?>">
                                    <i class="bx bx-building"></i> Company Info
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>