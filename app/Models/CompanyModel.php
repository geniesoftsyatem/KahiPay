<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table = 'companies';
    protected $primaryKey = 'company_id';
    protected $allowedFields = [
        'user_id',
        'company_code',
        'company_name',
        'email',
        'phone',
        'website',
        'industry',
        'address',
        'pan',
        'gst',
        'logo',
        'status',
        'created_by',
        'created_at',
        'updated_at'
    ];

    /**
     * Method to get employee tasks with optional search, pagination, and count
     *
     * @param array $searchArray The search filters
     * @param string $offset The offset for pagination
     * @param string $limit The limit for pagination
     * @param string $countOnly Whether to return count only or full records
     * @return array|int Returns either the list of tasks or count of records
     */
    public function getCompanies($searchArray = [], $offset = '', $limit = '', $countOnly = false)
    {
        $builder = $this->db->table($this->table . ' c');

        if ($countOnly) {
            $builder->select("COUNT(c.{$this->primaryKey}) as total_count");
        } else {
            $builder->select('c.*');
        }

        // Text search
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like("c.company_name", $searchTerm)
                ->orLike("c.company_code", $searchTerm)
                ->orLike("c.email", $searchTerm)
                ->orLike("c.phone", $searchTerm)
                ->groupEnd();
        }

        // Status filter
        if (isset($searchArray['status']) && $searchArray['status'] !== '') {
            $builder->where("c.status", $searchArray['status']);
        }

        $builder->orderBy("c.{$this->primaryKey}", 'ASC');

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
}
