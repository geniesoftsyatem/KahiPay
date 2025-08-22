<style>
   .employee-details-card {
      border-radius: 12px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
      border: none;
   }

   .detail-header {
      border-bottom: 1px solid #e1e1e1;
      padding-bottom: 15px;
      margin-bottom: 25px;
   }

   .detail-item {
      padding: 14px 0;
      border-bottom: 1px solid #efefef;
   }

   .detail-label {
      font-weight: 600;
      color: #444;
      min-width: 160px;
      font-size: 0.95rem;
   }

   .detail-value {
      color: #222;
      font-weight: 500;
      font-size: 0.95rem;
   }

   .back-btn {
      border-radius: 6px;
      padding: 10px 20px;
      font-weight: 500;
      transition: all 0.3s ease;
   }

   .back-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
   }

   .detail-container {
      background-color: #fff;
      border-radius: 10px;
      padding: 25px;
   }

   .btn-outline-secondary {
      opacity: 1 !important;
      border-color: #6c757d;
      color: #6c757d;
   }

   .btn-outline-secondary:hover {
      background-color: #6c757d;
      color: white;
   }

   .profile-picture {
      width: 130px;
      height: 130px;
      object-fit: cover;
      border-radius: 50%;
      border: 4px solid #fff;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
   }

   .status-badge {
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      display: inline-block;
      margin-top: 10px;
   }

   .status-active {
      background-color: #e6f4ea;
      color: #2e7d32;
   }

   .status-inactive {
      background-color: #fce8e6;
      color: #c62828;
   }

   .action-buttons .btn {
      min-width: 150px;
   }

   @media (max-width: 768px) {
      .detail-label {
         margin-bottom: 6px;
      }
   }
</style>

<div class="page-content">
   <div class="container-fluid">
      <div class="row justify-content-center">
         <div class="col-lg-12 mt-2">
            <div class="card employee-details-card">
               <div class="card-body p-4">
                  <div class="detail-header d-flex justify-content-between align-items-center flex-wrap">
                     <h3 class="mb-0 text-primary">
                        <i class="fas fa-user-tie mr-2"></i> Employee Details
                     </h3>
                     <a href="<?php echo site_url('employees') ?>" class="btn btn-outline-secondary back-btn mt-2 mt-md-0">
                        <i class="fas fa-arrow-left mr-2"></i> Back to List
                     </a>
                  </div>

                  <div class="row mt-4">
                     <div class="col-md-3 text-center mb-4">
                        <?php if (!empty($employee['profile_picture'])): ?>
                           <img src="<?php echo base_url('uploads/profile_pictures/' . $employee['profile_picture']); ?>"
                              class="profile-picture mb-2"
                              alt="Profile Picture">
                        <?php else: ?>
                           <div class="profile-picture mb-2 bg-light d-flex align-items-center justify-content-center">
                              <i class="fas fa-user text-muted" style="font-size: 3rem;"></i>
                           </div>
                        <?php endif; ?>

                        <div class="status-badge <?php echo ($employee['status'] == 'active') ? 'status-active' : 'status-inactive'; ?>">
                           <?php echo ucfirst($employee['status']); ?>
                        </div>
                     </div>

                     <div class="col-md-9">
                        <div class="detail-container">
                           <?php
                           $fields = [
                              'Employee Code' => $employee['employee_code'] ?? 'N/A',
                              'Full Name' => $employee['first_name'] . ' ' . $employee['last_name'],
                              'Date of Birth' => !empty($employee['dob']) ? date('F j, Y', strtotime($employee['dob'])) .
                                 ' <small class="text-muted ml-2">(Age: ' . date_diff(date_create($employee['dob']), date_create('today'))->y . ' years)</small>' : 'N/A',
                              'Gender' => ucfirst($employee['gender'] ?? 'N/A'),
                              'Phone' => !empty($employee['phone']) ? '<a href="tel:' . $employee['phone'] . '">' . $employee['phone'] . '</a>' : 'N/A',
                              'Email' => !empty($employee['email']) ? '<a href="mailto:' . $employee['email'] . '">' . $employee['email'] . '</a>' : 'N/A',
                              'Address' => $employee['address'] ?? 'N/A',
                              'Designation' => $employee['designation'] ?? 'N/A',
                              'Department' => $employee['department'] ?? 'N/A',
                              'Joined Date' => !empty($employee['joined_date']) ? date('F j, Y', strtotime($employee['joined_date'])) .
                                 ' <small class="text-muted ml-2">(' . date_diff(date_create($employee['joined_date']), date_create('today'))->y . ' years with company)</small>' : 'N/A',
                              'Member Since' => date('F j, Y', strtotime($employee['created_at'])) .
                                 ' <small class="text-muted ml-2">(' . date('h:i A', strtotime($employee['created_at'])) . ')</small>',
                              'Last Updated' => !empty($employee['updated_at']) ? date('F j, Y h:i A', strtotime($employee['updated_at'])) : 'Not updated yet',
                              'Created By' => $employee['created_by'] ?? 'System'
                           ];

                           foreach ($fields as $label => $value): ?>
                              <div class="row detail-item align-items-start">
                                 <div class="col-md-3 detail-label"><?php echo $label; ?></div>
                                 <div class="col-md-9 detail-value"><?php echo $value; ?></div>
                              </div>
                           <?php endforeach; ?>
                        </div>
                     </div>
                  </div>

                  <div class="mt-4 d-flex justify-content-between flex-wrap action-buttons">
                     <a href="#" class="btn btn-outline-primary mb-2">
                        <i class="fas fa-print mr-2"></i> Print Profile
                     </a>
                     <a href="<?php echo site_url('employees/edit/' . $employee['employee_id']); ?>" class="btn btn-primary mb-2">
                        <i class="fas fa-edit mr-2"></i> Edit Profile
                     </a>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>