<?php

namespace App\Models;

use Config\Database;
use CodeIgniter\Model;
use App\Libraries\TablesManager;

class EmployeeLocationModel extends Model
{
    protected $allowedFields = [
        'employee_id',
        'latitude',
        'longitude',
        'accuracy',
        'altitude',
        'speed',
        'heading',
        'timestamp',
        'ip_address',
        'device_info',
        'online_status',
        'last_seen_at',
        'created_at'
    ];

    /**
     * Save location into the correct monthly partition table
     */
    public function saveLocation(array $data)
    {
        $db = Database::connect();
        $library = new TablesManager();

        // Decide which partition to use based on timestamp
        $timestamp   = $data['timestamp'] ?? date('Y-m-d H:i:s');
        $monthSuffix = date('Y_m', strtotime($timestamp));
        $tableName   = "employee_locations_" . $monthSuffix;

        $library->createMonthlyLocationTableIfNotExists($timestamp);

        // Insert into the partition table
        return $db->table($tableName)->insert($data);
    }

    /**
     * Helper: get list of monthly partitioned tables between two dates
     */
    private function getMonthlyTables($fromDate = null, $toDate = null)
    {
        $tables = [];
        $start = new \DateTime($fromDate ?? 'now');
        $end   = new \DateTime($toDate ?? 'now');

        // Normalize to first day of month
        $start->modify('first day of this month');
        $end->modify('first day of this month');

        while ($start <= $end) {
            $tables[] = "employee_locations_" . $start->format("Y_m");
            $start->modify("+1 month");
        }

        return $tables;
    }

    /**
     * Get the latest location for each employee from the last 6 months.
     */
    public function getLatestEmployeeLocations(): array
    {
        $db = Database::connect();

        // Collect last 6 month table names
        $tables = [];
        for ($i = 0; $i < 6; $i++) {
            $month = date('Y_m', strtotime("-$i month"));
            $tableName = "employee_locations_" . $month;

            if ($db->tableExists($tableName)) {
                $tables[] = $tableName;
            }
        }

        if (empty($tables)) {
            return [];
        }

        // Build UNION ALL query selecting only required fields
        $unionParts = [];
        foreach ($tables as $t) {
            $unionParts[] = "SELECT employee_id, timestamp, latitude, longitude FROM {$t}";
        }
        $unionSql = implode("\nUNION ALL\n", $unionParts);

        // Use CTE to avoid running union twice (requires MySQL 8+ or similar)
        $sql = "
            WITH combined AS (
                {$unionSql}
            ),
            employee_latest_records AS (
                SELECT 
                    employee_id,
                    MAX(timestamp) AS latest_time
                FROM combined
                GROUP BY employee_id
            )
            SELECT 
                c.employee_id,
                c.timestamp,
                c.latitude,
                c.longitude
            FROM combined c
            INNER JOIN employee_latest_records latest_record
                ON c.employee_id = latest_record.employee_id
                AND c.timestamp = latest_record.latest_time
        ";

        $query = $db->query($sql);
        $results = $query->getResultArray();

        // Re-index array by employee_id
        $keyedResults = [];
        foreach ($results as $row) {
            $keyedResults[$row['employee_id']] = $row;
        }

        return $keyedResults;
    }

    /**
     * Get last known location for an employee
     */
    public function getLastLocation($employeeId)
    {
        // We may need to check last 6 months tables (or more)
        $locations = [];

        for ($i = 0; $i < 6; $i++) {
            $monthSuffix = date('Y_m', strtotime("-$i months"));
            $table = "employee_locations_" . $monthSuffix;

            if ($this->db->tableExists($table)) {
                $row = $this->db->table($table)
                    ->where('employee_id', $employeeId)
                    ->orderBy('timestamp', 'DESC')
                    ->get(1)
                    ->getRowArray();

                if ($row) {
                    $locations[] = $row;
                }
            }
        }

        // Return the latest location overall
        if (!empty($locations)) {
            usort($locations, function ($a, $b) {
                return strtotime($b['timestamp']) <=> strtotime($a['timestamp']);
            });
            return $locations[0]; // latest
        }

        return null;
    }

    /**
     * Fetch employee location records with optional filters, company and manager info
     */
    public function getEmployeeLocation($searchArray = [], $offset = '', $limit = '', $countOnly = false)
    {
        $fromDate = $searchArray['from_date'] ?? date('Y-m-01');
        $toDate   = $searchArray['to_date'] ?? date('Y-m-d');
        $tables   = $this->getMonthlyTables($fromDate, $toDate);

        $queries = [];
        foreach ($tables as $t) {
            if (!$this->db->tableExists($t)) {
                continue;
            }

            $builder = $this->db->table("$t el");

            if ($countOnly) {
                $builder->select("COUNT(el.location_id) as total_count");
            } else {
                $builder->select("
                    el.*,
                    e.first_name,
                    e.last_name,
                    e.email,
                    e.phone,
                    e.designation,
                    c.company_name,
                    CONCAT(m.first_name, ' ', m.last_name) as manager_name
                ");
            }

            // Joins
            $builder->join('employees e', 'e.employee_id = el.employee_id', 'left');
            $builder->join('companies c', 'c.company_id = e.company_id', 'left');
            $builder->join('reporting_managers rm', 'rm.employee_id = e.employee_id', 'left');
            $builder->join('employees m', 'rm.manager_id = m.employee_id', 'left');

            // Filters
            if (!empty($searchArray['txtsearch'])) {
                $searchTerm = $searchArray['txtsearch'];
                $builder->groupStart()
                    ->like('el.latitude', $searchTerm)
                    ->orLike('el.longitude', $searchTerm)
                    ->orLike("CONCAT(e.first_name, ' ', e.last_name)", $searchTerm)
                    ->orLike('e.email', $searchTerm)
                    ->orLike('e.phone', $searchTerm)
                    ->groupEnd();
            }
            if (!empty($searchArray['employee_id'])) {
                $builder->where('el.employee_id', (int)$searchArray['employee_id']);
            }
            if (!empty($searchArray['company_id'])) {
                $builder->where('e.company_id', (int)$searchArray['company_id']);
            }
            if (!empty($searchArray['manager'])) {
                $builder->where('rm.manager_id', (int)$searchArray['manager']);
            }
            if (!empty($searchArray['from_date'])) {
                $builder->where('el.timestamp >=', $searchArray['from_date'] . ' 00:00:00');
            }
            if (!empty($searchArray['to_date'])) {
                $builder->where('el.timestamp <=', $searchArray['to_date'] . ' 23:59:59');
            }

            $queries[] = $builder->getCompiledSelect();
        }

        if (empty($queries)) {
            return $countOnly ? 0 : [];
        }

        $sql = implode(" UNION ALL ", $queries);

        if (!$countOnly) {
            $sql .= " ORDER BY created_at DESC";
            if (!empty($limit) && !empty($offset)) {
                $sql .= " LIMIT $offset, $limit";
            }
        }

        $query = $this->db->query($sql);

        if ($countOnly) {
            $row = $query->getRow();
            return (int) $row->total_count;
        }

        return $query->getResult();
    }
}
