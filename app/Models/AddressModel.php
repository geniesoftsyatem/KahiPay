<?php

namespace App\Models;

use CodeIgniter\Model;

class AddressModel extends Model
{
    protected $table      = 'addresses';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = ['user_id', 'name', 'phone', 'address', 'area_street', 'landmark', 'city', 'state', 'pincode', 'country', 'address_type', 'is_primary'];
}
