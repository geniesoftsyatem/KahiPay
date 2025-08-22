<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CompanyModel;
use App\Models\EmployeeModel;
use App\Models\RequestLetterModel;
use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    protected $userModel;
    protected $companyModel;
    protected $employeeModel;
    protected $requestLetterModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->companyModel = new CompanyModel();
        $this->employeeModel = new EmployeeModel();
        $this->requestLetterModel = new RequestLetterModel();
    }

    public function index()
    {
        set_title('Dashboard | ' . SITE_NAME);
        $companyId  = session('company_id');
        $userType   = session('user_type');

        // Current month counts
        $currentMonthStart = date('Y-m-01 00:00:00');
        $currentMonthEnd   = date('Y-m-t 23:59:59');
        // Previous month counts
        $previousMonthStart = date('Y-m-01 00:00:00', strtotime('-1 month'));
        $previousMonthEnd   = date('Y-m-t 23:59:59', strtotime('-1 month'));


        if (strtolower($userType) === 'company') {
            $recentUsers = $this->employeeModel
                ->select('employees.employee_id, employees.employee_code, employees.first_name, employees.last_name, employees.phone, employees.email, employees.gender, employees.designation, employees.department, employees.status, employees.created_at')
                ->join('users', 'users.user_id = employees.user_id', 'left')
                ->where('employees.company_id', $companyId)
                ->orderBy('employees.created_at', 'DESC')
                ->limit(5)
                ->findAll();
        } else {

            $recentUsers = $this->userModel
                ->select('user_id, username, name, email, phone, gender, user_type, status, created_at')
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->findAll();
        }

        $currentUsers = $this->userModel
            ->where('created_at >=', $currentMonthStart)
            ->where('created_at <=', $currentMonthEnd)
            ->countAllResults();

        $previousUsers = $this->userModel
            ->where('created_at >=', $previousMonthStart)
            ->where('created_at <=', $previousMonthEnd)
            ->countAllResults();

        // Total users count (all user types)
        $totalUsers = $this->userModel
            ->countAllResults();

        $userGrowth = $this->calculateGrowth($currentUsers, $previousUsers);

        // 2. COMPANIES CALCULATION (only active companies)
        $currentCompanies = $this->companyModel
            ->where('status', 'Active')
            ->where('created_at >=', $currentMonthStart)
            ->where('created_at <=', $currentMonthEnd)
            ->countAllResults();

        $previousCompanies = $this->companyModel
            ->where('status', 'Active')
            ->where('created_at >=', $previousMonthStart)
            ->where('created_at <=', $previousMonthEnd)
            ->countAllResults();

        $companyGrowth = $this->calculateGrowth($currentCompanies, $previousCompanies);
        $totalCompanies = $this->companyModel->where('status', 'Active')->countAllResults();

        $requestLetters = $this->requestLetterModel->getCompanyRequestLetters($companyId);

        // 3. EMPLOYEES CALCULATION (only active employees)
        $currentEmployees = $this->employeeModel
            ->where('status', 'Active')
            ->where('created_at >=', $currentMonthStart)
            ->where('created_at <=', $currentMonthEnd)
            ->countAllResults();

        $previousEmployees = $this->employeeModel
            ->where('status', 'Active')
            ->where('created_at >=', $previousMonthStart)
            ->where('created_at <=', $previousMonthEnd)
            ->countAllResults();
        $employeeGrowth = $this->calculateGrowth($currentEmployees, $previousEmployees);
        $totalEmployees = $this->employeeModel->where('status', 'Active')->countAllResults();

        // Wallet calculations (you'll need to implement this based on your wallet system)
        $walletGrowth = 0; // Placeholder - implement your wallet growth logic

        $data = [
            'userType' => $userType,
            'total_users' => $totalUsers,
            'request_letters' => count($requestLetters),
            'total_companies' => $totalCompanies,
            'total_employees' => $totalEmployees,
            'wallet_balance' => 0,
            'total_assigned_tasks' => 0,

            // Dynamic growth percentages
            'user_growth' => $userGrowth,
            'company_growth' => $companyGrowth,
            'employee_growth' => $employeeGrowth,
            'wallet_growth' => $walletGrowth,

            'recent_users' => $recentUsers
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * Calculate growth percentage between two values
     */
    protected function calculateGrowth($current, $previous)
    {
        if ($previous == 0 && $current > 0) {
            return 100; // Or return "∞"
        }
        if ($previous == 0) {
            return 100; // Infinite growth (from 0 to X)
        }

        $growth = (($current - $previous) / $previous) * 100;
        return round($growth, 2);
    }
}
