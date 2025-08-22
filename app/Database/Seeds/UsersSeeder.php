<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username'          => 'PY1000',
                'name'              => 'Admin',
                'email'             => 'admin@gmail.com',
                'phone'             => '9876543210',
                'alt_mobile_number' => null,
                'password'          => password_hash('12345678', PASSWORD_DEFAULT),
                'gender'            => 'Male',
                'profile_image'     => null,
                'otp'               => null,
                'user_type'         => 'admin',
                'status'            => 'Active',
                'notes'             => 'God of all Admins',
            ],
            [
                'username'          => 'MIK872',
                'name'              => 'Mike Ross',
                'email'             => 'mike@gmail.com',
                'phone'             => '9123456780',
                'alt_mobile_number' => null,
                'password'          => password_hash('mike@1234', PASSWORD_DEFAULT),
                'gender'            => 'Female',
                'profile_image'     => null,
                'otp'               => null,
                'user_type'         => 'admin',
                'status'            => 'Active',
                'notes'             => 'Master of strategy and planning',
            ],
            [
                'username'          => 'RAC239',
                'name'              => 'Rachelle Goulding',
                'email'             => 'rachelle@gmail.com',
                'phone'             => '9988776655',
                'alt_mobile_number' => '9911223344',
                'password'          => password_hash('wingedboots', PASSWORD_DEFAULT),
                'gender'            => 'Other',
                'profile_image'     => null,
                'otp'               => null,
                'user_type'         => 'employee',
                'status'            => 'Inactive',
                'notes'             => 'Speedy deliveries and updates',
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
