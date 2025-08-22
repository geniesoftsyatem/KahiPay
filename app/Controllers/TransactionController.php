<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\WalletModel;
use App\Libraries\Pagination;
use App\Models\TransactionModel;
use App\Controllers\BaseController;

class TransactionController extends BaseController
{
    protected $session;
    protected $userModel;
    protected $walletModel;
    protected $transactionModel;

    public function __construct()
    {
        $this->session = session();
        $this->userModel = new UserModel();
        $this->walletModel = new WalletModel();
        $this->transactionModel = new TransactionModel();
    }

    public function index()
    {
        set_title('Transaction list | ' . SITE_NAME);

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
        $totalRecord = $this->transactionModel->getWalletTransactions($data['searchArray'], '', '', '1');
        $startLimit = ($page - 1) * $Limit;
        $data['reverse'] = $totalRecord - ($startLimit);
        $data['startLimit'] = $startLimit;
        $data['pagination'] = $customPagination->getPaginate($totalRecord, $page, $Limit);

        $data['results'] = $this->transactionModel->getWalletTransactions($data['searchArray'], $startLimit, $Limit);

        return view('admin/transaction/index', $data);
    }
}
