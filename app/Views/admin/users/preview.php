<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">

    <!-- Custom CSS -->
    <style>
        .avatar-xl {
            width: 80px;
            height: 80px;
            line-height: 80px;
            font-size: 2.5rem;
        }

        .table-borderless tbody tr th {
            font-weight: 500;
            color: #6c757d;
        }

        .card-header {
            padding: 1rem 1.25rem;
            background-color: #f8f9fa !important;
        }
    </style>
    <div class="container-fluid">
        <!-- Page Title & Breadcrumbs -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?= site_url('users') ?>">User Management</a></li>
                        <li class="breadcrumb-item active"><?= esc($pageTitle) ?></li>
                    </ol>

                    <div class="page-title-right">
                        <a href="<?= site_url('users') ?>" class="btn btn-secondary waves-effect waves-light">
                            <i class="mdi mdi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <!-- User Profile Header -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <?php if (!empty($record['profile_image'])):

                                            $publicPath = FCPATH . 'uploads/users/' . $record['profile_image'];
                                            if (file_exists($publicPath)) {
                                                $imagePath = base_url('uploads/users/' . $record['profile_image']);
                                            } else {
                                                $imagePath = base_url('assets/images/default.jpg'); // Default fallback
                                            }
                                        ?>
                                            <img src="<?= $imagePath ?>"
                                                alt="Profile Image"
                                                class="avatar-xl rounded-circle">
                                        <?php else: ?>
                                            <div class="avatar-xl rounded-circle bg-light text-center d-flex align-items-center justify-content-center">
                                                <span class="display-4 text-muted"><?= strtoupper(substr($record['name'], 0, 1)) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="mb-1"><?= esc($record['name']) ?></h4>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="badge bg-<?= $record['status'] === 'active' ? 'success' : 'danger' ?>">
                                                <?= ucfirst(esc($record['status'])) ?>
                                            </span>
                                            <span class="badge bg-info">
                                                <?= ucfirst(esc($record['user_type'] ?? 'customer')) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="<?= site_url('users/edit/' . $record['user_id']) ?>"
                                            class="btn btn-primary me-2">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Main Details Section -->
                        <div class="row">
                            <!-- Personal Information Card -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0"><i class="fas fa-user me-2"></i> Personal Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th width="40%">Full Name</th>
                                                        <td><?= esc($record['name']) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Username</th>
                                                        <td><?= esc($record['username'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Email Address</th>
                                                        <td><?= esc($record['email'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Phone Number</th>
                                                        <td><?= esc($record['phone'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Alternate Phone</th>
                                                        <td><?= esc($record['alt_mobile_number'] ?? 'N/A') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Gender</th>
                                                        <td><?= ucfirst(esc($record['gender'] ?? 'Not specified')) ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Information Card -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0"><i class="fas fa-cog me-2"></i> Account Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th width="40%">User Type</th>
                                                        <td><?= ucfirst(esc($record['user_type'] ?? 'customer')) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Account Status</th>
                                                        <td>
                                                            <span class="badge bg-<?= strtolower($record['status']) === 'active' ? 'success' : 'danger' ?>">
                                                                <?= ucfirst(esc($record['status'])) ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Registration Date</th>
                                                        <td><?= date('F j, Y, g:i a', strtotime($record['created_at'])) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Last Updated</th>
                                                        <td><?= date('F j, Y, g:i a', strtotime($record['updated_at'])) ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information Card -->
                            <div class="col-md-12 mt-4">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i> Additional Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h6 class="mb-3">Notes</h6>
                                                <div class="p-3 bg-light rounded">
                                                    <?= !empty($record['notes'])
                                                        ? nl2br(esc($record['notes']))
                                                        : '<span class="text-muted">No notes available</span>' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>