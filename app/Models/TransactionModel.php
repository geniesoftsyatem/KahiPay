<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table         = 'transactions';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'wallet_id', 'type', 'amount', 'description', 'reference_id', 'status'];

    public function getWalletTransactions($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        $builder = $this->db->table($this->table); // $this->table = 'transactions'

        // Join with users and wallets tables
        $builder->join('users', 'users.user_id = transactions.user_id', 'left');
        $builder->join('wallets', 'wallets.id = transactions.wallet_id', 'left');

        // Select columns
        if ($countOnly) {
            $builder->select("COUNT({$this->table}.{$this->primaryKey}) as total_count");
        } else {
            $builder->select('
            transactions.id as transaction_id,
            transactions.user_id,
            transactions.wallet_id,
            transactions.type,
            transactions.amount,
            transactions.description,
            transactions.reference_id,
            transactions.status,
            transactions.created_at,
            transactions.updated_at,
            users.name as user_name,
            users.email,
            users.phone,
            wallets.balance as wallet_balance
        ');
        }

        // Search filters
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('users.name', $searchTerm)
                ->orLike('users.email', $searchTerm)
                ->orLike('users.phone', $searchTerm)
                ->orLike('transactions.description', $searchTerm)
                ->groupEnd();
        }

        // Order by transaction ID
        $builder->orderBy("transactions.{$this->primaryKey}", 'DESC');

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
