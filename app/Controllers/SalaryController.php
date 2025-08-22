<?php

namespace App\Controllers;

use Mpdf\Mpdf;
use App\Models\SalaryModel;
use App\Models\CompanyModel;
use App\Models\EmployeeModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;
use App\Models\ReportingManagerModel;

class SalaryController extends BaseController
{
    protected $session;
    protected $salaryModel;
    protected $companyModel;
    protected $employeeModel;
    protected $reportingManagerModel;

    public function __construct()
    {
        $this->session = session();
        $this->salaryModel = new SalaryModel();
        $this->companyModel = new CompanyModel();
        $this->employeeModel = new EmployeeModel();
        $this->reportingManagerModel = new ReportingManagerModel();
    }

    public function index()
    {
        set_title('Employee Salaries | ' . SITE_NAME);

        $userType   = strtolower(session('user_type'));
        $companyId  = session('company_id');

        $data = [
            'action'       => "salary",
            'results'      => [],
            'pagination'   => '',
            'startLimit'   => 0,
            'reverse'      => 0,
            'searchArray'  => [],
            'months'       => [
                '1'  => 'January',
                '2'  => 'February',
                '3'  => 'March',
                '4'  => 'April',
                '5'  => 'May',
                '6'  => 'June',
                '7'  => 'July',
                '8'  => 'August',
                '9'  => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December'
            ]
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
        $totalRecord = $this->salaryModel->getSalariesWithEmployees($data['searchArray'], 0, 0, true);
        $startLimit = ($page - 1) * $Limit;
        $data['startLimit'] = $startLimit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $this->salaryModel->getSalariesWithEmployees($data['searchArray'], $startLimit, $Limit);

        return view('admin/salary/index', $data);
    }

    public function create()
    {
        set_title('Add Salary | ' . SITE_NAME);

        $userType  = $this->session->get('user_type');
        $companyId = $this->session->get('company_id');

        if ($userType === 'company') {
            $employees = $this->employeeModel->where('company_id', $companyId)->findAll();
            $companies = $this->companyModel->where('company_id', $companyId)->findAll();
        } else {
            $employees = [];
            $companies = $this->companyModel->where('status', 'Active')->findAll();
        }

        $data = [
            'pagetitle' => "Add Employee Salary",
            'employees' => $employees,
            'companies' => $companies,
        ];

        return view('admin/salary/create', $data);
    }

    public function edit($salaryId)
    {
        set_title('Edit Salary | ' . SITE_NAME);
        $data['pagetitle'] = "Edit Employee Salary";

        $salary = $this->salaryModel
            ->select('salaries.*, employees.company_id')
            ->join('employees', 'employees.employee_id = salaries.employee_id')
            ->where('salaries.salary_id', $salaryId)
            ->first();

        if (!$salary) {
            $this->session->setFlashdata('errmessage', 'Salary record not found');
            return redirect()->to(site_url('salary'));
        }

        $userType  = $this->session->get('user_type');
        $companyId = $this->session->get('company_id');

        if ($userType === 'company') {
            $employees = $this->employeeModel->where('company_id', $companyId)->findAll();
            $companies = $this->companyModel->where('company_id', $companyId)->findAll();
        } else {
            $employees = $this->employeeModel->findAll();
            $companies = $this->companyModel->where('status', 'Active')->findAll();
        }

        $data['salary']    = $salary;
        $data['employees'] = $employees;
        $data['companies'] = $companies;

        return view('admin/salary/create', $data);
    }

    public function store()
    {

        $rules = [
            'employee_id' => 'required|numeric',
            'month' => 'required|numeric|greater_than[0]|less_than_equal_to[12]',
            'year' => 'required|numeric',
            'basic_salary' => 'required|decimal',
            'allowances' => 'permit_empty|decimal',
            'deductions' => 'permit_empty|decimal',
            'net_salary' => 'required|decimal',
            'remarks' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('errmessage', implode('<br>', $this->validator->getErrors()));
            return redirect()->back()->withInput();
        }

        $postData = $this->request->getPost();

        $data = [
            'employee_id' => $postData['employee_id'],
            'month' => $postData['month'],
            'year' => $postData['year'],
            'basic_salary' => $postData['basic_salary'],
            'allowances' => $postData['allowances'] ?? 0,
            'deductions' => $postData['deductions'] ?? 0,
            'net_salary' => $postData['net_salary'],
            'remarks' => $postData['remarks'] ?? null,
        ];

        if (!empty($postData['salary_id'])) {
            $this->salaryModel->update($postData['salary_id'], $data);
            $message = 'Salary record updated successfully.';
        } else {
            $this->salaryModel->insert($data);
            $message = 'Salary record added successfully.';
        }

        $this->session->setFlashdata('message', $message);
        return redirect()->to(site_url('salary'));
    }

    public function delete()
    {

        $salaryId = $this->request->getPost('salary_id');

        if (empty($salaryId) || !is_numeric($salaryId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid salary ID'
            ]);
        }

        $salary = $this->salaryModel->find($salaryId);
        if (!$salary) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Salary record not found'
            ]);
        }

        $this->salaryModel->delete($salaryId);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Salary record deleted successfully.'
        ]);
    }

    public function preview($salaryId)
    {
        // Get salary data
        $salary = $this->salaryModel->find($salaryId);
        if (!$salary) {
            return redirect()->back()->with('error', 'Salary record not found');
        }

        $employee = $this->employeeModel->find($salary['employee_id']);
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found');
        }

        // Prepare data for the slip
        $data = [
            'employee_id' => $employee['employee_id'],
            'employee_name' => $employee['first_name'] . ' ' . $employee['last_name'],
            'designation' => $employee['designation'],
            'department' => $employee['department'],
            'month_year' => date('F Y', strtotime("{$salary['year']}-{$salary['month']}-01")),
            'basic_salary' => $salary['basic_salary'],
            'allowances' => $salary['allowances'],
            'deductions' => $salary['deductions'],
            'net_salary' => $salary['net_salary'],
            'company_name' => 'Kahipay Private Limited',
            'company_logo' => FCPATH . 'assets/images/kahipay_logo.jpg',
            'company_address' => '123 Business Street, City, Country',
            'salary_id' => $salaryId
        ];

        // Check if PDF path exists in database and file exists
        if (!empty($salary['payslip']) && file_exists(FCPATH . $salary['payslip'])) {
            $data["pdf_path"] = base_url($salary['payslip']);
            return view('admin/salary/salary_preview', $data);
        }

        // Generate PDF path (relative to FCPATH)
        $relativePath = 'uploads/salary_slips/' . date('Y/m/') .
            'salary_slip_' . $data['employee_id'] . '_' .
            date('F_Y', strtotime("{$salary['year']}-{$salary['month']}-01")) . '.pdf';

        $pdfPath = FCPATH . $relativePath;

        // Generate PDF if it doesn't exist
        if (!file_exists($pdfPath)) {
            $mpdf = $this->generatePdf($data);

            // Ensure directory exists
            $dir = dirname($pdfPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            // Save the PDF
            $mpdf->Output($pdfPath, 'F');
        }

        // Update database with PDF path
        $this->salaryModel->update($salaryId, ['payslip' => $relativePath]);

        $data["pdf_path"] = base_url($relativePath);
        return view('admin/salary/salary_preview', $data);
    }

    public function download($salaryId)
    {
        $salary = $this->salaryModel->find($salaryId);
        if (!$salary) {
            return redirect()->back()->with('error', 'Salary record not found');
        }

        // If PDF exists in database and filesystem
        if (!empty($salary['payslip']) && file_exists(FCPATH . $salary['payslip'])) {
            return $this->response->download(FCPATH . $salary['payslip'], null, true);
        }

        // Generate on-the-fly if not exists
        $employee = $this->employeeModel
            ->select('employees.*, companies.company_name, companies.company_code, companies.logo, companies.gst, companies.address')
            ->join('companies', 'companies.company_id = employees.company_id')
            ->where('employees.employee_id', $salary['employee_id'])
            ->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found');
        }

        $data = [
            'employee_id' => $employee['employee_id'],
            'employee_name' => $employee['first_name'] . ' ' . $employee['last_name'],
            'designation' => $employee['designation'],
            'department' => $employee['department'],
            'month_year' => date('F Y', strtotime("{$salary['year']}-{$salary['month']}-01")),
            'basic_salary' => $salary['basic_salary'],
            'allowances' => $salary['allowances'],
            'deductions' => $salary['deductions'],
            'net_salary' => $salary['net_salary'],
            'company_name' => $employee['company_name'],
            'company_logo' => FCPATH . 'uploads/companies/' . $employee['logo'],
            'company_address' => $employee['address'],
            'company_gst_number' => $employee['gst'],
        ];

        $mpdf = $this->generatePdf($data);

        // Output as download
        return $mpdf->Output(
            'salary_slip_' . $data['employee_id'] . '_' .
                date('F_Y', strtotime("{$salary['year']}-{$salary['month']}-01")) . '.pdf',
            'D'
        );
    }

    public function getPdf($salaryId)
    {
        $salary = $this->salaryModel->find($salaryId);
        if (!$salary) {
            return redirect()->back()->with('error', 'Salary record not found');
        }

        if (!empty($salary['payslip']) && file_exists(FCPATH . $salary['payslip'])) {
            return $this->response->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="' . basename($salary['payslip']) . '"')
                ->setBody(file_get_contents(FCPATH . $salary['payslip']));
        }

        return redirect()->back()->with('error', 'Salary slip not found');
    }

    protected function generatePdf($data)
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 20,
            'margin_header' => 5,
            'margin_footer' => 10
        ]);

        $mpdf->SetAuthor($data['company_name']);
        $mpdf->SetTitle('Salary Slip - ' . $data['employee_name']);
        $mpdf->SetSubject('Salary Slip');

        $mpdf->AddPage();
        $html = view('admin/salary/salary_slip_template', $data);
        $mpdf->WriteHTML($html);

        return $mpdf;
    }
}
