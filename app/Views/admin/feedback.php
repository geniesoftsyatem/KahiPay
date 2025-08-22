<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<style>
    .search-form {
        display: none;
        /* Initially hide the search form */
    }
</style>
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Feedback</a></li>
                        <li class="breadcrumb-item active">Feedback List</li>
                    </ol>
                    <div class="page-title-right">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <button id="toggleSearchBtn" type="button" class="btn btn-outline-primary waves-effect waves-light">
                                <i class="mdi mdi-magnify me-1"></i> Advanced Search
                            </button>

                            <a class="btn btn-primary waves-effect waves-light" onclick="window.history.back();">
                                <i class="mdi mdi-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="search-form">
            <form action="">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <label for="txtsearch">Search</label>
                                                <input class="form-control" name="txtsearch" type="text" value="<?= isset($txtsearch) ? $txtsearch : ''; ?>" placeholder="Type to search...">
                                            </div>

                                            <div class="col-lg-4" style="margin-top: 27px;">
                                                <button type="submit" class="btn btn-primary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Submit">
                                                    Submit
                                                </button>
                                                <a href="<?= site_url('feedback'); ?>" class="btn btn-secondary waves-effect waves-light mr-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Clear Searched Filters">
                                                    <i class="mdi mdi-refresh"></i> Clear
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <?= view('admin/_topmessage'); ?>
                    <div class="card-body">
                        <?php if ($pagination["totalRecords"] > 0) { ?>
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered dt-responsive">
                                    <thead>
                                        <tr>
                                            <th data-sortable="true" class="text-center">Sl</th>
                                            <th data-sortable="true" class="text-center">Name</th>
                                            <th data-sortable="true" class="text-center">Email</th>
                                            <th data-sortable="true" class="text-center">Phone</th>
                                            <th data-sortable="true" class="text-center">Feedback</th>
                                            <th data-sortable="true" class="text-center">Created At</th>
                                            <th data-orderable="false" class="text-center">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($results as $feedback) { ?>
                                            <tr>
                                                <td><?= ++$startLimit; ?></td>
                                                <td><?= esc($feedback->name); ?></td>
                                                <td><?= esc($feedback->email); ?></td>
                                                <td><?= esc($feedback->phone); ?></td>
                                                <td style="width: 300px; text-align: justify;"><?= esc($feedback->message); ?></td>
                                                <td><?= date('F j, Y h:i A', strtotime($feedback->created_at)); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-danger m-1 delete-feedback" data-id="<?= $feedback->id ?>" data-name="<?= $feedback->name ?>" data-bs-toggle="tooltip" title="Delete Feedback">
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
<div class="modal fade" id="deleteFeedbackModal" tabindex="-1" aria-labelledby="deleteFeedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteFeedbackModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                <h5>Are you sure you want to delete <strong id="feedbackNameToDelete"></strong>?</h5>
                <p>This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                <div class="avatar-lg mx-auto mb-4">
                    <div class="avatar-title bg-light text-success display-4 rounded-circle">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
                <h4 id="successMessage">Feedback deleted successfully!</h4>
                <p class="text-muted">The record has been permanently removed from the system.</p>
                <button type="button" class="btn btn-success mt-3" data-bs-dismiss="modal" onclick="setTimeout(function(){ location.reload(); }, 300);">
                    <i class="fas fa-check me-1"></i> Continue
                </button>
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
                <p class="text-muted">The feedback record was not deleted.</p>
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
        // Check if the search form should be shown
        const isFormVisible = localStorage.getItem('isSearchFormVisible');
        const currentTime = new Date().getTime();
        const visibilityDuration = 10 * 60 * 1000; // 10 minutes in milliseconds
        const formVisibleTimestamp = localStorage.getItem('searchFormVisible');

        if (isFormVisible === 'true' && formVisibleTimestamp && (currentTime - formVisibleTimestamp < visibilityDuration)) {
            $('.search-form').show();
        } else {
            $('.search-form').hide();
        }

        $('#toggleSearchBtn').click(function() {
            const isVisible = $('.search-form').is(':visible');

            if (isVisible) {
                $('.search-form').hide();
                localStorage.setItem('isSearchFormVisible', 'false');
            } else {
                $('.search-form').show(); // Show the form
                localStorage.setItem('isSearchFormVisible', 'true');
                localStorage.setItem('searchFormVisible', new Date().getTime());
            }
        });

        // Variables to track delete action
        let currentfeedbackId = null;
        let currentfeedbackName = null;
        let deleteConfirmed = false;

        // When delete button is clicked
        $('.delete-feedback').on('click', function() {
            currentfeedbackId = $(this).data('id');
            currentfeedbackName = $(this).data('name');
            deleteConfirmed = false; // Reset flag

            // Update modal text with feedback name
            $('#feedbackNameToDelete').text(currentfeedbackName);

            // Show the confirmation modal
            $('#deleteFeedbackModal').modal('show');
        });

        // When confirm delete is clicked
        $('#confirmDelete').on('click', function() {
            deleteConfirmed = true;
            // Close the confirmation modal
            $('#deleteFeedbackModal').modal('hide');

            // Show loading state
            const $btn = $(this);
            $btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Deleting...').prop('disabled', true);

            // AJAX request to delete feedback
            $.ajax({
                url: '<?= site_url("feedback/delete") ?>',
                type: 'POST',
                data: {
                    feedback_id: currentfeedbackId,
                },
                success: function(response) {
                    if (response.success) {
                        $('#successMessage').html(response.message);
                        $('#deleteSuccessModal').modal('show');
                    } else {
                        $('#successMessage').html(response.message || 'An error occurred while processing your request.');
                        $('#deleteSuccessModal').modal('show');
                    }
                },
                error: function() {
                    $('#successMessage').html(
                        `An error occurred while deleting ${currentfeedbackName}.`
                    );
                    $('#deleteSuccessModal').modal('show');
                },
                complete: function() {
                    // Reset button state
                    $btn.html('<i class="fas fa-trash-alt me-1"></i> Delete').prop('disabled', false);
                }
            });
        });

        // When delete modal is hidden (either by cancel or close button)
        $('#deleteFeedbackModal').on('hidden.bs.modal', function() {
            // Only show data safe modal if delete wasn't confirmed
            if (!deleteConfirmed) {
                $('#dataSafeModal').modal('show');
            }
        });
    });
</script>
<?= $this->endSection() ?>