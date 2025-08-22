<?php

namespace App\Controllers;

use App\Models\CompanyModel;
use App\Libraries\Pagination;
use App\Models\EmployeeModel;
use App\Controllers\BaseController;
use App\Models\EmployeeLocationModel;
use App\Models\ReportingManagerModel;

class TrackingController extends BaseController
{
    protected $db;
    protected $session;
    protected $companyModel;
    protected $employeeModel;
    protected $reportingManagerModel;
    protected $employeeLocationModel;

    public function __construct()
    {
        $this->session = session();
        $this->db = \Config\Database::connect();
        $this->companyModel = new CompanyModel();
        $this->employeeModel = new EmployeeModel();
        $this->reportingManagerModel = new ReportingManagerModel();
        $this->employeeLocationModel = new EmployeeLocationModel();
    }

    public function index()
    {
        set_title('Track employees | ' . SITE_NAME);

        $companyId  = session('company_id');
        $userType   = session('user_type');

        $data = [
            'action'       => "employee-locations",
            'startLimit'   => 0,
            'reverse'      => 0,
            'pagination'   => '',
            'results'      => [],
            'searchArray'  => [],
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
        $data['locations'] = $this->employeeLocationModel->getLatestEmployeeLocations();

        $Limit = 10;
        $page = (int) $this->request->getGet('page') ?: 1;

        // Get total records
        $totalRecord = $this->employeeModel->getEmployees($data['searchArray'], '', '', true);
        $startLimit = ($page - 1) * $Limit;

        $data['startLimit'] = $startLimit;
        $data['reverse'] = $totalRecord - $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        // Fetch paginated results
        $data['results'] = $this->employeeModel->getEmployees($data['searchArray'], $startLimit, $Limit);

        return view('admin/tracking/index', $data);
    }

    public function getEmployeeLocation($employeeId)
    {
        set_title('Employee Locations | ' . SITE_NAME);

        // Get employee details
        $employee = $this->employeeModel->find($employeeId);
        if (!$employee) {
            return redirect()->to('employee-locations')->with('error', 'Employee not found');
        }

        $data = [
            'action' => "employee-locations/view/" . $employeeId,
            'pageTitle' => "Employee Locations",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'txtsearch' => '',
            'searchArray' => [],
            'employee' => $employee // Add employee data to the view
        ];

        $customPagination = new Pagination();

        // Collect search criteria
        $searchFields = $this->request->getGet();
        foreach ($searchFields as $field => $searchValue) {
            $data['searchArray'][$field] = $searchValue;
        }

        // Always include employee_id
        $data['searchArray']['employee_id'] = $employeeId;

        if (empty($data['searchArray']['from_date']) && empty($data['searchArray']['to_date'])) {
            $data['searchArray']['from_date'] = date('Y-m-01', strtotime('-5 months'));
            $data['searchArray']['to_date']   = date('Y-m-d');
        }

        // Pagination
        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 20;

        $totalRecord = $this->employeeLocationModel->getEmployeeLocation($data['searchArray'], '', '', true);
        $startLimit = ($page - 1) * $Limit;

        $data['reverse'] = $totalRecord - $startLimit;
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        // Fetch results
        $data['results'] = $this->employeeLocationModel->getEmployeeLocation($data['searchArray'], $startLimit, $Limit);

        return view('admin/tracking/preview', $data);
    }

    public function delete()
    {
        $locationId = $this->request->getPost('location_id');

        if (empty($locationId) || !is_numeric($locationId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid location ID'
            ]);
        }

        $found = false;

        // Search last 12 months tables for safety
        $tables = $this->employeeLocationModel->getMonthlyTables(
            date('Y-m-01', strtotime('-12 months')),
            date('Y-m-d')
        );

        foreach ($tables as $table) {
            if ($this->db->tableExists($table)) {
                $location = $this->db->table($table)->where('location_id', $locationId)->get()->getRow();
                if ($location) {
                    $this->db->table($table)->where('location_id', $locationId)->delete();
                    $found = true;
                    break;
                }
            }
        }

        if ($found) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Employee location deleted successfully.'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Employee location not found'
        ]);
    }
}
