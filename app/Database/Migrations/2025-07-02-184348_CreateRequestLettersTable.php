<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRequestLettersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'request_id' => [
                'type'           => 'INT',
                'auto_increment' => true,
                'unsigned'       => true,
            ],
            'employee_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'reporting_employee_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'images' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Comma-separated list of image file names or paths',
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        $this->forge->addKey('request_id', true);
        $this->forge->createTable('request_letters', true, [ 'charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('request_letters');
    }
}
