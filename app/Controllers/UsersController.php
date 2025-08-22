<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Libraries\EmailSms;
use App\Models\CompanyModel;
use App\Models\EmployeeModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;
use App\Models\ReportingManagerModel;

class UsersController extends BaseController
{
    protected $db;
    protected $session;
    protected $userModel;
    protected $companyModel;
    protected $employeeModel;
    protected $reportingManagerModel;

    public function __construct()
    {
        $this->session = session();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
        $this->companyModel = new CompanyModel();
        $this->employeeModel = new EmployeeModel();
        $this->reportingManagerModel = new ReportingManagerModel();
    }

    public function index()
    {
        set_title('Users list | ' . SITE_NAME);

        $data = [
            'action' => "users",
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

        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 10;
        $totalRecord = $this->userModel->getUsersDetails($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $this->userModel->getUsersDetails($data['searchArray'], $startLimit, $Limit);

        return view('admin/users/index', $data);
    }

    public function create()
    {

        set_title('Create New User | ' . SITE_NAME);
        $data['pagetitle'] = "Create New User";
        return view('admin/users/create', $data);
    }

    public function edit($id)
    {
        set_title('Update User | ' . SITE_NAME);
        $userModel = new UserModel();
        $userDetails = $userModel->find($id);
        $data['pagetitle'] = "Update User";
        $data['user'] = $userDetails;

        return view('admin/users/create', $data);
    }

    public function save()
    {
        $rules = [
            'name' => 'required|max_length[100]',
            'email' => 'required|valid_email|max_length[100]',
            'phone' => 'required|numeric|min_length[8]|max_length[15]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $userId = $this->request->getPost('id');
        $email = $this->request->getPost('email');
        $status = $this->request->getPost('status') ?? 'active';

        // Check for duplicate email
        $userModel = new UserModel();
        if (!empty($email)) {
            $query = $userModel->where('email', $email);

            if ($userId) {
                $query->where('user_id !=', $userId);
            }

            if ($query->first()) {
                $this->session->setFlashdata('error', "Email already exists");
                return $userId ? redirect()->to('users/edit/' . $userId) : redirect()->to('users/add');
            }
        }

        // Prepare user data
        $userData = [
            'name' => $this->request->getPost('name'),
            'email' => $email,
            'phone' => $this->request->getPost('phone'),
            'alt_mobile_number' => $this->request->getPost('alt_mobile_number'),
            'gender' => $this->request->getPost('gender'),
            'status' => $status,
            'notes' => esc($this->request->getPost('notes')),
        ];

        // Handle profile image upload
        $profileImagePath = $this->handleProfileImageUpload($userId);
        if ($profileImagePath) {
            $userData['profile_image'] = $profileImagePath;
        }

        $this->db->transStart();

        try {
            if ($userId) {
                // ========== UPDATE OPERATION ==========
                $originalUser = $userModel->find($userId);

                // Update the user
                $userModel->update($userId, $userData);

                // Update associated records based on user type
                switch ($originalUser['user_type']) {
                    case 'company':
                        $company = $this->companyModel->where('user_id', $userId)->first();
                        if ($company) {
                            $companyData = [
                                'company_name' => $userData['name'],
                                'email' => $userData['email'],
                                'phone' => $userData['phone'],
                                'status' => $userData['status'] === 'active' ? 'Active' : 'Inactive',
                            ];
                            if ($profileImagePath) {
                                $companyData['logo'] = $profileImagePath;
                            }
                            $this->companyModel->update($company['company_id'], $companyData);
                        }
                        break;

                    case 'employee':
                        $employee = $this->employeeModel->where('user_id', $userId)->first();
                        if ($employee) {
                            $employeeData = [
                                'first_name' => explode(' ', $userData['name'])[0],
                                'last_name' => explode(' ', $userData['name'])[1] ?? '',
                                'email' => $userData['email'],
                                'phone' => $userData['phone'],
                                'status' => $userData['status'] === 'active' ? 'Active' : 'Inactive',
                            ];
                            if ($profileImagePath) {
                                $employeeData['profile_image'] = $profileImagePath;
                            }
                            $this->employeeModel->update($employee['employee_id'], $employeeData);
                        }
                        break;
                }

                $message = "User updated successfully";
                $redirect = 'users/edit/' . $userId;
            } else {
                // ========== CREATE OPERATION ==========
                $userData['username'] = $this->generateUsername($userModel);
                $userData['user_type'] = 'user'; // Default user type

                $userModel->save($userData);
                $userId = $userModel->getInsertID();

                $message = "User created successfully";
                $redirect = 'users';
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Failed to save user data');
            }

            return redirect()->to($redirect)->with('success', $message);
        } catch (\Exception $e) {
            $this->db->transRollback();

            // Clean up uploaded file if transaction failed
            if (!empty($profileImagePath) && file_exists(FCPATH . 'uploads/users/' . $profileImagePath)) {
                unlink(FCPATH . 'uploads/users/' . $profileImagePath);
            }

            return redirect()->back()->withInput()->with('error', "Failed to save user: " . $e->getMessage());
        }
    }

    protected function handleProfileImageUpload($userId = null)
    {
        $profileImage = $this->request->getFile('profile_image');
        if (!$profileImage || !$profileImage->isValid() || $profileImage->hasMoved()) {
            return null;
        }

        $newName = $profileImage->getRandomName();
        $profileImage->move(FCPATH . 'uploads/users', $newName);

        // Delete old image if updating
        if ($userId) {
            $oldImage = $this->userModel->find($userId)['profile_image'] ?? null;
            if ($oldImage && file_exists(FCPATH . 'uploads/users/' . $oldImage)) {
                unlink(FCPATH . 'uploads/users/' . $oldImage);
            }
        }

        return $newName;
    }

    // Helper function to generate random username (3 letters + 3 numbers)
    protected function generateUsername($userModel)
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';

        do {
            $randomLetters = '';
            for ($i = 0; $i < 3; $i++) {
                $randomLetters .= $letters[rand(0, strlen($letters) - 1)];
            }

            $randomNumbers = '';
            for ($i = 0; $i < 3; $i++) {
                $randomNumbers .= $numbers[rand(0, strlen($numbers) - 1)];
            }

            $username = $randomLetters . $randomNumbers;

            // Check if username already exists
            $exists = $userModel->where('username', $username)->first();
        } while ($exists);

        return $username;
    }

    public function showDetails($userId)
    {

        $data = array();
        set_title('users Details | ' . SITE_NAME);

        $data['pageTitle'] = "users Details";
        $data['record'] = $this->userModel
            ->join('addresses', 'addresses.user_id = users.user_id', 'left')
            ->where('users.user_id', $userId)
            ->select('users.*, addresses.address, addresses.city, addresses.state, addresses.pincode, addresses.country, addresses.address_type')
            ->first();

        return view('admin/users/preview', $data);
    }

    public function delete()
    {
        $userId = $this->request->getPost('user_id');

        // Validate user ID
        if (empty($userId) || !is_numeric($userId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'A valid user ID is required to proceed with deletion.'
            ]);
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No user found with the provided ID.'
            ]);
        }

