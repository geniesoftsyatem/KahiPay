<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table         = 'notifications';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['title', 'message', 'is_read'];

    public function getNotificationDetails($searchArray = [], $offset = '', $limit = '', $countOnly = false)
    {
        $builder = $this->db->table($this->table);
        if ($countOnly) {
            $builder->select("COUNT({$this->table}.{$this->primaryKey}) as total_count");
        } else {
            // Select notification fields
            $builder->select("{$this->table}.*,");
        }

        // Search filters on title and message
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like("{$this->table}.title", $searchTerm)
                ->orLike("{$this->table}.message", $searchTerm)
                ->groupEnd();
        }

        // Filter by read/unread if provided
        if (isset($searchArray['is_read'])) {
            $builder->where("{$this->table}.is_read", $searchArray['is_read']);
        }

        // Order by id descending (newest notifications first)
        $builder->orderBy("{$this->table}.{$this->primaryKey}", 'DESC');

        // Apply limit and offset if provided (pagination)
        if ($limit !== '' && $offset !== '') {
            $builder->limit($limit, $offset);
        }

        $query = $builder->get();

        // Return count or results
        if ($countOnly) {
            return $query->getRow()->total_count ?? 0;
        }

        return $query->getResult();
    }
}
