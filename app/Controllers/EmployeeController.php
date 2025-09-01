<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CompanyModel;
use App\Models\EmployeeModel;
use App\Libraries\Pagination;
use App\Models\AttendanceModel;
use App\Controllers\BaseController;
use App\Models\ReportingManagerModel;

class EmployeeController extends BaseController
{
    protected $db;
    protected $session;
    protected $userModel;
    protected $companyModel;
    protected $employeeModel;
    protected $reportingManagerModel;

    public function __construct()
    {
        $this->session = session();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
        $this->companyModel = new CompanyModel();
        $this->employeeModel = new EmployeeModel();
        $this->reportingManagerModel = new ReportingManagerModel();
    }
    public function index()
    {
        set_title('Employee List | ' . SITE_NAME);

        $companyId  = session('company_id');
        $userType   = session('user_type');

        $data = [
            'pagetitle'   => "Employee List",
            'action'      => "employees",
            'startLimit'  => 0,
            'reverse'     => 0,
            'pagination'  => '',
            'results'     => [],
            'searchArray' => []
        ];

        $customPagination = new Pagination();

        // Collect search criteria
        $searchFields = $this->request->getGet();
        foreach ($searchFields as $field => $searchValue) {
            $data['searchArray'][$field] = trim($searchValue);
        }

        // If logged in as company, force company_id in search
        if ($userType === 'company' && !empty($companyId)) {
            $data['searchArray']['company_id'] = $companyId;
        }

        $selectedCompanyId = $data['searchArray']['company_id'] ?? null;

        $data['managers'] = [];
        if (!empty($selectedCompanyId)) {
            $data['managers'] = $this->reportingManagerModel->getReportingManagers([
                'companyId' => $selectedCompanyId
            ]);
        }

        if (empty($data['managers']) && $userType === 'company') {
            $data['managers'] = $this->reportingManagerModel->getReportingManagers([
                'companyId' => $companyId
            ]);
        }
        $data['companies'] = $this->companyModel->where('status', 'Active')->findAll();

        $Limit = 10;
        $page = (int) $this->request->getGet('page') ?: 1;
        $totalRecord = $this->employeeModel->getEmployees($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['startLimit'] = $startLimit;
        $data['reverse'] = $totalRecord - $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);
        $data['results'] = $this->employeeModel->getEmployees($data['searchArray'], $startLimit, $Limit);

        return view('admin/employee/index', $data);
    }

    public function create()
    {
        set_title('Add Employee | ' . SITE_NAME);

        $data['pagetitle'] = "Add Employee";
        $userType = $this->session->get('user_type');

        if ($userType === 'company') {
            $data['companies'] = [];
            $companyId = $this->session->get('company_id');
            $data['managers'] = $this->reportingManagerModel->getReportingManagers([
                'search' => 'all',
                'companyId' => $companyId
            ]);
        } else {
            $data['managers'] = $this->reportingManagerModel->getReportingManagers([
                'search' => 'all'
            ]);
            $data['companies'] = $this->companyModel->where('status', 'Active')->findAll();
        }

        return view('admin/employee/create', $data);
    }

    public function edit($employeeId)
    {
        set_title('Edit Employee | ' . SITE_NAME);

        $data['pagetitle'] = "Edit Employee";
        $employee = $this->employeeModel->where('employee_id', $employeeId)->first();

        if (!$employee) {
            $this->session->setFlashdata('error', 'Employee not found');
            return redirect()->to(site_url('employees'));
        }

        $reportingManager = $this->reportingManagerModel
            ->where('employee_id', $employeeId)
            ->select('manager_id')
            ->first();

        $data['reporting_manager_id'] = $reportingManager['manager_id'] ?? null;

        $userType = $this->session->get('user_type');
        $companyId = $this->session->get('company_id');

        // Include 'search' => 'all' here too
        $managerOptions = [
            'search' => 'all',
            'employeeId' => $employeeId
        ];

        if ($userType === 'company') {
            $data['companies'] = []; // No dropdown
            $managerOptions['companyId'] = $companyId;
        } else {
            $data['companies'] = $this->companyModel
                ->where('status', 'Active')
                ->findAll();
        }

        $data['employee'] = $employee;
        $data['managers'] = $this->reportingManagerModel->getReportingManagers($managerOptions);

        return view('admin/employee/create', $data);
    }

