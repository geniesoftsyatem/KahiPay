<?php

namespace App\Models;

use CodeIgniter\Model;

class DeviceDetailsModel extends Model
{
    protected $table      = 'devices_details';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = ['user_id', 'device_type', 'os', 'browser', 'ip_address', 'user_agent', 'last_used_at'];
}