        // Start database transaction
        $this->db->transStart();

        try {
            // Delete user's profile image if it exists
            if (!empty($user['profile_image'])) {
                $filePath = FCPATH . 'uploads/users/' . $user['profile_image'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Handle deletion based on user type
            switch ($user['user_type']) {
                case 'company':
                    // Delete associated company and its logo
                    $company = $this->companyModel->where('user_id', $userId)->first();
                    if ($company) {
                        if (!empty($company['logo'])) {
                            $logoPath = FCPATH . 'uploads/companies/' . $company['logo'];
                            if (file_exists($logoPath)) {
                                unlink($logoPath);
                            }
                        }

                        $this->companyModel->delete($company['company_id']);
                    }
                    break;

                case 'employee':
                    // Delete associated employee and their profile image
                    $employee = $this->employeeModel->where('user_id', $userId)->first();
                    if ($employee) {
                        if (!empty($employee['profile_image'])) {
                            $empImagePath = FCPATH . 'uploads/employees/' . $employee['profile_image'];
                            if (file_exists($empImagePath)) {
                                unlink($empImagePath);
                            }
                        }

                        $this->employeeModel->delete($employee['employee_id']);

                        // Delete reporting manager relationships
                        $this->reportingManagerModel
                            ->where('employee_id', $employee['employee_id'])
                            ->orWhere('manager_id', $employee['employee_id'])
                            ->delete();
                    }
                    break;
            }

            // Delete the user record
            $userModel->delete($userId);

            // Complete transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Failed to complete the deletion transaction.');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'User and all associated records have been successfully deleted.'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred during deletion: ' . $e->getMessage()
            ]);
        }
    }
}