    public function store()
    {
        $rules = [
            'company_id' => 'required',
            'first_name' => 'required',
            'phone' => 'required|numeric|min_length[10]|max_length[15]',
            'email' => 'required|valid_email|max_length[150]',
            'status' => 'required|in_list[Active,Inactive,Suspended]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $employeeId = $this->request->getPost('employee_id');
        $companyId = $this->request->getPost('company_id');
        $email = $this->request->getPost('email');
        $reportingManagerId = $this->request->getPost('reporting_manager_id');
        $password = $this->request->getPost('password') ?? 'default@123';

        // Check for duplicate email in employees table
        $existingEmployee = $this->employeeModel
            ->where('email', $email)
            ->where('employee_id !=', $employeeId)
            ->first();

        if ($existingEmployee) {
            return redirect()->back()->withInput()->with('error', "The email address '{$email}' is already being used by another employee.");
        }

        // Prepare employee data
        $employeeData = [
            'company_id' => $companyId,
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $email,
            'phone' => $this->request->getPost('phone'),
            'designation' => $this->request->getPost('designation'),
            'department' => $this->request->getPost('department'),
            'address' => $this->request->getPost('address'),
            'dob' => $this->request->getPost('dob'),
            'gender' => $this->request->getPost('gender'),
            'status' => $this->request->getPost('status'),
            'joining_date' => $this->request->getPost('joining_date'),
            'geo_tracking' => (int)$this->request->getPost('geo_tracking'),
        ];

        // Handle profile image upload
        $profileImagePath = null;
        $profileImage = $this->request->getFile('profile_image');
        if ($profileImage && $profileImage->isValid() && !$profileImage->hasMoved()) {
            $profileImagePath = $profileImage->getRandomName();
            $profileImage->move(FCPATH . 'uploads/employees', $profileImagePath);

            // Delete old image if updating
            if ($employeeId) {
                $oldImage = $this->employeeModel->find($employeeId)['profile_image'] ?? null;
                if ($oldImage && file_exists(FCPATH . 'uploads/employees/' . $oldImage)) {
                    unlink(FCPATH . 'uploads/employees/' . $oldImage);
                }
            }
            $employeeData['profile_image'] = $profileImagePath;
        }

        $this->db->transStart();

        try {
            if ($employeeId) {
                // ========== UPDATE OPERATION ==========
                $originalEmployee = $this->employeeModel->find($employeeId);

                // 1. First update the associated user (if exists)
                if (!empty($originalEmployee['user_id'])) {
                    $userData = [
                        'email' => $email,
                        'name' => $employeeData['first_name'] . ' ' . $employeeData['last_name'],
                        'phone' => $employeeData['phone'],
                        'status' => $employeeData['status'] === 'Active' ? 'Active' : 'Inactive',
                    ];

                    if ($profileImagePath) {
                        $userData['profile_image'] = $profileImagePath;
                    }

                    if (!$this->userModel->update($originalEmployee['user_id'], $userData)) {
                        throw new \RuntimeException('Failed to update user');
                    }
                }

                // 2. Then update the employee
                if (!$this->employeeModel->update($employeeId, $employeeData)) {
                    throw new \RuntimeException('Failed to update employee');
                }

                $message = "Employee updated successfully";
            } else {
                // ========== CREATE OPERATION ==========
                // Generate unique employee code
                $company = $this->companyModel->find($companyId);
                if (!$company) {
                    throw new \RuntimeException('Invalid company ID.');
                }

                $cleanedName = preg_replace('/[^a-zA-Z]/', '', $company['company_name']);
                $companyPrefix = strtoupper(substr($cleanedName, 0, 3));
                if (strlen($companyPrefix) < 3) {
                    $companyPrefix = str_pad($companyPrefix, 3, 'X');
                }

                $employeeCode = '';
                for ($i = 0; $i < 1000; $i++) {
                    $number = rand(10000, 99999);
                    $employeeCode = $companyPrefix . $number;
                    if (!$this->employeeModel->where('employee_code', $employeeCode)->first()) {
                        break;
                    }
                    if ($i === 999) {
                        throw new \RuntimeException('Failed to generate unique employee code');
                    }
                }

                $employeeData['employee_code'] = $employeeCode;
                $employeeData['created_by'] = session()->get('user_id');

                // 1. First create the user
                $userData = [
                    'name' => $employeeData['first_name'] . ' ' . $employeeData['last_name'],
                    'username' => $employeeCode,
                    'email' => $email,
                    'phone' => $employeeData['phone'],
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'user_type' => 'employee',
                    'profile_image' => $profileImagePath,
                    'status' => $employeeData['status'] === 'Active' ? 'Active' : 'Inactive',
                ];

                if (!$this->userModel->save($userData)) {
                    throw new \RuntimeException('Failed to create user');
                }

                $employeeData['user_id'] = $this->userModel->getInsertID();

                // 2. Then create the employee
                if (!$this->employeeModel->save($employeeData)) {
                    throw new \RuntimeException('Failed to create employee');
                }

                $employeeId = $this->employeeModel->getInsertID();
                $message = "Employee created successfully";
            }

            // Handle reporting manager
            if (empty($reportingManagerId)) {
                $this->reportingManagerModel->where('employee_id', $employeeId)->delete();
            } else {
                $existingMapping = $this->reportingManagerModel
                    ->where('employee_id', $employeeId)
                    ->first();

                if ($existingMapping) {
                    $this->reportingManagerModel->update($existingMapping['id'], [
                        'manager_id' => $reportingManagerId,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    $this->reportingManagerModel->save([
                        'employee_id' => $employeeId,
                        'manager_id' => $reportingManagerId,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Transaction failed');
            }

            if ($employeeId) {
                $this->session->setFlashdata('success', $message);
                return redirect()->to("employees/preview/{$employeeId}");
            } else {
                $this->session->setFlashdata('success', $message);
                return redirect()->to('employees');
            }
        } catch (\Exception $e) {
            $this->db->transRollback();

            // Clean up uploaded file if transaction failed
            if (!empty($profileImagePath) && file_exists(FCPATH . 'uploads/employees/' . $profileImagePath)) {
                unlink(FCPATH . 'uploads/employees/' . $profileImagePath);
            }

            return redirect()->back()->withInput()->with('error', "Failed to save employee: " . $e->getMessage());
        }
    }

    public function getEmployeesByCompany($companyId)
    {
        $employees = $this->employeeModel
            ->where('company_id', $companyId)
            ->findAll();
        return $this->response->setJSON($employees);
    }

    public function preview($employeeId)
    {

        set_title('Employee Details | ' . SITE_NAME);

        $data['pageTitle'] = "Employee Details";
        $data['employee'] = $this->employeeModel->where('employee_id', $employeeId)->first();
        if (!$data['employee']) {
            $this->session->setFlashdata('error', 'Employee not found');
            return redirect()->to(site_url('employees'));
        }

        return view('admin/employee/preview', $data);
    }

    public function delete()
    {
        $employeeId = $this->request->getPost('employee_id');

        // Validate employee ID
        if (empty($employeeId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Employee ID is required to proceed with deletion.'
            ]);
        }

        // Fetch the employee record
        $employee = $this->employeeModel->find($employeeId);
        if (!$employee) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No employee found with the provided ID.'
            ]);
        }

        // Begin database transaction
        $this->db->transStart();

        try {
            // Delete employee's profile image if it exists
            if (!empty($employee['profile_image'])) {
                $profileImagePath = ROOTPATH . 'public/uploads/employees/' . $employee['profile_image'];
                if (file_exists($profileImagePath)) {
                    unlink($profileImagePath);
                }
            }

            // Delete associated user and their profile image if exists
            if (!empty($employee['user_id'])) {
                $user = $this->userModel->find($employee['user_id']);

                if ($user) {
                    if (!empty($user['profile_image'])) {
                        $userImagePath = ROOTPATH . 'public/uploads/users/' . $user['profile_image'];
                        if (file_exists($userImagePath)) {
                            unlink($userImagePath);
                        }
                    }

                    $this->userModel->delete($user['user_id']);
                }
            }

            // Delete the employee record
            $this->employeeModel->delete($employeeId);

            // Delete reporting manager relationships
            $this->reportingManagerModel
                ->where('employee_id', $employeeId)
                ->orWhere('manager_id', $employeeId)
                ->delete();

            // Complete transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Transaction failed during deletion.');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Employee and related user data have been successfully deleted.'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while deleting the employee: ' . $e->getMessage()
            ]);
        }
    }

