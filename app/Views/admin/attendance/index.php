<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-content">
    <div class="container-fluid">
        <!-- Breadcrumb and Back Button -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('employee-attendance') ?>">Employee Attendance</a></li>
                        <li class="breadcrumb-item active"><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></li>
                    </ol>
                    <div class="page-title-right">
                        <a href="<?= site_url('employees') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Form -->
        <form action="<?= current_url() ?>" method="get">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="from_date" class="form-label">From Date</label>
                                    <input type="date" class="form-control" name="from_date"
                                        value="<?= esc($searchArray['from_date'] ?? '') ?>">
                                </div>

                                <div class="col-md-3">
                                    <label for="to_date" class="form-label">To Date</label>
                                    <input type="date" class="form-control" name="to_date"
                                        value="<?= esc($searchArray['to_date'] ?? '') ?>">
                                </div>

                                <div class="col-md-6 d-flex align-items-end">
                                    <div>
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                        <a href="<?= site_url('employee-attendance/view/' . $searchArray['employee_id']) ?>"
                                            class="btn btn-outline-secondary">
                                            <i class="fas fa-sync-alt"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Attendance Table -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <?php echo view('admin/_topmessage'); ?>
                    <div class="card-body">
                        <?php if ($pagination["totalRecords"] > 0) { ?>
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered dt-responsive w-100">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Punch In</th>
                                            <th class="text-center">Punch Out</th>
                                            <th class="text-center">Total Hours</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $index => $item) { ?>
                                            <tr>
                                                <td class="text-center"><?= $startLimit + $index + 1 ?></td>
                                                <td class="text-center"><?= date('d M Y', strtotime($item['punch_date'])) ?></td>
                                                <td class="text-center"><?= $item['punch_in'] ? date('h:i A', strtotime($item['punch_in'])) : '-' ?></td>
                                                <td class="text-center"><?= $item['punch_out'] ? date('h:i A', strtotime($item['punch_out'])) : '-' ?></td>
                                                <td class="text-center"><?= $item['total_hours'] ?? '0.00' ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <br>
                            <?= view('admin/_paging', ['paginate' => $pagination, 'siteurl' => $action, 'varExtra' => $searchArray]); ?>
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
        // Initialize DataTable
        $('#datatable').DataTable({
            dom: 'Bfrtip',
            searching: false,
            paging: false,
            info: false,
        });
    });
</script>
<?= $this->endSection() ?>