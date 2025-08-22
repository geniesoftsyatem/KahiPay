<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

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

   .description-box {
      background-color: white;
      border: 1px solid #eee;
      border-radius: 8px;
      padding: 15px;
      min-height: 100px;
   }
</style>

<div class="page-content">
   <div class="container-fluid">
      <!-- Page Title & Breadcrumbs -->
      <div class="row">
         <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
               <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="<?= site_url('employee-tasks') ?>">Task Management</a></li>
                  <li class="breadcrumb-item active"><?= esc($pageTitle) ?></li>
               </ol>

               <div class="page-title-right">
                  <a href="<?= site_url('employee-tasks') ?>" class="btn btn-secondary waves-effect waves-light">
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

                  <!-- Main Details Section -->
                  <div class="row">
                     <!-- Task Information Card -->
                     <div class="col-md-6">
                        <div class="card border">
                           <div class="card-header bg-light">
                              <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i> Task Information</h5>
                           </div>
                           <div class="card-body">
                              <div class="table-responsive">
                                 <table class="table table-borderless mb-0">
                                    <tbody>
                                       <tr>
                                          <th width="40%">Assigned To</th>
                                          <td><?= esc($task['employee_name'] ?? 'N/A') ?></td>
                                       </tr>
                                       <tr>
                                          <th>Assigned By</th>
                                          <td><?= esc($task['assigner_name'] ?? 'N/A') ?></td>
                                       </tr>
                                       <tr>
                                          <th>Due Date</th>
                                          <td>
                                             <?= date('F j, Y', strtotime($task['due_date'])) ?>
                                             <small class="text-muted">(<?= date('h:i A', strtotime($task['due_date'])) ?>)</small>
                                          </td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- Status Information Card -->
                     <div class="col-md-6">
                        <div class="card border">
                           <div class="card-header bg-light">
                              <h5 class="card-title mb-0"><i class="fas fa-chart-line me-2"></i> Status Information</h5>
                           </div>
                           <div class="card-body">
                              <div class="table-responsive">
                                 <table class="table table-borderless mb-0">
                                    <tbody>
                                       <tr>
                                          <th width="40%">Priority</th>
                                          <td>
                                             <?php
                                             $priority = isset($task['priority']) ? strtolower($task['priority']) : '';
                                             if ($priority === 'high') {
                                                $badgeClass = 'danger';
                                             } elseif ($priority === 'medium') {
                                                $badgeClass = 'warning';
                                             } elseif ($priority === 'low') {
                                                $badgeClass = 'success';
                                             } else {
                                                $badgeClass = 'secondary';
                                             }
                                             ?>
                                             <span class="badge bg-<?= $badgeClass ?>">
                                                <?= $priority ? ucfirst(esc($priority)) : 'Not Set' ?>
                                             </span>
                                          </td>
                                       </tr>
                                       <tr>
                                          <th width="40%">Status</th>
                                          <td>
                                             <?php
                                             $status = isset($task['status']) ? strtolower($task['status']) : '';
                                             if ($status === 'pending') {
                                                $badgeClass = 'warning';
                                                $statusLabel = 'Pending';
                                             } elseif ($status === 'in_progress') {
                                                $badgeClass = 'primary';
                                                $statusLabel = 'In Progress';
                                             } elseif ($status === 'completed') {
                                                $badgeClass = 'success';
                                                $statusLabel = 'Completed';
                                             } else {
                                                $badgeClass = 'secondary';
                                                $statusLabel = 'Not Set';
                                             }
                                             ?>
                                             <span class="badge bg-<?= $badgeClass ?>">
                                                <?= esc($statusLabel) ?>
                                             </span>
                                          </td>
                                       </tr>

                                       <tr>
                                          <th>Created At</th>
                                          <td>
                                             <?= date('F j, Y', strtotime($task['created_at'])) ?>
                                             <small class="text-muted">(<?= date('h:i A', strtotime($task['created_at'])) ?>)</small>
                                          </td>
                                       </tr>
                                       <?php if ($task['status'] == 'completed'): ?>
                                          <tr>
                                             <th>Completed At</th>
                                             <td>
                                                <?= date('F j, Y', strtotime($task['completed_at'])) ?>
                                                <small class="text-muted">(<?= date('h:i A', strtotime($task['completed_at'])) ?>)</small>
                                             </td>
                                          </tr>
                                       <?php endif; ?>
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- Description Card -->
                     <div class="col-md-12 mt-4">
                        <div class="card border">
                           <div class="card-header bg-light">
                              <h5 class="card-title mb-0"><i class="fas fa-align-left me-2"></i> Task Description</h5>
                           </div>
                           <div class="card-body">
                              <div class="description-box">
                                 <?= nl2br(esc($task['description'])) ?>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- Action Buttons -->
                  <div class="row mt-4">
                     <div class="col-md-12 d-flex justify-content-between">
                        <a href="<?= site_url('employee-tasks/edit/' . $task['task_id']) ?>" class="btn btn-primary">
                           <i class="fas fa-edit me-1"></i> Edit Task
                        </a>

                        <?php if ($task['status'] != 'completed'): ?>
                           <form action="<?= site_url('employee-tasks/update-status') ?>" method="post" class="d-inline">
                              <input type="hidden" name="task_id" value="<?= $task['task_id'] ?>">
                              <input type="hidden" name="status" value="completed">
                              <button type="submit" class="btn btn-success">
                                 <i class="fas fa-check-circle me-1"></i> Mark as Completed
                              </button>
                           </form>
                        <?php endif; ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<?= $this->endSection() ?>