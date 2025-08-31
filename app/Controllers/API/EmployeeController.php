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

    /**
     * Register new employee or get if exists
     */
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
                    'message' => 'Employee already exists'
                ]
            ]);
        }

        // Insert new employee
        $newId = $this->employeeModel->insert([
            'employee_code' => $username,
            'email'    => $email,
            'phone'   => $mobile,
            'geo_tracking' => 1 // default enabled
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

        $onlineStatus = (strtolower($isOnline) === 'true' || $isOnline === '1') ? 1 : 0;

        $updateData = [
            'is_online' => $onlineStatus,
            'last_active' => date('Y-m-d H:i:s')
        ];

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
     * Get employee geo-tracking status
     */
    public function getGeoTrackingStatus()
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
            'message' => 'Employee geo-tracking status retrieved successfully',
            'data' => [
                'employee_id' => $employeeId,
                'geo_tracking' => $employee['geo_tracking'] == 1 ? true : false
            ]
        ]);
    }

    /**
     * Update employee geo-tracking status
     */
    public function updateGeoTrackingStatus()
    {
        $employeeId = $this->request->getVar('employee_id');
        $geoTracking = $this->request->getVar('geo_tracking');

        if (empty($employeeId) || !is_numeric($employeeId)) {
            return $this->failValidationErrors('Employee ID is required and must be numeric');
        }

        if (!isset($geoTracking)) {
            return $this->failValidationErrors('Geo-tracking status (true/false) is required');
        }

        $geoTrackingStatus = (strtolower($geoTracking) === 'true' || $geoTracking === '1') ? 1 : 0;

        try {
            $updated = $this->employeeModel->update($employeeId, ['geo_tracking' => $geoTrackingStatus]);

            if (!$updated) {
                return $this->fail('Failed to update geo-tracking status', 500);
            }
        } catch (\Exception $e) {
            return $this->failServerError('Exception: ' . $e->getMessage());
        }

        return $this->respond([
            'status' => 200,
            'error' => null,
            'message' => 'Employee geo-tracking status updated successfully',
            'data' => [
                'employee_id' => $employeeId,
                'geo_tracking' => $geoTrackingStatus
            ]
        ]);
    }

    /**
     * Get employee profile details
     */
    public function getProfileDetails()
    {
        $employeeId = $this->request->getGet('employee_id');

        $employee = $this->employeeModel->find($employeeId);

        if (!$employee) {
            return $this->failNotFound("Employee not found.");
        }

        $company = $this->companyModel->find($employee['company_id']);

        if (!$company) {
            return $this->failNotFound("Company not found for this employee.");
        }

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
                'geo_tracking'    => $employee['geo_tracking'],
                'created_at'      => $employee['created_at'],
                'updated_at'      => $employee['updated_at'],
            ]
        ];

        return $this->respond($response);
    }

    /**
     * Get employee details by code
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
            e.geo_tracking,
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
     * Get employee ID card details
     */
    public function getEmployeeIdCard()
    {
        $employeeId = $this->request->getGet('employee_id');
        $employee = $this->employeeModel->find($employeeId);

        if (!$employee) {
            return $this->failNotFound("Employee not found.");
        }

        $company = $this->companyModel->find($employee['company_id']);
        if (!$company) {
            return $this->failNotFound("Company information not found for this employee.");
        }

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
                'company_address' => $company['address'],
                'geo_tracking' => $employee['geo_tracking']
            ]
        ];

        return $this->respond($response);
    }

    /**
     * Get juniors (direct reports)
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
                employees.geo_tracking,
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
