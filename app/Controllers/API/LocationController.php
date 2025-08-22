<?php

namespace App\Controllers\API;

use App\Models\EmployeeModel;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\EmployeeLocationModel;

class LocationController extends BaseController
{
    use ResponseTrait;

    protected $employeeModel;
    protected $employeeLocationModel;

    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
        $this->employeeLocationModel = new EmployeeLocationModel();
    }

    /**
     * Store a new employee location
     * POST /api/employee-locations
     */
    public function create()
    {
        $rules = [
            'employee_id' => 'required',
            'latitude'    => 'required',
            'longitude'   => 'required',
            'timestamp'   => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $employeeId = $this->request->getVar('employee_id');
        $employee   = $this->employeeModel->find($employeeId);

        if (!$employee) {
            return $this->failNotFound('Employee not found');
        }

        $timestamp = $this->request->getVar('timestamp') ?? date('Y-m-d H:i:s');

        $data = [
            'employee_id'   => $employeeId,
            'latitude'      => $this->request->getVar('latitude'),
            'longitude'     => $this->request->getVar('longitude'),
            'accuracy'      => $this->request->getVar('accuracy'),
            'altitude'      => $this->request->getVar('altitude'),
            'speed'         => $this->request->getVar('speed'),
            'heading'       => $this->request->getVar('heading'),
            'timestamp'     => $timestamp,
            'ip_address'    => $this->request->getVar('ip_address'),
            'device_info'   => $this->request->getVar('device_info'),
            'online_status' => $employee['is_online'],
            'last_seen_at'  => $employee['last_active'],
        ];

        if ($this->employeeLocationModel->saveLocation($data)) {
            return $this->respondCreated([
                'success' => true,
                'message' => 'Location recorded successfully',
                'data'    => $data
            ]);
        } else {
            return $this->failServerError('Failed to record location');
        }
    }

    /**
     * Get last known location for an employee
     * GET /api/employee-locations/last/{employee_id}
     */
    public function getLastEmployeeLocation()
    {
        $employeeId = $this->request->getGet('employee_id');
        if (!$employeeId) {
            return $this->failValidationErrors(['employee_id' => 'Employee ID is required']);
        }

        // Validate employee exists
        $employee = $this->employeeModel->find($employeeId);
        if (!$employee) {
            return $this->failNotFound('Employee not found');
        }

        $location = $this->employeeLocationModel->getLastLocation($employeeId);

        return $this->respond([
            'success' => true,
            'employee' => $employee,
            'data'    => $location
        ]);
    }

    /**
     * Get all location details for a specific employee
     * GET /api/employee-locations?employee_id=1&from_date=2025-01-01&to_date=2025-06-30
     */
    public function getEmployeeLocations()
    {
        $employeeId = $this->request->getGet('employee_id');
        if (!$employeeId) {
            return $this->failValidationErrors(['employee_id' => 'Employee ID is required']);
        }

        // Validate employee exists
        $employee = $this->employeeModel->find($employeeId);
        if (!$employee) {
            return $this->failNotFound('Employee not found');
        }

        // Date filters (default = last 6 months)
        $fromDate = $this->request->getGet('from_date') ?? date('Y-m-01', strtotime('-5 months'));
        $toDate   = $this->request->getGet('to_date') ?? date('Y-m-d');

        $searchArray = [
            'employee_id' => $employeeId,
            'from_date'   => $fromDate,
            'to_date'     => $toDate
        ];

        // Fetch partitioned locations
        $locations = $this->employeeLocationModel->getEmployeeLocation($searchArray);

        return $this->respond([
            'success' => true,
            'employee' => [
                'id'   => $employee['employee_id'],
                'name' => $employee['first_name'] . ' ' . $employee['last_name'],
            ],
            'locations' => $locations,
            'count' => count($locations)
        ]);
    }
}
