<?php

namespace App\Controllers\API;

use App\Models\UserModel;
use App\Libraries\EmailSms;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class UsersController extends ResourceController
{
    use ResponseTrait;

    public function login()
    {
        // Implement login logic here
    }

    public function logout()
    {
        // Implement logout logic here
    }

    public function profile()
    {
        // Implement profile retrieval logic here
    }

    public function forgotPassword()
    {
        // Implement forgot password logic here
    }

    public function register()
    {
        // Get validation service
        $validation = \Config\Services::validation();

        // Set validation rules
        $validation->setRules([
            'company_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            // Validation failed, return validation errors
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'status' => false,
                    'errors' => $validation->getErrors()
                ]);
        }
        // Get post data
        $postData = $this->request->getPost();

        $usersModel = new UserModel();
        $userRecord = $usersModel
            ->where('email', $postData['email'])
            ->first();

        if ($userRecord) {
            return $this->response
                ->setStatusCode(409)
                ->setJSON([
                    'status' => false,
                    'error' => 'User already exists with email: ' . $postData['email']
                ]);
        }

        $loadViewPage = service('renderer');
        $mailbody = $loadViewPage
            ->setData(['first_name' => $postData['first_name']])
            ->render('welcome_email');

        $emailTemplate = new EmailSms();
        $subject = "Welcome to Stylo!";
        // Send the email using the sendEmail method
        $emailSent = $emailTemplate->sendEmail($postData['email'], $subject, $mailbody);
        if (!$emailSent) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status' => false,
                    'errors' => 'Oops! Something went wrong. Please try again later.'
                ]);
        }
        $otp =  rand(1000, 9999);
        $randomNumber = rand(1000, 9999);
        $firstCharacterFirstName = strtoupper(substr($postData['first_name'], 0, 1));
        $firstCharacterLastName = strtoupper(substr($postData['last_name'], 0, 1));
        $username = $firstCharacterFirstName . $firstCharacterLastName . $randomNumber;

        $checkUsernameExistOrNot = $usersModel->where('username', $username)->first();
        while ($checkUsernameExistOrNot !== null) {
            $randomNumber = rand(1000, 9999);
            $username = $firstCharacterFirstName . $firstCharacterLastName . $randomNumber;
            $checkUsernameExistOrNot = $usersModel->where('username', $username)->first();
        }

        $data = [
            "username" => $username,
            "company_id" => $postData['company_id'],
            "first_name" => $postData['first_name'],
            "last_name" => $postData['last_name'],
            "email" => $postData['email'],
            "phone" => $postData['phone'],
            "password" => password_hash($postData['password'], PASSWORD_DEFAULT),
            "otp" => $otp,
        ];

        $inserted = $usersModel->insert($data);

        // Send the login otp to user
        $emailcontent = $emailTemplate->getMessage('otpmessage');
        $subject = $emailcontent['SUBJECT'];
        $mailbody = $emailcontent['BODY'];

        //Add footer in email body template
        $mailbody .= $emailTemplate->emailFooter();
        $mailbody = str_replace("##USER_NAME##", $postData['first_name'], $mailbody);
        $mailbody = str_replace("##OTP_NUMBER##", $otp, $mailbody);

        $emailTemplate->sendEmail($postData['email'], $subject, $mailbody);

        if ($inserted) {
            return $this->response
                ->setStatusCode(201)
                ->setJSON([
                    'status' => true,
                    'message' => 'User created successfully.'
                ]);
        } else {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status' => false,
                    'error' => 'Internal server error. Please try again.'
                ]);
        }
    }

    public function updateProfile()
    {
        // Get validation service
        $validation = \Config\Services::validation();

        // Set validation rules
        $validation->setRules([
            'user_id' => 'required',
            'company_id' => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            // Validation failed, return validation errors
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'status' => false,
                    'errors' => $validation->getErrors()
                ]);
        }
        // Get post data
        $postData = $this->request->getPost();
        $usersModel = new UserModel();
        $userRecord = $usersModel
            ->where('id', $postData['user_id'])
            ->where('company_id', $postData['company_id'])
            ->first();

        if (!$userRecord) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status' => false,
                    'error' => 'User not found. Please enter valid user ID and company ID.'
                ]);
        }

        $data = [];

        if (isset($postData['email']) && !empty($postData['email'])) {
            $data['email'] = $postData['email'];
        }

        if (isset($postData['phone']) && !empty($postData['phone'])) {
            $data['phone'] = $postData['phone'];
        }

        $usersModel = new UserModel();
        $updateUserRecord = $usersModel
            ->set($data)
            ->where('id', $postData['user_id'])
            ->where('company_id', $postData['company_id'])
            ->update();

        if ($updateUserRecord) {
            return $this->response
                ->setStatusCode(201)
                ->setJSON([
                    'status' => true,
                    'message' => 'User updated successfully.'
                ]);
        } else {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status' => false,
                    'error' => 'Internal server error. Please try again.'
                ]);
        }
    }

    public function getUserInfo()
    {
        // Get validation service
        $validation = \Config\Services::validation();

        // Set validation rules
        $validation->setRules([
            'user_id' => 'required',
            'company_id' => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            // Validation failed, return validation errors
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'status' => false,
                    'errors' => $validation->getErrors()
                ]);
        }
        // Get post data
        $postData = $this->request->getPost();
        $usersModel = new UserModel();
        $userRecord = $usersModel
            ->where('id', $postData['user_id'])
            ->where('company_id', $postData['company_id'])
            ->select('username, phone, email')
            ->first();

        if (!$userRecord) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status' => false,
                    'error' => 'User not found. Please enter valid user ID and company ID.'
                ]);
        } else {
            return $this->response
                ->setStatusCode(200)
                ->setJSON([
                    'status' => true,
                    'message' => 'User details retrieved successfully.',
                    'data' => $userRecord
                ]);
        }
    }

    public function varifyOtp()
    {
        // Get validation service
        $validation = \Config\Services::validation();

        // Set validation rules
        $validation->setRules([
            'user_id' => 'required',
            'company_id' => 'required',
            'otp' => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            // Validation failed, return validation errors
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'status' => false,
                    'errors' => $validation->getErrors()
                ]);
        }
        // Get post data
        $postData = $this->request->getPost();
        $usersModel = new UserModel();
        $userRecord = $usersModel
            ->where('id', $postData['user_id'])
            ->where('company_id', $postData['company_id'])
            ->first();

        if (!$userRecord) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status' => false,
                    'error' => 'User not found. Please enter valid user ID and company ID.'
                ]);
        }
        if ($userRecord['otp'] == $postData['otp']) {
            return $this->response
                ->setStatusCode(200)
                ->setJSON([
                    'status' => true,
                    'message' => 'User verified successfully.',
                    "data" => [
                        'user_id' => $userRecord['id']
                    ]
                ]);
        } else {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status' => false,
                    'error' => 'Invalid OTP. Please enter a valid OTP.'
                ]);
        }
    }
}
