<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-content">

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
   </style>
   <div class="container-fluid">
      <!-- Page Title & Breadcrumbs -->
      <div class="row">
         <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
               <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="<?= site_url('employees') ?>">Employee Management</a></li>
                  <li class="breadcrumb-item active"><?= esc($pageTitle) ?></li>
               </ol>

               <div class="page-title-right">
                  <a href="<?= site_url('employees') ?>" class="btn btn-secondary waves-effect waves-light">
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
                  <!-- Employee Profile Header -->
                  <div class="row mb-4">
                     <div class="col-md-12">
                        <div class="d-flex align-items-center">
                           <div class="flex-shrink-0 me-3">
                              <?php if (!empty($employee['profile_picture'])):
                                 $imagePath = base_url('uploads/profile_pictures/' . $employee['profile_picture']);
                              ?>
                                 <img src="<?= $imagePath ?>"
                                    alt="Profile Image"
                                    class="avatar-xl rounded-circle">
                              <?php else: ?>
                                 <div class="avatar-xl rounded-circle bg-light text-center d-flex align-items-center justify-content-center">
                                    <span class="display-4 text-muted"><?= strtoupper(substr($employee['first_name'], 0, 1)) ?></span>
                                 </div>
                              <?php endif; ?>
                           </div>
                           <div class="flex-grow-1">
                              <h4 class="mb-1"><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></h4>
                              <div class="d-flex flex-wrap gap-2">
                                 <span class="badge bg-<?= $employee['status'] === 'active' ? 'success' : 'danger' ?>">
                                    <?= ucfirst(esc($employee['status'])) ?>
                                 </span>
                                 <span class="badge bg-info">
                                    <?= esc($employee['employee_code'] ?? 'N/A') ?>
                                 </span>
                                 <span class="badge bg-primary">
                                    <?= esc($employee['designation'] ?? 'N/A') ?>
                                 </span>
                              </div>
                           </div>
                           <div class="flex-shrink-0">
                              <a href="<?= site_url('employees/edit/' . $employee['employee_id']) ?>"
                                 class="btn btn-primary me-2">
                                 <i class="fas fa-edit me-1"></i> Edit
                              </a>
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- Main Details Section -->
                  <div class="row">
                     <!-- Personal Information Card -->
                     <div class="col-md-6">
                        <div class="card border">
                           <div class="card-header bg-light">
                              <h5 class="card-title mb-0"><i class="fas fa-user me-2"></i> Personal Information</h5>
                           </div>
                           <div class="card-body">
                              <div class="table-responsive">
                                 <table class="table table-borderless mb-0">
                                    <tbody>
                                       <tr>
                                          <th width="40%">Full Name</th>
                                          <td><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></td>
                                       </tr>
                                       <tr>
                                          <th>Date of Birth</th>
                                          <td>
                                             <?= !empty($employee['dob']) ? date('F j, Y', strtotime($employee['dob'])) : 'N/A' ?>
                                             <?php if (!empty($employee['dob'])): ?>
                                                <small class="text-muted">(Age: <?= date_diff(date_create($employee['dob']), date_create('today'))->y ?> years)</small>
                                             <?php endif; ?>
                                          </td>
                                       </tr>
                                       <tr>
                                          <th>Gender</th>
                                          <td><?= ucfirst(esc($employee['gender'] ?? 'N/A')) ?></td>
                                       </tr>
                                       <tr>
                                          <th>Phone Number</th>
                                          <td><?= esc($employee['phone'] ?? 'N/A') ?></td>
                                       </tr>
                                       <tr>
                                          <th>Email Address</th>
                                          <td><?= esc($employee['email'] ?? 'N/A') ?></td>
                                       </tr>
                                       <tr>
                                          <th>Address</th>
                                          <td><?= esc($employee['address'] ?? 'N/A') ?></td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- Employment Information Card -->
                     <div class="col-md-6">
                        <div class="card border">
                           <div class="card-header bg-light">
                              <h5 class="card-title mb-0"><i class="fas fa-briefcase me-2"></i> Employment Information</h5>
                           </div>
                           <div class="card-body">
                              <div class="table-responsive">
                                 <table class="table table-borderless mb-0">
                                    <tbody>
                                       <tr>
                                          <th width="40%">Employee Code</th>
                                          <td><?= esc($employee['employee_code'] ?? 'N/A') ?></td>
                                       </tr>
                                       <tr>
                                          <th>Designation</th>
                                          <td><?= esc($employee['designation'] ?? 'N/A') ?></td>
                                       </tr>
                                       <tr>
                                          <th>Department</th>
                                          <td><?= esc($employee['department'] ?? 'N/A') ?></td>
                                       </tr>
                                       <tr>
                                          <th>Joined Date</th>
                                          <td>
                                             <?= !empty($employee['joined_date']) ? date('F j, Y', strtotime($employee['joined_date'])) : 'N/A' ?>
                                             <?php if (!empty($employee['joined_date'])): ?>
                                                <small class="text-muted">(<?= date_diff(date_create($employee['joined_date']), date_create('today'))->y ?> years with company)</small>
                                             <?php endif; ?>
                                          </td>
                                       </tr>
                                       <tr>
                                          <th>Account Status</th>
                                          <td>
                                             <span class="badge bg-<?= strtolower($employee['status']) === 'active' ? 'success' : 'danger' ?>">
                                                <?= ucfirst(esc($employee['status'])) ?>
                                             </span>
                                          </td>
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
                                          <th width="40%">Member Since</th>
                                          <td><?= date('F j, Y, g:i a', strtotime($employee['created_at'])) ?></td>
                                       </tr>
                                       <tr>
                                          <th>Last Updated</th>
                                          <td><?= !empty($employee['updated_at']) ? date('F j, Y, g:i a', strtotime($employee['updated_at'])) : 'Not updated yet' ?></td>
                                       </tr>
                                       <tr>
                                          <th>Created By</th>
                                          <td>
                                             <?php
                                             $loggedInUserId = session()->get('user_id');
                                             $loggedInUserName = session()->get('name');

                                             if (isset($employee['created_by']) && $employee['created_by'] == $loggedInUserId) {
                                                echo esc($loggedInUserName);
                                             } else {
                                                echo esc($loggedInUserName ?? 'System');
                                             }
                                             ?>
                                          </td>
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