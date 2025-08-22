<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\TablesManager;

class AttendanceModel extends Model
{
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'employee_id',
        'punch_in',
        'punch_out',
        'total_hours',
        'punch_date',
        'status'
    ];

    protected $tablesManager;

    public function __construct()
    {
        parent::__construct();
        $this->tablesManager = new TablesManager();
    }

    /**
     * Switch to monthly attendance table dynamically
     */
    public function useMonthlyTable(string $year = null, string $month = null)
    {
        $year  = $year ?? date('Y');
        $month = $month ?? date('m');

        $tableName = "employee_attendance_{$year}_{$month}";
        $this->tablesManager->createMonthlyTableIfNotExists();
        $this->setTable($tableName);

        return $this;
    }

    /**
     * Switch to yearly summary table dynamically
     */
    public function useSummaryTable(string $year = null)
    {
        $year = $year ?? date('Y');

        $tableName = $this->tablesManager->createYearlySummaryTableIfNotExists($year);
        $this->setTable($tableName);

        return $this;
    }

    /**
     * Get employee daily record
     */
    public function getDailyRecord(int $employeeId, string $date)
    {
        $this->useMonthlyTable();

        return $this->where('employee_id', $employeeId)
            ->where('punch_date', $date)
            ->first();
    }

    /**
     * Get monthly attendance of employee
     */
    public function getEmployeeMonthly(int $employeeId, string $year = null, string $month = null)
    {
        $this->useMonthlyTable($year, $month);

        return $this->where('employee_id', $employeeId)
            ->orderBy('punch_date', 'ASC')
            ->findAll();
    }

    /**
     * Get monthly summary for all employees
     */
    public function getMonthlySummary(int $month, string $year = null)
    {
        $this->useSummaryTable($year);

        return $this->where('month', $month)
            ->orderBy('employee_id', 'ASC')
            ->findAll();
    }

    /**
     * Get employee yearly summary
     */
    public function getEmployeeSummary(int $employeeId, string $year = null)
    {
        $this->useSummaryTable($year);

        return $this->where('employee_id', $employeeId)
            ->orderBy('month', 'ASC')
            ->findAll();
    }
}
