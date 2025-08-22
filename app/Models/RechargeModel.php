<?php

namespace App\Models;

use CodeIgniter\Model;

class RechargeModel extends Model
{
    protected $table = 'recharges';
    protected $primaryKey = 'recharge_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'request_txn_id',
        'mobile_no',
        'amount',
        'operator_id',
        'status',
        'message',
        'error_code',
        'operator_txn_id',
        'txn_no',
        'http_code'
    ];
}
