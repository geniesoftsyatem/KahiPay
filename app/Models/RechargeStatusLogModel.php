<?php

namespace App\Models;

use CodeIgniter\Model;

class RechargeStatusLogModel extends Model
{
    protected $table = 'recharge_status_logs';
    protected $primaryKey = 'status_log_id';
    protected $allowedFields = [
        'request_txn_id',
        'customer_no',
        'operator',
        'amount',
        'status',
        'message',
        'circle',
        'error_code',
        'txn_no',
        'operator_txn_id',
        'http_code'
    ];
}
