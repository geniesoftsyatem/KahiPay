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

        $fromDate = $data['searchArray']['from_date'];
        $toDate   = $data['searchArray']['to_date'];

        // Pagination setup
        $page       = (int) $this->request->getGet('page') ?: 1;
        $limit      = 20;
        $startLimit = ($page - 1) * $limit;

        // Build month period
        $period = new \DatePeriod(
            new \DateTime(date('Y-m-01', strtotime($fromDate))),
            new \DateInterval('P1M'),
            (new \DateTime(date('Y-m-01', strtotime($toDate))))->modify('+1 month')
        );

        $allResults  = [];
        $totalRecord = 0;

        // Loop each month table
        foreach ($period as $dt) {
            $tableName = "employee_attendance_" . $dt->format("Y_m");

            $db = \Config\Database::connect();
            if (!$db->tableExists($tableName)) {
                continue; // skip missing table
            }
            // Count
            $count = $this->attendanceModel
                ->setTable($tableName)
                ->where('employee_id', $employeeId)
                ->where('punch_date >=', $fromDate)
                ->where('punch_date <=', $toDate)
                ->countAllResults(false);

            $totalRecord += $count;

            // Fetch rows (get all, we'll slice for pagination later)
            if ($count > 0) {
                $rows = $this->attendanceModel
                    ->setTable($tableName)
                    ->where('employee_id', $employeeId)
                    ->where('punch_date >=', $fromDate)
                    ->where('punch_date <=', $toDate)
                    ->orderBy('punch_date', 'DESC')
                    ->findAll();

                $allResults = array_merge($allResults, $rows);
            }
        }

        // Sort combined results by punch_date DESC
        usort($allResults, function ($a, $b) {
            return strtotime($b['punch_date']) <=> strtotime($a['punch_date']);
        });

        // Apply pagination manually on merged results
        $pagedResults = array_slice($allResults, $startLimit, $limit);

            // Calculate total present and absent based on total hours per date
            $dateHours = [];
            foreach ($allResults as $row) {
                $date = $row['punch_date'];
                $hours = isset($row['total_hours']) ? (float)$row['total_hours'] : 0.0;
                if (!isset($dateHours[$date])) {
                    $dateHours[$date] = 0.0;
                }
                $dateHours[$date] += $hours;
            }

            $totalPresent = 0;
            $totalAbsent = 0;
            foreach ($dateHours as $date => $hours) {
                if ($hours > 1.0) {
                    $totalPresent++;
                } else {
                    $totalAbsent++;
                }
            }

        // Pagination data
        $data['reverse']    = $totalRecord - $startLimit;
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $limit);

        // Final results for the view
        $data['results'] = $pagedResults;

            // Pass present/absent counts to view
            $data['totalPresent'] = $totalPresent;
            $data['totalAbsent'] = $totalAbsent;

        return view('admin/attendance/index', $data);
    }


}
