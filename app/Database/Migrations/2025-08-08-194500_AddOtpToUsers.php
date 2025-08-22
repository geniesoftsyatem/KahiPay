<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOtpToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'otp_expire_time' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'otp',
                'comment'    => 'OTP expiry timestamp'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'otp_expire_time');
    }
}
