<?php

namespace App\Controllers\API;

use App\Models\TaskModel;
use App\Models\EmployeeModel;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;

class TaskController extends BaseController
{
    use ResponseTrait;

    protected $taskModel;
    protected $employeeModel;

    public function __construct()
    {
        $this->taskModel = new TaskModel();
        $this->employeeModel = new EmployeeModel();
    }

    /**
     * Get tasks assigned to an employee on a specific date.
     * 
     * Endpoint: GET /api/tasks/by-date?employee_id=123&date=2025-08-03
     * 
     * @return \CodeIgniter\HTTP\Response JSON response
     */
    public function getTasksByDate()
    {
        $employeeId = $this->request->getGet('employee_id');
        $date = $this->request->getGet('date');

        if (empty($employeeId) || !is_numeric($employeeId)) {
            return $this->failValidationErrors('Employee ID is required and must be numeric');
        }

        if (empty($date) || !strtotime($date)) {
            return $this->failValidationErrors('Valid date is required (format: YYYY-MM-DD)');
        }

        $employee = $this->employeeModel->find($employeeId);
        if (!$employee) {
            return $this->failNotFound('Employee not found');
        }

        // Get reporting manager (if any)
        $db = \Config\Database::connect();
        $managerRow = $db->table('reporting_managers rm')
            ->select('m.employee_id as manager_id, m.first_name as manager_first_name, m.last_name as manager_last_name')
            ->join('employees m', 'm.employee_id = rm.manager_id')
            ->where('rm.employee_id', $employeeId)
            ->get()
            ->getRowArray();

        $managerFullName = $managerRow
            ? $managerRow['manager_first_name'] . ' ' . $managerRow['manager_last_name']
            : null;

        // Get tasks assigned to this employee
        $tasks = $this->taskModel
            ->select('tasks.*, emp.first_name, emp.last_name, emp.designation')
            ->join('employees emp', 'emp.employee_id = tasks.employee_id')
            ->where('tasks.employee_id', $employeeId)
            ->where('DATE(tasks.due_date)', date('Y-m-d', strtotime($date)))
            ->findAll();

        if (empty($tasks)) {
            return $this->respond([
                'status' => 200,
                'error' => null,
                'messages' => 'No tasks found for this employee on the specified date',
            ]);
        }

        $responseData = [];
        foreach ($tasks as $task) {
            $responseData[] = [
                'task_id' => $task['task_id'],
                'employee_name' => $task['first_name'] . ' ' . $task['last_name'],
                'designation' => $task['designation'],
                'title' => $task['title'],
                'description' => $task['description'],
                'due_date' => $task['due_date'],
                'status' => $task['status'],
                'priority' => $task['priority'],
                'reporting_manager' => $managerFullName ?? 'N/A',
            ];
        }

        return $this->respond([
            'status' => 200,
            'error' => null,
            'messages' => 'Tasks retrieved successfully',
            'data' => $responseData
        ]);
    }

    /**
     * Assign a new task to one or multiple employees.
     * 
     * POST /api/tasks/assign
     * 
     * @return \CodeIgniter\HTTP\Response JSON response
     */
    public function assignTask()
    {
        // Validate required fields
        $rules = [
            'employee_id' => 'required',
            'assigned_by' => 'required|numeric',
            'title' => 'required|string|min_length[3]|max_length[100]',
            'description' => 'required|string|min_length[10]',
            'priority' => 'permit_empty|in_list[low,medium,high]',
            'due_date' => 'permit_empty|valid_date[Y-m-d]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $postData = $this->request->getVar();

        // Normalize employee IDs to array
        $employeeIds = json_decode($postData['employee_id'], true);

        // Validate each employee ID
        foreach ($employeeIds as $id) {
            if (!is_numeric($id)) {
                return $this->failValidationErrors(['employee_id' => 'All employee IDs must be numeric']);
            }
        }

        // Prepare common task data
        $taskDataTemplate = [
            'assigned_by'  => $postData['assigned_by'],
            'title'        => $postData['title'],
            'description'  => $postData['description'],
            'status'       => 'pending',
            'priority'     => $postData['priority'] ?? 'low',
            'due_date'     => $postData['due_date'] ?? date('Y-m-d')
        ];

        $insertedIds = [];
        $this->taskModel->transStart();

        try {
            foreach ($employeeIds as $employeeId) {
                $taskData = $taskDataTemplate;
                $taskData['employee_id'] = $employeeId;

                if (!$taskId = $this->taskModel->insert($taskData, true)) {
                    throw new \RuntimeException('Failed to assign task to employee ID: ' . $employeeId);
                }

                $insertedIds[] = $taskId;
            }

            $this->taskModel->transComplete();
        } catch (\Exception $e) {
            $this->taskModel->transRollback();
            return $this->failServerError('Failed to assign tasks: ' . $e->getMessage());
        }

        // Prepare response
        $response = [
            'status'   => 201,
            'error'    => null,
            'messages' => [
                'success' => count($employeeIds) > 1 ?
                    'Tasks assigned successfully' :
                    'Task assigned successfully'
            ],
            'data' => [
                'task_ids' => implode(',', $insertedIds),
                'count'    => count($insertedIds)
            ]
        ];

        return $this->respondCreated($response);
    }

    /**
     * Submit a completed task by an employee.
     * 
     * POST /api/tasks/submit
     * 
     * @return \CodeIgniter\HTTP\Response JSON response
     */
    public function submitTask()
    {
        $rules = [
            'employee_id' => 'required|numeric',
            'task_id'     => 'required|numeric',
            'comment'     => 'required|string|min_length[5]|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $postData = $this->request->getVar();
        $task = $this->taskModel->where('task_id', $postData['task_id'])
            ->where('employee_id', $postData['employee_id'])
            ->first();

        if (!$task) {
            return $this->failNotFound('Task not found or does not belong to this employee');
        }

        if ($task['status'] === 'completed') {
            return $this->fail('This task has already been completed', 400);
        }

        $updateData = [
            'notes' => $postData['comment'],
            'status' => 'completed',
            'completed_at' => date('Y-m-d H:i:s'),
        ];

        if (!$this->taskModel->update($postData['task_id'], $updateData)) {
            return $this->failServerError('Failed to submit task. Please try again.');
        }

        return $this->respond([
            'status'   => 200,
            'error'    => null,
            'messages' => 'Task submitted successfully',
        ]);
    }
}