    public function getAttendance()
    {
        $employeeId = $this->request->getGet('employee_id');
        $month      = $this->request->getGet('month'); // 1–12
        $year       = $this->request->getGet('year');  // YYYY

        if (!$employeeId || !$month || !$year) {
            return $this->response->setJSON([
                'attendanceData' => [],
                'error'          => 'Invalid parameters.',
            ]);
        }

        $month = str_pad((int)$month, 2, '0', STR_PAD_LEFT);

        $attendanceModel = new \App\Models\AttendanceModel();
        $attendanceModel->useMonthlyTable($year, $month);

        $startDate = "{$year}-{$month}-01";
        $endDate   = date("Y-m-t", strtotime($startDate));

        $records = $attendanceModel->where('employee_id', $employeeId)
            ->where('punch_date >=', $startDate)
            ->where('punch_date <=', $endDate)
            ->orderBy('punch_in', 'ASC')
            ->findAll();

        $attendanceData = [];
        foreach ($records as $rec) {
            $day = date('j', strtotime($rec['punch_date']));

            if (!isset($attendanceData[$day])) {
                $attendanceData[$day] = [
                    'first_in'    => $rec['punch_in'],
                    'last_out'    => $rec['punch_out'],
                    'total_hours' => (float)($rec['duration'] ?? $rec['total_hours'] ?? 0)
                ];
            } else {
                if ($rec['punch_in'] && strtotime($rec['punch_in']) < strtotime($attendanceData[$day]['first_in'])) {
                    $attendanceData[$day]['first_in'] = $rec['punch_in'];
                }
                if ($rec['punch_out'] && strtotime($rec['punch_out']) > strtotime($attendanceData[$day]['last_out'])) {
                    $attendanceData[$day]['last_out'] = $rec['punch_out'];
                }
                $attendanceData[$day]['total_hours'] += (float)($rec['duration'] ?? $rec['total_hours'] ?? 0);
            }
        }

        // --- Auto assign status ---
        foreach ($attendanceData as $day => &$data) {
            $hours = $data['total_hours'];

            if ($hours < 4) {
                $data['status'] = 'Absent';
            } elseif ($hours >= 4 && $hours < 6) {
                $data['status'] = 'Half Day';
            } else {
                $data['status'] = 'Present';
            }
        }

        return view('admin/employee/attendance_calendar', [
            'attendanceData' => $attendanceData,
            'month'          => $month,
            'year'           => $year
        ]);
    }
}
