<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">
    <div class="container-fluid">
        <!-- Start Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Profile Management</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">My Profile</li>
                    </ol>
                </div>
            </div>
        </div>
        <!-- End Page Title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                            <h2 class="mb-0">My Profile</h2>
                            <div>
                                <a href="<?= base_url('edit-profile') ?>" class="btn btn-primary btn-sm">
                                    <i class="ri-edit-line align-bottom me-1"></i> Edit Profile
                                </a>
                                <a href="<?= base_url('change-password') ?>" class="btn btn-outline-secondary btn-sm ms-2">
                                    <i class="ri-lock-line align-bottom me-1"></i> Change Password
                                </a>
                            </div>
                        </div>

                        <?= view('admin/_topmessage') ?>

                        <div class="row">
                            <div class="col-md-4 col-xl-3">
                                <div class="card bg-light overflow-hidden">
                                    <div class="card-body p-4 text-center">
                                        <div class="position-relative d-inline-block mb-4">
                                            <img src="<?= !empty($profiledata['profile_image']) ? base_url('uploads/users/' . esc($profiledata['profile_image'])) : base_url('assets/images/default.jpg'); ?>"
                                                class="rounded-circle" style="width: 120px; height: 120px;" alt="Profile Image">
                                            <div class="position-absolute bottom-0 end-0">
                                                <button class="btn btn-sm btn-info rounded-circle p-0 avatar-xs" data-bs-toggle="modal" data-bs-target="#avatarModal">
                                                    <i class="ri-camera-line"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <h5 class="mb-1"><?= esc($profiledata['name']) ?></h5>
                                        <p class="text-muted mb-3"><?= esc($profiledata['role'] ?? 'Administrator') ?></p>

                                        <div class="mt-4">
                                            <h6 class="text-start">Profile Completion</h6>
                                            <div class="progress progress-sm mb-3">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 85%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8 col-xl-9">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">Personal Information</h5>
                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th width="30%"><i class="ri-user-line align-middle me-2 text-primary"></i> Name</th>
                                                        <td><?= esc($profiledata['name']) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th><i class="ri-phone-line align-middle me-2 text-primary"></i> Mobile</th>
                                                        <td><?= esc($profiledata['phone']) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th><i class="ri-mail-line align-middle me-2 text-primary"></i> Email</th>
                                                        <td><?= esc($profiledata['email']) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th><i class="ri-calendar-line align-middle me-2 text-primary"></i> Member Since</th>
                                                        <td><?= date('F j, Y', strtotime($profiledata['created_at'] ?? 'now')) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th><i class="ri-shield-user-line align-middle me-2 text-primary"></i> Status</th>
                                                        <td>
                                                            <span class="badge bg-success bg-opacity-10 text-success">
                                                                <i class="ri-checkbox-circle-line align-middle me-1"></i> Active
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th><i class="ri-map-pin-line align-middle me-2 text-primary"></i> Address</th>
                                                        <td><?= esc($profiledata['address'] ?? 'Not specified') ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="mt-4 pt-3 border-top">
                                            <h5 class="card-title mb-3">Additional Information</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="d-flex mb-4">
                                                        <div class="flex-shrink-0">
                                                            <i class="ri-time-line text-primary fs-4"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="fs-14 mb-1">Last Login</h6>
                                                            <p class="text-muted mb-0"><?php if (session()->has('last_login')): ?>
                                                                    <?= date('F j, Y, g:i a', strtotime(session()->get('last_login'))) ?>
                                                                <?php else: ?>
                                                                    Today, <?= date('g:i a') ?>
                                                                <?php endif; ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex mb-4">
                                                        <div class="flex-shrink-0">
                                                            <i class="ri-history-line text-primary fs-4"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="fs-14 mb-1">Last Updated</h6>
                                                            <p class="text-muted mb-0"><?= date('M j, Y g:i A', strtotime($profiledata['updated_at'] ?? 'now')) ?></p>
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
    </div>
</div>

<!-- Avatar Update Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="avatarModalLabel">Update Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="avatarForm" action="<?= base_url('profile/update-avatar') ?>" method="post" enctype="multipart/form-data">
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <img src="<?= !empty($profiledata['avatar']) ? esc($profiledata['avatar']) : base_url('assets/images/default.jpg') ?>"
                                class="rounded-circle" style="width: 120px; height: 120px;" alt="Profile Image">
                            <div class="position-absolute bottom-0 end-0">
                                <div class="position-absolute bottom-0 end-0">
                                    <label for="avatarInput" class="btn btn-sm btn-info rounded-circle p-0 avatar-xs cursor-pointer">
                                        <i class="ri-camera-line"></i>
                                        <input type="file" id="avatarInput" name="avatar" class="d-none" accept="image/*">
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="avatarInput" class="form-label">Choose new profile picture</label>
                            <input class="form-control" type="file" id="avatarInput" name="avatar" accept="image/*">
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Avatar preview functionality
        const avatarInput = document.getElementById('avatarInput');
        const avatarPreview = document.getElementById('avatarPreview');

        if (avatarInput && avatarPreview) {
            avatarInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        avatarPreview.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>

<?= $this->endSection() ?>