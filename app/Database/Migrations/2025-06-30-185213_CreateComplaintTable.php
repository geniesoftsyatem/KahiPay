<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateComplaintTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'callback_id' => [
                'type'           => 'INT',
                'auto_increment' => true,
                'unsigned'       => true,
            ],
            'complaint_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'complaint_status' => [
                'type'       => 'INT',
                'comment'    => '8 = Resolved, 2 = Processing, 5 = Pending',
            ],
            'user_remark' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'our_remark' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'operator_txn_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'our_txn_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'requester_txn_id' => [
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
                'null'       => true,
            ],
            'recharge_status' => [
                'type'       => 'INT',
                'comment'    => '1 = Success, 2 = Processing, 3 = Failed',
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);

        $this->forge->addKey('callback_id', true);
        $this->forge->createTable('complaints', true, ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('complaints');
    }
}
