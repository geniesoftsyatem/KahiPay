<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\CompanyModel;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'user_id';
    protected $protectFields    = true;
    protected $useAutoIncrement = true;
    protected $allowedFields = ['username', 'name', 'email', 'phone', 'alt_mobile_number', 'password', 'gender', 'profile_image', 'otp', 'otp_expire_time', 'user_type', 'status', 'notes'];

    public function authenticateUser($username, $password)
    {
        $userRecord = $this->where("email", $username)->first();

        if (!$userRecord) {
            return 'user_not_found';
        }

        if (!password_verify($password, $userRecord["password"])) {
            return 'invalid_password';
        }

        if (strtolower($userRecord["status"]) !== "active") {
            return 'inactive';
        }

        // Prepare session data
        $sessionData = [
            "user_id"       => $userRecord["user_id"],
            "name"          => $userRecord["name"],
            "email"         => $userRecord["email"],
            "phone"         => $userRecord["phone"],
            "status"        => $userRecord["status"],
            "user_type"     => $userRecord["user_type"],
            "profile_image" => $userRecord["profile_image"] ?? null,
            "last_login"    => $userRecord["last_login"] ?? null,
            "profile_updated" => $userRecord["profile_updated"] ?? false,
            "logged_in"     => true,
            "timezone"      => $userRecord["timezone"] ?? 'UTC'
        ];

        // Handle company-specific data
        if (strtolower($userRecord['user_type']) === 'company') {
            $companyModel = new CompanyModel();
            $company = $companyModel->where('user_id', $userRecord['user_id'])->first();

            if ($company) {
                $sessionData["company_id"] = $company['company_id'];
                $sessionData["company_name"] = $company['company_name'] ?? null;
            }
        }

        // Update last login timestamp
        $currentTime = date('Y-m-d H:i:s');
        $sessionData["last_login"] = $currentTime;

        // Set session data
        session()->set($sessionData);

        // Regenerate session ID for security
        session()->regenerate();

        return true;
    }

    public function getUsersDetails($searchArray = [], $offset = '', $limit = '', $countOnly = '')
    {
        // Initialize the Query Builder
        $builder = $this->db->table($this->table . ' as t');

        // Join with the addresses table
        $builder->join('addresses as a', 'a.user_id = t.user_id', 'left');

        // Select the necessary columns
        if ($countOnly) {
            $builder->select("COUNT(t.{$this->primaryKey}) as total_count");
        } else {
            // Select user fields and addresses fields
            $builder->select('t.*, a.address, a.city, a.state, a.pincode, a.country, a.address_type');
        }

        // Add search filters
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like('t.username', $searchTerm)
                ->orLike('t.name', $searchTerm)
                ->orLike('t.email', $searchTerm)
                ->orLike('t.phone', $searchTerm)
                ->groupEnd();
        }

        // Apply ordering
        $builder->orderBy("t.{$this->primaryKey}", 'DESC');

        // Limit the results if limit and offset are provided
        if (!empty($limit) && !empty($offset)) {
            $builder->limit($limit, $offset);
        }

        // Execute the query
        $query = $builder->get();

        // Return the count or the results
        if ($countOnly) {
            return $query->getRow()->total_count;
        }

        return $query->getResult();
    }
}
