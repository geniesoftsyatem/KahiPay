<?php

namespace App\Controllers\API;

use App\Models\EmployeeModel;
use App\Models\AttendanceModel;
use App\Libraries\TablesManager;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;

class AttendanceController extends BaseController
{
    use ResponseTrait;

    protected $tablesManager;
    protected $employeeModel;
    protected $attendanceModel;

    public function __construct()
    {
        $this->tablesManager   = new TablesManager();
        $this->employeeModel   = new EmployeeModel();
        $this->attendanceModel = new AttendanceModel();
    }

    /**
     * Punch In
     * POST /api/attendance/punch-in
     */
    public function punchIn()
    {
        $employeeId = $this->request->getVar('employee_id');

        if (empty($employeeId) || !is_numeric($employeeId)) {
            return $this->failValidationErrors('Employee ID is required and must be numeric');
        }

        $date = date('Y-m-d');
        $time = date('Y-m-d H:i:s');

        $tableName = $this->tablesManager->createMonthlyTableIfNotExists($date);
        $this->attendanceModel->setTable($tableName);

        // Check if there's already an active punch-in (no punch_out)
        $active = $this->attendanceModel
            ->where('employee_id', $employeeId)
            ->where('punch_date', $date)
            ->where('punch_out IS NULL')
            ->first();

        if ($active) {
            return $this->failResourceExists('You must punch out before punching in again.');
        }

        // Insert new punch-in
        $data = [
            'employee_id' => $employeeId,
            'punch_in'    => $time,
            'punch_date'  => $date,
        ];
        $this->attendanceModel->insert($data);

        // Mark employee online
        $this->employeeModel->update($employeeId, [
            'is_online'   => 1,
            'last_active' => $time,
        ]);

        return $this->respondCreated([
            'message' => 'Punched in successfully',
            'data'    => $data,
        ]);
    }

