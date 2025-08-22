<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttendanceTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
                'unsigned'       => true,
            ],
            'employee_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'attendance_date' => [
                'type' => 'DATE',
            ],
            'in_time' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'out_time' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'total_work_hours' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'Present',
                    'Half Day',
                    'Absent',
                    'Casual Leave',
                    'Sick Leave',
                    'Work From Home',
                    'On Duty / Official Visit',
                    'Paid Leave',
                    'Unpaid Leave',
                    'Compensatory Off',
                    'Holiday / Weekend',
                ],
                'default'    => 'Present',
            ],
            'remarks' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        // Add foreign key if you want strict integrity (optional but good practice)
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('employee_id', 'employees', 'employee_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('attendance', true, ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('attendance');
    }
}
