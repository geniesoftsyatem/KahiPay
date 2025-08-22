<?php

namespace App\Models;

use CodeIgniter\Model;

class WalletModel extends Model
{
    protected $table         = 'wallets';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'balance', 'currency', 'status'];

    public function getWalletDetails($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        $builder = $this->db->table($this->table);

        // Join with users and addresses tables (no aliases used)
        $builder->join('users', 'users.user_id = wallets.user_id', 'left');
        $builder->join('addresses', 'addresses.user_id = wallets.user_id', 'left');

        // Select columns
        if ($countOnly) {
            $builder->select("COUNT({$this->table}.{$this->primaryKey}) as total_count");
        } else {
            $builder->select('wallets.*, users.name as user_name, users.email, users.phone, addresses.address, addresses.city, addresses.state, addresses.pincode, addresses.country, addresses.address_type');
        }

        // Search filters
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('users.name', $searchTerm)
                ->orLike('users.email', $searchTerm)
                ->orLike('users.phone', $searchTerm)
                ->groupEnd();
        }

        // Order by wallet ID
        $builder->orderBy("{$this->table}.{$this->primaryKey}", 'DESC');

        // Apply limit and offset if provided
        if (!empty($limit) && $limit !== '' && $offset !== '') {
            $builder->limit($limit, $offset);
        }

        $query = $builder->get();

        // Return count or result set
        if ($countOnly) {
            return $query->getRow()->total_count ?? 0;
        }

        return $query->getResult();
    }
}
