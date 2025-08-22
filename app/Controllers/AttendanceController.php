<?php

namespace App\Controllers;


use Config\Database;
use App\Models\EmployeeModel;
use App\Libraries\Pagination;
use App\Models\AttendanceModel;
use App\Libraries\TablesManager;

class AttendanceController extends BaseController
{
    protected $tablesManager;
    protected $employeeModel;
    protected $attendanceModel;

    public function __construct()
    {
        $this->tablesManager   = new TablesManager();
        $this->employeeModel   = new EmployeeModel();
        $this->attendanceModel = new AttendanceModel();
    }

    public function getEmployeeAttendance($employeeId)
    {
        set_title('Employee Attendance | ' . SITE_NAME);

        // Get employee details
        $employee = $this->employeeModel->find($employeeId);
        if (!$employee) {
            return redirect()->to('employee-attendance')->with('error', 'Employee not found');
        }

        $data = [
            'action'      => "employee-attendance/view/" . $employeeId,
            'pageTitle'   => "Employee Attendance",
            'results'     => [],
            'pagination'  => '',
            'startLimit'  => 0,
            'reverse'     => 0,
            'txtsearch'   => '',
            'searchArray' => [],
            'employee'    => $employee,
        ];

        $customPagination = new Pagination();

        // Collect search criteria
        $searchFields = $this->request->getGet();
        foreach ($searchFields as $field => $searchValue) {
            $data['searchArray'][$field] = $searchValue;
        }

        // Always include employee_id
        $data['searchArray']['employee_id'] = $employeeId;

        // Default date range (last 5 months)
        if (empty($data['searchArray']['from_date']) && empty($data['searchArray']['to_date'])) {
            $data['searchArray']['from_date'] = date('Y-m-01', strtotime('-5 months'));
            $data['searchArray']['to_date']   = date('Y-m-d');
        }

        // Pagination setup
        $page  = (int) $this->request->getGet('page') ?: 1;
        $limit = 20;
        $startLimit = ($page - 1) * $limit;

        // Total count
        $totalRecord = $this->attendanceModel
            ->useMonthlyTable() // switch table dynamically
            ->where('employee_id', $employeeId)
            ->where('punch_date >=', $data['searchArray']['from_date'])
            ->where('punch_date <=', $data['searchArray']['to_date'])
            ->countAllResults(false); // don't reset query

        // Pagination
        $data['reverse']    = $totalRecord - $startLimit;
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $limit);

        // Fetch paginated results
        $data['results'] = $this->attendanceModel
            ->useMonthlyTable()
            ->where('employee_id', $employeeId)
            ->where('punch_date >=', $data['searchArray']['from_date'])
            ->where('punch_date <=', $data['searchArray']['to_date'])
            ->orderBy('punch_date', 'DESC')
            ->findAll($limit, $startLimit);

        return view('admin/attendance/index', $data);
    }
}
