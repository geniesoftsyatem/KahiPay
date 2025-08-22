<?php

namespace App\Controllers;

use App\Libraries\Pagination;
use App\Models\ContactUsModel;
use App\Controllers\BaseController;

class CommonController extends BaseController
{
    public function feedbacks()
    {
        set_title("Feedback | " . SITE_NAME);

        $data = [];
        $searchArray = [];
        $data['action'] = "feedback";
        $data['pageTitle'] = "Feedback List";

        $feedbackModel = new ContactUsModel();
        $txtsearch = $this->request->getGet('txtsearch');

        if (!empty($txtsearch)) {
            $searchArray['txtsearch'] = $txtsearch;
        }

        $page = $this->request->getGet('page') ?? 1;
        $limit = defined('RECORD_PER_PAGE') ? RECORD_PER_PAGE : 10;
        $totalRecord = $feedbackModel->getContactMessages($searchArray, '', '', '1');
        $startLimit = ($page - 1) * $limit;

        $pagination = new Pagination();
        $pagination = $pagination->getPaginate($totalRecord, $page, $limit);

        $data['reverse'] = $totalRecord - $startLimit;
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $pagination;
        $data['txtsearch'] = $txtsearch;
        $data["searchArray"] = $searchArray;
        $data['results'] = $feedbackModel->getContactMessages($searchArray, $startLimit, $limit);

        return view('admin/feedback', $data);
    }

    public function deleteFeedback()
    {

        $feedbackId = $this->request->getPost('feedback_id');

        if (is_numeric($feedbackId) && !empty($feedbackId)) {
            try {
                $feedbackModel = new ContactUsModel();
                $feedbackModel->where('id', $feedbackId)->delete();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Feedback deleted successfully.',
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again.',
            ]);
        }
    }
}
