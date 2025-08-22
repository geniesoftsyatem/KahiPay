<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\EmployeeModel;

class ReportingManagerModel extends Model
{
    protected $table = 'reporting_managers';
    protected $primaryKey = 'id';
    protected $allowedFields = ['employee_id', 'manager_id', 'created_at'];

    public function getReportingManagers(array $searchArray = [])
    {
        $employeeModel = new EmployeeModel();

        // If the keyword 'all' is passed, return all employees (optionally filtered by company)
        if (!empty($searchArray['search']) && strtolower($searchArray['search']) === 'all') {
            $builder = $employeeModel
                ->select('employees.*, companies.company_name, companies.address as company_address')
                ->join('companies', 'employees.company_id = companies.company_id', 'left');

            // Optional: filter by company if companyId is passed
            if (!empty($searchArray['companyId'])) {
                $builder->where('employees.company_id', $searchArray['companyId']);
            }

            // Optional: exclude a specific ID
            if (!empty($searchArray['employeeId'])) {
                $builder->where('employees.employee_id !=', $searchArray['employeeId']);
            }

            return $builder->findAll();
        }

        // If companyId is passed, filter employees under that company
        if (!empty($searchArray['companyId'])) {
            $employeeIds = $employeeModel
                ->where('company_id', $searchArray['companyId'])
                ->findColumn('employee_id');

            if (empty($employeeIds)) {
                return [];
            }

            $managerIds = $this
                ->select('manager_id')
                ->whereIn('employee_id', $employeeIds)
                ->distinct()
                ->findColumn('manager_id');
        } else {
            // No company filter, fetch all distinct manager IDs
            $managerIds = $this
                ->select('manager_id')
                ->distinct()
                ->findColumn('manager_id');
        }

        if (empty($managerIds)) {
            return [];
        }

        // Build query to fetch manager details
        $builder = $employeeModel
            ->select('employees.*, companies.company_name, companies.address as company_address')
            ->join('companies', 'employees.company_id = companies.company_id', 'left')
            ->whereIn('employees.employee_id', $managerIds);

        // Optional employeeId filter
        if (!empty($searchArray['employeeId'])) {
            $builder->where('employees.employee_id !=', $searchArray['employeeId']);
        }

        // Optional: if companyId is passed, keep filtering manager records to that company
        if (!empty($searchArray['companyId'])) {
            $builder->where('employees.company_id', $searchArray['companyId']);
        }

        return $builder->findAll();
    }

    public function getManager($employeeId)
    {
        return $this->db->table('reporting_managers rm')
            ->select('e.*')
            ->join('employees e', 'e.employee_id = rm.manager_id')
            ->where('rm.employee_id', $employeeId)
            ->get()
            ->getRow();
    }

    public function getTeamMembers($managerId)
    {
        return $this->db->table('reporting_managers rm')
            ->select('e.*')
            ->join('employees e', 'e.employee_id = rm.employee_id')
            ->where('rm.manager_id', $managerId)
            ->get()
            ->getResult();
    }

    public function getReportingManagersByCompany($companyId)
    {
        if (!$companyId) {
            return [];
        }

        $employeeModel = new EmployeeModel();

        // Step 1: Get all employee_ids under the company
        $employeeIds = $employeeModel
            ->where('company_id', $companyId)
            ->findColumn('employee_id');

        if (empty($employeeIds)) {
            return [];
        }

        // Step 2: Get distinct manager_ids who manage those employees
        $managerIds = $this
            ->select('manager_id')
            ->whereIn('employee_id', $employeeIds)
            ->distinct()
            ->findColumn('manager_id');

        if (empty($managerIds)) {
            return [];
        }

        // Step 3: Fetch manager details from employees table
        return $employeeModel
            ->select('employees.*, companies.company_name, companies.address as company_address')
            ->join('companies', 'employees.company_id = companies.company_id', 'left')
            ->where('employees.company_id', $companyId)
            ->whereIn('employees.employee_id', $managerIds)
            ->findAll();
    }
}
