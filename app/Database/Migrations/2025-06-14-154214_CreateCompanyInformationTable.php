<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCompanyInformationTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'company_name'    => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
            ],
            'phone'           => [
                'type'           => 'VARCHAR',
                'constraint'     => '50',
                'null'           => true,
            ],
            'email'           => [
                'type'           => 'VARCHAR',
                'constraint'     => '150',
                'null'           => true,
            ],
            'address'         => [
                'type'           => 'TEXT',
                'null'           => true,
            ],
            'city'            => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
                'null'           => true,
            ],
            'state'           => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
                'null'           => true,
            ],
            'country'         => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
                'null'           => true,
            ],
            'pincode'         => [
                'type'           => 'VARCHAR',
                'constraint'     => '20',
                'null'           => true,
            ],
            'website'         => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'null'           => true,
            ],
            'logo'            => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'null'           => true,
            ],
            'created_at'      => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'updated_at'      => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('company_information');
    }

    public function down()
    {
        $this->forge->dropTable('company_information');
    }
}
