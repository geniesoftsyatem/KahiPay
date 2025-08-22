<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRechargesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'recharge_id' => [
                'type'           => 'INT',
                'auto_increment' => true,
                'unsigned'       => true,
            ],
            'request_txn_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'mobile_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'operator_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'status' => [
                'type' => 'INT',
                'null' => true,
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'error_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'operator_txn_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'txn_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'http_code' => [
                'type' => 'INT',
                'null' => true,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        $this->forge->addKey('recharge_id', true);
        $this->forge->createTable('recharges', true, ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('recharges');
    }
}
