<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">

   <!-- Custom CSS -->
   <style>
      .logo-xl {
         width: 120px;
         height: 120px;
         object-fit: contain;
         border-radius: 8px;
         border: 1px solid #dee2e6;
         padding: 5px;
         background: white;
      }

      .table-borderless tbody tr th {
         font-weight: 500;
         color: #6c757d;
      }

      .card-header {
         padding: 1rem 1.25rem;
         background-color: #f8f9fa !important;
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
   <div class="container-fluid">
      <!-- Page Title & Breadcrumbs -->
      <div class="row">
         <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
               <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="<?= site_url('companies') ?>">Company Management</a></li>
                  <li class="breadcrumb-item active"><?= esc($pageTitle) ?></li>
               </ol>

               <div class="page-title-right">
                  <a href="<?= site_url('companies') ?>" class="btn btn-secondary waves-effect waves-light">
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
                  <!-- Company Profile Header -->
                  <div class="row mb-4">
                     <div class="col-md-12">
                        <div class="d-flex align-items-center">
                           <div class="flex-shrink-0 me-3">
                              <?php if (!empty($company['logo'])):
                                 $imagePath = base_url('uploads/companies/' . $company['logo']);
                              ?>
                                 <img src="<?= $imagePath ?>"
                                    alt="Company Logo"
                                    class="avatar-md rounded-circle">
                              <?php else: ?>
                                 <div class="avatar-md rounded-circle bg-light text-center d-flex align-items-center justify-content-center">
                                    <span class="display-4 text-muted"><?= strtoupper(substr($company['company_name'], 0, 1)) ?></span>
                                 </div>
                              <?php endif; ?>
                           </div>
                           <div class="flex-grow-1">
                              <h4 class="mb-1"><?= esc($company['company_name']) ?></h4>
                              <div class="d-flex flex-wrap gap-2">
                                 <span class="badge bg-<?= $company['status'] === 'active' ? 'success' : 'danger' ?>">
                                    <?= ucfirst(esc($company['status'])) ?>
                                 </span>
                                 <span class="badge bg-info">
                                    <?= esc($company['company_code'] ?? 'N/A') ?>
                                 </span>
                                 <?php if (!empty($company['website'])): ?>
                                    <span class="badge bg-primary">
                                       <a href="<?= esc($company['website']) ?>" target="_blank" class="text-white">
                                          <i class="fas fa-external-link-alt me-1"></i> Website
                                       </a>
                                    </span>
                                 <?php endif; ?>
                              </div>
                           </div>
                           <div class="flex-shrink-0">
                              <a href="<?= site_url('companies/edit/' . $company['company_id']) ?>"
                                 class="btn btn-primary me-2">
                                 <i class="fas fa-edit me-1"></i> Edit
                              </a>
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- Main Details Section -->
                  <div class="row">
                     <!-- Company Information Card -->
                     <div class="col-md-6">
                        <div class="card border">
                           <div class="card-header bg-light">
                              <h5 class="card-title mb-0"><i class="fas fa-building me-2"></i> Company Information</h5>
                           </div>
                           <div class="card-body">
                              <div class="table-responsive">
                                 <table class="table table-borderless mb-0">
                                    <tbody>
                                       <tr>
                                          <th width="40%">Company Name</th>
                                          <td><?= esc($company['company_name']) ?></td>
                                       </tr>
                                       <tr>
                                          <th>Company Code</th>
                                          <td><?= esc($company['company_code'] ?? 'N/A') ?></td>
                                       </tr>
                                       <tr>
                                          <th>Email Address</th>
                                          <td><?= esc($company['email'] ?? 'N/A') ?></td>
                                       </tr>
                                       <tr>
                                          <th>Phone Number</th>
                                          <td><?= esc($company['phone'] ?? 'N/A') ?></td>
                                       </tr>
                                       <tr>
                                          <th>Website</th>
                                          <td>
                                             <?php if (!empty($company['website'])): ?>
                                                <a href="<?= esc($company['website']) ?>" target="_blank">
                                                   <?= esc($company['website']) ?>
                                                </a>
                                             <?php else: ?>
                                                N/A
                                             <?php endif; ?>
                                          </td>
                                       </tr>
                                       <tr>
                                          <th>Status</th>
                                          <td>
                                             <span class="status-badge status-<?= strtolower($company['status']) ?>">
                                                <?= ucfirst(esc($company['status'])) ?>
                                             </span>
                                          </td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- Legal & Address Information Card -->
                     <div class="col-md-6">
                        <div class="card border">
                           <div class="card-header bg-light">
                              <h5 class="card-title mb-0"><i class="fas fa-file-contract me-2"></i> Legal & Address</h5>
                           </div>
                           <div class="card-body">
                              <div class="table-responsive">
                                 <table class="table table-borderless mb-0">
                                    <tbody>
                                       <tr>
                                          <th width="40%">PAN Number</th>
                                          <td><?= esc($company['pan'] ?? 'N/A') ?></td>
                                       </tr>
                                       <tr>
                                          <th>GST Number</th>
                                          <td><?= esc($company['gst'] ?? 'N/A') ?></td>
                                       </tr>
                                       <tr>
                                          <th>Address</th>
                                          <td><?= nl2br(esc($company['address'] ?? 'N/A')) ?></td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- System Information Card -->
                     <div class="col-md-12 mt-4">
                        <div class="card border">
                           <div class="card-header bg-light">
                              <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i> System Information</h5>
                           </div>
                           <div class="card-body">
                              <div class="table-responsive">
                                 <table class="table table-borderless mb-0">
                                    <tbody>
                                       <tr>
                                          <th width="40%">Created At</th>
                                          <td><?= date('F j, Y, g:i a', strtotime($company['created_at'])) ?></td>
                                       </tr>
                                       <tr>
                                          <th>Last Updated</th>
                                          <td><?= !empty($company['updated_at']) ? date('F j, Y, g:i a', strtotime($company['updated_at'])) : 'Not updated yet' ?></td>
                                       </tr>
                                    </tbody>
                                 </table>
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