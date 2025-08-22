<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmployeeLocationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'location_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'employee_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'latitude' => [
                'type' => 'DECIMAL',
                'constraint' => '10,8',
            ],
            'longitude' => [
                'type' => 'DECIMAL',
                'constraint' => '11,8',
            ],
            'accuracy' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'altitude' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'speed' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'heading' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'timestamp' => [
                'type' => 'DATETIME',
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'online_status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'comment'    => '1 = Online, 0 = Offline',
            ],
            'last_seen_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => 'Timestamp when employee was last seen (offline)',
            ],
            'device_info' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at datetime default current_timestamp',
        ]);

        $this->forge->addPrimaryKey('location_id');
        $this->forge->addForeignKey('employee_id', 'employees', 'employee_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('employee_locations', true, ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('employee_locations');
    }
}
