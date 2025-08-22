<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\WalletModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;

class WalletController extends BaseController
{
    protected $session;
    protected $userModel;
    protected $walletModel;

    public function __construct()
    {
        $this->session = session();
        $this->userModel = new UserModel();
        $this->walletModel = new WalletModel();
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
        $totalRecord = $this->walletModel->getWalletDetails($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $this->walletModel->getWalletDetails($data['searchArray'], $startLimit, $Limit);

        return view('admin/wallet/index', $data);
    }
}
