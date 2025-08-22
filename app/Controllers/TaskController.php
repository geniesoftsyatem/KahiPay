<?php

namespace App\Controllers;

use App\Models\TaskModel;
use App\Models\CompanyModel;
use App\Models\EmployeeModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;
use App\Models\ReportingManagerModel;

class TaskController extends BaseController
{
    protected $session;
    protected $taskModel;
    protected $companyModel;
    protected $employeeModel;
    protected $reportingManagerModel;

    public function __construct()
    {
        $this->session = session();
        $this->taskModel = new TaskModel();
        $this->companyModel = new CompanyModel();
        $this->employeeModel = new EmployeeModel();
        $this->reportingManagerModel = new ReportingManagerModel();
    }

    public function index()
    {
        set_title('Employee Tasks | ' . SITE_NAME);
        $companyId  = session('company_id');
        $userType   = session('user_type');

        $data = [
            'action'       => "employee-tasks",
            'startLimit'   => 0,
            'reverse'      => 0,
            'pagination'   => '',
            'results'      => [],
            'searchArray'  => [],
        ];

        $customPagination = new Pagination();

        // Collect search criteria
        $searchFields = $this->request->getGet();
        foreach ($searchFields as $field => $searchValue) {
            $data['searchArray'][$field] = trim($searchValue);
        }

        // If logged in as company, force company_id in search
        if ($userType === 'company' && !empty($companyId)) {
            $data['searchArray']['company_id'] = $companyId;
        }

        $selectedCompanyId = $data['searchArray']['company_id'] ?? null;

        $data['managers'] = [];
        if (!empty($selectedCompanyId)) {
            $data['managers'] = $this->reportingManagerModel->getReportingManagers([
                'companyId' => $selectedCompanyId
            ]);
        }

        if (empty($data['managers']) && $userType === 'company') {
            $data['managers'] = $this->reportingManagerModel->getReportingManagers([
                'companyId' => $companyId
            ]);
        }
        $data['companies'] = $this->companyModel->where('status', 'Active')->findAll();

        $Limit = 10;
        $page = (int) $this->request->getGet('page') ?: 1;
        $totalRecord = $this->taskModel->getTasks($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['startLimit'] = $startLimit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $this->taskModel->getTasks($data['searchArray'], $startLimit, $Limit);
        $data['taskStatistics'] = $this->taskModel->getTaskStatistics();

        return view('admin/task/index', $data);
    }

    public function create()
    {
        set_title('Add Task | ' . SITE_NAME);

        $userType  = $this->session->get('user_type');
        $companyId = $this->session->get('company_id');

        $data = [
            'pagetitle' => 'Add Task',
            'companies' => [],
            'employees' => []
        ];

        if ($userType === 'company') {
            $data['employees'] = $this->employeeModel
                ->where('company_id', $companyId)
                ->findAll();
        } else {
            $data['companies'] = $this->companyModel
                ->where('status', 'Active')
                ->findAll();
        }

        return view('admin/task/create', $data);
    }

    public function edit($taskId)
    {
        set_title('Edit Task | ' . SITE_NAME);

        $task = $this->taskModel
            ->select('tasks.*, employees.company_id')
            ->join('employees', 'employees.employee_id = tasks.employee_id', 'left')
            ->where('tasks.task_id', $taskId)
            ->first();

        if (!$task) {
            return redirect()->to('/tasks')->with('error', 'Task not found');
        }

        $userType  = $this->session->get('user_type');
        $companyId = $this->session->get('company_id');

        $data = [
            'pagetitle'  => 'Update Task',
            'companies'  => [],
            'employees'  => [],
            'task'       => $task,
        ];

        if ($userType === 'company') {
            // Load employees of the same company
            $data['employees'] = $this->employeeModel
                ->where('company_id', $companyId)
                ->findAll();
        } else {
            // Load all companies
            $data['companies'] = $this->companyModel
                ->where('status', 'Active')
                ->findAll();

            // Load employees of the task's company (for pre-selecting in form)
            if (!empty($task['company_id'])) {
                $data['employees'] = $this->employeeModel
                    ->where('company_id', $task['company_id'])
                    ->findAll();
            }
        }

        return view('admin/task/create', $data);
    }

    public function save()
    {
        $postData = $this->request->getPost();

        $data = [
            'employee_id' => $postData['employee_id'],
            'title'       => $postData['title'],
            'description' => $postData['description'],
            'priority'    => $postData['priority'],
            'due_date'    => $postData['due_date'],
            'assigned_by' => $this->session->get('user_id'),
            'notes'       => $this->session->get('notes'),
            'status'      => isset($postData['status']) ? $postData['status'] : 'pending',
        ];

        if (!empty($postData['status']) && $postData['status'] == 'completed') {
            $data['completed_at'] = date('Y-m-d H:i:s');
        }

        if (!empty($postData['task_id'])) {
            $result = $this->taskModel->update($postData['task_id'], $data);
            $message = 'Task updated successfully.';
        } else {
            $result = $this->taskModel->insert($data);
            $message = 'Task added successfully.';
        }

        if ($result) {
            $this->session->setFlashdata('message', $message);
        } else {
            $this->session->setFlashdata('errmessage', 'Something went wrong...');
        }

        return redirect()->to(site_url('employee-tasks'));
    }

    public function preview($taskId)
    {
        set_title('Task Details | ' . SITE_NAME);
        $data['pageTitle'] = "Task Details";

        $data['task'] = $this->taskModel->find($taskId);
        return view('admin/task/preview', $data);
    }

    public function updateStatus()
    {
        $task_id = $this->request->getPost('task_id');
        $status = $this->request->getPost('status');

        if ($this->taskModel->updateTaskStatus($task_id, $status)) {
            $this->session->setFlashdata('message', 'Task status updated successfully.');
        } else {
            $this->session->setFlashdata('errmessage', 'Failed to update task status.');
        }

        return redirect()->back();
    }

    public function delete()
    {
        $taskId = $this->request->getPost('task_id');

        if (empty($taskId) || !is_numeric($taskId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid task ID'
            ]);
        }

        $task = $this->taskModel->find($taskId);

        if (!$task) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Task not found'
            ]);
        }

        try {
            $this->taskModel->delete($taskId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Task deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete task: ' . $e->getMessage()
            ]);
        }
    }
}
