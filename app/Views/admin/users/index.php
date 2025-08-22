<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<style>
    .search-form {
        display: none;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .search-form.show {
        display: block;
        animation: fadeIn 0.3s ease;
    }
</style>
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">User Management</a></li>
                        <li class="breadcrumb-item active">Users List</li>
                    </ol>
                    <div class="page-title-right">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <button id="toggleSearchBtn" type="button" class="btn btn-outline-primary waves-effect waves-light">
                                <i class="mdi mdi-magnify me-1"></i> Advanced Search
                            </button>

                            <a href="<?= site_url("users/add") ?>" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> Add User
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="search-form <?= isset($txtsearch) ? 'show' : '' ?>">
            <div class="card customer-card">
                <div class="card-body">
                    <form action="">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="txtsearch" class="form-label">Search Users</label>
                                <div class="search-input-group">
                                    <input class="form-control" name="txtsearch" type="text" value="<?= isset($searchArray["txtsearch"]) ? esc($searchArray["txtsearch"]) : ''; ?>" placeholder="Search by name, email or phone...">
                                </div>
                            </div>

                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary waves-effect waves-light me-2">
                                    <i class="mdi mdi-filter-outline me-1"></i> Apply Filters
                                </button>
                                <a href="<?= site_url('users'); ?>" class="btn btn-light waves-effect">
                                    <i class="mdi mdi-autorenew me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <?= view('admin/_topmessage'); ?>
                    <div class="card-body">
                        <?php if ($pagination["totalRecords"] > 0) { ?>
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered table-striped dt-responsive w-100">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Username</th>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Email</th>
                                            <th class="text-center">Phone</th>
                                            <th class="text-center">Gender</th>
                                            <th class="text-center">User Type</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Created At</th>
                                            <th class="text-center" data-sortable="false">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $item): ?>
                                            <tr>
                                                <td><?= ++$startLimit; ?></td>
                                                <td><?= esc($item->username); ?></td>
                                                <td><?= esc($item->name); ?></td>
                                                <td><?= esc($item->email); ?></td>
                                                <td><?= esc($item->phone); ?></td>
                                                <td><?= esc(ucwords($item->gender ?? 'N/A')); ?></td>
                                                <td><?= esc(ucfirst($item->user_type)); ?></td>
                                                <td>
                                                    <span class="badge bg-<?= strtolower($item->status) === 'active' ? 'success' : (strtolower($item->status) === 'inactive' ? 'secondary' : 'danger') ?>">
                                                        <?= ucfirst($item->status) ?>
                                                    </span>
                                                </td>
                                                <td><?= esc(date('d M Y, h:i A', strtotime($item->created_at))); ?></td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <a href="<?= site_url("users/edit/" . $item->user_id) ?>" class="btn btn-success m-1" data-bs-toggle="tooltip" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="<?= site_url("users/preview/" . $item->user_id) ?>" class="btn btn-warning m-1" data-bs-toggle="tooltip" title="Preview">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-danger m-1 delete-user" data-id="<?= $item->user_id ?>" data-name="<?= esc($item->name) ?>" data-bs-toggle="tooltip" title="Delete User">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if ($pagination['totalRecords']) { ?>
                                <br>
                                <?= view('admin/_paging', array('paginate' => $pagination, 'siteurl' => $action, 'varExtra' => $searchArray)); ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?= view('admin/_noresult'); ?>
                        <?php } ?>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- container-fluid -->
</div> <!-- End Page-content -->

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                <h5>Are you sure you want to delete <strong id="userNameToDelete"></strong>?</h5>
                <p>This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteUser">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="deleteSuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="avatar-lg mx-auto mb-4">
                    <div class="avatar-title bg-light text-success display-4 rounded-circle">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
                <h4 id="successMessage">User deleted successfully!</h4>
                <p class="text-muted">The record has been permanently removed.</p>
                <button type="button" class="btn btn-success mt-3" data-bs-dismiss="modal" onclick="setTimeout(function(){ location.reload(); }, 300);">
                    <i class="fas fa-check me-1"></i> Continue
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#datatable').DataTable({
            dom: 'Bfrtip',
            searching: false,
            paging: false,
            info: false,
        });
        // Initialize Bootstrap tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Toggle search form with animation
        $('#toggleSearchBtn').click(function() {
            $('.search-form').toggleClass('show');
            localStorage.setItem('isSearchFormVisible', $('.search-form').hasClass('show'));
            localStorage.setItem('searchFormVisible', new Date().getTime());

            // Change button icon and text
            if ($('.search-form').hasClass('show')) {
                $(this).html('<i class="mdi mdi-close me-1"></i> Close Search');
                $(this).removeClass('btn-outline-primary').addClass('btn-primary');
            } else {
                $(this).html('<i class="mdi mdi-magnify me-1"></i> Advanced Search');
                $(this).removeClass('btn-primary').addClass('btn-outline-primary');
            }
        });

        // Check if the search form should be shown
        const currentTime = new Date().getTime();
        const visibilityDuration = 10 * 60 * 1000; // 10 minutes in milliseconds
        const isFormVisible = localStorage.getItem('isSearchFormVisible');
        const formVisibleTimestamp = localStorage.getItem('searchFormVisible');

        if (isFormVisible === 'true' && formVisibleTimestamp && (currentTime - formVisibleTimestamp < visibilityDuration)) {
            $('.search-form').addClass('show');
            $('#toggleSearchBtn').html('<i class="mdi mdi-close me-1"></i> Close Search').removeClass('btn-outline-primary').addClass('btn-primary');
        }

        let currentUserId = null;
        let currentUserName = null;

        $('.delete-user').on('click', function() {
            currentUserId = $(this).data('id');
            currentUserName = $(this).data('name');
            $('#userNameToDelete').text(currentUserName);
            $('#deleteUserModal').modal('show');
        });

        $('#confirmDeleteUser').on('click', function() {
            $('#deleteUserModal').modal('hide');
            $('#confirmDeleteUser').html('<i class="fas fa-spinner fa-spin"></i> Deleting...').prop('disabled', true);

            $.ajax({
                url: '<?= site_url("users/delete") ?>',
                type: 'POST',
                data: {
                    user_id: currentUserId
                },
                success: function(response) {
                    if (response.success) {
                        $('#user-row-' + currentUserId).remove();
                        $('#successMessage').html(response.message);
                    } else {
                        $('#successMessage').html(response.message);
                    }
                    $('#deleteSuccessModal').modal('show');
                },
                error: function() {
                    $('#successMessage').html(`An error occurred while deleting ${currentUserName}.`);
                    $('#deleteSuccessModal').modal('show');
                },
                complete: function() {
                    $('#confirmDeleteUser').html('Delete').prop('disabled', false);
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>