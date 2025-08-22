<?php

namespace App\Controllers\API;

use App\Models\CompanyModel;
use App\Models\EmployeeModel;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;

class EmployeeController extends BaseController
{
    use ResponseTrait;

    protected $db;
    protected $companyModel;
    protected $employeeModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->companyModel = new CompanyModel();
        $this->employeeModel = new EmployeeModel();
    }

    public function registerOrGetEmployee()
    {
        $rules = [
            'username' => 'required',
            'email'    => 'required|valid_email',
            'mobile'   => 'required|numeric|min_length[10]|max_length[15]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $postData = $this->request->getVar();

        $username = $postData['username'] ?? "";
        $email    = $postData['email'] ?? "";
        $mobile   = $postData['mobile'] ?? "";

        // Check if employee already exists
        $employee = $this->employeeModel->where([
            'employee_code' => $username,
            'email'    => $email,
            'phone'   => $mobile
        ])->first();

        if ($employee) {
            return $this->respond([
                'success' => true,
                'results' => [
                    'employee_id' => $employee['employee_id'],
                    'message' => 'roEmployee already exists'
                ]
            ]);
        }

        // Insert new employee
        $newId = $this->employeeModel->insert([
            'employee_code' => $username,
            'email'    => $email,
            'phone'   => $mobile
        ]);

        if ($newId) {
            return $this->respondCreated([
                'success' => true,
                'results' => [
                    'employee_id' => $newId,
                    'message' => 'New employee created'
                ]
            ]);
        }

        return $this->failServerError('Something went wrong. Please try again later.');
    }

    /**
     * Get employee online status
     * GET /api/employees/status?employee_id=123
     */
    public function getEmployeeOnlineStatus()
    {
        $employeeId = $this->request->getGet('employee_id');

        if (empty($employeeId) || !is_numeric($employeeId)) {
            return $this->failValidationErrors('Employee ID is required and must be numeric');
        }

        $employee = $this->employeeModel->find($employeeId);

        if (!$employee) {
            return $this->failNotFound('Employee not found');
        }

        return $this->respond([
            'status' => 200,
            'error' => null,
            'message' => 'Employee online status retrieved successfully',
            'data' => [
                'employee_id' => $employeeId,
                'is_online' => $employee['is_online'],
                'last_active' => $employee['last_active']
            ]
        ]);
    }

    /**
     * Update employee online status
     * POST /api/employees/status
     */
    public function updateEmployeeOnlineStatus()
    {
        $employeeId = $this->request->getVar('employee_id');
        $isOnline = $this->request->getVar('is_online');

        if (empty($employeeId) || !is_numeric($employeeId)) {
            return $this->failValidationErrors('Employee ID is required and must be numeric');
        }

        if (!isset($isOnline)) {
            return $this->failValidationErrors('Online status (true/false) is required');
        }

        // Convert string "true"/"false" to 1/0
        $onlineStatus = (strtolower($isOnline) === 'true' || $isOnline === '1') ? 1 : 0;

        // Update payload
        $updateData = [
            'is_online' => $onlineStatus,
            'last_active' => date('Y-m-d H:i:s')
        ];

        // Perform update
        try {
            $updated = $this->employeeModel->update($employeeId, $updateData);

            if (!$updated) {
                return $this->fail('Failed to update status', 500);
            }
        } catch (\Exception $e) {
            return $this->failServerError('Exception: ' . $e->getMessage());
        }

        return $this->respond([
            'status' => 200,
            'error' => null,
            'message' => 'Employee online status updated successfully',
            'data' => [
                'employee_id' => $employeeId,
                'is_online' => $onlineStatus,
                'last_active' => $updateData['last_active']
            ]
        ]);
    }

    /**
     * Get employee profile details
     * GET /api/employee-profile
     */

    public function getProfileDetails()
    {
        // Get employee ID from the request
        $employeeId = $this->request->getGet('employee_id');

        // Fetch employee details
        $employee = $this->employeeModel->find($employeeId);

        if (!$employee) {
            return $this->failNotFound("Employee not found.");
        }

        // Fetch company details
        $company = $this->companyModel->find($employee['company_id']);

        if (!$company) {
            return $this->failNotFound("Company not found for this employee.");
        }

        // Prepare response
        $response = [
            'status' => true,
            'base_url'  => base_url() . 'uploads/employees/',
            'company_base_url'  => base_url() . 'uploads/companies/',
            'results' => [
                'employee_id'     => $employee['employee_id'],
                'employee_code'   => $employee['employee_code'],
                'username'        => $employee['first_name'] . ' ' . $employee['last_name'],
                'email'           => $employee['email'],
                'phone'           => $employee['phone'],
                'address'         => $employee['address'],
                'designation'     => $employee['designation'],
                'profile_image'   => $employee['profile_image'],
                'company_name'    => $company['company_name'],
                'company_address' => $company['address'],
                'company_logo'    => $company['logo'],
                'created_at'      => $employee['created_at'],
                'updated_at'      => $employee['updated_at'],
            ]
        ];

        return $this->respond($response);
    }

    /**
     * Get employee details
     * GET /api/employees/:code/details
     */
    public function getEmployeeByCode()
    {
        $employeeCode = $this->request->getGet('employee_code');

        if (empty($employeeCode)) {
            return $this->failValidationErrors('Employee code is required');
        }

        $builder = $this->db->table('employees e');
        $builder->select('
            e.employee_id,
            e.employee_code,
            e.first_name,
            e.last_name,
            e.profile_image,
            e.designation,
            e.phone,
            e.email,
            e.created_at,
            e.updated_at,
            c.company_name,
            c.address as company_address,
            c.logo as company_logo
        ');

        $builder->join('companies c', 'c.company_id = e.company_id', 'left');
        $builder->where('e.employee_code', $employeeCode);

        $employee = $builder->get()->getRowArray();

        if (!$employee) {
            return $this->failNotFound('Employee not found');
        }

        return $this->respond([
            'status'    => 200,
            'error'     => null,
            'base_url'  => base_url() . 'uploads/employees/',
            'company_base_url'  => base_url() . 'uploads/companies/',
            'messages'  => 'Employee details retrieved successfully',
            'data'      => $employee
        ]);
    }

    /**
     * Get employee details
     * GET /api/employee-id-card
     */
    public function getEmployeeIdCard()
    {
        $employeeId = $this->request->getGet('employee_id');
        $employee = $this->employeeModel->find($employeeId);

        if (!$employee) {
            return $this->failNotFound("Employee not found.");
        }

        // Fetch the company using the employee's company_id
        $company = $this->companyModel->find($employee['company_id']);
        if (!$company) {
            return $this->failNotFound("Company information not found for this employee.");
        }

        // Generate employee image URL
        $imageUrl = null;
        if (!empty($employee['profile_image'])) {
            $imageUrl = base_url('uploads/employees/' . $employee['profile_image']);
        }

        $response = [
            'status' => true,
            'results' => [
                'employee_name' => $employee['first_name'] . ' ' . $employee['last_name'],
                'designation' => $employee['designation'],
                'profile_image_url' => $imageUrl,
                'company_name' => $company['company_name'],
                'company_address' => $company['address']
            ]
        ];

        return $this->respond($response);
    }

    /**
     * Get direct reporting employees (juniors) for a given manager.
     * Endpoint: GET /api/employees/juniors?employee_id=123
     */
    public function getJuniorsEmployees()
    {
        $employeeId = $this->request->getGet('employee_id');

        if (!$employeeId || !is_numeric($employeeId)) {
            return $this->failValidationErrors('Invalid employee ID.');
        }

        $juniors = $this->employeeModel
            ->select('
                employees.employee_id,
                employees.employee_code,
                employees.first_name,
                employees.last_name,
                employees.phone,
                employees.email,
                employees.designation,
                companies.company_name,
                companies.address AS company_address
            ')
            ->join('reporting_managers', 'reporting_managers.employee_id = employees.employee_id')
            ->join('companies', 'companies.company_id = employees.company_id', 'left')
            ->where('reporting_managers.manager_id', $employeeId)
            ->findAll();

        if (empty($juniors)) {
            return $this->respond([
                'success' => true,
                'results' => [],
                'message' => 'No juniors found for this employee.'
            ]);
        }

        return $this->respond([
            'success' => true,
            'results' => $juniors
        ]);
    }
}
