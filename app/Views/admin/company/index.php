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

    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-active {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-inactive {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    .status-suspended {
        background-color: #fef3c7;
        color: #92400e;
    }
</style>
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">CMS</a></li>
                        <li class="breadcrumb-item active">Company List</li>
                    </ol>
                    <?php if (isset($companyStatistics)): ?>
                        <div class="company-stats mt-2">
                            <span class="badge badge-primary mr-2">Total: <?= $companyStatistics->total_companies ?></span>
                            <span class="badge badge-success mr-2">Active: <?= $companyStatistics->active_companies ?></span>
                            <span class="badge badge-warning mr-2">Inactive: <?= $companyStatistics->inactive_companies ?></span>
                            <span class="badge badge-secondary">Suspended: <?= $companyStatistics->suspended_companies ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="page-title-right">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <button id="toggleSearchBtn" type="button" class="btn btn-outline-primary waves-effect waves-light">
                                <i class="mdi mdi-magnify me-1"></i> Advanced Search
                            </button>
                            <a class="btn btn-primary waves-effect waves-light" href="<?php echo site_url('companies/create'); ?>">
                                <i class="mdi mdi-plus me-1"></i> Add Company
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
                                <label for="txtsearch" class="form-label">Search</label>
                                <input class="form-control" name="txtsearch" type="text" value="<?= isset($searchArray["txtsearch"]) ? esc($searchArray["txtsearch"]) : ''; ?>" placeholder="Search by company name, code, or email...">
                            </div>

                            <div class="col-lg-2">
                                <label>Status</label>
                                <select class="form-control" name="status">
                                    <option value="">All Status</option>
                                    <option value="Active" <?= isset($searchArray["status"]) && $searchArray["status"] == 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="Inactive" <?= isset($searchArray["status"]) && $searchArray["status"] == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                    <option value="Suspended" <?= isset($searchArray["status"]) && $searchArray["status"] == 'Suspended' ? 'selected' : '' ?>>Suspended</option>
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <label>Date From</label>
                                <input class="form-control" name="startDate" type="date" value="<?php echo isset($searchArray["startDate"]) ? $searchArray["startDate"] : ''; ?>">
                            </div>

                            <div class="col-lg-2">
                                <label>Date To</label>
                                <input class="form-control" name="endDate" type="date" value="<?php echo isset($searchArray["endDate"]) ? $searchArray["endDate"] : ''; ?>">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary waves-effect waves-light me-2">
                                    <i class="mdi mdi-filter-outline me-1"></i> Apply Filters
                                </button>
                                <a href="<?= site_url('companies'); ?>" class="btn btn-light waves-effect">
                                    <i class="mdi mdi-autorenew me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <?php echo view('admin/_topmessage'); ?>
                    <div class="card-body">
                        <?php if ($pagination["totalRecords"] > 0) { ?>
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered dt-responsive w-100">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Company Name</th>
                                            <th>Company Code</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th data-sortable="false">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $item) {
                                            // Status classes
                                            $statusClass = 'secondary';
                                            $statusText = 'N/A';
                                            if (isset($item->status)) {
                                                $status = strtolower($item->status);
                                                if ($status == 'active') $statusClass = 'active';
                                                elseif ($status == 'inactive') $statusClass = 'inactive';
                                                elseif ($status == 'suspended') $statusClass = 'suspended';
                                                $statusText = ucfirst($item->status);
                                            } ?>
                                            <tr id="company-row-<?= $item->company_id ?>">
                                                <td class="text-center"><?= ++$startLimit ?></td>
                                                <td><?= htmlspecialchars($item->company_name ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($item->company_code ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($item->email ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($item->phone ?? 'N/A') ?></td>
                                                <td><span class="status-badge status-<?= $statusClass ?>"><?= $statusText ?></span></td>
                                                <td><?= !empty($item->created_at) ? date('M d, Y', strtotime($item->created_at)) : 'N/A' ?></td>
                                                <td class="text-center">
                                                    <a href="<?= site_url('companies/preview/' . $item->company_id) ?>" class="btn btn-success" title="View" data-bs-toggle="tooltip">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= site_url('companies/edit/' . $item->company_id) ?>" class="btn btn-primary" title="Edit" data-bs-toggle="tooltip">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger m-1 delete-company"
                                                        data-id="<?= htmlspecialchars($item->company_id) ?>"
                                                        data-name="<?= htmlspecialchars($item->company_name) ?>"
                                                        data-bs-toggle="tooltip"
                                                        title="Delete Company">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if ($pagination['totalRecords']) { ?>
                                <br>
                                <?= view('admin/_paging', ['paginate' => $pagination, 'siteurl' => $action, 'varExtra' => $searchArray]); ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?= view('admin/_noresult'); ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCompanyModal" tabindex="-1" aria-labelledby="deleteCompanyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCompanyModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                    <h5>Are you sure you want to delete <strong id="companyNameToDelete"></strong>?</h5>
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
                <h4 id="successMessage">Company deleted successfully!</h4>
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
                <p class="text-muted">The company record was not deleted.</p>
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

        let currentCompanyId = null;
        let currentCompanyName = null;

        // When delete button is clicked
        $('.delete-company').on('click', function() {
            currentCompanyId = $(this).data('id');
            currentCompanyName = $(this).data('name');

            // Update modal text with company name
            $('#companyNameToDelete').text(currentCompanyName);

            // Show the confirmation modal
            $('#deleteCompanyModal').modal('show');
        });

        // When confirm delete is clicked
        $('#confirmDelete').on('click', function() {
            // Close the confirmation modal
            $('#deleteCompanyModal').modal('hide');

            // Show loading state
            $('#confirmDelete').html('<i class="fas fa-spinner fa-spin"></i> Deleting...').prop('disabled', true);

            // AJAX request to delete company
            $.ajax({
                url: '<?= site_url("companies/delete") ?>',
                type: 'POST',
                data: {
                    company_id: currentCompanyId,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $('#company-row-' + currentCompanyId).remove();
                        $('#successMessage').html(response.message);
                        $('#deleteSuccessModal').modal('show');
                    } else {
                        $('#successMessage').html(response.message);
                        $('#deleteSuccessModal').modal('show');
                    }
                },
                error: function() {
                    $('#successMessage').html(
                        `An error occurred while deleting ${currentCompanyName}.`
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
        $('#deleteCompanyModal .btn-secondary').on('click', function() {
            // Show data safe modal
            $('#dataSafeModal').modal('show');
        });
    });
</script>
<?= $this->endSection() ?>