<?php

namespace App\Models;

use CodeIgniter\Model;

class SalaryModel extends Model
{
    protected $table = 'salaries';
    protected $primaryKey = 'salary_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = ['employee_id', 'month', 'year', 'basic_salary', 'allowances', 'deductions', 'net_salary', 'payslip', 'remarks', 'created_at', 'updated_at'];

    /**
     * Method to get latest employee salaries with employee, company, and reporting manager details
     *
     * @param array $searchArray The search filters
     * @param string $offset The offset for pagination
     * @param string $limit The limit for pagination
     * @param bool $countOnly Whether to return count only or full records
     * @return array|int Returns either the list of salaries or count of records
     */
    public function getSalariesWithEmployees($searchArray = [], $offset = '', $limit = '', $countOnly = false)
    {
        // Subquery to get the latest salary_id for each employee
        $subQuery = $this->db->table($this->table)
            ->select('MAX(salary_id) AS salary_id')
            ->groupBy('employee_id');

        // Main query
        $builder = $this->db->table("({$subQuery->getCompiledSelect()}) AS latest")
            ->join("{$this->table} es", 'es.salary_id = latest.salary_id', 'inner')
            ->join('employees e', 'e.employee_id = es.employee_id', 'inner')
            ->join('companies c', 'c.company_id = e.company_id', 'left')
            ->join('reporting_managers rm', 'rm.employee_id = e.employee_id', 'left')
            ->join('employees m', 'm.employee_id = rm.manager_id', 'left');

        if ($countOnly) {
            $builder->select("COUNT(es.salary_id) as total_count");
        } else {
            $builder->select('
                es.*,
                e.first_name, e.last_name, e.email, e.phone, c.company_name, CONCAT(m.first_name, " ", m.last_name) AS manager_name
            ');
        }
        // Company filter
        if (!empty($searchArray['company_id'])) {
            $builder->where('e.company_id', (int)$searchArray['company_id']);
        }

        // Manager filter
        if (!empty($searchArray['manager'])) {
            $builder->where('rm.manager_id', (int)$searchArray['manager']);
        }

        // Filters
        if (!empty($searchArray['employee_id'])) {
            $builder->where('es.employee_id', $searchArray['employee_id']);
        }
        if (!empty($searchArray['month'])) {
            $builder->where('es.month', $searchArray['month']);
        }
        if (!empty($searchArray['year'])) {
            $builder->where('es.year', $searchArray['year']);
        }

        // Text search
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('CONCAT(e.first_name, " ", e.last_name)', $searchTerm)
                ->orLike('e.email', $searchTerm)
                ->orLike('e.phone', $searchTerm)
                ->orLike('c.company_name', $searchTerm)
                ->orLike('CONCAT(m.first_name, " ", m.last_name)', $searchTerm)
                ->groupEnd();
        }

        // Order by latest salary
        $builder->orderBy('es.salary_id', 'DESC');

        // Pagination
        if (!empty($limit) && !empty($offset)) {
            $builder->limit($limit, $offset);
        }

        $query = $builder->get();

        // Return count or result
        if ($countOnly) {
            return $query->getRow()->total_count;
        }

        return $query->getResult();
    }
}
