<?php

namespace App\Models;

use CodeIgniter\Model;

class RequestLetterModel extends Model
{
    protected $table = 'request_letters';
    protected $primaryKey = 'request_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'employee_id',
        'title',
        'description',
        'images',
        'created_at',
        'updated_at'
    ];

    /**
     * Get employee request letters with company and reporting manager details
     *
     * @param array $searchArray Filters like name, email, title, company, manager, etc.
     * @param int|string $offset Pagination offset
     * @param int|string $limit Pagination limit
     * @param bool $countOnly Whether to return count only
     * @return array|int
     */
    public function getRequestLetters($searchArray = [], $offset = '', $limit = '', $countOnly = false)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table . ' rl');

        if ($countOnly) {
            $builder->select('COUNT(DISTINCT rl.' . $this->primaryKey . ') as total_count');
        } else {
            $builder->select('
            rl.*,
            CONCAT(emp.first_name, " ", emp.last_name) AS employee_name,
            c.company_name,
            CONCAT(mgr.first_name, " ", mgr.last_name) AS reporting_manager_name
        ');
        }

        // Join with employees (request creator)
        $builder->join('employees emp', 'emp.employee_id = rl.employee_id', 'left');

        // Join with companies
        $builder->join('companies c', 'c.company_id = emp.company_id', 'left');

        // Join with reporting_managers to find manager_id for this employee
        $builder->join('reporting_managers rm', 'rm.employee_id = emp.employee_id', 'left');

        // Join with employees table again to get manager details
        $builder->join('employees mgr', 'rm.manager_id = mgr.employee_id', 'left');

        // Search filter
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('CONCAT(emp.first_name, " ", emp.last_name)', $searchTerm)
                ->orLike('emp.email', $searchTerm)
                ->orLike('emp.phone', $searchTerm)
                ->orLike('rl.title', $searchTerm)
                ->orLike('rl.description', $searchTerm)
                ->orLike('c.company_name', $searchTerm)
                ->orLike('CONCAT(mgr.first_name, " ", mgr.last_name)', $searchTerm)
                ->groupEnd();
        }

        // Filter by company
        if (!empty($searchArray['company_id'])) {
            $builder->where('emp.company_id', (int)$searchArray['company_id']);
        }

        // Filter by employee
        if (!empty($searchArray['employee_id'])) {
            $builder->where('rl.employee_id', (int)$searchArray['employee_id']);
        }

        // Filter by manager
        if (!empty($searchArray['manager_id'])) {
            $builder->where('rm.manager_id', (int)$searchArray['manager_id']);
        }

        // Filter by status
        if (isset($searchArray['status']) && $searchArray['status'] !== '') {
            $builder->where('rl.status', $searchArray['status']);
        }

        // Order by latest created
        $builder->orderBy('rl.created_at', 'DESC');

        // Pagination
        if (!empty($limit) && !empty($offset)) {
            $builder->limit((int)$limit, (int)$offset);
        }

        $query = $builder->get();

        if ($countOnly) {
            return (int) $query->getRow()->total_count;
        }

        return $query->getResult();
    }

    public function getCompanyRequestLetters($companyId)
    {
        return $this->select('request_letters.*, employees.first_name, employees.last_name')
            ->join('employees', 'employees.employee_id = request_letters.employee_id')
            ->where('employees.company_id', $companyId)
            ->orderBy('request_letters.created_at', 'DESC')
            ->findAll();
    }
}
