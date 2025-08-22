<?php

namespace App\Controllers\API;

use CodeIgniter\Files\File;
use CodeIgniter\API\ResponseTrait;
use App\Models\RequestLetterModel;
use CodeIgniter\RESTful\ResourceController;

class RequestLetterController extends ResourceController
{
    use ResponseTrait;

    protected $requestLetterModel;

    public function __construct()
    {
        $this->requestLetterModel = new RequestLetterModel();
    }

    /**
     * Create a new request letter
     */
    public function createRequestLetter()
    {
        $rules = [
            'employee_id'           => 'required',
            'reporting_employee_id' => 'required|numeric',
            'title'                 => 'required|string|min_length[3]|max_length[100]',
            'description'           => 'required|string|min_length[10]',
            'images'                => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $postData = $this->request->getVar();
        $uploadedFiles = $this->request->getFiles();

        $imagePaths = [];
        $folderPath = FCPATH . 'uploads/request_letters/';

        // Ensure folder exists
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        // Handle images (same set for all employees)
        if (isset($uploadedFiles['images'])) {
            $files = $uploadedFiles['images'];

            // If only one file is uploaded, wrap it inside an array
            if (!is_array($files)) {
                $files = [$files];
            }

            if (count($files) > 5) {
                return $this->failValidationErrors("You can upload a maximum of 5 images.");
            }

            foreach ($files as $img) {
                if ($img && $img->isValid() && !$img->hasMoved()) {
                    $newName = time() . '_' . $img->getRandomName();
                    $img->move($folderPath, $newName);

                    // Save relative path to DB for later use
                    $imagePaths[] = 'uploads/request_letters/' . $newName;
                }
            }
        }


        // Normalize employee IDs
        $employeeIds = json_decode($postData['employee_id'], true);
        if (!is_array($employeeIds) || empty($employeeIds)) {
            return $this->failValidationErrors(['employee_id' => 'Invalid or empty employee_id list']);
        }

        foreach ($employeeIds as $id) {
            if (!is_numeric($id)) {
                return $this->failValidationErrors(['employee_id' => 'All employee IDs must be numeric']);
            }
        }

        // Common request letter data
        $requestLetterTemplate = [
            'reporting_employee_id' => $postData['reporting_employee_id'],
            'title'                 => $postData['title'],
            'description'           => $postData['description'],
            'images'                => !empty($imagePaths) ? implode(',', $imagePaths) : null,
            'created_at'            => date('Y-m-d H:i:s'),
        ];

        $insertedIds = [];
        $this->requestLetterModel->transStart();

        try {
            foreach ($employeeIds as $employeeId) {
                $requestLetter = $requestLetterTemplate;
                $requestLetter['employee_id'] = $employeeId;

                if (!$reqId = $this->requestLetterModel->insert($requestLetter, true)) {
                    throw new \RuntimeException('Failed to create request letter for employee ID: ' . $employeeId);
                }

                $insertedIds[] = $reqId;
            }

            $this->requestLetterModel->transComplete();
        } catch (\Exception $e) {
            $this->requestLetterModel->transRollback();
            return $this->failServerError('Failed to create request letters: ' . $e->getMessage());
        }

        return $this->respondCreated([
            'status'   => 201,
            'error'    => null,
            'base_url'  => base_url(),
            'messages' => count($employeeIds) > 1
                ? 'Request letters submitted successfully'
                : 'Request letter submitted successfully',
            'data'     => [
                'request_letter_ids' => implode(',', $insertedIds),
                'count'              => count($insertedIds),
                'images'             => $imagePaths
            ]
        ]);
    }

    /**
     * Get request letter history by employee_id
     */
    public function getRequestLetterHistory()
    {
        $employeeId = $this->request->getGet('employee_id');

        if (empty($employeeId) || !is_numeric($employeeId)) {
            return $this->failValidationErrors("Valid employee_id is required.");
        }

        $records = $this->requestLetterModel
            ->where('employee_id', $employeeId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        if (empty($records)) {
            return $this->respond([
                'status'   => 200,
                'error'    => null,
                'messages' => 'No request letters found.',
                'data'     => []
            ]);
        }

        foreach ($records as &$record) {
            $record['images'] = json_decode($record['images'], true);
        }

        return $this->respond([
            'status'   => 200,
            'error'    => null,
            'messages' => 'Request letter history fetched successfully.',
            'data'     => $records
        ]);
    }
}
