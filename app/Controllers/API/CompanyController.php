<?php

namespace App\Controllers\API;

use App\Models\UserModel;
use App\Models\CompanyModel;
use App\Models\EmployeeModel;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;

class CompanyController extends BaseController
{
    use ResponseTrait;

    protected $userModel;
    protected $companyModel;
    protected $employeeModel;

    public function __construct()
    {
        // Initialize models
        $this->userModel     = new UserModel();
        $this->companyModel  = new CompanyModel();
        $this->employeeModel = new EmployeeModel();
    }

    /**
     * Retrieve list of companies with the creator's name.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function getCompanyList()
    {
        try {
            // Fetch companies with creator's name from joined users table
            $companies = $this->companyModel
                ->select('companies.*, users.name')
                ->join('users', 'users.user_id = companies.created_by', 'left')
                ->findAll();

            // Replace created_by with the actual name and clean up the array
            $companies = array_map(function ($company) {
                $company['created_by'] = $company['name'] ?? "NA";
                unset($company['name']);
                return $company;
            }, $companies);

            // Return success response
            return $this->respond([
                'status'  => true,
                'message' => 'Company list fetched successfully.',
                'data'    => $companies,
            ], 200);
        } catch (\Exception $e) {
            // Return server error response
            return $this->failServerError(
                'An error occurred while retrieving the companies: ' . $e->getMessage()
            );
        }
    }

    public function getCompanyById($id)
    {
        try {
            $company = $this->companyModel
                ->select('companies.*, users.name')
                ->join('users', 'users.user_id = companies.created_by', 'left')
                ->where('companies.company_id', $id)
                ->first();

            if (!$company) {
                return $this->failNotFound('Company not found.');
            }

            $company['created_by'] = $company['name'] ?? "NA";
            unset($company['name']);

            return $this->respond([
                'status'  => true,
                'message' => 'Company fetched successfully.',
                'data'    => $company,
            ], 200);
        } catch (\Exception $e) {
            return $this->failServerError('Error fetching company: ' . $e->getMessage());
        }
    }

    public function updateCompanyDetails($id)
    {
        try {
            // Find the company by ID
            $company = $this->companyModel->find($id);

            if (!$company) {
                return $this->failNotFound('Company not found.');
            }

            // Get the JSON input data
            $input = $this->request->getJSON(true);

            // Initialize an array to store the fields that need to be updated
            $updateData = [];

            // List of allowed fields that can be updated
            $allowedFields = ['company_name', 'email', 'phone', 'website', 'address', 'pan', 'gst'];

            // Loop through each allowed field and update if present in input
            foreach ($allowedFields as $field) {
                if (isset($input[$field])) {
                    $updateData[$field] = $input[$field];
                }
            }

            // If no valid fields were provided for update
            if (empty($updateData)) {
                return $this->failValidationErrors('No valid fields to update.');
            }

            // Update the company record
            $this->companyModel->update($id, $updateData);

            // Return the updated company data
            return $this->respond([
                'status'  => true,
                'message' => 'Company updated successfully.',
                'data'    => $this->companyModel->find($id),
            ], 200);
        } catch (\Exception $e) {
            return $this->failServerError('Error updating company: ' . $e->getMessage());
        }
    }
}