    /**
     * Punch Out
     * POST /api/attendance/punch-out
     */
    public function punchOut()
    {
        $employeeId = $this->request->getVar('employee_id');

        if (empty($employeeId) || !is_numeric($employeeId)) {
            return $this->failValidationErrors('Employee ID is required and must be numeric');
        }

        $date = date('Y-m-d');
        $time = date('Y-m-d H:i:s');

        $tableName = $this->tablesManager->createMonthlyTableIfNotExists($date);
        $this->attendanceModel->setTable($tableName);

        $attendance = $this->attendanceModel
            ->where('employee_id', $employeeId)
            ->where('punch_date', $date)
            ->where('punch_out IS NULL')
            ->orderBy('punch_in', 'DESC')
            ->first();

        if (!$attendance) {
            return $this->failNotFound('No active punch-in found for today');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Calculate worked hours
            $punchInTime = strtotime($attendance['punch_in']);
            $punchOutTime = strtotime($time);
            $hours = round(($punchOutTime - $punchInTime) / 3600, 2);

            // Determine status
            if ($hours >= 8.5) {
                $status = 'Present';
            } elseif ($hours >= 4) {
                $status = 'Half Day';
            } else {
                $status = 'Absent';
            }

            // Update punch-out with hours and status
            $this->attendanceModel->update($attendance['id'], [
                'punch_out'   => $time,
                'total_hours' => $hours,
            ]);

            // Update employee status to offline
            $this->employeeModel->update($employeeId, [
                'is_online'   => 0,
                'last_active' => $time,
            ]);

            // Update yearly summary
            $month = date('n');
            $summaryTable = $this->tablesManager->createYearlySummaryTableIfNotExists();

            $summary = $db->table($summaryTable)
                ->where('employee_id', $employeeId)
                ->where('month', $month)
                ->get()
                ->getRowArray();

            if ($summary) {
                $updateData = [
                    'attendance_date' => $date,
                    'total_hours'     => $summary['total_hours'] + $hours,
                    'status'          => $status,
                ];

                $db->table($summaryTable)
                    ->where('id', $summary['id'])
                    ->update($updateData);
            } else {
                $insertData = [
                    'employee_id'     => $employeeId,
                    'month'           => $month,
                    'attendance_date' => $date,
                    'total_hours'     => $hours,
                    'status'          => $status,
                ];
                $db->table($summaryTable)->insert($insertData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failServerError('Failed to punch out. Please try again.');
            }

            return $this->respond([
                'status' => 200,
                'message' => 'Punch-out recorded successfully',
                'data' => [
                    'employee_id'  => $employeeId,
                    'date'         => $date,
                    'punch_out'    => $time,
                    'worked_hours' => $hours,
                    'status'       => $status,
                ]
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->failServerError('An unexpected error occurred during punch-out.');
        }
    }

    /**
     * Get today's attendance summary (first punch in, last punch out, total hours)
     * GET /api/attendance/status?employee_id=123
     */
    public function getAttendanceStatus()
    {
        $employeeId = $this->request->getGet('employee_id');

        if (empty($employeeId) || !is_numeric($employeeId)) {
            return $this->failValidationErrors('Employee ID is required and must be numeric');
        }

        $date = date('Y-m-d');

        $tableName = $this->tablesManager->createMonthlyTableIfNotExists($date);
        $this->attendanceModel->setTable($tableName);

        // Get all punches for today
        $records = $this->attendanceModel
            ->where('employee_id', $employeeId)
            ->where('punch_date', $date)
            ->orderBy('punch_in', 'ASC')
            ->findAll();

        if (empty($records)) {
            return $this->respond([
                'status'  => 200,
                'message' => 'No attendance records for today',
                'data'    => [
                    'employee_id'    => $employeeId,
                    'first_punch_in' => null,
                    'last_punch_out' => null,
                    'total_hours'    => 0,
                ],
            ]);
        }

        $employee = $this->employeeModel->find($employeeId);

        // First punch in = earliest punch_in
        $firstPunchIn = $records[0]['punch_in'];

        // Last punch out = latest punch_out (check all records, avoid nulls)
        $lastPunchOut = null;
        foreach ($records as $rec) {
            if (!empty($rec['punch_out'])) {
                $lastPunchOut = $rec['punch_out'];
            }
        }

        // Total hours worked today
        $totalHours = array_sum(array_column($records, 'total_hours'));

        return $this->respond([
            'status'  => 200,
            'message' => 'Attendance summary retrieved',
            'data'    => [
                'employee_id'    => $employeeId,
                'is_online'      => $employee['is_online'] ?? 0,
                'first_punch_in' => $firstPunchIn,
                'last_punch_out' => $lastPunchOut,
                'total_hours'    => $totalHours,
                'records'        => $records,
            ],
        ]);
    }

    public function getAttendance()
    {
        $employeeId = $this->request->getGet('employee_id');
        $month      = $this->request->getGet('month');
        $year       = $this->request->getGet('year');

        if (!$employeeId || !$month || !$year) {
            return $this->failValidationErrors("employee_id, month, and year are required.");
        }

        // Validate employee exists
        $employee = $this->employeeModel->find($employeeId);
        if (!$employee) {
            return $this->failNotFound("Employee not found.");
        }

        // Get monthly table name (same format as in TablesManager)
        $tableName = "employee_attendance_summary_" . $year;
        // Ensure table exists before querying
        $db = \Config\Database::connect();
        if (!$db->tableExists($tableName)) {
            return $this->respond([
                'status'   => true,
                'employee' => [
                    'employee_id' => $employee['employee_id'],
                    'name'        => $employee['first_name'] . ' ' . $employee['last_name'],
                    'designation' => $employee['designation'],
                ],
                'results'  => [],
                'message'  => "No attendance records found for $month/$year."
            ]);
        }

        // Get start and end date of the month
        $startDate = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
        $endDate   = date("Y-m-t", strtotime($startDate));

        // Query the dynamic table
        $attendanceRecords = $db->table($tableName)
            ->where('employee_id', $employeeId)
            ->where('attendance_date >=', $startDate)
            ->where('attendance_date <=', $endDate)
            ->orderBy('attendance_date', 'asc')
            ->get()
            ->getResultArray();

        return $this->respond([
            'status'   => true,
            'employee' => [
                'employee_id' => $employee['employee_id'],
                'name'        => $employee['first_name'] . ' ' . $employee['last_name'],
                'designation' => $employee['designation'],
            ],
            'results' => $attendanceRecords,
        ]);
    }
/**
 * Get attendance summary (total present, absent, working hours) within date range (multi-year supported)
 * GET /api/attendance/summary?employee_id=123&start_date=2024-12-15&end_date=2025-01-15
 */
public function getAttendanceSummary()
{
    $employeeId = $this->request->getGet('employee_id');
    $startDate  = $this->request->getGet('start_date');
    $endDate    = $this->request->getGet('end_date');

    if (!$employeeId || !$startDate || !$endDate) {
        return $this->failValidationErrors("employee_id, start_date, and end_date are required.");
    }

    // Ensure end_date is not in the future
    $today = date("Y-m-d");
    if ($endDate > $today) {
        return $this->failValidationErrors("end_date cannot be greater than today's date ($today).");
    }

    // Ensure start_date <= end_date
    if ($startDate > $endDate) {
        return $this->failValidationErrors("start_date cannot be greater than end_date.");
    }

    // Validate employee exists
    $employee = $this->employeeModel->find($employeeId);
    if (!$employee) {
        return $this->failNotFound("Employee not found.");
    }

    $db = \Config\Database::connect();

    // Determine year range
    $startYear = (int) date("Y", strtotime($startDate));
    $endYear   = (int) date("Y", strtotime($endDate));

    // Initialize counters
    $presentDays = 0;
    $absentDays  = 0;
    $totalHours  = 0;

    // Loop through each year in the range
    for ($year = $startYear; $year <= $endYear; $year++) {
        $tableName = "employee_attendance_summary_" . $year;

        if (!$db->tableExists($tableName)) {
            continue; // skip missing tables gracefully
        }

        // Adjust date range for current year
        $yearStart = ($year === $startYear) ? $startDate : "$year-01-01";
        $yearEnd   = ($year === $endYear) ? $endDate : "$year-12-31";

        // Fetch attendance records for this year
        $records = $db->table($tableName)
            ->where('employee_id', $employeeId)
            ->where('attendance_date >=', $yearStart)
            ->where('attendance_date <=', $yearEnd)
            ->get()
            ->getResultArray();

        // Aggregate data
        foreach ($records as $record) {
            $status = strtolower($record['status']);

            if (in_array($status, ['present', 'late'])) {
                $presentDays += 1;
            } elseif ($status === 'half day') {
                $presentDays += 0.5;
            } elseif ($status === 'absent') {
                $absentDays += 1;
            }

            $totalHours += (float) $record['total_hours'];
        }
    }

    return $this->respond([
        'status'   => true,
        'employee' => [
            'employee_id' => $employee['employee_id'],
            'name'        => $employee['first_name'] . ' ' . $employee['last_name'],
            'designation' => $employee['designation'],
        ],
        'results' => [
            'total_present_days'  => $presentDays,
            'total_absent_days'   => $absentDays,
            'total_working_hours' => $totalHours
        ]
    ]);
}

}
