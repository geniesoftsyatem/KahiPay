<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSalariesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'salary_id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'employee_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'month' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'null'       => false,
                'comment'    => '1 to 12 representing month',
            ],
            'year' => [
                'type'       => 'YEAR',
                'null'       => false,
            ],
            'basic_salary' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'allowances' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 0.00,
            ],
            'deductions' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 0.00,
            ],
            'net_salary' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'payslip' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Stores the path/URL of the salary PDF',
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        $this->forge->addKey('salary_id', true);
        $this->forge->createTable('salaries', true, ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('salaries');
    }
}
