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

        <!-- Page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">EMS</a></li>
                        <li class="breadcrumb-item active">Request Letters</li>
                    </ol>
                    <div class="page-title-right">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <button id="toggleSearchBtn" type="button" class="btn btn-outline-primary waves-effect waves-light">
                                <i class="mdi mdi-magnify me-1"></i> Advanced Search
                            </button>
                            <a class="btn btn-primary waves-effect waves-light" href="<?= site_url('request-letters/create'); ?>">
                                <i class="mdi mdi-plus me-1"></i> Add Request Letter
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="search-form <?= isset($txtsearch) ? 'show' : '' ?>">
            <div class="card customer-card">
                <div class="card-body">
                    <form action="">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="txtsearch" class="form-label">Search Employees</label>
                                <input class="form-control" name="txtsearch" type="text" value="<?= isset($searchArray["txtsearch"]) ? esc($searchArray["txtsearch"]) : ''; ?>" placeholder="Search by name, email, or phone...">
                            </div>
                            <div class="col-md-3">
                                <label for="manager" class="form-label">Reporting Manager</label>
                                <select class="form-select" name="manager" id="manager">
                                    <option value="">Select Reporting Manager</option>
                                    <?php foreach ($managers as $manager): ?>
                                        <option value="<?= esc($manager['employee_id']) ?>"
                                            <?= isset($searchArray['manager']) && $searchArray['manager'] == $manager['employee_id'] ? 'selected' : '' ?>>
                                            <?= esc($manager['first_name'] . ' ' . $manager['last_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php if (session('user_type') === 'admin'): ?>
                                <div class="col-md-3">
                                    <label for="company_id" class="form-label">Company</label>
                                    <select class="form-select" id="company_id" name="company_id">
                                        <option value="">Select Company</option>
                                        <?php foreach ($companies as $company): ?>
                                            <option value="<?= esc($company['company_id']) ?>"
                                                <?= (isset($searchArray['company_id']) && $searchArray['company_id'] == $company['company_id']) ? 'selected' : '' ?>>
                                                <?= esc($company['company_name']) ?> (<?= esc($company['company_code']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select a company.</div>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary waves-effect waves-light me-2">
                                    <i class="mdi mdi-filter-outline me-1"></i> Apply Filters
                                </button>
                                <a href="<?= site_url('request-letters'); ?>" class="btn btn-light waves-effect">
                                    <i class="mdi mdi-autorenew me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <?= view('admin/_topmessage') ?>
                    <div class="card-body">
                        <?php if ($pagination["totalRecords"] > 0) { ?>
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered dt-responsive w-100">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Company</th>
                                            <th>Title</th>
                                            <th>Employee</th>
                                            <th>Reporting Manager</th>
                                            <th>Description</th>
                                            <th>Images</th>
                                            <th>Created At</th>
                                            <th data-sortable="false">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $item): ?>
                                            <tr>
                                                <td class="text-center"><?= ++$startLimit ?></td>
                                                <td><?= $item->company_name ?? 'N/A'; ?></td>
                                                <td><?= esc($item->title) ?></td>
                                                <td><?= esc($item->employee_name) ?></td>
                                                <td><?= esc($item->reporting_manager_name) ?></td>
                                                <td><?= esc($item->description) ?></td>
                                                <td>
                                                    <?php
                                                    $images = explode(',', $item->images ?? '');
                                                    foreach ($images as $img):
                                                        if (!empty($img)): ?>
                                                            <img src="<?= base_url($img) ?>" width="40" class="img-thumbnail me-1 mb-1" />
                                                    <?php endif;
                                                    endforeach;
                                                    ?>
                                                </td>
                                                <td><?= date('M d, Y', strtotime($item->created_at)) ?></td>
                                                <td class="text-center">
                                                    <a href="<?= site_url('request-letters/preview?id=' . $item->request_id) ?>" class="btn btn-success" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= site_url('request-letters/edit?id=' . $item->request_id) ?>" class="btn btn-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <button type="button" class="btn btn-danger m-1 delete-request-letter"
                                                        data-id="<?= htmlspecialchars($item->request_id) ?>"
                                                        data-title="<?= htmlspecialchars($item->title) ?>"
                                                        data-bs-toggle="tooltip"
                                                        title="Delete Request Letter">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?= view('admin/_paging', ['paginate' => $pagination, 'siteurl' => $action, 'varExtra' => $searchArray]) ?>
                        <?php } else { ?>
                            <?= view('admin/_noresult') ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteRequestModal" tabindex="-1" aria-labelledby="deleteRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteRequestModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                    <h5>Are you sure you want to delete this request letter?</h5>
                    <p>This action cannot be undone and all related data will be permanently removed.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="deleteSuccessModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle fa-5x text-success"></i>
                </div>
                <h4 id="successMessage">Request Letter deleted successfully!</h4>
                <button type="button" class="btn btn-primary mt-3" data-dismiss="modal" onclick="setTimeout(function(){ location.reload(); }, 300);">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Safe Data Modal -->
<div class="modal fade" id="dataSafeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="fas fa-shield-alt fa-5x text-primary"></i>
                </div>
                <h4>Your data is safe!</h4>
                <p class="text-muted">The request letter record was not deleted.</p>
                <button type="button" class="btn btn-primary mt-3" data-dismiss="modal" onclick="setTimeout(function(){ location.reload(); }, 300);">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {

        $('#datatable').DataTable({
            dom: 'Bfrtip',
            searching: false,
            paging: false,
            info: false,
        });

        $('[data-bs-toggle="tooltip"]').tooltip();

        $('#toggleSearchBtn').click(function() {
            $('.search-form').toggleClass('show');
            localStorage.setItem('isSearchFormVisible', $('.search-form').hasClass('show'));
            localStorage.setItem('searchFormVisible', new Date().getTime());

            if ($('.search-form').hasClass('show')) {
                $(this).html('<i class="mdi mdi-close me-1"></i> Close Search').removeClass('btn-outline-primary').addClass('btn-primary');
            } else {
                $(this).html('<i class="mdi mdi-magnify me-1"></i> Advanced Search').removeClass('btn-primary').addClass('btn-outline-primary');
            }
        });

        const currentTime = new Date().getTime();
        const visibilityDuration = 10 * 60 * 1000;
        const isFormVisible = localStorage.getItem('isSearchFormVisible');
        const formVisibleTimestamp = localStorage.getItem('searchFormVisible');

        if (isFormVisible === 'true' && formVisibleTimestamp && (currentTime - formVisibleTimestamp < visibilityDuration)) {
            $('.search-form').addClass('show');
            $('#toggleSearchBtn').html('<i class="mdi mdi-close me-1"></i> Close Search').removeClass('btn-outline-primary').addClass('btn-primary');
        }

        let currentRequestLetterId = null;
        let currentRequestLetter = null;

        // When delete button is clicked
        $('.delete-request-letter').on('click', function() {
            currentRequestLetterId = $(this).data('id');
            currentRequestLetter = $(this).data('title');

            // Update modal text with employee name
            $('#deleteRequestModal .modal-body h5').html(
                `Are you sure you want to delete <strong>${currentRequestLetter}</strong>?`
            );

            // Show the confirmation modal
            $('#deleteRequestModal').modal('show');
        });

        // When confirm delete is clicked
        $('#confirmDelete').on('click', function() {
            // Close the confirmation modal
            $('#deleteRequestModal').modal('hide');

            // Show loading state
            $('#confirmDelete').html('<i class="fas fa-spinner fa-spin"></i> Deleting...').prop('disabled', true);

            // AJAX request to delete employee
            $.ajax({
                url: '<?= site_url("request-letters/delete") ?>',
                type: 'POST',
                data: {
                    request_id: currentRequestLetterId,
                },
                success: function(response) {
                    if (response.success) {
                        $('#employee-row-' + currentRequestLetterId).remove();
                        $('#successMessage').html(response.message);
                        $('#deleteSuccessModal').modal('show');
                    } else {
                        $('#successMessage').html(response.message);
                        $('#deleteSuccessModal').modal('show');
                    }
                },
                error: function() {
                    $('#successMessage').html(
                        `An error occurred while deleting ${currentEmployeeName}.`
                    );
                    $('#deleteSuccessModal').modal('show');
                },
                complete: function() {
                    // Reset button state
                    $('#confirmDelete').html('Delete').prop('disabled', false);
                }
            });
        });

        // When cancel is clicked in confirmation modal
        $('#deleteRequestModal .btn-secondary').on('click', function() {
            // Show data safe modal
            $('#dataSafeModal').modal('show');
        });
    });
</script>

<?= $this->endSection() ?>