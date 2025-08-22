<?php

namespace App\Controllers;

use App\Models\CompanyModel;
use App\Models\EmployeeModel;
use App\Libraries\Pagination;
use App\Models\RequestLetterModel;
use App\Controllers\BaseController;
use App\Models\ReportingManagerModel;

class RequestLetterController extends BaseController
{
    protected $session;
    protected $companyModel;
    protected $employeeModel;
    protected $requestLetterModel;
    protected $reportingManagerModel;

    public function __construct()
    {
        $this->session = session();
        $this->companyModel = new CompanyModel();
        $this->employeeModel = new EmployeeModel();
        $this->requestLetterModel = new RequestLetterModel();
        $this->reportingManagerModel = new ReportingManagerModel();
    }

    public function index()
    {
        set_title('Request Letters | ' . SITE_NAME);
        $companyId  = session('company_id');
        $userType   = session('user_type');

        $data = [
            'action' => "request-letters",
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
        if (strtolower($userType) === 'company' && !empty($companyId)) {
            $data['searchArray']['company_id'] = $companyId;
        }

        $Limit = 10;
        $page = (int) $this->request->getGet('page') ?: 1;
        $data['managers'] = $this->reportingManagerModel->getReportingManagers();
        $data['companies'] = $this->companyModel->where('status', 'Active')->findAll();
        $totalRecord = $this->requestLetterModel->getRequestLetters($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $this->requestLetterModel->getRequestLetters($data['searchArray'], $startLimit, $Limit);

        return view('admin/request_letter/index', $data);
    }

    public function create()
    {
        set_title('Add Request Letter | ' . SITE_NAME);

        $data['pagetitle'] = "Add Request Letter";
        $data['employees'] = $this->employeeModel->findAll();

        return view('admin/request_letter/create', $data);
    }

    public function edit()
    {
        set_title('Edit Request Letter | ' . SITE_NAME);
        $data['pagetitle'] = "Edit Request Letter";

        $id = $this->request->getGet('id');
        $data['letter'] = $this->requestLetterModel->find($id);
        $data['employees'] = $this->employeeModel->findAll();

        return view('admin/request_letter/create', $data);
    }

    public function save()
    {
        $postData = $this->request->getPost();
        $uploadedFiles = $this->request->getFiles();

        $isUpdate = !empty($postData['request_id']);
        $requestId = $isUpdate ? $postData['request_id'] : null;

        $imagePaths = [];
        $deleteOldImages = false;

        if (isset($uploadedFiles['images']) && is_array($uploadedFiles['images'])) {
            // Check if any new images are actually uploaded
            $hasNewImages = false;
            foreach ($uploadedFiles['images'] as $img) {
                if ($img->isValid() && !$img->hasMoved()) {
                    $hasNewImages = true;
                    break;
                }
            }

            if ($hasNewImages) {
                $deleteOldImages = true;

                // Create folder if not exists (common for both update and insert)
                $folderPath = FCPATH . 'uploads/request_letters';
                if (!is_dir($folderPath)) {
                    mkdir($folderPath, 0755, true);
                }

                if ($isUpdate && $deleteOldImages) {
                    // If updating and we have new images, delete old images
                    $existing = $this->requestLetterModel->find($requestId);
                    if (!empty($existing['images'])) {
                        $oldImages = explode(',', $existing['images']);
                        foreach ($oldImages as $imgPath) {
                            $fullPath = FCPATH . $imgPath;
                            if (file_exists($fullPath)) {
                                unlink($fullPath);
                            }
                        }
                    }
                }

                // Move new images (common for both update and insert)
                foreach ($uploadedFiles['images'] as $img) {
                    if ($img->isValid() && !$img->hasMoved()) {
                        $newName = time() . '_' . $img->getRandomName();
                        $img->move($folderPath, $newName);
                        $imagePaths[] = 'uploads/request_letters/' . $newName;
                    }
                }
            }
        }

        $data = [
            'employee_id' => $postData['employee_id'],
            'reporting_employee_id' => $postData['reporting_employee_id'],
            'title'       => $postData['title'],
            'description' => $postData['description'],
        ];

        // Only update images if new ones were uploaded
        if (!empty($imagePaths)) {
            $data['images'] = implode(',', $imagePaths);
        } elseif ($isUpdate && !$deleteOldImages) {
            // Keep existing images if not updating them
            $existing = $this->requestLetterModel->find($requestId);
            $data['images'] = $existing['images'] ?? null;
        }

        if ($isUpdate) {
            $this->requestLetterModel->update($requestId, $data);
            $message = 'Request Letter updated successfully.';
        } else {
            $requestId = $this->requestLetterModel->insert($data);
            $message = 'Request Letter added successfully.';
        }

        if ($requestId) {
            $this->session->setFlashdata('message', $message);
        } else {
            $this->session->setFlashdata('errmessage', 'Something went wrong...');
        }

        return redirect()->to(site_url('request-letters'));
    }

    public function preview()
    {
        set_title('Request Letter Details | ' . SITE_NAME);
        $data['pageTitle'] = "Request Letter Details";

        $id = $this->request->getGet('id');
        $data['letter'] = $this->requestLetterModel->find($id);
        return view('admin/request_letter/preview', $data);
    }

    public function delete()
    {
        $requestId = $this->request->getPost('request_id');
        if (empty($requestId) || !is_numeric($requestId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request ID'
            ]);
        }

        $letter = $this->requestLetterModel->find($requestId);

        if (!$letter) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Request letter not found'
            ]);
        }

        try {
            if (!empty($letter['images'])) {
                $imagePaths = explode(',', $letter['images']);
                foreach ($imagePaths as $imgPath) {
                    $fullPath = FCPATH . $imgPath;
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }
            }

            $this->requestLetterModel->delete($requestId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Request letter deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete request letter: ' . $e->getMessage()
            ]);
        }
    }
}
