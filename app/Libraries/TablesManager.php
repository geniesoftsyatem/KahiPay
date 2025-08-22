<?php

namespace App\Libraries;

use Config\Database;

class TablesManager
{
    /**
     * Create monthly attendance table (employee_attendance_YYYY_MM)
     */
    public function createMonthlyTableIfNotExists()
    {
        $db = Database::connect();
        $currentMonth = date('Y_m'); // Example: 2025_08
        $tableName = "employee_attendance_" . $currentMonth;

        if (!$db->tableExists($tableName)) {
            $forge = Database::forge();

            $fields = [
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true
                ],
                'employee_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true
                ],
                'punch_date' => [
                    'type' => 'DATE',
                    'null' => false
                ],
                'punch_in' => [
                    'type' => 'DATETIME',
                    'null' => true
                ],
                'punch_out' => [
                    'type' => 'DATETIME',
                    'null' => true
                ],
                'total_hours' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                    'null'       => true
                ],
                'created_at datetime default current_timestamp',
                'updated_at datetime default current_timestamp on update current_timestamp',
            ];

            $forge->addField($fields);
            $forge->addKey('id', true);
            $forge->createTable($tableName, true);
        }

        return $tableName;
    }

    /**
     * Create yearly summary table (employee_attendance_summary_YYYY)
     */
    public function createYearlySummaryTableIfNotExists(string $year = null)
    {
        $db   = Database::connect();
        $year = $year ?? date('Y');
        $tableName = "employee_attendance_summary_" . $year;

        if (!$db->tableExists($tableName)) {
            $forge = Database::forge();

            $fields = [
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true
                ],
                'employee_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true
                ],
                'month' => [
                    'type'       => 'TINYINT',
                    'constraint' => 2,
                    'null'       => false
                ],
                'attendance_date' => [
                    'type' => 'DATE',
                    'null' => false
                ],
                'total_hours' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '7,2',
                    'default'    => 0.00
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => [
                        'Present',
                        'Late',
                        'Half Day',
                        'Absent',
                        'Holiday',
                        'Weekly Off',
                        'Casual Leave',
                        'Sick Leave',
                        'Paid Leave'
                    ],
                    'default'    => 'Absent'
                ],
                'created_at datetime default current_timestamp',
                'updated_at datetime default current_timestamp on update current_timestamp',
            ];

            $forge->addField($fields);
            $forge->addKey('id', true);
            $forge->addUniqueKey(['employee_id', 'attendance_date']);
            $forge->createTable($tableName, true);
        }

        return $tableName;
    }

    /**
     * Create monthly employee_locations table (employee_locations_YYYY_MM)
     */
    public function createMonthlyLocationTableIfNotExists($date = null)
    {
        $db = Database::connect();
        $monthSuffix = $date ? date('Y_m', strtotime($date)) : date('Y_m');
        $tableName = "employee_locations_" . $monthSuffix;

        if (!$db->tableExists($tableName)) {
            $forge = Database::forge();

            $fields = [
                'location_id' => [
                    'type'           => 'BIGINT',
                    'unsigned'       => true,
                    'auto_increment' => true
                ],
                'employee_id' => [
                    'type'       => 'INT',
                    'unsigned'   => true,
                    'null'       => false
                ],
                'latitude' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,7',
                    'null'       => true
                ],
                'longitude' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,7',
                    'null'       => true
                ],
                'accuracy' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'null'       => true
                ],
                'altitude' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'null'       => true
                ],
                'speed' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'null'       => true
                ],
                'heading' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'null'       => true
                ],
                'timestamp' => [
                    'type'    => 'DATETIME',
                    'null'    => false,
                    'comment' => 'location timestamp'
                ],
                'ip_address' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 45,
                    'null'       => true
                ],
                'online_status' => [
                    'type'    => 'TINYINT',
                    'null'    => true,
                    'default' => 0
                ],
                'last_seen_at' => [
                    'type' => 'DATETIME',
                    'null' => true
                ],
                'device_info' => [
                    'type' => 'TEXT',
                    'null' => true
                ],
                'created_at datetime default current_timestamp',
            ];

            $forge->addField($fields);
            $forge->addKey('location_id', true);
            $forge->addKey(['employee_id', 'timestamp']);
            $forge->addKey('created_at');
            $forge->createTable($tableName, true);
        }

        return $tableName;
    }
}
