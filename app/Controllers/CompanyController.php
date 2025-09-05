<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CompanyModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;
use App\Models\ReportingManagerModel;

class CompanyController extends BaseController
{
    protected $db;
    protected $session;
    protected $userModel;
    protected $companyModel;
    protected $reportingManagerModel;

    public function __construct()
    {
        $this->session = session();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
        $this->companyModel = new CompanyModel();
        $this->reportingManagerModel = new ReportingManagerModel();
    }

    public function index()
    {
        set_title('Company List | ' . SITE_NAME);

        $data = [
            'pagetitle' => "Company List",
            'action' => "companies",
            'results' => [],
            'pagination' => '',
            'startLimit' => 0,
            'reverse' => 0,
            'searchArray' => []
        ];

        $customPagination = new Pagination();

        // Collect search criteria
        $searchFields = $this->request->getGet();
        foreach ($searchFields as $field => $searchValue) {
            $data['searchArray'][$field] = trim($searchValue);
        }

        $Limit = 10;
        $page = (int) $this->request->getGet('page') ?: 1;
        $totalRecord = $this->companyModel->getCompanies($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $this->companyModel->getCompanies($data['searchArray'], $startLimit, $Limit);

        return view('admin/company/index', $data);
    }

    public function create()
    {
        set_title('Add Company | ' . SITE_NAME);

        $data['pagetitle'] = "Add Company";
        return view('admin/company/create', $data);
    }

    public function edit($companyId)
    {
        set_title('Edit Company | ' . SITE_NAME);

        $data['pagetitle'] = "Edit Company";
        $data['company'] = $this->companyModel->where('company_id', $companyId)->first();

        if (!$data['company']) {
            $this->session->setFlashdata('error', 'Company not found');
            return redirect()->to(site_url('companies'));
        }

        return view('admin/company/create', $data);
    }

public function store()
{
    $rules = [
        'company_name' => 'required',
        'email' => 'permit_empty|max_length[150]|valid_email',
        'status' => 'required|in_list[Active,Inactive,Suspended]',
        'in_time' => 'required',
        'out_time' => 'required',
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
    }

    $companyId = $this->request->getPost('company_id');
    $email = $this->request->getPost('email');
    $phone = $this->request->getPost('phone');
    $companyName = $this->request->getPost('company_name');
    $password = $this->request->getPost('password') ?? 'default@123';
    $inTime = $this->request->getPost('in_time');
    $outTime = $this->request->getPost('out_time');

    // Validate that out_time is after in_time
    if ($inTime && $outTime && strtotime($outTime) <= strtotime($inTime)) {
        return redirect()->back()->withInput()->with('error', "Out Time must be later than In Time.");
    }

    $companyCode = $companyId ? $this->request->getPost('company_code') : $this->generateCompanyCode($companyName);

    // Check for duplicate email and phone
    if ($email) {
        $existingCompany = $this->companyModel
            ->where('email', $email)
            ->where('company_id !=', $companyId)
            ->first();
        if ($existingCompany) {
            return redirect()->back()->withInput()->with('error', "The email '$email' is already in use.");
        }
    }

    if ($phone) {
        $existingPhone = $this->companyModel
            ->where('phone', $phone)
            ->where('company_id !=', $companyId)
            ->first();
        if ($existingPhone) {
            return redirect()->back()->withInput()->with('error', "The phone '$phone' is already in use.");
        }
    }

    // Handle logo upload
    $logoPath = null;
    $logo = $this->request->getFile('logo');
    if ($logo && $logo->isValid() && !$logo->hasMoved()) {
        $logoPath = $logo->getRandomName();
        $logo->move(FCPATH . 'uploads/companies', $logoPath);

        // Delete old logo if updating
        if ($companyId) {
            $oldLogo = $this->companyModel->find($companyId)['logo'] ?? null;
            if ($oldLogo && file_exists(FCPATH . 'uploads/companies/' . $oldLogo)) {
                unlink(FCPATH . 'uploads/companies/' . $oldLogo);
            }
        }
    }

    $this->db->transStart();

    try {
        if ($companyId) {
            // ===== UPDATE OPERATION =====
            $originalCompany = $this->companyModel->find($companyId);

            // Update associated user
            if ($originalCompany && $originalCompany['user_id']) {
                $userData = [
                    'name' => $companyName,
                    'email' => $email,
                    'phone' => $phone,
                    'status' => $this->request->getPost('status') === 'Active' ? 'Active' : 'Inactive',
                ];
                if ($logoPath) $userData['profile_image'] = $logoPath;

                if (!$this->userModel->update($originalCompany['user_id'], $userData)) {
                    throw new \RuntimeException('Failed to update user: ' . implode(', ', $this->userModel->errors()));
                }
            }

            // Update company
            $companyData = [
                'company_name' => $companyName,
                'company_code' => $companyCode,
                'email' => $email,
                'phone' => $phone,
                'website' => $this->request->getPost('website'),
                'in_time' => $inTime,
                'out_time' => $outTime,
                'pan' => $this->request->getPost('pan'),
                'gst' => $this->request->getPost('gst'),
                'address' => $this->request->getPost('address'),
                'status' => $this->request->getPost('status'),
            ];
            if ($logoPath) $companyData['logo'] = $logoPath;

            if (!$this->companyModel->update($companyId, $companyData)) {
                throw new \RuntimeException('Failed to update company: ' . implode(', ', $this->companyModel->errors()));
            }

            $message = "Company updated successfully";
        } else {
            // ===== CREATE OPERATION =====
            $userId = null;
            if ($email) {
                $existingUser = $this->userModel->where('email', $email)->first();
                if ($existingUser) {
                    $this->db->transRollback();
                    return redirect()->back()->withInput()->with('error', "The email '$email' is already registered as a user.");
                }

                $userData = [
                    'name' => $companyName,
                    'username' => $companyCode,
                    'email' => $email,
                    'phone' => $phone,
                    'gender' => "Other",
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'user_type' => 'company',
                    'status' => $this->request->getPost('status') === 'Active' ? 'Active' : 'Inactive',
                    'profile_image' => $logoPath,
                ];

                if (!$this->userModel->save($userData)) {
                    throw new \RuntimeException('Failed to create user: ' . implode(', ', $this->userModel->errors()));
                }

                $userId = $this->userModel->getInsertID();
            }

            $companyData = [
                'user_id' => $userId,
                'company_name' => $companyName,
                'company_code' => $companyCode,
                'email' => $email,
                'phone' => $phone,
                'website' => $this->request->getPost('website'),
                'in_time' => $inTime,
                'out_time' => $outTime,
                'pan' => $this->request->getPost('pan'),
                'gst' => $this->request->getPost('gst'),
                'address' => $this->request->getPost('address'),
                'status' => $this->request->getPost('status'),
                'logo' => $logoPath,
                'created_by' => session()->get('user_id'),
            ];

            if (!$this->companyModel->save($companyData)) {
                throw new \RuntimeException('Failed to create company: ' . implode(', ', $this->companyModel->errors()));
            }

            $message = "Company created successfully";
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \RuntimeException('Transaction failed');
        }

        $this->session->setFlashdata('success', $message);
        return redirect()->to($companyId ? "companies/preview/{$companyId}" : 'companies');

    } catch (\Exception $e) {
        $this->db->transRollback();
        if ($logoPath && file_exists(FCPATH . 'uploads/companies/' . $logoPath)) {
            unlink(FCPATH . 'uploads/companies/' . $logoPath);
        }
        return redirect()->back()->withInput()->with('error', "Failed to save company: " . $e->getMessage());
    }
}




    protected function generateCompanyCode($companyName)
    {
        // Get first 3 alphabetic characters of company name (uppercase)
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $companyName), 0, 3));

        // Pad prefix with 'X' if less than 3 characters
        if (strlen($prefix) < 3) {
            $prefix = str_pad($prefix, 3, 'X');
        }

        $maxAttempts = 10;
        $attempt = 0;

        do {
            // Generate 5-digit random number
            $randomDigits = str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);

            // Combine to form 8-character company code
            $generatedCode = $prefix . $randomDigits;

            // Check for uniqueness in database
            $exists = $this->companyModel->where('company_code', $generatedCode)->first();

            if (!$exists) {
                return $generatedCode;
            }

            $attempt++;
        } while ($attempt < $maxAttempts);

        // Fallback: Use more entropy if needed
        $randomDigits = str_pad(mt_rand(10000, 99999), 5, '0', STR_PAD_LEFT);
        return $prefix . $randomDigits;
    }

    public function preview($companyId)
    {
        set_title('Company Details | ' . SITE_NAME);

        $data['pageTitle'] = "Company Details";
        $data['company'] = $this->companyModel->where('company_id', $companyId)->first();

        if (!$data['company']) {
            $this->session->setFlashdata('error', 'Company not found');
            return redirect()->to(site_url('companies'));
        }

        return view('admin/company/preview', $data);
    }

    public function delete()
    {
        $companyId = $this->request->getPost('company_id');

        // Validate company ID
        if (empty($companyId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Company ID is required to proceed with deletion.'
            ]);
        }

        // Retrieve the company record
        $company = $this->companyModel->find($companyId);
        if (!$company) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No company found with the provided ID.'
            ]);
        }

        // Begin database transaction
        $this->db->transStart();

        try {
            // Delete company logo if it exists
            if (!empty($company['logo'])) {
                $logoPath = FCPATH . 'uploads/companies/' . $company['logo'];
                if (file_exists($logoPath)) {
                    unlink($logoPath);
                }
            }

            // Delete associated user and their profile image if exists
            if (!empty($company['user_id'])) {
                $user = $this->userModel->find($company['user_id']);

                if ($user) {
                    if (!empty($user['profile_image'])) {
                        $profileImagePath = FCPATH . 'uploads/users/' . $user['profile_image'];
                        if (file_exists($profileImagePath)) {
                            unlink($profileImagePath);
                        }
                    }

                    $this->userModel->delete($user['user_id']);
                }
            }

            // Delete the company record
            $this->companyModel->delete($companyId);

            // Complete the transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Failed to complete the deletion transaction.');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Company and its associated user have been successfully deleted.'
            ]);
        } catch (\Exception $e) {
            // Roll back the transaction on error
            $this->db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while deleting the company: ' . $e->getMessage()
            ]);
        }
    }

    public function getManagersByCompany()
    {
        $companyId = $this->request->getPost('company_id');
        $managers = [];

        if ($companyId) {
            $searchArray = ["companyId" => $companyId];
            $managers = $this->reportingManagerModel->getReportingManagers($searchArray);
        }

        return $this->response->setJSON($managers);
    }
}
