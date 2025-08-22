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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Notifications</a></li>
                        <li class="breadcrumb-item active">Notification List</li>
                    </ol>
                    <div class="page-title-right">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <button id="toggleSearchBtn" type="button" class="btn btn-outline-primary waves-effect waves-light">
                                <i class="mdi mdi-magnify me-1"></i> Advanced Search
                            </button>
                            <a class="btn btn-outline-secondary waves-effect waves-light" onclick="window.history.back();">
                                <i class="mdi mdi-arrow-left me-1"></i> Back
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
                                <label for="txtsearch" class="form-label">Search Notifications</label>
                                <input class="form-control" name="txtsearch" type="text" value="<?= isset($txtsearch) ? esc($txtsearch) : ''; ?>" placeholder="Search by title or message...">
                            </div>
                            <div class="col-md-4">
                                <label for="is_read" class="form-label">Read Status</label>
                                <select name="is_read" class="form-select">
                                    <option value="" <?= !isset($is_read) ? 'selected' : '' ?>>All</option>
                                    <option value="0" <?= (isset($is_read) && $is_read === '0') ? 'selected' : '' ?>>Unread</option>
                                    <option value="1" <?= (isset($is_read) && $is_read === '1') ? 'selected' : '' ?>>Read</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary waves-effect waves-light me-2">
                                    <i class="mdi mdi-filter-outline me-1"></i> Apply Filters
                                </button>
                                <a href="<?= site_url('notifications'); ?>" class="btn btn-light waves-effect">
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
                                            <th class="text-center">Title</th>
                                            <th class="text-center">Message</th>
                                            <th class="text-center">Read Status</th>
                                            <th class="text-center">Created At</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $item) { ?>
                                            <tr>
                                                <td><?= ++$startLimit; ?></td>
                                                <td><?= esc($item->title); ?></td>
                                                <td><?= esc($item->message); ?></td>
                                                <td>
                                                    <?php if ($item->is_read) : ?>
                                                        <span class="badge bg-success">Read</span>
                                                    <?php else : ?>
                                                        <span class="badge bg-warning text-dark">Unread</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= date('d M Y, H:i', strtotime($item->created_at)); ?></td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a href="<?= site_url("notifications/view/" . $item->id) ?>" class="btn btn-info btn-sm" data-bs-toggle="tooltip" title="View Notification">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?= site_url("notifications/mark-read/" . $item->id) ?>" class="btn btn-success btn-sm" data-bs-toggle="tooltip" title="Mark as Read">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                        <a href="<?= site_url("notifications/delete/" . $item->id) ?>" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" title="Delete Notification" onclick="return confirm('Are you sure to delete this notification?');">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
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
    });
</script>

<?= $this->endSection() ?>