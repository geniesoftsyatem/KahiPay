<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

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

   .img-thumbnail {
      max-height: 100px;
      object-fit: cover;
   }
</style>

<div class="page-content">
   <div class="container-fluid">

      <!-- Page Title & Breadcrumbs -->
      <div class="row">
         <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
               <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="<?= site_url('request-letters') ?>">Request Letters</a></li>
                  <li class="breadcrumb-item active"><?= esc($pageTitle) ?></li>
               </ol>
               <div class="page-title-right">
                  <a href="<?= site_url('request-letters') ?>" class="btn btn-secondary waves-effect waves-light">
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

                  <div class="row">
                     <!-- Basic Info Card -->
                     <div class="col-md-6">
                        <div class="card border">
                           <div class="card-header bg-light">
                              <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i> Request Letter Info</h5>
                           </div>
                           <div class="card-body">
                              <table class="table table-borderless mb-0">
                                 <tbody>
                                    <tr>
                                       <th width="40%">Employee Name</th>
                                       <td><?= esc($letter['employee_name'] ?? 'N/A') ?></td>
                                    </tr>
                                    <tr>
                                       <th>Reporting Manager</th>
                                       <td><?= esc($letter['reporting_manager_name'] ?? 'N/A') ?></td>
                                    </tr>
                                    <tr>
                                       <th>Created At</th>
                                       <td>
                                          <?= date('F j, Y', strtotime($letter['created_at'])) ?>
                                          <small class="text-muted">(<?= date('h:i A', strtotime($letter['created_at'])) ?>)</small>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>

                     <!-- Images Card -->
                     <div class="col-md-6">
                        <div class="card border">
                           <div class="card-header bg-light">
                              <h5 class="card-title mb-0">
                                 <i class="fas fa-image me-2"></i> Uploaded Images
                              </h5>
                           </div>
                           <div class="card-body">
                              <?php if (!empty($letter['images'])) { ?>
                                 <div class="d-flex flex-wrap">
                                    <?php
                                    $images = explode(',', $letter['images']);
                                    foreach ($images as $img) {
                                       if (trim($img)) { ?>
                                          <img src="<?= base_url(trim($img)) ?>" class="img-thumbnail me-2 mb-2" style="width: 100px; height: 100px; object-fit: cover;" alt="Request Image">
                                    <?php }
                                    } ?>
                                 </div>
                              <?php } else { ?>
                                 <span class="text-muted">No images uploaded</span>
                              <?php } ?>
                           </div>
                        </div>
                     </div>

                     <!-- Description -->
                     <div class="col-md-12 mt-4">
                        <div class="card border">
                           <div class="card-header bg-light">
                              <h5 class="card-title mb-0"><i class="fas fa-align-left me-2"></i> Description</h5>
                           </div>
                           <div class="card-body">
                              <div class="description-box">
                                 <?= nl2br(esc($letter['description'])) ?>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- Action Buttons -->
                  <div class="row mt-4">
                     <div class="col-md-12 d-flex justify-content-between">
                        <a href="<?= site_url('request-letters/edit?id=' . $letter['request_id']) ?>" class="btn btn-primary">
                           <i class="fas fa-edit me-1"></i> Edit Request
                        </a>

                        <form action="<?= site_url('request-letters/delete') ?>" method="post" onsubmit="return confirm('Are you sure you want to delete this request?');">
                           <input type="hidden" name="id" value="<?= $letter['request_id'] ?>">
                           <button type="submit" class="btn btn-danger">
                              <i class="fas fa-trash-alt me-1"></i> Delete Request
                           </button>
                        </form>
                     </div>
                  </div>

               </div>
            </div>
         </div>
      </div>

   </div>
</div>

<?= $this->endSection() ?>