<?php

namespace App\Controllers;

use DateTime;
use DateTimeZone;
use RuntimeException;
use App\Models\UserModel;
use App\Models\CompanyModel;
use App\Libraries\EmailService;
use App\Controllers\BaseController;

class AuthController extends BaseController
{
    protected $session;
    protected $usersModel;
    protected $companyModel;

    public function __construct()
    {
        $this->usersModel = new UserModel();
        $this->companyModel = new CompanyModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $isLoggedIn = $this->session->get('logged_in');

        if ($isLoggedIn) {
            // Redirect to login page if not logged in
            return redirect()->to(site_url('dashboard'));
        }

        $data = [];
        return view('admin/login', $data);
    }

    public function verifyLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Check if username and password are provided
        if (!empty($username) && !empty($password)) {
            // Step 1: Fetch user record from the database based on the email/username
            $userRecord = $this->usersModel->where("email", $username)->first();

            if (!$userRecord) {
                $this->session->setFlashdata('error', 'User not found');
                return redirect()->to(site_url('login'));
            }

            // Step 2: Validate the password
            if (!password_verify($password, $userRecord["password"])) {
                $this->session->setFlashdata('error', 'Invalid Password');
                return redirect()->to(site_url('login'));
            }

            // Step 3: Check if the account is active
            if (strtolower($userRecord["status"]) !== "active") {
                $this->session->setFlashdata('error', 'Your account is currently inactive.');
                return redirect()->to(site_url('login'));
            }

            // Step 4: Generate OTP for validation
            $otp = rand(100000, 999999);
            // $otp = 111111;

            date_default_timezone_set('Asia/Kolkata');
            $otpExpiry = date('Y-m-d H:i:s', time() + 300);

            // Step 5: Save OTP in DB for this user
            $this->usersModel->update($userRecord['user_id'], [
                'otp'             => $otp,
                'otp_expire_time' => $otpExpiry
            ]);

            // Step 6: Send OTP Email using EmailService
            try {
                $emailService = new EmailService();
                $subject = "Your Login OTP Code";
                $message = "Dear User,<br><br>Your OTP code is: <b>{$otp}</b>. 
                            It will expire in 5 minutes.<br><br>Regards,<br>Kahipay";
                $emailService->sendEmail($username, $subject, $message);

                // Step 7: Redirect to OTP verification page
                return redirect()->to(site_url('verify-otp?email=' . urlencode($username)));
            } catch (\Exception $e) {
                // If email sending fails, destroy the session and show error
                session()->destroy();
                $this->session->setFlashdata('error', 'Failed to send OTP email: ' . $e->getMessage());
                return redirect()->to(site_url('login'));
            }
        } else {
            $this->session->setFlashdata('error', 'Username and Password are required.');
            return redirect()->to(site_url('login'));
        }
    }

    public function otpForm()
    {
        $username = $this->request->getGet('email');
        $data = ['email' => $username];

        return view('admin/verify_otp', $data);
    }

    public function resendOtp()
    {
        $email = $this->request->getGet('email');

        if (!$email) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Session expired. Please login again.'
            ]);
        }

        // Fetch user
        $userRecord = $this->usersModel->where('email', $email)->first();

        if (!$userRecord) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'User not found.'
            ]);
        }

        // Generate new OTP
        $otp = rand(100000, 999999);
        date_default_timezone_set('Asia/Kolkata');
        $otpExpiry = date('Y-m-d H:i:s', time() + 300);

        // Update DB
        $this->usersModel->update($userRecord['user_id'], [
            'otp'             => $otp,
            'otp_expire_time' => $otpExpiry
        ]);

        try {
            $emailService = new EmailService();
            $subject = "Your Login OTP Code (Resent)";
            $message = "Dear User,<br><br>Your new OTP code is: <b>{$otp}</b>. 
                It will expire in 5 minutes.<br><br>Regards,<br>Kahipay";

            $emailService->sendEmail($email, $subject, $message);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'A new OTP has been sent to your email.'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Failed to resend OTP: ' . $e->getMessage()
            ]);
        }
    }

    public function verifyOtp()
    {
        $session   = session();
        $username  = $this->request->getPost('otp_user');
        $inputOtp  = $this->request->getPost('otp_code');

        // Validate OTP
        if (empty($username)) {
            $this->session->setFlashdata('error', 'Session expired. Please login again.');
            return redirect()->to(site_url('login'));
        }

        // Fetch OTP & expiry from DB
        $user = $this->usersModel
            ->select('otp, otp_expire_time, user_id, name, email, phone, status, user_type, profile_image')
            ->where('email', $username)
            ->first();

        if (!$user) {
            $this->session->setFlashdata('error', 'User not found. Please login again.');
            return redirect()->to(site_url('login'));
        }

        // Check expiry
        $expiry = new DateTime($user['otp_expire_time'], new DateTimeZone('Asia/Kolkata'));
        if (time() > $expiry->getTimestamp()) {
            $this->session->setFlashdata('error', 'OTP expired. Please login again.');
            return redirect()->to(site_url('login'));
        }

        // Update last login timestamp
        $currentTime = date('Y-m-d H:i:s');
        $user["last_login"] = $currentTime;

        // Validate OTP
        if ($inputOtp == $user['otp']) {
            // Step 1: Mark user as logged in by setting session data
            $sessionData = [
                'user_id'       => $user['user_id'],
                'name'          => $user['name'],
                'email'         => $user['email'],
                'phone'         => $user['phone'],
                'status'        => $user['status'],
                'user_type'     => $user['user_type'],
                'profile_image' => $user['profile_image'] ?? null,
                'last_login'    => $user['last_login'] ?? null,
                'timezone'      => $user['timezone'] ?? 'UTC',
                'logged_in'     => true,  // User is now logged in
            ];

            // Handle company-specific data
            if (strtolower($user['user_type']) === 'company') {
                $companyModel = new CompanyModel();
                $company = $companyModel->where('user_id', $user['user_id'])->first();

                if ($company) {
                    $sessionData["company_id"] = $company['company_id'];
                    $sessionData["company_name"] = $company['company_name'] ?? null;
                }
            }

            // Set session data
            $session->set($sessionData);

            // Clear OTP in DB for security
            $this->usersModel->where('email', $username)->update(null, [
                'otp'        => null,
                'otp_expire_time' => null
            ]);

            $session->remove(['otp_user']);

            return redirect()->to(site_url('dashboard'));
        } else {
            $this->session->setFlashdata('error', 'Invalid OTP');
            return redirect()->to(site_url('verify-otp'));
        }
    }

    public function register()
    {
        $session = session();
        $validation = \Config\Services::validation();
        // Validate input
        if (!$this->validate([
            'name'     => 'required',
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
            'repeat_password' => 'matches[password]',
        ])) {
            $session->setFlashdata('errors', $validation->getErrors());
            return redirect()->to(site_url('rent-contracts/create'));
        }

        $userData = [
            'name' => $this->request->getPost('name'),
            'email'       => $this->request->getPost('email'),
            'password'    => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ];

        $this->usersModel->insert($userData);

        return redirect()->to('/login')->with('success', 'Registration successful. Please login.');
    }

    public function profile()
    {
        set_title('Profile | ' . SITE_NAME);

        $data = [];
        $userId = $this->session->get('user_id');
        $data['profiledata'] = $this->usersModel->where("user_id", $userId)->first();

        return view('admin/profile', $data);
    }

    /**
     * Show edit profile form
     */
    public function editProfile()
    {
        $data = [
            'title' => 'Edit Profile',
            'user' => $this->usersModel->find(session()->get('user_id'))
        ];

        return view('admin/edit_profile', $data);
    }

    /**
     * Update profile information
     */
    public function updateProfile()
    {
        $userId    = $this->session->get('user_id');
        $userType  = $this->session->get('user_type');
        $companyId = $this->session->get('company_id');

        $user = $this->usersModel->find($userId);

        // Validation rules for user
        $rules = [
            'name'  => 'required|max_length[100]',
            'phone' => 'permit_empty|max_length[20]',
            'notes'   => 'permit_empty|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('errors', $this->validator->getErrors());
            return redirect()->to('edit-profile');
        }

        // Handle profile image
        $profileImage = $this->request->getFile('profile_image');
        $imageName = $user['profile_image']; // keep old image by default
        $newName = null;

        if ($profileImage && $profileImage->isValid() && !$profileImage->hasMoved()) {
            // Size check (max 5MB)
            if ($profileImage->getSize() > 5 * 1024 * 1024) {
                return redirect()->back()->withInput()->with('error', 'Profile image size should not exceed 5MB.');
            }

            // Allowed types check
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            if (!in_array($profileImage->getMimeType(), $allowedTypes)) {
                return redirect()->back()->withInput()->with('error', 'Only JPG, JPEG, PNG, and WEBP images are allowed.');
            }

            // Generate new random name
            $newName = $profileImage->getRandomName();

            // Move file to uploads/users
            if ($profileImage->move(FCPATH . 'uploads/users', $newName)) {
                // Delete old file only if new upload succeeded
                if ($imageName && file_exists(FCPATH . 'uploads/users/' . $imageName)) {
                    unlink(FCPATH . 'uploads/users/' . $imageName);
                }
                $imageName = $newName; // update with new file name
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to upload new profile image.');
            }
        }

        // Prepare user update data
        $data = [
            'name'          => $this->request->getPost('name'),
            'email'         => $this->request->getPost('email'),
            'phone'         => $this->request->getPost('phone'),
            'notes'         => $this->request->getPost('notes'),
            'profile_image' => $imageName
        ];

        // Update user
        $this->usersModel->update($userId, $data);

        // If company, also update company info
        if ($userType === 'company') {
            $companyData = [
                'company_name' => $data['name'],
                'email'        => $data['email'],
                'phone'        => $data['phone']
            ];

            // If we have a new image, copy it to companies directory
            if ($newName !== null) {
                $companyImagePath = FCPATH . 'uploads/companies/' . $newName;

                // Copy the file instead of moving it
                if (copy(FCPATH . 'uploads/users/' . $newName, $companyImagePath)) {
                    // Delete old company logo if exists
                    $oldCompanyLogo = $this->companyModel->find($companyId)['logo'] ?? null;
                    if ($oldCompanyLogo && file_exists(FCPATH . 'uploads/companies/' . $oldCompanyLogo)) {
                        unlink(FCPATH . 'uploads/companies/' . $oldCompanyLogo);
                    }
                    $companyData["logo"] = $newName;
                } else {
                    return redirect()->back()->withInput()->with('error', 'Failed to copy profile image to company directory.');
                }
            }

            $this->companyModel->update($companyId, $companyData);
        }

        return redirect()->to('/profile')->with('success', 'Profile updated successfully!');
    }

    public function changePassword()
    {

        $data = [];
        return view('admin/change_password', $data);
    }

    public function updatePassword()
    {

        $userId = $this->session->get('user_id');
        $password = $this->request->getPost('password');

        $arrSaveData = [
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $this->usersModel->update($userId, $arrSaveData);

        $this->session->setFlashdata('success', 'Password updated successfully.');
        return redirect()->to(site_url('change-password'));
    }

    public function logout()
    {
        // Define the session keys to be cleared
        $sessionKeys = ['user_id', 'first_name', 'last_name', 'email', 'phone', 'user_type', 'status', 'logged_in'];
        // Get the session service
        $session = \Config\Services::session();
        // Remove the specified session variables
        foreach ($sessionKeys as $sessionKey) {
            $session->remove($sessionKey);
        }
        // Optionally destroy the session
        $session->destroy();
        // Redirect to the login page after logout
        return redirect()->to(site_url('login'));
    }
}
