<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'employee_id';
    protected $allowedFields = [
        'company_id',
        'user_id',
        'employee_code',
        'first_name',
        'last_name',
        'phone',
        'email',
        'dob',
        'gender',
        'designation',
        'department',
        'address',
        'joining_date',
        'profile_image',
        'status',
        'is_online',
        'last_active',
        'created_by',
        'created_at',
        'updated_at'
    ];

    /**
     * Method to get employee records with optional search, pagination, and count
     *
     * @param array $searchArray The search filters
     * @param int|string $offset The offset for pagination
     * @param int|string $limit The limit for pagination
     * @param bool $countOnly Whether to return count only or full records
     * @return array|int Returns either the list of employees or count of records
     */
    public function getEmployees($searchArray = [], $offset = '', $limit = '', $countOnly = false)
    {
        $builder = $this->db->table($this->table . ' e');

        if ($countOnly) {
            $builder->select("COUNT(e.{$this->primaryKey}) as total_count");
        } else {
            $builder->select('e.*, CONCAT(m.first_name, " ", m.last_name) as manager_name, c.company_name');
        }

        // Join with companies to get company name
        $builder->join('companies c', 'c.company_id = e.company_id', 'left');

        // Join with reporting_managers to get manager_id
        $builder->join('reporting_managers rm', 'rm.employee_id = e.employee_id', 'left');

        // Join with employees again to get manager details
        $builder->join('employees m', 'rm.manager_id = m.employee_id', 'left');

        // Search by text (name, email, phone)
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like("CONCAT(e.first_name, ' ', e.last_name)", $searchTerm)
                ->orLike("e.email", $searchTerm)
                ->orLike("e.phone", $searchTerm)
                ->groupEnd();
        }

        // companies Filter
        if (!empty($searchArray['company_id'])) {
            $builder->where("e.company_id", (int)$searchArray['company_id']);
        }

        // Manager Filter
        if (!empty($searchArray['manager'])) {
            $builder->where("rm.manager_id", (int)$searchArray['manager']);
        }

        // Status Filter
        if (isset($searchArray['status']) && $searchArray['status'] !== '') {
            $builder->where("e.status", $searchArray['status']);
        }

        // Order by employee_id
        $builder->orderBy("e.{$this->primaryKey}", 'DESC');

        // Pagination
        if (!empty($limit) && !empty($offset)) {
            $builder->limit($limit, $offset);
        }

        // Execute the query and get the results
        $query = $builder->get();

        // Return the count if requested, otherwise return the results
        if ($countOnly) {
            return $query->getRow()->total_count;
        }

        return $query->getResult();
    }

    public function getManagerCandidates(array $searchArray = [])
    {
        $builder = $this->select('employee_id, first_name, last_name, email, designation, department')
            ->orderBy('first_name', 'ASC');

        if (!empty($searchArray['employeeId'])) {
            $builder->where('employee_id !=', $searchArray['employeeId']);
        }

        if (!empty($searchArray['companyId'])) {
            $builder->where('company_id', $searchArray['companyId']);
        }

        return $builder->findAll();
    }
}
