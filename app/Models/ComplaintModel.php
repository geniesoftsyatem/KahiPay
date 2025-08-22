<?php

namespace App\Models;

use CodeIgniter\Model;

class ComplaintModel extends Model
{
    protected $table = 'complaints';
    protected $primaryKey = 'callback_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'complaint_id',
        'complaint_status',
        'user_remark',
        'our_remark',
        'operator_txn_id',
        'our_txn_id',
        'requester_txn_id',
        'mobile_no',
        'amount',
        'recharge_status',
    ];
}
