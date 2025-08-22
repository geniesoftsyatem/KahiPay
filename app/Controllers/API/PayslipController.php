<?php

namespace App\Controllers\API;

use Mpdf\Mpdf;
use App\Models\SalaryModel;
use App\Models\EmployeeModel;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;

class PayslipController extends BaseController
{
    use ResponseTrait;

    protected $salaryModel;
    protected $employeeModel;

    public function __construct()
    {
        $this->salaryModel = new SalaryModel();
        $this->employeeModel = new EmployeeModel();
    }

    public function generatePayslip()
    {
        $rules = [
            'employee_id' => 'required|numeric',
            'month' => 'required',
            'year' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $employeeId = (int)$this->request->getVar('employee_id');
        $month = (int)$this->request->getVar('month');
        $year = (int)$this->request->getVar('year');

        $employee = $this->employeeModel->find($employeeId);
        if (!$employee) {
            return $this->failNotFound('Employee not found');
        }

        $salary = $this->salaryModel->where(['employee_id' => $employeeId, 'month' => $month, 'year' => $year])->first();
        if (!$salary) {
            return $this->failNotFound('Salary record not found for specified period');
        }

        $monthName = date('F', mktime(0, 0, 0, $month, 1));

        // Define relative and absolute paths using writable directory
        $relativePath = 'salary_slips/' . $year . '/' . $month . '/';
        $filename = 'payslip_' . $employeeId . '_' . $monthName . '_' . $year . '.pdf';
        $absoluteDir = WRITEPATH . $relativePath;
        $absolutePath = $absoluteDir . $filename;

        $responseData = [
            'employee_id' => $employee['employee_id'],
            'month' => $month,
            'year' => $year,
            'payslip_generated' => false,
            'basic_salary' => (float)$salary['basic_salary'],
            'net_salary' => (float)$salary['net_salary'],
            'payslip_url' => null
        ];

        // Check if already generated
        if (!empty($salary['payslip']) && file_exists(WRITEPATH . $salary['payslip'])) {
            $responseData['payslip_generated'] = true;
            $responseData['payslip_url'] = base_url("api/payslip/serve/{$employeeId}/{$month}/{$year}");
            return $this->respond(['status' => true, 'data' => $responseData]);
        }

        // Create directory if not exists
        if (!is_dir($absoluteDir)) {
            mkdir($absoluteDir, 0777, true);
        }

        // Prepare PDF data
        $pdfData = [
            'employee_id' => $employee['employee_id'],
            'employee_name' => $employee['first_name'] . ' ' . $employee['last_name'],
            'designation' => $employee['designation'],
            'department' => $employee['department'],
            'month_year' => $monthName . ' ' . $year,
            'basic_salary' => $salary['basic_salary'],
            'allowances' => $salary['allowances'],
            'deductions' => $salary['deductions'],
            'net_salary' => $salary['net_salary'],
            'company_name' => 'Kahipay Private Limited',
            'company_logo' => FCPATH . 'assets/images/kahipay_logo.jpg',
            'company_address' => '123 Business Street, City, Country'
        ];

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

        $mpdf->SetAuthor($pdfData['company_name']);
        $mpdf->SetTitle('Salary Slip - ' . $pdfData['employee_name']);
        $mpdf->SetSubject('Salary Slip');

        $html = view('admin/salary/salary_slip_template', $pdfData);
        $mpdf->WriteHTML($html);
        $mpdf->Output($absolutePath, 'F');

        // Store only relative path in DB
        $this->salaryModel->update($salary['salary_id'], ['payslip' => $relativePath . $filename]);

        $responseData['payslip_generated'] = true;
        $responseData['payslip_url'] = base_url("api/payslip/serve/{$employeeId}/{$month}/{$year}");

        return $this->respond(['status' => true, 'data' => $responseData]);
    }

    public function getPayslipStatus()
    {
        $rules = [
            'employee_id' => 'required|numeric',
            'month' => 'required',
            'year' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $employeeId = (int)$this->request->getVar('employee_id');
        $month = (int)$this->request->getVar('month');
        $year = (int)$this->request->getVar('year');

        $salary = $this->salaryModel->where(['employee_id' => $employeeId, 'month' => $month, 'year' => $year])->first();

        if (!$salary) {
            return $this->respond(['status' => false, 'message' => 'Salary record not found']);
        }

        $response = [
            'status' => true,
            'data' => [
                'employee_id' => $employeeId,
                'month' => $month,
                'year' => $year,
                'payslip_generated' => !empty($salary['payslip']),
                'payslip_url' => !empty($salary['payslip']) ? base_url("api/payslip/serve/{$employeeId}/{$month}/{$year}") : null,
                'basic_salary' => $salary['basic_salary'],
                'net_salary' => $salary['net_salary']
            ]
        ];

        return $this->respond($response);
    }

    public function servePayslip($employeeId, $month, $year)
    {
        $salary = $this->salaryModel->where([
            'employee_id' => $employeeId,
            'month' => $month,
            'year' => $year
        ])->first();

        if (!$salary || empty($salary['payslip'])) {
            return $this->failNotFound('Payslip not found.');
        }

        $filePath = WRITEPATH . $salary['payslip'];

        if (!file_exists($filePath)) {
            return $this->failNotFound('Payslip file not found on server.');
        }

        return $this->response->download($filePath, null, true);
    }
}
