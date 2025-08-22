<?php

namespace App\Models;

use CodeIgniter\Model;

class ContactUsModel extends Model
{
    protected $table = 'contact_us';
    protected $primaryKey = 'id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = ['name', 'phone', 'email', 'message', 'status', 'created_at', 'updated_at'];

    /**
     * Method to get contact messages with optional search, pagination, and count
     *
     * @param array $searchArray The search filters
     * @param string $offset The offset for pagination
     * @param string $limit The limit for pagination
     * @param string $countOnly Whether to return count only or full records
     * @return array|int Returns either the list of contact messages or count of records
     */
    public function getContactMessages($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        // Start building the query
        $builder = $this->db->table($this->table);

        // Select count or full fields
        if ($countOnly) {
            $builder->select("COUNT({$this->primaryKey}) as total_count");
        } else {
            $builder->select('*');
        }

        // Search filter
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('name', $searchTerm)
                ->orLike('phone', $searchTerm)
                ->orLike('email', $searchTerm)
                ->orLike('message', $searchTerm)
                ->groupEnd();
        }

        $builder->orderBy("{$this->primaryKey}", 'DESC');

        // Apply pagination if provided
        if (!empty($limit) && !empty($offset)) {
            $builder->limit($limit, $offset);
        }

        $query = $builder->get();

        if ($countOnly) {
            return $query->getRow()->total_count ?? 0;
        }

        return $query->getResult();
    }
}
