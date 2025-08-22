<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'task_id';
    protected $protectFields = true;
    protected $useTimestamps = true;
    protected $useAutoIncrement = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = ['employee_id', 'title', 'description', 'status', 'priority', 'due_date', 'assigned_by', 'completed_at', 'notes', 'created_at', 'updated_at'];

    /**
     * Method to get employee tasks with optional search, pagination, and count
     *
     * @param array $searchArray The search filters
     * @param int|string $offset The offset for pagination
     * @param int|string $limit The limit for pagination
     * @param bool $countOnly Whether to return count only or full records
     * @return array|int Returns either the list of tasks or count of records
     */
    public function getTasks($searchArray = [], $offset = '', $limit = '', $countOnly = false)
    {
        $builder = $this->db->table($this->table . ' t');

        if ($countOnly) {
            $builder->select("COUNT(t.{$this->primaryKey}) as total_count");
        } else {
            $builder->select('
            t.*,
            CONCAT(e.first_name, " ", e.last_name) as employee_name,
            CONCAT(m.first_name, " ", m.last_name) as manager_name,
            c.company_name
        ');
        }

        // Join with employees to get task employee details
        $builder->join('employees e', 'e.employee_id = t.employee_id', 'left');

        // Join with companies to get company name
        $builder->join('companies c', 'c.company_id = e.company_id', 'left');

        // Join with reporting_managers to get manager_id
        $builder->join('reporting_managers rm', 'rm.employee_id = e.employee_id', 'left');

        // Join with employees again to get manager details
        $builder->join('employees m', 'rm.manager_id = m.employee_id', 'left');

        // Search by text (title, description, employee name)
        if (!empty($searchArray['txtsearch'])) {
            $searchTerm = $searchArray['txtsearch'];
            $builder->groupStart()
                ->like("t.title", $searchTerm)
                ->orLike("t.description", $searchTerm)
                ->orLike("CONCAT(e.first_name, ' ', e.last_name)", $searchTerm)
                ->orLike("CONCAT(a.first_name, ' ', a.last_name)", $searchTerm)
                ->groupEnd();
        }

        // Employee Filter
        if (!empty($searchArray['employee_id'])) {
            $builder->where("t.employee_id", (int)$searchArray['employee_id']);
        }

        // Company Filter
        if (!empty($searchArray['company_id'])) {
            $builder->where("e.company_id", (int)$searchArray['company_id']);
        }

        // Manager Filter
        if (!empty($searchArray['manager'])) {
            $builder->where("rm.manager_id", (int)$searchArray['manager']);
        }

        // Status Filter
        if (isset($searchArray['status']) && $searchArray['status'] !== '') {
            $builder->where("t.status", $searchArray['status']);
        }

        // Priority Filter
        if (!empty($searchArray['priority'])) {
            $builder->where("t.priority", $searchArray['priority']);
        }

        // Department Filter
        if (!empty($searchArray['department'])) {
            $builder->where("e.department", (int)$searchArray['department']);
        }

        // Order by priority and due date
        $builder->orderBy("t.priority", 'DESC');
        $builder->orderBy("t.due_date", 'DESC');

        // Pagination
        if (!empty($limit) && !empty($offset)) {
            $builder->limit($limit, $offset);
        }

        // Execute the query and get the results
        $query = $builder->get();

        // Return the count if requested, otherwise return the results
        if ($countOnly) {
            return $query->getRow()->total_count;
        }

        return $query->getResult();
    }

    /**
     * Get task statistics for dashboard
     */
    public function getTaskStatistics($employee_id = null)
    {
        $builder = $this->db->table($this->table);

        $builder->select("
            COUNT(*) as total_tasks,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_tasks,
            SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_tasks,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_tasks,
            SUM(CASE WHEN due_date < CURDATE() AND status != 'completed' THEN 1 ELSE 0 END) as overdue_tasks
        ");

        if ($employee_id) {
            $builder->where('employee_id', $employee_id);
        }

        return $builder->get()->getRow();
    }

    /**
     * Update task status
     */
    public function updateTaskStatus($task_id, $status)
    {
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($status == 'completed') {
            $data['completed_at'] = date('Y-m-d H:i:s');
        }

        return $this->update($task_id, $data);
    }
}
