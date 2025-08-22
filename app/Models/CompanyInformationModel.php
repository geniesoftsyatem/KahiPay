<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyInformationModel extends Model
{
    protected $table = 'company_information';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['company_name', 'address', 'city', 'state', 'country', 'pincode', 'phone', 'email', 'website', 'logo', 'created_at', 'updated_at'];
}
