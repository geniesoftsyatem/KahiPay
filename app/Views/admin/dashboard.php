<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Dashboard Overview</h4>
                    <div class="page-title-right">
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="dashboardMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-cog-outline"></i> Settings
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dashboardMenuButton">
                                <li><a class="dropdown-item" href="<?= site_url('dashboard'); ?>">
                                        <i class="mdi mdi-refresh me-2"></i>Refresh</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- Stats Cards -->
        <?php if ($userType == "company") { ?>
            <div class="row">
                <!-- Employees Card -->
                <div class="col-xl-3 col-md-6">
                    <a href="<?= site_url('employees') ?>" class="card-link">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="fw-medium text-muted mb-0">Employees</p>
                                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="<?= $total_employees ?>">0</span></h2>
                                        <p class="mb-0 <?= ($wallet_growth >= 0) ? 'text-success' : 'text-danger' ?>">
                                            <span class="badge bg-<?= ($wallet_growth >= 0) ? 'success' : 'danger' ?>-subtle text-<?= ($wallet_growth >= 0) ? 'success' : 'danger' ?>">
                                                <i class="ri-arrow-right-<?= ($wallet_growth >= 0) ? 'up' : 'down' ?>-line align-middle"></i> <?= abs($wallet_growth) ?>%
                                            </span> vs last month
                                        </p>
                                    </div>
                                    <div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-info-subtle rounded-circle fs-2">
                                                <i class="bx bx-group text-info"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <div class="text-end">
                                    <span class="text-muted">View Details <i class="ri-arrow-right-line align-middle"></i></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Request Letters Card -->
                <div class="col-xl-3 col-md-6">
                    <a href="<?= site_url('request-letters') ?>" class="card-link">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="fw-medium text-muted mb-0">Request Letters</p>
                                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="<?= $request_letters ?>">0</span></h2>
                                        <p class="mb-0 <?= ($wallet_growth >= 0) ? 'text-success' : 'text-danger' ?>">
                                            <span class="badge bg-<?= ($wallet_growth >= 0) ? 'success' : 'danger' ?>-subtle text-<?= ($wallet_growth >= 0) ? 'success' : 'danger' ?>">
                                                <i class="ri-arrow-right-<?= ($wallet_growth >= 0) ? 'up' : 'down' ?>-line align-middle"></i> <?= abs($wallet_growth) ?>%
                                            </span> vs last month
                                        </p>
                                    </div>
                                    <div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-warning-subtle rounded-circle fs-2">
                                                <i class="bx bx-envelope text-warning"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <div class="text-end">
                                    <span class="text-muted">View Details <i class="ri-arrow-right-line align-middle"></i></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Assigned Tasks Card -->
                <div class="col-xl-3 col-md-6">
                    <a href="<?= site_url('employee-tasks') ?>" class="card-link">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="fw-medium text-muted mb-0">Assigned Tasks</p>
                                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="<?= $total_assigned_tasks ?>">0</span></h2>
                                        <p class="mb-0 <?= ($wallet_growth >= 0) ? 'text-success' : 'text-danger' ?>">
                                            <span class="badge bg-<?= ($wallet_growth >= 0) ? 'success' : 'danger' ?>-subtle text-<?= ($wallet_growth >= 0) ? 'success' : 'danger' ?>">
                                                <i class="ri-arrow-right-<?= ($wallet_growth >= 0) ? 'up' : 'down' ?>-line align-middle"></i> <?= abs($wallet_growth) ?>%
                                            </span> vs last month
                                        </p>
                                    </div>
                                    <div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-secondary-subtle rounded-circle fs-2">
                                                <i class="bx bx-task text-secondary"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <div class="text-end">
                                    <span class="text-muted">View Details <i class="ri-arrow-right-line align-middle"></i></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- Wallets Card -->
                <div class="col-xl-3 col-md-6">
                    <a href="#" class="card-link">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="fw-medium text-muted mb-0">Wallets</p>
                                        <h2 class="mt-4 ff-secondary fw-semibold">₹<span class="counter-value" data-target="<?= $wallet_balance ?>">0</span></h2>
                                        <p class="mb-0 <?= ($wallet_growth >= 0) ? 'text-success' : 'text-danger' ?>">
                                            <span class="badge bg-<?= ($wallet_growth >= 0) ? 'success' : 'danger' ?>-subtle text-<?= ($wallet_growth >= 0) ? 'success' : 'danger' ?>">
                                                <i class="ri-arrow-right-<?= ($wallet_growth >= 0) ? 'up' : 'down' ?>-line align-middle"></i> <?= abs($wallet_growth) ?>%
                                            </span> vs last month
                                        </p>
                                    </div>
                                    <div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                                <i class="bx bx-wallet text-success"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <div class="text-end">
                                    <span class="text-muted">View Details <i class="ri-arrow-right-line align-middle"></i></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        <?php } else { ?>
            <div class="row">
                <!-- Users Card -->
                <div class="col-xl-3 col-md-6">
                    <a href="<?= site_url('users') ?>" class="card-link">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="fw-medium text-muted mb-0">Total Users</p>
                                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="<?= $total_users ?>">0</span></h2>
                                        <p class="mb-0 <?= ($user_growth >= 0) ? 'text-success' : 'text-danger' ?>">
                                            <span class="badge bg-<?= ($user_growth >= 0) ? 'success' : 'danger' ?>-subtle text-<?= ($user_growth >= 0) ? 'success' : 'danger' ?>">
                                                <i class="ri-arrow-right-<?= ($user_growth >= 0) ? 'up' : 'down' ?>-line align-middle"></i> <?= abs($user_growth) ?>%
                                            </span> vs last month
                                        </p>
                                    </div>
                                    <div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-primary-subtle rounded-circle fs-2">
                                                <i class="bx bx-user text-primary"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <div class="text-end">
                                    <span class="text-muted">View Details <i class="ri-arrow-right-line align-middle"></i></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Companies Card -->
                <div class="col-xl-3 col-md-6">
                    <a href="<?= site_url('companies') ?>" class="card-link">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="fw-medium text-muted mb-0">Companies</p>
                                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="<?= $total_companies ?>">0</span></h2>
                                        <p class="mb-0 <?= ($company_growth >= 0) ? 'text-success' : 'text-danger' ?>">
                                            <span class="badge bg-<?= ($company_growth >= 0) ? 'success' : 'danger' ?>-subtle text-<?= ($company_growth >= 0) ? 'success' : 'danger' ?>">
                                                <i class="ri-arrow-right-<?= ($company_growth >= 0) ? 'up' : 'down' ?>-line align-middle"></i> <?= abs($company_growth) ?>%
                                            </span> vs last month
                                        </p>
                                    </div>
                                    <div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-info-subtle rounded-circle fs-2">
                                                <i class="bx bx-building text-info"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <div class="text-end">
                                    <span class="text-muted">View Details <i class="ri-arrow-right-line align-middle"></i></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Employees Card -->
                <div class="col-xl-3 col-md-6">
                    <a href="<?= site_url('employees') ?>" class="card-link">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="fw-medium text-muted mb-0">Employees</p>
                                        <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value" data-target="<?= $total_employees ?>">0</span></h2>
                                        <p class="mb-0 <?= ($employee_growth >= 0) ? 'text-success' : 'text-danger' ?>">
                                            <span class="badge bg-<?= ($employee_growth >= 0) ? 'success' : 'danger' ?>-subtle text-<?= ($employee_growth >= 0) ? 'success' : 'danger' ?>">
                                                <i class="ri-arrow-right-<?= ($employee_growth >= 0) ? 'up' : 'down' ?>-line align-middle"></i> <?= abs($employee_growth) ?>%
                                            </span> vs last month
                                        </p>
                                    </div>
                                    <div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-info-subtle rounded-circle fs-2">
                                                <i class="bx bx-group text-info"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <div class="text-end">
                                    <span class="text-muted">View Details <i class="ri-arrow-right-line align-middle"></i></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Wallets Card -->
                <div class="col-xl-3 col-md-6">
                    <a href="#" class="card-link">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="fw-medium text-muted mb-0">Wallets</p>
                                        <h2 class="mt-4 ff-secondary fw-semibold">₹<span class="counter-value" data-target="<?= $wallet_balance ?>">0</span></h2>
                                        <p class="mb-0 <?= ($wallet_growth >= 0) ? 'text-success' : 'text-danger' ?>">
                                            <span class="badge bg-<?= ($wallet_growth >= 0) ? 'success' : 'danger' ?>-subtle text-<?= ($wallet_growth >= 0) ? 'success' : 'danger' ?>">
                                                <i class="ri-arrow-right-<?= ($wallet_growth >= 0) ? 'up' : 'down' ?>-line align-middle"></i> <?= abs($wallet_growth) ?>%
                                            </span> vs last month
                                        </p>
                                    </div>
                                    <div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-success-subtle rounded-circle fs-2">
                                                <i class="bx bx-wallet text-success"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <div class="text-end">
                                    <span class="text-muted">View Details <i class="ri-arrow-right-line align-middle"></i></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <!-- End Stats Cards -->

        <?php   } ?>

        <!-- Recent Users -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Recent Users</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php if ($userType == "company") { ?>
                                <table class="table table-nowrap align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Employee Code</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Gender</th>
                                            <th>Designation</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recent_users)): ?>
                                            <?php
                                            $serial = 1;
                                            foreach ($recent_users as $emp): ?>
                                                <tr>
                                                    <td><?= $serial++ ?></td>
                                                    <td><?= esc($emp['employee_code']) ?></td>
                                                    <td><?= esc($emp['first_name'] . ' ' . $emp['last_name']) ?></td>
                                                    <td><?= esc($emp['email']) ?></td>
                                                    <td><?= esc($emp['phone']) ?></td>
                                                    <td><?= esc($emp['gender']) ?></td>
                                                    <td><?= esc($emp['designation']) ?></td>
                                                    <td>
                                                        <?php if ($emp['status'] === 'Active'): ?>
                                                            <span class="badge bg-success-subtle text-success">Active</span>
                                                        <?php elseif ($emp['status'] === 'Inactive'): ?>
                                                            <span class="badge bg-warning-subtle text-warning">Inactive</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger-subtle text-danger">Deleted</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= date('d M, Y', strtotime($emp['created_at'])) ?></td>
                                                    <td>
                                                        <a href="<?= site_url("employees/preview/" . $emp['employee_id']) ?>" class="btn btn-sm btn-light">View</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="10" class="text-center">No recent employees found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            <?php } else { ?>
                                <!-- You can use the same table for other userTypes if required -->
                                <table class="table table-nowrap align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Username</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Gender</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recent_users)): ?>
                                            <?php
                                            $serial = 1;
                                            foreach ($recent_users as $user): ?>
                                                <tr>
                                                    <td><?= $serial++ ?></td>
                                                    <td><?= esc($user['username']) ?></td>
                                                    <td><?= esc($user['name']) ?></td>
                                                    <td><?= esc($user['email']) ?></td>
                                                    <td><?= esc($user['phone']) ?></td>
                                                    <td><?= esc($user['gender']) ?></td>
                                                    <td>
                                                        <?php if ($user['status'] === 'Active'): ?>
                                                            <span class="badge bg-success-subtle text-success">Active</span>
                                                        <?php elseif ($user['status'] === 'Inactive'): ?>
                                                            <span class="badge bg-warning-subtle text-warning">Inactive</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger-subtle text-danger">Deleted</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= date('d M, Y', strtotime($user['created_at'])) ?></td>
                                                    <td>
                                                        <a href="<?= site_url("users/preview/" . $user['user_id']) ?>" class="btn btn-sm btn-light">View</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="10" class="text-center">No recent users found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- container-fluid -->
</div>
<!-- End Page-content -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const counters = document.querySelectorAll('.counter-value');
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            let count = 0;
            const step = Math.ceil(target / 50); // Smooth animation

            const updateCounter = () => {
                if (count < target) {
                    count += step;
                    counter.innerText = count > target ? target : count;
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.innerText = target;
                }
            };

            updateCounter();
        });
    });
</script>
<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <script>
                    document.write(new Date().getFullYear())
                </script> © Kahipay. All rights reserved.
            </div>
            <div class="col-sm-6 text-sm-end d-none d-sm-block">
                Powered by Kahipay
            </div>
        </div>
    </div>
</footer>
<?= $this->endSection() ?>