<?php

namespace App\Controllers;

use App\Libraries\Pagination;
use App\Models\NotificationModel;
use App\Controllers\BaseController;

class NotificationController extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    public function index()
    {
        set_title('wallets list | ' . SITE_NAME);

        $data = [
            'action' => "wallets",
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
            $data['searchArray'][$field] = $searchValue;
        }

        $page = (int) $this->request->getGet('page') ?: 1;
        $Limit = 10;
        $totalRecord = $this->notificationModel->getNotificationDetails($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $this->notificationModel->getNotificationDetails($data['searchArray'], $startLimit, $Limit);

        return view('admin/notifications/index', $data);
    }
}
